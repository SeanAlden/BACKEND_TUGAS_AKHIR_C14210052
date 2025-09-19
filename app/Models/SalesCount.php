<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesCount extends Model
{
    //
    use HasFactory;

    protected $table = 'sales_count';

    protected $fillable = [
        'product_id',
        'transaction_date',
        'raw_sales',
        'weighted_sales',
        'days_between_first_last_transaction',
        'time_weight'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
