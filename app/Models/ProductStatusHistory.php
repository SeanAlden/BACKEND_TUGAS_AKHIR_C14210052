<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStatusHistory extends Model
{
    //
    protected $fillable = ['product_id', 'condition', 'changed_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function details()
    {
        return $this->hasMany(ProductStatusHistoryDetail::class);
    }
}
