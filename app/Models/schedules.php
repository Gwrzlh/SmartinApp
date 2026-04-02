<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class schedules extends Model
{
    protected $table = 'schedules';
    protected $fillable = ['bundling_id','subject_id','mentor_id','hari','jam_mulai','jam_selesai','ruangan','is_active'];

    public function enrollments()
    {
        return $this->belongsToMany(enrollments::class, 'enrollment_schedules', 'schedule_id', 'enrollment_id');
    }
    public function subject()
    {
        return $this->belongsTo(subjects::class, 'subject_id');
    }
    public function mentor()
    {
        return $this->belongsTo(mentors::class, 'mentor_id');
    }

    public function bundling()
    {
        return $this->belongsTo(bundlings::class, 'bundling_id');
    }
}
