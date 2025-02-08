<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'customer_id' , 'order_date', 'total'];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * An order can have many products through order_items.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'total_price');
    }

    /**
     * An order has many order items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
