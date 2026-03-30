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
            $todayDate = \Carbon\Carbon::now();
            $todayStr = $todayDate->translatedFormat('d F Y');
            $cashierName = Auth::user()->full_name ?? Auth::user()->name ?? 'Kasir';
            
            // --- 1. SYNC STATUS SISWA (Logic dari Transaksi) ---
            // Update otomatis siswa menjadi inactive jika masa kursus habis
            $activeSiswa = \App\Models\students::where('status', 'active')->get();
            foreach ($activeSiswa as $student) {
                $stillHasActiveCourse = \App\Models\enrollments::where('student_id', $student->id)
                    ->where('status_pembelajaran', 'active')
                    ->where('expired_at', '>=', now()->toDateString())
                    ->exists();

                if (!$stillHasActiveCourse) {
                    $student->update(['status' => 'inactive']);
                }
            }

            // --- 2. STATISTIK KASIR ---
            $data = [
                // Total uang masuk dari transaksi 'paid' hari ini
                'incomeToday' => \App\Models\transactions::whereDate('created_at', $todayDate)
                                    ->where('status_pembayaran', 'paid')
                                    ->sum('total_bayar'),
                
                // Jumlah transaksi sukses hari ini
                'todayTransactions' => \App\Models\transactions::whereDate('created_at', $todayDate)->count(),
                
                // Siswa yang perlu ditagih (Inactive)
                'inactiveCount' => \App\Models\students::where('status', 'inactive')->count(),
                
                // Total Siswa Aktif
                'activeCount' => \App\Models\students::where('status', 'active')->count(),
            ];

            // --- 3. JADWAL HARI INI ---
            $dayMap = [
                'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
            ];
            $day = $dayMap[$todayDate->format('l')] ?? $todayDate->format('l');
            $nowTime = $todayDate->format('H:i:s');

            $schedules = \DB::table('schedules as s')
                ->leftJoin('subjects as sub', 's.subject_id', '=', 'sub.id')
                ->leftJoin('mentors as m', 's.mentor_id', '=', 'm.id')
                ->select('s.*', 'sub.mapel_name as subject_name', 'm.mentor_name as mentor_name', 
                    \DB::raw('(SELECT COUNT(DISTINCT e.student_id) 
                            FROM enrollment_schedules es 
                            JOIN enrollments e ON es.enrollment_id = e.id 
                            WHERE es.schedule_id = s.id) as student_count'))
                ->where('s.hari', $day)
                ->orderByRaw("CASE WHEN s.jam_mulai <= '{$nowTime}' AND s.jam_selesai >= '{$nowTime}' THEN 0 ELSE 1 END, s.jam_mulai")
                ->get();

            return view('Kasir.dashboard', array_merge([
                'today' => $todayStr, 
                'cashierName' => $cashierName, 
                'schedules' => $schedules,
                'officeStatus' => 'Open'
            ], $data));
        }
}
