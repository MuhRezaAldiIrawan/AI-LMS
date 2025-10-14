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

        // Admin bisa lihat semua courses
        if (canAccess('admin')) {
            $coursesQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz']);

            if ($status && $status !== 'all' && in_array($status, ['draft', 'published'])) {
                $coursesQuery->where('status', $status);
            }

            if ($search) {
                $coursesQuery->where('title', 'like', '%' . $search . '%');
            }

            $allCourses = $coursesQuery->latest()->paginate(8, ['*'], 'all_courses_page');

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
            $coursesQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz']);

            // Apply status filter - karyawan juga bisa filter status
            if ($status && $status !== 'all' && in_array($status, ['draft', 'published'])) {
                $coursesQuery->where('status', $status);
            }
            // Jika tidak ada filter atau filter = 'all', default ke published saja untuk karyawan
            elseif (!$status || $status === 'all') {
                $coursesQuery->where('status', 'published');
            }

            if ($search) {
                $coursesQuery->where('title', 'like', '%' . $search . '%');
            }

            $availableCourses = $coursesQuery->latest()->paginate(8, ['*'], 'available_courses_page');
            return view('pages.course.course', compact('availableCourses'))
                ->with('userRole', 'karyawan');
        }

        return redirect()->route('dashboard.index');
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

        Course::create($dataToStore);

        return redirect()->route('course');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $course = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz.questions'])->findOrFail($id);
        $user = Auth::user();

        // Redirect jika user tidak login
        if (!$user) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('karyawan')) {
            $isEnrolled = $user->isEnrolledIn($course);
            $hasAccess = $user->hasAccessToCourse($course);
            return view('pages.course._partials.employee-course', compact('course', 'isEnrolled', 'hasAccess'));
        }


        $categories = Category::all();
        $courseType = CourseType::all();
        $users = User::all();

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


        $isOwner = $course->user_id === $user->id;

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

            // Log point activity (opsional - bisa ditambahkan nanti)
            // $user->addPoints(10, $course, 'Mendaftar kursus: ' . $course->title);

            // Response untuk AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mendaftar di kursus ini!',
                    'redirect_url' => route('course.show', $course->id)
                ]);
            }

            return redirect()->route('course.show', $course->id)
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
        if ($request->action === 'toggle_publish') {
            try {
                $course = Course::findOrFail($id);
                $isPublished = $request->boolean('is_published');
                $newStatus = $isPublished ? 'published' : 'draft';

                $course->update([
                    'status' => $newStatus
                ]);

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

        return redirect()->route('course');
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
        $request->validate([
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id'
        ]);

        try {
            $course = Course::findOrFail($course);
            $participantIds = $request->input('participants', []);


            $course->enrolledUsers()->sync($participantIds);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peserta kursus berhasil diperbarui.',
                    'data' => [
                        'enrolled_count' => count($participantIds),
                        'course_id' => $course->id
                    ]
                ]);
            }

            return redirect()->route('course.show', $course->id)
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
