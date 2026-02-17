@extends('app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">

    <h4 class="fw-bold mb-3">
        Penjualan
        <small class="text-muted d-block">Pilih produk untuk ditambahkan ke transaksi</small>
    </h4>

    <div class="row">

        <!-- ===================== DAFTAR PRODUK ===================== -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <!-- Search -->
                    <form method="GET" action="{{ route('keranjang.index') }}" class="mb-3">
                        <input type="text"
                               name="q"
                               class="form-control"
                               placeholder="Cari produk atau kategori..."
                               value="{{ $query ?? '' }}">
                    </form>

                    <!-- Grid Produk -->
                    <div class="row g-3">
                        @forelse ($produk as $item)
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">

                                        <!-- Info Produk -->
                                        <div>
                                            <div class="fw-bold text-uppercase">
                                                {{ $item->nama }}
                                            </div>
                                            <div class="text-primary fw-semibold">
                                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                            </div>
                                            <small class="text-muted">
                                                Stok {{ $item->stok }}
                                            </small>
                                        </div>

                                        <!-- Tombol + -->
                                        <form method="POST"
                                              action="{{ route('keranjang.tambah') }}"
                                              onsubmit="return checkStock(event, {{ $item->stok }})">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                            <input type="hidden" name="produk_id" value="{{ $item->id }}">
                                            <input type="hidden" name="jumlah" value="1">

                                            <button type="submit"
                                                class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center btn-add">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning text-center">
                                    Produk tidak ditemukan
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

        <!-- ===================== KERANJANG & PEMBAYARAN ===================== -->
        <div class="col-md-4">
            <!-- Pilih Resep -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    

            <!-- Keranjang -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Keranjang <small class="text-muted">{{ $keranjang->count() }} item</small></h6>
                    @php $subtotalAll = 0; @endphp
                    @if($keranjang && $keranjang->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($keranjang as $item)
                                @php $line = $item->jumlah * $item->harga_satuan; $subtotalAll += $line; @endphp
                                <li class="list-group-item" data-cart-id="{{ $item->id_keranjang }}">
                                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                        <!-- Product Info -->
                                        <div class="flex-grow-1 me-2">
                                            <div class="fw-bold">{{ $item->produk->nama }}</div>
                                            <small class="text-muted">Rp {{ number_format($item->harga_satuan,0,',','.') }}</small>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="qty-control flex-shrink-0">
                                            <button type="button" class="btn btn-sm"
                                                    onclick="updateQty({{ $item->id_keranjang }}, -1)"
                                                    title="Kurangi jumlah">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number"
                                                   id="qty{{ $item->id_keranjang }}"
                                                   class="form-control form-control-sm"
                                                   value="{{ $item->jumlah }}"
                                                   min="1"
                                                   max="{{ $item->produk->stok }}"
                                                   data-max-stock="{{ $item->produk->stok }}"
                                                   data-price="{{ $item->harga_satuan }}"
                                                   readonly
                                                   style="width: 50px;">
                                            <button type="button" class="btn btn-sm"
                                                    onclick="updateQty({{ $item->id_keranjang }}, 1)"
                                                    title="Tambah jumlah">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="text-end flex-shrink-0" style="min-width: 110px;">
                                            <small class="text-muted d-block">Subtotal</small>
                                            <span id="subtotal{{ $item->id_keranjang }}" class="fw-bold text-primary d-block">
                                                Rp {{ number_format($line, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('keranjang.hapus', $item->id_keranjang) }}" style="margin: 0; padding: 0;" class="flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-delete-item" title="Hapus item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">Keranjang kosong.</div>
                    @endif
                </div>
            </div>

            <!-- Diskon Global -->
            

            <!-- Total Transaksi & Pembayaran -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Total Transaksi</h6>

                    <div class="d-flex justify-content-between mb-2">
                        <div>Subtotal:</div>
                        <div id="subtotalView">Rp {{ number_format($subtotalAll ?? 0,0,',','.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <div>Total:</div>
                        <div id="totalView">Rp {{ number_format($subtotalAll ?? 0,0,',','.') }}</div>
                    </div>

                    <form method="POST" action="{{ route('penjualan.simpan') }}" id="paymentForm">
                        @csrf
                        <input type="hidden" name="total_belanja" id="totalBelanja" value="{{ intval($subtotalAll ?? 0) }}">

                        <div class="mb-2">
                            <label class="form-label fw-semibold">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-select">
                                <option value="Cash">Cash</option>
                                <option value="Non Tunai">Non Tunai</option>
                            </select>
                        </div>

                        <div id="cashFields">
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Uang Diterima</label>
                                <input type="text" name="uang_diterima" id="uangDiterima" class="form-control" placeholder="Masukkan uang diterima" oninput="formatRupiah(this)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kembalian</label>
                                <input type="text" id="kembalian" class="form-control" value="Rp 0" readonly>
                            </div>
                        </div>

                        <button type="submit" id="btnSimpan" class="btn btn-primary w-100">Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Stok Habis -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stok Tidak Cukup</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Stok produk sudah habis!
            </div>
        </div>
    </div>
</div>

<!-- STYLE -->
<style>
.btn-add {
    width: 42px;
    height: 42px;
    font-size: 18px;
    font-weight: bold;
    box-shadow: 0 6px 14px rgba(13,110,253,.35);
    transition: all .2s ease-in-out;
}

.btn-add:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 18px rgba(13,110,253,.5);
}

/* Cart List Item Styling */
.list-group-item {
    padding: 0.75rem !important;
    border-bottom: 1px solid #e9ecef !important;
    margin-bottom: 0 !important;
}

.list-group-item:last-child {
    border-bottom: none !important;
}

/* Quantity Control Group */
.qty-control {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    display: inline-flex;
    align-items: center;
}

.qty-control .btn {
    padding: 0.375rem 0.5rem;
    border: none;
    border-radius: 0;
    color: #495057;
    background: transparent;
}

.qty-control .btn:hover {
    background-color: #f0f0f0;
    color: #212529;
}

.qty-control input {
    border: none;
    border-radius: 0;
    text-align: center;
    font-weight: 600;
}

.qty-control input:focus {
    box-shadow: none;
    background-color: #f9f9f9;
}

/* Delete Button Styling */
.btn-delete-item {
    padding: 0.375rem 0.5rem !important;
    min-width: 38px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .list-group-item {
        padding: 0.75rem;
    }
    
    .qty-control input {
        width: 45px !important;
    }
}

</style>

<!-- SCRIPT -->
<script>
function checkStock(event, stok) {
    if (stok <= 0) {
        event.preventDefault();
        new bootstrap.Modal(document.getElementById('stockModal')).show();
        return false;
    }
    return true;
}
</script>

<!-- Cart Functionality Script -->
<script src="{{ asset('assets/js/cart.js') }}"></script>

@endsection
