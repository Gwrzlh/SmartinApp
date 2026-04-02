<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class enrollments extends Model
{
    protected $table = 'enrollments';
    protected $fillable = ['student_id','transaction_detail_id','item_type','item_id','tgl_daftar','status_pembelajaran','expired_at', 'finish_at'];

    public function subject()
    {
        return $this->belongsTo(subjects::class, 'item_id');
    }

    public function bundling()
    {
        return $this->belongsTo(bundlings::class, 'item_id');
    }

    public function enrollmentSchedule()
    {
        return $this->hasOne(EnrollmentSchedule::class, 'enrollment_id');
    }

    public function student()
    {
        return $this->belongsTo(students::class, 'student_id');
    }

    public function transaction_detail()
    {
        return $this->belongsTo(transaction_details::class, 'transaction_detail_id');
    }

    public function sppPayments()
    {
        return $this->hasMany(transaction_details::class, 'item_id')->where('item_type', 'spp');
    }
}
