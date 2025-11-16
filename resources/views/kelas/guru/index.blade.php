@extends('layouts.app')
@section('title', 'Kelas Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Kelas yang Saya Ajarkan</h3>
    <a href="{{ route('kelas.create') }}" class="btn btn-primary">+ Buat Kelas</a>
</div>

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Kelas</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kelas as $k)
        <tr>
            <td>{{ $k->kode_kelas }}</td>
            <td>{{ $k->nama_kelas }}</td>
            <td>{{ $k->deskripsi }}</td>
            <td>
                <a href="{{ route('kelas.show', $k->id) }}" class="btn btn-sm btn-info">Lihat</a>
                <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus kelas ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center">Belum ada kelas</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
