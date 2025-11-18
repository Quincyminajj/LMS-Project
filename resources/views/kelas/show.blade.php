@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', $kelas->nama_kelas)

@section('content')
<div class="container py-4">

    <!-- Header Kelas -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">{{ $kelas->nama_kelas }}</h3>
            <p class="text-secondary mb-1">{{ $kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}</p>
            <span class="badge bg-primary">{{ $kelas->kode_kelas }}</span>
        </div>

        <!-- Tombol Tambah Konten -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKonten">
            <i class="bi bi-plus-lg"></i> Buat Konten
        </button>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4">
        {{-- TAB KONTEN --}}
        <li class="nav-item">
            <a 
                class="nav-link {{ request()->is('kelas/'.$kelas->id) ? 'active' : '' }}"
                href="{{ url('kelas/'.$kelas->id) }}"
            >
                Konten
            </a>
        </li>
        {{-- TAB TUGAS --}}
        <li class="nav-item">
            <a 
                class="nav-link {{ request()->is('kelas/'.$kelas->id.'/tugas') ? 'active' : '' }}"
                href="{{ route('tugas.index', $kelas->id) }}"
            >
                Tugas
            </a>
        </li>
        {{-- TAB FORUM --}}
        <li class="nav-item">
            <a 
                class="nav-link {{ request()->is('kelas/'.$kelas->id.'/forum') ? 'active' : '' }}"
                href="{{ route('kelas.forum', $kelas->id) }}"
            >
                Forum
            </a>
        </li>

    </ul>


    <!-- Konten List -->
    <div class="row g-3">
        @forelse($kelas->konten as $k)
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <span class="badge bg-dark mb-2">{{ ucfirst($k->tipe) }}</span>
                <h6 class="fw-bold">{{ $k->judul }}</h6>
                <p class="text-secondary small">{{ Str::limit($k->isi, 80) }}</p>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    @if($k->tipe == 'file')
                        <a href="{{ asset('storage/'.$k->file_path) }}" class="btn btn-outline-dark btn-sm" download>
                            Download File
                        </a>
                    @endif

                    @if($k->tipe == 'link')
                        <a href="{{ $k->isi }}" target="_blank" class="btn btn-primary btn-sm">
                            Buka Link
                        </a>
                    @endif

                    <small class="text-muted">{{ $k->created_at->format('d M Y') }}</small>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-secondary">Belum ada konten pembelajaran</p>
        @endforelse
    </div>
</div>
@endsection


{{-- ================================================================ --}}
{{-- Modal Tambah Konten --}}
{{-- ================================================================ --}}
@section('modal')
<div class="modal fade" id="modalTambahKonten" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px;">
      <div class="modal-header">
        <h4 class="modal-title fw-semibold">Tambah Konten Pembelajaran</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ route('konten.store', $kelas->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="modal-body">
          <p class="text-secondary mb-3">Tambahkan materi, file, atau link untuk siswa</p>

          <div class="mb-3">
            <label class="form-label fw-semibold">Judul Konten *</label>
            <input type="text" name="judul" class="form-control form-control-lg" placeholder="Contoh: Materi Persamaan Kuadrat" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tipe Konten *</label>
            <select class="form-select form-select-lg" name="tipe" id="tipeKonten" required>
              <option disabled selected>- pilih tipe konten -</option>
              <option value="file">File (PDF, Word, PPT, dll)</option>
              <option value="link">Link</option>
              <option value="teks">Teks</option>
            </select>
          </div>

          <!-- File Upload -->
          <div id="fileUploadField" class="mb-3 d-none">
            <label class="form-label fw-semibold">Upload File *</label>
            <div class="border rounded-3 p-4 text-center bg-light" style="border-style: dashed;">
              <input type="file" name="file_path" class="form-control" id="fileInput" style="display:none;">
              <div onclick="document.getElementById('fileInput').click();" style="cursor:pointer;">
                <i class="bi bi-upload fs-1 text-primary"></i>
                <p class="mt-2 text-secondary">Klik untuk upload file<br>atau drag and drop file di sini</p>
              </div>
            </div>
          </div>

          <!-- Link -->
          <div id="linkField" class="mb-3 d-none">
            <label class="form-label fw-semibold">Masukkan Link *</label>
             <input type="text" name="isi" class="form-control form-control-lg"
            placeholder="https://example.com">
          </div>

          <!-- Text -->
          <div id="textField" class="mb-3 d-none">
            <label class="form-label fw-semibold">Deskripsi *</label>
            <textarea name="isi" class="form-control" rows="4" placeholder="Deskripsi singkat tentang konten ini"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Tambah Konten</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('tipeKonten').addEventListener('change', function () {
    let tipe = this.value;

    document.getElementById('fileUploadField').classList.add('d-none');
    document.getElementById('linkField').classList.add('d-none');
    document.getElementById('textField').classList.add('d-none');

    if (tipe === "file") document.getElementById('fileUploadField').classList.remove('d-none');
    if (tipe === "link") document.getElementById('linkField').classList.remove('d-none');
    if (tipe === "teks") document.getElementById('textField').classList.remove('d-none');
});
</script>
@endsection
