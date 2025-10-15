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
            $query = RewardRedemption::with('user', 'reward')->orderBy('created_at', 'desc');

            if ($request->status && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('user_name', function($row){
                    return $row->user->name;
                })
                ->addColumn('reward_name', function($row){
                    return $row->reward->name;
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('d F Y H:i');
                })
                ->addColumn('action', function($row) {
                    $btn = '<button class="btn btn-sm btn-info me-2" onclick="showDetailModal('.$row->id.')">Detail</button>';
                    if ($row->status == 'pending') {
                        $btn .= '<button class="btn btn-sm btn-success" onclick="updateStatus('.$row->id.', \'processed\')">Proses</button>';
                    } elseif ($row->status == 'processed') {
                        $btn .= '<button class="btn btn-sm btn-primary" onclick="updateStatus('.$row->id.', \'completed\')">Selesaikan</button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function updateStatus(Request $request, RewardRedemption $redeemtion)
    {
        $request->validate([
            'status' => 'required|in:pending,processed,completed,rejected',
        ]);

        $redeemtion->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }
}
