<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, PaymentManager $payments): JsonResponse
    {
        $payments->handleWebhook($request);

        return response()->json(['received' => true]);
    }
}
