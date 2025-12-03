@extends('layouts.app')

@section('title', 'Anggota Kelas - ' . $kelas->nama_kelas)

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
                    <p class="text-secondary mb-2">{{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }} • Kode: {{ $kelas->kode_kelas }}</p>
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
            <a class="nav-link" href="{{ route('kelas.forum', $kelas->id) }}">
                <i class="bi bi-chat-dots"></i> Forum
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('kelas.anggota', $kelas->id) }}">
                <i class="bi bi-people"></i> Anggota
            </a>
        </li>
    </ul>

    <!-- Header Daftar Siswa -->
    @if(session('role') === 'guru')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Siswa Terdaftar</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                <i class="bi bi-person-plus"></i> Tambah Siswa
            </button>
        </div>
    @else
        <h5 class="mb-3">Daftar Siswa Terdaftar</h5>
    @endif

    <!-- Daftar Siswa -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($kelas->anggota->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4" style="width: 50px;">No</th>
                                <th>Nama Siswa</th>
                                <th>NIPD</th>
                                <th>No. HP</th>
                                <th>Bergabung</th>
                                @if(session('role') === 'guru')
                                <th class="text-center" style="width: 100px;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas->anggota as $index => $anggota)
                            <tr>
                                <td class="px-4">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($anggota->siswa && $anggota->siswa->foto)
                                            <img src="{{ asset('storage/' . $anggota->siswa->foto) }}" 
                                                 alt="Foto" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 35px; height: 35px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 35px; height: 35px;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                        @endif
                                        <span class="fw-semibold">{{ $anggota->siswa->nama ?? 'Nama tidak tersedia' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $anggota->siswa->nipd ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-phone"></i> 
                                        {{ $anggota->siswa->hp ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-check"></i>
                                        {{ \Carbon\Carbon::parse($anggota->joined_at)->format('d M Y') }}
                                    </small>
                                </td>
                                @if(session('role') === 'guru')
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="hapusAnggota({{ $anggota->id }}, '{{ addslashes($anggota->siswa->nama ?? 'Siswa') }}', {{ $kelas->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <p class="text-secondary mt-3 mb-0">Belum ada siswa yang bergabung</p>
                    <small class="text-muted">Bagikan kode kelas untuk mengundang siswa</small>
                    @if(session('role') === 'guru')
                        <br>
                        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                            <i class="bi bi-person-plus"></i> Tambah Siswa Pertama
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Tambah Siswa (Guru Only) -->
@if(session('role') === 'guru')
<div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Siswa ke Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('kelas-anggotas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" id="selectedNisn" name="siswa_nisn" value="">
                
                <div class="modal-body">
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Cari Siswa *</label>
                        <input type="text" 
                               id="inputSearch"
                               class="form-control" 
                               placeholder="Ketik NISN atau Nama Siswa (min. 3 karakter)"
                               autocomplete="off"
                               required>
                        <small class="text-muted">Cari berdasarkan NISN atau Nama</small>
                        
                        <!-- Dropdown Hasil Pencarian -->
                        <div id="siswaDropdown" class="list-group position-absolute w-100 shadow-lg" style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto;"></div>
                    </div>

                    <!-- Info Siswa Terpilih -->
                    <div id="siswaInfo" class="alert alert-success" style="display: none;">
                        <i class="bi bi-check-circle"></i>
                        <strong>Siswa Dipilih:</strong>
                        <div id="siswaDetail" class="mt-2"></div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Info:</strong> Ketik minimal 3 karakter untuk mencari siswa berdasarkan NISN atau Nama.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitSiswa" disabled>
                        <i class="bi bi-plus-lg"></i> Tambah Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
// ========== HAPUS ANGGOTA ==========
function hapusAnggota(anggotaId, namaSiswa, kelasId) {
    Swal.fire({
        title: 'Keluarkan Siswa?',
        html: `Apakah Anda yakin ingin mengeluarkan <strong>${namaSiswa}</strong> dari kelas ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Keluarkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/kelas-anggotas/${anggotaId}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ========== AUTOCOMPLETE SISWA (NISN & NAMA) ==========
document.addEventListener('DOMContentLoaded', function() {
    const inputSearch = document.getElementById('inputSearch');
    const selectedNisn = document.getElementById('selectedNisn');
    const dropdown = document.getElementById('siswaDropdown');
    const siswaInfo = document.getElementById('siswaInfo');
    const siswaDetail = document.getElementById('siswaDetail');
    const btnSubmit = document.getElementById('btnSubmitSiswa');
    
    let selectedSiswa = null;
    let searchTimeout = null;

    // Event ketika user mengetik
    inputSearch.addEventListener('input', function() {
        const keyword = this.value.trim();
        
        // Reset jika input kosong
        if (keyword.length === 0) {
            dropdown.style.display = 'none';
            siswaInfo.style.display = 'none';
            btnSubmit.disabled = true;
            selectedSiswa = null;
            selectedNisn.value = '';
            return;
        }

        // Cari siswa jika sudah 3 karakter
        if (keyword.length >= 3) {
            // Debounce: tunggu 300ms setelah user berhenti mengetik
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchSiswa(keyword);
            }, 300);
        } else {
            dropdown.style.display = 'none';
        }
    });

    // Fungsi untuk search siswa via AJAX
    function searchSiswa(keyword) {
        dropdown.innerHTML = '<div class="list-group-item text-center"><span class="spinner-border spinner-border-sm"></span> Mencari...</div>';
        dropdown.style.display = 'block';

        fetch(`{{ route('api.siswa.search') }}?keyword=${encodeURIComponent(keyword)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    dropdown.innerHTML = '<div class="list-group-item text-muted"><i class="bi bi-info-circle"></i> Tidak ada siswa ditemukan</div>';
                } else {
                    dropdown.innerHTML = '';
                    data.forEach(siswa => {
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action';
                        
                        // Highlight keyword yang cocok
                        const namaHighlight = highlightText(siswa.nama, keyword);
                        const nisnHighlight = highlightText(siswa.nisn, keyword);
                        
                        item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${namaHighlight}</strong><br>
                                    <small class="text-muted">NISN: ${nisnHighlight} | NIPD: ${siswa.nipd || '-'}</small>
                                </div>
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                        `;
                        
                        // Event click untuk memilih siswa
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectSiswa(siswa);
                        });
                        
                        dropdown.appendChild(item);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                dropdown.innerHTML = '<div class="list-group-item text-danger"><i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan</div>';
            });
    }

    // Fungsi untuk highlight text yang cocok
    function highlightText(text, keyword) {
        if (!text) return '-';
        const regex = new RegExp(`(${keyword})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    // Fungsi ketika siswa dipilih
    function selectSiswa(siswa) {
        selectedSiswa = siswa;
        inputSearch.value = `${siswa.nama} (${siswa.nisn})`;
        selectedNisn.value = siswa.nisn;
        dropdown.style.display = 'none';
        
        // Tampilkan info siswa terpilih
        siswaDetail.innerHTML = `
            <strong>${siswa.nama}</strong><br>
            <small>NISN: ${siswa.nisn} | NIPD: ${siswa.nipd || '-'}</small>
        `;
        siswaInfo.style.display = 'block';
        btnSubmit.disabled = false;
    }

    // Sembunyikan dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        if (!inputSearch.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Reset form ketika modal ditutup
    document.getElementById('modalTambahSiswa').addEventListener('hidden.bs.modal', function() {
        inputSearch.value = '';
        selectedNisn.value = '';
        dropdown.style.display = 'none';
        siswaInfo.style.display = 'none';
        btnSubmit.disabled = true;
        selectedSiswa = null;
    });
});
</script>
@endsection