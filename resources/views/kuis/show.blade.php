@extends('layouts.app')

@section('title', 'Detail Kuis - ' . $kuis->judul)

@section('content')
    <div class="container py-4">

        <!-- Header -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <a href="{{ route('kuis.index', $kuis->kelas_id) }}" class="text-decoration-none text-secondary mb-2 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kuis
                </a>
                <h3 class="fw-bold mb-2">{{ $kuis->judul }}</h3>
                <p class="text-secondary mb-0">{{ $kuis->kelas->nama_kelas }} • {{ $kuis->kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}</p>
                
                @if($kuis->deskripsi)
                    <p class="text-muted mt-2 mb-0">{{ $kuis->deskripsi }}</p>
                @endif
            </div>
        </div>

        <!-- Info Kuis -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-event text-primary fs-3"></i>
                        <h6 class="mt-2 mb-1 text-muted small">Tanggal Mulai</h6>
                        <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($kuis->tanggal_mulai)->format('d M Y') }}</p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($kuis->tanggal_mulai)->format('H:i') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-x text-danger fs-3"></i>
                        <h6 class="mt-2 mb-1 text-muted small">Tanggal Selesai</h6>
                        <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($kuis->tanggal_selesai)->format('d M Y') }}</p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($kuis->tanggal_selesai)->format('H:i') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock text-success fs-3"></i>
                        <h6 class="mt-2 mb-1 text-muted small">Durasi</h6>
                        <p class="mb-0 fw-semibold fs-4">{{ $kuis->durasi }}</p>
                        <small class="text-muted">menit</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Ringkas -->
        <div class="row g-3 mb-4">
            @php
                $totalSiswa = $attempts->count();
                $rataRata = $totalSiswa > 0 ? $attempts->avg('nilai_akhir') : 0;
                $nilaiTertinggi = $totalSiswa > 0 ? $attempts->max('nilai_akhir') : 0;
                $nilaiTerendah = $totalSiswa > 0 ? $attempts->min('nilai_akhir') : 0;
            @endphp

            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalSiswa }}</h3>
                        <small>Total Peserta</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ number_format($nilaiTertinggi, 1) }}</h3>
                        <small>Nilai Tertinggi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ number_format($nilaiTerendah, 1) }}</h3>
                        <small>Nilai Terendah</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ number_format($rataRata, 1) }}</h3>
                        <small>Rata-rata Nilai</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('kuis.exportPdf', $kuis->id) }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>

        <!-- Tabel Daftar Siswa -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-semibold">Daftar Siswa yang Mengerjakan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">No</th>
                                <th>NIPD</th>
                                <th>Nama</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Durasi</th>
                                <th class="text-center">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attempts as $index => $attempt)
                                <tr>
                                    <td class="px-4">{{ $index + 1 }}</td>
                                    <td>{{ $attempt->siswa_nisn }}</td>
                                    <td class="fw-semibold">{{ $attempt->siswa->nama_siswa ?? 'Siswa tidak terdaftar' }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($attempt->mulai_pada)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($attempt->selesai_pada)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($attempt->selesai_pada)->format('d/m/Y H:i') }}
                                            </small>
                                        @else
                                            <span class="badge bg-warning text-dark">Sedang Mengerjakan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->durasi)
                                            <span class="badge bg-light text-dark">
                                                {{ $attempt->durasi }} menit
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($attempt->nilai_akhir !== null)
                                            <span class="badge bg-primary fs-6">
                                                {{ $attempt->nilai_akhir }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">Belum ada siswa yang mengerjakan kuis ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('styles')
<style>
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table td {
        vertical-align: middle;
    }
</style>
@endsection