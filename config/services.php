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
    'soap' => [
        'terminal_url' => env('WS_TERMINAL_URL', 'https://dev.testfidely.net/fnet3web/proxy/wsdl.php?v=01.95'),
        'terminal_serial' => env('WS_TERMINAL_SERIAL', '47294'),
        'terminal_user' => env('WS_TERMINAL_USER', 'opTemporalEBBBS'),
        'terminal_password' => env('WS_TERMINAL_PASSWORD', 'qM6iNHN7etfhKS2'),

        'ca_url' => env('WS_CA_URL', 'https://dev.testfidely.net/fnet3web/proxy/wsdl_ca.php?v=01.90'),
        'ca_kind' => env('WS_CA_KIND', 3),
        'ca_campaign_id' => env('WS_CA_CAMPAIGN_ID', 2005),
    ],

];
