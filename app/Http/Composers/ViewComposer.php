<?php
declare(strict_types=1);

namespace App\Http\Composers;

use Illuminate\View\View;

interface ViewComposer
{
    /**
     * Attach or modify view properties
     *
     * @param \Illuminate\View\View $view
     */
    public function compose(View $view): void;
}
