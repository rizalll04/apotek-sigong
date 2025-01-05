@extends('app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Laporan Penjualan</h2>

    <!-- Filter Tahun -->
    <form method="GET" action="{{ route('penjualan.laporan') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="tahun" class="form-label">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    @for ($i = now()->year; $i >= now()->year - 10; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </div>
    </form>

    <!-- Tabel Laporan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Jumlah Terjual</th>
                <th>Jumlah Pendapatan (Rp)</th>
                <th>Bulan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penjualan as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->total_harga, 2, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('F') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Data tidak tersedia untuk tahun ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Total -->
    <div class="mt-4">
        <h5>Total Jumlah Produk Terjual: {{ $formattedTotalJumlahProduk }}</h5>
        <h5>Total Pendapatan: Rp {{ $formattedTotalPenjualan }}</h5>
    </div>
</div>
@endsection
