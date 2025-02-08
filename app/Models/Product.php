<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name','price'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'total_price');
    }

    /**
     * A product has many order items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
