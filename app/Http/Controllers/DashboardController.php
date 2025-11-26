<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasAnggota;
use App\Models\Tugas;
use App\Models\TugasPengumpulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    $userRole = session('role');

    if ($userRole === 'guru') {
      return $this->dashboardGuru();
    } elseif ($userRole === 'siswa') {
      return $this->dashboardSiswa();
    }

    return redirect()->route('login');
  }

  private function dashboardGuru()
  {
    $nip = session('identifier');

    // Ambil kelas yang diajar guru (hanya yang aktif)
    $kelasAktif = Kelas::where('guru_nip', $nip)
      ->where('status', 'Aktif')
      ->with('anggota')
      ->get();

    // Total kelas aktif
    $totalKelasAktif = $kelasAktif->count();

    // Total siswa dari semua kelas
    $totalSiswa = KelasAnggota::whereIn('kelas_id', $kelasAktif->pluck('id'))->count();

    // Kelas yang diarsipkan
    $kelasDiarsipkan = Kelas::where('guru_nip', $nip)
      ->where('status', 'Arsip')
      ->count();

    return view('dashboard.guru', compact(
      'kelasAktif',
      'totalKelasAktif',
      'totalSiswa',
      'kelasDiarsipkan'
    ));
  }

  private function dashboardSiswa()
  {
    $nisn = session('identifier'); // ✅ Ubah kembali ke $nisn

    // Ambil kelas yang diikuti siswa
    $kelasYangDiikuti = KelasAnggota::where('siswa_nisn', $nisn) // ✅ Ubah ke siswa_nisn
      ->with(['kelas' => function ($query) {
        $query->where('status', 'Aktif')
          ->with(['guru', 'tugas']);
      }])
      ->get()
      ->pluck('kelas')
      ->filter(); // Remove null values

    // Total kelas yang diikuti
    $totalKelasDiikuti = $kelasYangDiikuti->count();

    // Ambil semua tugas dari kelas yang diikuti
    $kelaIds = $kelasYangDiikuti->pluck('id');

    $semuaTugas = Tugas::whereIn('kelas_id', $kelaIds)->get();
    $totalTugas = $semuaTugas->count();

    // Tugas yang sudah dikumpulkan
    $tugasDikumpulkan = TugasPengumpulan::where('siswa_nisn', $nisn) // ✅ Ubah ke siswa_nisn
      ->whereIn('tugas_id', $semuaTugas->pluck('id'))
      ->pluck('tugas_id');

    $tugasSelesai = $tugasDikumpulkan->count();
    $tugasPending = $totalTugas - $tugasSelesai;

    // Hitung progress tugas per kelas
    $kelasWithProgress = $kelasYangDiikuti->map(function ($kelas) use ($nisn) { // ✅ Ubah ke $nisn
      $totalTugasKelas = $kelas->tugas->count();

      if ($totalTugasKelas > 0) {
        $tugasDikumpulkanKelas = TugasPengumpulan::where('siswa_nisn', $nisn) // ✅ Ubah ke siswa_nisn
          ->whereIn('tugas_id', $kelas->tugas->pluck('id'))
          ->count();

        $kelas->progress_tugas = round(($tugasDikumpulkanKelas / $totalTugasKelas) * 100);
        $kelas->tugas_selesai = $tugasDikumpulkanKelas;
        $kelas->total_tugas = $totalTugasKelas;
      } else {
        $kelas->progress_tugas = 0;
        $kelas->tugas_selesai = 0;
        $kelas->total_tugas = 0;
      }

      return $kelas;
    });

    return view('dashboard.siswa', compact(
      'totalKelasDiikuti',
      'tugasSelesai',
      'tugasPending',
      'totalTugas',
      'kelasWithProgress'
    ));
  }
}
