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
    
        // try {
            // Store the CSV file using the file upload service
            $filePath = $this->csvUploadService->uploadCSV($file);
    
            //  Process the CSV and store the data in the database
            $data = $this->csvUploadService->processCSV($filePath);
    
            // // Generate a PDF report from the processed data
            // $pdfFilePath = $this->csvUploadService->generatePDFReport($data);
    
            // // Generate a public URL for the generated PDF file
            // $pdfLink = Storage::url($pdfFilePath);
    
            // Return a successful JSON response
            return response()->json([
                'message' => 'CSV file uploaded and processed successfully!',
                'file_path' => $filePath,
                // 'pdf_link' => $pdfLink,
            ], 200);
    
        // } catch (\Exception $e) {
        //     // Handle errors and exceptions
        //     return response()->json([
        //         'error' => 'An error occurred while processing the CSV file: ' . $e->getMessage(),
        //     ], 500);
        // }
    }
    // public function upload(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|mimes:csv,txt|max:10240', // Ensure it's a CSV file, max size 10MB
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => $validator->errors()->first()
    //         ], 400);
    //     }

    //     // Call the repository to handle the file storage logic
    //     $filePath = $this->csvUploadRepo->storeCSV($request->file('file'));


    //     //process sav data
        

    //     // enerate PDF report

    //     return response()->json([
    //         'message' => 'CSV file uploaded successfully!',
    //         'file_path' => $filePath
    //     ], 200);
    // }
}
