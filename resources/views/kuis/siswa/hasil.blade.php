@extends('layouts.app')

@section('title', 'Hasil Kuis - ' . $attempt->kuis->judul)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <a href="{{ route('kuis.index', $attempt->kuis->kelas_id) }}" class="text-decoration-none text-secondary mb-3 d-inline-block">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kuis
                    </a>
                    
                    <h3 class="fw-bold mb-2">{{ $attempt->kuis->judul }}</h3>
                    <p class="text-muted mb-0">Hasil Kuis Anda</p>
                </div>
            </div>

            <!-- Hasil Nilai -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-trophy-fill text-warning" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-2">Nilai Anda</h2>
                    
                    <div class="display-1 fw-bold text-primary mb-3">
                        {{ $attempt->nilai_akhir }}
                    </div>
                    
                    @php
                        $nilai = $attempt->nilai_akhir;
                        if ($nilai >= 90) {
                            $predikat = 'Sempurna';
                            $icon = 'bi-star-fill';
                            $color = 'success';
                        } elseif ($nilai >= 80) {
                            $predikat = 'Sangat Baik';
                            $icon = 'bi-hand-thumbs-up-fill';
                            $color = 'primary';
                        } elseif ($nilai >= 70) {
                            $predikat = 'Baik';
                            $icon = 'bi-hand-thumbs-up';
                            $color = 'info';
                        } elseif ($nilai >= 60) {
                            $predikat = 'Cukup';
                            $icon = 'bi-dash-circle';
                            $color = 'warning';
                        } else {
                            $predikat = 'Perlu Ditingkatkan';
                            $icon = 'bi-exclamation-triangle';
                            $color = 'danger';
                        }
                    @endphp
                    
                    <h4 class="text-{{ $color }} mb-4">
                        <i class="bi {{ $icon }} me-2"></i>{{ $predikat }}
                    </h4>
                    
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-{{ $color }}" 
                             role="progressbar" 
                             style="width: {{ $attempt->nilai_akhir }}%;" 
                             aria-valuenow="{{ $attempt->nilai_akhir }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $attempt->nilai_akhir }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Pengerjaan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Detail Pengerjaan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-calendar-check text-primary fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Waktu Mulai</small>
                                    <strong>{{ \Carbon\Carbon::parse($attempt->mulai_pada)->format('d M Y, H:i') }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-calendar-check-fill text-success fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Waktu Selesai</small>
                                    <strong>{{ \Carbon\Carbon::parse($attempt->selesai_pada)->format('d M Y, H:i') }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-clock-fill text-warning fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Durasi Pengerjaan</small>
                                    <strong>{{ $attempt->durasi }} Menit</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-question-circle-fill text-info fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Jumlah Soal</small>
                                    <strong>{{ $attempt->kuis->jumlah_soal }} Soal</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Jawaban -->
            @php
                $totalSoal = $attempt->kuis->jumlah_soal;
                $benar = round(($attempt->nilai_akhir / 100) * $totalSoal);
                $salah = $totalSoal - $benar;
            @endphp
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">Statistik Jawaban</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="card bg-success bg-opacity-10 border-0">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                                    <h3 class="mb-0 text-success">{{ $benar }}</h3>
                                    <small class="text-success">Jawaban Benar</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-danger bg-opacity-10 border-0">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-x-circle-fill text-danger fs-1 mb-2"></i>
                                    <h3 class="mb-0 text-danger">{{ $salah }}</h3>
                                    <small class="text-danger">Jawaban Salah</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            Persentase Kebenaran: 
                            <strong class="text-primary">{{ number_format(($benar / $totalSoal) * 100, 1) }}%</strong>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Motivasi -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    @if($attempt->nilai_akhir >= 80)
                        <i class="bi bi-emoji-smile-fill text-success fs-1 mb-3"></i>
                        <h5 class="fw-semibold mb-2">Selamat! Hasil yang Luar Biasa!</h5>
                        <p class="text-muted mb-3">Pertahankan prestasi yang baik ini dan terus tingkatkan kemampuanmu.</p>
                    @elseif($attempt->nilai_akhir >= 60)
                        <i class="bi bi-emoji-neutral-fill text-warning fs-1 mb-3"></i>
                        <h5 class="fw-semibold mb-2">Hasil yang Cukup Baik!</h5>
                        <p class="text-muted mb-3">Terus belajar dan tingkatkan pemahamanmu untuk hasil yang lebih baik lagi.</p>
                    @else
                        <i class="bi bi-emoji-frown-fill text-danger fs-1 mb-3"></i>
                        <h5 class="fw-semibold mb-2">Tetap Semangat!</h5>
                        <p class="text-muted mb-3">Jangan menyerah! Pelajari kembali materinya dan konsultasi dengan guru jika ada yang belum dipahami.</p>
                    @endif
                    
                    <a href="{{ route('kuis.index', $attempt->kuis->kelas_id) }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kuis
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        font-weight: bold;
        font-size: 1rem;
        line-height: 30px;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection