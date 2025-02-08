<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
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
    /**
     * Generate a PDF report from the stored data.
     *
     * @param array $data
     * @return string
     */
    public function generatePDFReport($data)
    {
        $pdf = new Dompdf();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $pdf->setOptions($options);

        $html = '<h1>CSV Data Report</h1><table style="width: 100%; border: 1px solid black; border-collapse: collapse;">';
        $html .= '<tr>';
        foreach (array_keys($data[0]) as $column) {
            $html .= "<th style='border: 1px solid black; padding: 5px;'>$column</th>";
        }
        $html .= '</tr>';

        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= "<td style='border: 1px solid black; padding: 5px;'>$value</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $pdfFilePath = 'pdf_reports/report_' . time() . '.pdf';
        Storage::put($pdfFilePath, $pdf->output());

        return $pdfFilePath;
    }
}
