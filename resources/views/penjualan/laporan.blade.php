@extends('app')

@section('content')
<div class="container-fluid">
<!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 page-title" style="font-weight: 700; ">
                    <i class="bi bi-file-earmark-text"></i> Laporan Penjualan
                </h2>
            </div>
        </div>
    </div>
    <!-- Filter -->
    <form method="GET" action="{{ route('penjualan.laporan') }}" class="mb-4">
        <div class="row">
            <!-- Jenis Filter -->
            <div class="col-md-3">
                <label for="filter" class="form-label">Filter</label>
                <select name="filter" id="filter" class="form-select form-select-sm" onchange="updateFilterFields()">
                    <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $filter == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            <!-- Tahun -->
            <div class="col-md-3" id="tahun-field">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select form-select-sm">
                    @for ($i = now()->year; $i >= now()->year - 10; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Bulan -->
            <div class="col-md-3" id="bulan-field">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-select form-select-sm">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal -->
            <div class="col-md-3 d-none" id="tanggal-field">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm">
            </div>

            <!-- Minggu -->
            <div class="col-md-3 d-none" id="minggu-field">
                <label for="minggu" class="form-label">Minggu ke-</label>
                <input type="number" name="minggu" id="minggu" class="form-control form-control-sm" min="1" max="52" value="{{ $minggu }}">
            </div>

            <!-- Tombol -->
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-sm">Tampilkan</button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <table id="data-laporan" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Jumlah Terjual</th>
                <th>Jumlah Pendapatan (Rp)</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penjualan as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->produk->nama }}</td>
                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->total_harga, 2, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Data tidak tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Ringkasan -->
    <div class="mt-4 d-flex justify-content-between">
        <div>
            <h5>Total Jumlah Produk Terjual: {{ $formattedTotalJumlahProduk }}</h5>
            <h5>Total Pendapatan: Rp {{ $formattedTotalPenjualan }}</h5>
        </div>
        <div>
            <button onclick="printReport()" class="btn btn-secondary">Cetak Laporan</button>
        </div>
    </div>
</div>

<!-- JS -->
<script>
function updateFilterFields() {
    var filter = document.getElementById('filter').value;
    document.getElementById('tahun-field').classList.remove('d-none');
    document.getElementById('bulan-field').classList.add('d-none');
    document.getElementById('tanggal-field').classList.add('d-none');
    document.getElementById('minggu-field').classList.add('d-none');

    if (filter === 'harian') {
        document.getElementById('tanggal-field').classList.remove('d-none');
    } else if (filter === 'mingguan') {
        document.getElementById('minggu-field').classList.remove('d-none');
    } else if (filter === 'bulanan') {
        document.getElementById('bulan-field').classList.remove('d-none');
    }
}
window.onload = updateFilterFields;

function printReport() {
    var table = document.getElementById('data-laporan').outerHTML;
    var mywindow = window.open('', 'PRINT', 'height=650,width=900,top=100,left=150');

    mywindow.document.write('<html><head><title>Laporan Penjualan</title>');
    mywindow.document.write('<style>table {width: 100%; border-collapse: collapse;} th, td {border:1px solid #000; padding:8px;}</style>');
    mywindow.document.write('</head><body>');
    mywindow.document.write('<h1 style="text-align:center;">Laporan Penjualan</h1>');
    mywindow.document.write(table);
    mywindow.document.write('</body></html>');

    mywindow.document.close();
    mywindow.focus();
    mywindow.print();
    mywindow.close();
}
</script>
@endsection
