<?php

namespace App\Policies;

use App\Collection;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function edit(User $user, Collection $collection)
    {
        return $user->id === $collection->user_id;
    }
}
