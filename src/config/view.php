<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    // Blade のコンパイル先（未設定だと例外になる）
    'compiled' => env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views'))),

    // 以下は既定値（必要最低限のみ）
    'cache' => env('VIEW_CACHE', true),
    'check_cache_timestamps' => env('VIEW_CHECK_CACHE_TIMESTAMPS', true),
    'relative_hash' => false,
    'compiled_extension' => 'php',
];

