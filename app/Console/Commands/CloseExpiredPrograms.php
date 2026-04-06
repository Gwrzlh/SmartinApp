<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\bundlings;
use App\Models\enrollments;
use App\Models\schedules;
use App\Models\students;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseExpiredPrograms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'programs:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatis penutupan program Bundling yang sudah melewati masa aktifnya.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan program yang kadaluwarsa...');

        // Cari bundling yang masih aktif tapi masa aktifnya sudah habis
        // Rumus: start_date + duration_mounths <= hari ini
        $expiredBundlings = bundlings::where('is_active', 1)
            ->whereNotNull('start_date')
            ->whereNotNull('duration_mounths')
            ->whereRaw("DATE_ADD(start_date, INTERVAL duration_mounths MONTH) <= CURDATE()")
            ->get();

        if ($expiredBundlings->isEmpty()) {
            $this->info('Tidak ada program yang kadaluwarsa saat ini.');
            return;
        }

        $totalBundlings       = 0;
        $totalGraduated       = 0; 
        $totalInactive        = 0; 
        $totalSchedules       = 0;
        $totalStudentsUpdated = 0;

        foreach ($expiredBundlings as $bundling) {
            DB::beginTransaction();
            try {
                // Hitung tanggal akhir program sebagai patokan kelulusan
                $endDate = \Carbon\Carbon::parse($bundling->start_date)
                    ->addMonths($bundling->duration_mounths);

                // 1. Ambil semua enrollment yang masih aktif/inactive untuk bundling ini
                //    SEBELUM bundling di-nonaktifkan agar perbandingan tanggal valid
                $enrollmentsToProcess = enrollments::where('item_type', 'bundling')
                    ->where('item_id', $bundling->id)
                    ->whereIn('status_pembelajaran', ['active', 'inactive'])
                    ->get();

                foreach ($enrollmentsToProcess as $enrollment) {
                    $student     = students::find($enrollment->student_id);
                    $expiredDate = $enrollment->expired_at
                        ? \Carbon\Carbon::parse($enrollment->expired_at)
                        : null;

                    // Perbandingan tanggal:
                    // expired_at >= endDate → sudah bayar sampai bulan terakhir → GRADUATED
                    // expired_at <  endDate → belum bayar penuh → INACTIVE
                    if ($expiredDate && $expiredDate->gte($endDate)) {
                        // --- LULUS (Lunas Penuh) ---
                        $enrollment->update(['status_pembelajaran' => 'graduated']);
                        $totalGraduated++;

                        if ($student) {
                            // Set siswa inactive hanya jika tidak ada enrollment aktif lain
                            $hasOtherActive = enrollments::where('student_id', $student->id)
                                ->where('status_pembelajaran', 'active')
                                ->where('id', '!=', $enrollment->id)
                                ->exists();

                            if (!$hasOtherActive) {
                                $student->update(['status' => 'inactive']);
                                $totalStudentsUpdated++;
                            }
                            $this->line("  [LULUS]    Siswa '{$student->student_name}' (expired: {$expiredDate->toDateString()} >= end: {$endDate->toDateString()}) → graduated");
                        }
                    } else {
                        // --- BELUM LUNAS (Ada Tunggakan / Belum Bayar Penuh) ---
                        $enrollment->update(['status_pembelajaran' => 'inactive']);
                        $totalInactive++;

                        if ($student) {
                            // Siswa tetap 'active' agar kasir bisa menagih
                            if ($student->status !== 'active') {
                                $student->update(['status' => 'active']);
                            }
                            $expiredStr = $expiredDate ? $expiredDate->toDateString() : 'NULL';
                            $this->line("  [TUNGGAK]  Siswa '{$student->student_name}' (expired: {$expiredStr} < end: {$endDate->toDateString()}) → inactive");
                        }
                    }
                }

                // 2. SETELAH semua enrollment diproses, baru non-aktifkan bundling
                $bundling->update(['is_active' => 0]);
                $totalBundlings++;

                // 3. Non-aktifkan semua jadwal terkait
                $affectedSchedules = schedules::where('bundling_id', $bundling->id)
                    ->where('is_active', 1)
                    ->update(['is_active' => 0]);

                $totalSchedules += $affectedSchedules;

                DB::commit();
                $this->line("[-] Program '{$bundling->bundling_name}' telah ditutup (end date: {$endDate->toDateString()}).");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Gagal menutup program '{$bundling->bundling_name}': " . $e->getMessage());
                Log::error("CloseExpiredPrograms Error: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('Selesai! Ringkasan aktivitas:');
        $this->table(
            ['Kategori', 'Jumlah Diperbarui'],
            [
                ['Program Bundling Ditutup',          $totalBundlings],
                ['Enrollment → Graduated (Lunas)',    $totalGraduated],
                ['Enrollment → Inactive (Tunggak)',   $totalInactive],
                ['Jadwal (Schedules) Dinonaktifkan',  $totalSchedules],
                ['Status Siswa → Inactive',           $totalStudentsUpdated],
            ]
        );
    }
}
