<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripePaymentGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;

    public function __construct()
    {
        if (! config('services.stripe.secret')) {
            return;
        }

        $this->stripe = new StripeClient([
            'api_key' => (string) config('services.stripe.secret'),
            'stripe_version' => '2026-02-25.clover',
        ]);
    }

    public function createCheckout(Order $order): Payment
    {
        if (! isset($this->stripe)) {
            return Payment::updateOrCreate(
                ['order_id' => $order->id, 'gateway' => 'stripe'],
                [
                    'gateway_payment_id' => 'test_'.$order->order_number,
                    'status' => 'test_pending',
                    'amount' => $order->total,
                    'currency' => $order->currency,
                    'transaction_reference' => 'test_'.$order->idempotency_key,
                    'checkout_url' => route('checkout.confirmation', ['locale' => app()->getLocale(), 'order' => $order]),
                    'payload' => ['mode' => 'local_test', 'message' => 'Set STRIPE_SECRET for hosted Checkout.'],
                ],
            );
        }

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'client_reference_id' => $order->order_number,
            'success_url' => route('checkout.confirmation', ['locale' => app()->getLocale(), 'order' => $order]),
            'cancel_url' => route('checkout.index', ['locale' => app()->getLocale(), 'payment' => 'failed']),
            'customer_email' => $order->customer_email,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'unit_amount' => (int) round((float) $order->total * 100),
                    'product_data' => [
                        'name' => "Maison De Mystere Order {$order->order_number}",
                        'description' => 'Includes UAE VAT and delivery fees where applicable.',
                    ],
                ],
            ]],
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'order_id' => (string) $order->id,
                    'order_number' => $order->order_number,
                ],
            ],
        ], [
            'idempotency_key' => $order->idempotency_key ?: $order->order_number,
        ]);

        return Payment::updateOrCreate(
            ['order_id' => $order->id, 'gateway' => 'stripe'],
            [
                'gateway_payment_id' => $session->id,
                'status' => $session->payment_status ?: 'pending',
                'amount' => $order->total,
                'currency' => $order->currency,
                'transaction_reference' => $session->payment_intent,
                'checkout_url' => $session->url,
                'payload' => $session->toArray(),
            ],
        );
    }

    public function verifyWebhook(Request $request): array
    {
        if (! config('services.stripe.webhook_secret')) {
            $payload = json_decode($request->getContent(), true) ?: $request->all();

            return [
                'id' => $payload['id'] ?? sha1($request->getContent()),
                'type' => $payload['type'] ?? 'checkout.session.completed',
                'data' => $payload['data']['object'] ?? $payload,
            ];
        }

        $event = Webhook::constructEvent(
            $request->getContent(),
            (string) $request->header('Stripe-Signature'),
            (string) config('services.stripe.webhook_secret'),
        );

        return [
            'id' => $event->id,
            'type' => $event->type,
            'data' => $event->data->object->toArray(),
        ];
    }

    public function refund(Payment $payment, float $amount, ?string $reason = null): Refund
    {
        if (! isset($this->stripe)) {
            return Refund::create([
                'order_id' => $payment->order_id,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'reason' => $reason,
                'status' => 'succeeded',
                'gateway_refund_id' => 'test_refund_'.$payment->id,
                'payload' => ['mode' => 'local_test'],
                'processed_at' => now(),
            ]);
        }

        $refund = $this->stripe->refunds->create([
            'payment_intent' => $payment->transaction_reference,
            'amount' => (int) round($amount * 100),
            'metadata' => [
                'order_id' => (string) $payment->order_id,
                'reason' => $reason,
            ],
        ]);

        return Refund::create([
            'order_id' => $payment->order_id,
            'payment_id' => $payment->id,
            'amount' => $amount,
            'reason' => $reason,
            'status' => $refund->status,
            'gateway_refund_id' => $refund->id,
            'payload' => $refund->toArray(),
            'processed_at' => now(),
        ]);
    }
}
