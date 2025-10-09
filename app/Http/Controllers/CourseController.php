<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseType;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $userId = Auth::id();
        $status = $request->query('status');
        $search = $request->query('search');

        $myCoursesQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
            ->where('user_id', $userId);

        $otherCoursesQuery = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])
            ->where('user_id', '!=', $userId);

        foreach ([$myCoursesQuery, $otherCoursesQuery] as $query) {
            if ($status && in_array($status, ['draft', 'published'])) {
                $query->where('status', $status);
            }
            if ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            }
        }

        $myCourses = $myCoursesQuery->latest()->paginate(4, ['*'], 'my_courses_page');
        $otherCourses = $otherCoursesQuery->latest()->paginate(4, ['*'], 'other_courses_page');

        return view('pages.course.course', compact('myCourses', 'otherCourses'));
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
    public function show(string $id)
    {
        $course = Course::with(['author', 'category', 'courseType', 'enrolledUsers', 'modules.lessons', 'modules.quiz'])->findOrFail($id);
        $categories = Category::all();
        $courseType = CourseType::all();

        return view('pages.course.show', compact('course', 'categories', 'courseType'));
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

        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:courses',
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
}
