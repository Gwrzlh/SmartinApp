<?php

namespace App\Http\Controllers;

use App\Models\enrollments;
use App\Models\EnrollmentSchedule;
use App\Models\schedules;
use App\Models\transaction_details;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\bundlings;
use Carbon\Carbon;

class SchedulePlacementController extends Controller
{
    public function index(Request $request)
    {
        $query = bundlings::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('bundling_name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status == 'active') {
                $query->where('is_active', 1);
            } elseif ($status == 'inactive') {
                $query->where('is_active', 0);
            }
        }

        $programs = $query->latest()
            ->paginate(5)
            ->withQueryString();

        foreach ($programs as $bundle) {
            $enrolledCount = enrollments::where('item_type', 'bundling')
                ->where('item_id', $bundle->id)
                ->where('status_pembelajaran', '!=', 'inactive')
                ->count();

            $bundle->active_students_count = $enrolledCount;

            $start = Carbon::parse($bundle->start_date, 'Asia/Jakarta')->startOfDay();
            $end = $start->copy()->addMonths($bundle->duration_mounths)->startOfDay();
            $today = Carbon::now()->timezone('Asia/Jakarta')->startOfDay();

            if ($today->isBefore($start)) {
                $bundle->program_status = 'Belum Mulai';
            } elseif ($today->greaterThanOrEqualTo($end)) {
                $bundle->program_status = 'Selesai';
            } else {
                $bundle->program_status = 'Berjalan';
            }
        }

        return view('Kasir.SchedulesManage', compact('programs'));
    }

    public function show($id)
    {
        $program = bundlings::with('details.subject')->findOrFail($id);

        $schedules = schedules::with(['subject', 'mentor'])
            ->where('bundling_id', $id)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $enrollments = enrollments::with('student')
            ->where('item_type', 'bundling')
            ->where('item_id', $id)
            ->latest()
            ->get();

        return view('Kasir.schedules.show', compact('program', 'schedules', 'enrollments'));
    }

    public function getAvailableSchedules(Request $request, $subject_id)
    {
        $currentScheduleId = $request->query('current_schedule_id');

        $schedules = schedules::with('mentor')
            ->where('subject_id', $subject_id)
            ->where('id', '!=', $currentScheduleId)
            ->where('is_active', 1)
            ->withCount(['enrollments as active_students_count' => function ($query) {
                $query->where('enrollment_schedules.status', 'ongoing');
            }])
            ->get();

        $availableSchedules = $schedules->filter(function ($schedule) {
            return $schedule->active_students_count < $schedule->capacity;
        })->values();

        return response()->json($availableSchedules);
    }

    public function reschedule(Request $request)
    {
        $request->validate([
            'enrollment_schedule_id' => 'required|exists:enrollment_schedules,id',
            'target_schedule_id' => 'required|exists:schedules,id',
        ]);

        DB::beginTransaction();
        try {
            $enrollmentSchedule = EnrollmentSchedule::findOrFail($request->enrollment_schedule_id);
            $targetSchedule = schedules::lockForUpdate()->findOrFail($request->target_schedule_id);

            $currentEnrolledCount = EnrollmentSchedule::where('schedule_id', $targetSchedule->id)
                ->where('status', 'ongoing')
                ->count();

            if ($currentEnrolledCount >= $targetSchedule->capacity) {
                throw new \Exception('Jadwal tujuan sudah penuh.');
            }

            $enrollment = $enrollmentSchedule->enrollment;
            if ($targetSchedule->subject_id != $enrollment->item_id) { 
                throw new \Exception('Jadwal tujuan harus memiliki mata pelajaran yang sama.');
            }

            $enrollmentSchedule->update([
                'schedule_id' => $targetSchedule->id,
            ]);

            DB::commit();

            logActivity('Memindahkan Jadwal Siswa', 'Ke Jadwal Baru ID: '.$targetSchedule->id);

            return back()->with('success', 'Siswa berhasil dipindahkan jadwalnya.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function showPlacementUI($transaction_id)
    {
        $transaction = transactions::findOrFail($transaction_id);

        $detailIds = transaction_details::where('transaction_id', $transaction_id)->pluck('id');

        $enrollments = enrollments::with(['subject.schedules', 'student']) // assume student relation exists if needed, or we just pluck it.
            ->whereIn('transaction_detail_id', $detailIds)
            ->where('item_type', 'subject')
            ->get();

        if ($enrollments->isEmpty()) {
            return redirect()->route('kasir.transaction')->with('success', 'Transaksi berhasil, tidak ada jadwal pelajaran yang perlu diatur.');
        }

        $studentInfo = \App\Models\students::find($enrollments->first()->student_id);

        foreach ($enrollments as $enrollment) {
            foreach ($enrollment->subject->schedules as $schedule) {
                $enrolledCount = EnrollmentSchedule::where('schedule_id', $schedule->id)
                    ->where('status', 'ongoing')
                    ->count();
                $schedule->remaining_capacity = $schedule->capacity - $enrolledCount;
            }
        }

        return view('Kasir.schedule_placement', compact('transaction', 'enrollments', 'studentInfo'));
    }

    public function storeAssignments(Request $request, $transaction_id)
    {
        $assignments = $request->input('schedules'); // Array format: [enrollment_id => schedule_id]

        if (empty($assignments)) {
            return back()->with('error', 'Silakan pilih jadwal terlebih dahulu.');
        }

        try {
            DB::transaction(function () use ($assignments) {
                foreach ($assignments as $enrollment_id => $schedule_id) {
                    if (empty($schedule_id)) {
                        throw new \Exception('Ada mata pelajaran yang belum dipilih jadwalnya.');
                    }

                    // 1. Lock the schedule row to prevent race conditions
                    $schedule = schedules::where('id', $schedule_id)->lockForUpdate()->firstOrFail();

                    // 2. Real-time Capacity Check
                    $enrolledCount = EnrollmentSchedule::where('schedule_id', $schedule_id)
                        ->where('status', 'ongoing')
                        ->count();

                    if ($enrolledCount >= $schedule->capacity) {
                        $subjectName = $schedule->subject->mapel_name ?? 'Mata Pelajaran';
                        throw new \Exception("Jadwal untuk {$subjectName} pada {$schedule->hari} {$schedule->jam_mulai} sudah penuh!");
                    }

                    // 3. Save Assignment
                    EnrollmentSchedule::create([
                        'enrollment_id' => $enrollment_id,
                        'schedule_id' => $schedule_id,
                        'status' => 'ongoing',
                    ]);
                }
            });

            // If successful
            logActivity('Menyimpan Penempatan Jadwal Siswa');

            return redirect()->route('kasir.transaction')->with('success', 'Jadwal siswa berhasil disimpan dan dikunci!');

        } catch (\Exception $e) {
            // Transaction failed, rollback triggered automatically
            return back()->with('error', 'Ops, Gagal Menyimpan: '.$e->getMessage());
        }
    }
}
