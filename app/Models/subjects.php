<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class subjects extends Model
{
    protected $table = 'subjects';
    protected $fillable = ['mapel_name','category_id','description'];

    public function categories()
    {
        return $this->belongsTo(categories::class, 'category_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany(schedules::class, 'subject_id', 'id');
    }

    public function mentors()
    {
        return $this->belongsToMany(mentors::class, 'mentor_subjects', 'subject_id', 'mentor_id');
    }
}
