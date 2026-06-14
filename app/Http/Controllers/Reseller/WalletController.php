<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user && (in_array(strtolower($user->role), ['chapter', 'reseller', 'agen']) || str_contains(strtolower($user->role), 'chapter'))) {
                return $next($request);
            }
            return redirect()->route('home')->with('error', 'Akses dibatasi.');
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));
        $wallet = $user->ensureWalletExists();

        // Dynamic Balance calculation based on EarningsService (Always All Time for availability)
        $totalEarningsAllTime = \App\Services\EarningsService::calculateTotalEarnings($user->id);
        $totalWithdrawnAllTime = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->whereIn('status', ['success', 'pending'])
            ->sum('amount');
        
        $availableBalance = $totalEarningsAllTime - $totalWithdrawnAllTime;
        
        // Monthly statistics for summary cards
        $monthlyIncome = \App\Services\EarningsService::calculateTotalEarnings($user->id, $selectedYear, $selectedMonth);
        $monthlyWithdrawal = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'success')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->sum('amount');

        // Hitung saldo tertahan (total current pending & rejected)
        $currentPending = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');

        // Sync the wallet balances in DB for record keeping
        $wallet->balance = $availableBalance;
        $wallet->pending_balance = $currentPending;
        $wallet->save();

        // Get dynamic incomes
        $dynamicIncomes = \App\Services\EarningsService::getDynamicTransactions($user->id, $selectedYear, $selectedMonth);

        // Fetch withdrawals from DB
        $withdrawalQuery = $wallet->transactions()->where('type', 'withdrawal')->latest();
        if ($selectedMonth) {
            $withdrawalQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedYear) {
            $withdrawalQuery->whereYear('created_at', $selectedYear);
        }
        $dbWithdrawals = $withdrawalQuery->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'created_at' => $t->created_at,
                'type' => 'withdrawal',
                'source' => 'Penarikan Dana',
                'description' => 'Penarikan ke rekening ' . $t->bank_name . ' (' . $t->account_number . ')',
                'amount' => $t->amount,
                'status' => $t->status,
                'reference_no' => $t->reference_no,
                'admin_note' => $t->admin_note,
            ];
        });

        // Merge and sort
        $recentTransactions = $dynamicIncomes->concat($dbWithdrawals)
            ->sortByDesc('created_at')
            ->take(10)
            ->map(function ($item) {
                return (object) $item;
            });

        $totalIncome = $monthlyIncome;
        $totalWithdrawal = $monthlyWithdrawal;

        return view('reseller.wallet.index', [
            'wallet'             => $wallet,
            'recentTransactions' => $recentTransactions,
            'totalIncome'        => $totalIncome,
            'totalWithdrawal'    => $totalWithdrawal,
            'availableBalance'   => $availableBalance,
            'selectedMonth'      => $selectedMonth,
            'selectedYear'       => $selectedYear,
            'savedBankName'      => $wallet->bank_name,
            'savedAccountNumber' => $wallet->account_number,
            'savedAccountName'   => $wallet->account_name,
        ]);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $wallet = $user->ensureWalletExists();
        
        $selectedMonth = $request->get('month');
        $selectedYear = $request->get('year');

        // Get dynamic incomes
        $dynamicIncomes = \App\Services\EarningsService::getDynamicTransactions($user->id, $selectedYear, $selectedMonth);

        // Fetch withdrawals from DB
        $withdrawalQuery = $wallet->transactions()->where('type', 'withdrawal')->latest();
        if ($selectedMonth) {
            $withdrawalQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedYear) {
            $withdrawalQuery->whereYear('created_at', $selectedYear);
        }
        $dbWithdrawals = $withdrawalQuery->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'created_at' => $t->created_at,
                'type' => 'withdrawal',
                'source' => 'Penarikan Dana',
                'description' => 'Penarikan ke rekening ' . $t->bank_name . ' (' . $t->account_number . ')',
                'amount' => $t->amount,
                'status' => $t->status,
                'reference_no' => $t->reference_no,
                'admin_note' => $t->admin_note,
            ];
        });

        $merged = $dynamicIncomes->concat($dbWithdrawals)->sortByDesc('created_at');

        if ($request->has('type') && $request->type !== 'all') {
            $merged = $merged->filter(fn($t) => $t['type'] === $request->type);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $merged = $merged->filter(fn($t) => $t['status'] === $request->status);
        }

        $transactions = $merged->map(fn($item) => (object)$item);

        // Paginate manually
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentPageItems = $transactions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $transactions->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('reseller.wallet.history', compact('wallet', 'transactions'));
    }

    public function withdraw(Request $request)
    {
        // Strip dots if any (from UI formatting)
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:100000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ], [
            'amount.min' => 'Minimal penarikan adalah Rp 100.000',
        ]);

        $user = Auth::user();
        $wallet = $user->ensureWalletExists();

        // RECALCULATE BALANCE BEFORE WITHDRAWAL (Security)
        $totalEarnings = \App\Services\EarningsService::calculateTotalEarnings($user->id);
        $totalWithdrawn = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->whereIn('status', ['success', 'pending'])
            ->sum('amount');
        $realAvailableBalance = $totalEarnings - $totalWithdrawn;

        if ($realAvailableBalance < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi untuk penarikan ini.');
        }

        DB::beginTransaction();
        try {
            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'type' => 'withdrawal',
                'status' => 'pending',
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'reference_no' => 'WD-' . strtoupper(substr(uniqid(), -6)),
            ]);

            // Simpan info rekening ke wallet untuk auto-fill berikutnya
            $wallet->bank_name      = $request->bank_name;
            $wallet->account_number = $request->account_number;
            $wallet->account_name   = $request->account_name;
            $wallet->save();

            DB::commit();
            return back()->with('success', 'Pengajuan penarikan berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $transaction = WalletTransaction::findOrFail($id);
        
        // Ensure user only deletes their own
        if ($transaction->wallet->user_id != Auth::id()) {
            abort(403);
        }

        // Only allow deleting rejected or pending withdrawals for users? 
        // Or all? Admin requested delete button. I'll allow all for now but maybe restricted for safety.
        $transaction->delete();

        return back()->with('success', 'Data transaksi berhasil dihapus.');
    }
}
