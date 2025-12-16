<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function preview($path)
    {
        $path = urldecode($path);
        $filePath = storage_path('app/public/' . $path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];

        if (!array_key_exists($extension, $mimeTypes)) {
            abort(403, 'Tipe file tidak didukung untuk preview');
        }

        return response()->file($filePath, [
            'Content-Type' => $mimeTypes[$extension],
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }
}