<?php

use App\Logging\Channel;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    'request' => [
        'enable' => env('LOG_REQUESTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver'            => 'stack',
            'channels'          => ['daily'],
            'ignore_exceptions' => false,
        ],

        'jobs' => [
            'driver'            => 'stack',
            'channels'          => ['stdout', 'job_daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path'   => storage_path('logs/application.log'),
            'level'  => 'debug',
        ],

        Channel::JOBS => [
            'driver' => 'daily',
            'path'   => storage_path('logs/jobs.log'),
            'level'  => 'debug',
            'days'   => 1,
        ],

        'daily' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/application_daily.log'),
            'level'  => 'debug',
            'days'   => 1,
            'tap'    => [App\Logging\Handlers\AttachRequestId::class],
        ],

        Channel::REQUESTS => [
            'driver' => 'daily',
            'path'   => storage_path('logs/requests.log'),
            'level'  => 'debug',
            'days'   => 1,
            'tap'    => [App\Logging\Handlers\AttachRequestId::class],
        ],

        'slack' => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => 'critical',
        ],

        'stderr' => [
            'driver'    => 'monolog',
            'level'     => 'error',
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with'      => [
                'stream' => 'php://stderr',
            ],
        ],

        'stdout' => [
            'driver'    => 'monolog',
            'level'     => 'debug',
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDOUT_FORMATTER'),
            'with'      => [
                'stream' => 'php://stdout',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level'  => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level'  => 'debug',
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/emergency.log'),
            'days'   => 1,
        ],
    ],

];
