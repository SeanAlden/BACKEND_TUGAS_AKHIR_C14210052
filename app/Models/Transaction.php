<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'status',
        'user_id',
        'gross_amount',
        'shipping_cost',
        'total_payment',
        'shipping_method',
        'payment_method',
        'shipping_time',
        'transaction_date',
        'is_final',
    ];

    protected $casts = [
        'transaction_date' => 'datetime', // Konversi otomatis ke Carbon
        // 'is_final' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'transaction_details')  // Menyebutkan nama tabel pivot secara eksplisit
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function statusHistories()
    {
        return $this->hasMany(TransactionStatusHistory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
