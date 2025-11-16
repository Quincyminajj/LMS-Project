<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;

class TugasController extends Controller
{
    
    public function index()
    {
        $tugas = Tugas::with('kelas')->orderBy('deadline', 'asc')->get();
        return response()->json($tugas);
    }

    
    public function show($id)
    {
        $tugas = Tugas::with('kelas')->findOrFail($id);
        return response()->json($tugas);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul' => 'required|string|max:255',
            'nilai_maksimal' => 'nullable|numeric|min:0',
            'deadline' => 'required|date',
            'deskripsi' => 'nullable|string',
            'file_contoh' => 'nullable|string|max:255',
            'created_by' => 'required|string|max:20',
        ]);

        $tugas = Tugas::create($validated);

        return response()->json($tugas, 201);
    }

    
    public function update(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);

        $validated = $request->validate([
            'kelas_id' => 'sometimes|exists:kelas,id',
            'judul' => 'sometimes|string|max:255',
            'nilai_maksimal' => 'sometimes|numeric|min:0',
            'deadline' => 'sometimes|date',
            'deskripsi' => 'nullable|string',
            'file_contoh' => 'nullable|string|max:255',
            'created_by' => 'sometimes|string|max:20',
        ]);

        $tugas->update($validated);

        return response()->json($tugas);
    }

    
    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();

        return response()->json(['message' => 'Tugas berhasil dihapus']);
    }
}
