<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\User;

class ReportsController extends Controller
{
    /**
     * Display a simple reports dashboard for admins.
     */
    public function index()
    {
        // Aggregates
        $totalCourses = Course::count();

        // Users excluding admins (handle with/without Spatie helpers)
        try {
            $totalUsers = User::withoutRole('admin')->count();
        } catch (\Throwable $e) {
            $adminIds = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'admin')
                ->pluck('model_id');
            $totalUsers = User::whereNotIn('id', $adminIds)->count();
        }

        $totalCompletions = DB::table('course_user')->whereNotNull('completed_at')->count();

        try {
            $totalInstructors = User::role(['pengajar', 'instruktur'])->distinct()->count();
        } catch (\Throwable $e) {
            $instructorIds = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->whereIn('roles.name', ['pengajar', 'instruktur'])
                ->distinct()
                ->pluck('model_id');
            $totalInstructors = User::whereIn('id', $instructorIds)->count();
        }

        // Recent admin logs (optional)
        $recentLogs = collect();
        if (Schema::hasTable('activity_log')) {
            $recentLogs = DB::table('activity_log')
                ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
                ->orderByDesc('activity_log.created_at')
                ->limit(10)
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
                ->limit(10)
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

        return view('pages.reports.index', compact(
            'totalCourses', 'totalUsers', 'totalCompletions', 'totalInstructors', 'recentLogs'
        ));
    }
}
