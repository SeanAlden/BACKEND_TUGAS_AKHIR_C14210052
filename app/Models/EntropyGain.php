<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntropyGain extends Model
{
    //
    use HasFactory;

    protected $table = 'entropy_gain';

    protected $fillable = [
        'product_id',
        'entropy',
        'gain'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
