<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\students;
use App\Models\transactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->translatedFormat('d F Y');
        $cashierName = Auth::user()->full_name ?? Auth::user()->name ?? 'Kasir';
        $officeStatus = 'Open';

        $unpaidCount = transactions::where('status_pembayaran', 'unpaid')->count();
        $inactiveCount = students::where('status', 'inactive')->count();

        $dayMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];

        $day = $dayMap[Carbon::now()->format('l')] ?? Carbon::now()->format('l');
        $nowTime = Carbon::now()->format('H:i:s');

        $schedulesQuery = DB::table('schedules as s')
            ->leftJoin('subjects as sub', 's.subject_id', '=', 'sub.id')
            ->leftJoin('mentors as m', 's.mentor_id', '=', 'm.id')
            ->leftJoin('enrollment_schedules as es', 's.id', '=', 'es.schedule_id')
            ->leftJoin('enrollments as e', 'es.enrollment_id', '=', 'e.id')
            ->leftJoin('students as st', 'e.student_id', '=', 'st.id')
            ->select('s.*', 'sub.mapel_name as subject_name', 'm.mentor_name as mentor_name', DB::raw('COUNT(DISTINCT st.id) as student_count'))
            ->where('s.hari', $day)
            ->groupBy('s.id', 'sub.mapel_name', 'm.mentor_name')
            ->orderByRaw("CASE WHEN s.jam_mulai <= '{$nowTime}' AND s.jam_selesai >= '{$nowTime}' THEN 0 ELSE 1 END, s.jam_mulai")
            ->get();

        $schedules = $schedulesQuery->map(function ($s) {
            $studentsList = DB::table('enrollment_schedules as es')
                ->join('enrollments as e', 'es.enrollment_id', '=', 'e.id')
                ->join('students as st', 'e.student_id', '=', 'st.id')
                ->where('es.schedule_id', $s->id)
                ->select('st.student_name')
                ->limit(6)
                ->get();
            $s->students = $studentsList;
            return $s;
        });

        return view('Kasir.dashboard', compact('today', 'cashierName', 'officeStatus', 'unpaidCount', 'inactiveCount', 'schedules'));
    }
}
