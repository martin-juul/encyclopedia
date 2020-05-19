<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AbstractModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AbstractModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AbstractModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AbstractModel query()
 * @mixin \Eloquent
 */
abstract class AbstractModel extends Model
{
    protected $keyType = 'string';

    protected $dateFormat = 'Y-m-d H:i:sO';

    /**
     * @param mixed $value
     *
     * @return string
     * @throws \JsonException
     */
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
