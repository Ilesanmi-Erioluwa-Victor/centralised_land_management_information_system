<?php

namespace App\Middleware;

use App\Helpers\Auth;

/**
 * Protects routes by user role.
 */
class RoleMiddleware
{
    /**
     * Enforce one of the allowed roles.
     *
     * @param array<int,string> $roles
     * @return void
     */
    public static function handle(array $roles): void
    {
        Auth::requireRole($roles);
    }
}
