<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: "Poppins", sans-serif;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .summary-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 1.5rem;
            border-radius: 1rem;
            background: white;
        }
        .summary-icon {
            font-size: 1.8rem;
            padding: 0.5rem 0.7rem;
            border-radius: 0.75rem;
            background-color: #eaf0ff;
            color: #1e40af;
        }
        .summary-title {
            font-size: 0.9rem;
            color: #6b7280;
        }
        .summary-value {
            font-size: 1.3rem;
            font-weight: 600;
            color: #111827;
        }
        .kelas-card {
            transition: 0.2s;
        }
        .kelas-card:hover {
            transform: translateY(-3px);
        }
        .kelas-badge {
            font-size: 0.8rem;
            background-color: #e0fce4;
            color: #15803d;
        }
        .header {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-book me-2"></i>LMS Dashboard</h5>
            <small class="text-secondary">Portal {{ ucfirst(session('role')) }}</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <strong>{{ session('user_name') }}</strong><br>
                <small class="text-secondary">NIP: {{ session('identifier') }}</small>
            </div>
            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                {{ strtoupper(substr(session('user_name'), 0, 1)) }}
            </div>
            <a href="{{ route('logout') }}" class="text-danger ms-2" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
        </div>
    </div>

    <!-- Main -->
    <div class="container py-4">
        <h4 class="fw-semibold mb-2">Selamat Datang, {{ session('user_name') }}!</h4>
        <p class="text-secondary mb-4">Kelola kelas dan materi pembelajaran Anda</p>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="summary-card">
                    <div>
                        <div class="summary-title">Total Kelas Aktif</div>
                        <div class="summary-value">2</div>
                    </div>
                    <div class="summary-icon"><i class="bi bi-journal-bookmark"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div>
                        <div class="summary-title">Total Siswa</div>
                        <div class="summary-value">53</div>
                    </div>
                    <div class="summary-icon" style="background-color: #e7f9ee; color: #16a34a;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card">
                    <div>
                        <div class="summary-title">Kelas Diarsipkan</div>
                        <div class="summary-value">0</div>
                    </div>
                    <div class="summary-icon" style="background-color: #fef3c7; color: #d97706;">
                        <i class="bi bi-archive"></i>
                    </div>
                </div>
            </div>
        </div>

                    <!-- Kelas Saya -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Kelas Saya</h5>
                        <a href="{{ route('kelas.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Buat Kelas Baru
                        </a>
                    </div>

                    <div class="row g-3">
                        @foreach($kelas as $kls)
                        <div class="col-md-4">
                            <a href="{{ route('kelas.show', $kls->id) }}" class="text-decoration-none text-dark">
                                <div class="card kelas-card p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge kelas-badge">Aktif</span>
                                        <i class="bi bi-gear text-secondary"></i>
                                    </div>

                                    <h6 class="fw-bold mb-1">{{ $kls->nama_kelas }}</h6>
                                    <p class="text-secondary small mb-2">{{ $kls->deskripsi ?? 'Guru tidak terdaftar' }}</p>

                                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                                        <div><i class="bi bi-people me-1"></i>-</div>
                                        <span class="badge bg-light text-primary">{{ $kls->kode_kelas }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
