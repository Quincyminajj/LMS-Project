<?php

namespace App\Http\Controllers;

use App\Models\TugasPengumpulan;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasPengumpulanController extends Controller
{
    /**
     * Menampilkan semua pengumpulan tugas
     */
    public function index()
    {
        $pengumpulans = TugasPengumpulan::with(['tugas.kelas', 'siswa'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pengumpulans);
    }

    /**
     * Detail satu pengumpulan tugas
     */
    public function show($id)
    {
        $pengumpulan = TugasPengumpulan::with(['tugas.kelas', 'siswa'])
            ->findOrFail($id);

        return response()->json($pengumpulan);
    }

    /**
     * Siswa submit/kumpulkan tugas
     */
    public function store(Request $request, $tugas_id = null)
    {
        // Jika tugas_id dari route parameter
        if ($tugas_id) {
            $request->merge(['tugas_id' => $tugas_id]);
        }

        $validated = $request->validate([
            'tugas_id' => 'required|exists:tugas,id',
            'jawaban' => 'required|string',
            'file_path' => 'nullable|file|max:5120', // Max 5MB
        ]);

        // Cek apakah siswa sudah mengumpulkan
        $existingPengumpulan = TugasPengumpulan::where('tugas_id', $validated['tugas_id'])
            ->where('siswa_nisn', session('identifier'))  // ✅ PERBAIKI: Ubah dari siswa_nis ke siswa_nisn
            ->first();

        if ($existingPengumpulan) {
            return redirect()->back()->with('error', 'Anda sudah mengumpulkan tugas ini sebelumnya!');
        }

        // Upload file jika ada
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('pengumpulan_tugas', 'public');
        }

        // Simpan data pengumpulan
        TugasPengumpulan::create([
            'tugas_id' => $validated['tugas_id'],
            'siswa_nisn' => session('identifier'),  // ✅ PERBAIKI: Ubah dari siswa_nis ke siswa_nisn
            'jawaban' => $validated['jawaban'],
            'file_path' => $validated['file_path'] ?? null,
        ]);

        return redirect()->route('tugas.show', $validated['tugas_id'])
            ->with('success', 'Tugas berhasil dikumpulkan!');
    }

    /**
     * Guru memberi nilai
     */
    public function update(Request $request, $id)
    {
        $pengumpulan = TugasPengumpulan::findOrFail($id);

        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0',
            'catatan_guru' => 'nullable|string',
        ]);

        // Update nilai dan catatan
        $pengumpulan->update([
            'nilai' => $validated['nilai'],
            'catatan_guru' => $validated['catatan_guru'] ?? null,
            'dinilai_oleh' => session('identifier'), // NIP guru
        ]);

        return redirect()->route('tugas.show', $pengumpulan->tugas_id)
            ->with('success', 'Nilai berhasil disimpan!');
    }

    /**
     * Hapus pengumpulan tugas
     */
    public function destroy($id)
    {
        $pengumpulan = TugasPengumpulan::findOrFail($id);

        // Hapus file jika ada
        if ($pengumpulan->file_path) {
            Storage::disk('public')->delete($pengumpulan->file_path);
        }

        $tugas_id = $pengumpulan->tugas_id;
        $pengumpulan->delete();

        return redirect()->route('tugas.show', $tugas_id)
            ->with('success', 'Pengumpulan berhasil dihapus');
    }
}
