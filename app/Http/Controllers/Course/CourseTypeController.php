<?php

namespace App\Http\Controllers\Course;
use App\Http\Controllers\Controller;

use App\Models\CourseType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CourseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.coursetype.coursetype');
    }

    public function getCourseType(Request $request)
    {
        if ($request->ajax()) {
            $data = CourseType::orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                        <a href="' . route('coursetype.edit', $row->id) . '" class="btn btn-sm btn-icon btn-primary" >
                            <i class="ph ph-pencil"></i>
                        </a>

                        <button class="btn btn-icon btn-danger btn-coursetype-delete" data-id="' . $row->id . '" type="button" role="button">
                            <i class="ph ph-trash"></i>
                        </button>

                        ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = 'create';
        $coursetype = new CourseType();
        return view('pages.coursetype.create', compact('coursetype', 'action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        CourseType::create($request->all());

        return redirect()->route('coursetype');
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
        $action = 'edit';
        $coursetype = CourseType::find($id);
        return view('pages.coursetype.create', compact('coursetype', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $coursetype = CourseType::find($id);
        $coursetype->update($request->all());

        return redirect()->route('coursetype');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $coursetype = CourseType::find($id);
        $coursetype->delete();
        return response()->json(['success' => 'Data berhasil dihapus']);
    }
}
