<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RewardsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.rewards.rewards');
    }

    public function getRewards(Request $request)
    {
        if ($request->ajax()) {
            $data = Reward::orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    return $row->image ? '<img src="' . asset('storage/' . $row->image) . '" class="rounded" width="100" height="100" alt="Image">' : '';
                    // return '<img src="' . asset('storage/' . $row->image) . '" class="rounded" width="100" height="100" alt="Image">';
                })
                ->addColumn('stock', function ($row) {
                    return $row->stock == -1 ? 'Unlimited' : $row->stock;
                })
                ->addColumn('is_active', function ($row) {
                    return $row->is_active ? 'Aktif' : 'Tidak Aktif';
                })
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                        <a href="' . route('rewards.edit', $row->id) . '" class="btn btn-sm btn-icon btn-primary" >
                            <i class="ph ph-pencil"></i>
                        </a>

                        <button class="btn btn-icon btn-danger btn-rewards-delete" data-id="' . $row->id . '" type="button" role="button">
                            <i class="ph ph-trash"></i>
                        </button>

                        ';
                    return $btn;
                })
                ->rawColumns(['image', 'is_active', 'action'])
                ->make(true);
        }
    }

    public function redeemPage()
    {
        $rewards = Reward::where('is_active', true)->get();
        return view('pages.rewards.redeem', compact('rewards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = 'create';
        $rewards = new Reward();
        return view('pages.rewards.create', compact('rewards', 'action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'points_cost' => 'required|integer|min:1',
            'stock' => 'nullable',
            'is_active' => 'required|boolean',
        ]);

        if($request->stock == null){
            $validated['stock'] = -1;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('rewards', $fileName, 'public');

            $validated['image'] = $path;
        }

        Reward::create($validated);

        return redirect()->route('rewards');
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
        $rewards = Reward::findOrFail($id);
        return view('pages.rewards.create', compact('rewards', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'points_cost' => 'required|integer|min:1',
            'stock' => 'nullable',
            'is_active' => 'required|boolean',
        ]);

        if($request->stock == null){
            $validated['stock'] = -1;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('rewards', $fileName, 'public');

            $validated['image'] = $path;
        }

        $rewards = Reward::findOrFail($id);
        $rewards->update($validated);

        return redirect()->route('rewards');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
 try {
            $kategori = Reward::findOrFail($id);

            $kategori->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }
}
