@extends('layouts.app')

@section('title', $tugas->judul)

@section('content')
    <div class="container-fluid py-4 px-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-{{ session('role') === 'guru' ? '12' : '8' }}">
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
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Deadline</small>
                                    <strong>
                                        <i class="bi bi-calendar-event text-danger"></i>
                                        {{ $tugas->deadline->format('d M Y, H:i') }}
                                    </strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Nilai Maksimal</small>
                                    <strong>
                                        <i class="bi bi-trophy text-warning"></i>
                                        {{ number_format($tugas->nilai_maksimal, 2) }}
                                    </strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">KKM</small>
                                    <strong>
                                        <i class="bi bi-bar-chart text-info"></i>
                                        {{ number_format($tugas->kkm, 0) }}
                                    </strong>
                                </div>
                                <div class="col-md-3">
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
                                  
                                {{-- Tambahkan tombol preview untuk PDF, JPG, dan PNG --}}
                                @php
                                    $extension = strtolower(pathinfo($tugas->file_contoh, PATHINFO_EXTENSION));
                                    $supportedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                                @endphp
                                
                                @if (in_array($extension, $supportedExtensions))
                                    <a href="{{ route('preview.file', $tugas->file_contoh) }}"
                                        class="btn btn-sm btn-outline-success ms-2" target="_blank">
                                        <i class="bi bi-eye"></i> Lihat File
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $tugas->file_contoh) }}"
                                        class="btn btn-sm btn-outline-primary ms-2" download>
                                        <i class="bi bi-download"></i> Unduh File
                                    </a>
                                @endif
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
                                                @php
                                                    $isLulusKkm = $pengumpulan->nilai >= $tugas->kkm;
                                                @endphp
                                                <div class="badge {{ $isLulusKkm ? 'bg-success' : 'bg-danger' }}"
                                                    style="font-size: 1.2rem; padding: 10px 15px;">
                                                    Nilai: {{ $pengumpulan->nilai }}
                                                </div>
                                                <small class="d-block mt-1 {{ $isLulusKkm ? 'text-success' : 'text-danger' }}">
                                                    {{ $isLulusKkm ? '✓ Lulus KKM' : '✗ Belum Lulus KKM' }}
                                                </small>
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

                                    <a href="{{ route('preview.file', $pengumpulan->file_path) }}"
                                        class="btn btn-sm btn-outline-success" target="_blank">
                                        <i class="bi bi-eye"></i> Lihat File
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

                <!-- Guru: Statistik & Daftar Pengumpulan -->
                @if (session('role') === 'guru' && session('identifier') === $tugas->kelas->guru_nip)
                    <!-- Card Statistik Pengumpulan -->
                    <div class="card shadow-sm border-0 mt-4 mb-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">📊 Statistik Pengumpulan Tugas</h6>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <small class="text-muted d-block mb-1">Total Pengumpulan</small>
                                        <strong class="fs-4 text-primary">{{ $tugas->pengumpulan->count() }}</strong>
                                        <small class="text-muted d-block">siswa</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <small class="text-muted d-block mb-1">Sudah Dinilai</small>
                                        <strong class="fs-4 text-success">{{ $tugas->pengumpulan->whereNotNull('nilai')->count() }}</strong>
                                        <small class="text-muted d-block">siswa</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <small class="text-muted d-block mb-1">Belum Dinilai</small>
                                        <strong class="fs-4 text-warning">{{ $tugas->pengumpulan->whereNull('nilai')->count() }}</strong>
                                        <small class="text-muted d-block">siswa</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <small class="text-muted d-block mb-1">Lulus KKM</small>
                                        <strong class="fs-4 text-success">{{ $tugas->pengumpulan->where('nilai', '>=', $tugas->kkm)->count() }}</strong>
                                        <small class="text-muted d-block">siswa</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <small class="text-muted d-block mb-1">Belum Lulus KKM</small>
                                        <strong class="fs-4 text-danger">{{ $tugas->pengumpulan->whereNotNull('nilai')->where('nilai', '<', $tugas->kkm)->count() }}</strong>
                                        <small class="text-muted d-block">siswa</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center p-3 bg-light rounded">
                                        <small class="text-muted d-block mb-1">Rata-rata Nilai</small>
                                        <strong class="fs-4 text-info">
                                            @php
                                                $avgNilai = $tugas->pengumpulan->whereNotNull('nilai')->avg('nilai');
                                            @endphp
                                            {{ $avgNilai ? number_format($avgNilai, 1) : '-' }}
                                        </strong>
                                        <small class="text-muted d-block">{{ $avgNilai ? 'poin' : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Daftar Pengumpulan -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">
                                    <i class="bi bi-people"></i> Daftar Pengumpulan
                                </h5>
                                @if ($tugas->pengumpulan->count() > 0)
                                    <div class="btn-group">
                                        <a href="{{ route('tugas.export.pdf', ['kelas' => $tugas->kelas_id, 'tugas' => $tugas->id]) }}" 
                                           class="btn btn-danger btn-sm">
                                            <i class="bi bi-file-pdf"></i> Export PDF
                                        </a>
                                        <a href="{{ route('tugas.export.excel', ['kelas' => $tugas->kelas_id, 'tugas' => $tugas->id]) }}" 
                                           class="btn btn-success btn-sm">
                                            <i class="bi bi-file-excel"></i> Export Excel
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if($tugas->pengumpulan->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 15%;">Nama Siswa</th>
                                                <th style="width: 10%;">NIPD</th>
                                                <th style="width: 25%;">Jawaban</th>
                                                <th style="width: 10%;">File</th>
                                                <th style="width: 12%;">Waktu Kumpul</th>
                                                <th style="width: 10%;">Nilai</th>
                                                <th style="width: 13%;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tugas->pengumpulan as $index => $p)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $p->siswa->nama ?? 'Siswa' }}</strong>
                                                    </td>
                                                    <td>{{ $p->siswa->nipd ?? '-' }}</td>
                                                    <td>
                                                        <div class="text-truncate" style="max-width: 250px;" 
                                                             data-bs-toggle="tooltip" 
                                                             title="{{ $p->jawaban }}">
                                                            {{ Str::limit($p->jawaban, 80) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($p->file_path)
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ asset('storage/' . $p->file_path) }}"
                                                                   class="btn btn-outline-secondary" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="Download File"
                                                                   download>
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                                <a href="{{ route('preview.file', $p->file_path) }}"
                                                                   class="btn btn-outline-success" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="Lihat File"
                                                                   target="_blank">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </div>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $p->created_at->format('d M Y') }}</small><br>
                                                        <small class="text-muted">{{ $p->created_at->format('H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        @if ($p->nilai)
                                                            @php
                                                                $isLulusKkm = $p->nilai >= $tugas->kkm;
                                                            @endphp
                                                            <span class="badge {{ $isLulusKkm ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $p->nilai }}
                                                            </span>
                                                            @if($p->catatan_guru)
                                                                <i class="bi bi-chat-left-text text-info ms-1" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="{{ $p->catatan_guru }}"></i>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="bi bi-hourglass-split"></i> Belum
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-primary" 
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#nilaiModal{{ $p->id }}">
                                                            <i class="bi bi-pencil"></i> Nilai
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- Modal Beri Nilai -->
                                                <div class="modal fade" id="nilaiModal{{ $p->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="bi bi-award"></i> Beri Nilai - {{ $p->siswa->nama_siswa ?? 'Siswa' }}
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('tugaspengumpulan.update', $p->id) }}" method="POST">
                                                                @csrf @method('PUT')
                                                                <div class="modal-body">
                                                                    <!-- Info Siswa -->
                                                                    <div class="alert alert-light border">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <small class="text-muted">NIPD:</small>
                                                                                <strong class="d-block">{{ $p->siswa->nipd ?? '-' }}</strong>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <small class="text-muted">Waktu Pengumpulan:</small>
                                                                                <strong class="d-block">{{ $p->created_at->format('d M Y, H:i') }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Jawaban Siswa -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Jawaban Siswa:</label>
                                                                        <div class="p-3 bg-light rounded" style="max-height: 200px; overflow-y: auto;">
                                                                            {{ $p->jawaban }}
                                                                        </div>
                                                                    </div>

                                                                    <!-- File Lampiran -->
                                                                    @if ($p->file_path)
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">File Lampiran:</label>
                                                                            <div>
                                                                                <a href="{{ asset('storage/' . $p->file_path) }}"
                                                                                   class="btn btn-sm btn-outline-secondary" download>
                                                                                    <i class="bi bi-download"></i> Download File
                                                                                </a>
                                                                                <a href="{{ route('preview.file', $p->file_path) }}"
                                                                                   class="btn btn-sm btn-outline-success" target="_blank">
                                                                                    <i class="bi bi-eye"></i> Lihat File
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <hr>

                                                                    <!-- Info KKM -->
                                                                    <div class="alert alert-info py-2 mb-3">
                                                                        <small>
                                                                            <i class="bi bi-info-circle"></i> 
                                                                            <strong>KKM Tugas: {{ $tugas->kkm }}</strong> | 
                                                                            Nilai Maksimal: {{ $tugas->nilai_maksimal }}
                                                                        </small>
                                                                    </div>

                                                                    <!-- Input Nilai -->
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-bold">Nilai *</label>
                                                                            <input type="number" name="nilai" class="form-control form-control-lg"
                                                                                   value="{{ $p->nilai }}" min="0"
                                                                                   max="{{ $tugas->nilai_maksimal }}" step="0.01" required>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-bold">Status KKM</label>
                                                                            <div class="form-control form-control-lg text-center" style="background-color: #f8f9fa;">
                                                                                @if($p->nilai)
                                                                                    @if($p->nilai >= $tugas->kkm)
                                                                                        <span class="badge bg-success fs-6">✓ Lulus KKM</span>
                                                                                    @else
                                                                                        <span class="badge bg-danger fs-6">✗ Belum Lulus</span>
                                                                                    @endif
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Catatan Guru -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Catatan untuk Siswa</label>
                                                                        <textarea name="catatan_guru" class="form-control" rows="4" 
                                                                                  placeholder="Berikan feedback atau catatan untuk siswa...">{{ $p->catatan_guru }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                        <i class="bi bi-x-circle"></i> Batal
                                                                    </button>
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="bi bi-check-circle"></i> Simpan Nilai
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">Belum ada siswa yang mengumpulkan tugas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar (Hanya untuk Siswa) -->
            @if (session('role') === 'siswa')
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

                            <hr>
                            <a href="{{ route('tugas.index', $tugas->kelas_id) }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-list"></i> Lihat Semua Tugas
                            </a>
                        </div>
                    </div>
                </div>
            @endif
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
                            <div class="mb-3">
                                <label class="form-label">KKM *</label>
                                <input type="number" name="kkm" class="form-control"
                                    value="{{ $tugas->kkm }}" min="0" max="100" step="0.01" required>
                                <small class="text-muted">Kriteria Ketuntasan Minimal (0-100)</small>
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

        // Inisialisasi Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection