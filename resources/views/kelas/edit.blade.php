@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="mb-4">
            <a href="{{ route('kelas.show', $kelas->id) }}" class="text-decoration-none text-secondary mb-2 d-inline-block">
                <i class="bi bi-arrow-left"></i> Kembali ke Kelas
            </a>
            <h3 class="fw-bold mb-1">Edit Kelas</h3>
            <p class="text-muted">Perbarui informasi kelas {{ $kelas->nama_kelas }}</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('kelas.update', $kelas->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Kode Kelas (Readonly) -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-key text-primary"></i> Kode Kelas
                                </label>
                                <input type="text" class="form-control form-control-lg bg-light"
                                    value="{{ $kelas->kode_kelas }}" readonly>
                                <small class="text-muted">Kode kelas tidak dapat diubah</small>
                            </div>

                            <!-- Nama Kelas -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-book text-success"></i> Nama Kelas *
                                </label>
                                <input type="text" name="nama_kelas" class="form-control form-control-lg"
                                    value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                                    placeholder="Contoh: Matematika Kelas 10A" required>
                                @error('nama_kelas')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-text-left text-info"></i> Deskripsi Kelas
                                </label>
                                <textarea name="deskripsi" class="form-control" rows="5" placeholder="Jelaskan tentang kelas ini...">{{ old('deskripsi', $kelas->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Kelas -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on text-warning"></i> Status Kelas
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" disabled
                                        {{ $kelas->status === 'Aktif' ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <span
                                            class="badge {{ $kelas->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $kelas->status }}
                                        </span>
                                    </label>
                                </div>
                                <small class="text-muted">Gunakan menu dropdown untuk mengarsipkan kelas</small>
                            </div>

                            <!-- Statistik -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">Statistik Kelas</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-primary fs-3 fw-bold">{{ $kelas->anggota->count() }}</div>
                                            <small class="text-muted">Siswa</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-success fs-3 fw-bold">{{ $kelas->konten->count() }}</div>
                                            <small class="text-muted">Konten</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-info fs-3 fw-bold">{{ $kelas->tugas->count() }}</div>
                                            <small class="text-muted">Tugas</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2 mt-4">
                                <a href="{{ route('kelas.show', $kelas->id) }}" class="btn btn-secondary flex-fill">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
