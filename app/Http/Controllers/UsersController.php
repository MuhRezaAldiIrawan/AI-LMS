<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Location;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('pages.users.users');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::orderBy('created_at', 'desc')->get();


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                        <a href="' . route('users.edit', $row->id) . '" class="btn btn-sm btn-icon btn-primary" >
                            <i class="ph ph-pencil"></i>
                        </a>

                        <button class="btn btn-icon btn-danger btn-pengguna-delete" data-id="' . $row->id . '" type="button" role="button">
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
        $user = new User(); //initial user data and the value is null of course wkwkw
        $roles = Role::all();
        $locations = Location::orderBy('name')->get();
        return view('pages.users.create', compact('action', 'user', 'roles', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required'],
            'photo' => ['nullable', 'image', 'max:1024'],
            'nik' => ['nullable', 'string', 'max:255', 'unique:users,nik'],
            'join_date' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'division' => ['nullable', 'string', 'max:255'],
            'location_id' => ['nullable', 'exists:locations,id'],
        ]);


        $userData = $request->only('name', 'email', 'nik', 'join_date', 'position', 'division', 'location_id');
        $userData['password'] = Hash::make($request->password);


        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile-photos', $fileName, 'public');
            $userData['profile_photo_path'] = $path;
        }

        $user = User::create($userData);
        $user->assignRole($request->role);

        return redirect()->route('users');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $action = 'edit';
        $user = User::find($id);
        $roles = Role::all();
        $userRole = $user->getRoleNames()->first();
        $locations = Location::orderBy('name')->get();

        return view('pages.users.create', compact('action', 'user', 'roles', 'userRole', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $user = User::findOrFail($id);

        $user->fill($request->only([
            'name', 'email', 'nik', 'join_date', 'position', 'division', 'location_id'
        ]));

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile-photos', $fileName, 'public');
            $user->profile_photo_path = $path;
        } else {
            unset($user->profile_photo_path);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $pengguna = User::findOrFail($id);

            $pengguna->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }

    public function profile()
    {
        $user = auth()->user();
        $lokasi = Location::orderBy('name')->get();
        return view('pages.userprofile.userprofile', compact('user', 'lokasi'));
    }

    public function updateProfile(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'photo' => ['nullable', 'image', 'max:1024'],
            'nik' => ['nullable', 'string', 'max:255', 'unique:users,nik,' . $user->id],
            'position' => ['nullable', 'string', 'max:255'],
            'division' => ['nullable', 'string', 'max:255'],
            'location_id' => ['nullable', 'exists:locations,id'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->nik = $request->nik;
        $user->position = $request->position;
        $user->division = $request->division;
        $user->location_id = $request->location_id;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile-photos', $fileName, 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
