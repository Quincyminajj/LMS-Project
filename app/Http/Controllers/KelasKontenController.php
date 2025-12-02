<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KelasKonten;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage;

class KelasKontenController extends Controller
{
    /**
     * Simpan konten baru
     */
    public function store(Request $request, $id)
    {
        // Validasi input dengan aturan yang lebih ketat
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:file,link,teks',
            'isi' => 'nullable|string',
            'file_path' => 'required_if:tipe,file|nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,zip,rar|max:10240',
        ], [
            'judul.required' => 'Judul konten wajib diisi',
            'tipe.required' => 'Tipe konten wajib dipilih',
            'isi.required_if' => 'Link atau teks wajib diisi',
            'file_path.required_if' => 'File wajib diupload',
            'file_path.mimes' => 'Format file tidak didukung',
            'file_path.max' => 'Ukuran file maksimal 10MB',
        ]);

        // Persiapkan data dasar
        $data = [
            'kelas_id' => $id,
            'judul' => $validated['judul'],
            'tipe' => $validated['tipe'],
            'uploaded_by' => session('identifier'),
            'isi' => null,
            'file_path' => null,
        ];

        // Proses berdasarkan tipe konten
        if ($validated['tipe'] === 'file' && $request->hasFile('file_path')) {

            // Upload file ke storage
            $file = $request->file('file_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('materi_kelas', $fileName, 'public');

            $data['file_path'] = $filePath;
            $data['isi'] = $file->getClientOriginalName(); // Simpan nama asli file

        } elseif ($validated['tipe'] === 'link') {

            // Format URL link - tambahkan https:// jika tidak ada protokol
            $link = trim($validated['isi']);

            // Cek apakah sudah ada protokol http:// atau https://
            if (!preg_match('/^https?:\/\//i', $link)) {
                $link = 'https://' . $link;
            }

            $data['isi'] = $link;
            $data['file_path'] = null;
        } elseif ($validated['tipe'] === 'teks') {

            // Simpan teks biasa
            $data['isi'] = $validated['isi'];
            $data['file_path'] = null;
        }

        // Simpan ke database
        try {
            KelasKonten::create($data);
            return back()->with('success', 'Konten berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan konten: ' . $e->getMessage());
        }
    }

    /**
     * Update konten yang sudah ada
     */
    public function update(Request $request, $id)
    {
        try {
            $konten = KelasKonten::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'isi' => 'nullable|string',
            ]);

            // Update data
            $konten->update([
                'judul' => $validated['judul'],
                'isi' => $validated['isi'] ?? $konten->isi,
            ]);

            return back()->with('success', 'Konten berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui konten: ' . $e->getMessage());
        }
    }

    /**
     * Hapus konten dari database
     */
    public function destroy($id)
    {
        try {
            $konten = KelasKonten::findOrFail($id);

            // Simpan kelas_id untuk redirect
            $kelasId = $konten->kelas_id;

            // Hapus file fisik jika tipe file
            if ($konten->tipe === 'file' && $konten->file_path) {
                if (Storage::disk('public')->exists($konten->file_path)) {
                    Storage::disk('public')->delete($konten->file_path);
                }
            }

            // Hapus dari database
            $konten->delete();

            return back()->with('success', 'Konten berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus konten: ' . $e->getMessage());
        }
    }
}
