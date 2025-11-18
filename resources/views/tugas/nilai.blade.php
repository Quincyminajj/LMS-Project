@extends('layouts.app')

@section('title', 'Penilaian Tugas')

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Penilaian Tugas: {{ $tugas->judul }}</h4>
        <a href="{{ route('guru.tugas.index', $tugas->kelas_id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Info Tugas --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <p class="mb-1"><strong> Kelas:</strong> {{ $tugas->kelas->nama }}</p>
            <p class="mb-1"><strong> Deadline:</strong> {{ $tugas->deadline->format('d M Y H:i') }}</p>
            <p class="mb-1"><strong> Nilai Maksimal:</strong> {{ $tugas->nilai_maksimal }}</p>
        </div>
    </div>

    {{-- Tabel Pengumpulan --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Daftar Pengumpulan Siswa</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>NISN</th>
                            <th>Tipe</th>
                            <th>Dikumpulkan Pada</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengumpulans as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->siswa_nisn }}</td>
                            <td>
                                <span class="badge bg-info text-dark text-uppercase">{{ $p->tipe }}</span>
                            </td>
                            <td>{{ $p->dikumpul_pada->format('d M Y H:i') }}</td>
                            <td>
                                @if ($p->nilai !== null)
                                    <span class="badge bg-success">{{ $p->nilai }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum dinilai</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $p->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>

                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalNilai{{ $p->id }}">
                                    <i class="bi bi-pencil"></i> Nilai
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Detail Pengumpulan --}}
                        <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">Detail Pengumpulan</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">

                                        <p><strong>NISN:</strong> {{ $p->siswa_nisn }}</p>
                                        <p><strong>Tipe:</strong> {{ strtoupper($p->tipe) }}</p>
                                        <p><strong>Dikumpulkan Pada:</strong>
                                            {{ $p->dikumpul_pada->format('d M Y H:i') }}
                                        </p>

                                        @if ($p->tipe == 'teks')
                                            <div class="p-3 border rounded">
                                                {{ $p->isi }}
                                            </div>
                                        @endif

                                        @if ($p->tipe == 'link')
                                            <a href="{{ $p->isi }}" target="_blank" class="btn btn-primary">
                                                Buka Link
                                            </a>
                                        @endif

                                        @if ($p->tipe == 'file' && $p->file_path)
                                            <a href="{{ asset('storage/' . $p->file_path) }}"
                                                target="_blank" class="btn btn-success">
                                                Download File
                                            </a>
                                        @endif

                                        @if ($p->feedback)
                                            <div class="mt-3">
                                                <strong>Feedback:</strong>
                                                <div class="p-2 border rounded bg-light">{{ $p->feedback }}</div>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Beri Nilai --}}
                        <div class="modal fade" id="modalNilai{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('guru.tugas.nilai.update', $p->id) }}"
                                    method="POST" class="modal-content">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Beri Nilai</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nilai</label>
                                            <input type="number" name="nilai" max="{{ $tugas->nilai_maksimal }}"
                                                step="0.1" class="form-control"
                                                value="{{ $p->nilai }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Feedback</label>
                                            <textarea name="feedback" class="form-control" rows="3">{{ $p->feedback }}</textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button class="btn btn-success">Simpan Nilai</button>
                                    </div>

                                </form>
                            </div>
                        </div>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
