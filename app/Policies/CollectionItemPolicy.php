<?php

namespace App\Policies;

use App\CollectionItem;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionItemPolicy
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

    public function edit(User $user, CollectionItem $item)
    {
        return $user->id === $item->collection->user_id;
    }
}
