<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\RbGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KelasController extends Controller
{
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

    /**
     * Simpan kelas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi'  => 'required|string',
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

        return redirect()->route('dashboard')->with('success', 'Kelas berhasil dibuat!');
    }

    /**
     * Tampilkan detail kelas
     */
    public function show(Kelas $kela)
    {
        // Load relasi yang diperlukan
        $kelas = $kela->load(['guru', 'konten', 'tugas', 'anggota']);

        // Cek apakah user adalah guru atau siswa yang terdaftar
        $userRole = session('role');
        $identifier = session('identifier');

        if ($userRole === 'guru' && $kelas->guru_nip === $identifier) {
            return view('kelas.guru.show', compact('kelas'));
        } elseif ($userRole === 'siswa') {
            // Cek apakah siswa terdaftar di kelas ini
            $isMember = $kelas->anggota()->where('siswa_nisn', $identifier)->exists();

            if ($isMember) {
                return view('kelas.siswa.show', compact('kelas'));
            }
        }

        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
    }

    /**
     * Form edit kelas
     */
    public function edit(Kelas $kela)
    {
        // Pastikan hanya guru yang bersangkutan yang bisa edit
        if ($kela->guru_nip !== session('identifier')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $guru = RbGuru::all();
        return view('kelas.edit', compact('kela', 'guru'));
    }

    /**
     * Update kelas
     */
    public function update(Request $request, Kelas $kela)
    {
        // Pastikan hanya guru yang bersangkutan yang bisa update
        if ($kela->guru_nip !== session('identifier')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        $kela->update([
            'nama_kelas' => $request->nama_kelas,
            'deskripsi'  => $request->deskripsi,
        ]);

        return back()->with('success', 'Kelas berhasil diperbarui!');
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

        // Filter pencarian
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

        // Pastikan hanya guru yang bersangkutan
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

        // Pastikan hanya guru yang bersangkutan
        if ($kelas->guru_nip !== session('identifier')) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $kelas->update(['status' => 'Aktif']);

        return redirect()->route('kelas.arsip')->with('success', 'Kelas berhasil dipulihkan!');
    }

    /**
     * Hapus kelas permanen
     * FIXED: Redirect ke dashboard bukan back()
     */
    public function destroy($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            // Pastikan hanya guru yang bersangkutan
            if ($kelas->guru_nip !== session('identifier')) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
            }

            // Simpan nama kelas untuk pesan success
            $namaKelas = $kelas->nama_kelas;

            // Hapus kelas (akan cascade delete semua data terkait)
            $kelas->delete();

            // PERBAIKAN: Redirect ke dashboard atau arsip (tergantung dari mana hapus dipanggil)
            // Jika dari arsip, redirect ke arsip. Jika dari detail kelas, redirect ke dashboard
            if (request()->header('referer') && strpos(request()->header('referer'), 'kelas-arsip') !== false) {
                return redirect()->route('kelas.arsip')->with('success', "Kelas '{$namaKelas}' berhasil dihapus permanen!");
            }

            return redirect()->route('dashboard')->with('success', "Kelas '{$namaKelas}' berhasil dihapus permanen!");
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }
}
