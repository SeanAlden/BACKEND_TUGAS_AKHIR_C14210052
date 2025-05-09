<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionDetail extends Model
{
    //
    use HasFactory;
    protected $fillable = ['transaction_id', 'product_id', 'exp_date','quantity', 'product_name', 'product_code', 'product_price', 'product_photo','stock_before', 'stock_after'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
