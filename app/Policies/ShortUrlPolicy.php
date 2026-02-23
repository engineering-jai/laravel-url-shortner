<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShortUrlPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        if ($user->isAdmin()) {
            return $shortUrl->company_id === $user->company_id;
        }
        return $shortUrl->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isMember();
    }

    public function update(User $user, ShortUrl $shortUrl): bool
    {
        return $this->view($user, $shortUrl);
    }

    public function delete(User $user, ShortUrl $shortUrl): bool
    {
        return $this->view($user, $shortUrl);
    }
}