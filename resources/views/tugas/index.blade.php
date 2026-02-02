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

        <!-- Tab Navigation - Mobile Optimized -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link px-2 px-md-3 small" href="{{ route('kelas.show', $kelas->id) }}">
                    <i class="bi bi-book"></i> <span class="d-none d-sm-inline">Konten</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active px-2 px-md-3 small" href="{{ route('tugas.index', $kelas->id) }}">
                    <i class="bi bi-clipboard-check"></i> Tugas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-2 px-md-3 small" href="{{ route('kuis.index', $kelas->id) }}">
                    <i class="bi bi-patch-question"></i> <span class="d-none d-sm-inline">Kuis</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-2 px-md-3 small" href="{{ route('kelas.forum', $kelas->id) }}">
                    <i class="bi bi-chat-dots"></i> <span class="d-none d-sm-inline">Forum</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-2 px-md-3 small {{ request()->is('kelas/*/anggota') ? 'active' : '' }}"
                    href="{{ route('kelas.anggota', $kelas->id) }}">
                    <i class="bi bi-people"></i> <span class="d-none d-sm-inline">Anggota</span>
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
                        $pengumpulan = $tugas->pengumpulan()->where('siswa_nisn', session('identifier'))->first();
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

                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-calendar-event"></i> Deadline:
                                    {{ $tugas->deadline->format('d M Y, H:i') }}
                                </span>
                                <span class="badge bg-primary">
                                    Nilai: {{ $tugas->nilai_maksimal }}
                                </span>
                                <span class="badge bg-warning text-dark">
                                    KKM: {{ $tugas->kkm }}
                                </span>
                            </div>

                             @if (session('role') === 'siswa')
                                @if ($sudahSubmit)
                                    @php
                                        $isLulus = $nilaiSiswa && $nilaiSiswa >= $tugas->kkm;
                                        $bgColor = $nilaiSiswa ? ($isLulus ? 'bg-success' : 'bg-danger') : 'bg-info';
                                        $textColor = $nilaiSiswa ? ($isLulus ? 'text-success' : 'text-danger') : 'text-info';
                                        $icon = $nilaiSiswa ? ($isLulus ? 'bi-check-circle-fill' : 'bi-x-circle-fill') : 'bi-clock-fill';
                                        $statusText = $nilaiSiswa ? ($isLulus ? 'Lulus KKM' : 'Belum Lulus') : 'Menunggu Penilaian';
                                    @endphp
                                    
                                    <div class="card border-0 shadow-sm mb-2" style="background: linear-gradient(135deg, {{ $nilaiSiswa ? ($isLulus ? '#d4edda' : '#f8d7da') : '#d1ecf1' }} 0%, #ffffff 100%);">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi {{ $icon }} {{ $textColor }} fs-5"></i>
                                                    <span class="fw-semibold text-dark">{{ $statusText }}</span>
                                                </div>
                                                @if ($nilaiSiswa)
                                                    <div class="text-end">
                                                        <div class="badge {{ $bgColor }} px-3 py-2" style="font-size: 0.95rem;">
                                                            {{ $nilaiSiswa }} / {{ $tugas->nilai_maksimal }}
                                                        </div>
                                                        <small class="d-block mt-1 {{ $textColor }} fw-semibold">
                                                            KKM: {{ $tugas->kkm }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 px-3 mb-2 border-0 shadow-sm">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-exclamation-circle fs-5"></i>
                                            <span class="fw-semibold">Belum dikumpulkan</span>
                                        </div>
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
                                        <div class="mb-3">
                                            <label class="form-label">KKM *</label>
                                            <input type="number" name="kkm" class="form-control"
                                                value="{{ $tugas->kkm }}" min="0" max="100" step="0.01" required>
                                            <small class="text-muted">Kriteria Ketuntasan Minimal (0-100)</small>
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
                                    <input type="number" name="nilai_maksimal" id="nilai_maksimal" class="form-control" placeholder="100"
                                        value="100" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">KKM *</label>
                                <div class="input-group">
                                    <input type="number" name="kkm" id="kkm_input" class="form-control" placeholder="75"
                                        value="75" min="0" max="100" step="0.01" required>
                                    <span class="input-group-text">
                                        <input class="form-check-input mt-0" type="checkbox" id="use_mapel_kkm">
                                    </span>
                                </div>
                                <div class="form-check mt-2">
                                    <label class="form-check-label text-muted small" for="use_mapel_kkm">
                                        ☑ Gunakan KKM Mata Pelajaran
                                    </label>
                                </div>
                                <small class="text-muted">Kriteria Ketuntasan Minimal (0-100)</small>
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

        // Auto-fill KKM dari Mata Pelajaran (jika checkbox dicentang)
        document.addEventListener('DOMContentLoaded', function() {
            const useMapelKkmCheckbox = document.getElementById('use_mapel_kkm');
            const kkmInput = document.getElementById('kkm_input');
            
            if (useMapelKkmCheckbox) {
                useMapelKkmCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Di sini Anda bisa fetch KKM dari mata pelajaran
                        // Contoh: kkmInput.value = "75"; // Atau dari AJAX
                        kkmInput.readOnly = true;
                        kkmInput.classList.add('bg-light');
                    } else {
                        kkmInput.readOnly = false;
                        kkmInput.classList.remove('bg-light');
                    }
                });
            }
        });
    </script>
@endif