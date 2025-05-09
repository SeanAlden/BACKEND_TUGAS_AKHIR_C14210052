<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecisionTree extends Model
{
    //
    use HasFactory;

    protected $table = 'decision_tree';

    protected $fillable = [
        'product_id',
        'accuracy_category',
        'price_category',
        'stock_category',
        'recommendation'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
