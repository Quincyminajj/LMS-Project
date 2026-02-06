<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TugasPengumpulanExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle,
    WithColumnWidths,
    WithEvents
{
    protected $tugas;
    protected $rowNumber = 0;

    public function __construct($tugas)
    {
        $this->tugas = $tugas;
    }

    /**
     * Data collection untuk export
     */
    public function collection()
    {
        return $this->tugas->pengumpulan;
    }

    /**
     * Heading tabel
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIPD',
            'Waktu Pengumpulan',
            'Jawaban',
            'File Lampiran',
            'Nilai',
            'Status KKM',
            'Catatan Guru',
        ];
    }

    /**
     * Mapping data untuk setiap row
     */
    public function map($pengumpulan): array
    {
        $this->rowNumber++;
        
        $isLulusKkm = $pengumpulan->nilai ? ($pengumpulan->nilai >= $this->tugas->kkm) : null;
        
        return [
            $this->rowNumber,
            $pengumpulan->siswa->nama ?? 'Siswa',
            $pengumpulan->siswa->nipd ?? '-',
            $pengumpulan->created_at->format('d-m-Y H:i'),
            $pengumpulan->jawaban,
            $pengumpulan->file_path ? 'Ada' : '-',
            $pengumpulan->nilai ?? '-',
            $isLulusKkm === null ? 'Belum Dinilai' : ($isLulusKkm ? 'Lulus KKM' : 'Belum Lulus'),
            $pengumpulan->catatan_guru ?? '-',
        ];
    }

    /**
     * Styling untuk worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0d6efd'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Title worksheet
     */
    public function title(): string
    {
        return 'Daftar Pengumpulan';
    }

    /**
     * Column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Nama Siswa
            'C' => 15,  // NIPD
            'D' => 18,  // Waktu Pengumpulan
            'E' => 40,  // Jawaban
            'F' => 15,  // File Lampiran
            'G' => 10,  // Nilai
            'H' => 15,  // Status KKM
            'I' => 30,  // Catatan Guru
        ];
    }

    /**
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Add info section di atas tabel
                $sheet->insertNewRowBefore(1, 8);

                // Judul Laporan
                $sheet->setCellValue('A1', 'LAPORAN PENGUMPULAN TUGAS');
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '0d6efd'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Informasi Tugas
                $infoData = [
                    ['Judul Tugas', ': ' . $this->tugas->judul],
                    ['Kelas', ': ' . $this->tugas->kelas->nama_kelas],
                    ['Guru Pengajar', ': ' . ($this->tugas->kelas->guru->nama_guru ?? '-')],
                    ['Deadline', ': ' . $this->tugas->deadline->format('d F Y, H:i')],
                    ['Nilai Maksimal', ': ' . number_format($this->tugas->nilai_maksimal, 2)],
                    ['KKM', ': ' . number_format($this->tugas->kkm, 0)],
                ];

                $startRow = 3;
                foreach ($infoData as $index => $info) {
                    $row = $startRow + $index;
                    $sheet->setCellValue('A' . $row, $info[0]);
                    $sheet->setCellValue('B' . $row, $info[1]);
                    $sheet->mergeCells('B' . $row . ':D' . $row);
                    
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['bold' => true],
                    ]);
                }

                // Statistik
                $avgNilai = $this->tugas->pengumpulan->whereNotNull('nilai')->avg('nilai');
                
                $statsData = [
                    ['Total Pengumpulan', ': ' . $this->tugas->pengumpulan->count() . ' siswa'],
                    ['Sudah Dinilai', ': ' . $this->tugas->pengumpulan->whereNotNull('nilai')->count() . ' siswa'],
                    ['Belum Dinilai', ': ' . $this->tugas->pengumpulan->whereNull('nilai')->count() . ' siswa'],
                    ['Lulus KKM', ': ' . $this->tugas->pengumpulan->where('nilai', '>=', $this->tugas->kkm)->count() . ' siswa'],
                    ['Belum Lulus KKM', ': ' . $this->tugas->pengumpulan->whereNotNull('nilai')->where('nilai', '<', $this->tugas->kkm)->count() . ' siswa'],
                    ['Rata-rata Nilai', ': ' . ($avgNilai ? number_format($avgNilai, 2) : '-')],
                ];

                $startCol = 'F';
                foreach ($statsData as $index => $stat) {
                    $row = $startRow + $index;
                    $sheet->setCellValue($startCol . $row, $stat[0]);
                    $sheet->setCellValue('G' . $row, $stat[1]);
                    $sheet->mergeCells('G' . $row . ':I' . $row);
                    
                    $sheet->getStyle($startCol . $row)->applyFromArray([
                        'font' => ['bold' => true],
                    ]);
                }

                // Border untuk semua data
                $lastRowWithData = $lastRow + 8;
                $sheet->getStyle('A9:I' . $lastRowWithData)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Zebra striping untuk data rows
                for ($row = 10; $row <= $lastRowWithData; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8F9FA'],
                            ],
                        ]);
                    }
                }

                // Wrap text untuk kolom jawaban dan catatan
                $sheet->getStyle('E10:E' . $lastRowWithData)->getAlignment()->setWrapText(true);
                $sheet->getStyle('I10:I' . $lastRowWithData)->getAlignment()->setWrapText(true);

                // Alignment
                $sheet->getStyle('A10:A' . $lastRowWithData)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G10:H' . $lastRowWithData)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Footer
                $footerRow = $lastRowWithData + 2;
                $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . now()->format('d F Y, H:i') . ' WIB');
                $sheet->mergeCells('A' . $footerRow . ':I' . $footerRow);
                $sheet->getStyle('A' . $footerRow)->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}