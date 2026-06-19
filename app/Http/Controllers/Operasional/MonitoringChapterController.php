<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SalesPlan;
use App\Models\Setting;
use App\Models\ChapterActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class MonitoringChapterController extends Controller
{
    /**
     * Show the monitoring page with chapter, agent, and activity tabs.
     */
    public function index(Request $request)
    {
        // 1. Auto-Fix: Create chapter_activities table dynamically if missing
        if (!Schema::hasTable('chapter_activities')) {
            try {
                Schema::create('chapter_activities', function ($table) {
                    $table->id();
                    $table->string('type'); // 'open_house' or 'autopilot'
                    $table->unsignedBigInteger('chapter_id')->nullable();
                    $table->date('tanggal');
                    $table->integer('target')->default(0);
                    $table->integer('realisasi')->default(0);
                    $table->string('evaluasi')->nullable();
                    $table->string('periode', 7); // 'YYYY-MM'
                    $table->timestamps();
                });
            } catch (\Exception $e) {
            }
        }

        // 2. Determine Selected Month Period
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));
        try {
            $parsedDate = Carbon::createFromFormat('Y-m', $periode);
            $startOfMonth = $parsedDate->copy()->startOfMonth();
            $endOfMonth = $parsedDate->copy()->endOfMonth();
        } catch (\Exception $e) {
            $periode = Carbon::now()->format('Y-m');
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }

        // List of chapters for select options (including chapters and central agents)
        $allChaptersList = User::where(function ($query) {
            $query->where('role', 'chapter')
                ->orWhere(function ($sub) {
                    $sub->where('role', 'agen')
                        ->where('kategori', 'Agen Pusat');
                });
        })->orderBy('name')->get();

        // 3. TAB 1: Data Chapter & Agen
        $targetEvent = Setting::where('key', 'target_event_peserta')->value('value') ?? 100;

        $realisasiEvent = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
            ->where('salesplans.status', 'sudah_transfer')
            ->where('peserta_smis.approval_status', 'Approved')
            ->whereBetween('peserta_smis.tanggal_masuk', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->whereIn('salesplans.created_by', function ($query) {
                $query->select('id')->from('users')->whereIn('role', ['chapter', 'reseller']);
            })
            ->count();

        // Load standard chapters and their agents
        $chapters = User::where('role', 'chapter')
            ->with(['wallet'])
            ->get();

        $chaptersData = [];
        foreach ($chapters as $ch) {
            $agents = User::where('role', 'reseller')
                ->where('created_by', $ch->id)
                ->with(['wallet'])
                ->get();

            $chId = $ch->id;
            $agentIds = $agents->pluck('id');
            $allTeamIds = $agentIds->merge([$chId])->unique();

            // Chapter Monthly Closing (Chapter + all Agents under it)
            $chClosingBulanIni = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                ->where('salesplans.status', 'sudah_transfer')
                ->where('peserta_smis.approval_status', 'Approved')
                ->whereBetween('peserta_smis.tanggal_masuk', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->whereIn('salesplans.created_by', $allTeamIds)
                ->count();

            // Chapter Active Members
            $chPesertaAktif = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                ->where('salesplans.status', 'sudah_transfer')
                ->where('peserta_smis.approval_status', 'Approved')
                ->whereIn('salesplans.created_by', $allTeamIds)
                ->count();

            $chEarnings = \App\Services\EarningsService::calculateTotalEarnings($chId);
            $chSaldo = $ch->wallet->balance ?? 0;

            $agentsData = [];
            foreach ($agents as $ag) {
                $agClosingBulanIni = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                    ->where('salesplans.status', 'sudah_transfer')
                    ->where('peserta_smis.approval_status', 'Approved')
                    ->whereBetween('peserta_smis.tanggal_masuk', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->where('salesplans.created_by', $ag->id)
                    ->count();

                $agPesertaAktif = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                    ->where('salesplans.status', 'sudah_transfer')
                    ->where('peserta_smis.approval_status', 'Approved')
                    ->where('salesplans.created_by', $ag->id)
                    ->count();

                $agEarnings = \App\Services\EarningsService::calculateTotalEarnings($ag->id);
                $agSaldo = $ag->wallet->balance ?? 0;

                $agentsData[] = [
                    'id' => $ag->id,
                    'name' => $ag->name,
                    'location' => $ag->chapter ?? $ch->chapter ?? '-',
                    'role' => $ag->role,
                    'closing_bulan_ini' => $agClosingBulanIni,
                    'peserta_aktif' => $agPesertaAktif,
                    'earnings' => $agEarnings,
                    'saldo' => $agSaldo,
                ];
            }

            $chaptersData[] = [
                'id' => $ch->id,
                'name' => $ch->name,
                'location' => $ch->chapter ?? '-',
                'role' => $ch->role,
                'closing_bulan_ini' => $chClosingBulanIni,
                'peserta_aktif' => $chPesertaAktif,
                'earnings' => $chEarnings,
                'saldo' => $chSaldo,
                'agents' => $agentsData,
            ];
        }

        // Fetch central agents (role = agen, kategori = Agen Pusat)
        $usersAgenPusatRaw = User::where('role', 'agen')
            ->where('kategori', 'Agen Pusat')
            ->with(['wallet'])
            ->get();

        $agenPusatData = [];
        foreach ($usersAgenPusatRaw as $ap) {
            $apClosingBulanIni = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                ->where('salesplans.status', 'sudah_transfer')
                ->where('peserta_smis.approval_status', 'Approved')
                ->whereBetween('peserta_smis.tanggal_masuk', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->where('salesplans.created_by', $ap->id)
                ->count();

            $apPesertaAktif = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                ->where('salesplans.status', 'sudah_transfer')
                ->where('peserta_smis.approval_status', 'Approved')
                ->where('salesplans.created_by', $ap->id)
                ->count();

            $apEarnings = \App\Services\EarningsService::calculateTotalEarnings($ap->id);
            $apSaldo = $ap->wallet->balance ?? 0;

            $agenPusatData[] = [
                'id' => $ap->id,
                'name' => $ap->name,
                'location' => $ap->chapter ?? '-',
                'role' => $ap->role,
                'kategori' => $ap->kategori,
                'closing_bulan_ini' => $apClosingBulanIni,
                'peserta_aktif' => $apPesertaAktif,
                'earnings' => $apEarnings,
                'saldo' => $apSaldo,
            ];
        }

        // 4. TAB 2 & 3: Open House & AutoPilot Activities
        $targetOpenHouse = Setting::where('key', 'target_open_house_' . $periode)->value('value') ?? 0;
        $targetAutoPilot = Setting::where('key', 'target_autopilot_' . $periode)->value('value') ?? 0;

        $openHouseActivities = ChapterActivity::where('type', 'open_house')
            ->where('periode', $periode)
            ->with('chapter')
            ->orderBy('tanggal', 'asc')
            ->get();

        $autoPilotActivities = ChapterActivity::where('type', 'autopilot')
            ->where('periode', $periode)
            ->with('chapter')
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalTargetOpenHouse = $targetOpenHouse * $openHouseActivities->count();
        $realisasiOpenHouse = $openHouseActivities->sum('realisasi');
        $realisasiAutoPilot = $autoPilotActivities->sum('realisasi');

        $kurangOpenHouse = max(0, $totalTargetOpenHouse - $realisasiOpenHouse);
        $kurangOpenHousePersen = $totalTargetOpenHouse > 0 ? round(($kurangOpenHouse / $totalTargetOpenHouse) * 100) : 0;

        $kurangAutoPilot = max(0, $targetAutoPilot - $realisasiAutoPilot);
        $kurangAutoPilotPersen = $targetAutoPilot > 0 ? round(($kurangAutoPilot / $targetAutoPilot) * 100) : 0;

        // Generate periods: last 6 months to next 6 months
        $periodsList = [];
        for ($i = -6; $i <= 6; $i++) {
            $p = Carbon::now()->addMonths($i);
            $periodsList[$p->format('Y-m')] = $p->translatedFormat('F Y');
        }
        if (!isset($periodsList[$periode])) {
            try {
                $pParsed = Carbon::createFromFormat('Y-m', $periode);
                $periodsList[$periode] = $pParsed->translatedFormat('F Y');
            } catch (\Exception $e) {
            }
        }
        ksort($periodsList);

        return view('operasional.monitoring.chapter', compact(
            'targetEvent', 'realisasiEvent', 'chaptersData', 'agenPusatData',
            'periode', 'allChaptersList', 'periodsList',
            'targetOpenHouse', 'totalTargetOpenHouse', 'realisasiOpenHouse', 'kurangOpenHouse', 'kurangOpenHousePersen', 'openHouseActivities',
            'targetAutoPilot', 'realisasiAutoPilot', 'kurangAutoPilot', 'kurangAutoPilotPersen', 'autoPilotActivities'
        ));
    }

    /**
     * Update dynamic target (e.g. target_open_house_2026-06).
     */
    public function updateTarget(Request $request)
    {
        $request->validate([
            'periode' => 'required|string|max:7',
            'type' => 'required|in:open_house,autopilot',
            'target' => 'required|integer|min:0',
        ]);

        Setting::updateOrCreate(
            ['key' => 'target_' . $request->type . '_' . $request->periode],
            ['value' => $request->target]
        );

        if ($request->type === 'open_house') {
            ChapterActivity::where('type', 'open_house')
                ->where('periode', $request->periode)
                ->update(['target' => $request->target]);
        }

        return redirect()->back()->with('success', 'Target berhasil diperbarui.');
    }

    /**
     * Store new activity row.
     */
    public function storeActivity(Request $request)
    {
        $request->validate([
            'type' => 'required|in:open_house,autopilot',
            'chapter_id' => 'nullable|exists:users,id',
            'tanggal' => 'required|date',
            'target' => 'required|integer|min:0',
            'realisasi' => 'required|integer|min:0',
            'evaluasi' => 'nullable|string|max:255',
            'periode' => 'required|string|max:7',
        ]);

        $data = $request->only([
            'type', 'chapter_id', 'tanggal', 'target', 'realisasi', 'evaluasi', 'periode'
        ]);

        if ($request->type === 'open_house') {
            $targetOpenHouse = Setting::where('key', 'target_open_house_' . $request->periode)->value('value') ?? 0;
            $data['target'] = $targetOpenHouse;
        }

        $activity = ChapterActivity::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'activity' => $activity
            ]);
        }

        return redirect()->back()->with('success', 'Data aktivitas berhasil ditambahkan.');
    }

    /**
     * Update an activity row inline via AJAX.
     */
    public function updateActivity(Request $request, $id)
    {
        $activity = ChapterActivity::findOrFail($id);

        $request->validate([
            'chapter_id' => 'nullable|exists:users,id',
            'tanggal' => 'required|date',
            'target' => 'required|integer|min:0',
            'realisasi' => 'required|integer|min:0',
            'evaluasi' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['chapter_id', 'tanggal', 'target', 'realisasi', 'evaluasi']);

        if ($activity->type === 'open_house') {
            $targetOpenHouse = Setting::where('key', 'target_open_house_' . $activity->periode)->value('value') ?? 0;
            $data['target'] = $targetOpenHouse;
        }

        $activity->update($data);

        return response()->json(['success' => true]);
    }

    /**
     * Delete an activity row via AJAX.
     */
    public function destroyActivity($id)
    {
        $activity = ChapterActivity::findOrFail($id);
        $activity->delete();

        return response()->json(['success' => true]);
    }
}
