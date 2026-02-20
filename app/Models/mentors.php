<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mentors extends Model
{
    protected $fillable = ['mentor_name','gender','phone_number','specialization_id','status'];
}
