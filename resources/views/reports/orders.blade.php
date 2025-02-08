<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .order { margin-bottom: 20px; }
        .order-header { font-size: 18px; font-weight: bold; }
        .order-date, .customer-name { margin-left: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .order-total { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    @foreach ($orders as $order)
        <div class="order">
            <div class="order-header">Order ID: {{ $order['order_id'] }}</div>
            <div class="order-date">Date: {{ $order['order_date'] }}</div>
            <div class="customer-name">Customer: {{ $order['customer_name'] }}</div>

            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order['products'] as $product)
                        <tr>
                            <td>{{ $product['product_name'] }}</td>
                            <td>{{ $product['quantity'] }}</td>
                            <td>{{ $product['total_price'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="order-total">Order Total: {{ $order['order_total'] }}</div>
        </div>
    @endforeach
</body>
</html>
