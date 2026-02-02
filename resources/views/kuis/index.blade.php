@extends('layouts.app')

@section('title', 'Kuis - ' . $kelas->nama_kelas)

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
                <a class="nav-link px-2 px-md-3 small" href="{{ route('tugas.index', $kelas->id) }}">
                    <i class="bi bi-clipboard-check"></i> <span class="d-none d-sm-inline">Tugas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active px-2 px-md-3 small" href="{{ route('kuis.index', $kelas->id) }}">
                    <i class="bi bi-patch-question"></i> <span class="d-none d-sm-inline">Kuis</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-2 px-md-3 small" href="{{ route('kelas.forum', $kelas->id) }}">
                    <i class="bi bi-chat-dots"></i> <span class="d-none d-sm-inline">Forum</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-2 px-md-3 small" href="{{ route('kelas.anggota', $kelas->id) }}">
                    <i class="bi bi-people"></i> <span class="d-none d-sm-inline">Anggota</span>
                </a>
            </li>
        </ul>

        <!-- Header Kuis -->
        @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Kuis</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKuis">
                    <i class="bi bi-plus-lg"></i> Buat Kuis
                </button>
            </div>
        @else
            <h5 class="mb-3">Daftar Kuis</h5>
        @endif

        <!-- List Kuis -->
        <div class="row g-3">
            @forelse($kuis as $item)
                @php
                    // Check if siswa sudah mengerjakan
                    $sudahMengerjakan = false;
                    $attemptSiswa = null;
                    if (session('role') === 'siswa') {
                        $attemptSiswa = $item->attempts()->where('siswa_nisn', session('identifier'))->first();
                        $sudahMengerjakan = !empty($attemptSiswa);
                    }
                    
                    // Cek status kuis (aktif/tidak)
                    $now = now();
                    $isAktif = $now->between($item->tanggal_mulai, $item->tanggal_selesai);
                    $belumMulai = $now->lt($item->tanggal_mulai);
                    $sudahSelesai = $now->gt($item->tanggal_selesai);
                @endphp

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $item->judul }}</h6>
                                    <p class="text-secondary small mb-2">{{ Str::limit($item->deskripsi, 100) }}</p>
                                </div>

                                @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form id="delete-kuis-{{ $item->id }}"
                                                    action="{{ route('kuis.destroy', ['kelas' => $item->kelas_id, 'kuis' => $item->id]) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button type="button"
                                                        class="dropdown-item text-danger"
                                                        onclick="confirmDeleteKuis({{ $item->id }})">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-calendar-event"></i> Mulai:
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y, H:i') }}
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-calendar-x"></i> Selesai:
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y, H:i') }}
                                </span>
                                <span class="badge bg-primary">
                                    <i class="bi bi-clock"></i> Durasi: {{ $item->durasi }} menit
                                </span>
                                <span class="badge bg-info">
                                    <i class="bi bi-question-circle"></i> {{ $item->jumlah_soal }} Soal
                                </span>
                            </div>

                            @if (session('role') === 'siswa')
                                @if ($sudahMengerjakan)
                                    @php
                                        // Tentukan status berdasarkan nilai (standar KKM 70)
                                        $nilai = $attemptSiswa->nilai_akhir ?? 0;
                                        $isLulus = $nilai >= 70;
                                        $bgColor = $isLulus ? '#d4edda' : '#f8d7da';
                                        $textColor = $isLulus ? 'text-success' : 'text-danger';
                                        $icon = $isLulus ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
                                        $statusText = $isLulus ? 'Lulus' : 'Tidak Lulus';
                                    @endphp
                                    
                                    <div class="card border-0 shadow-sm mb-2" style="background: linear-gradient(135deg, {{ $bgColor }} 0%, #ffffff 100%);">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi {{ $icon }} {{ $textColor }} fs-5"></i>
                                                    <div>
                                                        <div class="fw-semibold {{ $textColor }}">{{ $statusText }}</div>
                                                        <small class="text-muted">Nilai: {{ $nilai }}</small>
                                                    </div>
                                                </div>
                                                <a href="{{ route('kuis.siswa.hasil', $item->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye"></i> Lihat Hasil
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @if ($belumMulai)
                                        <div class="alert alert-warning py-2 px-3 mb-2">
                                            <i class="bi bi-hourglass-split"></i> Kuis belum dimulai
                                        </div>
                                    @elseif ($sudahSelesai)
                                        <div class="alert alert-secondary py-2 px-3 mb-2">
                                            <i class="bi bi-clock-history"></i> Kuis telah berakhir
                                        </div>
                                    @else
                                        <a href="{{ route('kuis.siswa.show', $item->id) }}" class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-play-circle"></i> Mulai Kuis
                                        </a>
                                    @endif
                                @endif
                            @endif

                            @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                                <a href="{{ route('kuis.show', $item->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                    <span class="badge bg-primary ms-1">{{ $item->attempts_count }}</span>
                                </a>
                            @endif

                            <!-- Status Badge -->
                            <div class="mt-2">
                                @if ($belumMulai)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i> Belum Dimulai
                                    </span>
                                @elseif ($isAktif)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle"></i> Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-patch-question fs-1 text-muted"></i>
                        <p class="text-secondary mt-3">Belum ada kuis</p>
                        @if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                data-bs-target="#modalTambahKuis">
                                <i class="bi bi-plus-lg"></i> Buat Kuis Pertama
                            </button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

    </div>
@endsection

<!-- Modal Tambah Kuis dengan Bank Soal (Only for Guru) -->
@if (session('role') === 'guru' && session('identifier') === $kelas->guru_nip)
    @section('modal')
        <div class="modal fade" id="modalTambahKuis" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-semibold">Buat Kuis Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('kuis.store', $kelas->id) }}" id="formKuis">
                        @csrf
                        <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                            <!-- Informasi Kuis Section -->
                            <h6 class="fw-semibold mb-3">Informasi Kuis</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Judul Kuis *</label>
                                <input type="text" name="judul" class="form-control"
                                    placeholder="Contoh: Kuis Persamaan Kuadrat" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="2"
                                    placeholder="Deskripsi kuis"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Durasi (menit) *</label>
                                    <input type="number" name="durasi" class="form-control" 
                                        placeholder="30" min="1" value="30" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Tanggal Mulai *</label>
                                    <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Tanggal Berakhir *</label>
                                    <input type="datetime-local" name="tanggal_selesai" class="form-control" required>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 px-3 mb-4">
                                <i class="bi bi-lightbulb"></i> 
                                <small><strong>10 soal pilihan ganda</strong> akan dipilih secara acak dari bank soal untuk setiap siswa</small>
                            </div>

                            <hr class="my-4">

                            <!-- Bank Soal Pilihan Ganda Section -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-semibold mb-0">Bank Soal Pilihan Ganda</h6>
                                <span class="badge bg-primary" id="soalCounter">0 soal</span>
                            </div>

                            <!-- Form Input Soal Baru -->
                            <div class="card mb-3 border-primary" id="formInputSoal">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Form Buat Soal</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Pertanyaan *</label>
                                        <textarea id="inputPertanyaan" class="form-control" rows="3" 
                                            placeholder="Masukkan pertanyaan..."></textarea>
                                    </div>
                                    
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Opsi A *</label>
                                            <input type="text" id="inputOpsiA" class="form-control" 
                                                placeholder="Jawaban A">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Opsi B *</label>
                                            <input type="text" id="inputOpsiB" class="form-control" 
                                                placeholder="Jawaban B">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Opsi C *</label>
                                            <input type="text" id="inputOpsiC" class="form-control" 
                                                placeholder="Jawaban C">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Opsi D *</label>
                                            <input type="text" id="inputOpsiD" class="form-control" 
                                                placeholder="Jawaban D">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Jawaban Benar *</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="inputJawabanBenar" 
                                                id="inputJawaban_A" value="A">
                                            <label class="btn btn-outline-primary" for="inputJawaban_A">A</label>
                                            
                                            <input type="radio" class="btn-check" name="inputJawabanBenar" 
                                                id="inputJawaban_B" value="B">
                                            <label class="btn btn-outline-primary" for="inputJawaban_B">B</label>
                                            
                                            <input type="radio" class="btn-check" name="inputJawabanBenar" 
                                                id="inputJawaban_C" value="C">
                                            <label class="btn btn-outline-primary" for="inputJawaban_C">C</label>
                                            
                                            <input type="radio" class="btn-check" name="inputJawabanBenar" 
                                                id="inputJawaban_D" value="D">
                                            <label class="btn btn-outline-primary" for="inputJawaban_D">D</label>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary w-100" onclick="tambahSoalDariForm()">
                                        <i class="bi bi-plus-circle"></i> Tambah Soal ke Bank
                                    </button>
                                </div>
                            </div>

                            <div id="bankSoalContainer">
                                <!-- Soal yang sudah ditambahkan akan muncul di sini -->
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" onclick="validateAndSubmit()">
                                <i class="bi bi-check-circle"></i> Tambahkan soal kuis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
    <script>
        let soalCount = 0;
        const MAX_SOAL = 30; // Maximum allowed questions

        function tambahSoalDariForm() {
            // Validasi input
            const pertanyaan = document.getElementById('inputPertanyaan').value.trim();
            const opsiA = document.getElementById('inputOpsiA').value.trim();
            const opsiB = document.getElementById('inputOpsiB').value.trim();
            const opsiC = document.getElementById('inputOpsiC').value.trim();
            const opsiD = document.getElementById('inputOpsiD').value.trim();
            const jawabanBenar = document.querySelector('input[name="inputJawabanBenar"]:checked');

            // Cek apakah semua field sudah diisi
            if (!pertanyaan || !opsiA || !opsiB || !opsiC || !opsiD || !jawabanBenar) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap!',
                    text: 'Semua field harus diisi dan jawaban benar harus dipilih.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (soalCount >= MAX_SOAL) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Maksimal!',
                    text: `Maksimal ${MAX_SOAL} soal dapat ditambahkan.`,
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            soalCount++;
            const container = document.getElementById('bankSoalContainer');
            
            const soalHTML = `
                <div class="card mb-3 soal-item shadow-sm" id="soal-${soalCount}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <span class="fw-semibold">Soal #${soalCount}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusSoal(${soalCount})">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pertanyaan</label>
                            <div class="alert alert-secondary mb-0 py-2">${escapeHtml(pertanyaan)}</div>
                            <textarea name="soal[${soalCount}][pertanyaan]" class="form-control d-none" required>${escapeHtml(pertanyaan)}</textarea>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Opsi A</label>
                                <div class="alert alert-light mb-0 py-2 ${jawabanBenar.value === 'A' ? 'border-success border-2' : ''}">${escapeHtml(opsiA)}</div>
                                <input type="hidden" name="soal[${soalCount}][opsi_a]" value="${escapeHtml(opsiA)}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Opsi B</label>
                                <div class="alert alert-light mb-0 py-2 ${jawabanBenar.value === 'B' ? 'border-success border-2' : ''}">${escapeHtml(opsiB)}</div>
                                <input type="hidden" name="soal[${soalCount}][opsi_b]" value="${escapeHtml(opsiB)}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Opsi C</label>
                                <div class="alert alert-light mb-0 py-2 ${jawabanBenar.value === 'C' ? 'border-success border-2' : ''}">${escapeHtml(opsiC)}</div>
                                <input type="hidden" name="soal[${soalCount}][opsi_c]" value="${escapeHtml(opsiC)}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Opsi D</label>
                                <div class="alert alert-light mb-0 py-2 ${jawabanBenar.value === 'D' ? 'border-success border-2' : ''}">${escapeHtml(opsiD)}</div>
                                <input type="hidden" name="soal[${soalCount}][opsi_d]" value="${escapeHtml(opsiD)}" required>
                            </div>
                        </div>
                        
                        <div class="alert alert-success py-2">
                            <i class="bi bi-check-circle-fill"></i> <strong>Jawaban Benar: ${jawabanBenar.value}</strong>
                        </div>
                        <input type="hidden" name="soal[${soalCount}][jawaban_benar]" value="${jawabanBenar.value}" required>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', soalHTML);
            updateSoalCounter();
            
            // Reset form
            resetFormInput();
            
            // Scroll to new soal
            setTimeout(() => {
                const newSoal = document.getElementById(`soal-${soalCount}`);
                if (newSoal) {
                    newSoal.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest'
                    });
                }
            }, 100);

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Soal Ditambahkan!',
                text: `Soal #${soalCount} berhasil ditambahkan ke bank soal.`,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function resetFormInput() {
            document.getElementById('inputPertanyaan').value = '';
            document.getElementById('inputOpsiA').value = '';
            document.getElementById('inputOpsiB').value = '';
            document.getElementById('inputOpsiC').value = '';
            document.getElementById('inputOpsiD').value = '';
            
            const radios = document.querySelectorAll('input[name="inputJawabanBenar"]');
            radios.forEach(radio => radio.checked = false);
        }

        function tambahSoal() {
            if (soalCount >= MAX_SOAL) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Maksimal!',
                    text: `Maksimal ${MAX_SOAL} soal dapat ditambahkan.`,
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            soalCount++;
            const container = document.getElementById('bankSoalContainer');
            
            const soalHTML = `
                <div class="card mb-3 soal-item shadow-sm" id="soal-${soalCount}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <span class="fw-semibold">Soal #${soalCount}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusSoal(${soalCount})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pertanyaan *</label>
                            <textarea name="soal[${soalCount}][pertanyaan]" class="form-control" rows="2" 
                                placeholder="Masukkan pertanyaan..." required></textarea>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi A *</label>
                                <input type="text" name="soal[${soalCount}][opsi_a]" class="form-control form-control-sm" 
                                    placeholder="Jawaban A" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi B *</label>
                                <input type="text" name="soal[${soalCount}][opsi_b]" class="form-control form-control-sm" 
                                    placeholder="Jawaban B" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi C *</label>
                                <input type="text" name="soal[${soalCount}][opsi_c]" class="form-control form-control-sm" 
                                    placeholder="Jawaban C" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small text-muted">Opsi D *</label>
                                <input type="text" name="soal[${soalCount}][opsi_d]" class="form-control form-control-sm" 
                                    placeholder="Jawaban D" required>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label class="form-label fw-semibold">Jawaban Benar *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="soal[${soalCount}][jawaban_benar]" 
                                    id="soal${soalCount}_A" value="A" required>
                                <label class="btn btn-outline-primary" for="soal${soalCount}_A">A</label>
                                
                                <input type="radio" class="btn-check" name="soal[${soalCount}][jawaban_benar]" 
                                    id="soal${soalCount}_B" value="B">
                                <label class="btn btn-outline-primary" for="soal${soalCount}_B">B</label>
                                
                                <input type="radio" class="btn-check" name="soal[${soalCount}][jawaban_benar]" 
                                    id="soal${soalCount}_C" value="C">
                                <label class="btn btn-outline-primary" for="soal${soalCount}_C">C</label>
                                
                                <input type="radio" class="btn-check" name="soal[${soalCount}][jawaban_benar]" 
                                    id="soal${soalCount}_D" value="D">
                                <label class="btn btn-outline-primary" for="soal${soalCount}_D">D</label>
                            </div>
                        </div>

                        ${soalCount < MAX_SOAL ? `
                        <button type="button" class="btn btn-primary w-100 mt-3" onclick="tambahSoal()">
                            <i class="bi bi-plus-circle"></i> Tambah Soal ke Bank
                        </button>
                        ` : ''}
                    </div>
                </div>
            `;
            
            // Remove "Tambah Soal" button from previous soal
            const prevButtons = container.querySelectorAll('.btn.btn-primary.w-100.mt-3');
            prevButtons.forEach(btn => btn.remove());
            
            container.insertAdjacentHTML('beforeend', soalHTML);
            updateSoalCounter();
            
            // Scroll to new soal
            setTimeout(() => {
                const newSoal = document.getElementById(`soal-${soalCount}`);
                if (newSoal) {
                    newSoal.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest'
                    });
                }
            }, 100);
        }

        function hapusSoal(id) {
            Swal.fire({
                title: 'Hapus Soal?',
                text: "Soal ini akan dihapus dari bank soal.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const soalElement = document.getElementById(`soal-${id}`);
                    if (soalElement) {
                        soalElement.remove();
                        updateSoalCounter();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Soal berhasil dihapus.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }

        function updateSoalCounter() {
            const count = document.querySelectorAll('.soal-item').length;
            document.getElementById('soalCounter').textContent = count + ' soal';
        }

        function validateAndSubmit() {
            const totalSoal = document.querySelectorAll('.soal-item').length;
            
            if (totalSoal < 10) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Soal Kurang!',
                    text: `Anda baru menambahkan ${totalSoal} soal. Minimal 10 soal diperlukan.`,
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            // Check if all soal have jawaban_benar (either from radio or hidden input)
            let allValid = true;
            const soalItems = document.querySelectorAll('.soal-item');
            
            soalItems.forEach((item) => {
                // Check for hidden input first (from form submission)
                const hiddenInput = item.querySelector('input[name*="[jawaban_benar]"][type="hidden"]');
                
                // If hidden input exists and has value, it's valid
                if (hiddenInput && hiddenInput.value) {
                    return; // continue to next iteration
                }
                
                // Otherwise check radio buttons
                const radios = item.querySelectorAll('input[type="radio"][name*="[jawaban_benar]"]');
                const checked = Array.from(radios).some(radio => radio.checked);
                
                if (!checked) {
                    allValid = false;
                }
            });
            
            if (!allValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap!',
                    text: 'Pastikan semua soal memiliki jawaban benar yang dipilih.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            // Show loading and submit form
            Swal.fire({
                title: 'Menyimpan Kuis...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            document.getElementById('formKuis').submit();
        }

        function confirmDeleteKuis(id) {
            Swal.fire({
                title: 'Hapus Kuis?',
                text: "Kuis beserta data terkait akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-kuis-${id}`).submit();
                }
            })
        }

        // Auto-add first question on modal open
        document.getElementById('modalTambahKuis').addEventListener('shown.bs.modal', function () {
            // Focus on pertanyaan field when modal opens
            document.getElementById('inputPertanyaan').focus();
        });
        
        // Reset on modal close
        document.getElementById('modalTambahKuis').addEventListener('hidden.bs.modal', function () {
            soalCount = 0;
            document.getElementById('bankSoalContainer').innerHTML = '';
            updateSoalCounter();
            resetFormInput();
            document.getElementById('formKuis').reset();
        });
    </script>
@endif