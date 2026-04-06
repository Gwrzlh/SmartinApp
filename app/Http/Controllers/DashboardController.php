<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\students;
use App\Models\transactions;
use App\Models\enrollments;
use App\Models\bundlings;
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
            // Update otomatis siswa menjadi inactive jika masa kursus habis.
            // PENGECUALIAN: Siswa yang menunggak (graduated_debt) tetap 'active'
            // agar sistem keuangan bisa terus menagih.
            $activeSiswa = \App\Models\students::where('status', 'active')->get();
            foreach ($activeSiswa as $student) {
                // Jika siswa punya enrollment graduated_debt, jangan ubah statusnya
                if ($student->isGraduatedWithDebt()) {
                    continue;
                }

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

                // Jumlah siswa yang berstatus graduated_debt (lulus tapi nunggak)
                'graduatedDebtCount' => \App\Models\enrollments::where('status_pembelajaran', 'graduated_debt')
                    ->distinct('student_id')->count('student_id'),
            ];

            // --- 2B. DATA PIUTANG (untuk widget tunggakan) ---
            // Total piutang dari siswa yang sudah lulus tapi masih nunggak SPP.
            // Dihitung dari jumlah bulan yang terlewati × harga bundling.
            $debtEnrollments = \App\Models\enrollments::with(['bundling', 'student'])
                ->where('status_pembelajaran', 'graduated_debt')
                ->whereNotNull('expired_at')
                ->get();

            $totalPiutangLulusan = 0;
            $debtByStudent = [];

            foreach ($debtEnrollments as $enrollment) {
                if (!$enrollment->bundling || !$enrollment->student) continue;

                $expiredDate = \Carbon\Carbon::parse($enrollment->expired_at);
                $monthsLate  = max(1, (int) $expiredDate->diffInMonths(now()) + 1);
                $debtAmount  = $enrollment->bundling->bundling_price * $monthsLate;

                $totalPiutangLulusan += $debtAmount;

                // Kumpulkan per siswa untuk top debtor
                $studentId = $enrollment->student_id;
                if (!isset($debtByStudent[$studentId])) {
                    $debtByStudent[$studentId] = [
                        'student'  => $enrollment->student,
                        'total'    => 0,
                    ];
                }
                $debtByStudent[$studentId]['total'] += $debtAmount;
            }

            // Sort descending berdasarkan total tunggakan, ambil Top 5
            usort($debtByStudent, fn($a, $b) => $b['total'] <=> $a['total']);
            $topDebtors = array_slice($debtByStudent, 0, 5);

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
                'today'               => $todayStr,
                'cashierName'         => $cashierName,
                'schedules'           => $schedules,
                'officeStatus'        => 'Open',
                'totalPiutangLulusan' => $totalPiutangLulusan,
                'topDebtors'          => $topDebtors,
            ], $data));
        }
}
