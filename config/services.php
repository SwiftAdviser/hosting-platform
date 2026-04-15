<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
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

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_CALLBACK_URL', '/auth/github/callback'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('GOOGLE_CALLBACK_URL', '/auth/google/callback')),
    ],

    'onchainos' => [
        'webhook_secret' => env('ONCHAINOS_WEBHOOK_SECRET', ''),
        'base_url' => env('ONCHAINOS_BASE_URL', ''),
        'api_key' => env('ONCHAINOS_API_KEY', ''),
        'secret_key' => env('ONCHAINOS_SECRET_KEY', ''),
        'passphrase' => env('ONCHAINOS_PASSPHRASE', ''),
    ],

    'kiloclaw' => [
        'base_url' => env('KILOCLAW_BASE_URL', ''),
        'api_key' => env('KILOCLAW_API_KEY', ''),
    ],

    'demo' => [
        'enabled' => env('DEMO_MODE', false),
    ],

    'coolify' => [
        'base_url' => env('COOLIFY_BASE_URL', 'https://coolz.krutovoy.me/api/v1'),
        'token' => env('COOLIFY_API_TOKEN', ''),
        'server_uuid' => env('COOLIFY_SERVER_UUID', ''),
        'destination_uuid' => env('COOLIFY_DESTINATION_UUID', ''),
        'project_uuid' => env('COOLIFY_PROJECT_UUID'),
        'image_name' => env('OPENCLAW_IMAGE_NAME', 'ghcr.io/swiftadviser/openclaw-agent'),
    ],

];
