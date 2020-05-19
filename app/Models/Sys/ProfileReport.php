<?php
declare(strict_types=1);

namespace App\Models\Sys;

use App\Models\AbstractModel;
use App\Profiling\XHProfReport;

/**
 * App\Models\Sys\ProfileReport
 *
 * @property string $id
 * @property string $category
 * @property array $context
 * @property array $xhprof
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $cpu
 * @property int|null $wall_time
 * @property int|null $memory_usage
 * @property int|null $peak_memory_usage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereCpu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereMemoryUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport wherePeakMemoryUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereWallTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\ProfileReport whereXhprof($value)
 * @mixin \Eloquent
 */
class ProfileReport extends AbstractModel
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
        'cpu',
        'wall_time',
        'memory_usage',
        'peak_memory_usage',
    ];

    protected $casts = [
        'xhprof'  => 'array',
        'context' => 'array',
    ];

    public static function create($attributes = [])
    {
        if (!isset($attributes['total_time'])) {
            $runtime = (new XHProfReport($attributes))->getMainRuntime();
            $attributes['cpu'] = $runtime['cpu'];
            $attributes['wall_time'] = $runtime['wall_time'];
            $attributes['memory_usage'] = $runtime['memory_usage'];
            $attributes['peak_memory_usage'] = $runtime['peak_memory_usage'];
        }

        return parent::create($attributes);
    }

    public function getReport(): XHProfReport
    {
        return new XHProfReport($this);
    }
}
