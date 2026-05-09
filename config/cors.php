<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    // Chỉ định rõ IP local của bạn thay vì dùng '*'
    'allowed_origins' => [
        'http://192.168.1.44:5173', 
        'http://localhost:5173',
        'https://dangkhoauni-backend.onrender.com',
        'https://dang-khoa-uni-frontend.vercel.app'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, 
];