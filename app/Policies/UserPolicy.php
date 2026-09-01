<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_users');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_users');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasPermission('edit_users');
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasPermission('deactivate_users')
            && $user->id !== $target->id
            && ! $target->hasRole('SUPER ADMIN');
    }
}