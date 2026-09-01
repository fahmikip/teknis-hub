<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_settings');
    }

    public function update(User $user, mixed $setting = null): bool
    {
        return $user->hasPermission('manage_settings');
    }
}