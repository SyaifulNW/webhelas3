<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TodoTemplate;
use App\Models\TodoLog;
use App\Models\User;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /** Daftar divisi yang tersedia */
    const DIVISI_LIST = ['Divisi Keuangan', 'Sales & Marketing'];

    /** Periode string untuk query */
    private function getPeriode(string $tipe): string
    {
        $now = Carbon::now();
        return match ($tipe) {
            'harian'   => $now->toDateString(),
            'mingguan' => $now->format('o-W'),
            'bulanan'  => $now->format('Y-m'),
            default    => $now->toDateString(),
        };
    }

    /** Info label & reset untuk setiap tipe */
    public static function getPeriodeInfo(string $tipe): array
    {
        $now = Carbon::now();
        switch ($tipe) {
            case 'harian':
                return [
                    'label'       => $now->translatedFormat('l, j F Y'),
                    'range'       => $now->translatedFormat('j F Y'),
                    'reset_label' => 'besok ' . $now->copy()->addDay()->translatedFormat('j F'),
                    'reset_info'  => 'Reset tiap hari 00.00 — checklist reset otomatis',
                ];
            case 'mingguan':
                $start = $now->copy()->startOfWeek(Carbon::MONDAY);
                $end   = $now->copy()->endOfWeek(Carbon::SUNDAY);
                $nextMonday = $end->copy()->addDay();
                return [
                    'label'       => $start->translatedFormat('j') . ' – ' . $end->translatedFormat('j F'),
                    'range'       => $start->translatedFormat('j') . ' – ' . $end->translatedFormat('j F'),
                    'reset_label' => 'Senin, ' . $nextMonday->translatedFormat('j F'),
                    'reset_info'  => 'Reset tiap Senin — checklist reset otomatis',
                ];
            case 'bulanan':
                $nextMonth = $now->copy()->startOfMonth()->addMonth();
                return [
                    'label'       => $now->translatedFormat('F Y'),
                    'range'       => $now->translatedFormat('F Y'),
                    'reset_label' => '1 ' . $nextMonth->translatedFormat('F'),
                    'reset_info'  => 'Reset tiap tgl 1 — checklist reset otomatis',
                ];
            default:
                return ['label' => '', 'range' => '', 'reset_label' => '', 'reset_info' => ''];
        }
    }

    /** Generate log periode aktif untuk semua template milik user */
    private function generateLogs(int $userId): void
    {
        $templates = TodoTemplate::where('created_by', $userId)
            ->where('is_active', true)->get();
        foreach ($templates as $tpl) {
            TodoLog::firstOrCreate(
                ['template_id' => $tpl->id, 'user_id' => $userId, 'periode' => $this->getPeriode($tpl->tipe)],
                ['is_done' => false]
            );
        }
    }

    /** Attach log + meta ke collection template */
    private function attachLogs($templates, int $userId)
    {
        return $templates->map(function ($tpl) use ($userId) {
            $periode  = $this->getPeriode($tpl->tipe);
            $log      = TodoLog::where('template_id', $tpl->id)
                ->where('user_id', $userId)
                ->where('periode', $periode)
                ->first();
            $tpl->log          = $log;
            $tpl->periode      = $periode;
            $tpl->periode_info = self::getPeriodeInfo($tpl->tipe);
            return $tpl;
        });
    }

    /**
     * Hanya Linda yang boleh akses Agenda.
     * Semua role lain (termasuk administrator) diarahkan ke 403.
     */
    private function authorizeAgenda(): void
    {
        if (Auth::user()->name !== 'Linda') {
            abort(403, 'Fitur Agenda hanya tersedia untuk Linda.');
        }
    }

    public function index()
    {
        $this->authorizeAgenda();

        $user    = Auth::user();
        $isAdmin = strtolower($user->role) === 'administrator';

        if ($isAdmin) {
            $users = User::where('is_active', 1)
                ->where('role', '!=', 'administrator')
                ->where('kategori', 'Pusat')
                ->orderBy('name')->get();

            foreach ($users as $u) $this->generateLogs($u->id);

            $allAgendas = [];
            foreach ($users as $u) {
                $templates = $this->attachLogs(
                    TodoTemplate::where('created_by', $u->id)->where('is_active', true)->get(),
                    $u->id
                );
                if ($templates->isNotEmpty()) {
                    $allAgendas[] = ['user' => $u, 'templates' => $templates];
                }
            }
            return view('agenda.index', compact('isAdmin', 'allAgendas'));
        }

        $this->generateLogs($user->id);

        // Load all templates, grouped by divisi then by tipe
        $allTemplates = $this->attachLogs(
            TodoTemplate::where('created_by', $user->id)
                ->where('is_active', true)
                ->orderBy('divisi')->orderBy('tipe')->orderBy('created_at')->get(),
            $user->id
        );

        $divisiList = self::DIVISI_LIST;

        // Build per-divisi data: stats + grouped templates
        $divisiData = [];
        foreach ($divisiList as $divisi) {
            $templates = $allTemplates->filter(fn($t) => ($t->divisi ?? 'Divisi Keuangan') === $divisi)->values();
            $total   = $templates->count();
            $selesai = $templates->filter(fn($t) => $t->log && $t->log->is_done)->count();
            $tersisa = $total - $selesai;
            $persen  = $total > 0 ? round(($selesai / $total) * 100) : 0;
            $divisiData[$divisi] = [
                'templates'  => $templates,
                'grouped'    => $templates->groupBy('tipe'),
                'total'      => $total,
                'selesai'    => $selesai,
                'tersisa'    => $tersisa,
                'persen'     => $persen,
            ];
        }

        // Periode info per tipe
        $periodeInfo = [
            'harian'   => self::getPeriodeInfo('harian'),
            'mingguan' => self::getPeriodeInfo('mingguan'),
            'bulanan'  => self::getPeriodeInfo('bulanan'),
        ];

        return view('agenda.index', compact('isAdmin', 'divisiList', 'divisiData', 'periodeInfo'));
    }

    public function store(Request $request)
    {
        $this->authorizeAgenda();

        $request->validate([
            'judul'  => 'required|string|max:255',
            'tipe'   => 'required|in:harian,mingguan,bulanan',
            'divisi' => 'required|string|in:Divisi Keuangan,Sales & Marketing',
        ]);

        $user = Auth::user();

        $tpl = TodoTemplate::create([
            'created_by' => $user->id,
            'judul'      => $request->judul,
            'deskripsi'  => $request->deskripsi,
            'tipe'       => $request->tipe,
            'divisi'     => $request->divisi,
            'is_active'  => true,
        ]);

        $periode = $this->getPeriode($tpl->tipe);
        $log = TodoLog::firstOrCreate(
            ['template_id' => $tpl->id, 'user_id' => $user->id, 'periode' => $periode],
            ['is_done' => false]
        );

        return response()->json([
            'success'     => true,
            'template_id' => $tpl->id,
            'log_id'      => $log->id,
            'is_done'     => (bool)$log->is_done,
            'judul'       => $tpl->judul,
            'deskripsi'   => $tpl->deskripsi ?? '',
            'tipe'        => $tpl->tipe,
            'divisi'      => $tpl->divisi,
            'periode_info'=> self::getPeriodeInfo($tpl->tipe),
        ]);
    }

    public function toggleCheck(Request $request, $logId)
    {
        $this->authorizeAgenda();

        $log = TodoLog::where('id', $logId)->where('user_id', Auth::id())->firstOrFail();
        $log->is_done = !$log->is_done;
        $log->done_at = $log->is_done ? now() : null;
        $log->save();
        return response()->json(['success' => true, 'is_done' => (bool)$log->is_done]);
    }

    public function destroy($id)
    {
        $this->authorizeAgenda();

        $tpl = TodoTemplate::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
        $tpl->delete();
        return response()->json(['success' => true]);
    }
}
