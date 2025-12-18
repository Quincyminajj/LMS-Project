<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Dashboard - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-3 sm:px-6 py-3 sm:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <i class="fas fa-book text-lg sm:text-2xl"></i>
                    <div>
                        <h1 class="text-base sm:text-xl font-bold">LMS Dashboard</h1>
                        <p class="text-xs sm:text-sm text-blue-100">Portal Guru</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="text-right max-w-[140px] sm:max-w-none">
                        <p class="font-semibold text-xs sm:text-sm break-words leading-tight">{{ session('user_name') }}</p>
                        <p class="text-xs text-blue-100 whitespace-nowrap">NIP: {{ session('identifier') }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-full flex items-center justify-center text-sm sm:text-lg font-bold flex-shrink-0">
                        {{ substr(session('user_name'), 0, 1) }}
                    </div>
                    <a href="#" class="text-white hover:text-blue-200 flex-shrink-0" id="logoutButton">
                        <i class="fas fa-sign-out-alt text-lg sm:text-xl"></i>
                    </a>
                    <form id="logoutForm" action="{{ route('logout') }}" method="GET" class="d-none"></form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-3 sm:px-6 py-4 sm:py-8">
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
    </main>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div id="toast"
            class="fixed bottom-4 right-4 left-4 sm:left-auto bg-green-500 text-white px-4 sm:px-6 py-3 rounded-lg shadow-lg animate-slide-in z-50">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span class="text-sm sm:text-base">{{ session('success') }}</span>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('toast').style.display = 'none';
            }, 3000);
        </script>
    @endif

    @if (session('error'))
        <div id="toast"
            class="fixed bottom-4 right-4 left-4 sm:left-auto bg-red-500 text-white px-4 sm:px-6 py-3 rounded-lg shadow-lg animate-slide-in z-50">
            <div class="flex items-center space-x-2">
                <i class="fas fa-exclamation-circle"></i>
                <span class="text-sm sm:text-base">{{ session('error') }}</span>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('toast').style.display = 'none';
            }, 3000);
        </script>
    @endif

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

        document.getElementById('logoutButton').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Yakin ingin logout?",
                text: "Anda akan keluar dari sistem.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Logout",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        });
    </script>
</body>

</html>