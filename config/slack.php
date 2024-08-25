<?php
return [
    'channels' => [
        'support' => env('SLACK_SUPPORT_WEBHOOK_URL'),
        'sales' => env('SLACK_SALES_WEBHOOK_URL'),
        'general' => env('SLACK_GENERAL_WEBHOOK_URL'),
    ],
    'client_id' => env('SLACK_CLIENT_ID'),
    'signing_secret' => env('SLACK_SIGNING_SECRET'),
    'client_secret' => env('SLACK_CLIENT_SECRET'),
];
