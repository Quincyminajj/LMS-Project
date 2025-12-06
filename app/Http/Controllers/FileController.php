<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function preview($path)
    {
        // Decode path jika ada encoding
        $path = urldecode($path);
        
        // Path lengkap ke file
        $filePath = storage_path('app/public/' . $path);
        
        // Cek apakah file ada
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan: ' . $path);
        }
        
        // Validasi extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        
        if (!in_array($extension, $allowedExtensions)) {
            abort(403, 'Tipe file tidak didukung untuk preview');
        }
        
        // Baca file
        $file = file_get_contents($filePath);
        
        // Return response dengan header yang benar untuk preview
        return response($file, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Accept-Ranges', 'bytes')
            ->header('Cache-Control', 'private, max-age=0, must-revalidate')
            ->header('Pragma', 'public');
    }
}