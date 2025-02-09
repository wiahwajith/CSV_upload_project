<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileDownloadController extends Controller
{
    public function downloadPDFReport($filename)
    {
        try {
            $filePath = storage_path('app/reports/' . $filename);

            if (file_exists($filePath)) {
                return response()->download($filePath);
            } else {
                return response()->json(['error' => 'File not found'], 404);
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error downloading PDF report: ' . $e->getMessage());

            // Return a JSON response with a 500 error if an exception occurs
            return response()->json(['error' => 'An error occurred while trying to download the file. Please try again later.'], 500);
        }
    }
}
