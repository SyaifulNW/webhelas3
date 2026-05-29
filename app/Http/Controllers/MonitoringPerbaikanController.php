<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPerbaikan;
use Illuminate\Http\Request;

class MonitoringPerbaikanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fasilitas' => 'nullable|string',
            'kerusakan' => 'nullable|string',
            'timeline'  => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'progress'   => 'nullable|string',
            'rencana'   => 'nullable|string',
            'budget'    => 'nullable|numeric',
            'realisasi_dana' => 'nullable|numeric',
        ]);

        $item = MonitoringPerbaikan::create($validated);
        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.', 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fasilitas' => 'nullable|string',
            'kerusakan' => 'nullable|string',
            'timeline'  => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'progress'   => 'nullable|string',
            'rencana'   => 'nullable|string',
            'budget'    => 'nullable|numeric',
            'realisasi_dana' => 'nullable|numeric',
        ]);

        $item = MonitoringPerbaikan::findOrFail($id);
        $item->update($validated);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = MonitoringPerbaikan::findOrFail($id);
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
            mkdir($destinationPath, 0755, true);
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
