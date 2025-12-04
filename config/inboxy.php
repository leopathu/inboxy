<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Email Sending Driver
    |--------------------------------------------------------------------------
    |
    | Choose the email sending driver: 'ses' for Amazon SES or 'smtp' for
    | standard SMTP. This allows you to switch between ultra-low-cost SES
    | and any SMTP provider based on your needs.
    |
    */

    'driver' => env('MAIL_DRIVER', 'ses'),

    /*
    |--------------------------------------------------------------------------
    | Amazon SES Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for sending emails via Amazon SES. Requires AWS SDK.
    | SES provides ultra-low-cost email delivery.
    |
    */

    'ses' => [
        'key' => env('AWS_SES_KEY', env('AWS_ACCESS_KEY_ID')),
        'secret' => env('AWS_SES_SECRET', env('AWS_SECRET_ACCESS_KEY')),
        'region' => env('AWS_SES_REGION', env('AWS_DEFAULT_REGION', 'us-east-1')),
        'configuration_set' => env('AWS_SES_CONFIGURATION_SET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for sending emails via standard SMTP. Can be used with
    | any SMTP provider as a fallback or alternative to SES.
    |
    */

    'smtp' => [
        'host' => env('SMTP_HOST', env('MAIL_HOST', '127.0.0.1')),
        'port' => env('SMTP_PORT', env('MAIL_PORT', 587)),
        'username' => env('SMTP_USERNAME', env('MAIL_USERNAME')),
        'password' => env('SMTP_PASSWORD', env('MAIL_PASSWORD')),
        'encryption' => env('SMTP_ENCRYPTION', 'tls'),
        'timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | From Address
    |--------------------------------------------------------------------------
    |
    | Default "from" address for all outgoing emails.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Inboxy'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Email campaigns are processed via queues for better performance.
    | Configure the queue connection and name for email jobs.
    |
    */

    'queue' => [
        'connection' => env('MAIL_QUEUE_CONNECTION', 'database'),
        'queue' => env('MAIL_QUEUE_NAME', 'emails'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Campaign Settings
    |--------------------------------------------------------------------------
    |
    | Settings for email campaign processing including throttling and limits.
    |
    */

    'campaign' => [
        // Number of emails to send per batch
        'batch_size' => env('CAMPAIGN_BATCH_SIZE', 100),
        
        // Delay between batches in seconds
        'batch_delay' => env('CAMPAIGN_BATCH_DELAY', 1),
        
        // Maximum retry attempts for failed emails
        'max_retries' => env('CAMPAIGN_MAX_RETRIES', 3),
        
        // SES sending rate limit (emails per second)
        'ses_rate_limit' => env('SES_RATE_LIMIT', 14),
    ],
];
