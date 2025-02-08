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

    /**
     * Store the uploaded CSV file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
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

    /**
     * Process the CSV file and save data into the database.
     *
     * @param string $filePath
     * @return array
     */
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
        $orders = $this->csvUploadInterface->getOrderData();

        $pdf = Pdf::loadView('reports.orders', compact('orders'))
            ->setPaper('a4', 'landscape');

        $uniqueFileName = 'orders_report_' . time() . '.pdf'; 
        $filePath = storage_path('app/reports/' . $uniqueFileName);
        
        $pdf->save($filePath);

        return $uniqueFileName;
    }
}
