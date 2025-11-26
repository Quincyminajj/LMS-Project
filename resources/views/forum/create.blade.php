@extends('layouts.app')

@section('title', 'Buat Diskusi Baru')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <a href="{{ route('kelas.forum', $kelas->id) }}"
                            class="text-decoration-none text-secondary mb-3 d-inline-block">
                            <i class="bi bi-arrow-left"></i> Kembali ke Forum
                        </a>

                        <h4 class="fw-bold mb-4">Buat Diskusi Baru</h4>

                        <form action="{{ route('forum.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Judul Diskusi *</label>
                                <input type="text" name="judul" class="form-control form-control-lg"
                                    placeholder="Contoh: Pertanyaan tentang Persamaan Kuadrat" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Isi Diskusi *</label>
                                <textarea name="isi" class="form-control" rows="8"
                                    placeholder="Tuliskan pertanyaan atau topik diskusi Anda..." required></textarea>
                                <small class="text-muted">Jelaskan pertanyaan atau topik diskusi dengan jelas agar mendapat
                                    respon yang baik</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-send"></i> Posting Diskusi
                                </button>
                                <a href="{{ route('kelas.forum', $kelas->id) }}" class="btn btn-light px-4">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
