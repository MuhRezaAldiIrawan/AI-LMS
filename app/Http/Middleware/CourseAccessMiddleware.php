<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Course;

class CourseAccessMiddleware
{
    /**
     * Handle an incoming request.
     * Check if user can access course based on enrollment and role
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin bisa akses semua course
        if (isAdmin()) {
            return $next($request);
        }

        // Get course ID from route parameter
        $courseId = $request->route('id');

        if ($courseId) {
            $course = Course::find($courseId);

            if (!$course) {
                return redirect()->back()->with('error', 'Kursus tidak ditemukan.');
            }

            // Pengajar - akses berdasarkan ownership dan enrollment
            if (isPengajar()) {
                // Jika pengajar adalah author course, bisa akses penuh
                if ($course->user_id === $user->id) {
                    return $next($request);
                }
                // Pengajar lain harus enrolled untuk mengakses course content
                if (!$user->isEnrolledIn($course)) {
                    // Pass course info ke request untuk handling di controller
                    $request->merge(['access_denied' => true, 'user_role' => 'pengajar']);
                    return $next($request);
                }
            }

            // Karyawan harus enrolled di course untuk mengakses
            if (isKaryawan()) {
                if (!$user->isEnrolledIn($course)) {
                    // Pass course info ke request untuk handling di controller
                    $request->merge(['access_denied' => true, 'user_role' => 'karyawan']);
                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}
