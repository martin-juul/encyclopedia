<?php

namespace App\Http\Composers;

use App\Facades\RequestId;
use Illuminate\View\View;

class RequestIdComposer implements ViewComposer
{
    /**
     * @inheritDoc
     */
    public function compose(View $view): void
    {
        $view->with('request_id', RequestId::get());
    }
}
