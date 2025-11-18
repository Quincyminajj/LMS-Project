<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;

class TugasController extends Controller
{

    public function index(Request $request, $kelasId)
    {
        // ambil data
        $kelas = \App\Models\Kelas::with('tugas')->findOrFail($kelasId);
        $tugas = $kelas->tugas->sortBy('deadline');

        // jika client meminta JSON (mis. API) kembalikan json
        if ($request->wantsJson()) {
            return response()->json($tugas);
        }

        // untuk web: tampilkan blade
        return view('tugas.index', compact('kelas', 'tugas'));
    }


    /**
     * Detail tugas
     */
    public function show($id)
    {
        $tugas = Tugas::with('kelas')->findOrFail($id);
        return response()->json($tugas);
    }

    /**
     * Buat tugas baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id'        => 'required|exists:kelas,id',
            'judul'           => 'required|string|max:255',
            'nilai_maksimal'  => 'nullable|numeric|min:0',
            'deadline'        => 'required|date',
            'deskripsi'       => 'nullable|string',
            'file_contoh'     => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
            'created_by'      => 'required|string|max:20',
        ]);

        // Upload file soal
        if ($request->hasFile('file_contoh')) {
            $validated['file_contoh'] = $request->file('file_contoh')
                                                ->store('tugas', 'public');
        }

        $tugas = Tugas::create($validated);

        return response()->json($tugas, 201);
    }

    /**
     * Update tugas
     */
    public function update(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);

        $validated = $request->validate([
            'kelas_id'        => 'sometimes|exists:kelas,id',
            'judul'           => 'sometimes|string|max:255',
            'nilai_maksimal'  => 'sometimes|numeric|min:0',
            'deadline'        => 'sometimes|date',
            'deskripsi'       => 'nullable|string',
            'file_contoh'     => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
            'created_by'      => 'sometimes|string|max:20',
        ]);

        // Jika ganti file
        if ($request->hasFile('file_contoh')) {
            $validated['file_contoh'] = $request->file('file_contoh')
                                                ->store('tugas', 'public');
        }

        $tugas->update($validated);

        return response()->json($tugas);
    }

    /**
     * Hapus tugas
     */
    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);

        $tugas->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus']);
    }
}
