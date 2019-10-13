<?php

namespace App\Policies;

use App\Ejaculation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EjaculationPolicy
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

    public function edit(User $user, Ejaculation $ejaculation): bool
    {
        return $user->id === $ejaculation->user_id;
    }
}
