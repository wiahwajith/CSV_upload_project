<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 20px;
            page-break-before: auto; /* Remove unnecessary page-break */
            margin-top: 0; /* Ensure no margin at the top */
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-header h1 {
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        .summary {
            margin-top: 20px;
        }

        .summary table th, .summary table td {
            width: 25%;
        }

        /* Ensure page breaks occur only after content */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="report-header">
            <h1>Sales Report - January 2024</h1>
        </div>

        <!-- Total Metrics -->
        <div class="summary">
            <h3>Total Summary</h3>
            <table>
                <tr>
                    <th>Total Orders</th>
                    <td>{{ $orderData['total_orders'] }}</td>
                </tr>
                <tr>
                    <th>Total Revenue</th>
                    <td>${{ number_format($orderData['total_revenue'], 2) }}</td>
                </tr>
                <tr>
                    <th>Total Customers</th>
                    <td>{{ $orderData['total_customers'] }}</td>
                </tr>
            </table>
        </div>

        <!-- Product Sales Summary -->
        <div class="summary page-break">
            <h3>Product Sales Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity Sold</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderData['product_sales_summary'] as $product)
                        <tr>
                            <td>{{ $product['product_name'] }}</td>
                            <td>${{ number_format($product['product_price'], 2) }}</td>
                            <td>{{ $product['product_quantity'] }}</td>
                            <td>${{ number_format($product['product_total_revenue'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Customer Summary -->
        <div class="summary page-break">
            <h3>Customer Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Total Orders</th>
                        <th>Total Quantity Purchased</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderData['customer_summary'] as $customer)
                        <tr>
                            <td>{{ $customer['customer_name'] }}</td>
                            <td>{{ $customer['customer_email'] }}</td>
                            <td>{{ $customer['customer_total_orders'] }}</td>
                            <td>{{ $customer['customer_total_quantity_purchased'] }}</td>
                            <td>${{ number_format($customer['customer_total_spent'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
