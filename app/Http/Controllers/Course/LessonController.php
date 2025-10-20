<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Ensure Auth facade is imported
use App\Jobs\ProcessLessonContent;
use App\Models\Module;

class LessonController extends Controller
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content_type' => 'required|in:video,text,file',
            'video_url' => 'nullable|required_if:content_type,video|url',
            'content_text' => 'nullable|required_if:content_type,text',
            'attachment' => 'nullable|required_if:content_type,file|file|max:512000', // 10MB max
            'duration_in_minutes' => 'required|integer|min:0',
        ]);

        $dataToStore = $validated;

    // Authorization: only course owner can add lessons to the module
        $module = Module::findOrFail($request->module_id);
        $course = $module->course;
        $user = Auth::user();
    if (!$user || $course->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menambah pelajaran pada kursus ini.'
            ], 403);
        }

        // Handle file upload if content_type is 'file'
        if ($validated['content_type'] === 'file' && $request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('lessons/attachments', 'public');
            $dataToStore['attachment_path'] = $path;
        }

        $dataToStore['module_id'] = $request->module_id;

        // Store the lesson data
        $lesson = Lesson::create($dataToStore);

        // dd($lesson);

        ProcessLessonContent::dispatch($lesson);

        return response()->json([
            'success' => true,
            'lesson_id' => $lesson->id,
            'redirect_url' => route('course.show', $request->course_id) . '#kurikulum'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lesson = Lesson::with(['module.course.modules.lessons', 'module.course.modules.quiz.questions'])->findOrFail($id);

        // Check if user is enrolled and has access
        $user = Auth::user();
        $course = $lesson->module->course;

        // Hanya pemilik kursus, admin (view-only), atau user yang sudah terdaftar yang bisa mengakses konten pelajaran
        if (!$user) {
            abort(403, 'Anda belum terdaftar di kursus ini');
        }
        // Admin diizinkan melihat konten dalam mode read-only
        if (!($user->hasRole('admin'))) {
            if ($course->user_id !== $user->id && !$user->isEnrolledIn($course)) {
                abort(403, 'Anda belum terdaftar di kursus ini');
            }
        }

        // Get completion status
        $isCompleted = $lesson->isCompletedByUser($user);

        // Get all lessons sorted properly
        $allLessons = collect();
        foreach ($course->modules()->orderBy('order')->get() as $module) {
            foreach ($module->lessons()->orderBy('order')->get() as $lessonItem) {
                $allLessons->push($lessonItem);
            }
        }

        // Re-index collection for proper array access
        $allLessons = $allLessons->values();

        // Find current lesson index
        $currentIndex = null;
        foreach ($allLessons as $index => $lessonItem) {
            if ($lessonItem->id === $lesson->id) {
                $currentIndex = $index;
                break;
            }
        }

        // Get previous and next lessons
        $previousLesson = null;
        $nextLesson = null;

        if ($currentIndex !== null && is_int($currentIndex)) {
            $prevIndex = $currentIndex - 1;
            $nextIndex = $currentIndex + 1;

            if ($prevIndex >= 0 && $allLessons->has($prevIndex)) {
                $previousLesson = $allLessons->get($prevIndex);
            }

            if ($nextIndex < $allLessons->count() && $allLessons->has($nextIndex)) {
                $nextLesson = $allLessons->get($nextIndex);
            }
        }

        // Determine if this is the last lesson in its module
        $currentModule = $lesson->module;
        $moduleLessons = $currentModule->lessons()->orderBy('order')->get();
        $isLastLessonInModule = $moduleLessons->isNotEmpty() && $moduleLessons->last()->id === $lesson->id;

        // Module quiz (if exists) regardless of next lesson in other modules
        $moduleQuiz = $currentModule->quiz;

        // Whether user already passed module quiz
        $hasPassedModuleQuiz = false;
        if ($moduleQuiz && $user) {
            $hasPassedModuleQuiz = $user->quizAttempts()
                ->where('quiz_id', $moduleQuiz->id)
                ->where('passed', true)
                ->exists();
        }

        // Calculate course completion percentage
        $completionPercentage = $course->getCompletionPercentage($user);

        // Get all course modules for sidebar
        $courseModules = $course->modules()->with(['lessons', 'quiz.questions'])->orderBy('order')->get();

        // Get certificate if course already completed (to show download button at the very end)
        $certificate = null;
        if ($user && $course->isCompletedByUser($user)) {
            $certificate = $user->getCertificateForCourse($course->id);
        }

        return view('pages.lesson.show', compact(
            'lesson', 'isCompleted', 'previousLesson', 'nextLesson',
            'moduleQuiz', 'isLastLessonInModule', 'hasPassedModuleQuiz',
            'completionPercentage', 'courseModules', 'certificate'
        ));
    }

    /**
     * Mark lesson as completed
     */
    public function complete(Request $request, string $id)
    {
        $lesson = Lesson::findOrFail($id);
        $user = Auth::user();

        // Admin tidak diperbolehkan melakukan aksi penyelesaian
        if ($user && $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Admin hanya dapat melihat konten (read-only).'], 403);
        }

        // Check if user is enrolled
        if (!$user->isEnrolledIn($lesson->module->course)) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses'], 403);
        }

        // Mark as completed
        if (!$lesson->isCompletedByUser($user)) {
            $user->completedLessons()->attach($lesson->id, ['completed_at' => now()]);

            // Award 5 points for lesson completion
            $user->addPoints(5, "Menyelesaikan pelajaran: {$lesson->title}", $lesson);

            // Check if course is now 100% complete and trigger certificate generation
            $course = $lesson->module->course;
            if ($course->isCompletedByUser($user)) {
                $course->markAsCompletedFor($user);
            }
        }

        return response()->json(['success' => true, 'message' => 'Pelajaran berhasil diselesaikan']);
    }

    /**
     * Get lesson data for editing (AJAX)
     */
    public function edit(string $id)
    {
        $lesson = Lesson::findOrFail($id);

        return response()->json([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'summary' => $lesson->summary,
            'content_type' => $lesson->content_type,
            'video_url' => $lesson->video_url,
            'content_text' => $lesson->content_text,
            'attachment_path' => $lesson->attachment_path,
            'duration_in_minutes' => $lesson->duration_in_minutes,
            'module_id' => $lesson->module_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lesson = Lesson::findOrFail($id);

    // Authorization: only course owner can update lessons
        $user = Auth::user();
        $course = $lesson->module->course;
    if (!$user || $course->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah pelajaran ini.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content_type' => 'required|in:video,text,file',
            'video_url' => 'nullable|required_if:content_type,video|url',
            'content_text' => 'nullable|required_if:content_type,text',
            'attachment' => 'nullable|file|max:512000', // 10MB max
            'duration_in_minutes' => 'required|integer|min:0',
        ]);

        $dataToUpdate = $validated;

        // Capture original values to detect content changes
        $originalContentType = $lesson->content_type;
        $originalText = $lesson->content_text;
        $originalVideoUrl = $lesson->video_url;
        $originalAttachmentPath = $lesson->attachment_path;
        $shouldReprocess = false;

        // Handle file upload if content_type is 'file' and new file is uploaded
        if ($validated['content_type'] === 'file' && $request->hasFile('attachment')) {
            $file = $request->file('attachment');

            // Compare hashes to determine if the uploaded document is actually new/different
            $oldHash = null;
            if ($originalAttachmentPath && Storage::disk('public')->exists($originalAttachmentPath)) {
                $oldHash = @hash_file('sha256', Storage::disk('public')->path($originalAttachmentPath)) ?: null;
            }
            $newHash = @hash_file('sha256', $file->getRealPath());

            if ($oldHash && $newHash && hash_equals($oldHash, $newHash)) {
                // Same document content: keep existing file, do not replace, and do not reprocess (unless type changed)
                $dataToUpdate['attachment_path'] = $originalAttachmentPath;
                // No reprocess flag here; will still consider content_type change below
            } else {
                // Different document: replace file and mark for reprocessing
                // Delete old file if exists
                if ($originalAttachmentPath && Storage::disk('public')->exists($originalAttachmentPath)) {
                    Storage::disk('public')->delete($originalAttachmentPath);
                }

                $path = $file->store('lessons/attachments', 'public');
                $dataToUpdate['attachment_path'] = $path;
                $shouldReprocess = true;
            }
        }

        // Clear other content fields based on content type
        if ($validated['content_type'] !== 'video') {
            $dataToUpdate['video_url'] = null;
        }
        if ($validated['content_type'] !== 'text') {
            $dataToUpdate['content_text'] = null;
        }
        if ($validated['content_type'] !== 'file') {
            // Only clear attachment if content type is not file
            if ($lesson->attachment_path && Storage::disk('public')->exists($lesson->attachment_path)) {
                Storage::disk('public')->delete($lesson->attachment_path);
            }
            $dataToUpdate['attachment_path'] = null;
        }

        // Detect content changes to decide reprocessing
        if ($validated['content_type'] !== $originalContentType) {
            // Content type changed: always reprocess
            $shouldReprocess = true;
        } else {
            if ($validated['content_type'] === 'text') {
                // Compare text content (null-safe, trimmed)
                $newText = $validated['content_text'] ?? null;
                if (($newText !== null ? trim($newText) : null) !== ($originalText !== null ? trim($originalText) : null)) {
                    $shouldReprocess = true;
                }
            } elseif ($validated['content_type'] === 'video') {
                $newVideoUrl = $validated['video_url'] ?? null;
                if ($newVideoUrl !== $originalVideoUrl) {
                    $shouldReprocess = true;
                }
            } elseif ($validated['content_type'] === 'file') {
                // If no new file uploaded above, but path changed somehow
                if (($dataToUpdate['attachment_path'] ?? $originalAttachmentPath) !== $originalAttachmentPath) {
                    $shouldReprocess = true;
                }
            }
        }

        // Update the lesson
        $lesson->update($dataToUpdate);

        // Dispatch reprocess job only if content actually changed
        if ($shouldReprocess) {
            ProcessLessonContent::dispatch($lesson);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lesson updated successfully',
            'reprocessed' => $shouldReprocess,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

            // Authorization: only course owner can delete lessons
            $user = Auth::user();
            $course = $lesson->module->course;
            if (!$user || $course->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus pelajaran ini.'
                ], 403);
            }

            // Delete attached file if exists
            if ($lesson->attachment_path && Storage::disk('public')->exists($lesson->attachment_path)) {
                Storage::disk('public')->delete($lesson->attachment_path);
            }

            // Delete the lesson
            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lesson berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lesson'
            ], 500);
        }
    }
}
