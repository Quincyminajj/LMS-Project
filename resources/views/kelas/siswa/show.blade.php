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
                        <p class="text-secondary mb-2">
                            <i class="bi bi-person-circle"></i> {{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}
                            • <i class="bi bi-key"></i> Kode: {{ $kelas->kode_kelas }}
                        </p>
                        <p class="text-muted small mb-0">{{ $kelas->deskripsi }}</p>
                    </div>

                    <!-- Badge Status Siswa -->
                    <span class="badge bg-success fs-6">
                        <i class="bi bi-check-circle"></i> Anggota Kelas
                    </span>
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
            <li class="nav-item">
                <a class="nav-link {{ request()->is('kelas/*/anggota') ? 'active' : '' }}"
                    href="{{ route('kelas.anggota', $kelas->id) }}">
                    <i class="bi bi-people"></i> Anggota
                </a>
            </li>
        </ul>

        <!-- ========================================= -->
        <!-- TAB KONTEN -->
        <!-- ========================================= -->
        @if (!request()->is('kelas/*/tugas') && !request()->is('kelas/*/forum'))

            <h5 class="mb-3">Materi Pembelajaran</h5>

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
                                    <small class="text-muted">{{ $konten->created_at->format('d M Y') }}</small>
                                </div>

                                <h6 class="fw-bold mb-2">{{ $konten->judul }}</h6>

                                @if ($konten->tipe == 'file')
                                    @if($konten->deskripsi)
                                        <p class="text-secondary small mb-2">{{ Str::limit($konten->deskripsi, 100) }}</p>
                                    @endif
                                    <p class="text-muted small mb-3">
                                        <i class="bi bi-paperclip"></i> {{ $konten->isi }}
                                    </p>
                                @elseif($konten->tipe == 'link')
                                    @if($konten->deskripsi)
                                        <p class="text-secondary small mb-2">{{ Str::limit($konten->deskripsi, 100) }}</p>
                                    @endif
                                    <p class="text-secondary small mb-3 text-truncate">
                                        <i class="bi bi-link-45deg"></i> {{ $konten->isi }}
                                    </p>
                                @else
                                    <p class="text-secondary small mb-3">{{ Str::limit($konten->isi, 100) }}</p>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    @if ($konten->tipe == 'file')
                                    <div class="d-flex gap-2">
                                        <a href="{{ asset('storage/' . $konten->file_path) }}"
                                        class="btn btn-outline-primary btn-sm"
                                        download>
                                            <i class="bi bi-download"></i>
                                        </a>

                                        <a href="{{ route('preview.file', $konten->file_path) }}"
                                        class="btn btn-outline-success btn-sm"
                                        target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                    @elseif($konten->tipe == 'link')
                                        <a href="{{ $konten->isi }}" target="_blank" class="btn btn-primary btn-sm">
                                            <i class="bi bi-box-arrow-up-right"></i> Buka Link
                                        </a>
                                    @else
                                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#viewTextModal{{ $konten->id }}">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </button>
                                    @endif

                                    <small class="text-muted">{{ $konten->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal View Text Content -->
                    @if ($konten->tipe == 'teks')
                        <div class="modal fade" id="viewTextModal{{ $konten->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ $konten->judul }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-calendar"></i> {{ $konten->created_at->format('d M Y, H:i') }}
                                        </p>
                                        <div class="border-top pt-3">
                                            <p style="white-space: pre-wrap;">{{ $konten->isi }}</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-secondary mt-3 mb-0">Belum ada materi pembelajaran</p>
                                <small class="text-muted">Guru belum menambahkan konten</small>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

        @endif

        <!-- ========================================= -->
        <!-- TAB TUGAS -->
        <!-- ========================================= -->
        @if (request()->is('kelas/*/tugas'))

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Tugas</h5>
                <span class="badge bg-primary">{{ $kelas->tugas->count() }} Tugas</span>
            </div>

            <div class="row g-3">
                @forelse($kelas->tugas as $tugas)
                    @php
                        $sudahDikumpulkan = isset($tugasDikumpulkan) && in_array($tugas->id, $tugasDikumpulkan);
                        $pengumpulan = $sudahDikumpulkan
                            ? \App\Models\TugasPengumpulan::where('tugas_id', $tugas->id)
                                ->where('siswa_nisn', session('identifier'))
                                ->first()
                            : null;

                        $deadline = \Carbon\Carbon::parse($tugas->deadline);
                        $now = \Carbon\Carbon::now();
                        $isLate = $now->greaterThan($deadline);
                    @endphp

                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold mb-2">{{ $tugas->judul }}</h5>
                                        <p class="text-muted mb-3">{{ $tugas->deskripsi }}</p>

                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event"></i>
                                                    Deadline: {{ $deadline->format('d M Y, H:i') }}
                                                </small>
                                                @if ($isLate && !$sudahDikumpulkan)
                                                    <span class="badge bg-danger ms-2">Terlambat</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="bi bi-star"></i>
                                                    Nilai Maksimal: {{ $tugas->nilai_maksimal }}
                                                </small>
                                            </div>
                                        </div>

                                        @if ($sudahDikumpulkan)
                                            <!-- Status Sudah Dikumpulkan -->
                                            <div class="alert alert-success border-0 mb-0">
                                                <div class="d-flex align-items-start">
                                                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">Tugas Sudah Dikumpulkan</h6>
                                                        <p class="mb-2 small">
                                                            Dikumpulkan:
                                                            {{ $pengumpulan->created_at->format('d M Y, H:i') }}
                                                        </p>
                                                        @if ($pengumpulan->nilai)
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-success fs-6">
                                                                    Nilai:
                                                                    {{ $pengumpulan->nilai }}/{{ $tugas->nilai_maksimal }}
                                                                </span>
                                                            </div>
                                                            @if ($pengumpulan->feedback)
                                                                <div class="mt-2 p-2 bg-light rounded">
                                                                    <small class="fw-semibold">Feedback Guru:</small>
                                                                    <p class="mb-0 small">{{ $pengumpulan->feedback }}</p>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-warning">Menunggu Penilaian</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Tombol Kumpulkan -->
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalKumpulkanTugas{{ $tugas->id }}">
                                                <i class="bi bi-upload"></i> Kumpulkan Tugas
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Kumpulkan Tugas -->
                    @if (!$sudahDikumpulkan)
                        <div class="modal fade" id="modalKumpulkanTugas{{ $tugas->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Kumpulkan: {{ $tugas->judul }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('tugas.submit', $tugas->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="tugas_id" value="{{ $tugas->id }}">
                                        <input type="hidden" name="siswa_nisn" value="{{ session('identifier') }}">

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tipe Pengumpulan *</label>
                                                <select name="tipe" id="tipeSubmit{{ $tugas->id }}"
                                                    class="form-select" onchange="toggleSubmitInput({{ $tugas->id }})"
                                                    required>
                                                    <option value="file">Upload File</option>
                                                    <option value="link">Link URL</option>
                                                    <option value="teks">Teks Jawaban</option>
                                                </select>
                                            </div>

                                            <!-- File Upload -->
                                            <div id="submitFile{{ $tugas->id }}" class="mb-3">
                                                <label class="form-label fw-semibold">Upload File *</label>
                                                <input type="file" name="file_path" class="form-control">
                                                <small class="text-muted">Format: PDF, Word, Excel, Gambar (Max:
                                                    10MB)</small>
                                            </div>

                                            <!-- Link -->
                                            <div id="submitLink{{ $tugas->id }}" class="mb-3 d-none">
                                                <label class="form-label fw-semibold">URL Link *</label>
                                                <input type="url" name="isi" class="form-control"
                                                    placeholder="https://example.com">
                                            </div>

                                            <!-- Text -->
                                            <div id="submitTeks{{ $tugas->id }}" class="mb-3 d-none">
                                                <label class="form-label fw-semibold">Jawaban Anda *</label>
                                                <textarea name="isi" class="form-control" rows="6" placeholder="Tulis jawaban Anda di sini..."></textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send"></i> Kumpulkan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                                <p class="text-secondary mt-3 mb-0">Belum ada tugas</p>
                                <small class="text-muted">Guru belum memberikan tugas</small>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

        @endif

    </div>

    <!-- JavaScript untuk Toggle Input -->
    <script>
        function toggleSubmitInput(tugasId) {
            const tipe = document.getElementById('tipeSubmit' + tugasId).value;

            document.getElementById('submitFile' + tugasId).classList.add('d-none');
            document.getElementById('submitLink' + tugasId).classList.add('d-none');
            document.getElementById('submitTeks' + tugasId).classList.add('d-none');

            if (tipe === 'file') {
                document.getElementById('submitFile' + tugasId).classList.remove('d-none');
            } else if (tipe === 'link') {
                document.getElementById('submitLink' + tugasId).classList.remove('d-none');
            } else if (tipe === 'teks') {
                document.getElementById('submitTeks' + tugasId).classList.remove('d-none');
            }
        }
    </script>
@endsection