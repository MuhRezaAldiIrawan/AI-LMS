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
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return false;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

    $user = \Illuminate\Support\Facades\Auth::user();

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
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return null;
        }

        $user = \Illuminate\Support\Facades\Auth::user();

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

if (!function_exists('log_admin_activity')) {
    /**
     * Log an admin activity to admin_activities table.
     *
     * @param string $action          A short action key, e.g., 'user.created'
     * @param string|null $description Human readable description
     * @param string|null $subjectType Fully qualified class name of subject (e.g., App\Models\User::class)
     * @param int|null $subjectId      Subject primary key
     * @param array $properties        Additional payload
     * @return void
     */
    function log_admin_activity(string $action, ?string $description = null, ?string $subjectType = null, ?int $subjectId = null, array $properties = []): void
    {
        try {
            // Ensure table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('admin_activities')) {
                return;
            }

            $userId = \Illuminate\Support\Facades\Auth::check() ? (int) \Illuminate\Support\Facades\Auth::id() : null;

            $data = [
                'causer_id' => $userId,
                'action' => $action,
                'description' => $description,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('admin_activities', 'properties')) {
                $data['properties'] = empty($properties) ? null : json_encode($properties);
            }

            \Illuminate\Support\Facades\DB::table('admin_activities')->insert($data);
        } catch (\Throwable $e) {
            // Silent fail; optionally log to laravel log
            \Illuminate\Support\Facades\Log::debug('log_admin_activity failed: ' . $e->getMessage());
        }
    }
}
