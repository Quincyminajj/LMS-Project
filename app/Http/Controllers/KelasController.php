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
        $kelas = Kelas::with('guru')->where('status', '!=', 'Arsip')->get();
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
            'nama_kelas' => 'required',
            'deskripsi'  => 'required',
        ]);

        // Generate kode kelas otomatis
        $lastKelas = Kelas::orderBy('id', 'desc')->first();

        if (!$lastKelas) {
            $newCode = 'KLS-001';
        } else {
            $lastNumber = intval(substr($lastKelas->kode_kelas, 4));
            $newCode = 'KLS-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        Kelas::create([
            'kode_kelas' => $newCode,
            'nama_kelas' => $request->nama_kelas,
            'deskripsi'  => $request->deskripsi,
            'guru_nip'   => session('identifier'),
            'status'     => 'Aktif'
        ]);

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

    public function archive($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->status = 'Arsip';
        $kelas->save();

        return back()->with('success', 'Kelas berhasil diarsipkan.');
    }

    public function restore($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->update(['status' => 'Aktif']);

        return redirect()->route('kelas.arsip')->with('success', 'Kelas berhasil dipulihkan!');
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
