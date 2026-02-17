@extends('app')

@section('content')
<div class="container-fluid">
    <h1>Riwayat Transaksi</h1>

    <!-- Flash Message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex gap-5 flex-wrap">
        <!-- Form Filter untuk Bulan dan Tahun -->
        <form method="GET" action="{{ route('penjualan.index') }}" class="mb-3">
            <div class="d-flex gap-2">
                <div>
                    <select name="bulan" id="bulan" class="form-select form-select-sm">
                        <option value="">Pilih Bulan</option>
                        @foreach ($months as $key => $month)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('m', $key)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <select name="tahun" id="tahun" class="form-select form-select-sm">
                        <option value="">Pilih Tahun</option>
                        @foreach ($years as $year => $data)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary btn-sm form-control">Filter</button>
                </div>
            </div>
        </form>
        
    
        <!-- Form Upload Excel -->
        <form action="{{ route('penjualan.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="d-flex gap-2">
                <div>
                    
                    <input type="file" name="file" class="form-control form-control-sm" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary btn-sm">Import</button>
                </div>
            </div>
        </form>
    <!-- Tombol Hapus Semua -->
    <div>  
        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteAllModal">Hapus Semua</button>
</div>
    </div>
    

  <!-- Tabel Penjualan -->
<div style="overflow-x: auto; width: 100%;">
    <table class="table table-bordered table-striped table-hover" style="min-width: 1000px;">
        <thead class="table-light sticky-top bg-light">
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPenjualan = 0; @endphp
            @foreach ($penjualan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->produk->nama }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>Rp {{ number_format($item->harga, 2) }}</td>
                    <td>Rp {{ number_format($item->total_harga, 2) }}</td>
                    <td>{{ $item->metode_pembayaran }}</td>
                    <td>{{ $item->payment_status }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('penjualan.edit', $item->id_penjualan) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('penjualan.destroy', $item->id_penjualan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @php $totalPenjualan += $item->total_harga; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total Penjualan:</th>
                <th>Rp {{ number_format($totalPenjualan, 2) }}</th>
                <th colspan="4"></th>
            </tr>
        </tfoot>
    </table>
</div>



    <!-- Modal Konfirmasi Hapus Semua -->
<div class="modal fade" id="confirmDeleteAllModal" tabindex="-1" aria-labelledby="confirmDeleteAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteAllModalLabel">Konfirmasi Hapus Semua</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus semua transaksi? Tindakan ini tidak bisa dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <form action="{{ route('penjualan.deleteAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Hapus Semua</button>
                </form>
            </div>
        </div>
    </div>
</div>

</div>

@endsection
