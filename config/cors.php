<?php

return [
    'paths' => ['api/*','auth/*', 'sanctum/csrf-cookie', 'auth/google*', 'login', 'logout'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
