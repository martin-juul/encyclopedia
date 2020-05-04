<?php

namespace App\Http\Composers;

use App\Models\User;
use Illuminate\View\View;

class UserComposer implements ViewComposer
{
    public function compose(View $view): void
    {
        $user = \Auth::getUser();

        if (!$user) {
            $user = User::make(['name' => 'Guest', 'email' => 'guest@localhost']);
        }

        $view->with([
            'user' => $user,
        ]);
    }
}
