@extends('layouts.app')

@section('title', 'LMS Dashboard - Portal Siswa')
@section('body-class', 'bg-gray-50')
@section('main-class', 'container mx-auto px-3 sm:px-6 py-4 sm:py-8')
@section('header-title', 'LMS Dashboard')
@section('header-subtitle', 'Portal Siswa')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-3xl font-bold text-gray-800 mb-1 sm:mb-2">Selamat Datang, {{ session('user_name') }}!</h2>
        <p class="text-sm sm:text-base text-gray-600">Akses materi pembelajaran dan tugas Anda</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <!-- Kelas Diikuti -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Kelas Diikuti</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalKelasDiikuti }}</p>
                </div>
                <div class="bg-blue-100 p-3 sm:p-4 rounded-lg">
                    <i class="fas fa-book text-blue-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Tugas Selesai -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Tugas Selesai</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $tugasSelesai }} / {{ $totalTugas }}</p>
                </div>
                <div class="bg-green-100 p-3 sm:p-4 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Tugas Pending -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs sm:text-sm mb-1">Tugas Pending</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $tugasPending }}</p>
                </div>
                <div class="bg-orange-100 p-3 sm:p-4 rounded-lg">
                    <i class="fas fa-clock text-orange-600 text-xl sm:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Kelas Section -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-3 sm:gap-0">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800">Kelas Saya</h3>
            <button onclick="showJoinModal()" class="bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition flex items-center justify-center space-x-2 text-sm font-medium">
                <i class="fas fa-plus"></i>
                <span>Gabung Kelas</span>
            </button>
        </div>

        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Daftar kelas yang Anda ikuti</p>

        @if($kelasWithProgress->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                @foreach($kelasWithProgress as $kelas)
                    <div class="border border-gray-200 rounded-lg p-4 sm:p-6 hover:shadow-lg transition cursor-pointer" 
                         onclick="window.location.href='{{ route('kelas.show', $kelas->id) }}'">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center flex-wrap gap-2">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-semibold">
                                    {{ $kelas->kode_kelas }}
                                </span>
                                @if($kelas->progress_tugas == 100)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @endif
                            </div>
                        </div>
                        
                        <h4 class="text-base sm:text-lg font-bold text-gray-800 mb-2">{{ $kelas->nama_kelas }}</h4>
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($kelas->deskripsi, 100) }}</p>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <i class="fas fa-user mr-2"></i>
                            <span class="truncate">{{ $kelas->guru->nama_guru ?? 'Guru tidak ditemukan' }}</span>
                        </div>

                        <div class="mb-2">
                            <div class="flex justify-between text-xs sm:text-sm mb-1">
                                <span class="text-gray-600">Progress Tugas</span>
                                <span class="font-semibold text-gray-800">{{ $kelas->progress_tugas }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                     style="width: {{ $kelas->progress_tugas }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 sm:py-12">
                <i class="fas fa-book-open text-gray-300 text-4xl sm:text-6xl mb-3 sm:mb-4"></i>
                <p class="text-gray-500 text-base sm:text-lg mb-3 sm:mb-4">Anda belum mengikuti kelas apapun</p>
                <button onclick="showJoinModal()" class="bg-blue-600 text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base">
                    Gabung Kelas Sekarang
                </button>
            </div>
        @endif
    </div>

    <!-- Modal Gabung Kelas -->
    <div id="joinModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8 max-w-md w-full">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800">Gabung Kelas</h3>
                <button onclick="hideJoinModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('siswa.join-kelas') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm sm:text-base">Kode Kelas</label>
                    <input type="text" 
                           name="kode_kelas" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base" 
                           placeholder="Contoh: KLS-001"
                           required>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2">Masukkan kode kelas yang diberikan oleh guru Anda</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <button type="button" 
                            onclick="hideJoinModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold text-sm sm:text-base">
                        Gabung
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showJoinModal() {
            document.getElementById('joinModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideJoinModal() {
            document.getElementById('joinModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('joinModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideJoinModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideJoinModal();
            }
        });
    </script>
@endpush