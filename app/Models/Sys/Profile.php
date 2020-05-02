<?php

namespace App\Models\Sys;

use App\Models\AbstractModel;
use App\Profiling\XHProfReport;

/**
 * App\Models\Sys\Profile
 *
 * @property string $id
 * @property string $category
 * @property array $context
 * @property array $xhprof
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\Profile whereXhprof($value)
 * @mixin \Eloquent
 */
class Profile extends AbstractModel
{
    public static $metrics = [
        // symbol, [shorthand, full name, sample type
        'wt'      => ['Wall', 'walltime', 'microseconds'],
        'ut'      => ['User', 'user cpu time', 'microseconds'],
        'st'      => ['Sys', 'system cpu time', 'microseconds'],
        'cpu'     => ['CPU', 'cpu time', 'microseconds'],
        'mu'      => ['MUse', 'memory usage', 'bytes'],
        'pmu'     => ['PMUse', 'peak memory usage', 'bytes'],
        'samples' => ['Samples', 'cpu time', 'samples'],
    ];

    protected $fillable = [
        'category',
        'xhprof',
        'context',
    ];

    protected $casts = [
        'xhprof'  => 'array',
        'context' => 'array',
    ];

    public function getReport(): XHProfReport
    {
        return new XHProfReport($this);
    }
}
