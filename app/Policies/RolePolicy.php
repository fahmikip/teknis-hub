<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_roles');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_roles');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermission('manage_roles');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermission('manage_roles')
            && ! in_array($role->name, ['SUPER ADMIN', 'ADMIN', 'OPERATOR', 'VIEWER'], true);
    }
}