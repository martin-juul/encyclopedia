<?php

function human_duration($seconds): string
{
    return (new \App\Http\Blade\Time\HumanDuration($seconds))->humanize();
}

function human_bytes($bytes, $decimals = 2): string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }

    $factor = floor(log($bytes, 1024));
    return sprintf("%.{$decimals}f ", $bytes / (1024 ** $factor)) . ['B', 'KB', 'MB', 'GB', 'TB', 'PB'][$factor];
}

function any_to_string($value)
{
    if (is_object($value)) {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    if (is_array($value)) {
        return implode(PHP_EOL, \App\Utilities\Extensions\ArrExt::dotKeys($value));
    }

    return $value;
}
