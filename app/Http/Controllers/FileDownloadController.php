<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileDownloadController extends Controller
{
    public function downloadPDFReport($filename)
{
    $filePath = storage_path('app/reports/' . $filename);

    if (file_exists($filePath)) {
        return response()->download($filePath);
    } else {
        return response()->json(['error' => 'File not found'], 404);
    }
}
}
