<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class students extends Model
{
    use SoftDeletes;

    protected $fillable = ['student_nik', 'student_name','phone_number','gender','address','status','email'];

    public function enrollments()
    {
        return $this->hasMany(enrollments::class, 'student_id');
    }

    /**
     * Cek apakah siswa memiliki tunggakan SPP.
     * Sistem berbasis SPP bulanan: siswa dianggap menunggak jika ada enrollment
     * yang masih 'active' tapi tanggal expired_at-nya sudah terlewati hari ini.
     *
     * @return bool
     */
    public function hasDebt(): bool
    {
        return $this->enrollments()
            ->where('status_pembelajaran', 'active')
            ->where('expired_at', '<', now()->toDateString())
            ->exists();
    }

    /**
     * Hitung total nominal tunggakan SPP siswa.
     * Kalkulasi: jumlah enrollment aktif yang menunggak × harga bundling per bulan.
     * Karena SPP = 1 bulan, setiap enrollment menunggak = 1 bulan SPP belum dibayar.
     *
     * @return int
     */
    public function totalDebt(): int
    {
        // Ambil enrollment yang menunggak beserta data bundling-nya
        $debtEnrollments = $this->enrollments()
            ->with('bundling')
            ->where('status_pembelajaran', 'active')
            ->where('expired_at', '<', now()->toDateString())
            ->get();

        $total = 0;
        foreach ($debtEnrollments as $enrollment) {
            // Hitung berapa bulan yang sudah terlewati sejak expired_at
            if ($enrollment->expired_at && $enrollment->bundling) {
                $expiredDate  = \Carbon\Carbon::parse($enrollment->expired_at);
                $monthsLate   = max(1, (int) $expiredDate->diffInMonths(now()) + 1);
                $total       += $enrollment->bundling->bundling_price * $monthsLate;
            }
        }

        return $total;
    }

    /**
     * Cek apakah siswa sudah lulus tapi masih memiliki tunggakan.
     * Status 'graduated_debt' di-set oleh command CloseExpiredPrograms
     * ketika bundling kadaluwarsa dan siswa belum lunas SPP.
     *
     * @return bool
     */
    public function isGraduatedWithDebt(): bool
    {
        return $this->enrollments()
            ->where('status_pembelajaran', 'graduated_debt')
            ->exists();
    }
}
