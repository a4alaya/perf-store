<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TapPaymentGateway implements PaymentGatewayInterface
{
    public function createCheckout(Order $order): Payment
    {
        $response = Http::withToken((string) config('services.tap.secret_key'))
            ->acceptJson()
            ->post('https://api.tap.company/v2/charges', [
                'amount' => (float) $order->total,
                'currency' => $order->currency,
                'threeDSecure' => true,
                'save_card' => false,
                'description' => "Maison De Mystere {$order->order_number}",
                'customer' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => ['country_code' => '971', 'number' => preg_replace('/\D+/', '', $order->customer_phone)],
                ],
                'source' => ['id' => 'src_all'],
                'redirect' => ['url' => route('checkout.confirmation', ['locale' => app()->getLocale(), 'order' => $order])],
                'metadata' => ['order_id' => $order->id, 'order_number' => $order->order_number],
            ])
            ->throw()
            ->json();

        return Payment::updateOrCreate(
            ['order_id' => $order->id, 'gateway' => 'tap'],
            [
                'gateway_payment_id' => $response['id'] ?? null,
                'status' => $response['status'] ?? 'pending',
                'amount' => $order->total,
                'currency' => $order->currency,
                'checkout_url' => $response['transaction']['url'] ?? null,
                'payload' => $response,
            ],
        );
    }

    public function verifyWebhook(Request $request): array
    {
        $payload = $request->all();

        return [
            'id' => $payload['id'] ?? sha1($request->getContent()),
            'type' => $payload['object'] ?? 'tap.charge.updated',
            'data' => $payload,
        ];
    }

    public function refund(Payment $payment, float $amount, ?string $reason = null): Refund
    {
        $response = Http::withToken((string) config('services.tap.secret_key'))
            ->acceptJson()
            ->post('https://api.tap.company/v2/refunds', [
                'charge_id' => $payment->gateway_payment_id,
                'amount' => $amount,
                'currency' => $payment->currency,
                'reason' => $reason ?: 'requested_by_customer',
                'metadata' => ['order_id' => $payment->order_id],
            ])
            ->throw()
            ->json();

        return Refund::create([
            'order_id' => $payment->order_id,
            'payment_id' => $payment->id,
            'amount' => $amount,
            'reason' => $reason,
            'status' => $response['status'] ?? 'pending',
            'gateway_refund_id' => $response['id'] ?? null,
            'payload' => $response,
            'processed_at' => now(),
        ]);
    }
}
