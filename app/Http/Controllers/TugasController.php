<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index(Request $request, $kelasId)
    {
        // ambil data kelas dengan relasi
        $kelas = \App\Models\Kelas::with(['guru', 'tugas.pengumpulan'])->findOrFail($kelasId);

        // jika client meminta JSON (mis. API) kembalikan json
        if ($request->wantsJson()) {
            return response()->json($kelas->tugas);
        }

        // untuk web: tampilkan blade
        return view('tugas.index', compact('kelas'));
    }

    public function show($id)
    {
        $tugas = Tugas::with(['kelas.guru', 'pengumpulan.siswa'])->findOrFail($id);

        // Cek jika siswa sudah submit
        $pengumpulan = null;
        if (session('role') === 'siswa') {
            $pengumpulan = $tugas->pengumpulan()
                ->where('siswa_nisn', session('identifier'))
                ->first();
        }

        return view('tugas.show', compact('tugas', 'pengumpulan'));
    }

    public function store(Request $request, $kelas)
    {
        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'required',
            'deadline' => 'required|date',
            'nilai_maksimal' => 'nullable|numeric|min:0',
            'file_contoh' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', 
        ]);

        try {
            // Handle file upload jika ada
            $fileContohPath = null;
            if ($request->hasFile('file_contoh')) {
                $fileContohPath = $request->file('file_contoh')->store('tugas_contoh', 'public');
            }

            Tugas::create([
                'kelas_id' => $kelas,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'deadline' => $request->deadline,
                'nilai_maksimal' => $request->nilai_maksimal ?? 100,
                'file_contoh' => $fileContohPath,
                'created_by' => session('identifier'),
            ]);

            return back()->with('success', 'Tugas berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat tugas: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $kelasId, $id)
    {
        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'required',
            'deadline' => 'required|date',
            'nilai_maksimal' => 'nullable|numeric|min:0',
            'file_contoh' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        try {
            $tugas = Tugas::findOrFail($id);

            // Handle file upload jika ada file baru
            $fileContohPath = $tugas->file_contoh; // Keep old file by default
            
            if ($request->hasFile('file_contoh')) {
                // Hapus file lama jika ada
                if ($tugas->file_contoh) {
                    Storage::disk('public')->delete($tugas->file_contoh);
                }
                
                // Upload file baru
                $fileContohPath = $request->file('file_contoh')->store('tugas_contoh', 'public');
            }

            $tugas->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'nilai_maksimal' => $request->nilai_maksimal ?? 100,
                'deadline' => $request->deadline,
                'file_contoh' => $fileContohPath,
            ]);

            return back()->with('success', 'Tugas berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui tugas: ' . $e->getMessage());
        }
    }

    public function destroy($kelasId, $id)
    {
        try {
            $tugas = Tugas::findOrFail($id);
            
            // Hapus file contoh jika ada
            if ($tugas->file_contoh) {
                Storage::disk('public')->delete($tugas->file_contoh);
            }
            
            $tugas->delete();

            return redirect()->route('tugas.index', $kelasId)
                ->with('success', 'Tugas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }
}