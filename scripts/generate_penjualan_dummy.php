<?php
declare(strict_types=1);

// Generate an Excel file containing:
// 1) Template Bulanan (like screenshot)
// 2) Import Ready (matching PenjualanImport.php headers)
// 3) ProdukMapping (name to produk_id mapping used)

require dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$products = [
    1 => 'Amoxicillin 500mg',
    2 => 'hansaplast',
    3 => 'Bodrex',
    4 => 'Diapet',
    5 => 'Imboost',
    6 => 'Cetirizine',
    7 => 'ibuprofen',
    8 => 'Paracetamol 500mg',
    9 => 'Promag',
    10 => 'Vitamin C IPI',
];

// Data transcribed from the provided image
$monthlyData = [
    ['2024-01', [144, 51, 63, 70, 59, 85, 278, 320, 127, 62]],
    ['2024-02', [140, 47, 63, 60, 60, 102, 263, 273, 128, 61]],
    ['2024-03', [137, 50, 78, 62, 67, 133, 239, 273, 120, 68]],
    ['2024-04', [138, 56, 78, 68, 61, 151, 205, 300, 124, 70]],
    ['2024-05', [128, 55, 79, 61, 62, 163, 169, 267, 123, 64]],
    ['2024-06', [129, 63, 60, 65, 65, 140, 193, 238, 123, 73]],
    ['2024-07', [124, 64, 60, 68, 73, 171, 141, 238, 128, 74]],
    ['2024-08', [124, 64, 64, 67, 67, 166, 146, 146, 114, 69]],
    ['2024-09', [122, 66, 66, 64, 70, 180, 148, 146, 110, 63]],
    ['2024-10', [120, 66, 66, 64, 71, 133, 217, 219, 110, 73]],
    ['2024-11', [129, 60, 73, 64, 70, 178, 267, 261, 112, 80]],
    ['2024-12', [116, 50, 70, 65, 76, 81, 269, 292, 110, 84]],
    ['2025-01', [121, 57, 62, 79, 76, 83, 300, 300, 113, 82]],
    ['2025-02', [130, 68, 62, 86, 70, 107, 295, 296, 124, 88]],
    ['2025-03', [109, 70, 71, 71, 83, 130, 268, 273, 167, 88]],
    ['2025-04', [112, 72, 76, 77, 87, 125, 225, 227, 98, 80]],
    ['2025-05', [111, 82, 76, 80, 78, 158, 182, 198, 102, 84]],
    ['2025-06', [109, 80, 65, 68, 86, 168, 146, 164, 104, 88]],
    ['2025-07', [110, 86, 69, 66, 88, 164, 165, 163, 106, 88]],
    ['2025-08', [104, 79, 69, 68, 97, 162, 170, 153, 98, 90]],
    ['2025-09', [107, 89, 68, 77, 94, 152, 170, 143, 96, 92]],
    ['2025-10', [107, 89, 76, 79, 95, 125, 238, 244, 94, 94]],
    ['2025-11', [103, 89, 67, 77, 97, 104, 274, 286, 87, 96]],
    ['2025-12', [93, 82, 61, 76, 89, 83, 207, 307, 81, 97]],
    ['2026-01', [96, 94, 67, 72, 103, 85, 301, 300, 86, 100]],
];

$spreadsheet = new Spreadsheet();

// 1) Template Bulanan sheet
$templateSheet = $spreadsheet->getActiveSheet();
$templateSheet->setTitle('Template Bulanan');
$templateSheet->setCellValue('A1', 'Bulan');
// Headers for products
$colIndex = 2; // Column B
foreach ($products as $pid => $name) {
    $templateSheet->setCellValueByColumnAndRow($colIndex, 1, $name);
    $colIndex++;
}
// Data rows
$row = 2;
foreach ($monthlyData as [$month, $values]) {
    $templateSheet->setCellValueByColumnAndRow(1, $row, $month);
    $colIndex = 2;
    foreach ($values as $val) {
        $templateSheet->setCellValueByColumnAndRow($colIndex, $row, $val);
        $colIndex++;
    }
    $row++;
}

// 2) Import Ready sheet
$importSheet = $spreadsheet->createSheet();
$importSheet->setTitle('Import Ready');
$importSheet->setCellValue('A1', 'produk_id');
$importSheet->setCellValue('B1', 'jumlah');
$importSheet->setCellValue('C1', 'harga');
$importSheet->setCellValue('D1', 'uang_diterima');
$importSheet->setCellValue('E1', 'tanggal');

// Default values (adjust as needed)
$defaultHargaPerProduk = [
    1 => 15000,
    2 => 10000,
    3 => 12000,
    4 => 8000,
    5 => 20000,
    6 => 10000,
    7 => 7000,
    8 => 9000,
    9 => 11000,
    10 => 15000,
];

$row = 2;
foreach ($monthlyData as [$month, $values]) {
    // use first day of month
    $date = $month . '-01';
    foreach ($values as $index => $jumlah) {
        $produkId = $index + 1; // according to $products order
        $harga = $defaultHargaPerProduk[$produkId] ?? 0;
        $importSheet->setCellValue("A{$row}", $produkId);
        $importSheet->setCellValue("B{$row}", $jumlah);
        $importSheet->setCellValue("C{$row}", $harga);
        $importSheet->setCellValue("D{$row}", 0); // dummy cash received
        $importSheet->setCellValue("E{$row}", $date);
        $row++;
    }
}

// 3) ProdukMapping sheet
$mapSheet = $spreadsheet->createSheet();
$mapSheet->setTitle('ProdukMapping');
$mapSheet->setCellValue('A1', 'produk_id');
$mapSheet->setCellValue('B1', 'produk_nama');
$row = 2;
foreach ($products as $pid => $name) {
    $mapSheet->setCellValue("A{$row}", $pid);
    $mapSheet->setCellValue("B{$row}", $name);
    $row++;
}

// Output
$outputDir = dirname(__DIR__) . '/samples';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$outputFile = $outputDir . '/penjualan_import_dummy.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($outputFile);

echo "Generated: {$outputFile}\n";
