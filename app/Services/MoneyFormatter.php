<?php

namespace App\Services;

class MoneyFormatter
{
    public function format(float|int|string|null $amount, ?string $currency = null): string
    {
        $currency ??= config('store.currency', 'AED');

        return sprintf('%s %s', $currency, number_format((float) $amount, 2));
    }
}
