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
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_nisn' => 'required|exists:rb_siswa,nisn',
        ]);

        try {
            // Cek apakah siswa sudah terdaftar di kelas ini
            $exists = KelasAnggota::where('kelas_id', $request->kelas_id)
                ->where('siswa_nisn', $request->siswa_nisn)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Siswa sudah terdaftar di kelas ini!');
            }

            KelasAnggota::create([
                'kelas_id' => $request->kelas_id,
                'siswa_nisn' => $request->siswa_nisn,
                'joined_at' => now(),
            ]);

            // Redirect ke halaman anggota kelas
            return redirect()->route('kelas.anggota', $request->kelas_id)
                ->with('success', 'Siswa berhasil ditambahkan ke kelas!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    public function show(KelasAnggota $kelasAnggotum)
    {
        return view('kelas_anggota.show', compact('kelasAnggotum'));
    }

    public function searchSiswa(Request $request)
    {
        $keyword = $request->get('keyword');
        
        if (strlen($keyword) < 3) {
            return response()->json([]);
        }
        
        // Search berdasarkan NISN atau Nama
        $siswa = RbSiswa::where(function($query) use ($keyword) {
                $query->where('nisn', 'like', '%' . $keyword . '%')
                    ->orWhere('nama', 'like', '%' . $keyword . '%');
            })
            ->select('nisn', 'nama', 'nipd')
            ->limit(10)
            ->get();
        
        return response()->json($siswa);
    }

    public function edit(KelasAnggota $kelasAnggotum)
    {
        $kelas = Kelas::all();
        $siswa = RbSiswa::all();
        return view('kelas_anggota.edit', compact('kelasAnggotum', 'kelas', 'siswa'));
    }

    public function update(Request $request, KelasAnggota $kelasAnggotum)
    {
        $request->validate([
            'siswa_nisn' => 'required|exists:rb_siswa,nisn',
        ]);

        try {
            $kelasAnggotum->update($request->all());
            
            // Redirect ke halaman anggota kelas
            return redirect()->route('kelas.anggota', $kelasAnggotum->kelas_id)
                ->with('success', 'Data anggota kelas berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

public function destroy($id)
{
    try {
        $kelasAnggotum = KelasAnggota::findOrFail($id);
        $kelasId = $kelasAnggotum->kelas_id;
        $namaSiswa = $kelasAnggotum->siswa->nama ?? 'Siswa';
        
        $kelasAnggotum->delete();
        
        return redirect()->route('kelas.anggota', ['id' => $kelasId])
            ->with('success', "Siswa '{$namaSiswa}' berhasil dikeluarkan dari kelas!");
            
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal mengeluarkan siswa: ' . $e->getMessage());
    }
}
}