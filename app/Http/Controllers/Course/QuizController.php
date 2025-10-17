<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Module;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            // Authorization: only course owner or admin can create quiz for the module
            $module = Module::findOrFail($request->module_id);
            $course = $module->course;
            $user = Auth::user();
            if (!$user || (!($user->hasRole('admin')) && $course->user_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk membuat kuis pada kursus ini.'
                ], 403);
            }

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
        $quiz = Quiz::with(['module.course', 'questions.options', 'attempts.user'])->findOrFail($id);

        // Check if user is enrolled
        $user = Auth::user();
        $course = $quiz->module->course;

        // Admin dan author course bisa akses tanpa enrollment
        if (!isAdmin() && $course->user_id !== $user->id && !$user->isEnrolledIn($course)) {
            abort(403, 'Anda belum terdaftar di kursus ini');
        }

        // Get user's quiz attempts
        $attempts = $quiz->attempts()->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $totalAttempts = $attempts->count();
        $bestScore = $attempts->max('score') ?? 0;
        $lastScore = $attempts->first()->score ?? 0;
        $averageScore = $attempts->avg('score') ?? 0;
        $isPassed = $attempts->where('passed', true)->isNotEmpty();
        $hasPassedQuiz = $isPassed;

        // Check if can take quiz
        $canTakeQuiz = $totalAttempts < $quiz->max_attempts;
        $canAttempt = $canTakeQuiz; // Alias for view compatibility
        $remainingAttempts = max(0, $quiz->max_attempts - $totalAttempts);

        // Get course completion percentage
        $completionPercentage = $course->getCompletionPercentage($user);

        // Get all course modules for sidebar
        $courseModules = $course->modules()->with(['lessons', 'quiz.questions'])->orderBy('order')->get();

        // Determine next module's first lesson after this quiz's module
        $orderedModules = $course->modules()->orderBy('order')->get()->values();
        $currentModuleIndex = $orderedModules->search(fn($m) => $m->id === $quiz->module_id);
        $nextModuleFirstLesson = null;
        if ($currentModuleIndex !== false) {
            $nextModule = $orderedModules->get($currentModuleIndex + 1);
            if ($nextModule) {
                $firstLesson = $nextModule->lessons()->orderBy('order')->first();
                if ($firstLesson) {
                    $nextModuleFirstLesson = $firstLesson;
                }
            }
        }

        // Certificate if course complete
        $certificate = null;
        if ($course->isCompletedByUser($user)) {
            $certificate = $user->getCertificateForCourse($course->id);
        }

        return view('pages.quiz.show', compact(
            'quiz', 'attempts', 'totalAttempts', 'bestScore',
            'lastScore', 'averageScore', 'isPassed', 'canTakeQuiz', 'canAttempt',
            'hasPassedQuiz', 'remainingAttempts', 'completionPercentage', 'courseModules',
            'nextModuleFirstLesson', 'certificate'
        ));
    }

    /**
     * Start quiz attempt
     */
    public function attempt(string $id)
    {
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);

        // Check if user is enrolled
        $user = Auth::user();
        $course = $quiz->module->course;

        // Admin dan author course bisa akses tanpa enrollment
        if (!isAdmin() && $course->user_id !== $user->id && !$user->isEnrolledIn($course)) {
            abort(403, 'Anda belum terdaftar di kursus ini');
        }

        // Check if user can take quiz
        $totalAttempts = $quiz->attempts()->where('user_id', $user->id)->count();
        if ($totalAttempts >= $quiz->max_attempts) {
            return redirect()->route('quiz.show', $quiz->id)
                ->with('error', 'Anda telah mencapai batas maksimal percobaan');
        }

        // Create new attempt
        $attempt = $quiz->attempts()->create([
            'user_id' => $user->id,
            'started_at' => now(),
            'score' => 0,
            'passed' => false
        ]);

        // Shuffle questions if needed
        $questions = $quiz->questions()->with('options')->get();

        // Get time limit (use duration_in_minutes from quiz)
        $timeLimit = $quiz->duration_in_minutes ?? 60;

        return view('pages.quiz.attempt', compact('quiz', 'attempt', 'questions', 'timeLimit'));
    }

    /**
     * Submit quiz attempt
     */
    public function submit(Request $request, string $quizId, string $attemptId)
    {
        $user = Auth::user();
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->firstOrFail();

        // Calculate score
        $answers = $request->input('answers', []);
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $correctOption = $question->options->where('is_correct', true)->first();

            $isCorrect = $correctOption && $userAnswer == $correctOption->id;

            if ($isCorrect) {
                $correctAnswers++;
            }

            // Save answer with is_correct value
            $attempt->answers()->updateOrCreate(
                ['question_id' => $question->id],
                [
                    'option_id' => $userAnswer,
                    'is_correct' => $isCorrect
                ]
            );
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        $isPassed = $score >= $quiz->passing_score;

        // Update attempt
        $attempt->update([
            'score' => $score,
            'passed' => $isPassed,
            'finished_at' => now()
        ]);

        // Award 10 points if passed quiz
        if ($isPassed) {
            $user = Auth::user();

            // Check if user already got points for this quiz before (prevent double points)
            $existingPassed = $quiz->attempts()
                ->where('user_id', $user->id)
                ->where('passed', true)
                ->where('id', '!=', $attempt->id)
                ->exists();

            // Only award points if this is first time passing this quiz
            if (!$existingPassed) {
                $user->addPoints(10, "Lulus kuis: {$quiz->title}", $quiz);
            }
        }

        // Check if course is now 100% complete and trigger certificate generation
        if ($isPassed) {
            $user = Auth::user();
            $course = $quiz->module->course;
            if ($course->isCompletedByUser($user)) {
                $certificate = $course->markAsCompletedFor($user);
                if ($certificate) {
                    session()->flash('course_completed', true);
                    session()->flash('certificate_info', [
                        'id' => $certificate->id,
                        'certificate_number' => $certificate->certificate_number,
                        'download_url' => $certificate->getDownloadUrl(),
                        'preview_url' => $certificate->getPreviewUrl(),
                    ]);
                } else {
                    session()->flash('course_completed', true);
                }
            }
        }

        $message = "Kuis selesai! Skor Anda: {$score}% - Jawaban Benar: {$correctAnswers}/{$totalQuestions} " .
                   ($isPassed ? '✅ (LULUS)' : '❌ (BELUM LULUS)');

        return redirect()->route('quiz.show', $quiz->id)
            ->with($isPassed ? 'success' : 'warning', $message)
            ->with('show_review', $attempt->id);
    }

    /**
     * Get quiz data for editing (AJAX)
     */
    public function edit(string $id)
    {
        $quiz = Quiz::findOrFail($id);

        return response()->json([
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'duration_in_minutes' => $quiz->duration_in_minutes,
            'passing_score' => $quiz->passing_score,
            'max_attempts' => $quiz->max_attempts,
            'instructions' => $quiz->instructions,
            'module_id' => $quiz->module_id,
        ]);
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

            // Authorization: only course owner or admin can update quiz
            $user = Auth::user();
            $course = $quiz->module->course;
            if (!$user || (!($user->hasRole('admin')) && $course->user_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah kuis ini.'
                ], 403);
            }

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

            // Authorization: only course owner or admin can manage quiz
            $user = Auth::user();
            $course = $quiz->module->course;
            if (!$user || (!($user->hasRole('admin')) && $course->user_id !== $user->id)) {
                return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengelola kuis ini.');
            }

            return view('pages.course._partials.quiz-question', compact('quiz'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Kuis tidak ditemukan');
        }
    }

    /**
     * Review quiz attempt with answers
     */
    public function reviewAttempt(string $attemptId)
    {
        $user = Auth::user();
        $attempt = QuizAttempt::with(['quiz.questions.options', 'answers', 'quiz.module.course'])
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $quiz = $attempt->quiz;
        $course = $quiz->module->course;

        // Check if user is enrolled
        $user = Auth::user();
        // Admin dan author course bisa akses tanpa enrollment
        if (!isAdmin() && $course->user_id !== $user->id && !$user->isEnrolledIn($course)) {
            abort(403, 'Anda belum terdaftar di kursus ini');
        }

        // Prepare questions with user answers and correct answers
        $reviewData = [];
        foreach ($quiz->questions as $question) {
            $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
            $correctOption = $question->options->where('is_correct', true)->first();
            $selectedOption = $userAnswer ? $question->options->where('id', $userAnswer->option_id)->first() : null;

            $reviewData[] = [
                'question' => $question,
                'user_answer' => $selectedOption,
                'correct_answer' => $correctOption,
                'is_correct' => $selectedOption && $correctOption && $selectedOption->id === $correctOption->id
            ];
        }

        return view('pages.quiz.review', compact('attempt', 'quiz', 'reviewData'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $quiz = Quiz::findOrFail($id);

            // Authorization: only course owner or admin can delete quiz
            $user = Auth::user();
            $course = $quiz->module->course;
            if (!$user || (!($user->hasRole('admin')) && $course->user_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus kuis ini.'
                ], 403);
            }
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
