<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class schedules extends Model
{
    protected $table = 'schedules';
    protected $fillable = ['subject_id','mentor_id','hari','jam_mulai','jam_selesai','ruangan','capacity','is_active'];

    public function enrollments()
    {
        return $this->belongsToMany(enrollments::class, 'enrollment_schedules', 'enrollment_id', 'schedule_id');
    }
    public function subject()
    {
        return $this->belongsTo(subjects::class, 'subject_id');
    }
    public function mentor()
    {
        return $this->belongsTo(mentors::class, 'mentor_id');
    }
}
