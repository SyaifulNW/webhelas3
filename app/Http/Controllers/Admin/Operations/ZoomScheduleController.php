<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\ZoomSchedule;
use App\Models\Data;
use App\Models\SalesPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ZoomScheduleController extends Controller
{
    /**
     * Store or update a Zoom Schedule.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'scheduled_at' => 'required',
                'status' => 'required|in:scheduled,done,cancelled',
            ]);

            $dataId = $request->input('data_id');
            $salesplanId = $request->input('salesplan_id');

            // Find data_id from SalesPlan if not provided
            if (empty($dataId) && !empty($salesplanId)) {
                $sp = SalesPlan::find($salesplanId);
                if ($sp) {
                    $dataId = $sp->data_id;
                }
            }

            // Clean date format from picker (d/m/Y H:i or Y-m-d H:i)
            $scheduledAtRaw = $request->input('scheduled_at');
            $scheduledAt = null;
            try {
                if (strpos($scheduledAtRaw, '/') !== false) {
                    $scheduledAt = Carbon::createFromFormat('d/m/Y H:i', $scheduledAtRaw);
                } else {
                    $scheduledAt = Carbon::parse($scheduledAtRaw);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Format tanggal jadwal tidak valid (gunakan DD/MM/YYYY HH:MM)']);
            }

            $userId = Auth::id();

            // Find or create schedule for this specific target
            $schedule = null;
            if (!empty($salesplanId)) {
                $schedule = ZoomSchedule::where('salesplan_id', $salesplanId)->first();
            } elseif (!empty($dataId)) {
                $schedule = ZoomSchedule::where('data_id', $dataId)->whereNull('salesplan_id')->first();
            }

            if (!$schedule) {
                $schedule = new ZoomSchedule();
            }

            $schedule->data_id = $dataId;
            $schedule->salesplan_id = $salesplanId ?: null;
            $schedule->cs_id = $userId;
            $schedule->scheduled_at = $scheduledAt;
            $schedule->zoom_link = $request->input('zoom_link');
            $schedule->status = $request->input('status');
            $schedule->notes = $request->input('notes');
            $schedule->save();

            // Auto-sync "ikut_zoom" on the participant record only if not related to a specific salesplan/class
            // (to avoid marking other classes of the same participant as completed)
            $isDone = ($request->input('status') === 'done');
            $zoomVal = $isDone ? 1 : 0;
            if (empty($salesplanId) && !empty($dataId)) {
                $data = Data::find($dataId);
                if ($data) {
                    $data->ikut_zoom = $zoomVal;
                    $data->save();
                }
            }

            // Sync daily activity
            try {
                \App\Models\DailyActiviti::updateAutomated($userId, $scheduledAt->toDateString());
            } catch (\Exception $e) {}

            return response()->json(['success' => true, 'message' => 'Jadwal Zoom berhasil disimpan!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Render the visual calendar dashboard page.
     */
    public function calendar(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->role), ['administrator', 'manager', 'operasional']);

        // Load all CS users for Admin filter
        $csUsers = [];
        if ($isAdmin) {
            $csUsers = User::whereIn('role', ['cs-mbc', 'cs-smi', 'customer_service'])
                ->where('is_active', 1)
                ->where('name', 'not like', '%umum%')
                ->orderBy('name')
                ->get();
        }

        // Target progress calculation for the active CS (target: 4 Zoom per day)
        $todaySchedulesCount = 0;
        $progressPercent = 0;
        if (!$isAdmin) {
            $todaySchedulesCount = ZoomSchedule::where('cs_id', $user->id)
                ->whereDate('scheduled_at', Carbon::today())
                ->count();
            $progressPercent = min(100, round(($todaySchedulesCount / 4) * 100));
        }

        return view('admin.zoom_schedule.calendar', compact('isAdmin', 'csUsers', 'todaySchedulesCount', 'progressPercent'));
    }

    /**
     * Get schedules events JSON for FullCalendar.
     */
    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->role), ['administrator', 'manager', 'operasional']);

        $query = ZoomSchedule::with(['data.kelas', 'salesPlan.kelas', 'cs']);

        // Date range filters from FullCalendar
        if ($request->has('start')) {
            $query->where('scheduled_at', '>=', Carbon::parse($request->input('start')));
        }
        if ($request->has('end')) {
            $query->where('scheduled_at', '<=', Carbon::parse($request->input('end')));
        }

        // Apply CS filters
        if ($isAdmin) {
            if ($request->has('cs_id') && !empty($request->input('cs_id'))) {
                $query->where('cs_id', $request->input('cs_id'));
            } elseif ($request->has('cs_name') && !empty($request->input('cs_name'))) {
                $csName = $request->input('cs_name');
                $query->whereHas('cs', function($q) use ($csName) {
                    $q->where('name', $csName);
                });
            }
        } else {
            $query->where('cs_id', $user->id);
        }

        $schedules = $query->get();

        $events = [];
        foreach ($schedules as $schedule) {
            if (!$schedule->data || empty($schedule->data->nama) || stripos($schedule->data->nama, 'umum') !== false) {
                continue;
            }
            $participantName = $schedule->data->nama;
            $csName = $schedule->cs ? $schedule->cs->name : 'Unknown CS';

            // Determine dynamic coloring based on status
            $color = '#25799E'; // default blue
            if ($schedule->status === 'done') {
                $color = '#3CDE1D'; // green
            } elseif ($schedule->status === 'cancelled') {
                $color = '#E61717'; // red
            }

            $events[] = [
                'id' => $schedule->id,
                'title' => "{$csName} ({$participantName})",
                'start' => $schedule->scheduled_at->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'participant' => $participantName,
                    'cs' => $csName,
                    'zoom_link' => $schedule->zoom_link,
                    'status' => ucfirst($schedule->status),
                    'notes' => $schedule->notes ?: '-',
                    'time' => $schedule->scheduled_at->format('H:i'),
                    'data_id' => $schedule->data_id,
                    'salesplan_id' => $schedule->salesplan_id,
                    'no_wa' => $schedule->data ? $schedule->data->no_wa : '',
                    'kelas_nama' => $schedule->salesPlan && $schedule->salesPlan->kelas 
                        ? $schedule->salesPlan->kelas->nama_kelas 
                        : ($schedule->data && $schedule->data->kelas ? $schedule->data->kelas->nama_kelas : ''),
                    'ikut_zoom' => ($schedule->status === 'done') ? 1 : (($schedule->salesplan_id) ? 0 : ($schedule->data ? $schedule->data->ikut_zoom : 0)),
                    'bant_budget' => $schedule->data ? $schedule->data->bant_budget : 0,
                    'bant_authority' => $schedule->data ? $schedule->data->bant_authority : 0,
                    'bant_time' => $schedule->data ? $schedule->data->bant_time : 0,
                    'scheduled_at' => $schedule->scheduled_at->toIso8601String()
                ]
            ];
        }

        return response()->json($events);
    }
}
