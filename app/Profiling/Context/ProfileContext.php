<?php
declare(strict_types=1);

namespace App\Profiling\Context;

use Illuminate\Contracts\Support\Arrayable;

abstract class ProfileContext implements Arrayable
{
    public string $category;
}
