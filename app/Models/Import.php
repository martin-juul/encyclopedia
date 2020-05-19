<?php
declare(strict_types=1);

namespace App\Models;

/**
 * App\Models\Import
 *
 * @property string $id
 * @property string $path
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Import whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Import extends AbstractModel
{
    public const TYPE_ARTICLES = 'articles';

    protected $fillable = [
        'path',
        'type',
        'status',
        'reason',
        'ended_at',
    ];

    public static function getTypes(): array
    {
        return [
            self::TYPE_ARTICLES,
        ];
    }

    public static function completed(string $path, string $type)
    {
        return static::create([
            'path'     => $path,
            'type'     => $type,
            'status'   => 'complete',
            'ended_at' => now(),
        ]);
    }

    public static function failed(string $path, string $type, string $reason)
    {
        return static::create([
            'path'     => $path,
            'type'     => $type,
            'status'   => 'failed',
            'reason'   => $reason,
            'ended_at' => now(),
        ]);
    }

    public function setTypeAttribute(string $type): void
    {
        $types = static::getTypes();
        if (!\in_array($type, $types, true)) {
            throw new \InvalidArgumentException("Invalid type: $type valid options are: " . implode(', ', $types));
        }

        $this->attributes['type'] = $type;
    }
}
