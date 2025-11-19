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

    public function show($id)
    {
        $tugas = Tugas::with('kelas')->findOrFail($id);
        return response()->json($tugas);
    }

    public function store(Request $request, $kelas)
    {
        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'required',
            'deadline' => 'required'
        ]);

        Tugas::create([
            'kelas_id' => $kelas,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
            'nilai_maksimal' => $request->nilai_maksimal ?? 100,
            'created_by'     => session('identifier'),
        ]);

        return redirect()->route('tugas.index', $kelas)->with('success', 'Tugas berhasil dibuat!');
    }

    public function update(Request $request, $kelasId, $id)
    {
        $tugas = Tugas::findOrFail($id);

        $tugas->update([
            'judul'          => $request->judul,
            'deskripsi'      => $request->deskripsi,
            'nilai_maksimal' => $request->nilai_maksimal,
            'deadline'       => $request->deadline,
        ]);

        return redirect()->route('tugas.index', $kelasId)->with('success', 'Tugas berhasil diupdate');
    }


    public function destroy($kelasId, $id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();

        return redirect()->route('tugas.index', $kelasId)->with('success', 'Tugas berhasil dihapus');
    }

}
