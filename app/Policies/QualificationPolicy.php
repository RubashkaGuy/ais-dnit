<?php

namespace App\Policies;

use App\Models\Qualification;
use App\Models\User;

class QualificationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Qualification $qualification): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Qualification $qualification): bool
    {
        return true;
    }

    public function delete(User $user, Qualification $qualification): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Qualification $qualification): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Qualification $qualification): bool
    {
        return $user->isAdmin();
    }
}
