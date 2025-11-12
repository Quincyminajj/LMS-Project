<?php

namespace App\Http\Controllers;

use App\Models\KelasAnggota;
use App\Models\Kelas;
use App\Models\RbSiswa;
use Illuminate\Http\Request;

class KelasAnggotaController extends Controller
{
    public function index()
    {
        $anggota = KelasAnggota::with(['kelas', 'siswa'])->get();
        return view('kelas_anggota.index', compact('anggota'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $siswa = RbSiswa::all();
        return view('kelas_anggota.create', compact('kelas', 'siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'siswa_nisn' => 'required',
        ]);

        KelasAnggota::create($request->all());
        return redirect()->route('kelas_anggota.index')->with('success', 'Anggota kelas berhasil ditambahkan.');
    }

    public function show(KelasAnggota $kelasAnggotum)
    {
        return view('kelas_anggota.show', compact('kelasAnggotum'));
    }

    public function edit(KelasAnggota $kelasAnggotum)
    {
        $kelas = Kelas::all();
        $siswa = RbSiswa::all();
        return view('kelas_anggota.edit', compact('kelasAnggotum', 'kelas', 'siswa'));
    }

    public function update(Request $request, KelasAnggota $kelasAnggotum)
    {
        $kelasAnggotum->update($request->all());
        return redirect()->route('kelas_anggota.index')->with('success', 'Data anggota kelas berhasil diperbarui.');
    }

    public function destroy(KelasAnggota $kelasAnggotum)
    {
        $kelasAnggotum->delete();
        return redirect()->route('kelas_anggota.index')->with('success', 'Anggota kelas berhasil dihapus.');
    }
}
