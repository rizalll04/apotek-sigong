<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Services\ForecastOptimizationService;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PersediaanController extends Controller
{
    protected $forecastService;

    public function __construct(ForecastOptimizationService $forecastService)
    {
        $this->forecastService = $forecastService;
    }


  
    
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Ambil input bulan, tahun, dan kata kunci pencarian dari request
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $search = $request->search; // Kata kunci pencarian
    
        // Query dasar untuk mendapatkan data penjualan
        $penjualanQuery = Penjualan::with('produk');
    
        // Filter berdasarkan bulan jika ada
        if ($bulan) {
            $penjualanQuery->whereMonth('tanggal', $bulan);
        }
    
        // Filter berdasarkan tahun jika ada
        if ($tahun) {
            $penjualanQuery->whereYear('tanggal', $tahun);
        }
    
        // Filter berdasarkan pencarian nama produk jika ada
        if ($search) {
            $penjualanQuery->whereHas('produk', function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%');
            });
        }
    
        // Ambil data penjualan yang sudah difilter
        $penjualan = $penjualanQuery->get();
    
        // Hitung jumlah terjual untuk setiap produk
        $produkTerjual = $penjualan
            ->groupBy('produk_id')
            ->map(function ($item) {
                return [
                    'produk' => $item->first()->produk->nama ?? 'Tidak diketahui',
                    'jumlah_terjual' => $item->sum('jumlah'),
                ];
            });
    
        // Tampilkan view dengan data produk terjual
        return view('manajemen.index', compact('produkTerjual', 'bulan', 'tahun', 'search'));
    }
    

    /**
     * Prediksi pembelian untuk suatu produk.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Prediksi pembelian untuk suatu produk menggunakan triple exponential smoothing (Holt-Winters additive).
     * - Menggunakan perioda musiman = 12 (bulan)
     * - Default smoothing (alpha, beta, gamma) dapat disesuaikan via query params
     */
    public function prediksi($id, Request $request)
    {
        // Ambil parameter
        $tahunFilter = $request->get('tahun', now()->year);
        $startFromNow = $request->boolean('start_from_now', false);
        $horizon = max(1, (int) $request->get('horizon', 12)); // jumlah bulan yang ingin diprediksi

        // Ambil data penjualan per bulan untuk produk
        $rows = Penjualan::where('produk_id', $id)
            ->selectRaw('YEAR(tanggal) as tahun, MONTH(tanggal) as bulan, SUM(jumlah) as total_terjual')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        if ($rows->isEmpty()) {
            return view('manajemen.prediksi', [
                'message' => 'Tidak ditemukan data penjualan untuk produk ini.',
                'id' => $id,
                'namaProduk' => Produk::find($id)->nama ?? 'Produk',
                'tahunFilter' => $tahunFilter,
                'start_from_now' => false,
                'horizon' => null,
                'alpha' => null,
                'beta' => null,
                'gamma' => null,
                'optimizedParametersUsed' => false,
            ]);
        }

        // Buat series bulanan lengkap (isi 0 bila tidak ada penjualan pada bulan tertentu)
        $dates = [];
        $dataMap = [];
        foreach ($rows as $r) {
            $key = $r->tahun . '-' . str_pad($r->bulan, 2, '0', STR_PAD_LEFT);
            $dataMap[$key] = (int) $r->total_terjual;
            $dates[$key] = ['tahun' => $r->tahun, 'bulan' => $r->bulan];
        }

        $first = collect(array_keys($dates))->first();
        $last = collect(array_keys($dates))->last();

        list($startYearSeries, $startMonthSeries) = explode('-', $first);
        list($endYearSeries, $endMonthSeries) = explode('-', $last);
        $startYearSeries = (int) $startYearSeries; $startMonthSeries = (int) $startMonthSeries;
        $endYearSeries = (int) $endYearSeries; $endMonthSeries = (int) $endMonthSeries;

        $series = [];
        $dateIndex = []; // index -> ['tahun','bulan']
        $idx = 0;
        $currYear = $startYearSeries; $currMonth = $startMonthSeries;
        while ($currYear < $endYearSeries || ($currYear == $endYearSeries && $currMonth <= $endMonthSeries)) {
            $k = $currYear . '-' . str_pad($currMonth, 2, '0', STR_PAD_LEFT);
            $val = $dataMap[$k] ?? 0;
            $series[] = $val;
            $dateIndex[$idx] = ['tahun' => $currYear, 'bulan' => $currMonth];
            $idx++;
            $currMonth++;
            if ($currMonth > 12) { $currMonth = 1; $currYear++; }
        }

        $n = count($series);
        $m = 12; // season length

        if ($n < 24) {
            return view('manajemen.prediksi', [
                'message' => 'Tidak cukup data historis (minimal 2 tahun) untuk melakukan prediksi musiman.',
                'id' => $id,
                'namaProduk' => Produk::find($id)->nama ?? 'Produk',
                'tahunFilter' => $tahunFilter,
                'start_from_now' => false,
                'horizon' => null,
                'alpha' => null,
                'beta' => null,
                'gamma' => null,
                'optimizedParametersUsed' => false,
            ]);
        }

        // Ambil smoothing params dari optimasi otomatis
        $optimizationResult = $this->forecastService->optimizeParameters($id, 12);
        $alpha = $optimizationResult['optimal']['alpha'];
        $beta = $optimizationResult['optimal']['beta'];
        $gamma = $optimizationResult['optimal']['gamma'];
        $mapeOptimal = $optimizationResult['optimal']['mape'];

        // Inisialisasi: compute season averages
        $seasons = intdiv($n, $m);
        $seasonAverages = [];
        for ($s = 0; $s < $seasons; $s++) {
            $sum = 0; $count = 0;
            for ($j = 0; $j < $m; $j++) {
                $idxS = $s * $m + $j;
                if ($idxS < $n) { $sum += $series[$idxS]; $count++; }
            }
            $seasonAverages[$s] = $count ? $sum / $count : 0;
        }

        $seasonals = array_fill(0, $m, 0.0);
        for ($j = 0; $j < $m; $j++) {
            $sum = 0.0; $cnt = 0;
            for ($s = 0; $s < $seasons; $s++) {
                $idxS = $s * $m + $j;
                if ($idxS < $n) {
                    $sum += ($series[$idxS] - $seasonAverages[$s]);
                    $cnt++;
                }
            }
            $seasonals[$j] = $cnt ? ($sum / $cnt) : 0.0;
        }

        // Initial level and trend
        $level = $seasonAverages[0];
        $trend = 0.0;
        if ($seasons > 1) {
            $sumDiff = 0.0; $countDiff = 0;
            for ($s = 0; $s < $seasons - 1; $s++) {
                $sumDiff += ($seasonAverages[$s + 1] - $seasonAverages[$s]); $countDiff++;
            }
            $trend = $countDiff ? ($sumDiff / $countDiff) / $m : 0.0;
        }

        // Fit Holt-Winters (additive)
        $l = $level; $b = $trend; $sArr = $seasonals; $fitted = [];
        for ($t = 0; $t < $n; $t++) {
            $val = $series[$t];
            $prevL = $l; $l = $alpha * ($val - $sArr[$t % $m]) + (1 - $alpha) * ($l + $b);
            $b = $beta * ($l - $prevL) + (1 - $beta) * $b;
            $sArr[$t % $m] = $gamma * ($val - $l) + (1 - $gamma) * $sArr[$t % $m];
            $fitted[$t] = $l + $b + $sArr[$t % $m];
        }

        // Tentukan start month/year dan hitung kebutuhan horizon forecast
        if ($startFromNow) {
            $startDate = \Illuminate\Support\Carbon::now()->addMonth();
            $startYear = $startDate->year; $startMonth = $startDate->month;
        } else {
            $startYear = (int) $tahunFilter; $startMonth = 1;
        }

        $firstYear = $dateIndex[0]['tahun'];
        $firstMonth = $dateIndex[0]['bulan'];

        $startOffset = ($startYear - $firstYear) * 12 + ($startMonth - $firstMonth);
        $finalOffset = $startOffset + $horizon - 1;

        $forecastHorizonNeeded = max($horizon, max(0, $finalOffset - ($n - 1)));

        // Forecast sampai forecastHorizonNeeded
        $forecasts = [];
        for ($k = 1; $k <= $forecastHorizonNeeded; $k++) {
            $indexSeason = ($n + $k - 1) % $m;
            $forecasts[] = max(0, $l + $k * $b + $sArr[$indexSeason]);
        }

        // Build sequential results starting from startYear/startMonth for $horizon months
        $results = [];
        for ($i = 0; $i < $horizon; $i++) {
            $mth = $startMonth + $i;
            $yr = $startYear + intdiv($mth - 1, 12);
            $mon = ($mth - 1) % 12 + 1;

            $offset = ($yr - $firstYear) * 12 + ($mon - $firstMonth);

            if ($offset >= 0 && $offset < $n) {
                $actual = $series[$offset];
                $pred = $fitted[$offset] ?? null; // in-sample use fitted
            } elseif ($offset >= $n) {
                $k = $offset - $n + 1;
                $actual = null;
                $pred = ($k >= 1 && $k <= count($forecasts)) ? $forecasts[$k - 1] : null;
            } else {
                $actual = null;
                $pred = null;
            }

            $results[] = [
                'tahun' => $yr,
                'bulan' => $mon,
                'nama_bulan' => \DateTime::createFromFormat('!m', $mon)->format('F'),
                'actual' => $actual,
                'pred' => isset($pred) ? round($pred, 2) : null,
                'error' => (isset($actual) && isset($pred) && $actual > 0) ? round(abs($actual - $pred) / $actual * 100, 2) : null,
            ];
        }

        // Hitung MAPE rata-rata untuk bulan yang memiliki actual dan pred
        $totalError = 0; $countError = 0;
        foreach ($results as $r) {
            if (!is_null($r['error'])) { $totalError += $r['error']; $countError++; }
        }
        $avgMape = $countError ? round($totalError / $countError, 2) : null;

        return view('manajemen.prediksi', [
            'id' => $id,
            'namaProduk' => Produk::find($id)->nama ?? 'Produk',
            'results' => $results,
            'avgMape' => $avgMape,
            'alpha' => $alpha,
            'beta' => $beta,
            'gamma' => $gamma,
            'message' => null,
            'tahunFilter' => $tahunFilter,
            'start_from_now' => $startFromNow,
            'horizon' => $horizon,
            'startYear' => $startYear,
            'startMonth' => $startMonth,
            'optimalParams' => $optimizationResult['optimal'],
            'optimizationMetadata' => $optimizationResult['metadata'],
            'qualityAnalysis' => $optimizationResult['quality_analysis'],
            'optimizedParametersUsed' => true,
        ]);
    }
     
     
    
     
    
    
    
}
