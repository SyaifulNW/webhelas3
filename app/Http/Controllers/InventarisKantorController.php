<?php

namespace App\Http\Controllers;

use App\Models\InventarisKantor;
use App\Models\ReportInventaris;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventarisKantorController extends Controller
{
    // ─── Inventaris CRUD ────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $item = InventarisKantor::create([
            'lokasi'         => $request->lokasi ?? '',
            'nama_peralatan' => $request->nama_peralatan ?? '',
            'jumlah'         => $request->jumlah ?? '',
            'status'         => $request->status ?? 'Normal',
        ]);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = InventarisKantor::findOrFail($id);
        $item->update($request->all());
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        InventarisKantor::destroy($id);
        return response()->json(['success' => true]);
    }

    // ─── Checklist PDF ──────────────────────────────────────────────────────────

    public function generateChecklistPdf()
    {
        $now       = Carbon::now();
        $inventaris = InventarisKantor::orderBy('lokasi')->orderBy('nama_peralatan')->get();

        $pdf = Pdf::loadView('operasional.checklist_inventaris_pdf', [
            'inventaris'   => $inventaris,
            'tanggalCetak' => $now->format('d-m-Y'),
            'periode'      => $now->translatedFormat('F Y'),
            'totalUnit'    => $inventaris->sum('jumlah'),
        ])->setPaper('a4', 'portrait');

        $filename = 'checklist-inventaris-' . $now->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    // ─── Report Upload ──────────────────────────────────────────────────────────

    public function uploadReport(Request $request)
    {
        $request->validate([
            'bulan'               => 'required|integer|min:1|max:12',
            'tahun'               => 'required|integer|min:2020|max:2099',
            'tanggal_pemeriksaan' => 'required|date',
            'file_pdf'            => 'required|file|mimes:pdf|max:10240',
            'catatan'             => 'nullable|string|max:1000',
        ], [
            'file_pdf.mimes' => 'File harus berformat PDF.',
            'file_pdf.max'   => 'Ukuran file tidak boleh melebihi 10 MB.',
        ]);

        $file     = $request->file('file_pdf');
        $bulan    = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
        $tahun    = $request->tahun;
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug     = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original);
        $filename = 'report-inventaris-' . $tahun . '-' . $bulan . '-' . $slug . '-' . time() . '.pdf';

        $path = $file->storeAs('report-inventaris', $filename, 'public');

        ReportInventaris::create([
            'bulan'               => (int) $request->bulan,
            'tahun'               => (int) $tahun,
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'nama_file'           => $file->getClientOriginalName(),
            'file_pdf'            => $path,
            'catatan'             => $request->catatan,
            'uploaded_by'         => auth()->id(),
        ]);

        return back()->with('success_report', 'Report inventaris berhasil diunggah.');
    }

    public function destroyReport($id)
    {
        $report = ReportInventaris::findOrFail($id);

        if (Storage::disk('public')->exists($report->file_pdf)) {
            Storage::disk('public')->delete($report->file_pdf);
        }

        $report->delete();

        return back()->with('success_report', 'Report berhasil dihapus.');
    }

    public function downloadReport($id)
    {
        $report = ReportInventaris::findOrFail($id);
        $path   = storage_path('app/public/' . $report->file_pdf);

        abort_unless(file_exists($path), 404, 'File tidak ditemukan.');

        return response()->download($path, $report->nama_file);
    }
}
