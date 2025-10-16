<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRewardController extends Controller
{
    /**
     * Menampilkan halaman "toko" reward untuk pengguna.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userPoints = $user->getTotalPoints();

        // Filter rewards
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $rewardsQuery = Reward::where('is_active', true);

        // Status filter
        if ($status === 'available') {
            $rewardsQuery->where(function ($query) {
                $query->where('stock', '>', 0)
                      ->orWhere('stock', -1);
            });
        } elseif ($status === 'out') {
            $rewardsQuery->where('stock', 0);
        } else {
            // 'all': show all active rewards
        }

        // Search filter
        if ($search) {
            $rewardsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%") ;
            });
        }

        $rewards = $rewardsQuery->get();

        // user's redemption history
        $redemptions = $user->rewardRedemptions()->with('reward')->latest()->get();

        // AJAX: return partial for reward list or history
        if ($request->ajax()) {
            if ($request->input('tab') === 'history') {
                $historyHtml = view('pages.rewards._partials.reward-history', ['redemptions' => $redemptions])->render();
                return response()->json(['html' => $historyHtml, 'type' => 'history']);
            } else {
                $rewardHtml = view('pages.rewards._partials.reward-list', ['rewards' => $rewards, 'userPoints' => $userPoints])->render();
                return response()->json(['html' => $rewardHtml, 'type' => 'rewards']);
            }
        }

        return view('pages.rewards.shop', compact('rewards', 'userPoints', 'redemptions'));
    }

    /**
     * Memproses permintaan klaim reward dari pengguna.
     */
    public function redeem(Request $request, Reward $reward)
    {
        $user = Auth::user();

        if ($user->getTotalPoints() < $reward->points_cost) {
            return back()->with('error', 'Poin Anda tidak cukup untuk menukarkan reward ini.');
        }

        if ($reward->stock == 0) {
            return back()->with('error', 'Maaf, stok untuk reward ini telah habis.');
        }

        // Kurangi poin pengguna
        $user->deductPoints($reward->points_cost, 'Menukar reward: ' . $reward->name, $reward);

        // Kurangi stok jika tidak tak terbatas
        if ($reward->stock != -1) {
            $reward->decrement('stock');
        }

        // Buat catatan penukaran
        RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_cost' => $reward->points_cost,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Selamat! Reward berhasil diklaim dan akan segera kami proses.');
    }
}
