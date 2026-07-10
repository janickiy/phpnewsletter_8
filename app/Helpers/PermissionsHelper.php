<?php

namespace App\Helpers;

use App\Enums\UserRole;

class PermissionsHelper
{
    /**
     * @param string $permissions
     * @return bool
     */
    public static function has_permission(string $permissions = ''): bool
    {
        if (\Auth::user()->role === UserRole::Admin->value) return true;

        $permissions = explode('|', $permissions);

        if (in_array(\Auth::user()->role, $permissions)) {
            return true;
        } else {
            return false;
        }
    }
}
