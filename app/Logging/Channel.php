<?php
declare(strict_types=1);

namespace App\Logging;

class Channel
{
    public const JOBS = 'job_daily';
    public const REQUESTS = 'requests';
    public const STDOUT = 'stdout';
}
