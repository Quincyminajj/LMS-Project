@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', $kelas->nama_kelas)

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
                        <p class="text-secondary mb-2">{{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }} • Kode:
                            {{ $kelas->kode_kelas }}</p>
                        <p class="text-muted small mb-0">{{ $kelas->deskripsi }}</p>
                    </div>

                    @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                        <div class="dropdown">
                            <button class="btn btn-light" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('kelas.edit', $kelas->id) }}">Edit Kelas</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('kelas.arsip') }}">Arsipkan</a></li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ !request()->is('kelas/*/tugas') && !request()->is('kelas/*/forum') ? 'active' : '' }}"
                    href="{{ route('kelas.show', $kelas->id) }}">
                    <i class="bi bi-book"></i> Konten
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('kelas/*/tugas') ? 'active' : '' }}"
                    href="{{ route('tugas.index', $kelas->id) }}">
                    <i class="bi bi-clipboard-check"></i> Tugas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('kelas/*/forum') ? 'active' : '' }}"
                    href="{{ route('kelas.forum', $kelas->id) }}">
                    <i class="bi bi-chat-dots"></i> Forum
                </a>
            </li>
        </ul>

        <!-- ========================================= -->
        <!-- TAB KONTEN -->
        <!-- ========================================= -->
        @if (!request()->is('kelas/*/tugas') && !request()->is('kelas/*/forum'))

            @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Konten Pembelajaran</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKonten">
                        <i class="bi bi-plus-lg"></i> Tambah Konten
                    </button>
                </div>
            @else
                <h5 class="mb-3">Konten Pembelajaran</h5>
            @endif

            <div class="row g-3">
                @forelse($kelas->konten()->orderBy('created_at', 'desc')->get() as $konten)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span
                                        class="badge {{ $konten->tipe == 'file' ? 'bg-primary' : ($konten->tipe == 'link' ? 'bg-info' : 'bg-secondary') }}">
                                        <i
                                            class="bi bi-{{ $konten->tipe == 'file' ? 'file-earmark' : ($konten->tipe == 'link' ? 'link-45deg' : 'text-left') }}"></i>
                                        {{ ucfirst($konten->tipe) }}
                                    </span>

                                    @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditKonten{{ $konten->id }}">Edit</a></li>
                                                <li>
                                                    <form action="{{ route('konten.destroy', $konten->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus konten ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item text-danger">Hapus</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <h6 class="fw-bold mb-2">{{ $konten->judul }}</h6>
                                <p class="text-secondary small mb-3">{{ Str::limit($konten->isi, 100) }}</p>

                                <div class="d-flex justify-content-between align-items-center">
                                    @if ($konten->tipe == 'file')
                                        <a href="{{ asset('storage/' . $konten->file_path) }}"
                                            class="btn btn-outline-primary btn-sm" download>
                                            <i class="bi bi-download"></i> Download File
                                        </a>
                                    @elseif($konten->tipe == 'link')
                                        <a href="{{ $konten->isi }}" target="_blank" class="btn btn-primary btn-sm">
                                            <i class="bi bi-box-arrow-up-right"></i> Buka Link
                                        </a>
                                    @endif

                                    <small class="text-muted">{{ $konten->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Edit Konten (Only for Guru) -->
                    @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                        <div class="modal fade" id="modalEditKonten{{ $konten->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Konten</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('konten.destroy', $konten->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Judul</label>
                                                <input type="text" name="judul" class="form-control"
                                                    value="{{ $konten->judul }}" required>
                                            </div>
                                            @if ($konten->tipe == 'teks')
                                                <div class="mb-3">
                                                    <label class="form-label">Isi</label>
                                                    <textarea name="isi" class="form-control" rows="4">{{ $konten->isi }}</textarea>
                                                </div>
                                            @endif
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
                    @endif

                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-secondary mt-3">Belum ada konten pembelajaran</p>
                            @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                    data-bs-target="#modalTambahKonten">
                                    <i class="bi bi-plus-lg"></i> Tambah Konten Pertama
                                </button>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
        @endif

    </div>
@endsection

<!-- Modal Tambah Konten (Only for Guru) -->
@if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
    @section('modal')
        <div class="modal fade" id="modalTambahKonten" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Tambah Konten Pembelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('konten.store', $kelas->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Judul Konten *</label>
                                <input type="text" name="judul" class="form-control"
                                    placeholder="Contoh: Materi Persamaan Kuadrat" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tipe Konten *</label>
                                <select class="form-select" name="tipe" id="tipeKonten" required>
                                    <option value="" disabled selected>- Pilih tipe konten -</option>
                                    <option value="file">File (PDF, Word, PPT, dll)</option>
                                    <option value="link">Link</option>
                                    <option value="teks">Teks</option>
                                </select>
                            </div>

                            <!-- File Upload -->
                            <div id="fileUploadField" class="mb-3 d-none">
                                <label class="form-label fw-semibold">Upload File *</label>
                                <input type="file" name="file_path" class="form-control">
                            </div>

                            <!-- Link -->
                            <div id="linkField" class="mb-3 d-none">
                                <label class="form-label fw-semibold">Masukkan Link *</label>
                                <input type="url" name="isi" class="form-control"
                                    placeholder="https://example.com">
                            </div>

                            <!-- Text -->
                            <div id="textField" class="mb-3 d-none">
                                <label class="form-label fw-semibold">Deskripsi *</label>
                                <textarea name="isi" class="form-control" rows="4" placeholder="Deskripsi konten"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah Konten</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('tipeKonten').addEventListener('change', function() {
                let tipe = this.value;

                document.getElementById('fileUploadField').classList.add('d-none');
                document.getElementById('linkField').classList.add('d-none');
                document.getElementById('textField').classList.add('d-none');

                if (tipe === 'file') document.getElementById('fileUploadField').classList.remove('d-none');
                if (tipe === 'link') document.getElementById('linkField').classList.remove('d-none');
                if (tipe === 'teks') document.getElementById('textField').classList.remove('d-none');
            });
        </script>
    @endsection
@endif
