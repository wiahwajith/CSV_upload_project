<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\CSVUploadRepositoryInterface;

class CSVService
{
    protected $csvUploadInterface;

    public function __construct(CSVUploadRepositoryInterface $csvUploadInterface)
    {
        $this->csvUploadInterface = $csvUploadInterface;
    }


    public function uploadCSV($file)
    {
        $directory = 'csv_uploads';
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($directory, $fileName, 'local');

        if (!$filePath) {
            throw new \Exception("Failed to store the CSV file.");
        }

        return $filePath;
    }


    public function processCSV($filePath)
    {
        $csvData = Excel::toArray([], Storage::path($filePath))[0]; 
    
        $header = array_map('trim', $csvData[0]); 
        unset($csvData[0]); 
        $rows = array_values($csvData);
    
        $entities = [];
        foreach ($rows as $row) {
            $entities[] = array_combine($header, $row); // Map header to row data
        }
    
        $validator = Validator::make($entities, [
            '*.customer_email' => 'required|email',
            '*.quantity' => 'required|integer|min:1',
            '*.product_price' => 'required|numeric|min:0',
        ]);
    
        if ($validator->fails()) {
            throw new \Exception("Invalid data in CSV: " . $validator->errors()->first());
        }
    
        $this->csvUploadInterface->storeData($entities);
    
        return $entities;
    }

    public function generatePDFReport($data)
    {
        try {
            $orderData = $this->setReportData();

            $pdf = Pdf::loadView('reports.orders', compact('orderData'))
            ->setPaper('a4', 'landscape');

            $uniqueFileName = 'orders_report_' . time() . '.pdf';
            $filePath = storage_path('app/reports/' . $uniqueFileName);

            $pdf->save($filePath);

            return $uniqueFileName;
        } catch (\Exception $e) {
            // Log the exception message for debugging
            \Log::error('Error generating PDF report: ' . $e->getMessage());

            // Return a custom error message or handle it according to your needs
            return 'Error generating the PDF report. Please try again later.';
        }
    }

    public function setReportData()
    {
        try {
            $result = [
                'total_orders' => 0,
                'total_revenue' => 0.00,
                'total_customers' => 0,
                'product_sales_summary' => [],
                'customer_summary' => []
            ];

            // Retrieve all orders with their relationships
            $orders =  $this->csvUploadInterface->getOrderData();

            // Loop through orders to accumulate data
            foreach ($orders as $order) {
                $result['total_orders']++;
                $result['total_revenue'] += $order->orderProducts->sum('total_price');

                // Accumulate product sales summary
                foreach ($order->orderProducts as $orderProduct) {
                    $productName = $orderProduct->product->name;
                    $productPrice = $orderProduct->product->price;
                    $productQuantity = $orderProduct->quantity;
                    $productRevenue = $orderProduct->total_price;

                    // If the product is not already in the summary, add it
                    if (!isset($result['product_sales_summary'][$productName])) {
                        $result['product_sales_summary'][$productName] = [
                            'product_name' => $productName,
                            'product_price' => $productPrice,
                            'product_quantity' => 0,
                            'product_total_revenue' => 0.00
                        ];
                    }

                    $result['product_sales_summary'][$productName]['product_quantity'] += $productQuantity;
                    $result['product_sales_summary'][$productName]['product_total_revenue'] += $productRevenue;
                }

                $customerName = $order->customer->name;
                $customerEmail = $order->customer->email;

                // If the customer is not already in the summary, add them
                if (!isset($result['customer_summary'][$customerName])) {
                    $result['customer_summary'][$customerName] = [
                        'customer_name' => $customerName,
                        'customer_email' => $customerEmail,
                        'customer_total_orders' => 0,
                        'customer_total_quantity_purchased' => 0,
                        'customer_total_spent' => 0.00
                    ];
                }

                $result['customer_summary'][$customerName]['customer_total_orders']++;
                $result['customer_summary'][$customerName]['customer_total_quantity_purchased'] += $order->orderProducts->sum('quantity');
                $result['customer_summary'][$customerName]['customer_total_spent'] += $order->orderProducts->sum('total_price');
            }

            // Count the total number of unique customers
            $result['total_customers'] = count($result['customer_summary']);

            // Convert the product sales summary array to a numerically indexed array
            $result['product_sales_summary'] = array_values($result['product_sales_summary']);
            $result['customer_summary'] = array_values($result['customer_summary']);

            return $result;
        } catch (\Exception $e) {
            // Log the error message
            \Log::error('Error generating report data: ' . $e->getMessage());

            // Optionally rethrow the error with a custom message or handle it in another way
            throw new \Exception('An error occurred while generating the report data. Please try again later.');
        }
    }
}
