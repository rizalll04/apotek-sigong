<?php
namespace App\Imports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class ProdukImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            return new Produk([
                'nama' => isset($row['nama']) && is_string($row['nama']) ? trim($row['nama']) : 'Unknown',

                'stok' => isset($row['stok']) && is_numeric($row['stok']) ? (int) $row['stok'] : 0,

                'harga_beli' => isset($row['harga_beli']) && is_numeric($row['harga_beli']) ? (float) $row['harga_beli'] : 0.0,

                'harga_jual' => isset($row['harga_jual']) && is_numeric($row['harga_jual']) ? (float) $row['harga_jual'] : 0.0,

                'kategori' => isset($row['kategori']) && is_string($row['kategori']) ? trim($row['kategori']) : 'Tidak Diketahui',

                'keterangan' => isset($row['keterangan']) ? trim((string) $row['keterangan']) : null,

                'tanggal_kadaluarsa' => isset($row['tanggal_kadaluarsa']) && $this->isValidExcelDate($row['tanggal_kadaluarsa']) 
                    ? Date::excelToDateTimeObject($row['tanggal_kadaluarsa']) 
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengimpor data produk: ' . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    /**
     * Validasi apakah nilai tanggal dari Excel benar.
     */
    private function isValidExcelDate($date)
    {
        return is_numeric($date) && $date > 0;
    }
}
