<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RewardRedemption;
use Yajra\DataTables\Facades\DataTables;

class RedeemtionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.redeemtion.redeemtion');
    }

    public function getRedeemData(Request $request)
    {
        if ($request->ajax()) {
            $query = RewardRedemption::with('user', 'reward')->orderBy('created_at', 'desc')->get();

            if ($request->status && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    return '<button class="btn btn-sm btn-info">Detail</button>';
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
