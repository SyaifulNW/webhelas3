<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Data;

class FormController extends Controller
{
    public function store(Request $request)
    {
        try {

            // 🔥 mapping chapter → user & wilayah
            $chapters = [
                'kaltim' => [
                    'user_id' => 31,
                    'nama' => 'Kalimantan Timur'
                ],
                'tangerang' => [
                    'user_id' => 47,
                    'nama' => 'Tangerang'
                ],
                'jakarta' => [
                    'user_id' => 45,
                    'nama' => 'Jakarta'
                ],
                'makassar' => [
                    'user_id' => 46,
                    'nama' => 'Makassar'
                ],
                'depok' => [
                    'user_id' => 44,
                    'nama' => 'Depok'
                ],
                'cirebon' => [
                    'user_id' => 43,
                    'nama' => 'Cirebon'
                ],
                'lampung' => [
                    'user_id' => 48,
                    'nama' => 'Lampung'
                ],
                'kediri' => [
                    'user_id' => 52,
                    'nama' => 'Karesidenan Kediri'
                ],
            ];

            // 🔥 ambil chapter dari Apps Script
            $key = strtolower($request->chapter);
            $chapterInfo = $chapters[$key] ?? null;

            if (!$chapterInfo) {
                return response()->json([
                    'error' => 'Chapter tidak ditemukan'
                ], 400);
            }

            // Get User Name for accountability
            $user = \App\Models\User::find($chapterInfo['user_id']);
            $createdBy = $user ? $user->name : $chapterInfo['user_id'];

            // Overrides for specific IDs to ensure correct name display as requested
            $overrides = [
                31 => 'FARID MANAF',
                43 => 'ALIF RINGGA PERSADA',
                44 => 'AGUNG H. WIBOWO',
                45 => 'Phingki Surya',
                46 => 'SARWANDI EKA SARBINI',
                47 => 'ASEP MAULIDIANSYAH',
                48 => 'JIHAN',
                52 => 'Yulia'
            ];

            if (isset($overrides[$chapterInfo['user_id']])) {
                $createdBy = $overrides[$chapterInfo['user_id']];
            }

            // 🔥 simpan data
            $data = Data::create([
                'nama' => $request->nama ?? '-',
                'no_wa' => $request->no_wa ?? '-',
                'nama_bisnis' => $request->nama_bisnis ?? '-',

                'leads' => 'open_house',
                'kendala' => $request->harapan ?? '-',

                'created_by' => $createdBy, // ✅ Nama user (for display consistency)
                'created_by_role' => 'chapter',

                'chapter' => $chapterInfo['nama'], // ✅ wilayah

                'potensi' => 'ALL',
            ]);

            // 🔥 otomatis simpan jadwal zoom jika dikirim dari Google Form
            $dateZoom = $request->pilih_tanggal_sesi_zoom ?? $request->tanggal_zoom ?? $request->tanggal_sesi_zoom;
            $timeZoom = $request->pilih_jam_sesi_zoom ?? $request->jam_zoom ?? $request->jam_sesi_zoom;

            if (!empty($dateZoom) && !empty($timeZoom)) {
                $startTime = '09:00:00';
                if (stripos($timeZoom, '9.00') !== false || stripos($timeZoom, '09.00') !== false || stripos($timeZoom, '09:00') !== false) {
                    $startTime = '09:00:00';
                } elseif (stripos($timeZoom, '11.00') !== false || stripos($timeZoom, '11:00') !== false) {
                    $startTime = '11:00:00';
                } elseif (stripos($timeZoom, '13.00') !== false || stripos($timeZoom, '13:00') !== false) {
                    $startTime = '13:00:00';
                } elseif (stripos($timeZoom, '15.00') !== false || stripos($timeZoom, '15:00') !== false) {
                    $startTime = '15:00:00';
                }
                
                try {
                    $scheduledAt = \Carbon\Carbon::parse($dateZoom . ' ' . $startTime);
                    
                    $schedule = new \App\Models\ZoomSchedule();
                    $schedule->data_id = $data->id;
                    $schedule->scheduled_at = $scheduledAt;
                    $schedule->status = 'scheduled';
                    $schedule->notes = 'Jadwal otomatis dari Google Form';
                    $schedule->save();
                } catch (\Exception $ex) {
                    \Log::error('ZOOM AUTOSYNC ERROR: ' . $ex->getMessage());
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            \Log::error('FORM ERROR: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}