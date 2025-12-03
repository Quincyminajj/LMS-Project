@extends('layouts.app')

@section('content')
<div class="container py-4">

    <a href="{{ route('kelas.anggota', $kelas->id) }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Anggota Kelas
    </a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Detail Siswa</h4>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Nama</div>
                <div class="col-md-9">{{ $siswa->nama ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">NISN</div>
                <div class="col-md-9">{{ $siswa->nisn ?? '-' }}</div>
            </div>

            @if($siswa->nipd)
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">NIPD</div>
                <div class="col-md-9">{{ $siswa->nipd }}</div>
            </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Email</div>
                <div class="col-md-9">{{ $siswa->email ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">No HP</div>
                <div class="col-md-9">{{ $siswa->hp ?? '-' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Angkatan</div>
                <div class="col-md-9">{{ $siswa->angkatan ?? '-' }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Status Siswa</div>
                <div class="col-md-9">
                    @if(strtolower($siswa->status_siswa) === 'aktif')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Tidak Aktif</span>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Foto</div>
                <div class="col-md-9">
                    @if($siswa->foto)
                        <img src="{{ asset('foto_siswa/'.$siswa->foto) }}" 
                             width="150" class="rounded shadow-sm">
                    @else
                        <em>Tidak ada foto</em>
                    @endif
                </div>
            </div>

            <hr>

            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Bergabung pada</div>
                <div class="col-md-9">
                    {{ \Carbon\Carbon::parse($anggota->joined_at)->format('d M Y') }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 fw-bold">Kelas</div>
                <div class="col-md-9">
                    {{ $kelas->nama_kelas }} ({{ $kelas->kode_kelas }})
                </div>
            </div>

        </div>
    </div>

</div>
@endsection