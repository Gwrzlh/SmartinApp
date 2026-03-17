<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentSchedule extends Model
{
    protected $table = 'enrollment_schedules';
    protected $fillable = ['enrollment_id', 'schedule_id', 'status'];
    
    public function enrollment()
    {
        return $this->belongsTo(enrollments::class, 'enrollment_id');
    }

    public function schedule()
    {
        return $this->belongsTo(schedules::class, 'schedule_id');
    }
}
