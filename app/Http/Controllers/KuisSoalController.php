<?php

namespace App\Http\Controllers;

use App\Models\Kuis;
use App\Models\KuisSoal;
use Illuminate\Http\Request;

class KuisSoalController extends Controller
{
    /**
     * DAFTAR BANK SOAL
     */
    public function index($kuisId)
    {
        $kuis = Kuis::with('soal')->findOrFail($kuisId);

        return view('kuis.soal.index', compact('kuis'));
    }

    /**
     * SIMPAN SOAL BARU
     */
    public function store(Request $request, $kuisId)
    {
        $request->validate([
            'pertanyaan'     => 'required|string',
            'opsi_a'         => 'required|string',
            'opsi_b'         => 'required|string',
            'opsi_c'         => 'required|string',
            'opsi_d'         => 'required|string',
            'jawaban_benar'  => 'required|in:A,B,C,D',
        ]);

        KuisSoal::create([
            'kuis_id'        => $kuisId,
            'pertanyaan'     => $request->pertanyaan,
            'opsi_a'         => $request->opsi_a,
            'opsi_b'         => $request->opsi_b,
            'opsi_c'         => $request->opsi_c,
            'opsi_d'         => $request->opsi_d,
            'jawaban_benar'  => $request->jawaban_benar,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }
}
