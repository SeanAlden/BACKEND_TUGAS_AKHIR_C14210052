<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    //
    use HasFactory;
    protected $fillable = ['code', 'name', 'price', 'photo', 'description', 'category_id', 'condition'];

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class);
    // }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_details')  // Menyebutkan nama tabel pivot secara eksplisit
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function getIsInTransactionAttribute()
    {
        return $this->hasMany(TransactionDetail::class, 'product_id')->exists();
    }

    public function statusHistories()
    {
        return $this->hasMany(ProductStatusHistory::class);
    }

}
