@extends('layouts.app')

@section('title', 'Buat Kelas Baru')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="text-decoration-none text-secondary mb-2 d-inline-block">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h3 class="fw-bold mb-1">Buat Kelas Baru</h3>
            <p class="text-muted">Isi form di bawah untuk membuat kelas baru</p>
        </div>

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('kelas.store') }}">
                            @csrf

                            <!-- Kode Kelas (Auto-generated, readonly) -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-key text-primary"></i> Kode Kelas
                                </label>
                                <input type="text" name="kode_kelas" class="form-control form-control-lg bg-light"
                                    value="{{ $newCode }}" readonly>
                                <small class="text-muted">Kode kelas dibuat otomatis oleh sistem</small>
                            </div>

                            <!-- Nama Kelas -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-book text-success"></i> Nama Kelas *
                                </label>
                                <input type="text" name="nama_kelas" class="form-control form-control-lg"
                                    placeholder="Contoh: Matematika Kelas 10A" value="{{ old('nama_kelas') }}" required>
                                @error('nama_kelas')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-text-left text-info"></i> Deskripsi Kelas *
                                </label>
                                <textarea name="deskripsi" class="form-control" rows="5"
                                    placeholder="Jelaskan tentang kelas ini, materi yang akan dipelajari, atau informasi penting lainnya..." required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Deskripsi akan membantu siswa memahami konten kelas</small>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2 mt-4">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary flex-fill">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-check-circle"></i> Buat Kelas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card border-0 bg-light mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-info-circle text-primary"></i> Informasi
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li>Setelah kelas dibuat, Anda dapat menambahkan konten pembelajaran</li>
                            <li>Siswa dapat bergabung menggunakan <strong>kode kelas</strong></li>
                            <li>Anda dapat mengelola anggota, tugas, dan forum diskusi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
