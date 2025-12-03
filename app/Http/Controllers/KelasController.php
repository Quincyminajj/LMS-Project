<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\RbGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    /**
     * Generate kode kelas unik dan kompleks
     * Format: KLS-YYMM-XXXXX
     * Contoh: KLS-2512-A3F7B
     */
    private function generateKodeKelas()
    {
        do {
            // Tahun (2 digit) + Bulan (2 digit)
            $yearMonth = date('ym');
            
            // Generate random alphanumeric 5 karakter
            $random = strtoupper(Str::random(5));
            
            // Gabungkan
            $kodeKelas = "KLS-{$yearMonth}-{$random}";
            
            // Cek keunikan
            $exists = Kelas::where('kode_kelas', $kodeKelas)->exists();
            
        } while ($exists);
        
        return $kodeKelas;
    }

    /**
     * Tampilkan semua kelas (tidak termasuk arsip)
     */
    public function index()
    {
        $kelas = Kelas::with('guru')->where('status', '!=', 'Arsip')->get();
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Form buat kelas baru
     */
    public function create()
    {
        // Generate kode baru
        $newCode = $this->generateKodeKelas();

        return view('kelas.guru.create', compact('newCode'));
    }

    /**
     * Simpan kelas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi'  => 'required|string',
        ]);

        // Generate kode kelas otomatis yang kompleks
        $newCode = $this->generateKodeKelas();

        Kelas::create([
            'kode_kelas' => $newCode,
            'nama_kelas' => $request->nama_kelas,
            'deskripsi'  => $request->deskripsi,
            'guru_nip'   => session('identifier'),
            'status'     => 'Aktif'
        ]);

        return redirect()->route('dashboard')->with('success', 'Kelas berhasil dibuat dengan kode: ' . $newCode);
    }

    /**
     * Tampilkan detail kelas
     */
    public function show($id)
    {
        $kelas = Kelas::with(['guru', 'konten', 'tugas', 'anggota'])->findOrFail($id);

        $userRole = session('role');
        $identifier = session('identifier');

        // GURU
        if ($userRole === 'guru' && $kelas->guru_nip === $identifier) {
            return view('kelas.guru.show', compact('kelas'));
        }

        // SISWA
        if ($userRole === 'siswa') {
            $isMember = $kelas->anggota()->where('siswa_nisn', $identifier)->exists();

            if ($isMember) {
                $tugasDikumpulkan = \App\Models\TugasPengumpulan::where('siswa_nisn', $identifier)
                    ->whereIn('tugas_id', $kelas->tugas->pluck('id'))
                    ->pluck('tugas_id')
                    ->toArray();

                return view('kelas.siswa.show', compact('kelas', 'tugasDikumpulkan'));
            }
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
    }

    /**
     * Form edit kelas
     */
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->guru_nip !== session('identifier')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $guru = RbGuru::all();
        return view('kelas.edit', compact('kelas', 'guru'));
    }

    /**
     * Update kelas
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->guru_nip !== session('identifier')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'deskripsi'  => $request->deskripsi,
        ]);

        return redirect()->route('kelas.show', $kelas->id)->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Tampilkan kelas yang diarsipkan (Guru Only)
     */
    public function arsip(Request $request)
    {
        $nip = session('identifier');

        $query = Kelas::where('guru_nip', $nip)
            ->where('status', 'Arsip')
            ->with(['guru', 'anggota', 'tugas']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('kode_kelas', 'like', "%{$search}%");
            });
        }

        $kelasArsip = $query->orderBy('updated_at', 'desc')->paginate(9);

        return view('kelas.arsip', compact('kelasArsip'));
    }

    /**
     * Arsipkan kelas
     */
    public function archive($id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->guru_nip !== session('identifier')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $kelas->update(['status' => 'Arsip']);

        return redirect()->route('dashboard')->with('success', 'Kelas berhasil diarsipkan!');
    }

    /**
     * Pulihkan kelas dari arsip
     */
    public function restore($id)
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->guru_nip !== session('identifier')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $kelas->update(['status' => 'Aktif']);

        return redirect()->route('kelas.arsip')->with('success', 'Kelas berhasil dipulihkan!');
    }

    /**
     * Hapus kelas permanen
     */
    public function destroy($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            if ($kelas->guru_nip !== session('identifier')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
            }

            $namaKelas = $kelas->nama_kelas;
            $kelas->delete();

            if (request()->header('referer') && strpos(request()->header('referer'), 'kelas-arsip') !== false) {
                return redirect()->route('kelas.arsip')->with('success', "Kelas '{$namaKelas}' berhasil dihapus permanen!");
            }

            return redirect()->route('dashboard')->with('success', "Kelas '{$namaKelas}' berhasil dihapus permanen!");
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }

    /**
 * Tampilkan daftar siswa dalam kelas
 */
public function anggota($id)
{
    $kelas = Kelas::with(['guru', 'anggota.siswa'])->findOrFail($id);
    
    $userRole = session('role');
    $identifier = session('identifier');

    // GURU - hanya guru pemilik kelas
    if ($userRole === 'guru' && $kelas->guru_nip === $identifier) {
        return view('kelas.anggota.show', compact('kelas'));
    }

    // SISWA - hanya siswa yang terdaftar di kelas
    if ($userRole === 'siswa') {
        $isMember = $kelas->anggota()->where('siswa_nisn', $identifier)->exists();
        
        if ($isMember) {
            return view('kelas.anggota.show', compact('kelas'));
        }
    }

    return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
}
}