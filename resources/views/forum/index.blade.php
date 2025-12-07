@extends('layouts.app')

@section('title', 'Forum - ' . $kelas->nama_kelas)

@section('content')
    <div class="container py-4">

        <!-- Header Kelas -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-secondary mb-2 d-inline-block">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <h3 class="fw-bold mb-2">{{ $kelas->nama_kelas }}</h3>
                        <p class="text-secondary mb-0">{{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }} • Kode:
                            {{ $kelas->kode_kelas }}</p>
                        <p class="text-muted small mb-0">{{ $kelas->deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kelas.show', $kelas->id) }}">
                    <i class="bi bi-book"></i> Konten
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tugas.index', $kelas->id) }}">
                    <i class="bi bi-clipboard-check"></i> Tugas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('kelas.forum', $kelas->id) }}">
                    <i class="bi bi-chat-dots"></i> Forum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('kelas/*/anggota') ? 'active' : '' }}"
                    href="{{ route('kelas.anggota', $kelas->id) }}">
                    <i class="bi bi-people"></i> Anggota
                </a>
            </li>
        </ul>

        <!-- Header Forum -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Diskusi Forum</h5>
            <a href="{{ route('forum.create', $kelas->id) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Buat Diskusi
            </a>
        </div>

        <!-- List Forum -->
        <div class="row g-3">
            @php
                $forums = App\Models\Forum::where('kelas_id', $kelas->id)->orderBy('created_at', 'desc')->get();
            @endphp

            @forelse($forums as $forum)
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <!-- Thumbnail Gambar jika ada -->
                                @if ($forum->gambar)
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('storage/forum_images/' . $forum->gambar) }}"
                                            alt="Forum thumbnail" class="rounded"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                @endif

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">
                                                {{ $forum->judul }}
                                                @if ($forum->gambar)
                                                    <i class="bi bi-image text-primary ms-1"></i>
                                                @endif
                                            </h6>
                                            <p class="text-secondary mb-2">{{ Str::limit($forum->isi, 150) }}</p>
                                            <small class="text-muted">
                                                <i class="bi bi-person-circle"></i> {{ $forum->dibuat_oleh }} •
                                                <i class="bi bi-clock"></i> {{ $forum->created_at->diffForHumans() }} •
                                                <i class="bi bi-chat"></i> {{ $forum->komentars->count() }} komentar
                                            </small>
                                        </div>

                                        @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form id="delete-form-{{ $forum->id }}"
                                                            action="{{ route('forum.destroy', $forum->id) }}"
                                                            method="POST" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button type="button" class="dropdown-item text-danger"
                                                            onclick="confirmDeleteForum({{ $forum->id }})">
                                                            Hapus Diskusi
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    <a href="{{ route('forum.show', $forum->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Lihat Diskusi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-chat-square-text fs-1 text-muted"></i>
                        <p class="text-secondary mt-3">Belum ada diskusi forum</p>
                        <a href="{{ route('forum.create', $kelas->id) }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-lg"></i> Mulai Diskusi Pertama
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function confirmDeleteForum(id) {
            Swal.fire({
                title: 'Hapus Diskusi?',
                text: "Aksi ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>
@endsection
