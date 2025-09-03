<?php

namespace App\Services;

class TerbilangService
{
    public function convert($angka): string
    {
        $angka = abs($angka);
        $baca = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
            'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
        ];

        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return $this->convert($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            return $this->convert($angka / 10) . ' Puluh ' . $this->convert($angka % 10);
        } elseif ($angka < 200) {
            return 'Seratus ' . $this->convert($angka - 100);
        } elseif ($angka < 1000) {
            return $this->convert($angka / 100) . ' Ratus ' . $this->convert($angka % 100);
        } elseif ($angka < 2000) {
            return 'Seribu ' . $this->convert($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->convert($angka / 1000) . ' Ribu ' . $this->convert($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->convert($angka / 1000000) . ' Juta ' . $this->convert($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return $this->convert($angka / 1000000000) . ' Miliar ' . $this->convert($angka % 1000000000);
        }

        return 'Angka terlalu besar';
    }
}
