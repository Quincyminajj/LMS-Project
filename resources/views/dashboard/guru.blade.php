<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Dashboard - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-book text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold">LMS Dashboard</h1>
                        <p class="text-sm text-blue-100">Portal Guru</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-semibold">{{ session('user_name') }}</p>
                        <p class="text-sm text-blue-100">NIP: {{ session('identifier') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-lg font-bold">
                        {{ substr(session('user_name'), 0, 1) }}
                    </div>
                    <a href="{{ route('logout') }}" class="text-white hover:text-blue-200">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang, {{ session('user_name') }}!</h2>
            <p class="text-gray-600">Kelola kelas dan materi pembelajaran Anda</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Kelas Aktif -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Total Kelas Aktif</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalKelasAktif }}</p>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <i class="fas fa-book text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Total Siswa</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalSiswa }}</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Kelas Diarsipkan -->
            <a href="{{ route('kelas.arsip') }}"
                class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-lg transition cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Kelas Diarsipkan</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $kelasDiarsipkan }}</p>
                        <p class="text-xs text-yellow-600 mt-2">
                            <i class="fas fa-arrow-right mr-1"></i>Klik untuk lihat
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-lg">
                        <i class="fas fa-archive text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Kelas Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Kelas Saya</h3>
                <div class="flex space-x-3">
                    <a href="{{ route('kelas.arsip') }}"
                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center space-x-2">
                        <i class="fas fa-archive"></i>
                        <span>Lihat Arsip</span>
                        @if ($kelasDiarsipkan > 0)
                            <span
                                class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $kelasDiarsipkan }}</span>
                        @endif
                    </a>
                    <a href="{{ route('kelas.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Buat Kelas Baru</span>
                    </a>
                </div>
            </div>

            @if ($kelasAktif->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($kelasAktif as $kelas)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
                                <span
                                    class="inline-block bg-white text-blue-600 text-xs px-3 py-1 rounded-full font-semibold">
                                    {{ $kelas->kode_kelas }}
                                </span>
                            </div>

                            <div class="p-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $kelas->nama_kelas }}</h4>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($kelas->deskripsi, 80) }}</p>

                                <div class="flex items-center justify-between text-sm mb-4">
                                    <div class="flex items-center text-gray-500">
                                        <i class="fas fa-users mr-2"></i>
                                        <span>{{ $kelas->anggota->count() }} Siswa</span>
                                    </div>
                                </div>

                                <a href="{{ route('kelas.show', $kelas->id) }}"
                                    class="block w-full text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                    Buka Kelas
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-chalkboard-teacher text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg mb-4">Anda belum membuat kelas apapun</p>
                    <a href="{{ route('kelas.create') }}"
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Buat Kelas Pertama
                    </a>
                </div>
            @endif
        </div>
    </main>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div id="toast"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slide-in">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
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
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slide-in">
            <div class="flex items-center space-x-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('toast').style.display = 'none';
            }, 3000);
        </script>
    @endif
</body>

</html>
