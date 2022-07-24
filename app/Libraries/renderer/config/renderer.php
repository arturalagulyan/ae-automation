<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    */

    'replicate_folder' => env('REPLICATE_FOLDER'),
    'assets_folder' => env('ASSETS_FOLDER'),
    'templates_folder' => env('TEMPLATES_FOLDER'),
    'render_folder' => env('RENDER_FOLDER'),
    'outputs_folder' => env('OUTPUTS_FOLDER'),
    'logs_folder' => env('LOGS_FOLDER'),

    /*
    |--------------------------------------------------------------------------
    | Programs
    |--------------------------------------------------------------------------
    */

    'ae' => env('AE_BINARY'),
//    'ffmpeg' => 'C:\\Users\\Bob\\AppData\\Local\\Temp\\nexrender\\ffmpeg-b4.2.2.exe',
    'ffmpeg' => env('FFMPEG_BINARY'),

    /*
    |--------------------------------------------------------------------------
    | Nexrender config
    |--------------------------------------------------------------------------
    */

    'nexrender' => [
        'secret' => 'test',
        'cli' => 'nexrender-cli',
        'server' => 'nexrender-server',
        'worker' => 'nexrender-worker',
        'options_json' => 'nexrender-options.json',
        'api_url' =>  'http://localhost:3050/api/v1/',
        'server_url' =>  'http://localhost:3050',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'active' => true,
        'channel' => [
            'driver' => 'daily',
            'path' => storage_path('logs/renderer.log'),
            'level' => 'debug',
            'days' => 20,
        ]
    ],
];
