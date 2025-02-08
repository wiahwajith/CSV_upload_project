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
                // Identify unique customers
                $customer = Customer::firstOrCreate(
                    ['email' => $data['customer_email']],
                    ['name' => $data['customer_name']]
                );
    
                // Identify unique orders and associate with the customer
                $order = Order::firstOrCreate(
                    ['id' => 12233558],
                    [
                        'customer_id' => $customer->id,
                        'order_date' => $data['order_date'],
                    ]
                );
                return dd([$order->id , $data['order_id']]);
                // Identify unique products
                $product = Product::firstOrCreate(
                    ['name' => $data['product_name']],
                    ['price' => $data['product_price']]
                );
                // Insert into order_items to associate products with orders
                $existingOrderItem = OrderProduct::where('order_id', $order->id)
                    ->where('product_id', $product->id)
                    ->first();
    
                if ($existingOrderItem) {
                    // If the product is already in the order, update the quantity and total price
                    $existingOrderItem->quantity += $data['quantity'];
                    $existingOrderItem->total_price += $data['quantity'] * $product->price;
                    $existingOrderItem->save();
                } else {
                    // Otherwise, create a new order item
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

    public function getDataForReport()
    {
        return Order::with(['customer', 'products'])
            ->get()
            ->map(function ($order) {
                return [
                    'Order ID' => $order->id,
                    'Customer Name' => $order->customer->name,
                    'Customer Email' => $order->customer->email,
                    'Order Date' => $order->order_date,
                    'Products' => $order->products->map(function ($product) {
                        return [
                            'Product Name' => $product->name,
                            'Product Price' => $product->price,
                            'Quantity' => $product->pivot->quantity,
                        ];
                    }),
                ];
            })
            ->toArray();
    }
}
