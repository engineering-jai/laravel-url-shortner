<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvitationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function view(User $user, Invitation $invitation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return $user->isAdmin() && $invitation->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $this->view($user, $invitation);
    }

    public function restore(User $user, Invitation $invitation): bool
    {
        return false;
    }

    public function forceDelete(User $user, Invitation $invitation): bool
    {
        return false;
    }
}
