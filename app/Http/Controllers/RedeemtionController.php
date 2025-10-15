<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RewardRedemption;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

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
                    // Allow admin to reject requests from pending or processed states
                    if ($row->status == 'pending') {
                        $btn .= '<button class="btn btn-sm btn-success me-2" onclick="updateStatus('.$row->id.', \'processed\')">Proses</button>';
                        $btn .= '<button class="btn btn-sm btn-danger" onclick="updateStatus('.$row->id.', \'rejected\')">Tolak</button>';
                    } elseif ($row->status == 'processed') {
                        $btn .= '<button class="btn btn-sm btn-primary me-2" onclick="updateStatus('.$row->id.', \'completed\')">Selesaikan</button>';
                        $btn .= '<button class="btn btn-sm btn-danger" onclick="updateStatus('.$row->id.', \'rejected\')">Tolak</button>';
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
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // keep previous status to avoid double refunds
        $previousStatus = $redeemtion->status;

        $data = ['status' => $request->status];
        if ($request->filled('admin_notes')) {
            $data['admin_notes'] = $request->admin_notes;
        }

        // perform update and potential refund/stock restore in a transaction
        DB::transaction(function () use ($redeemtion, $data, $previousStatus, $request) {
            $redeemtion->load('user', 'reward');
            $redeemtion->update($data);

            // If status changed to rejected and it wasn't rejected before, refund points and restore stock
            if ($request->status === 'rejected' && $previousStatus !== 'rejected') {
                $user = $redeemtion->user;
                // refund points back to user (this also creates a point log)
                $user->refundPoints($redeemtion->points_cost, 'Pengembalian poin untuk klaim reward dibatalkan: ' . ($redeemtion->reward->name ?? ''), $redeemtion);

                // restore reward stock if it's not unlimited (-1 indicates unlimited in this project)
                if ($redeemtion->reward && $redeemtion->reward->stock !== -1) {
                    $redeemtion->reward->increment('stock');
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }

    /**
     * Return redemption details as JSON for admin modal
     */
    public function show(RewardRedemption $redeemtion)
    {
        $redeemtion->load('user', 'reward');
        return response()->json([
            'id' => $redeemtion->id,
            'user' => [ 'id' => $redeemtion->user->id, 'name' => $redeemtion->user->name, 'email' => $redeemtion->user->email ],
            'reward' => [ 'id' => $redeemtion->reward->id, 'name' => $redeemtion->reward->name ],
            'points_cost' => $redeemtion->points_cost,
            'status' => $redeemtion->status,
            'admin_notes' => $redeemtion->admin_notes,
            'created_at' => $redeemtion->created_at->format('d F Y H:i'),
        ]);
    }
}
