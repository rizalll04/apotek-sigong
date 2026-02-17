@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="page-title mb-2"><i class="bi bi-speedometer2"></i> Dashboard Owner</h1>
        <p class="page-subtitle">Ringkasan data dan statistik apotek Anda</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Produk -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Produk</p>
                            <h3 class="mb-0" style="color: #0d6efd;">{{ $totalProduk }}</h3>
                        </div>
                        <div class="p-3" style="background: rgba(13, 110, 253, 0.1); border-radius: 0.75rem;">
                            <i class="bi bi-box-seam" style="font-size: 1.75rem; color: #0d6efd;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terjual -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Produk Terjual</p>
                            <h3 class="mb-0" style="color: #ffc107;">{{ $totalProdukTerjual }}</h3>
                        </div>
                        <div class="p-3" style="background: rgba(255, 193, 7, 0.1); border-radius: 0.75rem;">
                            <i class="bi bi-bag-check" style="font-size: 1.75rem; color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Penjualan</p>
                            <h3 class="mb-0" style="color: #198754;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-3" style="background: rgba(25, 135, 84, 0.1); border-radius: 0.75rem;">
                            <i class="bi bi-cash-coin" style="font-size: 1.75rem; color: #198754;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Tersisa -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Stok Tersisa</p>
                            <h3 class="mb-0" style="color: #dc3545;">{{ $stokTersisa }}</h3>
                        </div>
                        <div class="p-3" style="background: rgba(220, 53, 69, 0.1); border-radius: 0.75rem;">
                            <i class="bi bi-exclamation-triangle" style="font-size: 1.75rem; color: #dc3545;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-3 mb-4 align-items-start">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Grafik Penjualan</h5>
                    <form method="GET" action="{{ route('owner.dashboard') }}" class="d-flex align-items-center gap-2">
                        <label for="tahun" class="form-label mb-0 me-1">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select" style="width: 120px;" onchange="this.form.submit()">
                            @foreach(range(date('Y'), 2000) as $tahunOption)
                                <option value="{{ $tahunOption }}" {{ request('tahun') == $tahunOption ? 'selected' : '' }}>{{ $tahunOption }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <div id="traffic-overview"></div>
                </div>
            </div>
        </div>

        <!-- Best Selling Products -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-star-fill"></i> Produk Terlaris</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($produkTerlaris as $produk)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $produk->nama }}</h6>
                                        <small class="text-muted">Penjualan</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $produk->penjualan_count }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="text-muted mb-0">Belum ada data penjualan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Links removed for Owner: no quick actions requested -->
</div>

<!-- Chart Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.0/apexcharts.min.js"></script>
<script>
    // Sales Chart
    var options = {
        series: [{
            name: 'Penjualan',
            data: {!! json_encode($grafikPenjualanData) !!}
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: false
            }
        },
        colors: ['#0d6efd'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            }
        },
        xaxis: {
            categories: {!! json_encode($grafikPenjualanLabels) !!},
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            title: {
                text: 'Rp'
            }
        },
        tooltip: {
            x: {
                format: 'dddd'
            },
            y: {
                formatter: function(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#traffic-overview"), options);
    chart.render();
</script>

<!-- Additional Styles -->
<style>
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
    }

    .page-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
    }

    .list-group-item {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endsection
