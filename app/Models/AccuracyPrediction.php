<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccuracyPrediction extends Model
{
    //
    use HasFactory;

    protected $table = 'accuracy_prediction';

    protected $fillable = [
        'product_id',
        'accuracy_percentage'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
