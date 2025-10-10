<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Module;

class ModuleController extends Controller
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $createModule = Module::create([
            'title' => $request->title,
            'course_id' => $request->course_id
        ]);

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
        $request->validate(['title' => 'required|string|max:255']);
        $module = Module::findOrFail($id);
        $module->update(['title' => $request->title]);
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $module = Module::findOrFail($id);

            // Delete the module (lessons will be deleted due to cascade)
            $module->delete();

            return response()->json([
                'success' => true,
                'message' => 'Module berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus module'
            ], 500);
        }
    }
}
