<?php

return [

    'default' => env('FILESYSTEM_DISK', 'public'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'avatars' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatars'),
            'url' => env('APP_URL') . '/storage/avatars',
            'visibility' => 'public',
            'throw' => false,
        ],

        'videos' => [
            'driver' => 'local',
            'root' => storage_path('app/public/videos'),
            'url' => env('APP_URL') . '/storage/videos',
            'visibility' => 'public',
            'throw' => false,
        ],

        'previews' => [
            'driver' => 'local',
            'root' => storage_path('app/public/previews'),
            'url' => env('APP_URL') . '/storage/previews',
            'visibility' => 'public',
            'throw' => false,
        ],

        'media' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media'),
            'url' => env('APP_URL') . '/storage/media',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    'links' => [

        public_path('storage') => storage_path('app/public'),

    ],

];
