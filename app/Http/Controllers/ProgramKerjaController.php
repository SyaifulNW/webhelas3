<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\Inisiatif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgramKerjaController extends Controller
{
    /**
     * Tampilkan daftar program kerja beserta inisiatifnya
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $viewRole = $request->query('view_role');
        $isRafiRofi = preg_match('/Rafi|Rofi/i', $user->name);

        $query = ProgramKerja::with('inisiatifs')
            ->orderBy('created_at', 'desc');

        $userRole = strtolower($user->role);
        $userName = $user->name;
        $isYasminLinda = stripos($userName, 'Yasmin') !== false || stripos($userName, 'Linda') !== false;

        // Jika administrator ingin melihat monitoring role tertentu
        if ($userRole === 'administrator') {
            if ($viewRole) {
                // Monitor role tertentu jika ada parameter view_role
                $query->where('created_by_role', $viewRole);
            } else {
                // DEFAULT: Tampilkan role produksi untuk Admin
                $query->where('created_by_role', 'produksi');
            }
        }
        // Untuk semua role/pengguna lainnya (termasuk Operasional, Yasmin, Linda, Felmi, Chapter, dsb)
        else {
            // Pre-fetch ID program yang dibuat oleh user ini (sama seperti analogi Gantt)
            $ownProgramIds = ProgramKerja::where('created_by', $user->id)->pluck('id');

            // Tampilkan program yang:
            // 1. Dibuat oleh user yang sedang login (created_by), ATAU
            // 2. User tersebut menjadi PIC di salah satu inisiatif di dalamnya, ATAU
            // 3. User tersebut yang membuat inisiatif di dalamnya (created_by di inisiatifs)
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('inisiatifs', function ($sub) use ($user) {
                      $sub->where('pic', 'like', '%' . trim($user->name) . '%')
                          ->orWhere('created_by', $user->id);
                  });
            });

            // Saring inisiatif di dalamnya:
            // - Untuk program milik sendiri: tampilkan semua inisiatif
            // - Untuk program orang lain: hanya tampilkan inisiatif yang dia jadi PIC ATAU yang dia buat
            $query->with([
                'inisiatifs' => function ($sub) use ($user, $ownProgramIds) {
                    $sub->where(function ($q) use ($user, $ownProgramIds) {
                        $q->whereIn('program_kerja_id', $ownProgramIds)
                          ->orWhere('pic', 'like', '%' . trim($user->name) . '%')
                          ->orWhere('created_by', $user->id);
                    });
                }
            ]);
        }

        $programs = $query->get();
        $allUsers = \App\Models\User::orderBy('name', 'asc')->get();
        $ganttData = [];

        return view('marketing.programkerja.index', compact('programs', 'viewRole', 'allUsers', 'ganttData'));
    }

    /**
     * Update kolom secara inline di tabel
     */
    public function updateInline(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'field' => 'required|string',
            'value' => 'nullable|string',
            'type' => 'nullable|string'
        ]);

        if ($request->type === 'program') {
            $item = ProgramKerja::findOrFail($request->id);
        } else {
            $item = Inisiatif::findOrFail($request->id);
        }

        // Jika field numeric
        if (in_array($request->field, ['target', 'realisasi'])) {
            $item->{$request->field} = (int) $request->value;
        } else {
            $item->{$request->field} = $request->value;
        }

        $item->save();

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $user = Auth::user();

        $program = ProgramKerja::create([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'created_by' => $user->id,
            'created_by_role' => $user->role,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'program' => $program]);
        }

        return redirect()
            ->route('programkerja.index')
            ->with('success', 'Program Kerja berhasil ditambahkan!');
    }

    public function storeInisiatif(Request $request)
    {
        try {
            $request->merge(json_decode($request->getContent(), true) ?? []);

            $validated = $request->validate([
                'program_kerja_id' => 'required|integer|exists:program_kerjas,id',
                'judul' => 'required|string|max:255',
                'pic' => 'nullable|string|max:100',
                'requester' => 'nullable|string|max:100',
                'target' => 'required|integer|min:0',
                'realisasi' => 'required|integer|min:0',
                'tanggal_mulai' => 'nullable|date',
                'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
                'status' => 'required|in:progress,done,overdue',
                'deskripsi' => 'nullable|string',
            ]);

            // -----------------------------------------
            // HITUNG NILAI QTY
            // -----------------------------------------
            $nilai_qty = $validated['target'] > 0
                ? ($validated['realisasi'] / $validated['target']) * 100
                : 0;

            if ($nilai_qty > 100)
                $nilai_qty = 100;

            // -----------------------------------------
            // HITUNG NILAI WAKTU
            // -----------------------------------------
            $nilai_waktu = 0;

            if ($validated['status'] === 'done') {
                if (!empty($validated['tanggal_selesai']) && now()->lte($validated['tanggal_selesai'])) {
                    $nilai_waktu = 100;
                } else {
                    $nilai_waktu = 50;
                }
            } elseif ($validated['status'] === 'progress') {
                if (!empty($validated['tanggal_selesai']) && now()->lte($validated['tanggal_selesai'])) {
                    $nilai_waktu = 60;
                } else {
                    $nilai_waktu = 30;
                }
            } elseif ($validated['status'] === 'overdue') {
                $nilai_waktu = 0;
            }

            // -----------------------------------------
            // NILAI FINAL
            // -----------------------------------------
            $nilai_final = ($nilai_qty + $nilai_waktu) / 2;

            // Tambahkan nilai dan created_by ke array validated
            $validated['nilai'] = $nilai_final;
            $validated['created_by'] = Auth::id();

            $inisiatif = Inisiatif::create($validated);

            return response()->json([
                'success' => true,
                'id' => $inisiatif->id,
                'nilai' => $nilai_final
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateInisiatif(Request $request, $id)
    {
        try {
            $request->merge(json_decode($request->getContent(), true) ?? []);

            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'pic' => 'nullable|string|max:100',
                'tanggal_mulai' => 'nullable|date',
                'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
                'status' => 'required|in:progress,done,overdue',
                'target' => 'nullable|integer|min:0',
                'realisasi' => 'nullable|integer|min:0',
            ]);

            $inisiatif = Inisiatif::findOrFail($id);

            // Ambil data lama jika tidak dikirim
            $target = $validated['target'] ?? $inisiatif->target;
            $realisasi = $validated['realisasi'] ?? $inisiatif->realisasi;

            // Hitung nilai qty
            $nilai_qty = $target > 0 ? ($realisasi / $target) * 100 : 0;
            if ($nilai_qty > 100)
                $nilai_qty = 100;

            // Hitung nilai waktu
            $nilai_waktu = 0;
            $status = $validated['status'];

            if ($status === 'done') {
                if (!empty($validated['tanggal_selesai']) && now()->lte($validated['tanggal_selesai'])) {
                    $nilai_waktu = 100;
                } else {
                    $nilai_waktu = 50;
                }
            } elseif ($status === 'progress') {
                if (!empty($validated['tanggal_selesai']) && now()->lte($validated['tanggal_selesai'])) {
                    $nilai_waktu = 60;
                } else {
                    $nilai_waktu = 30;
                }
            } elseif ($status === 'overdue') {
                $nilai_waktu = 0;
            }

            // Nilai final
            $validated['nilai'] = ($nilai_qty + $nilai_waktu) / 2;

            $inisiatif->update($validated);

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus program kerja & inisiatifnya
     */
    public function destroyInisiatif(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:inisiatifs,id',
        ]);

        $inisiatif = Inisiatif::find($request->id);

        if (!$inisiatif) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $inisiatif->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Hapus program kerja
     */
    public function destroy($id)
    {
        try {
            $programKerja = ProgramKerja::findOrFail($id);

            // Hapus inisiatif terkait
            $programKerja->inisiatifs()->delete();
            $programKerja->delete();

            return redirect()->route('programkerja.index')->with('success', 'Program Kerja berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Program Kerja: ' . $e->getMessage());
        }
    }

    public function performance()
    {
        $user = Auth::user();
        $userName = $user->name;
        $role = $user->role;

        // Logic visibility:
        // Admin: semua task
        // Non-Admin: task yang dibuatnya (program_kerja.created_by) ATAU task yang PIC-nya adalah dia

        if ($role === 'admin' || $role === 'administrator') {
            $allInisiatifs = Inisiatif::all();
        } else {
            $myProgramIds = ProgramKerja::where('created_by', $user->id)->pluck('id');
            $allInisiatifs = Inisiatif::whereIn('program_kerja_id', $myProgramIds)
                ->orWhere('pic', $userName)
                ->get();
        }

        $done = 0;
        $progress = 0;
        $overdue = 0;

        foreach ($allInisiatifs as $item) {
            $status = $item->status ?? 'progress';
            $end = $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai) : null;

            if ($status !== 'done' && $end && $end->isPast()) {
                $overdue++;
            } elseif ($status === 'done') {
                $done++;
            } else {
                $progress++;
            }
        }

        $stats = [
            'done' => $done,
            'progress' => $progress,
            'overdue' => $overdue,
            'total' => $allInisiatifs->count(),
        ];

        return view('marketing.produksi.performance', compact('stats'));
    }
}
