<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;

use App\Models\MonitoringPerbaikan;
use Illuminate\Http\Request;

class MonitoringPerbaikanController extends Controller
{
    public function store(Request $request)
    {
        $input = $request->all();
        if (isset($input['inventaris_id'])) {
            if ($input['inventaris_id'] === 'other') {
                $input['inventaris_id'] = null;
            } elseif (empty($input['inventaris_id'])) {
                $input['inventaris_id'] = null;
                $input['fasilitas_manual'] = null;
            } else {
                $input['fasilitas_manual'] = null;
            }
        }
        $request->replace($input);

        $validated = $request->validate([
            'inventaris_id' => 'nullable|exists:inventaris_kantors,id',
            'fasilitas_manual' => 'nullable|string',
            'kerusakan' => 'nullable|string',
            'timeline'  => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'progress'   => 'nullable|string',
            'budget'    => 'nullable|numeric',
            'realisasi_dana' => 'nullable|numeric',
        ]);

        $item = MonitoringPerbaikan::create($validated);
        $item->load('inventaris');

        // Otomatis buat PengajuanAnggaran
        $user = auth()->user();
        $facilityName = $item->fasilitas_manual ?: ($item->inventaris ? $item->inventaris->nama_peralatan : 'Perbaikan');

        $pengajuan = \App\Models\PengajuanAnggaran::create([
            'tanggal_pengajuan' => \Carbon\Carbon::now(),
            'nama_pengajuan'    => $facilityName . ' (Perbaikan)',
            'jumlah_biaya'      => is_numeric($item->budget) ? $item->budget : 0,
            'user_id'           => $user ? $user->id : null,
            'diajukan_oleh'     => $user ? $user->name : 'Operasional',
            'status'            => 'pending',
            'keterangan'        => 'Otomatis dikirim dari monitoring perbaikan operasional. Kerusakan: ' . ($item->kerusakan ?? '-'),
            'is_recurring'      => false,
        ]);

        $item->update(['pengajuan_anggaran_id' => $pengajuan->id]);

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.', 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        if (isset($input['inventaris_id'])) {
            if ($input['inventaris_id'] === 'other') {
                $input['inventaris_id'] = null;
            } elseif (empty($input['inventaris_id'])) {
                $input['inventaris_id'] = null;
                $input['fasilitas_manual'] = null;
            } else {
                $input['fasilitas_manual'] = null;
            }
        }
        $request->replace($input);

        $validated = $request->validate([
            'inventaris_id' => 'nullable|exists:inventaris_kantors,id',
            'fasilitas_manual' => 'nullable|string',
            'kerusakan' => 'nullable|string',
            'timeline'  => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'progress'   => 'nullable|string',
            'budget'    => 'nullable|numeric',
            'realisasi_dana' => 'nullable|numeric',
        ]);

        $item = MonitoringPerbaikan::findOrFail($id);
        $item->update($validated);

        // Jika nama_barang atau budget berubah, sync ke pengajuan anggaran terkait
        if ($item->pengajuan_anggaran_id) {
            $pengajuan = \App\Models\PengajuanAnggaran::find($item->pengajuan_anggaran_id);
            if ($pengajuan && $pengajuan->status === 'pending') {
                $syncData = [];
                if (isset($validated['budget'])) {
                    $syncData['jumlah_biaya'] = $validated['budget'];
                }
                if (isset($validated['fasilitas_manual']) || isset($validated['inventaris_id'])) {
                    $item->load('inventaris');
                    $facilityName = $item->fasilitas_manual ?: ($item->inventaris ? $item->inventaris->nama_peralatan : 'Perbaikan');
                    $syncData['nama_pengajuan'] = $facilityName . ' (Perbaikan)';
                }
                if (isset($validated['kerusakan'])) {
                    $syncData['keterangan'] = 'Otomatis dikirim dari monitoring perbaikan operasional. Kerusakan: ' . ($validated['kerusakan'] ?? '-');
                }
                if (!empty($syncData)) {
                    $pengajuan->update($syncData);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = MonitoringPerbaikan::findOrFail($id);

        // Hapus juga pengajuan anggaran terkait jika masih pending
        if ($item->pengajuan_anggaran_id) {
            $pengajuan = \App\Models\PengajuanAnggaran::find($item->pengajuan_anggaran_id);
            if ($pengajuan && $pengajuan->status === 'pending') {
                $pengajuan->delete();
            }
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:pdf|max:10240', // Validate PDF, max 10MB
        ]);

        $item = MonitoringPerbaikan::findOrFail($id);

        if (!$request->hasFile('bukti_transfer')) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 422);
        }

        $subFolder = 'uploads/lpj';
        $destinationPath = public_path($subFolder);

        // Delete old file if exists
        if ($item->bukti_transfer && file_exists(public_path($item->bukti_transfer))) {
            @unlink(public_path($item->bukti_transfer));
        }

        $file = $request->file('bukti_transfer');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $safeName = \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
        $filename = 'lpj_mon_' . time() . '_' . $safeName;

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);

        $item->update(['bukti_transfer' => $subFolder . '/' . $filename]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'File LPJ berhasil diunggah.',
                'bukti_transfer' => asset($subFolder . '/' . $filename)
            ]);
        }

        return redirect()->back()->with('success', 'File LPJ berhasil diunggah.');
    }
}
