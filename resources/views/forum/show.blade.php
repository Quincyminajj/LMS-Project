@extends('layouts.app')

@section('title', $forum->judul)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Back Button -->
                <a href="{{ route('kelas.forum', $forum->kelas_id) }}"
                    class="text-decoration-none text-secondary mb-3 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Kembali ke Forum
                </a>

                <!-- Forum Post -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="fw-bold mb-2">{{ $forum->judul }}</h4>
                                <div class="text-muted small">
                                    <i class="bi bi-person-circle"></i> {{ $forum->dibuat_oleh }} •
                                    <i class="bi bi-clock"></i> {{ $forum->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>

                            @if (session('role') === 'guru' && session('identifier') === $forum->kelas->guru_nip)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form id="delete-forum-{{ $forum->id }}"
                                                action="{{ route('forum.destroy', $forum->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf @method('DELETE')
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

                        <div class="border-top pt-3">
                            <p class="mb-3">{{ $forum->isi }}</p>

                            <!-- Tampilkan Gambar jika ada -->
                            @if ($forum->gambar)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/forum_images/' . $forum->gambar) }}" alt="Forum Image"
                                        class="img-fluid rounded shadow-sm" style="max-width: 100%; cursor: pointer;"
                                        onclick="openImageModal(this.src)">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-chat-dots"></i> Komentar ({{ $forum->komentars->count() }})
                        </h5>

                        <!-- Comment Form -->
                        <form action="{{ route('forum-komentar.store') }}" method="POST" class="mb-4">
                            @csrf
                            <input type="hidden" name="forum_id" value="{{ $forum->id }}">

                            <div class="mb-3">
                                <textarea name="isi" class="form-control" rows="3" placeholder="Tulis komentar Anda..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Kirim Komentar
                            </button>
                        </form>

                        <!-- Comments List -->
                        <div class="border-top pt-4">
                            @forelse($forum->komentars()->orderBy('created_at', 'asc')->get() as $komentar)
                                <div class="d-flex gap-3 mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $komentar->dibuat_oleh }}</strong>
                                                <small
                                                    class="text-muted d-block">{{ $komentar->created_at->diffForHumans() }}</small>
                                            </div>

                                            @if (session('user_name') === $komentar->dibuat_oleh ||
                                                    (session('role') === 'guru' && session('identifier') === $forum->kelas->guru_nip))
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if (session('user_name') === $komentar->dibuat_oleh)
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal{{ $komentar->id }}">Edit</a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <form
                                                                action="{{ route('forum-komentar.destroy', $komentar->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin ingin menghapus komentar ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger">Hapus</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <p class="mt-2 mb-0">{{ $komentar->isi }}</p>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $komentar->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Komentar</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('forum-komentar.update', $komentar->id) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <textarea name="isi" class="form-control" rows="4" required>{{ $komentar->isi }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-chat-square-text fs-3"></i>
                                    <p class="mt-2">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk melihat gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <img id="modalImage" src="" alt="Full Image" class="img-fluid w-100">
                </div>
            </div>
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
                    document.getElementById(`delete-forum-${id}`).submit();
                }
            });
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }
    </script>
@endsection
