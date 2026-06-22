<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\PengajuanAnggaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanAnggaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');
        $isAdmin = strtolower($user->role) === 'administrator';

        $sortBy = $request->get('sort_by', 'tanggal_pengajuan');
        $sortOrder = $request->get('sort_order', 'desc');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $this->syncRecurringItems($month, $year);

        $selectedStatus = $request->get('status');
        $selectedApplicant = $request->get('applicant');
        $applicants = collect();

        $query = PengajuanAnggaran::with(['pengadaanBarang.buktiFotos']);

        // Linda and Administrator can see all requests
        if (!($isLinda || $isAdmin)) {
            $query->where('user_id', $user->id);
        } else {
            // Only fetch distinct applicants for the dropdown if user is Linda/Admin
            $applicants = PengajuanAnggaran::distinct()->pluck('diajukan_oleh');
            if ($selectedApplicant) {
                $query->where('diajukan_oleh', $selectedApplicant);
            }
        }

        if ($month && $month !== 'all') {
            $query->whereMonth('tanggal_pengajuan', $month);
        }

        if ($year && $year !== 'all') {
            $query->whereYear('tanggal_pengajuan', $year);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        // Apply Sorting
        if ($sortBy === 'diajukan_oleh') {
            $query->orderBy('diajukan_oleh', $sortOrder);
        } elseif ($sortBy === 'status') {
            $query->orderBy('status', $sortOrder);
        } elseif ($sortBy === 'jumlah_biaya') {
            $query->orderBy('jumlah_biaya', $sortOrder);
        } else {
            $query->orderBy('tanggal_pengajuan', $sortOrder);
        }

        // Calculate Totals before pagination
        $totalAwal = (clone $query)->sum('jumlah_biaya');
        $totalSetuju = (clone $query)->whereIn('status', ['approved', 'belum_lunas'])->sum('biaya_disetujui');
        $totalSisa = (clone $query)->whereIn('status', ['approved', 'belum_lunas'])
            ->get()
            ->sum(function ($q) {
                return $q->jumlah_biaya - $q->biaya_disetujui;
            });

        $requests = $query->paginate(100)->withQueryString();

        // Fetch overdue items (pending from previous months)
        $targetDate = Carbon::create($year, $month, 1);
        $overdueQuery = PengajuanAnggaran::whereIn('status', ['pending', 'belum_lunas', 'approved'])
            ->where('tanggal_pengajuan', '<', $targetDate->startOfMonth());

        if (!($isLinda || $isAdmin)) {
            $overdueQuery->where('user_id', $user->id);
        }

        $overdueItems = $overdueQuery->get()->groupBy(function ($item) {
            return $item->nama_pengajuan . '|' . $item->diajukan_oleh;
        });

        // Attach overdue items to requests
        foreach ($requests as $req) {
            $key = $req->nama_pengajuan . '|' . $req->diajukan_oleh;
            $req->overdue_items = $overdueItems->get($key, collect());
        }


        return view('admin.Finance.keuangan.pengajuan_anggaran', compact(
            'requests',
            'sortBy',
            'sortOrder',
            'month',
            'year',
            'selectedStatus',
            'selectedApplicant',
            'applicants',
            'isLinda',
            'isAdmin',
            'totalAwal',
            'totalSetuju',
            'totalSisa'
        ));
    }

    public function exportPDF(Request $request)
    {
        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');
        $isAdmin = strtolower($user->role) === 'administrator';

        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $query = PengajuanAnggaran::query();

        if (!($isLinda || $isAdmin)) {
            $query->where('user_id', $user->id);
            $displayName = $user->name;
        } else {
            $displayName = 'Semua Tim';
        }

        if ($month && $month !== 'all') {
            $query->whereMonth('tanggal_pengajuan', $month);
        }

        if ($year && $year !== 'all') {
            $query->whereYear('tanggal_pengajuan', $year);
        }

        $requests = $query->orderBy('tanggal_pengajuan', 'asc')->get();

        if ($month !== 'all') {
            $monthName = Carbon::createFromDate($year !== 'all' ? $year : date('Y'), $month, 1)->translatedFormat('F');
        } else {
            $monthName = 'Semua Bulan';
        }

        $yearDisplay = $year !== 'all' ? $year : 'Semua Tahun';

        $pdf = \PDF::loadView('admin.Finance.keuangan.pdf_pengajuan', compact('requests', 'month', 'year', 'monthName', 'displayName', 'yearDisplay'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_Pengajuan_Anggaran_{$monthName}_{$yearDisplay}.pdf");
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');
        $isAdmin = strtolower($user->role) === 'administrator';

        if ($isAdmin && !$isLinda) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Administrator hanya memiliki akses lihat.'], 403);
            }
            return redirect()->back()->with('error', 'Administrator hanya memiliki akses lihat.');
        }

        $request->validate([
            'nama_pengajuan' => 'required|string|max:255',
            'jumlah_biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal_pengajuan' => 'nullable|date',
            'is_recurring' => 'nullable|boolean',
            'recurring_interval' => 'nullable|string',
            'recurring_end_date' => 'nullable|date',
        ]);

        $pengajuan = PengajuanAnggaran::create([
            'tanggal_pengajuan' => $request->tanggal_pengajuan ?: Carbon::now(),
            'nama_pengajuan' => $request->nama_pengajuan,
            'jumlah_biaya' => $request->jumlah_biaya,
            'user_id' => Auth::id(),
            'diajukan_oleh' => Auth::user()->name,
            'status' => 'pending',
            'keterangan' => $request->keterangan,
            'is_recurring' => $request->is_recurring ?? false,
            'recurring_interval' => $request->recurring_interval,
            'recurring_end_date' => $request->recurring_end_date,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan anggaran berhasil dikirim.',
                'data' => [
                    'id' => $pengajuan->id,
                    'tanggal' => $pengajuan->tanggal_pengajuan->format('d/m/Y'),
                    'jam' => $pengajuan->tanggal_pengajuan->format('H:i'),
                    'nama' => $pengajuan->nama_pengajuan,
                    'biaya' => number_format($pengajuan->jumlah_biaya, 0, ',', '.'),
                    'keterangan' => $pengajuan->keterangan,
                    'status' => 'pending'
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Pengajuan anggaran berhasil dikirim.');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');

        if (!$isLinda) {
            return redirect()->back()->with('error', 'Hanya Keuangan yang dapat melakukan tindakan ini.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan_admin' => 'nullable|string',
            'biaya_disetujui' => 'nullable|numeric|min:0',
            'bukti_transfer' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:10240',
        ]);

        $anggaran = PengajuanAnggaran::findOrFail($id);

        $inputBiaya = $request->biaya_disetujui;
        // Jika status approved, kita anggap input adalah nominal tambahan atau total yang baru?
        // Berdasarkan permintaan user: "bertambah 3 juta", maka kita akumulasikan.
        // Jika input kosong, maka dianggap lunas (sisa dibayar semua).
        
        $currentApproved = $anggaran->biaya_disetujui ?? 0;
        $remaining = $anggaran->jumlah_biaya - $currentApproved;
        
        $addedAmount = $inputBiaya !== null ? $inputBiaya : $remaining;
        $totalApproved = $currentApproved + $addedAmount;
        
        $newStatus = $request->status;
        if ($newStatus === 'approved') {
            if ($totalApproved < $anggaran->jumlah_biaya) {
                $newStatus = 'belum_lunas';
            } else {
                $newStatus = 'approved';
                $totalApproved = $anggaran->jumlah_biaya; // Max out at total
            }
        } else {
            // Jika rejected, reset biaya disetujui? 
            // Biasanya kalau sudah ada cicilan tapi direject di tengah jalan tetap tersimpan?
            // Tapi untuk amannya kalau status 'rejected', kita ikuti input atau biarkan?
            // User tidak spesifik soal reject, tapi biasanya reject berarti tidak lanjut.
            $totalApproved = 0; 
        }

        $updateData = [
            'status' => $newStatus,
            'catatan_admin' => $request->catatan_admin,
            'biaya_disetujui' => $totalApproved
        ];

        if ($request->hasFile('bukti_transfer')) {
            $subFolder = 'uploads/bukti_transfer';
            $destinationPath = public_path($subFolder);

            // Delete old file if exists
            if ($anggaran->bukti_transfer && file_exists(public_path($anggaran->bukti_transfer))) {
                @unlink(public_path($anggaran->bukti_transfer));
            }

            $file = $request->file('bukti_transfer');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $safeName = \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
            $filename = 'bukti_trans_' . time() . '_' . $safeName;

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);
            $updateData['bukti_transfer'] = $subFolder . '/' . $filename;
        }

        $anggaran->update($updateData);

        // Sync status ACC, realisasi_dana, dan bukti_transfer ke PengadaanBarang yang terkait
        $pengadaanBarang = \App\Models\PengadaanBarang::where('pengajuan_anggaran_id', $anggaran->id)->first();
        if ($pengadaanBarang) {
            $syncPengadaan = [];
            if ($newStatus === 'approved') {
                $syncPengadaan['acc'] = 'Iya';
                $syncPengadaan['realisasi_dana'] = $totalApproved;
            } elseif ($newStatus === 'belum_lunas') {
                $syncPengadaan['acc'] = 'Belum Lunas';
                $syncPengadaan['realisasi_dana'] = $totalApproved;
            } elseif ($request->status === 'rejected') {
                $syncPengadaan['acc'] = 'Tidak';
                $syncPengadaan['realisasi_dana'] = null;
            }
            // Sync bukti_transfer jika Linda upload saat approve
            if (isset($updateData['bukti_transfer'])) {
                $syncPengadaan['bukti_transfer'] = $updateData['bukti_transfer'];
            }
            if (!empty($syncPengadaan)) {
                $pengadaanBarang->update($syncPengadaan);
            }

            // Sync foto ke tabel pengadaan_bukti_transfers agar muncul di grid operasional
            if (isset($updateData['bukti_transfer'])) {
                $filePath = $updateData['bukti_transfer'];
                $sudahAda = \App\Models\PengadaanBuktiTransfer::where('pengadaan_barang_id', $pengadaanBarang->id)
                    ->where('file_path', $filePath)->exists();
                if (!$sudahAda) {
                    \App\Models\PengadaanBuktiTransfer::create([
                        'pengadaan_barang_id' => $pengadaanBarang->id,
                        'file_path'           => $filePath,
                    ]);
                }
            }

            // Auto-sync ke Inventaris Kantor jika ACC disetujui (hindari duplikasi)
            // DIHAPUS: sync sekarang dipicu saat status_beli = "Sudah Dibeli" oleh operasional
        }

        $message = $request->status === 'approved' ? 'Pengajuan anggaran disetujui.' : 'Pengajuan anggaran ditolak.';
        return redirect()->back()->with('success', $message);
    }

    public function gantiBuktiFoto(Request $request, $id, $buktiId)
    {
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:10240',
        ]);

        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');
        if (!$isLinda) {
            return redirect()->back()->with('error', 'Hanya Staff Keuangan yang dapat mengganti foto ini.');
        }

        $anggaran = PengajuanAnggaran::findOrFail($id);

        // Cari record bukti di tabel pengadaan_bukti_transfers
        $pengadaanBarang = \App\Models\PengadaanBarang::where('pengajuan_anggaran_id', $anggaran->id)->first();
        if (!$pengadaanBarang) {
            return redirect()->back()->with('error', 'Pengadaan barang terkait tidak ditemukan.');
        }

        $bukti = \App\Models\PengadaanBuktiTransfer::where('id', $buktiId)
                    ->where('pengadaan_barang_id', $pengadaanBarang->id)
                    ->firstOrFail();

        $subFolder = 'uploads/bukti_transfer';
        $destinationPath = public_path($subFolder);

        // Hapus file lama
        if (file_exists(public_path($bukti->file_path))) {
            @unlink(public_path($bukti->file_path));
        }

        $file = $request->file('bukti_transfer');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $safeName = \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
        $filename = 'bukti_trans_' . time() . '_' . $safeName;

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);
        $newPath = $subFolder . '/' . $filename;

        // Update record bukti
        $bukti->update(['file_path' => $newPath]);

        // Jika ini adalah foto utama (bukti_transfer di pengadaan/pengajuan), update juga
        if ($pengadaanBarang->bukti_transfer === $bukti->file_path || $pengadaanBarang->bukti_transfer === null) {
            $pengadaanBarang->update(['bukti_transfer' => $newPath]);
        }
        if ($anggaran->bukti_transfer === $bukti->file_path || $anggaran->bukti_transfer === null) {
            $anggaran->update(['bukti_transfer' => $newPath]);
        }

        return redirect()->back()->with('success', 'Foto berhasil diganti.');
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:10240',
        ]);

        $anggaran = PengajuanAnggaran::findOrFail($id);

        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');
        $isAdmin = strtolower($user->role) === 'administrator';

        if ($isAdmin && !$isLinda) {
            return redirect()->back()->with('error', 'Administrator hanya memiliki akses lihat.');
        }

        if (!$request->hasFile('bukti_transfer')) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $subFolder = 'uploads/bukti_transfer';
        $destinationPath = public_path($subFolder);

        // Delete old file if exists
        if ($anggaran->bukti_transfer && file_exists(public_path($anggaran->bukti_transfer))) {
            @unlink(public_path($anggaran->bukti_transfer));
        }

        $file = $request->file('bukti_transfer');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $safeName = \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
        $filename = 'bukti_trans_' . time() . '_' . $safeName;

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);
        $filePath = $subFolder . '/' . $filename;

        // Simpan ke pengajuan_anggarans
        $anggaran->update(['bukti_transfer' => $filePath]);

        // Sync ke pengadaan_barangs yang terkait
        $pengadaanBarang = \App\Models\PengadaanBarang::where('pengajuan_anggaran_id', $anggaran->id)->first();
        if ($pengadaanBarang) {
            if ($pengadaanBarang->bukti_transfer && $pengadaanBarang->bukti_transfer !== $filePath
                && file_exists(public_path($pengadaanBarang->bukti_transfer))) {
                @unlink(public_path($pengadaanBarang->bukti_transfer));
            }
            $pengadaanBarang->update(['bukti_transfer' => $filePath]);

            // Sync ke tabel pengadaan_bukti_transfers (multiple foto) agar muncul di grid operasional
            $sudahAda = \App\Models\PengadaanBuktiTransfer::where('pengadaan_barang_id', $pengadaanBarang->id)
                ->where('file_path', $filePath)->exists();
            if (!$sudahAda) {
                \App\Models\PengadaanBuktiTransfer::create([
                    'pengadaan_barang_id' => $pengadaanBarang->id,
                    'file_path'           => $filePath,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Bukti transfer berhasil diunggah.');
    }

    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);

        $request->validate([
            'nama_pengajuan' => 'required|string|max:255',
            'jumlah_biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal_pengajuan' => 'nullable|date',
            'status' => 'nullable|string',
            'is_recurring' => 'nullable|boolean',
            'recurring_interval' => 'nullable|string',
            'recurring_end_date' => 'nullable|date',
        ]);

        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');
        $isAdmin = strtolower($user->role) === 'administrator';

        // Authorization: only the requester can edit
        // Administrator Monitor cannot edit
        if ($isAdmin && !$isLinda) {
            return response()->json(['success' => false, 'message' => 'Administrator hanya memiliki akses lihat.'], 403);
        }

        if ($pengajuan->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Only pending or rejected can be edited
        if ($pengajuan->status !== 'pending' && $pengajuan->status !== 'rejected') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajuan pending atau ditolak yang dapat diubah'], 422);
        }

        $updateData = [
            'nama_pengajuan' => $request->nama_pengajuan,
            'jumlah_biaya' => $request->jumlah_biaya,
            'keterangan' => $request->keterangan,
            'tanggal_pengajuan' => $request->tanggal_pengajuan ?? $pengajuan->tanggal_pengajuan,
            'is_recurring' => $request->has('is_recurring') ? $request->is_recurring : $pengajuan->is_recurring,
            'recurring_interval' => $request->has('recurring_interval') ? $request->recurring_interval : $pengajuan->recurring_interval,
            'recurring_end_date' => $request->has('recurring_end_date') ? $request->recurring_end_date : $pengajuan->recurring_end_date,
        ];

        // If explicitly resubmitting or editing a rejected one, set back to pending
        if ($request->status === 'pending' || $pengajuan->status === 'rejected') {
            $updateData['status'] = 'pending';
            $updateData['catatan_admin'] = null; // Clear rejection note
        }

        $oldIsRecurring = (bool) $pengajuan->is_recurring;
        $pengajuan->update($updateData);

        // If recurring is turned off, stop the chain
        if ($oldIsRecurring && !$pengajuan->is_recurring) {
            $futureQuery = PengajuanAnggaran::where('nama_pengajuan', $pengajuan->nama_pengajuan)
                ->where('user_id', $pengajuan->user_id)
                ->where('tanggal_pengajuan', '>', $pengajuan->tanggal_pengajuan);

            // 1. Delete future drafts/pending items
            (clone $futureQuery)->where('status', 'pending')->delete();

            // 2. Uncheck recurring for future approved/rejected items so they don't spawn more
            (clone $futureQuery)->whereIn('status', ['approved', 'rejected', 'ditolak'])
                ->update(['is_recurring' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil diperbarui.',
            'data' => [
                'nama' => $pengajuan->nama_pengajuan,
                'biaya' => number_format($pengajuan->jumlah_biaya, 0, ',', '.'),
                'keterangan' => $pengajuan->keterangan,
                'status' => $pengajuan->status
            ]
        ]);
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanAnggaran::findOrFail($id);
        $user = Auth::user();
        $isLinda = $user->hasHakAkses('finance_access');
        $isAdmin = strtolower($user->role) === 'administrator';

        // Authorization check
        // Administrators who are not Linda should ONLY be able to delete their own items if they submitted them, 
        // OR follow the current strict policy: Administrator = See only.

        if ($isAdmin && !$isLinda) {
            return response()->json(['success' => false, 'message' => 'Administrator hanya memiliki akses lihat.'], 403);
        }

        // Use non-strict comparison (!=) because ID types (int vs string) can vary depending on the database driver
        if ($pengajuan->user_id != $user->id && !$isLinda) {
            return response()->json(['success' => false, 'message' => 'Maaf, Anda hanya dapat menghapus pengajuan yang Anda buat sendiri.'], 403);
        }

        $pengajuan->delete();

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dihapus.']);
    }

    private function syncRecurringItems($month, $year)
    {
        if ($month === 'all' || $year === 'all')
            return;

        $targetDate = Carbon::create($year, $month, 1);
        $endOfMonth = (clone $targetDate)->endOfMonth();

        // Find items that are marked as recurring
        $recurringTemplates = PengajuanAnggaran::where('is_recurring', true)
            ->where('tanggal_pengajuan', '<', $targetDate)
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(function ($item) {
                return $item->nama_pengajuan . '|' . $item->diajukan_oleh;
            });

        foreach ($recurringTemplates as $key => $items) {
            $template = $items->sortByDesc('tanggal_pengajuan')->first();
            $interval = $template->recurring_interval ?: 'monthly';

            $lastDate = Carbon::parse($template->tanggal_pengajuan);
            $nextDate = clone $lastDate;

            // Calculate next due date based on interval
            while (true) {
                if ($interval === 'daily')
                    $nextDate->addDay();
                elseif ($interval === 'weekly')
                    $nextDate->addWeek();
                elseif ($interval === 'monthly')
                    $nextDate->addMonth();
                elseif ($interval === '3_monthly')
                    $nextDate->addMonths(3);
                elseif ($interval === '6_monthly')
                    $nextDate->addMonths(6);
                elseif ($interval === 'yearly')
                    $nextDate->addYear();
                else
                    break;

                // Stop if next date is beyond the current month being viewed
                if ($nextDate->gt($endOfMonth))
                    break;

                // Stop if next date is beyond original template's end date
                if ($template->recurring_end_date && $nextDate->gt($template->recurring_end_date)) {
                    break;
                }

                // Only process if next date is in the month being viewed
                if ($nextDate->year == $year && $nextDate->month == $month) {
                    // Check if already exists for this exact date (or just within the same month for non-daily)
                    $existsQuery = PengajuanAnggaran::where('nama_pengajuan', $template->nama_pengajuan)
                        ->where('diajukan_oleh', $template->diajukan_oleh);

                    if ($interval === 'daily') {
                        $existsQuery->whereDate('tanggal_pengajuan', $nextDate->toDateString());
                    } else {
                        $existsQuery->whereMonth('tanggal_pengajuan', $nextDate->month)
                            ->whereYear('tanggal_pengajuan', $nextDate->year);
                    }

                    if (!$existsQuery->exists()) {
                        PengajuanAnggaran::create([
                            'tanggal_pengajuan' => $nextDate,
                            'nama_pengajuan' => $template->nama_pengajuan,
                            'jumlah_biaya' => $template->jumlah_biaya,
                            'user_id' => $template->user_id,
                            'diajukan_oleh' => $template->diajukan_oleh,
                            'status' => 'pending',
                            'keterangan' => $template->keterangan,
                            'is_recurring' => true,
                            'recurring_interval' => $interval,
                            'recurring_end_date' => $template->recurring_end_date,
                        ]);
                    }
                }
            }
        }
    }
}
