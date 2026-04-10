<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\enrollments;
use App\Models\mentors;
use App\Models\schedules;
use App\Models\students;
use App\Models\transactions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $data = Cache::remember('owner_dashboard_data', 900, function () {

            $now = Carbon::now();
            $currentMonth = $now->month;
            $currentYear = $now->year;
            $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$now->dayOfWeek];

            // --- 1. GROWTH REVENUE (Bulan Ini vs Bulan Lalu) ---
            $revenueThisMonth = transactions::where('status_pembayaran', 'paid')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total_bayar');

            $lastMonth = $now->copy()->subMonth();
            $revenueLastMonth = transactions::where('status_pembayaran', 'paid')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('total_bayar');

            $revenueGrowth = 0;
            if ($revenueLastMonth > 0) {
                $revenueGrowth = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
            } elseif ($revenueThisMonth > 0) {
                $revenueGrowth = 100;
            }

            // --- 2. TOP MENTORS (Berdasarkan jumlah jadwal) ---
            $topMentors = mentors::withCount('schedules')
                ->orderByDesc('schedules_count')
                ->take(5)
                ->get();

            // --- 3. TOTAL PIUTANG (Logic sama dengan Kasir) ---
            // Mengambil semua enrollment yang nunggak (Active & Expired ATAU graduated_debt)
            $debtEnrollments = enrollments::with(['bundling', 'student'])
                ->where(function($q) {
                    $q->where(function($sub) {
                        $sub->where('status_pembelajaran', 'active')
                            ->where('expired_at', '<', now()->toDateString());
                    })->orWhere('status_pembelajaran', 'graduated_debt');
                })
                ->whereNotNull('expired_at')
                ->get();

            $totalPiutang = 0;
            $debtByStudent = [];

            foreach ($debtEnrollments as $enrollment) {
                if (!$enrollment->bundling || !$enrollment->student) continue;

                $expiredDate = \Carbon\Carbon::parse($enrollment->expired_at);
                $monthsLate  = max(1, (int) $expiredDate->diffInMonths(now()) + 1);
                $debtAmount  = $enrollment->bundling->bundling_price * $monthsLate;

                $totalPiutang += $debtAmount;

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

            // --- 4. TREND REVENUE (6 Bulan) ---
            $revenueTrend = [
                'labels' => [],
                'data' => [],
            ];
            for ($i = 5; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $revenue = transactions::where('status_pembayaran', 'paid')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('total_bayar');
                $revenueTrend['labels'][] = $month->translatedFormat('M Y');
                $revenueTrend['data'][] = $revenue;
            }

            // --- 5. ROOM OCCUPANCY & UPCOMING ---
            $schedulesList = schedules::with(['subject', 'enrollments'])->withCount('enrollments')->get();
            $roomOccupancy = [];
            foreach ($schedulesList as $schedule) {
                $ruangan = $schedule->ruangan;
                $capacity = $schedule->capacity ?? 1; // Prevent division by zero
                $filled = $schedule->enrollments_count;

                $roomOccupancy[] = [
                    'ruangan' => $ruangan,
                    'subject' => $schedule->subject->mapel_name ?? 'Unknown',
                    'capacity' => $capacity,
                    'filled' => $filled,
                    'is_full' => $filled >= $capacity,
                    'percentage' => $capacity > 0 ? min(100, round(($filled / $capacity) * 100)) : 0,
                ];
            }
            usort($roomOccupancy, fn($a, $b) => $b['percentage'] <=> $a['percentage']);
            $roomOccupancy = array_slice($roomOccupancy, 0, 5);

            $currentTime = $now->format('H:i:s');
            $upcomingSchedules = schedules::with(['subject', 'mentor'])
                ->where('hari', $hariIndo)
                ->where('jam_mulai', '>=', $currentTime)
                ->orderBy('jam_mulai', 'asc')
                ->take(5)
                ->get();

            if ($upcomingSchedules->count() < 5) {
                $upcomingSchedules = schedules::with(['subject', 'mentor'])
                    ->orderBy('hari', 'asc')
                    ->orderBy('jam_mulai', 'asc')
                    ->take(5)
                    ->get();
            }

            $activeStudents = students::where('status', 'active')->count();
            $newEnrollments = enrollments::where('created_at', '>=', $now->copy()->subDays(30))->count();
            $classesToday = schedules::where('hari', $hariIndo)->count();

            $activityLogs = ActivityLog::with('user')->latest()->take(5)->get();

            return [
                'totalRevenue' => $revenueThisMonth,
                'revenueGrowth' => $revenueGrowth,
                'activeStudents' => $activeStudents,
                'newEnrollments' => $newEnrollments,
                'classesToday' => $classesToday,
                'totalPiutang' => $totalPiutang,
                'topDebtors' => $topDebtors,
                'topMentors' => $topMentors,
                'revenueTrend' => $revenueTrend,
                'roomOccupancy' => $roomOccupancy,
                'upcomingSchedules' => $upcomingSchedules,
                'activityLogs' => $activityLogs,
            ];
        });

        $user = auth()->user();

        return view('Owner.dashboard', array_merge($data, ['user' => $user]));
    }

    public function laporanKeuangan(Request $request)
    {
        $query = transactions::with(['user', 'details.enrollment.student'])
            ->where('status_pembayaran', 'paid')->latest();

        // Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        // Search (ID Transaksi atau Nama Siswa)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('details.enrollment.student', function ($sq) use ($search) {
                        $sq->where('student_name', 'like', "%{$search}%");
                    });
            });
        }

        // Summary Stats untuk data yang difilter (Tanpa Pagination)
        $summaryQuery = clone $query;
        $totalRevenue = $summaryQuery->sum('total_bayar');
        $totalTransactions = $summaryQuery->count();
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Final Data with Pagination
        $transactions = $query->latest()->paginate(10)->withQueryString();

        return view('Owner.laporanKeuangan', compact(
            'transactions',
            'totalRevenue',
            'totalTransactions',
            'avgTransaction'
        ));
    }

    public function exportExcel(Request $request)
    {
        $query = transactions::with(['user', 'details.enrollment.student'])
            ->where('status_pembayaran', 'paid');

        // Apply same filters as in the main view
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('details.enrollment.student', function ($sq) use ($search) {
                        $sq->where('student_name', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->latest()->get();

        $fileName = 'Laporan_Keuangan_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID Transaksi', 'Tanggal', 'Siswa', 'Total Bayar', 'Kasir', 'Status'];

        $callback = function () use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $transaction) {
                $studentName = 'N/A';
                foreach ($transaction->details as $detail) {
                    if ($detail->enrollment && $detail->enrollment->student) {
                        $studentName = $detail->enrollment->student->student_name;
                        break;
                    }
                }

                fputcsv($file, [
                    $transaction->id,
                    $transaction->tgl_bayar ? $transaction->tgl_bayar->format('Y-m-d') : $transaction->created_at->format('Y-m-d'),
                    $studentName,
                    $transaction->total_bayar,
                    $transaction->user->full_name ?? 'Unknown',
                    $transaction->status_pembayaran,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function manajemenAsset(Request $request)
    {
        // Ambil input filter
        $mode = $request->get('mode', 'paket'); // Default mode adalah mapel
        $categoryId = $request->get('category_id');
        $q_mapel = $request->get('q_mapel');
        $q_bundling = $request->get('q_bundling');
        $q_mentor = $request->get('q_mentor');
        $filter_subject_id = $request->get('filter_subject_id');

        $categories = \App\Models\categories::all();
        $allSubjects = \App\Models\subjects::all();

        $bundlingsQuery = \App\Models\bundlings::with(['details.subject'])
            ->withCount('enrollments');
        if ($q_bundling) {
            $bundlingsQuery->where('bundling_name', 'like', '%'.$q_bundling.'%');
        }

        $bundlings = $bundlingsQuery->get();

        $mentorsQuery = mentors::with(['subjects'])->withCount('schedules');
        if ($q_mentor) {
            $mentorsQuery->where(function ($q) use ($q_mentor) {
                $q->where('mentor_name', 'like', '%'.$q_mentor.'%')
                    ->orWhereHas('subjects', function ($sq) use ($q_mentor) {
                        $sq->where('mapel_name', 'like', '%'.$q_mentor.'%');
                    });
            });
        }
        if ($filter_subject_id) {
            $mentorsQuery->whereHas('subjects', function ($q) use ($filter_subject_id) {
                $q->where('subjects.id', $filter_subject_id);
            });
        }
        $mentors = $mentorsQuery->get();

        return view('Owner.manajemanAsset', [
            'bundlings' => $bundlings,
            'mentors' => $mentors,
            'categories' => $categories,
            'allSubjects' => $allSubjects,
            'mode' => $mode,
        ]);
    }

    public function logActivity(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activityLogs = $query->latest()->paginate(20)->withQueryString();

        return view('Owner.logActivity', compact('activityLogs'));
    }
}
