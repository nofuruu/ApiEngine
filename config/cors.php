<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*'],  // Menentukan path API yang terkena CORS

    'allowed_methods' => ['*'],  // Mengizinkan semua metode HTTP (GET, POST, PUT, DELETE, dll.)

   'allowed_origins' => [
        'http://10.21.1.125',
        'http://10.21.1.125/UI',
        'http://10.21.1.125/UI/',
        'http://localhost',
        'http://localhost/UI'
    ],

    'allowed_origins_patterns' => [
        'http://10.21.1.125/UI/*'
    ],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',  // Mengizinkan header Authorization untuk token
    ],

    'exposed_headers' => [
        'Authorization',  // Mengekspos header Authorization agar bisa diakses oleh frontend
    ],

    'max_age' => 0,  // Waktu untuk cache CORS di browser, bisa diubah sesuai kebutuhan

    'supports_credentials' => true,  // Jika menggunakan cookie atau session-based auth, setel ke true
];
