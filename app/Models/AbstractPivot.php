<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

abstract class AbstractPivot extends Pivot
{
    public $incrementing = true;

    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:sO';
}
