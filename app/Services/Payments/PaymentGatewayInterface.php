<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function createCheckout(Order $order): Payment;

    /**
     * @return array{id:string,type:string,data:array}
     */
    public function verifyWebhook(Request $request): array;

    public function refund(Payment $payment, float $amount, ?string $reason = null): Refund;
}
