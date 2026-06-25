<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Position $position): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Position $position): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Position $position): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Position $position): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Position $position): bool
    {
        return $user->isAdmin();
    }
}
