@extends('layouts.app')

@section('title', $tugas->judul)

@section('content')
    <div class="container py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Back Button -->
                <a href="{{ route('tugas.index', $tugas->kelas_id) }}"
                    class="text-decoration-none text-secondary mb-3 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Tugas
                </a>

                <!-- Tugas Detail Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <span class="badge bg-primary mb-2">
                                    <i class="bi bi-clipboard-check"></i> Tugas Kelas
                                </span>
                                <h3 class="fw-bold mb-2">{{ $tugas->judul }}</h3>
                                <p class="text-secondary mb-0">{{ $tugas->kelas->nama_kelas }}</p>
                            </div>

                            @if (session('role') === 'guru' && session('identifier') === $tugas->kelas->guru_nip)
                                <div class="dropdown position-relative">
                                    <button class="btn btn-light" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end mt-3"
                                        style="min-width: 50px;"
                                        data-bs-offset="20,0">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#editTugasModal">
                                                <i class="bi bi-pencil"></i> Edit Tugas
                                            </a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form id="delete-tugas-{{ $tugas->id }}"
                                                action="{{ route('tugas.destroy', ['kelas' => $tugas->kelas_id, 'tugas' => $tugas->id]) }}"
                                                method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <button type="button" class="dropdown-item text-danger"
                                                    onclick="confirmDeleteTugas({{ $tugas->id }})">
                                                <i class="bi bi-trash"></i> Hapus Tugas
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="border-top border-bottom py-3 my-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Deadline</small>
                                    <strong>
                                        <i class="bi bi-calendar-event text-danger"></i>
                                        {{ $tugas->deadline->format('d M Y, H:i') }}
                                    </strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Nilai Maksimal</small>
                                    <strong>
                                        <i class="bi bi-trophy text-warning"></i>
                                        {{ $tugas->nilai_maksimal }}
                                    </strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Status Deadline</small>
                                    @if ($tugas->deadline->isPast())
                                        <span class="badge bg-danger">Sudah Lewat</span>
                                    @else
                                        <span class="badge bg-success">Masih Aktif</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5 class="fw-bold mb-3">Deskripsi Tugas</h5>
                            <p class="text-secondary" style="white-space: pre-line;">{{ $tugas->deskripsi }}</p>
                        </div>

                        @if ($tugas->file_contoh)
                            <div class="alert alert-light border">
                                <i class="bi bi-paperclip"></i> <strong>File Lampiran:</strong>
                                <a href="{{ asset('storage/' . $tugas->file_contoh) }}"
                                    class="btn btn-sm btn-outline-primary ms-2" download>
                                    <i class="bi bi-download"></i> Download File Contoh
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Siswa: Form Pengumpulan -->
                @if (session('role') === 'siswa')
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-send"></i> Pengumpulan Tugas
                            </h5>

                            @php
                                $pengumpulan = $tugas
                                    ->pengumpulan()
                                    ->where('siswa_nisn', session('identifier'))
                                    ->first();
                            @endphp

                            @if ($pengumpulan)
                                <!-- Sudah Mengumpulkan -->
                                <div class="alert alert-success">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-check-circle-fill"></i>
                                            <strong>Tugas Sudah Dikumpulkan</strong>
                                            <p class="mb-0 mt-2 small">Dikumpulkan pada:
                                                {{ $pengumpulan->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                        @if ($pengumpulan->nilai)
                                            <div class="text-end">
                                                <div class="badge bg-primary"
                                                    style="font-size: 1.2rem; padding: 10px 15px;">
                                                    Nilai: {{ $pengumpulan->nilai }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="border rounded p-3 mb-3">
                                    <strong>Jawaban Anda:</strong>
                                    <p class="mb-2 mt-2">{{ $pengumpulan->jawaban }}</p>

                                    @if ($pengumpulan->file_path)
                                        <a href="{{ asset('storage/' . $pengumpulan->file_path) }}"
                                            class="btn btn-sm btn-outline-primary" download>
                                            <i class="bi bi-download"></i> Download File Pengumpulan
                                        </a>
                                    @endif
                                </div>

                                @if ($pengumpulan->catatan_guru)
                                    <div class="alert alert-info">
                                        <strong><i class="bi bi-chat-left-text"></i> Catatan Guru:</strong>
                                        <p class="mb-0 mt-2">{{ $pengumpulan->catatan_guru }}</p>
                                    </div>
                                @endif
                            @else
                                <!-- Belum Mengumpulkan -->
                                @if ($tugas->deadline->isPast())
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        <strong>Deadline sudah lewat!</strong> Tugas tidak bisa dikumpulkan lagi.
                                    </div>
                                @else
                                    <form action="{{ route('tugas.submit', $tugas->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Jawaban Tugas *</label>
                                            <textarea name="jawaban" class="form-control" rows="6" placeholder="Tulis jawaban Anda di sini..." required></textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Upload File (opsional)</label>
                                            <input type="file" name="file_path" class="form-control">
                                            <small class="text-muted">Format: PDF, Word, Image (Max: 5MB)</small>
                                        </div>

                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-send-fill"></i> Kumpulkan Tugas
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Guru: Daftar Pengumpulan -->
                @if (session('role') === 'guru' && session('identifier') === $tugas->kelas->guru_nip)
                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-people"></i> Daftar Pengumpulan
                                <span class="badge bg-primary">{{ $tugas->pengumpulan->count() }}</span>
                            </h5>

                            @forelse($tugas->pengumpulan as $p)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $p->siswa->nama_siswa ?? 'Siswa' }}</h6>
                                            <small class="text-muted">
                                                NIS: {{ $p->siswa_nisn }} •
                                                Dikumpulkan: {{ $p->created_at->format('d M Y, H:i') }}
                                            </small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#nilaiModal{{ $p->id }}">
                                            <i class="bi bi-pencil"></i> Beri Nilai
                                        </button>
                                    </div>

                                    <div class="mt-2">
                                        <strong>Jawaban:</strong>
                                        <p class="mb-2">{{ Str::limit($p->jawaban, 200) }}</p>

                                        @if ($p->file_path)
                                            <a href="{{ asset('storage/' . $p->file_path) }}"
                                                class="btn btn-sm btn-outline-secondary mb-2" download>
                                                <i class="bi bi-download"></i> Download File
                                            </a>
                                        @endif

                                        @if ($p->nilai)
                                            <div class="alert alert-success py-2 mb-0 mt-2">
                                                <strong>Nilai: {{ $p->nilai }}</strong>
                                                @if ($p->catatan_guru)
                                                    <p class="mb-0 small mt-1">Catatan: {{ $p->catatan_guru }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning py-2 mb-0 mt-2">
                                                <i class="bi bi-hourglass-split"></i> Belum dinilai
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Modal Beri Nilai -->
                                <div class="modal fade" id="nilaiModal{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Beri Nilai -
                                                    {{ $p->siswa->nama_siswa ?? 'Siswa' }}</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('tugaspengumpulan.update', $p->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nilai *</label>
                                                        <input type="number" name="nilai" class="form-control"
                                                            value="{{ $p->nilai }}" min="0"
                                                            max="{{ $tugas->nilai_maksimal }}" required>
                                                        <small class="text-muted">Maksimal:
                                                            {{ $tugas->nilai_maksimal }}</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan untuk Siswa</label>
                                                        <textarea name="catatan_guru" class="form-control" rows="3" placeholder="Berikan feedback untuk siswa...">{{ $p->catatan_guru }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">Belum ada siswa yang mengumpulkan tugas</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Informasi Kelas</h6>

                        <div class="mb-3">
                            <small class="text-muted d-block">Nama Kelas</small>
                            <strong>{{ $tugas->kelas->nama_kelas }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Kode Kelas</small>
                            <strong>{{ $tugas->kelas->kode_kelas }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Guru Pengajar</small>
                            <strong>{{ $tugas->kelas->guru->nama_guru ?? '-' }}</strong>
                        </div>

                        @if (session('role') === 'guru')
                            <hr>
                            <div class="mb-3">
                                <small class="text-muted d-block">Total Pengumpulan</small>
                                <strong class="fs-4">{{ $tugas->pengumpulan->count() }}</strong> siswa
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">Sudah Dinilai</small>
                                <strong
                                    class="fs-4 text-success">{{ $tugas->pengumpulan->whereNotNull('nilai')->count() }}</strong>
                                siswa
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block">Belum Dinilai</small>
                                <strong
                                    class="fs-4 text-warning">{{ $tugas->pengumpulan->whereNull('nilai')->count() }}</strong>
                                siswa
                            </div>
                        @endif

                        <hr>
                        <a href="{{ route('tugas.index', $tugas->kelas_id) }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-list"></i> Lihat Semua Tugas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Tugas (Guru Only) -->
    @if (session('role') === 'guru' && session('identifier') === $tugas->kelas->guru_nip)
        <div class="modal fade" id="editTugasModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Tugas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('tugas.update', [$tugas->kelas_id, $tugas->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Judul Tugas *</label>
                                <input type="text" name="judul" class="form-control" value="{{ $tugas->judul }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi *</label>
                                <textarea name="deskripsi" class="form-control" rows="5" required>{{ $tugas->deskripsi }}</textarea>
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
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
        <script>
        function confirmDeleteTugas(id) {
            Swal.fire({
                title: 'Hapus Tugas?',
                text: "Tugas ini akan dihapus secara permanen!",
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
@endsection
