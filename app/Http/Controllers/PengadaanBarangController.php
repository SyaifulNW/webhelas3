<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengadaanBarang;
use App\Models\PengajuanAnggaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengadaanBarangController extends Controller
{
    public function store(Request $request)
    {
        $item = PengadaanBarang::create([
            'nama_barang'    => $request->nama_barang ?? '',
            'jumlah'         => $request->jumlah ?? '',
            'budget'         => $request->budget ?? 0,
            'realisasi_dana' => null,
            'acc'            => 'Pending',
        ]);

        // Otomatis buat PengajuanAnggaran untuk Linda
        $user = Auth::user();
        $pengajuan = PengajuanAnggaran::create([
            'tanggal_pengajuan' => Carbon::now(),
            'nama_pengajuan'    => ($item->nama_barang ?: 'Pengadaan Barang') . ' (Pengadaan)',
            'jumlah_biaya'      => is_numeric($item->budget) ? $item->budget : 0,
            'user_id'           => $user->id,
            'diajukan_oleh'     => $user->name,
            'status'            => 'pending',
            'keterangan'        => 'Otomatis dikirim dari pengadaan barang operasional. Jumlah: ' . ($item->jumlah ?? '-'),
            'is_recurring'      => false,
        ]);

        $item->update(['pengajuan_anggaran_id' => $pengajuan->id]);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = PengadaanBarang::findOrFail($id);

        // Jangan izinkan update acc, progress, realisasi_dana, dan pengajuan_anggaran_id dari operasional
        $data = $request->except('acc', 'progress', 'realisasi_dana', 'pengajuan_anggaran_id');

        // status_beli hanya boleh diubah jika ACC sudah Iya
        if (isset($data['status_beli']) && $item->acc !== 'Iya') {
            unset($data['status_beli']);
        }

        // Jika nama_barang atau budget berubah, sync ke pengajuan anggaran terkait
        if ($item->pengajuan_anggaran_id) {
            $pengajuan = PengajuanAnggaran::find($item->pengajuan_anggaran_id);
            if ($pengajuan && $pengajuan->status === 'pending') {
                $syncData = [];
                if (isset($data['nama_barang']) && $data['nama_barang'] !== '') {
                    $syncData['nama_pengajuan'] = $data['nama_barang'] . ' (Pengadaan)';
                }
                if (isset($data['budget'])) {
                    $cleanBudget = is_string($data['budget'])
                        ? (float) str_replace(['.', ','], ['', '.'], $data['budget'])
                        : (float) $data['budget'];
                    $syncData['jumlah_biaya'] = $cleanBudget;
                }
                if (!empty($syncData)) {
                    $pengajuan->update($syncData);
                }
            }
        }

        $item->update($data);

        // Jika status_beli berubah jadi "Sudah Dibeli", sync ke inventaris
        $item->refresh();
        $synced = $item->syncToInventaris();

        return response()->json([
            'success'              => true,
            'data'                 => $item,
            'is_inventory_created' => $item->is_inventory_created,
            'synced_to_inventory'  => $synced,
        ]);
    }

    public function destroy($id)
    {
        $item = PengadaanBarang::findOrFail($id);

        // Hapus juga pengajuan anggaran terkait jika masih pending
        if ($item->pengajuan_anggaran_id) {
            $pengajuan = PengajuanAnggaran::find($item->pengajuan_anggaran_id);
            if ($pengajuan && $pengajuan->status === 'pending') {
                $pengajuan->delete();
            }
        }

        $item->delete();
        return response()->json(['success' => true]);
    }

    public function addBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:10240',
        ]);

        $item = PengadaanBarang::findOrFail($id);

        if (!$request->hasFile('bukti_transfer')) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 422);
        }

        $subFolder = 'uploads/bukti_transfer';
        $destinationPath = public_path($subFolder);

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

        // Simpan ke tabel bukti_transfers (multiple)
        $bukti = \App\Models\PengadaanBuktiTransfer::create([
            'pengadaan_barang_id' => $item->id,
            'file_path'           => $filePath,
        ]);

        // Jika ini foto pertama dan bukti_transfer utama masih kosong, set juga ke field utama
        if (!$item->bukti_transfer) {
            $item->update(['bukti_transfer' => $filePath]);
            // Sync ke pengajuan anggaran
            if ($item->pengajuan_anggaran_id) {
                $pengajuan = PengajuanAnggaran::find($item->pengajuan_anggaran_id);
                if ($pengajuan) {
                    $pengajuan->update(['bukti_transfer' => $filePath]);
                }
            }
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Foto bukti berhasil ditambahkan.',
            'bukti_id'  => $bukti->id,
            'file_url'  => asset($filePath),
            'file_path' => $filePath,
        ]);
    }

    public function deleteBukti(Request $request, $id, $buktiId)
    {
        $bukti = \App\Models\PengadaanBuktiTransfer::where('id', $buktiId)
                    ->where('pengadaan_barang_id', $id)
                    ->firstOrFail();

        if (file_exists(public_path($bukti->file_path))) {
            @unlink(public_path($bukti->file_path));
        }

        $bukti->delete();

        // Jika field utama mengarah ke foto yang dihapus, update ke foto berikutnya
        $item = PengadaanBarang::findOrFail($id);
        if ($item->bukti_transfer === $bukti->file_path) {
            $next = \App\Models\PengadaanBuktiTransfer::where('pengadaan_barang_id', $id)->first();
            $newPath = $next ? $next->file_path : null;
            $item->update(['bukti_transfer' => $newPath]);
            if ($item->pengajuan_anggaran_id) {
                $pengajuan = PengajuanAnggaran::find($item->pengajuan_anggaran_id);
                if ($pengajuan) {
                    $pengajuan->update(['bukti_transfer' => $newPath]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:10240',
        ]);

        $item = PengadaanBarang::findOrFail($id);

        if (!$request->hasFile('bukti_transfer')) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 422);
        }

        $subFolder = 'uploads/bukti_transfer';
        $destinationPath = public_path($subFolder);

        // Delete old file if exists
        if ($item->bukti_transfer && file_exists(public_path($item->bukti_transfer))) {
            @unlink(public_path($item->bukti_transfer));
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

        // Simpan ke pengadaan_barangs
        $item->update(['bukti_transfer' => $filePath]);

        // Sync ke pengajuan_anggarans yang terkait
        if ($item->pengajuan_anggaran_id) {
            $pengajuan = PengajuanAnggaran::find($item->pengajuan_anggaran_id);
            if ($pengajuan) {
                // Hapus file lama di pengajuan jika berbeda
                if ($pengajuan->bukti_transfer && $pengajuan->bukti_transfer !== $filePath
                    && file_exists(public_path($pengajuan->bukti_transfer))) {
                    @unlink(public_path($pengajuan->bukti_transfer));
                }
                $pengajuan->update(['bukti_transfer' => $filePath]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil diunggah.',
                'bukti_transfer' => asset($filePath)
            ]);
        }

        return redirect()->back()->with('success', 'Bukti transfer berhasil diunggah.');
    }
}
