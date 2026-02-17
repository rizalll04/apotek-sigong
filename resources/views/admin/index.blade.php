@extends('app')

@section('content')
<div class="container-fluid">
   

    <div class="row g-">
        <!-- Total Produk -->
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-lg">
                <iconify-icon icon="mdi:package" class="fs-10 text-primary"></iconify-icon>
                <div class="ms-3">
                    <p class="mb-2">Produk</p>
                    <h6 class="mb-0">{{ $totalProduk }}</h6>
                </div>
            </div>
        </div>

        <!-- Produk Terjual -->
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-lg">
                <iconify-icon icon="mdi:history" class="fs-10 text-warning"></iconify-icon>
                <div class="ms-3">
                    <p class="mb-2">Produk Terjual</p>
                    <h6 class="mb-0">{{ $totalProdukTerjual }}</h6>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-lg">
                <iconify-icon icon="solar:wallet-bold-duotone" class="fs-10 text-success"></iconify-icon>
                <div class="ms-3">
                    <p class="mb-2">Penjualan</p>
                    <h6 class="mb-0">Rp.{{ number_format($totalPenjualan, 2) }}</h6>
                </div>
            </div>
        </div>

        <!-- Stok Tersisa -->
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-lg">
                <iconify-icon icon="solar:box-bold-duotone" class="fs-10 text-danger"></iconify-icon>
                <div class="ms-3">
                    <p class="mb-2">Stok Tersisa</p>
                    <h6 class="mb-0">{{ $stokTersisa }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Aksi Cepat</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('keranjang.index') }}" class="btn btn-light flex-fill text-start p-3 rounded-3 shadow-sm" style="min-width: 200px;">
                            <i class="bi bi-cart-fill me-2 text-primary"></i>
                            <div>
                                <div class="fw-bold">Transaksi Baru</div>
                                <small class="text-muted">Akses fitur yang sering digunakan</small>
                            </div>
                        </a>
                        <a href="{{ route('produk.index') }}" class="btn btn-light flex-fill text-start p-3 rounded-3 shadow-sm" style="min-width: 200px;">
                            <i class="bi bi-box-seam me-2 text-success"></i>
                            <div>
                                <div class="fw-bold">Kelola Produk</div>
                                <small class="text-muted">Tambah / edit produk</small>
                            </div>
                        </a>
                        </a>
            
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
   
 <!-- Grafik Penjualan -->
<div class="col-sm-12 col-xl-8">
    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Grafik Penjualan</h5>
            <!-- Form untuk memilih tahun -->
            <form method="GET" action="{{ route('admin.index') }}" class="d-flex align-items-center">
                <label for="tahun" class="form-label mb-0 me-2">Pilih Tahun:</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    @foreach(range(date('Y'), 2000) as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            <!-- Grafik Penjualan, menggunakan Chart.js -->
            <div id="traffic-overvieww"></div>
            
        </div>
    </div>
</div>

        <!-- Produk Terlaris -->
        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h5 class="mb-0">Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($produkTerlaris as $produk)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $produk->nama }}
                                <span class="badge bg-primary rounded-pill">{{ $produk->penjualan_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Grafik Chart.js -->

<script>
    $(function () {
      // -----------------------------------------------------------------------
      // Grafik Penjualan per Bulan
      // -----------------------------------------------------------------------
      
      var chart = {
        series: [
          {
            name: "Total Penjualan",
            data: @json($grafikPenjualanData), // Data total penjualan per bulan
          },
        ],
        chart: {
          toolbar: {
            show: false,
          },
          type: "line",
          fontFamily: "inherit",
          foreColor: "#adb0bb",
          height: 320,
          stacked: false,
        },
        colors: ["var(--bs-primary)"], // Warna garis
        plotOptions: {},
        dataLabels: {
          enabled: false,
        },
        legend: {
          show: false,
        },
        stroke: {
          width: 2,
          curve: "smooth",
          dashArray: [8, 0],
        },
        grid: {
          borderColor: "rgba(0,0,0,0.1)",
          strokeDashArray: 3,
          xaxis: {
            lines: {
              show: false,
            },
          },
        },
        yaxis: {
          tickAmount: 4,
        },
        xaxis: {
          axisBorder: {
            show: false,
          },
          axisTicks: {
            show: false,
          },
          categories: @json($grafikPenjualanLabels), // Menampilkan bulan (grafikPenjualanLabels)
        },
        markers: {
          strokeColor: ["var(--bs-primary)"],
          strokeWidth: 2,
        },
        tooltip: {
          theme: "dark",
        },
      };
  
      var chart = new ApexCharts(
        document.querySelector("#traffic-overvieww"),
        chart
      );
      chart.render();
    });
  </script>
  





@endsection
