<?php
namespace App\Imports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProdukImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            // Skip jika baris kosong atau semua kolom penting kosong
            if (empty($row) || (empty($row['nama'] ?? null) && empty($row['nama_produk'] ?? null))) {
                return null;
            }

            // Flexible column mapping - handle berbagai kemungkinan nama kolom
            $nama = $this->getColumnValue($row, ['nama', 'nama_produk', 'product_name', 'name']);
            $stok = $this->getColumnValue($row, ['stok', 'stock', 'qty', 'jumlah']);
            $harga_beli = $this->getColumnValue($row, ['harga_beli', 'harga_beli', 'buying_price', 'cost']);
            $harga_jual = $this->getColumnValue($row, ['harga_jual', 'harga_jual', 'selling_price', 'price']);
            $kategori = $this->getColumnValue($row, ['kategori', 'category', 'jenis']);
            $keterangan = $this->getColumnValue($row, ['keterangan', 'description', 'deskripsi']);
            $tanggal_kadaluarsa = $this->getColumnValue($row, ['tanggal_kadaluarsa', 'tanggal_exp', 'expiry_date', 'expired_date']);

            // Validasi data required
            if (empty($nama)) {
                Log::warning('Import Produk: Nama produk kosong', ['row' => $row]);
                return null;
            }

            // Parse tanggal
            $parsedDate = $this->parseDate($tanggal_kadaluarsa);

            return new Produk([
                'nama' => is_string($nama) ? trim((string)$nama) : 'Unknown',
                'stok' => is_numeric($stok) ? (int)$stok : 0,
                'harga_beli' => is_numeric($harga_beli) ? (float)$harga_beli : 0.0,
                'harga_jual' => is_numeric($harga_jual) ? (float)$harga_jual : 0.0,
                'kategori' => is_string($kategori) ? trim((string)$kategori) : 'Tidak Diketahui',
                'keterangan' => is_string($keterangan) ? trim((string)$keterangan) : null,
                'tanggal_kadaluarsa' => $parsedDate,
            ]);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengimpor data produk: ' . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    /**
     * Ambil nilai kolom dengan flexible naming
     */
    private function getColumnValue(array $row, array $possibleNames)
    {
        foreach ($possibleNames as $name) {
            if (isset($row[$name]) && !empty($row[$name])) {
                return $row[$name];
            }
        }
        return null;
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Jika numeric Excel date
            if (is_numeric($date) && $date > 0) {
                return Date::excelToDateTimeObject($date)->format('Y-m-d');
            }

            // Jika string date format
            if (is_string($date)) {
                $date = trim($date);
                
                // Coba parse dengan berbagai format
                $formats = ['Y-m-d', 'd-m-Y', 'm-d-Y', 'Y/m/d', 'd/m/Y', 'm/d/Y', 'd/m/Y H:i', 'Y-m-d H:i'];
                
                foreach ($formats as $format) {
                    try {
                        $parsed = Carbon::createFromFormat($format, $date);
                        return $parsed->format('Y-m-d');
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // Fallback: gunakan Carbon's natural parsing
                $parsed = Carbon::parse($date);
                return $parsed->format('Y-m-d');
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Gagal parse tanggal: ' . $date . ' - ' . $e->getMessage());
            return null;
        }
    }
}
