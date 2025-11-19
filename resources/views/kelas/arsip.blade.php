@extends('layouts.app')

@section('title', 'Kelas Diarsipkan')

@section('content')
<div class="container py-4">
    <h4 class="fw-semibold mb-4"><i class="bi bi-archive me-2"></i>Kelas Diarsipkan</h4>

    @if($kelasArsip->count() > 0)
        <div class="row g-3">
            @foreach($kelasArsip as $kls)
                <div class="col-md-4">
                    <div class="card p-3">
                        <h6 class="fw-bold mb-1">{{ $kls->nama_kelas }}</h6>
                        <p class="text-secondary small mb-2">{{ $kls->deskripsi }}</p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-warning text-dark">Arsip</span>

                            <form action="{{ route('kelas.restore', $kls->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Tidak ada kelas yang diarsipkan.</div>
    @endif

    <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>
@endsection
