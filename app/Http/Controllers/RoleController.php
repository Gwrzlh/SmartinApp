<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\enrollments;
use Carbon\Carbon;

class RoleController extends Controller
{
   
    public function dashboard()
    {
  
        $data = [
            'jumlahSiswa'  => \App\Models\students::count(),
            'jumlahMapel'  => \App\Models\subjects::count(),
            'jumlahMentor' => \App\Models\mentors::count(),
            'jumlahPaket'  => \App\Models\bundlings::count(),
        ];

        // Jadwal Hari Ini
        $dayMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        $day     = $dayMap[Carbon::now()->format('l')];
        $nowTime = Carbon::now()->format('H:i:s');

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
            ->orderByRaw("CASE WHEN s.jam_mulai <= '{$nowTime}' AND s.jam_selesai >= '{$nowTime}' THEN 0 ELSE 1 END")
            ->orderBy('s.jam_mulai')
            ->limit(5)
            ->get();

        $debtEnrollments = enrollments::with(['bundling', 'student'])
            ->where('status_pembelajaran', 'graduated_debt')
            ->whereNotNull('expired_at')
            ->get();

        $totalPiutangLulusan = 0;
        $debtByStudent       = [];

        foreach ($debtEnrollments as $enrollment) {
            if (!$enrollment->bundling || !$enrollment->student) continue;

            $expiredDate = Carbon::parse($enrollment->expired_at);
            $monthsLate  = max(1, (int) $expiredDate->diffInMonths(now()) + 1);
            $debtAmount  = $enrollment->bundling->bundling_price * $monthsLate;

            $totalPiutangLulusan += $debtAmount;

            $studentId = $enrollment->student_id;
            if (!isset($debtByStudent[$studentId])) {
                $debtByStudent[$studentId] = [
                    'student' => $enrollment->student,
                    'total'   => 0,
                ];
            }
            $debtByStudent[$studentId]['total'] += $debtAmount;
        }

        usort($debtByStudent, fn($a, $b) => $b['total'] <=> $a['total']);
        $topDebtors = array_slice($debtByStudent, 0, 5);

        $user = Auth::user();

        return match($user->role) {
            'admin' => view('Admin.dashboard', array_merge([
                'user'                => $user,
                'schedules'           => $schedules,
                'totalPiutangLulusan' => $totalPiutangLulusan,
                'topDebtors'          => $topDebtors,
            ], $data)),
            default => abort(403)
        };
    }
}
