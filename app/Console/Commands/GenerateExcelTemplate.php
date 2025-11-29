<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateExcelTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'template:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Excel template for student receivables import';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->generateTemplate();
        $this->info('Template generated successfully!');
    }

    public static function generateTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $headers = [
            'Nomor Induk Siswa',
            'Kode Akun Piutang',
            'Kode Akun Pendapatan',
            'Jumlah Piutang',
            'Deskripsi (Opsional)'
        ];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index); // A, B, C, D, E
            $sheet->setCellValue($column . '1', $header);
        }

        // Sample data
        $sampleData = [
            ['001234', '1-120001-1', '4-120001-1', 500000, 'SPP November 2025'],
            ['001235', '1-120001-1', '4-120001-1', 500000, 'SPP November 2025'],
            ['001236', '1-120001-2', '4-120001-2', 250000, 'DPP November 2025'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            foreach ($data as $index => $value) {
                $column = chr(65 + $index); // A, B, C, D, E
                $sheet->setCellValue($column . $row, $value);
            }
            $row++;
        }

        // Pastikan folder templates ada
        if (!is_dir(public_path('templates'))) {
            mkdir(public_path('templates'), 0755, true);
        }

        // Save file
        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path('templates/template_import_piutang_siswa.xlsx'));
    }
}
