<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\CSVService;

class CSVUploadController extends Controller
{
    protected $csvUploadService;

    public function __construct(CSVService $csvUploadService)
    {
        $this->csvUploadService = $csvUploadService;
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt|max:10240', // Ensure it's a CSV file, max size 10MB
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 400);
        }
    
        $file = $request->file('file');
    
        try {
            $filePath = $this->csvUploadService->uploadCSV($file);
    
            $data = $this->csvUploadService->processCSV($filePath);
    
            $pdfFileName = $this->csvUploadService->generatePDFReport($data);
    
    
            return response()->json([
                'message' => 'CSV file uploaded and processed successfully!',
                'file_path' => $filePath,
                'pdf_link' =>  route('download.report', ['filename' => basename($pdfFileName)]),
            ], 200);
    
        } catch (\Exception $e) {
            // Handle errors and exceptions
            return response()->json([
                'error' => 'An error occurred while processing the CSV file: ' . $e->getMessage(),
            ], 500);
        }
    }

}
