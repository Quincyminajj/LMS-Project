<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\RbGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('guru')->get();
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        // Ambil kelas terakhir
        $lastKelas = Kelas::orderBy('id', 'desc')->first();

        // Generate kode baru
        if (!$lastKelas) {
            $newCode = 'KLS-001';
        } else {
            $lastNumber = intval(substr($lastKelas->kode_kelas, 4));
            $newCode = 'KLS-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        // Kirim ke view
        return view('kelas.guru.create', compact('newCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|unique:kelas',
            'nama_kelas' => 'required',
        ]);

        // Tambahkan guru_nip otomatis dari session
        $data = $request->only(['kode_kelas', 'nama_kelas', 'deskripsi']);
        $data['guru_nip'] = session('identifier');

        Kelas::create($data);

        return redirect()->route('dashboard')->with('success', 'Kelas berhasil dibuat.');
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
