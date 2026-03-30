<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class categories extends Model
{
    protected $fillable = ['category_name','id'];


    public function subjects(): HasMany
    {
        return $this->hasMany(subjects::class, 'category_id');
    }
}
