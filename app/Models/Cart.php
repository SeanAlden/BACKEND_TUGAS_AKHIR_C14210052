<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// class Cart extends Model
// {
//     use HasFactory;
//     protected $fillable = ['product_id', 'exp_date', 'quantity', 'gross_amount', 'shipping_method', 'payment_method'];

//     public function product()
//     {
//         return $this->belongsTo(Product::class);
//     }
// }


class Cart extends Model
{
    use HasFactory;

    // Tambahkan semua kolom yang bisa diisi (fillable)
    protected $fillable = [
        'user_id',
        'product_id',
        'exp_date',
        'quantity',
        'gross_amount',
        'shipping_method',
        'payment_method',
    ];

    // Relasi ke model Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke model User (karena ada user_id di tabel carts)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // // Cart.php
    // public function productStock()
    // {
    //     return $this->hasOne(ProductStock::class, 'product_id', 'product_id')
    //         ->whereColumn('exp_date', 'carts.exp_date');
    // }
    public function productStock()
    {
        return $this->hasMany(ProductStock::class, 'product_id', 'product_id');
    }
}

