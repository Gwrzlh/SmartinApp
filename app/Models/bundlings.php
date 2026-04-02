<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bundlings extends Model
{
    protected $table = 'bundlings';
    protected $fillable = ['bundling_name','description','bundling_price','is_active','duration_mounths','start_date','capacity'];

    public function details()
    {
        return $this->hasMany(bundling_details::class, 'bundling_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(subjects::class, 'bundling_details', 'bundling_id', 'subject_id');
    }

}
