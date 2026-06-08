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

        $settings = [
            'absensi_latitude' => \App\Models\Setting::where('key', 'absensi_latitude')->value('value') ?? '-6.201200',
            'absensi_longitude' => \App\Models\Setting::where('key', 'absensi_longitude')->value('value') ?? '106.816000',
            'absensi_radius' => \App\Models\Setting::where('key', 'absensi_radius')->value('value') ?? '50',
            'absensi_jam_masuk_weekday' => \App\Models\Setting::where('key', 'absensi_jam_masuk_weekday')->value('value') ?? '08:00',
            'absensi_jam_pulang_weekday' => \App\Models\Setting::where('key', 'absensi_jam_pulang_weekday')->value('value') ?? '16:00',
            'absensi_jam_masuk_sabtu' => \App\Models\Setting::where('key', 'absensi_jam_masuk_sabtu')->value('value') ?? '08:00',
            'absensi_jam_pulang_sabtu' => \App\Models\Setting::where('key', 'absensi_jam_pulang_sabtu')->value('value') ?? '14:00',
        ];

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
            'employees',
            'settings'
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
            $dayOfWeek = $now->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
            
            if ($dayOfWeek == 0) {
                // Sunday
                $jamMasukSetting = '08:00'; // Or just leave as is, usually sunday is off
            } elseif ($dayOfWeek == 6) {
                // Saturday
                $jamMasukSetting = \App\Models\Setting::where('key', 'absensi_jam_masuk_sabtu')->value('value') ?? '08:00';
            } else {
                // Monday to Friday
                $jamMasukSetting = \App\Models\Setting::where('key', 'absensi_jam_masuk_weekday')->value('value') ?? '08:00';
            }

            if (strlen($jamMasukSetting) == 5) {
                $jamMasukSetting .= ':00';
            }
            $boundary = Carbon::createFromFormat('H:i:s', $jamMasukSetting);
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

    /**
     * Update Attendance Settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'absensi_latitude' => 'required|numeric',
            'absensi_longitude' => 'required|numeric',
            'absensi_radius' => 'required|numeric',
            'absensi_jam_masuk_weekday' => 'required|string',
            'absensi_jam_pulang_weekday' => 'required|string',
            'absensi_jam_masuk_sabtu' => 'required|string',
            'absensi_jam_pulang_sabtu' => 'required|string',
        ]);

        $keys = [
            'absensi_latitude', 'absensi_longitude', 'absensi_radius', 
            'absensi_jam_masuk_weekday', 'absensi_jam_pulang_weekday', 
            'absensi_jam_masuk_sabtu', 'absensi_jam_pulang_sabtu'
        ];

        foreach ($keys as $key) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan Absensi berhasil diperbarui!');
    }
}
