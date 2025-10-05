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
    public function index(Request $request)
    {

        // dd('hello there');
        if ($request->ajax()) {

            $data = User::orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =
                        '
                            <button class="btn btn-icon btn-primary btn-pengguna-edit" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-edit"></i>
                            </button>

                            <button class="btn btn-icon btn-danger btn-pengguna-delete" data-id="' . $row->id . '" type="button" role="button">
                                <i class="anticon anticon-delete"></i>
                            </button>
                            ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
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
                        <button class="btn btn-icon btn-primary btn-pengguna-edit" data-id="' . $row->id . '" type="button" role="button">
                            <i class="ph ph-pencil-simple-line"></i>
                        </button>

                        <button class="btn btn-icon btn-danger btn-pengguna-delete" data-id="' . $row->id . '" type="button" role="button">
                            <i class="ph ph-trash-simple"></i>
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
        $roles = Role::all();
        $locations = Location::orderBy('name')->get();
        return view('pages.users.create', compact('roles', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // dd($request->all());
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
            $path = $file->move(public_path('storage/profile-photos'), $file->getClientOriginalName());
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
