@extends('layouts.app')

@section('title', 'Preview Kuis - ' . $kuis->judul)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <a href="{{ route('kuis.index', $kuis->kelas_id) }}" class="text-decoration-none text-secondary mb-3 d-inline-block">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kuis
                    </a>
                    
                    <h3 class="fw-bold mb-2">{{ $kuis->judul }}</h3>
                    
                    @if($kuis->deskripsi)
                        <p class="text-muted mb-0">{{ $kuis->deskripsi }}</p>
                    @endif
                </div>
            </div>

            @if($attempt)
                <!-- Sudah Mengerjakan -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            @if($attempt->selesai_pada)
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                <h4 class="mt-3 mb-2">Kuis Sudah Selesai</h4>
                                <p class="text-muted">Anda sudah menyelesaikan kuis ini.</p>
                            @else
                                <i class="bi bi-hourglass-split text-warning" style="font-size: 4rem;"></i>
                                <h4 class="mt-3 mb-2">Kuis Sedang Berlangsung</h4>
                                <p class="text-muted">Anda sudah memulai kuis ini.</p>
                            @endif
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-3">
                                        <small class="text-muted d-block">Mulai Pada</small>
                                        <strong>{{ \Carbon\Carbon::parse($attempt->mulai_pada)->format('d M Y, H:i') }}</strong>
                                    </div>
                                </div>
                            </div>
                            @if($attempt->selesai_pada)
                                <div class="col-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body py-3">
                                            <small class="text-muted d-block">Nilai Akhir</small>
                                            <strong class="fs-4 text-primary">{{ $attempt->nilai_akhir }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($attempt->selesai_pada)
                            <a href="{{ route('kuis.siswa.hasil', $kuis->id) }}" class="btn btn-primary">
                                <i class="bi bi-eye"></i> Lihat Hasil Detail
                            </a>
                        @else
                            <a href="{{ route('kuis.siswa.kerjakan', $kuis->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Lanjutkan Mengerjakan
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <!-- Belum Mengerjakan - Tampilkan Info Kuis -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Informasi Kuis</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                        <i class="bi bi-clock text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Durasi</small>
                                        <strong>{{ $kuis->durasi }} Menit</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                        <i class="bi bi-question-circle text-success fs-4"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Jumlah Soal</small>
                                        <strong>{{ $kuis->jumlah_soal }} Soal</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                        <i class="bi bi-list-check text-info fs-4"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Tipe Soal</small>
                                        <strong>Pilihan Ganda</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waktu Kuis -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Jadwal Kuis</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-calendar-check text-success me-3 fs-5"></i>
                                    <div>
                                        <small class="text-muted d-block">Mulai</small>
                                        <strong>{{ \Carbon\Carbon::parse($kuis->tanggal_mulai)->format('d M Y, H:i') }}</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-calendar-x text-danger me-3 fs-5"></i>
                                    <div>
                                        <small class="text-muted d-block">Berakhir</small>
                                        <strong>{{ \Carbon\Carbon::parse($kuis->tanggal_selesai)->format('d M Y, H:i') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Petunjuk -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">
                            <i class="bi bi-info-circle text-primary"></i> Petunjuk Pengerjaan
                        </h5>
                        
                        <ol class="mb-0">
                            <li class="mb-2">Pastikan koneksi internet Anda stabil</li>
                            <li class="mb-2">Kuis hanya dapat dikerjakan <strong>1 kali</strong></li>
                            <li class="mb-2">Waktu pengerjaan adalah <strong>{{ $kuis->durasi }} menit</strong></li>
                            <li class="mb-2">Pilih salah satu jawaban yang paling tepat</li>
                            <li class="mb-2">Pastikan semua soal sudah dijawab sebelum submit</li>
                            <li class="mb-0">Nilai akan muncul otomatis setelah kuis selesai</li>
                        </ol>
                    </div>
                </div>

                <!-- Tombol Mulai -->
                @php
                    $now = now();
                    $belumMulai = $now->lt($kuis->tanggal_mulai);
                    $sudahSelesai = $now->gt($kuis->tanggal_selesai);
                @endphp

                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-4">
                        @if($belumMulai)
                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-hourglass-split"></i> Kuis belum dimulai. 
                                Mulai pada: <strong>{{ \Carbon\Carbon::parse($kuis->tanggal_mulai)->format('d M Y, H:i') }}</strong>
                            </div>
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-lock"></i> Kuis Belum Tersedia
                            </button>
                        @elseif($sudahSelesai)
                            <div class="alert alert-secondary mb-3">
                                <i class="bi bi-clock-history"></i> Kuis telah berakhir pada: 
                                <strong>{{ \Carbon\Carbon::parse($kuis->tanggal_selesai)->format('d M Y, H:i') }}</strong>
                            </div>
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-x-circle"></i> Kuis Sudah Ditutup
                            </button>
                        @else
                            <p class="text-muted mb-3">
                                Dengan menekan tombol di bawah, kuis akan dimulai dan waktu mulai berjalan.
                            </p>
                            
                            <form action="{{ route('kuis.siswa.start', $kuis->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="bi bi-play-circle"></i> Mulai Kuis Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection