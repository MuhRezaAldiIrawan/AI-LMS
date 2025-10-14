<?php

if (!function_exists('setActive')) {
    function setActive($routes)
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (request()->routeIs($route)) {
                    return 'activePage';
                }
            }
        } else {
            if (request()->routeIs($routes)) {
                return 'activePage';
            }
        }
        return '';
    }
}

if (!function_exists('canAccess')) {
    /**
     * Check if current user can access certain functionality based on role
     *
     * @param string|array $roles
     * @return bool
     */
    function canAccess($roles)
    {
        if (!auth()->check()) {
            return false;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        $user = auth()->user();

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if current user is admin
     *
     * @return bool
     */
    function isAdmin()
    {
        return canAccess('admin');
    }
}

if (!function_exists('isPengajar')) {
    /**
     * Check if current user is pengajar
     *
     * @return bool
     */
    function isPengajar()
    {
        return canAccess('pengajar');
    }
}

if (!function_exists('isKaryawan')) {
    /**
     * Check if current user is karyawan
     *
     * @return bool
     */
    function isKaryawan()
    {
        return canAccess('karyawan');
    }
}

if (!function_exists('canManageCourses')) {
    /**
     * Check if user can create/edit/delete courses
     *
     * @return bool
     */
    function canManageCourses()
    {
        return canAccess(['admin', 'pengajar']);
    }
}

if (!function_exists('getUserRole')) {
    /**
     * Get user's primary role name
     *
     * @return string|null
     */
    function getUserRole()
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return 'admin';
        } elseif ($user->hasRole('pengajar')) {
            return 'pengajar';
        } elseif ($user->hasRole('karyawan')) {
            return 'karyawan';
        }

        return null;
    }
}

if (!function_exists('getUserRoleDisplay')) {
    /**
     * Get user's role display name
     *
     * @return string
     */
    function getUserRoleDisplay()
    {
        $role = getUserRole();

        switch ($role) {
            case 'admin':
                return 'Administrator';
            case 'pengajar':
                return 'Pengajar';
            case 'karyawan':
                return 'Karyawan';
            default:
                return 'User';
        }
    }
}
