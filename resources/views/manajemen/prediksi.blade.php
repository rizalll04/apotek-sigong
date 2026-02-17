@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-graph-up"></i> Peramalan Penjualan</h1>
        <p class="page-subtitle">{{ $namaProduk ?? 'Produk' }} - Prediksi menggunakan metode Holt-Winters</p>
    </div>

    <!-- Alert Warning -->
    @if(isset($message))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Parameter Peramalan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('manajemen.prediksi', ['id' => $id]) }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="tahun" class="form-label fw-600">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ (isset($tahunFilter) && $tahunFilter == $y) ? 'selected' : (request('tahun') == $y ? 'selected' : '') }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="horizon" class="form-label fw-600">Horizon (bulan)</label>
                        <input type="number" name="horizon" id="horizon" class="form-control" min="1" value="{{ $horizon ?? request('horizon') ?? 6 }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-600">Mulai dari Bulan Berikutnya</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="start_from_now" name="start_from_now" value="1" {{ (isset($start_from_now) && $start_from_now) || request('start_from_now') ? 'checked' : '' }}>
                            <label class="form-check-label" for="start_from_now">Ya, mulai dari bulan depan</label>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Tampilkan
                        </button>
                    </div>
                </div>
                <div class="small text-muted mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Centang opsi "Mulai dari Bulan Berikutnya" untuk meramalkan dari bulan setelah bulan sekarang.
                </div>
            </form>
        </div>
    </div>

    <!-- Parameter Optimization Results Section -->
    @if($optimizedParametersUsed && isset($optimalParams))
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm border-3 border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-sliders me-2"></i>Parameter Optimal (Otomatis)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted">α (Alpha - Level)</small><br>
                                <span class="badge bg-primary fs-6">{{ number_format($optimalParams['alpha'], 3) }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">β (Beta - Trend)</small><br>
                                <span class="badge bg-info fs-6">{{ number_format($optimalParams['beta'], 3) }}</span>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted">γ (Gamma - Seasonality)</small><br>
                                <span class="badge bg-warning text-dark fs-6">{{ number_format($optimalParams['gamma'], 3) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <small class="text-muted">MAPE Optimal</small><br>
                                <span class="badge bg-danger fs-6">{{ number_format($optimalParams['mape'], 2) }}%</span>
                            </div>
                            <div class="small text-muted">
                                <p class="mb-2"><strong>Fase:</strong> {{ ucfirst($optimizationMetadata['optimization_phase'] ?? 'coarse') }}</p>
                                <p class="mb-0"><strong>Waktu:</strong> {{ $optimizationMetadata['computation_time_ms'] ?? 'N/A' }}ms</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Kualitas Data</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Skor Kualitas Data</small>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: {{ ($qualityAnalysis['data_quality_score'] ?? 0.5) * 100 }}%">
                                {{ number_format(($qualityAnalysis['data_quality_score'] ?? 0.5) * 100, 0) }}%
                            </div>
                        </div>
                    </div>

                    @if(!empty($qualityAnalysis['warnings']))
                        <div>
                            <p class="mb-2"><i class="bi bi-exclamation-triangle text-warning me-1"></i><strong>Peringatan:</strong></p>
                            <ul class="small mb-0">
                                @foreach($qualityAnalysis['warnings'] as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-success small mb-0"><i class="bi bi-check-circle me-1"></i><strong>Data berkualitas baik</strong></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Results Section -->
    @if(!isset($message))
        @if(isset($start_from_now) && $start_from_now)
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Menampilkan peramalan mulai <strong>{{ \DateTime::createFromFormat('!m', $startMonth)->format('F') }} {{ $startYear }}</strong> selama <strong>{{ $horizon }}</strong> bulan.
            </div>
        @else
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                Menampilkan peramalan untuk tahun <strong>{{ $tahunFilter }}</strong>.
            </div>
        @endif

        <!-- Forecast Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-table"></i> Hasil Peramalan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="align-middle">Bulan</th>
                                <th class="align-middle text-center">Penjualan Aktual</th>
                                <th class="align-middle text-center">Peramalan (Holt-Winters)</th>
                                <th class="align-middle text-center">MAPE (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $row)
                            <tr>
                                <td class="align-middle fw-600">{{ $row['nama_bulan'] }} {{ $row['tahun'] }}</td>
                                <td class="align-middle text-center">
                                    @if($row['actual'])
                                        <span class="badge bg-primary">{{ $row['actual'] }} unit</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    @if($row['pred'])
                                        <span class="badge bg-success">{{ round($row['pred'], 0) }} unit</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    @if($row['error'])
                                        <span class="badge bg-warning text-dark">{{ $row['error'] }}%</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="align-middle text-end"><strong>Rata-rata MAPE:</strong></td>
                                <td class="align-middle text-center">
                                    <strong class="badge bg-info text-dark fs-6">{{ $avgMape ?? '-' }} {{ isset($avgMape) ? '%' : '' }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
