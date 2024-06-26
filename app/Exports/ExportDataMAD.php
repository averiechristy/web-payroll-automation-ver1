<?php
namespace App\Exports;

use App\Models\Mad;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportDataMAD implements FromQuery, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function query()
    {
        $query = Mad::query();

        if ($this->month) {
            $query->whereMonth('tanggal_lembur', $this->month);
        }

        if ($this->year) {
            $query->whereYear('tanggal_lembur', $this->year);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No PBB/Amandemen',
            'NIK (sesuai KTP)',
            'Nama (sesuai KTP)',
            'Unit Kerja Penempatan',
            'Posisi',
            'Kode Cabang Pembayaran',
            'RCC Pembayaran',
            'Tanggal Lembur',
            'Cek Tanggal',
            'L/K',
            'Upah Pokok',
            'Tunjangan Supervisor',
            'Jam Mulai',
            'Jam Selesai',
            'Jumlah Jam Lembur per Hari',
            'Jam Pertama',
            'Jam Kedua',
            'Jam Ketiga',
            'Jam Keempat',
            'Biaya Jam Pertama',
            'Biaya Jam Kedua',
            'Biaya Jam Ketiga',
            'Biaya Jam Keempat',
            'Subtotal',
            'Management Fee (%)',
            'Management Fee (besaran)',
            'Total Sebelum PPN',
            'Keterangan Lembur',
            'Keterangan Perbaikan',
            'Kode SLID',
        ];
    }

    public function map($item): array
    {
        $formattedDate = Carbon::parse($item->tanggal_lembur)->format('d-F-Y');
        return [
            $item->karyawan->no_amandemen,
            $item->karyawan->nik_ktp,
            $item->karyawan->nama_karyawan,
            $item->karyawan->penempatan->nama_unit_kerja,
            $item->karyawan->posisi->posisi,
            $item->karyawan->penempatan->kode_cabang_pembayaran,
            $item->karyawan->penempatan->rcc_pembayaran,
            $formattedDate,
            $item->jenis_hari,
            $item->jenis_hari == "Kerja" ? "K" : ($item->jenis_hari == "Libur" ? "L" : ""),
            $item->gaji,
            $item->tunjangan,
            $item->jam_mulai,
            $item->jam_selesai,
            $item->jumlah_jam_lembur,
            $item->jam_pertama ?: '0',
            $item->jam_kedua ?: '0',
            $item->jam_ketiga ?: '0',
            $item->jam_keempat ?: '0',
            $item->biaya_jam_pertama,
            $item->biaya_jam_kedua, 
            $item->biaya_jam_ketiga, 
            $item->biaya_jam_keempat, 
            $item->subtotal, 
            $item->karyawan->management_fee * 100,
            $item->management_fee_amount, 
            $item->total_sebelum_ppn, 
            $item->keterangan_lembur,
            $item->keterangan_perbaikan,
            $item->karyawan->penempatan->kode_slid,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 6); // Menambahkan 6 baris kosong sebelum heading
                $sheet->setCellValue('A1', 'Perhitungan Tambahan Biaya untuk Lembur'); // Menulis pada baris A1
                $sheet->setCellValue('A3', 'PT.EXA MITRA SOLUSI');
                $months = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ];
                $monthName = $months[(int)$this->month];
                $sheet->setCellValue('A5', 'Periode: ' . $monthName . ' ' . $this->year); // Menulis periode pada baris A5
                $sheet->getStyle('A7:AD7')->getFont()->setBold(true); // Membuat heading menjadi bold
                
                $sheet->getStyle('A7:AD7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Get the highest row number
                $lastRow = $sheet->getHighestRow();

                // Define columns to sum
                $columnsToSum = [
                    'O' => 'Jumlah Jam Lembur per Hari',
                    'P' => 'Jam Pertama',
                    'Q' => 'Jam Kedua',
                    'R' => 'Jam Ketiga',
                    'S' => 'Jam Keempat',
                    'T' => 'Biaya Jam Pertama',
                    'U' => 'Biaya Jam Kedua',
                    'V' => 'Biaya Jam Ketiga',
                    'W' => 'Biaya Jam Keempat',
                    'X' => 'Subtotal',
                    'Z' => 'Management Fee (besaran)',
                    'AA' => 'Total Sebelum PPN',
                ];

                $sumRow = $lastRow + 1;

                // Insert the sum formulas
                foreach ($columnsToSum as $column => $header) {
                    $sheet->setCellValue($column . $sumRow, '=SUM(' . $column . '8:' . $column . $lastRow . ')');
                }

                // Apply yellow background color to the entire row
                $highestColumn = 'AA'; // Adjust this to your highest column that needs coloring
                $range = 'A' . $sumRow . ':' . $highestColumn . $sumRow;
                $sheet->getStyle($range)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                                   ->getStartColor()->setARGB('FFFF00');

                // Set the currency format for the columns
                $currencyColumns = ['T', 'U', 'V', 'W', 'X', 'Z', 'AA'];
                foreach ($currencyColumns as $column) {
                    $sheet->getStyle($column . '8:' . $column . $sumRow)
                          ->getNumberFormat()
                          ->setFormatCode('"Rp"#,##0');
                }

                $sheet->setCellValue('Z' . ($lastRow + 2), 'Total');
                $sheet->setCellValue('Z' . ($lastRow + 3), 'PPN');
                $sheet->setCellValue('Z' . ($lastRow + 4), 'Subtotal');

                // Membuat teks di sel Z bold
                $sheet->getStyle('Z' . ($lastRow + 2))->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('Z' . ($lastRow + 3))->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('Z' . ($lastRow + 4))->applyFromArray(['font' => ['bold' => true]]);

                $sheet->setCellValue('AA' . ($lastRow + 2), '=AA' . ($lastRow + 1)); // Total
                $sheet->setCellValue('AA' . ($lastRow + 3), '=ROUND(AA' . ($lastRow + 1) . '*0.11, 0)'); // PPN
                $sheet->setCellValue('AA' . ($lastRow + 4), '=AA' . ($lastRow + 2) . '+AA' . ($lastRow + 3)); // Subtotal

                $currencyColumns = ['AA' . ($lastRow + 2), 'AA' . ($lastRow + 3), 'AA' . ($lastRow + 4)];
                foreach ($currencyColumns as $cell) {
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('"Rp"#,##0');
                }


                $sheet->setCellValue('A' . ($lastRow + 7), 'Dibuat Oleh, ');
                $sheet->setCellValue('C' . ($lastRow + 7), 'Mengetahui, ');

                $sheet->setCellValue('A' . ($lastRow + 12), 'Sondang Esteria Resta');
                $sheet->setCellValue('C' . ($lastRow + 12), 'Cynthia Widjaja');
                
                // Set alignment to left for the item columns
                $columnsToAlignLeft = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
                foreach ($columnsToAlignLeft as $column) {
                    $sheet->getStyle($column . '8:' . $column . $sumRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                }
            }
        ];
    }

    public function title(): string
    {
        return 'Data MAD';
    }
}
