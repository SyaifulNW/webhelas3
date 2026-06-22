<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Models\SalesPlan;
use App\Services\EarningsService;
use Illuminate\Support\Facades\DB;

class AdminWalletController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && ($user->role === 'administrator' || $user->hasHakAkses('finance_access'))) {
                return $next($request);
            }
            return redirect('/home')->with('error', 'Akses ditolak.');
        });
    }

    public function index()
    {
        $wallets = Wallet::with('user')->get();

        // Sync each wallet balance to ensure Admin sees the same data as the User
        foreach ($wallets as $wallet) {
            if ($wallet->user) {
                $totalEarningsAllTime = \App\Services\EarningsService::calculateTotalEarnings($wallet->user_id);
                $totalWithdrawnAllTime = $wallet->transactions()
                    ->where('type', 'withdrawal')
                    ->whereIn('status', ['success', 'pending'])
                    ->sum('amount');
                
                $wallet->balance = $totalEarningsAllTime - $totalWithdrawnAllTime;

                $wallet->pending_balance = $wallet->transactions()
                    ->where('type', 'withdrawal')
                    ->where('status', 'pending')
                    ->sum('amount');
                
                $wallet->save();
            }
        }

        $pendingWithdrawals = WalletTransaction::where('type', 'withdrawal')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.Finance.wallet.index', compact('wallets', 'pendingWithdrawals'));
    }

    public function processWithdrawal(Request $request, $id)
    {
        $transaction = WalletTransaction::findOrFail($id);
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'nullable|string',
            'proof_of_transfer' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $wallet = $transaction->wallet;
            
            if ($request->action === 'approve') {
                // [FIX] Since balance is already "Available" (Total - Success - Pending), 
                // the pending transaction amount is already subtracted.
                // We check if (Available + This Transaction) >= This Transaction.
                if (($wallet->balance + $transaction->amount) < $transaction->amount) {
                    return back()->with('error', 'Saldo user tidak mencukupi untuk penarikan ini.');
                }
                
                // [FIX] We do NOT subtract again here because the sync logic in index() 
                // will handle it once the status changes to 'success'.
                $transaction->status = 'success';

                // Handle Proof of Transfer Upload
                if ($request->hasFile('proof_of_transfer')) {
                    $file = $request->file('proof_of_transfer');
                    $filename = 'WD_PROOF_' . time() . '_' . $transaction->id . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/wallet'), $filename);
                    $transaction->proof_of_transfer = 'uploads/wallet/' . $filename;
                }
            } else {
                $transaction->status = 'rejected';
            }

            $transaction->admin_note = $request->admin_note;
            $transaction->save();

            DB::commit();
            return back()->with('success', 'Transaksi berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function transactions()
    {
        $transactions = WalletTransaction::with('wallet.user')->latest()->paginate(50);

        // Sum of all earnings of all reseller and chapter users
        $users = User::whereIn('role', ['reseller', 'chapter'])->get();
        $totalIncome = 0;
        foreach ($users as $user) {
            $totalIncome += \App\Services\EarningsService::calculateTotalEarnings($user->id);
        }

        // Sum of all successful withdrawals
        $totalWithdrawal = WalletTransaction::where('type', 'withdrawal')
            ->where('status', 'success')
            ->sum('amount');

        return view('admin.Finance.wallet.transactions', compact('transactions', 'totalIncome', 'totalWithdrawal'));
    }

    public function destroyTransaction($id)
    {
        $transaction = WalletTransaction::findOrFail($id);
        $transaction->delete();
        return back()->with('success', 'Data transaksi berhasil dihapus.');
    }

    /**
     * Detail pendapatan lengkap per user (Chapter/Reseller/Agen)
     */
    public function earnings($userId)
    {
        $user = User::findOrFail($userId);
        $wallet = $user->ensureWalletExists();

        // Sync balance
        $totalEarningsAllTime = EarningsService::calculateTotalEarnings($user->id);
        $totalWithdrawnAllTime = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->whereIn('status', ['success', 'pending'])
            ->sum('amount');
        $availableBalance = $totalEarningsAllTime - $totalWithdrawnAllTime;

        $wallet->balance = $availableBalance;
        $wallet->pending_balance = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');
        $wallet->save();

        // === Earnings Breakdown ===
        $role = strtolower($user->role);
        $isChapter = ($role === 'chapter');
        $chapterName = $user->chapter;
        $cleanChapterName = trim(str_ireplace('CHAPTER', '', $chapterName));

        $resellerMembersIds = User::where('role', 'reseller')
            ->where('created_by', $userId)
            ->pluck('id');
        $allTeamIds = $resellerMembersIds->merge([$userId])->unique();

        $regionalTeamIds = $allTeamIds;
        if ($isChapter) {
            $regionalMemberIds = User::where('role', 'reseller')
                ->where('chapter', 'LIKE', '%' . $cleanChapterName . '%')
                ->pluck('id');
            $regionalTeamIds = $regionalMemberIds->merge([$userId])->unique();
        }

        // Base query
        $baseQuery = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
            ->where('salesplans.status', 'sudah_transfer')
            ->where('peserta_smis.approval_status', 'Approved');

        $sumSql = 'CAST(COALESCE(NULLIF(COALESCE(peserta_smis.spp_1, 0) + COALESCE(peserta_smis.spp_2, 0) + COALESCE(peserta_smis.spp_3, 0) + COALESCE(peserta_smis.spp_4, 0) + COALESCE(peserta_smis.spp_5, 0) + COALESCE(peserta_smis.spp_6, 0) + COALESCE(peserta_smis.spp_7, 0) + COALESCE(peserta_smis.spp_8, 0) + COALESCE(peserta_smis.spp_9, 0) + COALESCE(peserta_smis.spp_10, 0) + COALESCE(peserta_smis.spp_11, 0) + COALESCE(peserta_smis.spp_12, 0), 0), GREATEST(0, COALESCE(salesplans.nominal, 0) - 500000), 0) AS DECIMAL(15,2))';

        // Omset
        $omsetPribadi = (clone $baseQuery)->where('salesplans.created_by', $userId)->sum(DB::raw($sumSql));
        $omsetReseller = 0;
        if ($resellerMembersIds->isNotEmpty()) {
            $omsetReseller = (clone $baseQuery)->whereIn('salesplans.created_by', $resellerMembersIds)->sum(DB::raw($sumSql));
        }

        // Komisi, Royalti, Direct Fee, Bonus
        $komisi = $omsetPribadi * 0.10;
        $royalti = $omsetReseller * 0.05;

        $directFee = 0;
        $directFeeCount = 0;
        $isAgenPusat = (strtolower($user->role) === 'agen' && $user->kategori === 'Agen Pusat');
        if ($isChapter) {
            $directFeeCount = (clone $baseQuery)->whereIn('salesplans.created_by', $regionalTeamIds)->count();
            $directFee = $directFeeCount * 500000;
        } elseif ($isAgenPusat) {
            $directFeeCount = (clone $baseQuery)->where('salesplans.created_by', $userId)->count();
            $directFee = $directFeeCount * 200000;
        }

        // Bonus Pribadi - berdasarkan jumlah closing di bulan pertama user mendaftar
        // Rumus: jumlah_closing × Rp 2.000.000 × tier% (5% jika ≥10jt, 10% jika ≥20jt)
        $bonusPribadi = 0;
        $firstMonthClosingCount = 0;
        $firstMonthBase = 0;
        $userJoinMonth = $user->created_at ? $user->created_at->format('Y-m') : null;
        if ($userJoinMonth) {
            $firstMonthClosingCount = (clone $baseQuery)
                ->where('salesplans.created_by', $userId)
                ->whereRaw("DATE_FORMAT(salesplans.updated_at, '%Y-%m') = ?", [$userJoinMonth])
                ->count();
            $firstMonthBase = $firstMonthClosingCount * 2000000;
            if ($firstMonthBase >= 20000000) $bonusPribadi = $firstMonthBase * 0.10;
            elseif ($firstMonthBase >= 10000000) $bonusPribadi = $firstMonthBase * 0.05;
        }

        $bonusTim = 0;
        $totalTeamSales = $omsetPribadi + $omsetReseller;
        if ($resellerMembersIds->isNotEmpty() && $totalTeamSales >= 30000000) {
            $bonusTim = $totalTeamSales * 0.10;
        }

        // Detail peserta
        $pesertaList = (clone $baseQuery)
            ->whereIn('salesplans.created_by', $allTeamIds)
            ->select('salesplans.*', 'peserta_smis.nama as peserta_nama', 'peserta_smis.pembayaran_spp', 'peserta_smis.approval_status')
            ->orderBy('salesplans.updated_at', 'desc')
            ->get()
            ->map(function ($p) {
                $creator = User::find($p->created_by);
                $spp = (float)$p->pembayaran_spp;
                if ($spp <= 0) $spp = max(0, (float)$p->nominal - 500000);
                return (object)[
                    'peserta_nama' => $p->peserta_nama,
                    'cs_name' => $creator ? $creator->name : 'Unknown',
                    'cs_id' => $p->created_by,
                    'nominal' => (float)$p->nominal,
                    'spp' => $spp,
                    'tanggal' => $p->updated_at,
                    'is_own' => $p->created_by == $p->created_by,
                ];
            });

        // Tim reseller
        $teamMembers = User::whereIn('id', $resellerMembersIds)->get(['id', 'name', 'role', 'chapter']);

        // Withdrawal history
        $withdrawals = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->orderBy('created_at', 'desc')
            ->get();

        // Dynamic income transactions
        $dynamicIncomes = EarningsService::getDynamicTransactions($user->id);

        return view('admin.Finance.wallet.earnings', compact(
            'user', 'wallet', 'availableBalance', 'totalEarningsAllTime',
            'omsetPribadi', 'omsetReseller', 'komisi', 'royalti',
            'directFee', 'directFeeCount', 'bonusPribadi', 'bonusTim',
            'totalTeamSales', 'pesertaList', 'teamMembers',
            'withdrawals', 'dynamicIncomes', 'isChapter',
            'firstMonthClosingCount', 'firstMonthBase', 'userJoinMonth'
        ));
    }
}
