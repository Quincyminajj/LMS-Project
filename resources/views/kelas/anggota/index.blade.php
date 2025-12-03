@extends('layouts.app')

@section('title', 'Anggota Kelas - ' . $kelas->nama_kelas)

@section('content')
<div class="container py-4">

    <!-- Header -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <a href="{{ route('kelas.show', $kelas->id) }}" class="text-decoration-none text-secondary mb-2 d-inline-block">
                <i class="bi bi-arrow-left"></i> Kembali ke Kelas
            </a>
            <h3 class="fw-bold mb-2">Daftar Siswa</h3>
            <p class="text-secondary mb-0">
                <i class="bi bi-book"></i> {{ $kelas->nama_kelas }} 
                <span class="mx-2">•</span>
                <i class="bi bi-person-circle"></i> {{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}
            </p>
        </div>
    </div>

    <!-- Daftar Siswa -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-people"></i> Daftar Siswa Terdaftar
                </h5>
                
                @if(session('role') === 'guru')
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                    <i class="bi bi-person-plus"></i> Tambah Siswa
                </button>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            @if($kelas->anggota->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4" style="width: 50px;">No</th>
                                <th>Nama Siswa</th>
                                <th>Email</th>
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
                                        <i class="bi bi-envelope"></i> 
                                        {{ $anggota->siswa->email ?? '-' }}
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
                                    <form id="hapus-anggota-{{ $anggota->id }}" 
                                          action="{{ route('kelas-anggotas.destroy', $anggota->id) }}" 
                                          method="POST" 
                                          style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmRemoveSiswa({{ $anggota->id }}, '{{ $anggota->siswa->nama ?? 'Siswa' }}')">
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
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NISN Siswa *</label>
                        <input type="text" 
                               name="siswa_nisn" 
                               class="form-control" 
                               placeholder="Masukkan NISN siswa"
                               required>
                        <small class="text-muted">Masukkan NISN siswa yang ingin ditambahkan</small>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Info:</strong> Pastikan NISN sudah terdaftar di sistem.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmRemoveSiswa(id, nama) {
    Swal.fire({
        title: 'Keluarkan Siswa?',
        html: `Apakah Anda yakin ingin mengeluarkan <strong>${nama}</strong> dari kelas ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Keluarkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`hapus-anggota-${id}`).submit();
        }
    });
}
</script>
@endif

@endsection