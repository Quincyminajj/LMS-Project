@extends('layouts.app')

@section('title', 'Tugas Kelas')

@section('content')
<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Tugas untuk {{ $kelas->nama }}</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatTugas">
            + Buat Tugas
        </button>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a href="{{ route('kelas.konten', $kelas->id) }}" class="nav-link">Konten</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active">Tugas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link disabled">Forum</a>
        </li>
    </ul>

    <div class="row g-3">
        @forelse($kelas->tugas as $t)
        <div class="col-md-6">
            <div class="card p-3 shadow-sm rounded-3">

                <h5 class="fw-semibold">{{ $t->judul }}</h5>
                <p class="text-secondary small">{{ Str::limit($t->deskripsi, 120) }}</p>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">
                        Deadline: {{ $t->deadline->format('d M Y H:i') }}
                    </span>

                    <span class="badge bg-primary">
                        Maks: {{ $t->nilai_maks }}
                    </span>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('tugas.edit', [$kelas->id, $t->id]) }}" 
                       class="btn btn-outline-secondary btn-sm">
                        Edit
                    </a>

                    <a href="{{ route('tugas.nilai', [$kelas->id, $t->id]) }}" 
                       class="btn btn-dark btn-sm">
                        Lihat Pengumpulan
                    </a>
                </div>

            </div>
        </div>
        @empty
            <p class="text-center text-secondary">Belum ada tugas dibuat.</p>
        @endforelse
    </div>

</div>
@endsection

@include('tugas.modal-create')
