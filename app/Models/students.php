<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class students extends Model
{
    protected $fillable = ['student_nik', 'student_name','phone_number','gender','address','status'];
}
