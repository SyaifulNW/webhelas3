<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasKecil;
use Illuminate\Support\Facades\Auth;

class KasKecilController extends Controller
{
    public function index(Request $request)
    {
        // Auto-fix: Create kas_kecils table if missing on server
        if (!\Illuminate\Support\Facades\Schema::hasTable('kas_kecils')) {
            try {
                \Illuminate\Support\Facades\Schema::create('kas_kecils', function ($table) {
                    $table->id();
                    $table->date('tanggal');
                    $table->string('keterangan');
                    $table->decimal('masuk', 15, 2)->default(0);
                    $table->decimal('keluar', 15, 2)->default(0);
                    $table->decimal('sisa', 15, 2)->default(0);
                    $table->string('bukti_transfer')->nullable();
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->timestamps();
                });
            } catch (\Exception $e) {
                // Ignore
            }
        }

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $kategori = $request->get('kategori', 'Pusat');

        // Ensure column exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('kas_kecils', 'bukti_transfer')) {
            \Illuminate\Support\Facades\Schema::table('kas_kecils', function ($table) {
                $table->string('bukti_transfer')->nullable();
            });
        }
        
        if (!\Illuminate\Support\Facades\Schema::hasColumn('kas_kecils', 'kategori')) {
            \Illuminate\Support\Facades\Schema::table('kas_kecils', function ($table) {
                $table->string('kategori')->default('Pusat');
            });
        }

        $startDate = "$tahun-$bulan-01";
        
        $saldoAwalPusat = KasKecil::where('kategori', 'Pusat')->where('tanggal', '<', $startDate)->sum('masuk')
            - KasKecil::where('kategori', 'Pusat')->where('tanggal', '<', $startDate)->sum('keluar');
            
        $saldoAwalAesthetic = KasKecil::where('kategori', 'Aesthetic')->where('tanggal', '<', $startDate)->sum('masuk')
            - KasKecil::where('kategori', 'Aesthetic')->where('tanggal', '<', $startDate)->sum('keluar');

        $kasPusat = KasKecil::where('kategori', 'Pusat')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        $kasAesthetic = KasKecil::where('kategori', 'Aesthetic')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.keuangan.kas_kecil', compact('kasPusat', 'kasAesthetic', 'bulan', 'tahun', 'saldoAwalPusat', 'saldoAwalAesthetic', 'kategori'));
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        $kas = KasKecil::findOrFail($id);

        if ($request->hasFile('bukti_transfer')) {
            $subFolder = 'uploads/kas_kecil';
            $destinationPath = public_path($subFolder);

            // Delete old file if exists
            if ($kas->bukti_transfer && file_exists(public_path($kas->bukti_transfer))) {
                @unlink(public_path($kas->bukti_transfer));
            }

            $file = $request->file('bukti_transfer');
            $filename = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);
            $kas->bukti_transfer = $subFolder . '/' . $filename;
            $kas->save();

            return response()->json([
                'success' => true,
                'path' => asset($kas->bukti_transfer)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 400);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'masuk' => 'nullable|numeric',
            'keluar' => 'nullable|numeric',
            'bukti_transfer' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'kategori' => 'required|string',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $subFolder = 'uploads/kas_kecil';
            $destinationPath = public_path($subFolder);

            $file = $request->file('bukti_transfer');
            $filename = time() . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);
            $buktiPath = $subFolder . '/' . $filename;
        }

        KasKecil::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'masuk' => $request->masuk ?? 0,
            'keluar' => $request->keluar ?? 0,
            'bukti_transfer' => $buktiPath,
            'created_by' => Auth::id(),
            'kategori' => $request->kategori,
        ]);

        return response()->json(['success' => true]);
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $kategori = $request->get('kategori', 'Pusat');

        $startDate = "$tahun-$bulan-01";
        $saldoAwal = KasKecil::where('kategori', $kategori)->where('tanggal', '<', $startDate)->sum('masuk')
            - KasKecil::where('kategori', $kategori)->where('tanggal', '<', $startDate)->sum('keluar');

        $kas = KasKecil::where('kategori', $kategori)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.keuangan.kas_kecil_pdf', compact('kas', 'bulan', 'tahun', 'saldoAwal', 'kategori'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Kas_Kecil_{$bulan}_{$tahun}.pdf");
    }

    public function destroy($id)
    {
        KasKecil::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}
