@extends('app')

@section('content')
<div class="container mt-4">
    <h3>Import Data Penjualan</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('penjualan.import.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Upload File Excel</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
    </form>

    <hr>

    <h4>Data Penjualan Terbaru</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produk ID</th>
                <th>nama</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total Harga</th>
                <th>Uang Diterima</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Kembalian</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan as $p)
            <tr>
                <td>{{ $p->id_penjualan }}</td>
                <td>{{ $p->produk_id }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->jumlah }}</td>
                <td>{{ number_format($p->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($p->total_harga, 0, ',', '.') }}</td>
                <td>{{ number_format($p->uang_diterima, 0, ',', '.') }}</td>
                <td>{{ number_format($p->kembalian, 0, ',', '.') }}</td>
                <td>{{ $p->tanggal }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $penjualan->links() }}
</div>
@endsection
