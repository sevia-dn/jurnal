<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whatsapp' => [
        'url' => env('WHATSAPP_URL'),
        'api_key' => env('WHATSAPP_API_KEY'),
        'approval_base_url' => env('WHATSAPP_APPROVAL_BASE_URL', 'https://public-aeration-unleaded.ngrok-free.dev'),
        'piket_confirmation_number' => env('WHATSAPP_PIKET_CONFIRMATION_NUMBER', '083838606396'),
        'admin_number' => env('WHATSAPP_ADMIN_NUMBER', env('WHATSAPP_PIKET_CONFIRMATION_NUMBER', '083838606396')),
        'waka_recipients' => [
            ['username' => 'fajarluthfiantospd', 'name' => 'Fajar Luthfianto', 'number' => env('WHATSAPP_WAKA_FAJAR_NUMBER', '083157785970')],
        ],
    ],

];
