<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;

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
            'nilai_maksimal' => 'nullable|numeric|min:0'
        ]);

        try {
            Tugas::create([
                'kelas_id' => $kelas,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'deadline' => $request->deadline,
                'nilai_maksimal' => $request->nilai_maksimal ?? 100,
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
            'nilai_maksimal' => 'nullable|numeric|min:0'
        ]);

        try {
            $tugas = Tugas::findOrFail($id);

            $tugas->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'nilai_maksimal' => $request->nilai_maksimal ?? 100,
                'deadline' => $request->deadline,
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
            $tugas->delete();

            return redirect()->route('tugas.index', $kelasId)
                ->with('success', 'Tugas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }
}
