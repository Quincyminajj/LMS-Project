<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasAnggota;
use Illuminate\Http\Request;

class SiswaKelasController extends Controller
{
  /**
   * Siswa bergabung ke kelas dengan kode kelas
   */
  public function joinKelas(Request $request)
  {
    $request->validate([
      'kode_kelas' => 'required|string',
    ]);

    $nisn = session('identifier');
    $kodeKelas = strtoupper(trim($request->kode_kelas));

    // Cari kelas berdasarkan kode
    $kelas = Kelas::where('kode_kelas', $kodeKelas)
      ->where('status', 'Aktif')
      ->first();

    if (!$kelas) {
      return back()->with('error', 'Kode kelas tidak ditemukan atau kelas tidak aktif.');
    }

    // Cek apakah siswa sudah terdaftar di kelas ini
    $sudahTerdaftar = KelasAnggota::where('kelas_id', $kelas->id)
      ->where('siswa_nisn', $nisn)  // ✅ PERBAIKI: Ubah dari siswa_nis ke siswa_nisn
      ->exists();

    if ($sudahTerdaftar) {
      return back()->with('info', 'Anda sudah terdaftar di kelas ini.');
    }

    // Daftarkan siswa ke kelas
    KelasAnggota::create([
      'kelas_id' => $kelas->id,
      'siswa_nisn' => $nisn,  // ✅ PERBAIKI: Ubah dari siswa_nis ke siswa_nisn
      'joined_at' => now(),
    ]);

    return back()->with('success', 'Berhasil bergabung ke kelas: ' . $kelas->nama_kelas);
  }


  /**
   * Siswa keluar dari kelas
   */
  public function leaveKelas($kelasId)
  {
    $nisn = session('identifier');

    $anggota = KelasAnggota::where('kelas_id', $kelasId)
      ->where('siswa_nisn', $nisn)  // ✅ PERBAIKI: Ubah dari siswa_nis ke siswa_nisn
      ->first();

    if (!$anggota) {
      return back()->with('error', 'Anda tidak terdaftar di kelas ini.');
    }

    $anggota->delete();

    return redirect()->route('dashboard')->with('success', 'Berhasil keluar dari kelas.');
  }
}
