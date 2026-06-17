<?php

namespace App\Http\Controllers\Operasional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SalesPlan;
use App\Models\Setting;
use Carbon\Carbon;

class MonitoringChapterController extends Controller
{
    public function index(Request $request)
    {
        $targetEvent = Setting::where('key', 'target_event_peserta')->value('value') ?? 100;
        
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $realisasiEvent = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
            ->where('salesplans.status', 'sudah_transfer')
            ->where('peserta_smis.approval_status', 'Approved')
            ->whereBetween('salesplans.updated_at', [$startOfMonth, $endOfMonth])
            ->whereIn('salesplans.created_by', function ($query) {
                $query->select('id')->from('users')->whereIn('role', ['chapter', 'reseller']);
            })
            ->count();

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
                ->whereBetween('salesplans.updated_at', [$startOfMonth, $endOfMonth])
                ->whereIn('salesplans.created_by', $allTeamIds)
                ->count();

            // Chapter Active Members (Chapter + all Agents under it)
            $chPesertaAktif = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                ->where('salesplans.status', 'sudah_transfer')
                ->where('peserta_smis.approval_status', 'Approved')
                ->whereIn('salesplans.created_by', $allTeamIds)
                ->count();

            // Earnings
            $chEarnings = \App\Services\EarningsService::calculateTotalEarnings($chId);

            // Saldo
            $chSaldo = $ch->wallet->balance ?? 0;

            $agentsData = [];
            foreach ($agents as $ag) {
                $agClosingBulanIni = SalesPlan::join('peserta_smis', 'salesplans.id', '=', 'peserta_smis.sales_plan_id')
                    ->where('salesplans.status', 'sudah_transfer')
                    ->where('peserta_smis.approval_status', 'Approved')
                    ->whereBetween('salesplans.updated_at', [$startOfMonth, $endOfMonth])
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

        return view('operasional.monitoring.chapter', compact(
            'targetEvent', 'realisasiEvent', 'chaptersData'
        ));
    }
}
