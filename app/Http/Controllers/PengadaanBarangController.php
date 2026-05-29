<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengadaanBarang;

class PengadaanBarangController extends Controller
{
    public function store(Request $request)
    {
        $item = PengadaanBarang::create($request->all());
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = PengadaanBarang::findOrFail($id);
        $item->update($request->all());
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $item = PengadaanBarang::findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        $item->update(['bukti_transfer' => $subFolder . '/' . $filename]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil diunggah.',
                'bukti_transfer' => asset($subFolder . '/' . $filename)
            ]);
        }

        return redirect()->back()->with('success', 'Bukti transfer berhasil diunggah.');
    }
}
