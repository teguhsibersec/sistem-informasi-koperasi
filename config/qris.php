<?php

return [
    'provider' => env('QRIS_PROVIDER'),
    'masa_berlaku_default' => 15, // menit
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'base_url' => env('MIDTRANS_BASE_URL', 'https://api.sandbox.midtrans.com'),
        'ca_bundle' => env('MIDTRANS_CA_BUNDLE', base_path('resources/certificates/midtrans-cacert.pem')),
    ],
];
