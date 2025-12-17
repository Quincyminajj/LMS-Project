@extends('layouts.app')

@section('title', 'LMS Dashboard - Portal Guru')
@section('body-class', 'bg-gray-50')
@section('main-class', 'container mx-auto px-3 sm:px-6 py-4 sm:py-8')
@section('header-title', 'LMS Dashboard')
@section('header-subtitle', 'Portal Guru')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-3xl font-bold text-gray-800 mb-1 sm:mb-2">Selamat Datang, {{ session('user_name') }}!</h2>
        <p class="text-sm sm:text-base text-gray-600">Kelola kelas dan materi pembelajaran Anda</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <!-- Total Kelas Aktif -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Total Kelas Aktif</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalKelasAktif }}</p>
                </div>
                <div class="bg-blue-100 p-3 sm:p-4 rounded-lg">
                    <i class="fas fa-book text-blue-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Total Siswa</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalSiswa }}</p>
                </div>
                <div class="bg-green-100 p-3 sm:p-4 rounded-lg">
                    <i class="fas fa-users text-green-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Kelas Diarsipkan -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-yellow-500 transition cursor-default">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Kelas Diarsipkan</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $kelasDiarsipkan }}</p>
                </div>
                <div class="bg-yellow-100 p-3 sm:p-4 rounded-lg">
                    <i class="fas fa-archive text-yellow-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Kelas Section -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-3 sm:gap-0">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Kelas Saya</h3>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="{{ route('kelas.arsip') }}"
                    class="bg-gray-100 text-gray-700 px-3 sm:px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center justify-center space-x-2 text-sm">
                    <i class="fas fa-archive"></i>
                    <span>Lihat Arsip</span>
                    @if ($kelasDiarsipkan > 0)
                        <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $kelasDiarsipkan }}</span>
                    @endif
                </a>
                <a href="{{ route('kelas.create') }}"
                    class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center space-x-2 text-sm">
                    <i class="fas fa-plus"></i>
                    <span>Buat Kelas Baru</span>
                </a>
            </div>
        </div>

        @if ($kelasAktif->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($kelasAktif as $kelas)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 sm:p-4">
                            <span id="kodeKelasText-{{ $kelas->id }}"
                                onclick="copyKodeKelas('{{ $kelas->kode_kelas }}')"
                                class="kode-badge inline-block bg-white text-blue-600 text-xs px-3 py-1 rounded-full font-semibold"
                                style="cursor:pointer;">
                                {{ $kelas->kode_kelas }}
                            </span>
                        </div>

                        <div class="p-4 sm:p-6">
                            <h4 class="text-base sm:text-lg font-bold text-gray-800 mb-2">{{ $kelas->nama_kelas }}</h4>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($kelas->deskripsi, 80) }}</p>

                            <div class="flex items-center justify-between text-sm mb-4">
                                <div class="flex items-center text-gray-500">
                                    <i class="fas fa-users mr-2"></i>
                                    <span>{{ $kelas->anggota->count() }} Siswa</span>
                                </div>
                            </div>

                            <a href="{{ route('kelas.show', $kelas->id) }}"
                                class="block w-full text-center bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                Buka Kelas
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 sm:py-12">
                <i class="fas fa-chalkboard-teacher text-gray-300 text-4xl sm:text-6xl mb-3 sm:mb-4"></i>
                <p class="text-gray-500 text-base sm:text-lg mb-3 sm:mb-4">Anda belum membuat kelas apapun</p>
                <a href="{{ route('kelas.create') }}"
                    class="inline-block bg-blue-600 text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base">
                    Buat Kelas Pertama
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function copyKodeKelas(kode) {
            navigator.clipboard.writeText(kode).then(() => {
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "Kode kelas disalin!",
                    showConfirmButton: false,
                    timer: 1400
                });
            });
        }
    </script>
@endpush