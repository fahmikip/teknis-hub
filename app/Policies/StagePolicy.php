<?php

namespace App\Policies;

use App\Models\Stage;
use App\Models\User;

class StagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_stages');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_stages');
    }

    public function update(User $user, Stage $stage): bool
    {
        return $user->hasPermission('manage_stages');
    }

    public function delete(User $user, Stage $stage): bool
    {
        return $user->hasPermission('manage_stages');
    }
}