@extends('layouts.app')

@section('title', 'Kelas Diarsipkan')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('dashboard') }}" class="text-decoration-none text-secondary mb-2 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
                <h3 class="fw-bold mb-1">Kelas Diarsipkan</h3>
                <p class="text-muted">Daftar kelas yang sudah diarsipkan</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('kelas.arsip') }}" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama kelas atau kode..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('kelas.arsip') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kelas Arsip List -->
        <div class="row g-4">
            @forelse($kelasArsip as $kelas)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <span class="badge bg-secondary mb-2">
                                        <i class="bi bi-archive"></i> Diarsipkan
                                    </span>
                                    <h5 class="fw-bold mb-1">{{ $kelas->nama_kelas }}</h5>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-key"></i> {{ $kelas->kode_kelas }}
                                    </p>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form action="{{ route('kelas.restore', $kelas->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-arrow-counterclockwise text-success"></i> Pulihkan Kelas
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('kelas.destroy', $kelas->id) }}" method="POST" 
                                                  onsubmit="return confirm('Yakin ingin menghapus permanen kelas ini? Data tidak bisa dikembalikan!')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash"></i> Hapus Permanen
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="border-top pt-3 mb-3">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-person"></i> 
                                    {{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}
                                </p>
                                
                                @if($kelas->deskripsi)
                                    <p class="text-secondary small mb-0">{{ Str::limit($kelas->deskripsi, 80) }}</p>
                                @endif
                            </div>

                            <div class="d-flex gap-2 text-muted small">
                                <div class="flex-fill text-center p-2 bg-light rounded">
                                    <i class="bi bi-people"></i>
                                    <div class="fw-bold">{{ $kelas->anggota->count() }}</div>
                                    <small>Anggota</small>
                                </div>
                                <div class="flex-fill text-center p-2 bg-light rounded">
                                    <i class="bi bi-clipboard-check"></i>
                                    <div class="fw-bold">{{ $kelas->tugas->count() }}</div>
                                    <small>Tugas</small>
                                </div>
                                <div class="flex-fill text-center p-2 bg-light rounded">
                                    <i class="bi bi-chat-dots"></i>
                                    <div class="fw-bold">{{ $kelas->forums->count() ?? 0 }}</div>
                                    <small>Forum</small>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="bi bi-clock-history"></i> 
                                    Diarsipkan: {{ $kelas->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Tidak Ada Kelas Diarsipkan</h5>
                            <p class="text-secondary">Kelas yang diarsipkan akan muncul di sini</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-house"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($kelasArsip->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $kelasArsip->links() }}
            </div>
        @endif
    </div>
@endsection