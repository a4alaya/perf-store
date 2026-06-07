<?php

return [
    'name' => env('APP_NAME', 'Maison De Mystere Perfumes'),
    'currency' => env('STORE_CURRENCY', 'AED'),
    'vat_rate' => (float) env('STORE_VAT_RATE', 5),
    'free_shipping_threshold' => (float) env('STORE_FREE_SHIPPING_THRESHOLD', 500),
    'default_locale' => env('APP_LOCALE', 'en'),
    'locales' => ['en', 'ar'],
    'payment_gateway' => env('PAYMENT_GATEWAY', 'stripe'),
    'trn' => env('STORE_TRN'),
    'support_email' => env('STORE_SUPPORT_EMAIL', 'care@maisondemystere.ae'),
    'support_phone' => env('STORE_SUPPORT_PHONE', '+971501234567'),
    'emirates' => [
        'abu_dhabi' => 'Abu Dhabi',
        'dubai' => 'Dubai',
        'sharjah' => 'Sharjah',
        'ajman' => 'Ajman',
        'umm_al_quwain' => 'Umm Al Quwain',
        'ras_al_khaimah' => 'Ras Al Khaimah',
        'fujairah' => 'Fujairah',
    ],
];
