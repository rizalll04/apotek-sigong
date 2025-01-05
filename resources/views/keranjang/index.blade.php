@extends('app')

@section('content')
<div class="container-fluid">
    <div class="container mt-5">
        <h1 style="text-align: center; margin-bottom: 20px; font-family: 'Courier New', monospace;">Kasir - Keranjang Belanja</h1>

        <div class="row">
            <!-- Card Pencarian Produk dan Keranjang -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Cari Produk dan Masukkan ke Keranjang</h4>
                    </div>
                    <div class="card-body">
                  <!-- Form Pencarian Produk -->
<form method="GET" action="{{ route('keranjang.index') }}" class="mb-3">
    <div class="input-group">
        <input type="text" name="q" id="q" class="form-control" value="{{ $query ?? '' }}" placeholder="Cari Produk...">
        <button type="submit" class="btn btn-outline-primary">Cari</button>
    </div>
</form>

@if ($produk && $produk->isNotEmpty())
    <h5 class="mt-4" style="font-size: 1.2rem;">Hasil Pencarian</h5>
    <ul class="list-group mb-4" style="font-size: 0.9rem;">
        @foreach ($produk as $item)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ $item->nama_produk }} - Rp {{ number_format($item->harga_jual, 2) }}</span>
                <form method="POST" action="{{ route('keranjang.tambah') }}" class="d-flex align-items-center">
                    @csrf
                    <input type="hidden" name="user_id" value="1"> <!-- ID user hardcoded -->
                    <input type="hidden" name="produk_id" value="{{ $item->id_produk }}">
                    <div class="input-group" style="max-width: 180px;">
                        <input type="number" name="jumlah" class="form-control w-50" value="1" min="1" style="font-size: 0.8rem;">
                        <button type="submit" class="btn btn-success btn-sm" style="font-size: 0.8rem;">Tambah</button>
                    </div>
                </form>
            </li>
        @endforeach
    </ul>
@elseif($produk && $produk->isEmpty())
    <div class="alert alert-warning mt-4" role="alert">
        Produk yang Anda cari tidak ditemukan.
    </div>
@endif

                    
                    </div>
                </div>
            </div>

         <!-- Card Pembayaran -->
<div class="col-md-6" style="padding: 20px;">
    <div class="card" style="border: 1px solid #ddd; padding: 15px;">
        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #ddd;">
            <h4 style="margin: 0; font-size: 1.25rem;">Proses Pembayaran</h4>
        </div>
        <div class="card-body" style="padding: 20px;">
            <h5 style="font-size: 1.1rem; font-weight: bold; margin-bottom: 15px;">Produk di Keranjang</h5>
            <div class="container" style="padding: 10px 0;">
                @php $totalBelanja = 0; @endphp
                @foreach ($keranjang as $item)
                    @php $totalBelanja += $item->jumlah * $item->harga_satuan; @endphp
                    <div class="card mb-3" style="border: 1px solid #ddd; padding: 10px; display: flex; justify-content: space-between;">
                        <div style="flex: 1; padding-right: 15px;">
                            <strong>{{ $item->produk->nama_produk }}</strong>
                            <div style="display: flex; justify-content: space-between;">
                                <div>Harga Satuan: <span style="color: #007bff;"></span></div>
                                <div>Rp {{ number_format($item->harga_satuan, 2) }}</div>
                            </div>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px;">
                            <!-- Form Ubah Jumlah -->
                            <div style="display: flex; align-items: center;">
                                <form method="POST" action="{{ route('keranjang.ubah-jumlah', $item->id_keranjang) }}" style="display: flex; align-items: center;">
                                    @csrf
                                    <input type="number" name="jumlah" class="form-control" value="{{ $item->jumlah }}" min="1" style="font-size: 0.9rem; width: 60px;">
                                    <button type="submit" class="btn btn-warning btn-sm" style="font-size: 0.9rem; margin-left: 10px;">Ubah</button>
                                </form>
                            </div>
                            

                        
                            <!-- Form Hapus -->
                            <div style="flex: 0 0 80px; text-align: center;">
                                <form method="POST" action="{{ route('keranjang.hapus', $item->id_keranjang) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.9rem;">Hapus</button>
                                </form>
                            </div>
                        </div>
                        
                    </div>
                @endforeach

                <div class="card" style="border: 1px solid #ddd; padding: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: bold;">
                    <div>Total Belanja:</div>
                    <div>Rp {{ number_format($totalBelanja, 2) }}</div>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <form method="POST" action="{{ route('penjualan.simpan') }}" class="mb-4">
                @csrf
                <div class="form-group mb-3">
                    <label for="uangDiterima" style="font-weight: bold;">Uang Diterima</label>
                    <input type="number" name="uang_diterima" id="uangDiterima" class="form-control" placeholder="Masukkan uang yang diterima" min="{{ $totalBelanja }}" required style="font-size: 1rem; padding: 10px;">
                </div>
                <div class="form-group mb-3">
                    <label for="kembalian" style="font-weight: bold;">Kembalian</label>
                    <input type="text" id="kembalian" class="form-control" value="Rp 0" readonly style="font-size: 1rem; padding: 10px;">
                </div>
                <input type="hidden" name="total_belanja" value="{{ $totalBelanja }}">
                <button type="submit" class="btn btn-primary btn-lg btn-block" style="font-size: 1.1rem; padding: 10px;">Simpan Transaksi</button>
            </form>
        </div>
    </div>
</div>

        </div>
    </div>
</div>

<script>
    // Script untuk menghitung kembalian
    document.getElementById('uangDiterima').addEventListener('input', function () {
        let totalBelanja = {{ $totalBelanja }};
        let uangDiterima = parseInt(this.value);
        let kembalian = uangDiterima - totalBelanja;

        if (kembalian >= 0) {
            document.getElementById('kembalian').value = 'Rp ' + kembalian.toLocaleString();
        } else {
            document.getElementById('kembalian').value = 'Rp 0';
        }
    });
</script>
@endsection
