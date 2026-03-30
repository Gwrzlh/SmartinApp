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
}
