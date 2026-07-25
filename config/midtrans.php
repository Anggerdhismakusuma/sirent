<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Your Midtrans server key. Used for API calls from backend (Snap token
    | generation, transaction status checks). Keep this secret.
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Your Midtrans client key. Used to initialize the Snap.js frontend
    | library. This is safe to expose in the browser.
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set to true for live transactions. When false, all transactions go
    | through Midtrans Sandbox. The snap.js CDN URL is also determined by
    | this value.
    |
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Sanitization
    |--------------------------------------------------------------------------
    |
    | When true, Midtrans will sanitize input parameters to prevent XSS.
    | Recommended to keep enabled.
    |
    */
    'is_sanitized' => true,

    /*
    |--------------------------------------------------------------------------
    | 3D Secure
    |--------------------------------------------------------------------------
    |
    | When true, credit card transactions will require 3D Secure
    | authentication. Recommended for security compliance.
    |
    */
    'is_3ds' => true,

    /*
    |--------------------------------------------------------------------------
    | Merchant Name
    |--------------------------------------------------------------------------
    |
    | Displayed in the Midtrans Snap popup and payment notifications.
    |
    */
    'merchant_name' => env('APP_NAME', 'SI-RENT'),

];
