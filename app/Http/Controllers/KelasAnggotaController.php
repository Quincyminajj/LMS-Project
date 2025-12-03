<?php

namespace App\Http\Controllers;

use App\Models\KelasAnggota;
use App\Models\Kelas;
use App\Models\RbSiswa;
use Illuminate\Http\Request;

class KelasAnggotaController extends Controller
{
   public function index($kelasId, Request $request)
{
    $kelas = Kelas::with(['guru', 'anggota.siswa'])->findOrFail($kelasId);

    $query = KelasAnggota::where('kelas_id', $kelasId)
        ->with('siswa');

    // Filter pencarian
    if ($request->has('search') && $request->search) {
        $search = $request->search;

        $query->whereHas('siswa', function ($q) use ($search) {
            $q->where('nama', 'like', "%$search%")
              ->orWhere('nisn', 'like', "%$search%")
              ->orWhere('nipd', 'like', "%$search%");
        });
    }

    // Ambil semua hasil (atau bisa pagination jika banyak)
    $anggota = $query->orderBy('joined_at', 'desc')->get();

    // Cek role user
    $userRole = session('role');
    $identifier = session('identifier');

    // GURU - hanya guru pemilik kelas
    if ($userRole === 'guru' && $kelas->guru_nip === $identifier) {
        return view('kelas.anggota.index', compact('kelas', 'anggota'));
    }

    // SISWA - hanya siswa yang terdaftar di kelas
    if ($userRole === 'siswa') {
        $isMember = $kelas->anggota()->where('siswa_nisn', $identifier)->exists();
        
        if ($isMember) {
            return view('kelas.anggota.index', compact('kelas', 'anggota'));
        }
    }

    return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
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

    public function show($kelasId, KelasAnggota $anggota)
    {
        $anggota->load(['siswa', 'kelas']);

        return view('kelas.anggota.show', [
            'anggota' => $anggota,
            'siswa' => $anggota->siswa,
            'kelas' => $anggota->kelas
        ]);
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