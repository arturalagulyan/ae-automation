<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    */

    'replicate_folder' => 'C:\\nexrender\\',
    'assets_folder' => 'D:\\assets\\',
    'templates_folder' => 'D:\\template-projects\\',
    'render_folder' => 'D:\\renders\\',
    'outputs_folder' => 'D:\\final-outputs\\',

    'logs_folder' => storage_path('logs\\jobs\\'),
    'processes_folder' => storage_path('processes'),

    /*
    |--------------------------------------------------------------------------
    | Programs
    |--------------------------------------------------------------------------
    */

    'ae' => 'C:\\"Program Files"\\Adobe\\"Adobe After Effects 2020"\\"Support Files"\\aerender.exe',
//    'ffmpeg' => 'C:\\Users\\Bob\\AppData\\Local\\Temp\\nexrender\\ffmpeg-b4.2.2.exe',
    'ffmpeg' => 'C:\\ffmpeg\\ffmpeg-b4.2.2.exe',

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
