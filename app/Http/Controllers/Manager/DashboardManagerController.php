<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Data;
use App\Models\Inisiatif;
use App\Models\Kelas;
use App\Models\SalesPlan;

class DashboardManagerController extends Controller
{
    public function index()
    {
        // 1. Total Leads: kelas Start-Up Muda Indonesia
        $totalLeads = Data::where('kelas_id', '11')->count();

        // 2. Total program aktif
        $programAktif = Inisiatif::where('status', 'Progress')->count();

        // 3. Program selesai
        $programSelesai = Inisiatif::where('status', 'done')->count();

        // 4. Closing bulan ini dari CS Pusat & CS MBC/SMI
        $managerCsNames = \App\Models\User::whereIn('role', ['cs-smi', 'cs-mbc'])->orWhereJsonContains('hak_akses', 'cs_pusat')->pluck('name')->toArray();
        $closingBulanIni = SalesPlan::whereMonth('created_at', now()->month)
            ->whereIn('created_by', $managerCsNames)
            ->count();

        return view('manager.dashboard', compact(
            'totalLeads',
            'programAktif',
            'programSelesai',
            'closingBulanIni'
        ));
    }
}
