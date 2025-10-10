<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Module;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_in_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer',
            'instructions' => 'nullable|string',
            'module_id' => 'required|exists:modules,id',
            'course_id' => 'required|exists:courses,id'
        ]);

        try {
            $quiz = Quiz::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration_in_minutes' => $request->duration_in_minutes,
                'passing_score' => $request->passing_score,
                'max_attempts' => $request->max_attempts,
                'instructions' => $request->instructions,
                'module_id' => $request->module_id,
                'course_id' => $request->course_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kuis berhasil dibuat',
                'data' => $quiz
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kuis'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            return response()->json($quiz);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis tidak ditemukan'
            ], 404);
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_in_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:1',
            'instructions' => 'nullable|string'
        ]);

        try {
            $quiz = Quiz::findOrFail($id);

            $quiz->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration_in_minutes' => $request->duration_in_minutes,
                'passing_score' => $request->passing_score,
                'max_attempts' => $request->max_attempts,
                'instructions' => $request->instructions,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kuis berhasil diperbarui',
                'data' => $quiz
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kuis'
            ], 500);
        }
    }

    /**
     * Show the quiz management page with questions
     */
    public function manage(string $id)
    {
        try {
            $quiz = Quiz::with(['questions.options', 'module.course'])->findOrFail($id);

            return view('pages.course._partials.quiz-question', compact('quiz'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Kuis tidak ditemukan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $quiz = Quiz::findOrFail($id);
            $quiz->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kuis berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kuis'
            ], 500);
        }
    }
}
