<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseType;
use App\Models\User;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = Auth::id();
        $status = $request->query('status');
        $search = $request->query('search');

        // Admin: TAMPILKAN HANYA KURSUS PUBLISHED (satu bagian list)
        if (canAccess('admin')) {
            $allCoursesQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
                ->where('status', 'published');

            if ($search) {
                $allCoursesQuery->where('title', 'like', '%' . $search . '%');
            }

            $allCourses = $allCoursesQuery->latest()->paginate(8, ['*'], 'all_courses_page');

            return view('pages.course.course', compact('allCourses'))
                ->with('userRole', 'admin');
        }

        if (canAccess('pengajar')) {
            // My Courses Query
            $myCoursesQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
                ->where('user_id', $userId);

            // Apply status filter to my courses
            if ($status && $status !== 'all' && in_array($status, ['draft', 'published'])) {
                $myCoursesQuery->where('status', $status);
            }

            // Apply search filter to my courses
            if ($search) {
                $myCoursesQuery->where('title', 'like', '%' . $search . '%');
            }

            // Other Courses Query
            $otherCoursesQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
                ->where('user_id', '!=', $userId);

            // Apply status filter to other courses
            if ($status && $status !== 'all' && in_array($status, ['draft', 'published'])) {
                $otherCoursesQuery->where('status', $status);
            }
            // Jika tidak ada filter status atau status = 'all', tampilkan semua
            // (tidak perlu filter, biar tampil draft + published dari pengajar lain)

            // Apply search filter to other courses
            if ($search) {
                $otherCoursesQuery->where('title', 'like', '%' . $search . '%');
            }

            $myCourses = $myCoursesQuery->latest()->paginate(4, ['*'], 'my_courses_page');
            $otherCourses = $otherCoursesQuery->latest()->paginate(4, ['*'], 'other_courses_page');

            return view('pages.course.course', compact('myCourses', 'otherCourses'))
                ->with('userRole', 'pengajar');
        }

        if (canAccess('karyawan')) {
            // Karyawan: Dua bagian
            // 1) Kursus yang sudah di-enroll (enrolled_at not null)
            //    Dengan filter: all | on_progress (completed_at null) | completed (completed_at not null)
            // 2) Kursus yang sudah di-assign tetapi belum enroll (enrolled_at null)

            $enrolledQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
                ->where('status', 'published')
                ->whereHas('enrolledUsers', function ($q) use ($userId, $status) {
                    $q->where('user_id', $userId)
                      ->whereNotNull('enrolled_at');

                    // Terapkan filter progres jika diminta
                    if ($status === 'on_progress') {
                        $q->whereNull('completed_at');
                    } elseif ($status === 'completed') {
                        $q->whereNotNull('completed_at');
                    }
                });

            if ($search) {
                $enrolledQuery->where('title', 'like', '%' . $search . '%');
            }

            $enrolledCourses = $enrolledQuery->latest()->paginate(6, ['*'], 'enrolled_courses_page');

            // Assigned tapi belum enroll
            $assignedQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
                ->where('status', 'published')
                ->whereHas('enrolledUsers', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->whereNull('enrolled_at');
                });

            if ($search) {
                $assignedQuery->where('title', 'like', '%' . $search . '%');
            }

            $assignedCourses = $assignedQuery->latest()->paginate(6, ['*'], 'assigned_courses_page');

            return view('pages.course.course', compact('enrolledCourses', 'assignedCourses'))
                ->with('userRole', 'karyawan');
        }

        return redirect()->route('dashboard.index');
    }

    /**
     * Kursus Saya untuk Pengajar dalam mode pembelajar (mirip karyawan)
     */
    public function myCourses(Request $request)
    {
        if (!canAccess('pengajar')) {
            return redirect()->route('course');
        }

        $userId = Auth::id();
        $status = $request->query('status'); // all | on_progress | completed
        $search = $request->query('search');

        // Enrolled courses (published) dengan filter progress
        $enrolledQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
            ->where('status', 'published')
            ->whereHas('enrolledUsers', function ($q) use ($userId, $status) {
                $q->where('user_id', $userId)
                  ->whereNotNull('enrolled_at');

                if ($status === 'on_progress') {
                    $q->whereNull('completed_at');
                } elseif ($status === 'completed') {
                    $q->whereNotNull('completed_at');
                }
            });

        if ($search) {
            $enrolledQuery->where('title', 'like', '%' . $search . '%');
        }

        $enrolledCourses = $enrolledQuery->latest()->paginate(6, ['*'], 'enrolled_courses_page');

        // Assigned but not enrolled
        $assignedQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
            ->where('status', 'published')
            ->whereHas('enrolledUsers', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereNull('enrolled_at');
            });

        if ($search) {
            $assignedQuery->where('title', 'like', '%' . $search . '%');
        }

        $assignedCourses = $assignedQuery->latest()->paginate(6, ['*'], 'assigned_courses_page');

        // Reuse course view with learner-style sections
        return view('pages.course.course', compact('enrolledCourses', 'assignedCourses'))
            ->with('userRole', 'pengajar')
            ->with('learnerMode', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $course = new Course();
        $categories = Category::all();
        $courseType = CourseType::all();

        return view('pages.course.create', compact('course', 'categories', 'courseType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:courses',
            'description' => 'nullable|string',
            'summary' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            'category_id' => 'required|exists:categories,id',
            'course_type_id' => 'required|exists:course_types,id',
        ]);

        $dataToStore = $validatedData;
        $dataToStore['slug'] = Str::slug($request->title);
        $dataToStore['user_id'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $path = $file->storeAs('thumbnails', $fileName, 'public');

            $dataToStore['thumbnail'] = $path;
        }

        $course = Course::create($dataToStore);

        // Log admin/pengajar activity
        if (function_exists('log_admin_activity')) {
            \call_user_func('log_admin_activity', 'course.created', 'Membuat kursus baru: ' . $course->title, \App\Models\Course::class, $course->id);
        }

        // If AJAX, return redirect URL to course detail with Kurikulum tab
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Course berhasil disimpan.',
                'id' => $course->id,
                'redirect_url' => route('course.show', $course->id) . '#kurikulum',
            ]);
        }

        // Non-AJAX fallback: go to course detail page (Kurikulum tab)
        return redirect()->to(route('course.show', $course->id) . '#kurikulum');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $course = Course::with([
            'author',
            'category',
            'courseType',
            'enrolledUsers',
            'modules.lessons',
            'modules.quiz.questions'
        ])->findOrFail($id);

        $user = Auth::user();

        // Redirect jika user tidak login
        if (!$user) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Admin: tampilkan seperti overview/pembelajar saja (tanpa manage)
        if ($user->hasRole('admin')) {
            // Tampilkan halaman show dengan hanya overview aktif; isOwner = false
            $categories = Category::all();
            $courseType = CourseType::all();
            $users = collect();
            $isOwner = false;
            $isEnrolled = $user->isEnrolledIn($course);
            return view('pages.course.show', compact('course', 'categories', 'courseType', 'users', 'isOwner', 'isEnrolled'))
                ->with('accessDenied', false);
        }

        // Paksa mode pembelajar jika query string ?mode=learn
        $forceLearner = $request->query('mode') === 'learn';
        // Tampilkan tampilan pembelajar (employee) untuk Karyawan atau ketika dipaksa learnerMode
        if ($user->hasRole('karyawan') || $forceLearner) {
            // Load completed lessons untuk menghitung progress dengan efisien
            $user->load(['completedLessons', 'quizAttempts' => function($query) use ($course) {
                // Load quiz attempts hanya untuk quiz di course ini
                $quizIds = $course->modules->map->quiz->filter()->pluck('id');
                if ($quizIds->isNotEmpty()) {
                    $query->whereIn('quiz_id', $quizIds)->where('passed', true);
                }
            }]);

            $isEnrolled = $user->isEnrolledIn($course);
            $hasAccess = $user->hasAccessToCourse($course);
            return view('pages.course._partials.employee-course', compact('course', 'isEnrolled', 'hasAccess'));
        }


        $categories = Category::all();
        $courseType = CourseType::all();
        // Hanya tampilkan user dengan role karyawan pada langkah Kelola Peserta
        // Gunakan pagination agar daftar tetap ringan dan method links() tersedia di view
        $users = User::role('karyawan')
            ->orderBy('name')
            ->paginate(20);

        $accessDenied = $request->get('access_denied', false);
        $userRole = $request->get('user_role');

        if ($accessDenied) {
            $enrollmentMessage = '';
            $canEnroll = false;

            if ($userRole === 'pengajar') {
                $enrollmentMessage = 'Anda belum diberikan akses ke kursus ini. Silakan hubungi admin untuk mendapatkan akses.';
                $canEnroll = false;
            } elseif ($userRole === 'karyawan') {
                $enrollmentMessage = 'Anda perlu mendaftar di kursus ini untuk dapat mengakses konten pembelajaran.';
                $canEnroll = true;
            }

            return view('pages.course.show', compact('course', 'categories', 'courseType', 'users', 'enrollmentMessage', 'canEnroll'))
                ->with('accessDenied', true);
        }

        // Honor learner mode (e.g., Pengajar visiting as learner) untuk menentukan owner UI
        $isOwner = !$forceLearner && ($course->user_id === $user->id);

        $isEnrolled = $user->isEnrolledIn($course);

        return view('pages.course.show', compact(
            'course',
            'categories',
            'courseType',
            'users',
            'isOwner',
            'isEnrolled'
        ))->with('accessDenied', false);
    }

    /**
     * Enroll user ke course
     */
    public function enroll(Request $request, Course $course)
    {
        $user = Auth::user();


        if (!$user) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */

        if ($user->isEnrolledIn($course)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah terdaftar di kursus ini.'
                ]);
            }
            return redirect()->back()->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        if (!$user->hasAccessToCourse($course)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke kursus ini. Hubungi admin untuk mendapatkan akses.'
                ]);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kursus ini. Hubungi admin untuk mendapatkan akses.');
        }

        try {
            $user->enrolledCourses()->updateExistingPivot($course->id, [
                'enrolled_at' => now(),
                'updated_at' => now()
            ]);

            // Tentukan halaman redirect: modul & pelajaran pertama jika tersedia
            $course->loadMissing('modules.lessons');
            $firstLesson = $course->modules
                ->sortBy('order')
                ->flatMap->lessons
                ->sortBy('order')
                ->first();
            $redirectUrl = $firstLesson
                ? route('lesson.show', $firstLesson->id)
                : route('course.show', $course->id);

            // Log point activity (opsional - bisa ditambahkan nanti)
            // $user->addPoints(10, $course, 'Mendaftar kursus: ' . $course->title);

            // Response untuk AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mendaftar di kursus ini!',
                    'redirect_url' => $redirectUrl
                ]);
            }

            return redirect()->to($redirectUrl)
                ->with('success', 'Berhasil mendaftar di kursus ini!');

        } catch (\Exception $e) {
            \Log::error('Enrollment failed: ' . $e->getMessage());

            // Response untuk AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendaftar di kursus ini. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal mendaftar di kursus ini. Silakan coba lagi.');
        }
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
    // Authorization: only owner can update
        $courseAuth = Course::findOrFail($id);
        $user = Auth::user();
    if (!$user || $courseAuth->user_id !== $user->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah kursus ini.'
                ], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        if ($request->action === 'toggle_publish') {
            try {
                $course = $courseAuth; // already found above
                $isPublished = $request->boolean('is_published');
                $newStatus = $isPublished ? 'published' : 'draft';

                // Prevent publish if requirements not met
                if ($isPublished) {
                    // Load relations used by validation
                    $course->loadMissing('modules.lessons', 'enrolledUsers');
                    $errors = $course->getPublishValidationErrors();
                    if (!empty($errors)) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Tidak dapat publish. Lengkapi data berikut:',
                                'errors' => $errors,
                            ], 422);
                        }
                        return redirect()->back()->withErrors($errors)->with('error', 'Tidak dapat publish. Lengkapi data kursus.');
                    }
                }

                $course->update([
                    'status' => $newStatus
                ]);

                // Log publish/unpublish action
                if (function_exists('log_admin_activity')) {
                    $action = $isPublished ? 'course.published' : 'course.unpublished';
                    $desc = ($isPublished ? 'Mempublish' : 'Meng-unpublish') . ' kursus: ' . $course->title;
                    \call_user_func('log_admin_activity', $action, $desc, \App\Models\Course::class, $course->id);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $isPublished ? 'Kursus berhasil dipublish.' : 'Kursus berhasil di-unpublish.',
                        'data' => [
                            'status' => $course->status,
                            'is_published' => $isPublished
                        ]
                    ]);
                }

                return redirect()->back()
                    ->with('success', $isPublished ? 'Kursus berhasil dipublish.' : 'Kursus berhasil di-unpublish.');

            } catch (\Exception $e) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengubah status kursus.'
                    ], 500);
                }

                return redirect()->back()
                    ->with('error', 'Gagal mengubah status kursus.');
            }
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:courses,title,' . $id,
            'description' => 'nullable|string',
            'summary' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            'category_id' => 'required|exists:categories,id',
            'course_type_id' => 'required|exists:course_types,id',
        ]);

        $dataToStore = $validatedData;
        $dataToStore['slug'] = Str::slug($request->title);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $path = $file->storeAs('thumbnails', $fileName, 'public');

            $dataToStore['thumbnail'] = $path;
        }

    Course::where('id', $id)->update($dataToStore);

        // Log course updated
        if (function_exists('log_admin_activity')) {
            $courseUpdated = Course::find($id);
            $title = $courseUpdated ? $courseUpdated->title : ('ID ' . $id);
            \call_user_func('log_admin_activity', 'course.updated', 'Memperbarui kursus: ' . $title, \App\Models\Course::class, (int) $id);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Course berhasil diperbarui.',
                'redirect_url' => route('course')
            ]);
        }

        return redirect()->route('course')->with('success', 'Data Course berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function updateParticipants(Request $request, $course)
    {
    // Authorization: only owner can update participants
        $courseModel = Course::findOrFail($course);
        $user = Auth::user();
    if (!$user || $courseModel->user_id !== $user->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengelola peserta kursus ini.'
                ], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id'
        ]);

        try {
            $participantIds = $request->input('participants', []);

            // Validasi tambahan di sisi server: hanya ijinkan user dengan role karyawan
            $validIds = User::role('karyawan')
                ->whereIn('id', $participantIds)
                ->pluck('id')
                ->toArray();

            // Jika ada ID yang bukan karyawan, beri respon error agar jelas bagi klien
            if (count($validIds) !== count($participantIds)) {
                $message = 'Hanya pengguna berperan karyawan yang dapat ditambahkan sebagai peserta.';
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                return redirect()->back()->withErrors(['participants' => $message]);
            }

            $courseModel->enrolledUsers()->sync($validIds);

            // Log participant updates
            if (function_exists('log_admin_activity')) {
                \call_user_func('log_admin_activity', 'course.participants_updated', 'Memperbarui peserta untuk kursus: ' . $courseModel->title, \App\Models\Course::class, $courseModel->id, [
                    'count' => count($validIds)
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peserta kursus berhasil diperbarui.',
                    'data' => [
                        'enrolled_count' => count($validIds),
                        'course_id' => $courseModel->id
                    ]
                ]);
            }

            return redirect()->route('course.show', $courseModel->id)
                ->with('success', 'Peserta kursus berhasil diperbarui.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui peserta kursus.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui peserta kursus.');
        }
    }
}
