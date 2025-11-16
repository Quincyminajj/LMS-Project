<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KelasKonten;   
use App\Models\Kelas;         

class KelasKontenController extends Controller
{
    public function store(Request $request, $id)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'tipe' => 'required|in:file,link,teks',
            'isi' => 'nullable',
            'file_path' => 'nullable|file',
        ]);

        $data['kelas_id'] = $id;
        $data['uploaded_by'] = session('identifier');

        // Upload file
        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('materi_kelas', 'public');
            $data['isi'] = $data['file_path'];   // simpan path FILENYA
        }

        // Format URL link
        if ($request->tipe === 'link' && !empty($data['isi'])) {
            if (!str_starts_with($data['isi'], 'http://') && !str_starts_with($data['isi'], 'https://')) {
                $data['isi'] = 'https://' . $data['isi'];
            }
        }

        KelasKonten::create($data);

        return back()->with('success', 'Konten berhasil ditambahkan');
    }
}
