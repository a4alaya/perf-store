<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PaymentManager
{
    public function __construct(private readonly PaymentGatewayInterface $gateway)
    {
    }

    public function start(Order $order): Payment
    {
        if ($order->payment_method === 'cod') {
            return Payment::create([
                'order_id' => $order->id,
                'gateway' => 'cod',
                'status' => 'cod_pending',
                'amount' => $order->total,
                'currency' => $order->currency,
            ]);
        }

        return $this->gateway->createCheckout($order);
    }

    public function handleWebhook(Request $request): PaymentLog
    {
        $event = $this->gateway->verifyWebhook($request);

        return DB::transaction(function () use ($event): PaymentLog {
            $existing = PaymentLog::query()->where('gateway_event_id', $event['id'])->first();

            if ($existing) {
                return $existing;
            }

            $order = $this->resolveOrder($event['data']);
            $payment = $order?->payment;

            $log = PaymentLog::create([
                'payment_id' => $payment?->id,
                'order_id' => $order?->id,
                'gateway' => config('store.payment_gateway', 'stripe'),
                'event_type' => $event['type'],
                'gateway_event_id' => $event['id'],
                'status' => $event['data']['payment_status'] ?? $event['data']['status'] ?? null,
                'payload' => $event['data'],
                'received_at' => now(),
            ]);

            if ($order && in_array($event['type'], ['checkout.session.completed', 'payment_intent.succeeded', 'charge.succeeded'], true)) {
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'confirmed_at' => now(),
                ]);
                $payment?->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'transaction_reference' => $event['data']['payment_intent'] ?? $event['data']['id'] ?? $payment->transaction_reference,
                    'payload' => $event['data'],
                ]);
                Notification::route('mail', $order->customer_email)->notify(new PaymentStatusNotification($order, 'successful'));
            }

            if ($order && in_array($event['type'], ['payment_intent.payment_failed', 'checkout.session.expired', 'charge.failed'], true)) {
                $order->update(['status' => 'payment_failed', 'payment_status' => 'failed']);
                $payment?->update(['status' => 'failed', 'payload' => $event['data']]);
                Notification::route('mail', $order->customer_email)->notify(new PaymentStatusNotification($order, 'failed'));
            }

            return $log;
        });
    }

    private function resolveOrder(array $payload): ?Order
    {
        $orderId = $payload['metadata']['order_id'] ?? null;
        $orderNumber = $payload['metadata']['order_number'] ?? $payload['client_reference_id'] ?? null;

        return Order::query()
            ->when($orderId, fn ($query) => $query->whereKey($orderId))
            ->when(! $orderId && $orderNumber, fn ($query) => $query->where('order_number', $orderNumber))
            ->first();
    }
}
