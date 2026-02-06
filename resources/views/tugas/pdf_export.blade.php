<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengumpulan Tugas - {{ $tugas->judul }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0d6efd;
        }

        .header h1 {
            font-size: 20px;
            color: #0d6efd;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-section {
            margin-bottom: 25px;
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 5px 10px;
            background-color: #f8f9fa;
        }

        .info-value {
            display: table-cell;
            padding: 5px 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .stats-container {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .stats-grid {
            display: table;
            width: 100%;
        }

        .stats-row {
            display: table-row;
        }

        .stat-label {
            display: table-cell;
            width: 40%;
            padding: 5px;
            font-weight: bold;
        }

        .stat-value {
            display: table-cell;
            padding: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #0d6efd;
            color: white;
        }

        table thead th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        table tbody td {
            padding: 8px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #198754;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
            font-style: italic;
        }

        .jawaban-text {
            max-width: 250px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Pengumpulan Tugas</h1>
    </div>

    <!-- Informasi Tugas -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Judul Tugas</div>
            <div class="info-value">{{ $tugas->judul }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kelas</div>
            <div class="info-value">{{ $tugas->kelas->nama_kelas }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Guru Pengajar</div>
            <div class="info-value">{{ $tugas->kelas->guru->nama_guru ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Deadline</div>
            <div class="info-value">{{ $tugas->deadline->format('d F Y, H:i') }} WIB</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nilai Maksimal</div>
            <div class="info-value">{{ number_format($tugas->nilai_maksimal, 2) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">KKM</div>
            <div class="info-value">{{ number_format($tugas->kkm, 0) }}</div>
        </div>
    </div>

    <!-- Statistik -->
    @php
        $totalPengumpulan = $tugas->pengumpulan->count();
        $sudahDinilai = $tugas->pengumpulan->whereNotNull('nilai')->count();
        $belumDinilai = $tugas->pengumpulan->whereNull('nilai')->count();
        $lulusKkm = $tugas->pengumpulan->where('nilai', '>=', $tugas->kkm)->count();
        $belumLulusKkm = $tugas->pengumpulan->whereNotNull('nilai')->where('nilai', '<', $tugas->kkm)->count();
        $avgNilai = $tugas->pengumpulan->whereNotNull('nilai')->avg('nilai');
    @endphp

    <div class="stats-container">
        <h3 style="margin-bottom: 10px; font-size: 14px;">Statistik Pengumpulan</h3>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-label">Total Pengumpulan:</div>
                <div class="stat-value">{{ $totalPengumpulan }} siswa</div>
            </div>
            <div class="stats-row">
                <div class="stat-label">Sudah Dinilai:</div>
                <div class="stat-value">{{ $sudahDinilai }} siswa</div>
            </div>
            <div class="stats-row">
                <div class="stat-label">Belum Dinilai:</div>
                <div class="stat-value">{{ $belumDinilai }} siswa</div>
            </div>
            <div class="stats-row">
                <div class="stat-label">Lulus KKM:</div>
                <div class="stat-value">{{ $lulusKkm }} siswa</div>
            </div>
            <div class="stats-row">
                <div class="stat-label">Belum Lulus KKM:</div>
                <div class="stat-value">{{ $belumLulusKkm }} siswa</div>
            </div>
            <div class="stats-row">
                <div class="stat-label">Rata-rata Nilai:</div>
                <div class="stat-value">{{ $avgNilai ? number_format($avgNilai, 2) : '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Pengumpulan -->
    @if($tugas->pengumpulan->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 18%;">Nama Siswa</th>
                    <th style="width: 12%;">NIPD</th>
                    <th style="width: 13%;">Waktu Pengumpulan</th>
                    <th style="width: 28%;">Jawaban</th>
                    <th style="width: 8%;">File</th>
                    <th style="width: 7%;">Nilai</th>
                    <th style="width: 10%;">Status KKM</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tugas->pengumpulan as $index => $pengumpulan)
                    @php
                        $isLulusKkm = $pengumpulan->nilai ? ($pengumpulan->nilai >= $tugas->kkm) : null;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $pengumpulan->siswa->nama ?? 'Siswa' }}</td>
                        <td>{{ $pengumpulan->siswa->nipd ?? '-' }}</td>
                        <td>{{ $pengumpulan->created_at->format('d-m-Y H:i') }}</td>
                        <td class="jawaban-text">{{ \Illuminate\Support\Str::limit($pengumpulan->jawaban, 150) }}</td>
                        <td class="text-center">{{ $pengumpulan->file_path ? 'Ada' : '-' }}</td>
                        <td class="text-center">
                            @if($pengumpulan->nilai)
                                <strong>{{ $pengumpulan->nilai }}</strong>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($isLulusKkm === null)
                                <span class="badge badge-secondary">Belum Dinilai</span>
                            @elseif($isLulusKkm)
                                <span class="badge badge-success">Lulus KKM</span>
                            @else
                                <span class="badge badge-warning">Belum Lulus</span>
                            @endif
                        </td>
                    </tr>
                    
                    @if($pengumpulan->catatan_guru)
                        <tr>
                            <td colspan="8" style="background-color: #fff3cd; padding: 8px;">
                                <strong>Catatan Guru:</strong> {{ $pengumpulan->catatan_guru }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            Belum ada siswa yang mengumpulkan tugas
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB
    </div>
</body>
</html>