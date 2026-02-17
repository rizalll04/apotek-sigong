<?php

namespace App\Imports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PenjualanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Konversi serial Excel date ke format Y-m-d jika berupa angka
        $tanggal = is_numeric($row['tanggal']) 
            ? Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d') 
            : $row['tanggal'];

        return new Penjualan([
            'produk_id'      => $row['produk_id'], 
            'jumlah'         => $row['jumlah'], 
            'harga'          => $row['harga'], 
            'total_harga'    => $row['jumlah'] * $row['harga'], 
            'uang_diterima'  => $row['uang_diterima'], 
            'kembalian'      => ($row['uang_diterima'] >= ($row['jumlah'] * $row['harga'])) 
                                ? ($row['uang_diterima'] - ($row['jumlah'] * $row['harga'])) 
                                : null,
            'tanggal'        => $tanggal, 
        ]);
    }
}
