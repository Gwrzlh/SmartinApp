<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transaction_details extends Model
{
    protected $table = 'transaction_details';
    protected $fillable = ['transaction_id','item_type','item_id','price'];

    public function transaction()
    {
        return $this->belongsTo(transactions::class, 'transaction_id');
    }

    public function enrollment()
    {
        // Jika SPP, item_id adalah ID Enrollment (belongsTo)
        if ($this->item_type == 'spp') {
            return $this->belongsTo(enrollments::class, 'item_id');
        }
        
        // Untuk subject/bundling/registrasi, link-nya via transaction_detail_id
        return $this->hasOne(enrollments::class, 'transaction_detail_id');
    }
}
