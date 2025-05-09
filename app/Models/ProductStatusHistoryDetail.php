<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductStatusHistoryDetail extends Model
{
    //
    use HasFactory;

    protected $fillable = ['product_status_history_id', 'product_exp_date', 'product_stock'];

    public function statusHistory()
    {
        return $this->belongsTo(ProductStatusHistory::class);
    }
}
