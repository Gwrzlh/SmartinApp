<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bundling_details extends Model
{
    protected $table = 'bundling_details';
    protected $fillable = ['bundling_id','subject_id'];

    public function subject()
    {
        return $this->belongsTo(subjects::class, 'subject_id');
    }
}
