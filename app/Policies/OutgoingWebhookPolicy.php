<?php

namespace App\Policies;

use App\OutgoingWebhook;
use App\User;
use Illuminate\Auth\Access\Response;

class OutgoingWebhookPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OutgoingWebhook $outgoingWebhook): bool
    {
        return $user->id === $outgoingWebhook->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OutgoingWebhook $outgoingWebhook): bool
    {
        return $user->id === $outgoingWebhook->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OutgoingWebhook $outgoingWebhook): bool
    {
        return $user->id === $outgoingWebhook->user_id;
    }
}
