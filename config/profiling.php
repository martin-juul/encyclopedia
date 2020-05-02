<?php

return [
    'enabled' => env('PROFILING_ENABLED', false),

    'xhprof' => [
        'enabled'                 => extension_loaded('xhprof'),
        'collect_additional_info' => env('XHPROF_EXTRA_INFO', true),

        // Default flags.
        // The config here uses integers, as the constants are only defined if
        // xhprof is installed. Change as needed.
        //
        // XHPROF_FLAGS_NO_BUILTINS = 1
        // XHPROF_FLAGS_CPU = 2
        // XHPROF_FLAGS_MEMORY = 4
        'flags'                   => 1 | 2 | 4,
    ],
];
