<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\KuisAttempt;
use App\Models\KuisSoal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;

class KuisController extends Controller
{
    /**
     * DAFTAR KUIS DALAM KELAS
     */
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        $kuis = Kuis::where('kelas_id', $kelasId)
            ->withCount('attempts')
            ->latest()
            ->get();

        return view('kuis.index', compact('kelas', 'kuis'));
    }

    /**
     * FORM CREATE KUIS
     */
    public function create($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        return view('kuis.create', compact('kelas'));
    }

    /**
     * SIMPAN KUIS BARU BESERTA BANK SOAL
     */
    public function store(Request $request, $kelasId)
    {
        $request->validate([
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'durasi'          => 'required|integer|min:1',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'soal'            => 'required|array|min:10',
            'soal.*.pertanyaan'    => 'required|string',
            'soal.*.opsi_a'        => 'required|string',
            'soal.*.opsi_b'        => 'required|string',
            'soal.*.opsi_c'        => 'required|string',
            'soal.*.opsi_d'        => 'required|string',
            'soal.*.jawaban_benar' => 'required|in:A,B,C,D',
        ], [
            'soal.required' => 'Minimal 10 soal harus ditambahkan.',
            'soal.min' => 'Minimal 10 soal harus ditambahkan.',
        ]);

        DB::beginTransaction();
        try {
            // Simpan Kuis
            $kuis = Kuis::create([
                'kelas_id'        => $kelasId,
                'judul'           => $request->judul,
                'deskripsi'       => $request->deskripsi,
                'durasi'          => $request->durasi,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'jumlah_soal'     => 10,
            ]);

            // Simpan Bank Soal
            foreach ($request->soal as $soalData) {
                KuisSoal::create([
                    'kuis_id'        => $kuis->id,
                    'pertanyaan'     => $soalData['pertanyaan'],
                    'opsi_a'         => $soalData['opsi_a'],
                    'opsi_b'         => $soalData['opsi_b'],
                    'opsi_c'         => $soalData['opsi_c'],
                    'opsi_d'         => $soalData['opsi_d'],
                    'jawaban_benar'  => $soalData['jawaban_benar'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('kuis.index', $kelasId)
                ->with('success', 'Kuis berhasil dibuat dengan ' . count($request->soal) . ' soal.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat kuis: ' . $e->getMessage());
        }
    }

    /**
     * DETAIL KUIS (TABEL SISWA)
     */
    public function show($kuisId)
    {
        $kuis = Kuis::with([
            'kelas',
            'attempts.siswa'
        ])->findOrFail($kuisId);

        $attempts = KuisAttempt::where('kuis_id', $kuisId)
            ->with('siswa')
            ->orderBy('mulai_pada')
            ->get();

        return view('kuis.show', compact(
            'kuis',
            'attempts'
        ));
    }

    /**
     * HAPUS KUIS
     */
    public function destroy($kelas, $kuis)
    {
        $kuisData = Kuis::findOrFail($kuis);
        
        // Hapus semua soal terkait
        $kuisData->soal()->delete();
        
        // Hapus semua attempt terkait
        $kuisData->attempts()->delete();
        
        // Hapus kuis
        $kuisData->delete();

        return redirect()
            ->route('kuis.index', $kelas)
            ->with('success', 'Kuis berhasil dihapus.');
    }

    /**
     * EXPORT PDF DAFTAR NILAI SISWA
     */
    public function exportPdf($kuisId)
    {
        $kuis = Kuis::with('kelas')->findOrFail($kuisId);

        $attempts = KuisAttempt::where('kuis_id', $kuisId)
            ->with('siswa')
            ->orderBy('mulai_pada')
            ->get();

        $pdf = PDF::loadView('kuis.pdf', [
            'kuis'     => $kuis,
            'attempts' => $attempts
        ]);

        return $pdf->download(
            'hasil-kuis-' . \Str::slug($kuis->judul) . '.pdf'
        );
    }
}