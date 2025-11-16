@extends('layouts.app')
@section('title', 'Edit Kelas')

@section('content')
<h3>Edit Kelas</h3>
<form method="POST" action="{{ route('kelas.update', $kela->id) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Nama Kelas</label>
        <input type="text" name="nama_kelas" value="{{ $kela->nama_kelas }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control">{{ $kela->deskripsi }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Perbarui</button>
</form>
@endsection
