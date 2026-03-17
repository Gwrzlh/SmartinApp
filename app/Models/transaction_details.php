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
        // Jika item_type adalah subject/spp, dia mengambil dari item_id (belongsTo)
        if ($this->item_type == 'subject' || $this->item_type == 'spp') {
            return $this->belongsTo(enrollments::class, 'item_id');
        }
        
        // Jika pendaftaran baru, dia mencari yang transaction_detail_id-nya sama (hasOne)
        return $this->hasOne(enrollments::class, 'transaction_detail_id');
    }
}
