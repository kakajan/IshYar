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

    /*
    |--------------------------------------------------------------------------
    | IPPanel SMS Gateway
    |--------------------------------------------------------------------------
    |
    | Configuration for IPPanel SMS service used for OTP verification
    | and pattern-based SMS notifications.
    | Docs: https://ippanelcom.github.io/Edge-Document/docs/
    |
    */

    'ippanel' => [
        'base_url' => env('IPPANEL_BASE_URL', 'https://edge.ippanel.com/v1'),
        'api_key' => env('IPPANEL_API_KEY'),
        'from_number' => env('IPPANEL_FROM_NUMBER'),
        'patterns' => [
            'otp' => env('IPPANEL_PATTERN_OTP'),
            'task_assigned' => env('IPPANEL_PATTERN_TASK_ASSIGNED'),
            'task_completed' => env('IPPANEL_PATTERN_TASK_COMPLETED'),
            'task_comment' => env('IPPANEL_PATTERN_TASK_COMMENT'),
            'approval_request' => env('IPPANEL_PATTERN_APPROVAL'),
        ],
    ],

];
