<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\RbGuru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('guru')->get();
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $guru = RbGuru::all();
        return view('kelas.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|unique:kelas',
            'nama_kelas' => 'required',
        ]);

        Kelas::create($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(Kelas $kela)
    {
        return view('kelas.show', ['kelas' => $kela]);
    }

    public function edit(Kelas $kela)
    {
        $guru = RbGuru::all();
        return view('kelas.edit', compact('kela', 'guru'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $kela->update($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
