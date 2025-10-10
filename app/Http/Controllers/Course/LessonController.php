<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

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

        // Handle file upload if content_type is 'file'
        if ($validated['content_type'] === 'file' && $request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('lessons/attachments', 'public');
            $dataToStore['attachment_path'] = $path;
        }

        $dataToStore['module_id'] = $request->module_id;

        // Store the lesson data
        Lesson::create($dataToStore);

        return response()->json([
            'success' => true,
            'redirect_url' => route('course.show', $request->course_id) . '#kurikulum'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lesson = Lesson::findOrFail($id);
        return response()->json($lesson);
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
        $lesson = Lesson::findOrFail($id);

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

        // Handle file upload if content_type is 'file' and new file is uploaded
        if ($validated['content_type'] === 'file' && $request->hasFile('attachment')) {
            // Delete old file if exists
            if ($lesson->attachment_path && Storage::disk('public')->exists($lesson->attachment_path)) {
                Storage::disk('public')->delete($lesson->attachment_path);
            }

            $file = $request->file('attachment');
            $path = $file->store('lessons/attachments', 'public');
            $dataToUpdate['attachment_path'] = $path;
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

        // Update the lesson
        $lesson->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Lesson updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);

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
