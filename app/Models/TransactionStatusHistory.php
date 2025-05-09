<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'transaction_status_histories';

    protected $fillable = [
        'transaction_id',
        'status',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
