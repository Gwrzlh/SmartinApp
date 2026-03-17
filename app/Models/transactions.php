<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transactions extends Model
{
    protected $table = 'transactions';
    protected $fillable = ['user_id','tgl_bayar','total_bayar','uang_diterima','uang_kembali','status_pembayaran'];

    public function details()
    {
        return $this->hasMany(transaction_details::class, 'transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
