<?php
declare(strict_types=1);

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the user "creating" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function creating(User $user)
    {

    }

    /**
     * Handle the user "updated" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    public function saving(User $user)
    {

    }

    /**
     * Handle the user "deleted" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
