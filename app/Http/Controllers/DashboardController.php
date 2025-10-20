<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $user = Auth::user();

        // Default (karyawan) dashboard metrics – learner-centric
        $completedCoursesCount = $user->getCompletedCourses()->count();
        $inProgressCoursesCount = $user->getOnProgressCourses()->count();
        $earnedCertificatesCount = $user->certificates()->count();
        $studyTime = $user->getFormattedStudyTime();

        // For admin & instructor (pengajar): creator-centric metrics
        $createdCoursesCount = null;
        $assignedStudentsCount = null;
        $graduatedStudentsCount = null;
        $createdStudyTime = null; // HH:MM
        $createdCourses = collect();

        // Admin-wide aggregate metrics (system-level)
        $adminTotalCourses = null;
        $adminTotalUsers = null; // exclude admins
        $adminTotalCompletions = null; // count of course completions (course_user.completed_at)
        $adminTotalInstructors = null; // users with role pengajar/instruktur
        $recentCourses = collect(); // latest course activities

        if (function_exists('isAdmin') && isAdmin()) {
            // ADMIN VIEW: system-wide aggregates
            $adminTotalCourses = Course::count();

            // All users except those with role 'admin'
            try {
                $adminTotalUsers = User::withoutRole('admin')->count();
            } catch (\Throwable $e) {
                // Fallback if spatie helpers unavailable for any reason
                $adminRoleUserIds = DB::table('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('roles.name', 'admin')
                    ->pluck('model_id');
                $adminTotalUsers = User::whereNotIn('id', $adminRoleUserIds)->count();
            }

            // Total times courses were completed by anyone
            $adminTotalCompletions = DB::table('course_user')->whereNotNull('completed_at')->count();

            // Total instructors (pengajar/instruktur)
            try {
                $adminTotalInstructors = User::role(['pengajar', 'instruktur'])->distinct()->count();
            } catch (\Throwable $e) {
                $instructorRoleUserIds = DB::table('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->whereIn('roles.name', ['pengajar', 'instruktur'])
                    ->distinct()
                    ->pluck('model_id');
                $adminTotalInstructors = User::whereIn('id', $instructorRoleUserIds)->count();
            }

            // Recent course activities: latest created/updated courses
            $recentCourses = Course::with(['category','author'])
                ->withCount('enrolledUsers')
                ->orderByDesc('updated_at')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'category' => $course->category?->name ?? 'Uncategorized',
                        'author' => $course->author?->name ?? 'Unknown',
                        'thumbnail' => $course->thumbnail ? $course->thumbnail : 'default-thumbnail.jpg',
                        'participants' => $course->enrolled_users_count ?? 0,
                        'created_at' => $course->created_at,
                        'updated_at' => $course->updated_at,
                        'status' => $course->status,
                    ];
                });

            // Recent admin activities (from activity_log or admin_activities if available)
            $recentLogs = collect();
            if (Schema::hasTable('activity_log')) {
                $recentLogs = DB::table('activity_log')
                    ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
                    ->orderByDesc('activity_log.created_at')
                    ->limit(5)
                    ->get([
                        'activity_log.description',
                        'activity_log.created_at',
                        'users.name as causer_name',
                    ])->map(function ($row) {
                        return [
                            'text' => ($row->causer_name ? ($row->causer_name . ' ') : '') . ($row->description ?? ''),
                            'time' => $row->created_at,
                            'causer_name' => $row->causer_name,
                            'action' => null,
                            'description' => $row->description,
                        ];
                    });
            } elseif (Schema::hasTable('admin_activities')) {
                $recentLogs = DB::table('admin_activities')
                    ->leftJoin('users', 'users.id', '=', 'admin_activities.causer_id')
                    ->orderByDesc('admin_activities.created_at')
                    ->limit(5)
                    ->get([
                        'admin_activities.action',
                        'admin_activities.description',
                        'admin_activities.created_at',
                        'users.name as causer_name',
                    ])->map(function ($row) {
                        $desc = $row->description ?: $row->action;
                        return [
                            'text' => ($row->causer_name ? ($row->causer_name . ' ') : '') . $desc,
                            'time' => $row->created_at,
                            'causer_name' => $row->causer_name,
                            'action' => $row->action,
                            'description' => $desc,
                        ];
                    });
            }

            // Online users (sessions table if exists; fallback to recent activity)
            $onlineUserCount = 0;
            $window = now()->subMinutes(5);
            if (Schema::hasTable('sessions')) {
                // sessions.last_activity is a UNIX timestamp
                $onlineUserCount = DB::table('sessions')
                    ->where('last_activity', '>=', $window->getTimestamp())
                    ->count();
            } elseif (Schema::hasTable('activity_log')) {
                $onlineUserCount = DB::table('activity_log')
                    ->where('created_at', '>=', $window)
                    ->distinct('causer_id')
                    ->count('causer_id');
            } elseif (Schema::hasTable('admin_activities')) {
                $onlineUserCount = DB::table('admin_activities')
                    ->where('created_at', '>=', $window)
                    ->distinct('causer_id')
                    ->count('causer_id');
            }

            // System info
            $appVersion = config('app.version') ?? env('APP_VERSION', 'v1.0.0');
            $serverStatus = 'Online';
        } elseif (function_exists('isAdmin') && isPengajar()) {
            // Courses owned by current user (or all, if admin)
            $coursesQuery = Course::where('user_id', $user->id);

            // Summary cards
            $createdCoursesCount = (clone $coursesQuery)->count();

            // Total assigned students (count of enrollments across these courses)
            $courseIds = (clone $coursesQuery)->pluck('id');
            if ($courseIds->isEmpty()) {
                $assignedStudentsCount = 0;
                $graduatedStudentsCount = 0;
                $createdStudyTime = '00:00';
            } else {
                $assignedStudentsCount = DB::table('course_user')
                    ->whereIn('course_id', $courseIds)
                    ->count();

                // Distinct graduates (unique users with certificates for these courses)
                $graduatedStudentsCount = Certificate::whereIn('course_id', $courseIds)
                    ->distinct('user_id')
                    ->count('user_id');

                // Sum total lesson minutes for all lessons created under these courses
                $moduleIds = Module::whereIn('course_id', $courseIds)->pluck('id');
                $totalMinutes = $moduleIds->isEmpty()
                    ? 0
                    : (int) Lesson::whereIn('module_id', $moduleIds)->sum('duration_in_minutes');
                $createdStudyTime = $this->formatMinutesToHHMM($totalMinutes);
            }

            // Created courses list (limit 5 for dashboard)
            $createdCourses = (clone $coursesQuery)
                ->with(['category'])
                ->withCount('enrolledUsers')
                ->latest('updated_at')
                ->limit(5)
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'category' => $course->category?->name ?? 'Uncategorized',
                        'thumbnail' => $course->thumbnail ? $course->thumbnail : 'default-thumbnail.jpg',
                        'participants' => $course->enrolled_users_count ?? 0,
                        'created_at' => $course->created_at,
                        'updated_at' => $course->updated_at,
                        'status' => $course->status,
                    ];
                });
        }

        // Learner courses (karyawan) – enrolled courses with progress (limit 5)
        $enrolledCourses = $user->enrolledCourses()
            ->whereNotNull('enrolled_at')
            ->with(['modules.lessons', 'modules.quiz'])
            ->latest('enrolled_at')
            ->limit(5)
            ->get()
            ->map(function($course) use ($user) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'category' => $course->category?->name ?? 'Uncategorized',
                    'duration' => $course->duration_in_hours,
                    'progress' => $course->getCompletionPercentage($user),
                    'is_completed' => $course->isCompletedByUser($user),
                    'thumbnail' => $course->thumbnail ? $course->thumbnail : 'default-thumbnail.jpg',
                ];
            });

        return view('pages.dashboard.dashboard', compact(
            // learner-centric
            'completedCoursesCount',
            'inProgressCoursesCount',
            'earnedCertificatesCount',
            'studyTime',
            'enrolledCourses',
            // creator-centric
            'createdCoursesCount',
            'assignedStudentsCount',
            'graduatedStudentsCount',
            'createdStudyTime',
            'createdCourses',
            // admin aggregates
            'adminTotalCourses',
            'adminTotalUsers',
            'adminTotalCompletions',
            'adminTotalInstructors',
            'recentCourses',
            // admin sidebar data
            'recentLogs',
            'onlineUserCount',
            'appVersion',
            'serverStatus'
        ));
    }

    private function formatMinutesToHHMM(int $minutes): string
    {
        if ($minutes <= 0) return '00:00';
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
