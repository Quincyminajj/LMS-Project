@extends('layouts.app')

@section('title', $kelas->nama_kelas.' — Tugas')

@section('content')
<div class="container py-4">

    <!-- Header Kelas (KONSISTEN) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">{{ $kelas->nama_kelas }}</h3>
            <p class="text-secondary mb-1">{{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}</p>
            <span class="badge bg-primary">{{ $kelas->kode_kelas }}</span>
        </div>

        <!-- TOMBOL TAMBAH TUGAS (KONSISTEN POSISI DAN STYLE) -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatTugas">
            <i class="bi bi-plus-lg"></i> Buat Tugas
        </button>
    </div>

    <!-- NAV TAB (SAMA PERSIS DENGAN VIEW KONTEN) -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a 
                class="nav-link" 
                href="{{ url('kelas/'.$kelas->id) }}"
            >Konten</a>
        </li>

        <li class="nav-item">
            <a 
                class="nav-link active"
                href="{{ route('tugas.index', $kelas->id) }}"
            >Tugas</a>
        </li>

        <li class="nav-item">
            <a 
                class="nav-link"
                href="{{ route('kelas.forum', $kelas->id) }}"
            >Forum</a>
        </li>
    </ul>


    <!-- LIST TUGAS (CARD MIRIP KONTEN STYLE) -->
    <div class="row g-3">
        @forelse ($tugas as $t)
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">

                <h5 class="fw-bold">{{ $t->judul }}</h5>
                <p class="text-secondary small mb-2">{{ Str::limit($t->deskripsi, 90) }}</p>

                <p class="text-danger small mb-3">
                    Deadline: {{ $t->deadline->format('d M Y — H:i') }}
                </p>

                <div class="d-flex justify-content-between align-items-center">

                    <a href="{{ route('tugaspengumpulan.index', [$kelas->id, $t->id]) }}" 
                       class="btn btn-primary btn-sm">
                        Detail Tugas
                    </a>

                    <div>
                        <!-- EDIT -->
                        <button class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditTugas{{ $t->id }}">
                            Edit
                        </button>

                        <!-- DELETE -->
                        <form action="{{ route('tugas.delete', [$kelas->id, $t->id]) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus tugas ini?')">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- ===========================
             MODAL EDIT
        ============================ -->
        <div class="modal fade" id="modalEditTugas{{ $t->id }}" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
              <div class="modal-header">
                <h4 class="modal-title fw-semibold">Edit Tugas</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <form action="{{ route('tugas.update', [$kelas->id, $t->id]) }}" method="POST">
                @csrf

                <div class="modal-body">

                  <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" value="{{ $t->judul }}" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ $t->deskripsi }}</textarea>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Nilai Maksimal</label>
                      <input type="number" name="nilai_maksimal" value="{{ $t->nilai_maksimal }}" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label">Deadline</label>
                      <input type="datetime-local" name="deadline"
                             value="{{ $t->deadline->format('Y-m-d\TH:i') }}"
                             class="form-control">
                    </div>
                  </div>

                </div>

                <div class="modal-footer">
                  <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                  <button class="btn btn-warning">Simpan Perubahan</button>
                </div>

              </form>
            </div>
          </div>
        </div>

        @empty
        <p class="text-center text-secondary">Belum ada tugas dibuat.</p>
        @endforelse
    </div>
</div>


<!-- ===========================
     MODAL CREATE TUGAS
=========================== -->
<div class="modal fade" id="modalBuatTugas" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header">
        <h4 class="modal-title fw-semibold">Buat Tugas Baru</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('tugas.store', $kelas->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-body">

          <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
          <input type="hidden" name="created_by" value="{{ auth()->user()->username ?? 'guru' }}">

          <div class="mb-3">
            <label class="form-label fw-semibold">Judul Tugas *</label>
            <input type="text" name="judul" class="form-control form-control-lg" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi *</label>
            <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Nilai Maks *</label>
              <input type="number" name="nilai_maksimal" class="form-control" value="100">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Deadline *</label>
              <input type="datetime-local" name="deadline" class="form-control" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">File Contoh (opsional)</label>
            <input type="file" name="file_contoh" class="form-control">
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary">Buat Tugas</button>
        </div>

      </form>
    </div>
  </div>
</div>

@endsection
