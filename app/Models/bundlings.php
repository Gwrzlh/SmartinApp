<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bundlings extends Model
{
    protected $table = 'bundlings';
    protected $fillable = ['bundling_name','description','bundling_price','is_active'];

    public function details()
    {
        return $this->hasMany(bundling_details::class, 'bundling_id');
    }

    public function subjects()
    {
        return $this->belongsTo(subjects::class, 'subjects_id');
    }

}
