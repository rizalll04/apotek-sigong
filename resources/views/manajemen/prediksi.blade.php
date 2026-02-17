@extends('app')

@section('content')
<div class="container-fluid">
    <h1 class="my-4">Peramalan Penjualan: {{ $namaProduk ?? 'Produk' }}</h1>

    @if(isset($message))
        <div class="alert alert-warning">{{ $message }}</div>
    @endif

    <div class="card p-3 mb-4">
        <form action="{{ route('manajemen.prediksi', ['id' => $id]) }}" method="GET" class="row g-2">
            <div class="col-auto">
                <label for="tahun" class="form-label">Pilih Tahun (digunakan bila tidak mulai dari sekarang):</label>
                <select name="tahun" id="tahun" class="form-control">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ (isset($tahunFilter) && $tahunFilter == $y) ? 'selected' : (request('tahun') == $y ? 'selected' : '') }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-auto">
                <label for="start_from_now" class="form-label">Mulai dari bulan berikutnya:</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="start_from_now" name="start_from_now" value="1" {{ (isset($start_from_now) && $start_from_now) || request('start_from_now') ? 'checked' : '' }}>
                    <label class="form-check-label" for="start_from_now">Ya</label>
                </div>
            </div>

            <div class="col-auto">
                <label for="horizon" class="form-label">Horizon (bulan)</label>
                <input type="number" name="horizon" id="horizon" class="form-control" min="1" value="{{ $horizon ?? request('horizon') ?? 6 }}">
            </div>

            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
        <div class="small text-muted mt-2">Centang <strong>Mulai dari bulan berikutnya</strong> untuk meramalkan dari bulan setelah bulan sekarang (mis. jika sekarang Des 2025, peramalan dimulai Jan 2026).</div>
    </div>

    <!-- Parameter Optimization Results Section -->
    @if($optimizedParametersUsed && isset($optimalParams))
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">⚙️ Parameter Optimal (Hasil Optimasi Otomatis)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>α (Alpha):</strong><br>
                                <span class="badge bg-primary">{{ number_format($optimalParams['alpha'], 2) }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>β (Beta):</strong><br>
                                <span class="badge bg-info">{{ number_format($optimalParams['beta'], 2) }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>γ (Gamma):</strong><br>
                                <span class="badge bg-warning text-dark">{{ number_format($optimalParams['gamma'], 2) }}</span>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>MAPE Optimal:</strong><br>
                                <span class="badge bg-danger">{{ number_format($optimalParams['mape'], 2) }}%</span>
                            </p>
                            <p class="text-muted small">
                                <strong>Fase:</strong> {{ ucfirst($optimizationMetadata['optimization_phase'] ?? 'coarse') }}<br>
                                <strong>Waktu:</strong> {{ $optimizationMetadata['computation_time_ms'] ?? 'N/A' }}ms
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">📊 Kualitas Data</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Skor Kualitas Data:</strong><br>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($qualityAnalysis['data_quality_score'] ?? 0.5) * 100 }}%">
                                {{ number_format(($qualityAnalysis['data_quality_score'] ?? 0.5) * 100, 0) }}%
                            </div>
                        </div>
                    </p>

                    @if(!empty($qualityAnalysis['warnings']))
                        <p class="mb-2"><strong>⚠️ Peringatan:</strong></p>
                        <ul class="small mb-0">
                            @foreach($qualityAnalysis['warnings'] as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-success small"><strong>✓ Data berkualitas baik</strong></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!isset($message))
        @if(isset($start_from_now) && $start_from_now)
            <div class="mb-2">Menampilkan peramalan mulai <strong>{{ \DateTime::createFromFormat('!m', $startMonth)->format('F') }} {{ $startYear }}</strong> selama <strong>{{ $horizon }}</strong> bulan.</div>
        @else
            <div class="mb-2">Menampilkan peramalan untuk tahun <strong>{{ $tahunFilter }}</strong>.</div>
        @endif
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Actual Penjualan</th>
                    <th>Peramalan (Holt-Winters)</th>
                    <th>MAPE (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $row)
                <tr>
                    <td>{{ $row['nama_bulan'] }} {{ $row['tahun'] }}</td>
                    <td>{{ $row['actual'] ?? '-' }}</td>
                    <td>{{ $row['pred'] ?? '-' }}</td>
                    <td>{{ $row['error'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-info">
                    <td colspan="3" class="text-end"><strong>Rata-rata MAPE:</strong></td>
                    <td><strong>{{ $avgMape ?? '-' }} {{ isset($avgMape) ? '%' : '' }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

</div>
@endsection
