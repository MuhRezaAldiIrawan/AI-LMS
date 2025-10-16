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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'),
    ],

    'pinecone' => [
        'api_key' => env('PINECONE_API_KEY'),
        'host' => env('PINECONE_HOST'),
        'index' => env('PINECONE_INDEX_NAME'),
    ],

    'google' => [
        'project_id' => env('GOOGLE_PROJECT_ID'),
        'location_id' => env('GOOGLE_LOCATION_ID', 'asia-southeast1'),
        'shared_drive_id' => env('GOOGLE_SHARED_DRIVE_ID'),
        'storage_bucket' => env('GOOGLE_STORAGE_BUCKET'),
        'document_ai_processor_id' => env('GOOGLE_DOCUMENT_AI_PROCESSOR_ID'),
        'credentials_path' => env('GOOGLE_APPLICATION_CREDENTIALS', storage_path('app/google-credentials.json')),
    ],

    'media' => [
        'youtube_dl_path' => env('YOUTUBE_DL_PATH'),
        'ffmpeg_path' => env('FFMPEG_PATH'),
    ],

    'cloudconvert' => [
        'api_key' => env('CLOUDCONVERT_API_KEY'),
        'sandbox' => env('CLOUDCONVERT_SANDBOX', false),
    ],
];
