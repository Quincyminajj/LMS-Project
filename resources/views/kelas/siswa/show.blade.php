<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kelas->nama_kelas }} - LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $kelas->nama_kelas }}</h1>
                        <p class="text-sm text-gray-600">{{ $kelas->guru->nama_guru }} • Kode: {{ $kelas->kode_kelas }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Tabs Navigation -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-6">
            <div class="flex space-x-8">
                <button onclick="showTab('konten')" id="tab-konten"
                    class="tab-button py-4 px-2 border-b-2 border-blue-600 text-blue-600 font-semibold">
                    <i class="fas fa-book mr-2"></i>Konten
                </button>
                <button onclick="showTab('tugas')" id="tab-tugas"
                    class="tab-button py-4 px-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-tasks mr-2"></i>Tugas
                </button>
                <button onclick="showTab('forum')" id="tab-forum"
                    class="tab-button py-4 px-2 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-comments mr-2"></i>Forum
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <!-- Tab Konten -->
        <div id="content-konten" class="tab-content">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Materi Pembelajaran</h2>

            @if ($kelas->konten->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($kelas->konten as $konten)
                        <div
                            class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition">
                            <div class="flex items-start space-x-3 mb-4">
                                @if ($konten->tipe === 'file')
                                    <div class="bg-blue-100 p-3 rounded-lg">
                                        <i class="fas fa-file text-blue-600 text-xl"></i>
                                    </div>
                                @elseif($konten->tipe === 'link')
                                    <div class="bg-green-100 p-3 rounded-lg">
                                        <i class="fas fa-link text-green-600 text-xl"></i>
                                    </div>
                                @else
                                    <div class="bg-purple-100 p-3 rounded-lg">
                                        <i class="fas fa-align-left text-purple-600 text-xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 mb-1">{{ $konten->judul }}</h3>
                                    <p class="text-sm text-gray-500">{{ $konten->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            @if ($konten->tipe === 'teks')
                                <p class="text-gray-600 mb-4">{{ Str::limit($konten->isi, 100) }}</p>
                            @endif

                            @if ($konten->tipe === 'file')
                                <a href="{{ asset('storage/' . $konten->file_path) }}" target="_blank"
                                    class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-download mr-2"></i>Download File
                                </a>
                            @elseif($konten->tipe === 'link')
                                <a href="{{ $konten->isi }}" target="_blank"
                                    class="block w-full bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-external-link-alt mr-2"></i>Buka Link
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg">
                    <i class="fas fa-book-open text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500">Belum ada materi pembelajaran</p>
                </div>
            @endif
        </div>

        <!-- Tab Tugas -->
        <div id="content-tugas" class="tab-content hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Daftar Tugas</h2>

            @if ($kelas->tugas->count() > 0)
                <div class="space-y-4">
                    @foreach ($kelas->tugas as $tugas)
                        @php
                            $sudahDikumpulkan = in_array($tugas->id, $tugasDikumpulkan);
                            $pengumpulan = $sudahDikumpulkan
                                ? \App\Models\TugasPengumpulan::where('tugas_id', $tugas->id)
                                    ->where('siswa_nisn', session('identifier'))
                                    ->first()
                                : null;
                        @endphp

                        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-bold text-gray-800">{{ $tugas->judul }}</h3>
                                        @if ($sudahDikumpulkan && $pengumpulan->nilai)
                                            <span
                                                class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-semibold">
                                                Nilai: {{ $pengumpulan->nilai }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600 mb-4">{{ $tugas->deskripsi }}</p>

                                    <div class="flex items-center space-x-6 text-sm mb-4">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar mr-2"></i>
                                            <span>Deadline: {{ $tugas->deadline->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-star mr-2"></i>
                                            <span>Nilai Maksimal: {{ $tugas->nilai_maksimal }}</span>
                                        </div>
                                    </div>

                                    @if ($sudahDikumpulkan)
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                            <div class="flex items-center text-green-700 mb-2">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                <span class="font-semibold">Tugas telah dinilai</span>
                                            </div>
                                            <p class="text-sm text-gray-600">Nilai Anda:
                                                {{ $pengumpulan->nilai ?? 'Belum dinilai' }}</p>
                                            @if ($pengumpulan->feedback)
                                                <p class="text-sm text-gray-600 mt-2">
                                                    <strong>Feedback:</strong> {{ $pengumpulan->feedback }}
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <button onclick="showSubmitModal({{ $tugas->id }}, '{{ $tugas->judul }}')"
                                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-upload mr-2"></i>Kumpulkan Tugas
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg">
                    <i class="fas fa-tasks text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500">Belum ada tugas</p>
                </div>
            @endif
        </div>

        <!-- Tab Forum -->
        <div id="content-forum" class="tab-content hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Forum Diskusi</h2>

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600 mb-4">Diskusikan materi pembelajaran dengan teman dan guru Anda</p>
                <a href="{{ route('kelas.forum', $kelas->id) }}"
                    class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-comments mr-2"></i>Lihat Forum
                </a>
            </div>
        </div>
    </main>

    <!-- Modal Submit Tugas -->
    <div id="modalSubmit" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">Kumpulkan Tugas: <span id="judulTugas"></span></h3>
            <form id="formSubmit" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tugas_id" id="tugasId">
                <input type="hidden" name="siswa_nisn" value="{{ session('identifier') }}">

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Tipe Pengumpulan</label>
                    <select name="tipe" id="tipe-submit" class="w-full px-4 py-2 border rounded-lg"
                        onchange="toggleSubmitInput()" required>
                        <option value="file">Upload File</option>
                        <option value="link">Link URL</option>
                        <option value="teks">Teks</option>
                    </select>
                </div>

                <div id="submit-file" class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Upload File</label>
                    <input type="file" name="file_path" class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div id="submit-link" class="mb-4 hidden">
                    <label class="block text-gray-700 font-semibold mb-2">URL Link</label>
                    <input type="url" name="isi" class="w-full px-4 py-2 border rounded-lg"
                        placeholder="https://example.com">
                </div>

                <div id="submit-teks" class="mb-4 hidden">
                    <label class="block text-gray-700 font-semibold mb-2">Jawaban Anda</label>
                    <textarea name="isi" class="w-full px-4 py-2 border rounded-lg" rows="6"></textarea>
                </div>

                <div class="flex space-x-4">
                    <button type="button" onclick="hideSubmitModal()"
                        class="flex-1 bg-gray-200 py-2 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-2"></i>Kumpulkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(el => {
                el.classList.remove('border-blue-600', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-600');
            });

            // Show selected tab
            document.getElementById('content-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-600');
            document.getElementById('tab-' + tab).classList.add('border-blue-600', 'text-blue-600');
        }

        function showSubmitModal(tugasId, judul) {
            document.getElementById('tugasId').value = tugasId;
            document.getElementById('judulTugas').textContent = judul;
            document.getElementById('formSubmit').action = `/tugas/${tugasId}/submit`;
            document.getElementById('modalSubmit').classList.remove('hidden');
        }

        function hideSubmitModal() {
            document.getElementById('modalSubmit').classList.add('hidden');
        }

        function toggleSubmitInput() {
            const tipe = document.getElementById('tipe-submit').value;
            document.getElementById('submit-file').classList.add('hidden');
            document.getElementById('submit-link').classList.add('hidden');
            document.getElementById('submit-teks').classList.add('hidden');

            if (tipe === 'file') {
                document.getElementById('submit-file').classList.remove('hidden');
            } else if (tipe === 'link') {
                document.getElementById('submit-link').classList.remove('hidden');
            } else if (tipe === 'teks') {
                document.getElementById('submit-teks').classList.remove('hidden');
            }
        }
    </script>

    @if (session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
</body>

</html>
