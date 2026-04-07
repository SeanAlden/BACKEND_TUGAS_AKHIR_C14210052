<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'external_id',
        'payment_url',
        'status',
        'amount',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
