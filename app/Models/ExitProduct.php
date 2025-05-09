<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitProduct extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'exp_date', 'removed_stock', 'previous_stock', 'current_stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
