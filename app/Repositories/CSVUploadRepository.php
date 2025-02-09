<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;
use App\Interfaces\CSVUploadRepositoryInterface;

class CSVUploadRepository implements CSVUploadRepositoryInterface
{
    public function storeData(array $entities)
    {
        DB::transaction(function () use ($entities) {
            foreach ($entities as $data) {

                $customer = Customer::firstOrCreate(
                    ['email' => $data['customer_email']],
                    ['name' => $data['customer_name']]
                );
    
                $order = Order::firstOrCreate(
                    ['id' => $data['order_id']],
                    [
                        'customer_id' => $customer->id,
                        'order_date' => $data['order_date'],
                    ]
                );
                $product = Product::firstOrCreate(
                    ['name' => $data['product_name']],
                    ['price' => $data['product_price']]
                );
                $existingOrderItem = OrderProduct::where('order_id', $order->id)
                    ->where('product_id', $product->id)
                    ->first();
    
                if ($existingOrderItem) {
                    $existingOrderItem->quantity += $data['quantity'];
                    $existingOrderItem->total_price += $data['quantity'] * $product->price;
                    $existingOrderItem->save();
                } else {
                    OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $data['quantity'],
                        'total_price' => $data['quantity'] * $product->price,
                    ]);
                }
            }
        });
    }

    public function getOrderData()
    {
        return Order::with(['customer', 'orderProducts.product'])
            ->get();
    }
}
