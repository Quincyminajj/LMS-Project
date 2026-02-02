<?php

namespace App\Http\Controllers;

use App\Models\Kuis;
use App\Models\KuisAttempt;
use App\Models\KuisSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KuisSiswaController extends Controller
{
    /**
     * PREVIEW KUIS (UNTUK SISWA)
     */
    public function show($kuisId)
    {
        $siswaNisn = session('identifier');
        
        if (!$siswaNisn) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kuis = Kuis::findOrFail($kuisId);

        // Cek apakah siswa sudah pernah mengerjakan kuis ini
        $attempt = KuisAttempt::where('kuis_id', $kuisId)
            ->where('siswa_nisn', $siswaNisn)
            ->first();

        return view('kuis.siswa.start', compact('kuis', 'attempt'));
    }

    /**
     * MULAI KUIS (1X)
     */
    public function start($kuisId)
    {
        $siswaNisn = session('identifier');
        
        if (!$siswaNisn) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah sudah pernah mengerjakan
        $exists = KuisAttempt::where('kuis_id', $kuisId)
            ->where('siswa_nisn', $siswaNisn)
            ->exists();

        if ($exists) {
            return redirect()->route('kuis.siswa.hasil', $kuisId)
                ->with('warning', 'Anda sudah pernah mengerjakan kuis ini.');
        }

        // Buat attempt baru
        KuisAttempt::create([
            'kuis_id'    => $kuisId,
            'siswa_nisn' => $siswaNisn,
            'mulai_pada' => now(),
        ]);

        return redirect()->route('kuis.siswa.kerjakan', $kuisId);
    }

    /**
     * HALAMAN KERJAKAN SOAL
     */
    public function kerjakan($kuisId)
    {
        $siswaNisn = session('identifier');
        
        if (!$siswaNisn) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil attempt siswa
        $attempt = KuisAttempt::where('kuis_id', $kuisId)
            ->where('siswa_nisn', $siswaNisn)
            ->firstOrFail();

        // Jika sudah selesai, redirect ke hasil
        if ($attempt->selesai_pada) {
            return redirect()->route('kuis.siswa.hasil', $kuisId)
                ->with('info', 'Anda sudah menyelesaikan kuis ini.');
        }

        $kuis = Kuis::findOrFail($kuisId);

        // Ambil soal secara random
        $soal = KuisSoal::where('kuis_id', $kuisId)
            ->inRandomOrder()
            ->limit($kuis->jumlah_soal)
            ->get();

        // Cek apakah ada soal
        if ($soal->isEmpty()) {
            return redirect()->route('kuis.index', $kuis->kelas_id)
                ->with('error', 'Kuis ini belum memiliki soal.');
        }

        return view('kuis.siswa.kerjakan', compact(
            'kuis',
            'attempt',
            'soal'
        ));
    }

    /**
     * SUBMIT JAWABAN
     */
    public function submit(Request $request, $kuisId)
    {
        $siswaNisn = session('identifier');
        
        if (!$siswaNisn) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil attempt siswa
        $attempt = KuisAttempt::where('kuis_id', $kuisId)
            ->where('siswa_nisn', $siswaNisn)
            ->firstOrFail();

        // Cek apakah sudah selesai sebelumnya
        if ($attempt->selesai_pada) {
            return redirect()->route('kuis.siswa.hasil', $kuisId)
                ->with('warning', 'Anda sudah menyelesaikan kuis ini sebelumnya.');
        }

        $kuis = Kuis::findOrFail($kuisId);
        
        // Ambil semua soal untuk kuis ini
        $soal = KuisSoal::where('kuis_id', $kuisId)->get();

        if ($soal->isEmpty()) {
            return redirect()->route('kuis.index', $kuis->kelas_id)
                ->with('error', 'Kuis ini tidak memiliki soal.');
        }

        // Hitung jawaban yang benar
        $benar = 0;
        $totalSoal = $soal->count();

        foreach ($soal as $item) {
            if (
                isset($request->jawaban[$item->id]) &&
                $request->jawaban[$item->id] === $item->jawaban_benar
            ) {
                $benar++;
            }
        }

        // Hitung nilai (0-100)
        $nilai = ($benar / $totalSoal) * 100;

        // Hitung durasi pengerjaan
        $selesai = now();
        $durasi  = Carbon::parse($attempt->mulai_pada)->diffInMinutes($selesai);

        // Update attempt dengan hasil
        $attempt->update([
            'selesai_pada' => $selesai,
            'durasi'       => $durasi,
            'nilai_akhir'  => round($nilai, 2),
        ]);

        return redirect()->route('kuis.siswa.hasil', $kuisId)
            ->with('success', 'Kuis berhasil diselesaikan!');
    }

    /**
     * HASIL KUIS SISWA
     */
    public function hasil($kuisId)
    {
        $siswaNisn = session('identifier');
        
        if (!$siswaNisn) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil attempt siswa
        $attempt = KuisAttempt::with('kuis')
            ->where('kuis_id', $kuisId)
            ->where('siswa_nisn', $siswaNisn)
            ->firstOrFail();

        return view('kuis.siswa.hasil', compact('attempt'));
    }
}