@extends('layouts.app')

@section('title', $kelas->nama_kelas.' — Forum')

@section('content')
<div class="container py-4">

    <!-- HEADER KELAS (SAMA DENGAN TUGAS) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">{{ $kelas->nama_kelas }}</h3>
            <p class="text-secondary mb-1">{{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}</p>
            <span class="badge bg-primary">{{ $kelas->kode_kelas }}</span>
        </div>

        <!-- BUTTON BUAT FORUM (MODAL SAMA SEPERTI TUGAS) -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatForum">
            <i class="bi bi-plus-lg"></i> Buat Forum
        </button>
    </div>

    <!-- NAV TAB -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ url('kelas/'.$kelas->id) }}">Konten</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('tugas.index', $kelas->id) }}">Tugas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active">Forum</a>
        </li>
    </ul>

    <!-- SPLIT LAYOUT -->
    <div class="row g-3">

        <!-- LIST FORUM (SEBELAH KIRI) -->
        <div class="col-md-4">
            <div class="list-group shadow-sm">

                @forelse ($forums as $f)
                <a href="{{ route('forum.show', [$kelas->id, $f->id]) }}"
                    class="list-group-item list-group-item-action py-3
                    {{ isset($forum) && $forum->id == $f->id ? 'active text-white' : '' }}"
                    style="border-radius: 10px;">

                    <h6 class="fw-bold mb-1">{{ $f->judul }}</h6>
                    <small class="d-block">{{ Str::limit($f->isi, 70) }}</small>
                    <small class="text-muted">Oleh: {{ $f->dibuat_oleh ?? 'Unknown' }}</small>

                </a>
                @empty
                    <p class="text-center text-secondary mt-3 p-3">Belum ada forum.</p>
                @endforelse

            </div>
        </div>

        <!-- DETAIL FORUM (SEBELAH KANAN) -->
        <div class="col-md-8">

            @if(!isset($forum))
                <div class="card p-4 text-center shadow-sm" style="border-radius: 14px;">
                    <p class="text-secondary">Pilih forum untuk melihat diskusi.</p>
                </div>
            @else
                <div class="card p-4 shadow-sm" style="border-radius: 14px;">

                    <h4 class="fw-bold">{{ $forum->judul }}</h4>
                    <p class="text-secondary">{{ $forum->isi }}</p>

                    <small class="text-muted">
                        Dibuat oleh {{ $forum->dibuat_oleh }} • {{ $forum->created_at->format('d M Y') }}
                    </small>

                    <hr>

                    <!-- KOMENTAR -->
                    @foreach ($forum->komentars as $k)
                        <div class="p-3 mb-3 rounded {{ $k->role == 'guru' ? 'bg-light border-start border-3 border-primary' : 'bg-light' }}">
                            <strong>{{ $k->nama }}</strong>
                            <span class="badge bg-secondary ms-2">{{ ucfirst($k->role) }}</span>
                            <small class="text-muted ms-2">{{ $k->created_at->format('d/m/Y') }}</small>
                            <p class="mt-2 mb-0">{{ $k->komentar }}</p>
                        </div>
                    @endforeach

                    <hr>

                    <!-- FORM KOMENTAR -->
                    <form action="{{ route('forum.comment', [$kelas->id, $forum->id]) }}" method="POST">
                        @csrf
                        <textarea name="komentar" rows="3" class="form-control mb-3" placeholder="Tulis komentar..." required></textarea>
                        <button class="btn btn-primary px-4">Kirim Komentar</button>
                    </form>

                </div>
            @endif

        </div>

    </div>
</div>


<!-- =============================
     MODAL BUAT FORUM
============================= -->
<div class="modal fade" id="modalBuatForum" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header">
        <h4 class="modal-title fw-semibold">Buat Forum Diskusi</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('forum.store') }}" method="POST">
        @csrf

        <div class="modal-body">
          <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

          <div class="mb-3">
            <label class="form-label fw-semibold">Judul Forum *</label>
            <input type="text" name="judul" class="form-control form-control-lg" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi *</label>
            <textarea name="isi" rows="3" class="form-control" required></textarea>
          </div>

          <div class="alert alert-info small">💡 Siswa dapat berkomentar dan berdiskusi pada forum ini</div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary">Buat Forum</button>
        </div>

      </form>
    </div>
  </div>
</div>

@endsection
