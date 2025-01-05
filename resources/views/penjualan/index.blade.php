@extends('app')

@section('content')
<div class="container-fluid">

    <h1>Daftar Penjualan</h1>

    <!-- Flash Message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Penjualan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPenjualan = 0; @endphp
            @foreach ($penjualan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>Rp {{ number_format($item->harga, 2) }}</td>
                    <td>Rp {{ number_format($item->total_harga, 2) }}</td>
                    <td>{{ $item->created_at->format('d M Y') }}</td>
                </tr>
                @php $totalPenjualan += $item->total_harga; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total Penjualan:</th>
                <th>Rp {{ number_format($totalPenjualan, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

@endsection
