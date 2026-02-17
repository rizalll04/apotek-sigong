@extends('app')

@section('content')
<div class="container-fluid">
    <div class="container">
        <h1>Riwayat Pembelian</h1>

        <!-- Form Filter untuk Bulan dan Tahun -->
        <form method="GET" action="{{ route('pembelian.riwayat') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="bulan">Bulan</label>
                    <select name="bulan" id="bulan" class="form-control">
                        <option value="">Pilih Bulan</option>
                        <option value="1" {{ request('bulan') == 1 ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ request('bulan') == 2 ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ request('bulan') == 3 ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ request('bulan') == 4 ? 'selected' : '' }}>April</option>
                        <option value="5" {{ request('bulan') == 5 ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ request('bulan') == 6 ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ request('bulan') == 7 ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ request('bulan') == 8 ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ request('bulan') == 9 ? 'selected' : '' }}>September</option>
                        <option value="10" {{ request('bulan') == 10 ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ request('bulan') == 11 ? 'selected' : '' }}>November</option>
                        <option value="12" {{ request('bulan') == 12 ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tahun">Tahun</label>
                    <select name="tahun" id="tahun" class="form-control">
                        <option value="">Pilih Tahun</option>
                        @for ($i = 2020; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary form-control">Filter</button>
                </div>
            </div>
        </form>

        <!-- Tabel Riwayat Pembelian -->
        @if($pembelian->isEmpty())
            <div class="alert alert-warning">Tidak ada data riwayat pembelian.</div>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Tanggal Pembelian</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->produk->nama }}</td> <!-- Asumsi produk memiliki relasi dengan Pembelian -->
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ $item->supplier }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
