<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleController extends Controller
{
   
    public function dashboard()
    {
        // 1. Data Statistik Dasar
        $data = [
            'jumlahSiswa' => \App\Models\students::count(),
            'jumlahMapel' => \App\Models\subjects::count(),
            'jumlahMentor' => \App\Models\mentors::count(),
            'jumlahPaket'  => \App\Models\bundlings::count(),
        ];

        // 2. Logika Jadwal Hari Ini
        $dayMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        $day = $dayMap[\Carbon\Carbon::now()->format('l')];
        $nowTime = \Carbon\Carbon::now()->format('H:i:s');

        // Query Jadwal yang disederhanakan untuk Admin
        $schedules = \DB::table('schedules as s')
            ->join('subjects as sub', 's.subject_id', '=', 'sub.id')
            ->join('mentors as m', 's.mentor_id', '=', 'm.id')
            ->select(
                's.id', 
                's.jam_mulai', 
                's.jam_selesai', 
                'sub.mapel_name as subject_name', 
                'm.mentor_name',
                \DB::raw('(SELECT COUNT(*) FROM enrollment_schedules WHERE schedule_id = s.id) as student_count')
            )
            ->where('s.hari', $day)
            // Logic: Kelas yang sedang berjalan muncul paling atas
            ->orderByRaw("CASE WHEN s.jam_mulai <= '{$nowTime}' AND s.jam_selesai >= '{$nowTime}' THEN 0 ELSE 1 END")
            ->orderBy('s.jam_mulai')
            ->limit(5) // Batasi 5 jadwal terdekat saja agar dashboard tetap rapi
            ->get();

        $user = Auth::user();

        // Pastikan variabel $schedules dikirim ke view
        return match($user->role) {
            'admin' => view('Admin.dashboard', array_merge(['user' => $user, 'schedules' => $schedules], $data)),
            default => abort(403)
        };
    }
}

