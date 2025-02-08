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

    public function getOrderData()
    {
        return Order::with(['customer', 'orderProducts.product'])
            ->get()
            ->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'order_date' => $order->order_date->format('Y-m-d'),
                    'customer_name' => $order->customer->name,
                    'products' => $order->orderProducts->map(function ($orderProduct) {
                        return [
                            'product_name' => $orderProduct->product->name,
                            'quantity' => $orderProduct->quantity,
                            'total_price' => $orderProduct->total_price,
                        ];
                    }),
                    'order_total' => $order->orderProducts->sum('total_price'),
                ];
            })->toArray();
    }
}
