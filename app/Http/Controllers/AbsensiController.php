<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Show the HR Dashboard with Attendance monitoring and recap.
     */
    public function hr(Request $request)
    {
        $selectedDate  = $request->get('date', Carbon::today()->toDateString());
        $selectedMonth = $request->get('month', Carbon::today()->format('Y-m'));

        // Fetch records for the selected day
        $attendances = Absensi::where('tanggal', $selectedDate)->orderBy('created_at', 'desc')->get();

        // Calculate statistics for the selected day
        $totalAbsen    = Absensi::where('tanggal', $selectedDate)->count();
        $totalHadir    = Absensi::where('tanggal', $selectedDate)->where('status_kehadiran', 'Hadir')->count();
        $totalTerlambat = Absensi::where('tanggal', $selectedDate)->where('is_late', 'Terlambat')->count();
        $totalIzin     = Absensi::where('tanggal', $selectedDate)->whereIn('status_kehadiran', ['Sakit', 'Izin', 'Dinas'])->count();

        $persenHadir    = $totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100) : 0;
        $persenTerlambat = $totalAbsen > 0 ? round(($totalTerlambat / $totalAbsen) * 100) : 0;
        $persenIzin     = $totalAbsen > 0 ? round(($totalIzin / $totalAbsen) * 100) : 0;

        // Fetch monthly recap data
        $monthlyAttendances = Absensi::where('tanggal', 'like', $selectedMonth . '%')
            ->orderBy('tanggal', 'desc')
            ->orderBy('employee_id', 'asc')
            ->get();

        // Fetch active employees from users table (is_active = 1, kategori = Pusat, exclude administrator)
        $employees = User::where('is_active', 1)
            ->where('kategori', 'Pusat')
            ->where('role', '!=', 'administrator')
            ->select('id', 'name', 'divisi', 'tipe_kontrak', 'status_sdm')
            ->orderBy('name')
            ->get();

        return view('hr', compact(
            'attendances',
            'selectedDate',
            'selectedMonth',
            'totalAbsen',
            'totalHadir',
            'totalTerlambat',
            'totalIzin',
            'persenHadir',
            'persenTerlambat',
            'persenIzin',
            'monthlyAttendances',
            'employees'
        ));
    }

    /**
     * Update employee SDM fields via AJAX.
     */
    public function updateEmployee(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:users,id',
            'field' => 'required|in:divisi,tipe_kontrak,status_sdm',
            'value' => 'required|string|max:100',
        ]);

        User::where('id', $request->id)->update([
            $request->field => $request->value,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete employee permanently via AJAX.
     */
    public function destroyEmployee($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Store attendance via AJAX from Android view.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'employee_name' => 'required|string',
            'mode' => 'required|in:masuk,pulang',
            'status_kehadiran' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $user = auth()->user();
        $employeeId = $user->id_no ?: 'HC-' . sprintf('%04d', $user->id);
        $employeeName = $user->name;
        $mode = $request->input('mode');
        $tanggal = $request->input('tanggal');
        $statusKehadiran = $request->input('status_kehadiran');
        $keterangan = $request->input('keterangan');
        $gpsLatitude = $request->input('gps_latitude');
        $gpsLongitude = $request->input('gps_longitude');
        $radiusStatus = $request->input('radius_status');
        $selfie = $request->input('selfie'); // base64 string

        // Find existing record for this employee today
        $attendance = Absensi::where('employee_id', $employeeId)
            ->where('tanggal', $tanggal)
            ->first();

        $now = Carbon::now();
        $timeStr = $now->format('H:i:s');

        // Automatic late validation (after 08:00 AM is late for check-in)
        $isLate = 'Tepat Waktu';
        if ($mode === 'masuk' && $statusKehadiran === 'Hadir') {
            $boundary = Carbon::createFromFormat('H:i:s', '08:00:00');
            if ($now->greaterThan($boundary)) {
                $isLate = 'Terlambat';
            }
        }

        if (!$attendance) {
            // Create new record
            $attendance = new Absensi();
            $attendance->employee_id = $employeeId;
            $attendance->employee_name = $employeeName;
            $attendance->tanggal = $tanggal;
            $attendance->status_kehadiran = $statusKehadiran;
            $attendance->keterangan = $keterangan;
            $attendance->gps_latitude = $gpsLatitude;
            $attendance->gps_longitude = $gpsLongitude;
            $attendance->radius_status = $radiusStatus;

            if ($mode === 'masuk') {
                $attendance->jam_masuk = $timeStr;
                $attendance->selfie_masuk = $selfie;
                $attendance->is_late = $isLate;
            } else {
                $attendance->jam_pulang = $timeStr;
                $attendance->selfie_pulang = $selfie;
                $attendance->is_late = 'Pulang Kerja';
            }
            $attendance->save();
        } else {
            // Update existing record
            if ($mode === 'masuk') {
                if (!$attendance->jam_masuk) {
                    $attendance->jam_masuk = $timeStr;
                    $attendance->selfie_masuk = $selfie;
                    $attendance->is_late = $isLate;
                }
            } else {
                $attendance->jam_pulang = $timeStr;
                $attendance->selfie_pulang = $selfie;
                
                // Calculate total work hours if jam_masuk exists
                if ($attendance->jam_masuk) {
                    $inTime = Carbon::parse($attendance->jam_masuk);
                    $diff = $inTime->diff($now);
                    $attendance->total_jam_kerja = $diff->format('%h Jam %i Menit');
                }
            }
            
            // Overwrite status or notes if updated
            $attendance->status_kehadiran = $statusKehadiran;
            if ($keterangan) {
                $attendance->keterangan = $keterangan;
            }
            $attendance->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disimpan!',
            'data' => $attendance
        ]);
    }

    /**
     * Get attendance history for the frontend app.
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $employeeId = $user->id_no ?: 'HC-' . sprintf('%04d', $user->id);
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        $attendances = Absensi::where('employee_id', $employeeId)
            ->where('tanggal', $tanggal)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }
}
