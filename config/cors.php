<?php

return [
    // Đảm bảo đường dẫn login của bạn được bao phủ (thường là api/login)
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    // KHÔNG dùng ['*'] khi supports_credentials là true
    // Hãy liệt kê chính xác các địa chỉ Frontend của bạn
    'allowed_origins' => [
        'http://192.168.1.44:5173', 
        'http://localhost:5173',
        'https://dangkhoauni-backend.onrender.com' // Thêm chính nó để tránh lỗi self-call
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Giữ là true nếu bạn dùng Sanctum/Session
];