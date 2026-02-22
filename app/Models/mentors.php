<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mentors extends Model
{
    protected $table = 'mentors';
    protected $fillable = ['mentor_name','gender','phone_number','specialization_id','is_active'];


    public function subjects()
    {
        return $this->belongsTo(subjects::class, 'specialization_id', 'id');
    }
}
