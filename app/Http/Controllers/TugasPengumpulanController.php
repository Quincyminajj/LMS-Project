<?php

namespace App\Http\Controllers;

use App\Models\TugasPengumpulan;
use Illuminate\Http\Request;

class TugasPengumpulanController extends Controller
{
    /**
     * Menampilkan semua pengumpulan tugas
     */
    public function index()
    {
        $pengumpulans = TugasPengumpulan::with(['tugas.kelas'])
            ->orderBy('dikumpul_pada', 'desc')
            ->get();

        return response()->json($pengumpulans);
    }

    /**
     * Detail satu pengumpulan tugas
     */
    public function show($id)
    {
        $pengumpulan = TugasPengumpulan::with(['tugas.kelas'])
            ->findOrFail($id);

        return response()->json($pengumpulan);
    }

    /**
     * Menyimpan pengumpulan tugas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tugas_id'      => 'required|exists:tugas,id',
            'siswa_nisn'    => 'required|string|max:20',
            'tipe'          => 'required|in:file,link,teks',
            'isi'           => 'nullable|string',
            'file_path'     => 'nullable|string|max:255',
            'nilai'         => 'nullable|numeric|min:0',
            'feedback'      => 'nullable|string',
            'dinilai_oleh'  => 'nullable|string|max:20',
            'dikumpul_pada' => 'nullable|date',
            'dinilai_pada'  => 'nullable|date',
        ]);

        // Pastikan siswa belum mengumpulkan tugas tersebut
        if (
            TugasPengumpulan::where('tugas_id', $validated['tugas_id'])
                ->where('siswa_nisn', $validated['siswa_nisn'])
                ->exists()
        ) {
            return response()->json([
                'error' => 'Pengumpulan untuk siswa ini sudah ada.'
            ], 409);
        }

        // Jika tidak diisi → otomatis ambil now()
        if (empty($validated['dikumpul_pada'])) {
            $validated['dikumpul_pada'] = now();
        }

        $pengumpulan = TugasPengumpulan::create($validated);

        return response()->json($pengumpulan, 201);
    }

    /**
     * Update data pengumpulan
     */
    public function update(Request $request, $id)
    {
        $pengumpulan = TugasPengumpulan::findOrFail($id);

        $validated = $request->validate([
            'tugas_id'      => 'sometimes|exists:tugas,id',
            'siswa_nisn'    => 'sometimes|string|max:20',
            'tipe'          => 'sometimes|in:file,link,teks',
            'isi'           => 'nullable|string',
            'file_path'     => 'nullable|string|max:255',
            'nilai'         => 'nullable|numeric|min:0',
            'feedback'      => 'nullable|string',
            'dinilai_oleh'  => 'sometimes|string|max:20',
            'dikumpul_pada' => 'nullable|date',
            'dinilai_pada'  => 'nullable|date',
        ]);

        $pengumpulan->update($validated);

        return response()->json($pengumpulan);
    }

    /**
     * Hapus pengumpulan tugas
     */
    public function destroy($id)
    {
        $pengumpulan = TugasPengumpulan::findOrFail($id);
        $pengumpulan->delete();

        return response()->json(['message' => 'Pengumpulan berhasil dihapus']);
    }
}
