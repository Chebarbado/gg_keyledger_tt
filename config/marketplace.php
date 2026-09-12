<?php

return [
    'admin_token' => env('ADMIN_TOKEN', 'dev-admin-token'),

    // бронь на чекауте (сек). для демо удобно 120, в проде обычно 300+
    'reservation_ttl' => (int) env('RESERVATION_TTL', 120),

    'supplier_a_failure_rate' => (float) env('SUPPLIER_A_FAILURE_RATE', 0),
    'supplier_a_timeout_rate' => (float) env('SUPPLIER_A_TIMEOUT_RATE', 0),
    'supplier_b_failure_rate' => (float) env('SUPPLIER_B_FAILURE_RATE', 0),
    'supplier_b_timeout_rate' => (float) env('SUPPLIER_B_TIMEOUT_RATE', 0),
    'supplier_timeout_seconds' => (int) env('SUPPLIER_TIMEOUT_SECONDS', 3),
];
