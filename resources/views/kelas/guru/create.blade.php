@extends('layouts.app')
@section('title', 'Buat Kelas')

@section('content')
<h3>Buat Kelas Baru</h3>
<form method="POST" action="{{ route('kelas.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Kode Kelas</label>
        <input type="text" name="kode_kelas" class="form-control" value="{{ $newCode }}" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Kelas</label>
        <input type="text" name="nama_kelas" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
</form>
@endsection
