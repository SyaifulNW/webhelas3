<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\PesertaSmi;
use Illuminate\Http\Request;

class PembelajaranSiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $peserta = PesertaSmi::orderBy('nama', 'asc')->get();
        return view('admin.Operations.pembelajaran-siswa.index', compact('peserta'));
    }
}
