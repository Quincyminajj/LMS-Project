@extends('layouts.app')

@section('title', 'Tugas - ' . $kelas->nama_kelas)

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
                <a class="nav-link active" href="{{ route('tugas.index', $kelas->id) }}">
                    <i class="bi bi-clipboard-check"></i> Tugas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('kelas.forum', $kelas->id) }}">
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

        <!-- Header Tugas -->
        @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Tugas</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahTugas">
                    <i class="bi bi-plus-lg"></i> Buat Tugas
                </button>
            </div>
        @else
            <h5 class="mb-3">Daftar Tugas</h5>
        @endif

        <!-- List Tugas -->
        <div class="row g-3">
            @forelse($kelas->tugas()->orderBy('deadline', 'asc')->get() as $tugas)
                @php
                    // Check if siswa sudah submit
                    $sudahSubmit = false;
                    $nilaiSiswa = null;
                    if (session('role') === 'siswa') {
                        $pengumpulan = $tugas->pengumpulan()->where('siswa_nisn', session('identifier'))->first(); // ✅ BENAR
                        $sudahSubmit = !empty($pengumpulan);
                        $nilaiSiswa = $pengumpulan->nilai ?? null;
                    }
                @endphp

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $tugas->judul }}</h6>
                                    <p class="text-secondary small mb-2">{{ Str::limit($tugas->deskripsi, 100) }}</p>
                                </div>

                                @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditTugas{{ $tugas->id }}">Edit</a></li>
                                            <li>
                                                <form id="delete-tugas-{{ $tugas->id }}"
                                                    action="{{ route('tugas.destroy', ['kelas' => $tugas->kelas_id, 'tugas' => $tugas->id]) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button type="button"
                                                        class="dropdown-item text-danger"
                                                        onclick="confirmDeleteTugas({{ $tugas->id }})">
                                                    Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-calendar-event"></i> Deadline:
                                    {{ $tugas->deadline->format('d M Y, H:i') }}
                                </span>
                                <span class="badge bg-primary">
                                    Nilai: {{ $tugas->nilai_maksimal }}
                                </span>
                            </div>

                            @if (session('role') === 'siswa')
                                @if ($sudahSubmit)
                                    <div class="alert alert-success py-2 px-3 mb-2">
                                        <i class="bi bi-check-circle"></i> Tugas telah dinilai
                                        @if ($nilaiSiswa)
                                            <strong class="float-end">Nilai Anda: {{ $nilaiSiswa }}</strong>
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 px-3 mb-2">
                                        <i class="bi bi-exclamation-circle"></i> Belum dikumpulkan
                                    </div>
                                @endif
                            @endif

                            <a href="{{ route('tugas.show', $tugas->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Tugas (Only for Guru) -->
                @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                    <div class="modal fade" id="modalEditTugas{{ $tugas->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Tugas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('tugas.update', [$kelas->id, $tugas->id]) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Tugas *</label>
                                            <input type="text" name="judul" class="form-control"
                                                value="{{ $tugas->judul }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi *</label>
                                            <textarea name="deskripsi" class="form-control" rows="4" required>{{ $tugas->deskripsi }}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Deadline *</label>
                                                <input type="datetime-local" name="deadline" class="form-control"
                                                    value="{{ $tugas->deadline->format('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nilai Maksimal *</label>
                                                <input type="number" name="nilai_maksimal" class="form-control"
                                                    value="{{ $tugas->nilai_maksimal }}" required>
                                            </div>
                                        </div>
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
                        <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                        <p class="text-secondary mt-3">Belum ada tugas</p>
                        @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                data-bs-target="#modalTambahTugas">
                                <i class="bi bi-plus-lg"></i> Buat Tugas Pertama
                            </button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

    </div>
@endsection

<!-- Modal Tambah Tugas (Only for Guru) -->
@if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
    @section('modal')
        <div class="modal fade" id="modalTambahTugas" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Buat Tugas Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('tugas.store', $kelas->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Judul Tugas *</label>
                                <input type="text" name="judul" class="form-control"
                                    placeholder="Contoh: Latihan Soal Persamaan Kuadrat" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi *</label>
                                <textarea name="deskripsi" class="form-control" rows="4"
                                    placeholder="Kerjakan 10 soal persamaan kuadrat yang telah disediakan" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Deadline *</label>
                                    <input type="datetime-local" name="deadline" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Nilai Maksimal *</label>
                                    <input type="number" name="nilai_maksimal" class="form-control" placeholder="100"
                                        value="100" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">File Contoh (opsional)</label>
                                <input type="file" name="file_contoh" class="form-control">
                                <small class="text-muted">Upload file contoh soal jika diperlukan</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Buat Tugas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
    <script>
        function confirmDeleteTugas(id) {
            Swal.fire({
                title: 'Hapus Tugas?',
                text: "Tugas beserta data terkait akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-tugas-${id}`).submit();
                }
            })
        }
</script>
@endif
