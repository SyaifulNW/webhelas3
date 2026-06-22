<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TodoTemplate;
use App\Models\TodoLog;
use App\Models\User;
use App\Models\DailyTask;
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
        // Allowed for all authenticated users who pass route middleware
    }

    public function index()
    {
        $this->authorizeAgenda();

        $user    = Auth::user();
        $isAdmin = strtolower($user->role) === 'administrator';
        $dailyTasks = collect();

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
            return view('agenda.index', compact('isAdmin', 'allAgendas', 'dailyTasks'));
        }

        $this->generateLogs($user->id);

        $dailyTasks = DailyTask::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->get();

        // Load all templates, grouped by divisi then by tipe
        $allTemplates = $this->attachLogs(
            TodoTemplate::where('created_by', $user->id)
                ->where('is_active', true)
                ->orderBy('divisi')->orderBy('tipe')->orderBy('created_at')->get(),
            $user->id
        );

        // Determine user's division list
        if ($user->hasAnyHakAkses(['spp_admin', 'finance_access'])) {
            $divisiList = self::DIVISI_LIST;
        } else {
            $divisiName = 'Sales & Marketing'; // fallback
            if ($user->divisi) {
                if (stripos($user->divisi, 'Keuangan') !== false) {
                    $divisiName = 'Divisi Keuangan';
                } elseif (stripos($user->divisi, 'Sales') !== false || stripos($user->divisi, 'Marketing') !== false) {
                    $divisiName = 'Sales & Marketing';
                } else {
                    $divisiName = $user->divisi;
                }
            } else {
                $role = strtolower($user->role);
                if ($role === 'marketing') {
                    $divisiName = 'Sales & Marketing';
                } elseif ($role === 'produksi') {
                    $divisiName = 'Produksi';
                } elseif ($role === 'operasional') {
                    $divisiName = 'Operasional';
                } elseif ($role === 'advertising') {
                    $divisiName = 'Advertising';
                } else {
                    $divisiName = ucfirst($user->role);
                }
            }
            $divisiList = [$divisiName];
        }

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

        return view('agenda.index', compact('isAdmin', 'divisiList', 'divisiData', 'periodeInfo', 'dailyTasks'));
    }

    public function store(Request $request)
    {
        $this->authorizeAgenda();

        $request->validate([
            'judul'  => 'required|string|max:255',
            'tipe'   => 'required|in:harian,mingguan,bulanan',
            'divisi' => 'required|string|max:255',
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

    // Fitur Baru: Store Daily Task (AJAX)
    public function storeDailyTask(Request $request)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
        ]);

        $task = DailyTask::create([
            'user_id' => Auth::id(),
            'divisi' => $request->divisi,
            'tanggal' => Carbon::today()->toDateString(),
            'judul' => $request->judul ?? '',
            'deskripsi' => $request->deskripsi ?? '',
            'deadline' => $request->deadline ?? '',
            'target' => $request->target ?? '',
            'realisasi' => $request->realisasi ?? '',
            'is_done' => false,
        ]);

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    // Fitur Baru: Update Daily Task details (inline edit)
    public function updateDailyTask(Request $request, $id)
    {
        $task = DailyTask::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($request->has('judul')) $task->judul = $request->judul ?? '';
        if ($request->has('deskripsi')) $task->deskripsi = $request->deskripsi ?? '';
        if ($request->has('deadline')) $task->deadline = $request->deadline ?? '';
        if ($request->has('target')) $task->target = $request->target ?? '';
        if ($request->has('realisasi')) $task->realisasi = $request->realisasi ?? '';
        
        $task->save();

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    // Fitur Baru: Toggle Done Daily Task
    public function doneDailyTask(Request $request, $id)
    {
        $task = DailyTask::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $task->is_done = !$task->is_done;
        $task->done_at = $task->is_done ? now() : null;
        $task->save();

        return response()->json([
            'success' => true,
            'is_done' => $task->is_done,
            'done_at' => $task->done_at ? $task->done_at->format('H:i') : null
        ]);
    }

    // Fitur Baru: Delete Daily Task
    public function deleteDailyTask($id)
    {
        $task = DailyTask::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $task->delete();

        return response()->json([
            'success' => true
        ]);
    }

    // Fitur Baru: History (Riwayat)
    public function riwayat(Request $request)
    {
        $user = Auth::user();
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');
        $q = $request->input('q');
        $divisi = $request->input('divisi');

        // Recurrent todo logs
        $logsQuery = TodoLog::join('todo_templates', 'todo_logs.template_id', '=', 'todo_templates.id')
            ->select([
                'todo_logs.id as id',
                'todo_templates.judul as judul',
                'todo_templates.deskripsi as deskripsi',
                'todo_templates.divisi as divisi',
                'todo_templates.tipe as tipe',
                'todo_logs.done_at as done_at',
            ])
            ->where('todo_logs.user_id', $user->id)
            ->where('todo_logs.is_done', 1);

        if ($divisi) {
            $logsQuery->where('todo_templates.divisi', $divisi);
        }
        if ($dari) {
            $logsQuery->whereDate('todo_logs.done_at', '>=', $dari);
        }
        if ($sampai) {
            $logsQuery->whereDate('todo_logs.done_at', '<=', $sampai);
        }
        if ($q) {
            $logsQuery->where(function($query) use ($q) {
                $query->where('todo_templates.judul', 'like', "%{$q}%")
                      ->orWhere('todo_templates.deskripsi', 'like', "%{$q}%");
            });
        }

        $logs = $logsQuery->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'source' => 'recurrent',
                'judul' => $item->judul,
                'deskripsi' => $item->deskripsi,
                'divisi' => $item->divisi,
                'tipe' => $item->tipe,
                'done_at' => $item->done_at,
            ];
        });

        // Daily tasks
        $dailyQuery = DailyTask::select([
                'id',
                'judul',
                'deskripsi',
                'deadline',
                'divisi',
                'done_at',
            ])
            ->where('user_id', $user->id)
            ->where('is_done', 1);

        if ($divisi) {
            $dailyQuery->where('divisi', $divisi);
        }
        if ($dari) {
            $dailyQuery->whereDate('done_at', '>=', $dari);
        }
        if ($sampai) {
            $dailyQuery->whereDate('done_at', '<=', $sampai);
        }
        if ($q) {
            $dailyQuery->where(function($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                      ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        $daily = $dailyQuery->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'source' => 'daily',
                'judul' => $item->judul,
                'deskripsi' => $item->deskripsi,
                'deadline' => $item->deadline,
                'divisi' => $item->divisi,
                'tipe' => 'harian_beda',
                'done_at' => $item->done_at,
            ];
        });

        // Combine and sort by done_at DESC
        $combined = $logs->concat($daily)->sortByDesc('done_at')->values();

        // Group by date
        $grouped = $combined->groupBy(function ($item) {
            return $item['done_at'] ? Carbon::parse($item['done_at'])->toDateString() : 'Unknown';
        })->map(function ($items, $date) {
            $dateLabel = $date !== 'Unknown' ? Carbon::parse($date)->translatedFormat('l, j F Y') : 'Unknown';
            return [
                'date' => $date,
                'date_label' => $dateLabel,
                'items' => $items,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    public function updateTarget(Request $request, $id)
    {
        $tpl = TodoTemplate::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
        $tpl->target = $request->target;
        $tpl->save();

        return response()->json([
            'success' => true,
            'target' => $tpl->target
        ]);
    }

    public function updateRealisasi(Request $request, $id)
    {
        $log = TodoLog::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $log->realisasi = $request->realisasi;
        $log->save();

        return response()->json([
            'success' => true,
            'realisasi' => $log->realisasi
        ]);
    }

    /**
     * Rekap: Ringkasan todolist semua divisi untuk tanggal tertentu (max 2 hari lalu).
     * Hanya boleh diakses oleh HRD (atau administrator).
     */
    public function rekap(Request $request)
    {
        $user = Auth::user();

        // Security: only Yasmin or administrator
        if (!$user->hasHakAkses('hrd_settings') && strtolower($user->role) !== 'administrator') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // --- Date resolution: accept ?date=YYYY-MM-DD, clamp to [today-2, today] ---
        $today        = Carbon::today();
        $minAllowed   = $today->copy()->subDays(2);
        $requestedDate = $request->input('date');

        if ($requestedDate) {
            try {
                $targetDate = Carbon::createFromFormat('Y-m-d', $requestedDate)->startOfDay();
            } catch (\Exception $e) {
                $targetDate = $today->copy();
            }
        } else {
            $targetDate = $today->copy();
        }

        // Clamp
        if ($targetDate->lt($minAllowed)) $targetDate = $minAllowed->copy();
        if ($targetDate->gt($today))      $targetDate = $today->copy();

        $dateStr  = $targetDate->toDateString();           // e.g. 2026-06-19
        $weekStr  = $targetDate->format('o-W');            // ISO week e.g. 2026-25
        $monthStr = $targetDate->format('Y-m');            // e.g. 2026-06

        // --- Fetch data ---
        $templates  = TodoTemplate::where('is_active', true)->with('creator')->get();

        $logs = TodoLog::whereIn('periode', [$dateStr, $weekStr, $monthStr])
            ->get()
            ->groupBy('template_id');

        $dailyTasks = \App\Models\DailyTask::whereDate('tanggal', $dateStr)->with('user')->get();

        // Period resolver based on target date
        $getPeriode = fn($tipe) => match ($tipe) {
            'harian'   => $dateStr,
            'mingguan' => $weekStr,
            'bulanan'  => $monthStr,
            default    => $dateStr,
        };

        $recap = [];

        // --- Process recurrent templates ---
        foreach ($templates as $t) {
            $divisi  = $t->divisi ?? 'Lainnya';
            $tLogs   = $logs->get($t->id, collect());
            $periode = $getPeriode($t->tipe);
            $log     = $tLogs->firstWhere('periode', $periode);
            $isDone  = $log ? (bool)$log->is_done : false;

            if (!isset($recap[$divisi])) {
                $recap[$divisi] = ['total' => 0, 'done' => 0, 'tasks' => []];
            }
            $recap[$divisi]['total']++;
            if ($isDone) $recap[$divisi]['done']++;

            $recap[$divisi]['tasks'][] = [
                'source'     => 'recurrent',
                'tipe'       => $t->tipe,
                'tipe_label' => ucfirst($t->tipe),
                'judul'      => $t->judul,
                'deskripsi'  => $t->deskripsi,
                'target'     => $t->target,
                'realisasi'  => $log ? $log->realisasi : '',
                'is_done'    => $isDone,
                'user'       => $t->creator->name ?? 'Unknown',
                'deadline'   => null,
            ];
        }

        // --- Process manual daily tasks ---
        foreach ($dailyTasks as $dt) {
            $divisi = $dt->divisi ?? 'Lainnya';
            $isDone = (bool)$dt->is_done;

            if (!isset($recap[$divisi])) {
                $recap[$divisi] = ['total' => 0, 'done' => 0, 'tasks' => []];
            }
            $recap[$divisi]['total']++;
            if ($isDone) $recap[$divisi]['done']++;

            $recap[$divisi]['tasks'][] = [
                'source'     => 'daily',
                'tipe'       => 'harian_beda',
                'tipe_label' => 'Harian Beda',
                'judul'      => $dt->judul ?: '(Tugas baru)',
                'deskripsi'  => $dt->deskripsi,
                'target'     => $dt->target,
                'realisasi'  => $dt->realisasi,
                'is_done'    => $isDone,
                'user'       => $dt->user->name ?? 'Unknown',
                'deadline'   => $dt->deadline ? Carbon::parse($dt->deadline)->toDateString() : null,
            ];
        }

        // --- Build output ---
        $output = [];
        foreach ($recap as $divisi => $data) {
            $total    = $data['total'];
            $done     = $data['done'];
            $persen   = $total > 0 ? round(($done / $total) * 100) : 0;
            $output[] = [
                'divisi'  => $divisi,
                'total'   => $total,
                'done'    => $done,
                'tersisa' => $total - $done,
                'persen'  => $persen,
                'tasks'   => $data['tasks'],
            ];
        }

        // Sort: Divisi Keuangan → Sales & Marketing → others alphabetically
        usort($output, function ($a, $b) {
            $priority = ['Divisi Keuangan' => 0, 'Sales & Marketing' => 1];
            $pA = $priority[$a['divisi']] ?? 99;
            $pB = $priority[$b['divisi']] ?? 99;
            if ($pA !== $pB) return $pA - $pB;
            return strcmp($a['divisi'], $b['divisi']);
        });

        // Date label for frontend
        $diffDays  = (int) $today->diffInDays($targetDate, false); // 0=today, -1=yesterday, -2=two days ago
        if ($diffDays === 0)       $dateLabel = 'Hari Ini';
        elseif ($diffDays === -1)  $dateLabel = 'Kemarin';
        else                       $dateLabel = '2 Hari Lalu';

        return response()->json([
            'success'    => true,
            'date'       => $targetDate->translatedFormat('l, j F Y'),
            'date_str'   => $dateStr,
            'date_label' => $dateLabel,
            'is_today'   => $targetDate->isToday(),
            'can_prev'   => $targetDate->gt($minAllowed),
            'can_next'   => !$targetDate->isToday(),
            'data'       => $output,
        ]);
    }
}

