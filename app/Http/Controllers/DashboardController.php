<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Get completed courses count
        $completedCoursesCount = $user->getCompletedCourses()->count();

        // Get in progress courses count
        $inProgressCoursesCount = $user->getOnProgressCourses()->count();

        // Get earned certificates count
        $earnedCertificatesCount = $user->certificates()->count();

        // Get total study time (formatted)
        $studyTime = $user->getFormattedStudyTime();

        // Get enrolled courses with progress (limit 5 for dashboard)
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
            'completedCoursesCount',
            'inProgressCoursesCount',
            'earnedCertificatesCount',
            'studyTime',
            'enrolledCourses'
        ));
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
