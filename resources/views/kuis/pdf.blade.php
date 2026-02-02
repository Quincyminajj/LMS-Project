<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Kuis - {{ $kuis->judul }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 20px;
        }
        .header p {
            margin: 3px 0;
            color: #555;
        }
        .info-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-section table {
            width: 100%;
            border: none;
        }
        .info-section td {
            padding: 5px;
            border: none;
        }
        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
            color: #2c3e50;
        }
        .statistics {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            background-color: #e9ecef;
            border-right: 2px solid white;
        }
        .stat-box:last-child {
            border-right: none;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .main-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #1a252f;
        }
        .main-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 10px;
        }
        .main-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .main-table tbody tr:hover {
            background-color: #e9ecef;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #666;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN HASIL KUIS</h1>
        <p><strong>{{ $kuis->judul }}</strong></p>
        <p>{{ $kuis->kelas->nama_kelas }} • {{ $kuis->kelas->guru->nama_guru ?? 'Guru tidak terdaftar' }}</p>
    </div>

    <!-- Info Kuis -->
    <div class="info-section">
        <table>
            <tr>
                <td>Tanggal Mulai</td>
                <td>: {{ \Carbon\Carbon::parse($kuis->tanggal_mulai)->format('d F Y, H:i') }} WIB</td>
                <td>Durasi</td>
                <td>: {{ $kuis->durasi }} menit</td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td>: {{ \Carbon\Carbon::parse($kuis->tanggal_selesai)->format('d F Y, H:i') }} WIB</td>
                <td>Jumlah Soal</td>
                <td>: {{ $kuis->jumlah_soal }} soal</td>
            </tr>
            @if($kuis->deskripsi)
            <tr>
                <td>Deskripsi</td>
                <td colspan="3">: {{ $kuis->deskripsi }}</td>
            </tr>
            @endif
        </table>
    </div>

    @php
        $totalSiswa = $attempts->count();
        $rataRata = $totalSiswa > 0 ? $attempts->avg('nilai_akhir') : 0;
        $nilaiTertinggi = $totalSiswa > 0 ? $attempts->max('nilai_akhir') : 0;
        $nilaiTerendah = $totalSiswa > 0 ? $attempts->min('nilai_akhir') : 0;
    @endphp

    <!-- Statistik -->
    @if($totalSiswa > 0)
    <div class="statistics">
        <div class="stat-box">
            <div class="label">Total Peserta</div>
            <div class="value">{{ $totalSiswa }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Nilai Tertinggi</div>
            <div class="value">{{ number_format($nilaiTertinggi, 1) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Nilai Terendah</div>
            <div class="value">{{ number_format($nilaiTerendah, 1) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Rata-rata</div>
            <div class="value">{{ number_format($rataRata, 1) }}</div>
        </div>
    </div>
    @endif

    <!-- Tabel Hasil -->
    <table class="main-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 12%;">NIPD</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th class="text-center" style="width: 14%;">Mulai</th>
                <th class="text-center" style="width: 14%;">Selesai</th>
                <th class="text-center" style="width: 10%;">Durasi</th>
                <th class="text-center" style="width: 10%;">Nilai</th>
                <th class="text-center" style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attempts as $index => $attempt)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $attempt->siswa_nisn }}</td>
                    <td><strong>{{ $attempt->siswa->nama_siswa ?? 'Siswa tidak terdaftar' }}</strong></td>
                    <td class="text-center">
                        {{ $attempt->mulai_pada ? \Carbon\Carbon::parse($attempt->mulai_pada)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $attempt->selesai_pada ? \Carbon\Carbon::parse($attempt->selesai_pada)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $attempt->durasi ? $attempt->durasi . ' menit' : '-' }}
                    </td>
                    <td class="text-center">
                        @if ($attempt->nilai_akhir !== null)
                            <strong style="font-size: 12px;">{{ number_format($attempt->nilai_akhir, 1) }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($attempt->nilai_akhir !== null)
                            @php
                                $nilai = $attempt->nilai_akhir;
                                if ($nilai >= 80) {
                                    $badgeClass = 'badge-success';
                                    $status = 'Lulus';
                                } elseif ($nilai >= 60) {
                                    $badgeClass = 'badge-warning';
                                    $status = 'Cukup';
                                } else {
                                    $badgeClass = 'badge-danger';
                                    $status = 'Kurang';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                        @else
                            <span style="color: #999;">Belum Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <div class="icon">📋</div>
                        <p><strong>Belum ada siswa yang mengerjakan kuis ini</strong></p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none;">
                    <strong>Catatan:</strong><br>
                    Dokumen ini digenerate secara otomatis oleh sistem.<br>
                    Untuk informasi lebih lanjut, hubungi administrator.
                </td>
                <td style="width: 50%; text-align: right; border: none;">
                    <strong>Dicetak pada:</strong><br>
                    {{ \Carbon\Carbon::now()->format('d F Y, H:i:s') }} WIB<br>
                    Oleh: {{ auth()->user()->name ?? 'Sistem' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>