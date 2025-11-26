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
                <div class="flex items-center space-x-3">
                    <!-- Dropdown Menu -->
                    <div class="relative">
                        <button onclick="toggleDropdown()"
                            class="text-gray-600 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-ellipsis-v text-xl"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdown"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-10">
                            <a href="#" onclick="alert('Fitur edit akan segera ditambahkan')"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-edit mr-2"></i>Edit Kelas
                            </a>
                            <hr class="my-1">
                            <form action="{{ route('kelas.archive', $kelas->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin mengarsipkan kelas ini?')">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="w-full text-left px-4 py-3 text-yellow-600 hover:bg-yellow-50">
                                    <i class="fas fa-archive mr-2"></i>Arsipkan Kelas
                                </button>
                            </form>
                            <hr class="my-1">
                            <form action="{{ route('kelas.destroy', $kelas->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus permanen kelas ini? Data tidak bisa dikembalikan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50">
                                    <i class="fas fa-trash mr-2"></i>Hapus Permanen
                                </button>
                            </form>
                        </div>
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
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Materi Pembelajaran</h2>
                <button onclick="showModalKonten()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Konten
                </button>
            </div>

            @if ($kelas->konten->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($kelas->konten as $konten)
                        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
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
                                            <i class="fas fa-text text-purple-600 text-xl"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-bold text-gray-800">{{ $konten->judul }}</h3>
                                        <p class="text-sm text-gray-500">{{ $konten->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <button onclick="deleteKonten({{ $konten->id }})"
                                    class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

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
                            @else
                                <p class="text-gray-600">{{ $konten->isi }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg">
                    <i class="fas fa-book-open text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500">Belum ada konten pembelajaran</p>
                </div>
            @endif
        </div>

        <!-- Tab Tugas -->
        <div id="content-tugas" class="tab-content hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Daftar Tugas</h2>
                <button onclick="showModalTugas()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Tugas
                </button>
            </div>

            @if ($kelas->tugas->count() > 0)
                <div class="space-y-4">
                    @foreach ($kelas->tugas as $tugas)
                        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $tugas->judul }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $tugas->deskripsi }}</p>
                                    <div class="flex items-center space-x-6 text-sm">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar mr-2"></i>
                                            <span>Deadline: {{ $tugas->deadline->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-star mr-2"></i>
                                            <span>Nilai Maksimal: {{ $tugas->nilai_maksimal }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editTugas({{ $tugas->id }})"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteTugas({{ $tugas->id }})"
                                        class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Forum Diskusi</h2>
                <a href="{{ route('forum.create', $kelas->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Buat Topik Baru
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600">Klik "Buat Topik Baru" untuk memulai diskusi</p>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Konten -->
    <div id="modalKonten" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">Tambah Konten Pembelajaran</h3>
            <form action="{{ route('konten.store', $kelas->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Judul</label>
                    <input type="text" name="judul" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Tipe Konten</label>
                    <select name="tipe" id="tipe-konten" class="w-full px-4 py-2 border rounded-lg"
                        onchange="toggleKontenInput()" required>
                        <option value="file">File</option>
                        <option value="link">Link</option>
                        <option value="teks">Teks</option>
                    </select>
                </div>
                <div id="input-file" class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Upload File</label>
                    <input type="file" name="file_path" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div id="input-link" class="mb-4 hidden">
                    <label class="block text-gray-700 font-semibold mb-2">URL Link</label>
                    <input type="url" name="isi" class="w-full px-4 py-2 border rounded-lg"
                        placeholder="https://example.com">
                </div>
                <div id="input-teks" class="mb-4 hidden">
                    <label class="block text-gray-700 font-semibold mb-2">Konten Teks</label>
                    <textarea name="isi" class="w-full px-4 py-2 border rounded-lg" rows="4"></textarea>
                </div>
                <div class="flex space-x-4">
                    <button type="button" onclick="hideModalKonten()"
                        class="flex-1 bg-gray-200 py-2 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Tugas -->
    <div id="modalTugas" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">Tambah Tugas Baru</h3>
            <form action="{{ route('tugas.store', $kelas->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Judul Tugas</label>
                    <input type="text" name="judul" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full px-4 py-2 border rounded-lg" rows="4" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deadline</label>
                        <input type="datetime-local" name="deadline" class="w-full px-4 py-2 border rounded-lg"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nilai Maksimal</label>
                        <input type="number" name="nilai_maksimal" value="100"
                            class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <button type="button" onclick="hideModalTugas()"
                        class="flex-1 bg-gray-200 py-2 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.fa-ellipsis-v')) {
                const dropdown = document.getElementById('dropdown');
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }

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

        function showModalKonten() {
            document.getElementById('modalKonten').classList.remove('hidden');
        }

        function hideModalKonten() {
            document.getElementById('modalKonten').classList.add('hidden');
        }

        function showModalTugas() {
            document.getElementById('modalTugas').classList.remove('hidden');
        }

        function hideModalTugas() {
            document.getElementById('modalTugas').classList.add('hidden');
        }

        function toggleKontenInput() {
            const tipe = document.getElementById('tipe-konten').value;
            document.getElementById('input-file').classList.add('hidden');
            document.getElementById('input-link').classList.add('hidden');
            document.getElementById('input-teks').classList.add('hidden');

            if (tipe === 'file') {
                document.getElementById('input-file').classList.remove('hidden');
            } else if (tipe === 'link') {
                document.getElementById('input-link').classList.remove('hidden');
            } else if (tipe === 'teks') {
                document.getElementById('input-teks').classList.remove('hidden');
            }
        }

        function deleteKonten(id) {
            if (confirm('Yakin ingin menghapus konten ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/konten/${id}`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteTugas(id) {
            if (confirm('Yakin ingin menghapus tugas ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/kelas/{{ $kelas->id }}/tugas/${id}`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editTugas(id) {
            alert('Fitur edit akan segera ditambahkan');
        }
    </script>
</body>

</html>
