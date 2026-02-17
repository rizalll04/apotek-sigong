@extends('app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">

    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 page-title" style="font-weight: 700; ">
                    <i class="bi bi-cart3"></i> Penjualan
                </h2>
                <p class="text-muted mb-0">Pilih produk untuk ditambahkan ke transaksi</p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- ===================== DAFTAR PRODUK (LEFT SIDE) ===================== -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0" style="font-weight: 600;">
                        <i class="bi bi-box2"></i> Daftar Produk
                    </h6>
                </div>
                <div class="card-body">

                    <!-- Search -->
                    <form method="GET" action="{{ route('keranjang.index') }}" class="mb-4">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text"
                                   name="q"
                                   class="form-control border-start-0"
                                   placeholder="Cari produk atau kategori..."
                                   value="{{ $query ?? '' }}">
                        </div>
                    </form>

                    <!-- Grid Produk -->
                    <div class="row g-3">
                        @forelse ($produk as $item)
                            <div class="col-sm-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm transition-hover" style="cursor: pointer;">
                                    <div class="position-relative">
                                        <!-- Stock Badge -->
                                        <div class="position-absolute top-0 end-0 m-2">
                                            @if($item->stok > 0)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Stok {{ $item->stok }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-circle"></i> Habis
                                                </span>
                                            @endif
                                        </div>
                                        <!-- Spacer to prevent badge overlapping content -->
                                        <div style="height: 32px;"></div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <!-- Product Name -->
                                        <h6 class="card-title fw-bold text-uppercase mb-2" style="min-height: 2.5rem;">
                                            {{ $item->nama }}
                                        </h6>
                                        
                                        <!-- Category -->
                                        <small class="badge bg-light text-dark mb-2 align-self-start">
                                            {{ $item->kategori }}
                                        </small>

                                        <!-- Price -->
                                        <div class="mb-3 mt-auto">
                                            <div class="text-primary fw-bold fs-5">
                                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                            </div>
                                        </div>

                                        <!-- Add Button -->
                                        <form method="POST"
                                              action="{{ route('keranjang.tambah') }}"
                                              onsubmit="return checkStock(event, {{ $item->stok }})">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                            <input type="hidden" name="produk_id" value="{{ $item->id }}">
                                            <input type="hidden" name="jumlah" value="1">

                                            <button type="submit"
                                                class="btn btn-primary w-100 btn-add-product"
                                                {{ $item->stok <= 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-plus-circle"></i> Tambah ke Keranjang
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning text-center" role="alert">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">Produk tidak ditemukan</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

        <!-- ===================== KERANJANG & PEMBAYARAN (RIGHT SIDE) ===================== -->
        <div class="col-lg-4">
            <!-- Cart Summary -->
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white border-0">
                    <h6 class="mb-0" style="font-weight: 600;">
                        <i class="bi bi-bag-check"></i> Keranjang Belanja
                    </h6>
                </div>

                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @php $subtotalAll = 0; @endphp
                    @if($keranjang && $keranjang->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($keranjang as $item)
                                @php $line = $item->jumlah * $item->harga_satuan; $subtotalAll += $line; @endphp
                                <div class="p-2 bg-light rounded mb-2" data-cart-id="{{ $item->id_keranjang }}">
                                    <!-- Item Header -->
                                    <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fw-bold small" style="word-break: break-word; line-height: 1.2;">{{ $item->produk->nama }}</div>
                                            <small class="text-muted d-block">
                                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                            </small>
                                        </div>
                                        <form method="POST" action="{{ route('keranjang.hapus', $item->id_keranjang) }}" style="margin: 0;" class="ms-auto flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Quantity Control -->
                                    <div class="qty-control-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="updateQty({{ $item->id_keranjang }}, -1)"
                                                title="Kurangi">
                                            <i class="bi bi-dash-lg"></i>
                                        </button>
                                        <input type="number"
                                               id="qty{{ $item->id_keranjang }}"
                                               class="form-control form-control-sm text-center fw-bold"
                                               value="{{ $item->jumlah }}"
                                               min="1"
                                               max="{{ $item->produk->stok }}"
                                               data-max-stock="{{ $item->produk->stok }}"
                                               data-price="{{ $item->harga_satuan }}"
                                               readonly
                                               style="flex: 0 0 70px; font-size: 1rem;">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="updateQty({{ $item->id_keranjang }}, 1)"
                                                title="Tambah">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="text-end mt-2">
                                        <small class="text-muted d-block">Subtotal</small>
                                        <span id="subtotal{{ $item->id_keranjang }}" class="fw-bold text-primary">
                                            Rp {{ number_format($line, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-bag-x" style="font-size: 2.5rem; color: #ccc;"></i>
                            <p class="text-muted mt-2 mb-0">Keranjang kosong</p>
                        </div>
                    @endif
                </div>

                <!-- Total Section -->
                <div class="card-footer bg-light border-top">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotalView" class="fw-bold">Rp {{ number_format($subtotalAll ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3" style="font-size: 1.2rem;">
                        <span class="fw-bold">Total:</span>
                        <span id="totalView" class="fw-bold text-primary">Rp {{ number_format($subtotalAll ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <!-- Payment Method -->
                    <form method="POST" action="{{ route('penjualan.simpan') }}" id="paymentForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="total_belanja" id="totalBelanja" value="{{ intval($subtotalAll ?? 0) }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-select form-select-sm">
                                <option value="Cash">
                                    <i class="bi bi-cash-coin"></i> Cash
                                </option>
                                <option value="Non Tunai">
                                    <i class="bi bi-credit-card"></i> Non Tunai
                                </option>
                            </select>
                        </div>

                        <!-- Cash Payment Fields -->
                        <div id="cashFields">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Uang Diterima</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="uang_diterima" id="uangDiterima" class="form-control" placeholder="0" min="0" oninput="hitungKembalian()">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Kembalian</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="kembalian" class="form-control bg-light fw-bold" value="0" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Non-Tunai Payment Fields -->
                        <div id="nonTunaiFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Bukti Pembayaran (Foto) <span class="text-danger">*</span></label>
                                <input type="file" name="bukti_transfer_file" id="buktiTransferFile" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted d-block mt-1">Unggah foto bukti pembayaran (JPEG/PNG).</small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btnSimpan" class="btn btn-primary w-100 btn-lg" {{ $subtotalAll <= 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle"></i> Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Stok Habis -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-warning border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle"></i> Stok Tidak Cukup
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Stok produk sudah habis atau tidak mencukupi!</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- STYLES -->
<style>
/* Product Grid Hover Effect */
.transition-hover {
    transition: all 0.3s ease;
}

.transition-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(13, 110, 253, 0.15) !important;
}

/* Add Product Button */
.btn-add-product {
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-add-product:hover:not(:disabled) {
    transform: scale(1.02);
}

.btn-add-product:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Quantity Control Group */
.qty-control-group {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.qty-control-group input {
    border: 1px solid #dee2e6;
    text-align: center;
    font-weight: 600;
}

.qty-control-group .btn {
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
}

/* Cart Items Container */
.space-y-2 > div {
    animation: slideIn 0.2s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Button Icon */
.btn-icon {
    padding: 0.375rem 0.5rem;
    min-width: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Sticky Cart */
.sticky-top {
    z-index: 99;
}

/* Cart Body Scrollbar */
.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.card-body::-webkit-scrollbar-thumb {
    background: #0d6efd;
    border-radius: 10px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background: #0b5ed7;
}

/* Responsive */
@media (max-width: 991px) {
    .sticky-top {
        position: static !important;
    }
}

@media (max-width: 576px) {
    .col-lg-8, .col-lg-4 {
        margin-bottom: 1rem;
    }
}
</style>

<!-- SCRIPTS -->
<script>
function checkStock(event, stok) {
    if (stok <= 0) {
        event.preventDefault();
        new bootstrap.Modal(document.getElementById('stockModal')).show();
        return false;
    }
    return true;
}

/**
 * Hitung kembalian otomatis
 */
function hitungKembalian() {
    const totalBelanja = parseInt(document.getElementById('totalBelanja').value) || 0;
    const uangDiterima = parseInt(document.getElementById('uangDiterima').value) || 0;
    const kembalian = uangDiterima - totalBelanja;
    
    // Update displayed kembalian
    document.getElementById('kembalian').value = kembalian >= 0 ? formatRupiahNoPrefix(kembalian) : '0';
}

/**
 * Format Rupiah tanpa prefix (untuk input field)
 */
function formatRupiahNoPrefix(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return '0';
    }
    const intAmount = Math.round(Number(amount));
    return intAmount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Toggle payment method fields (Cash vs Non-Tunai)
 */
function togglePaymentFields() {
    const metodeSelect = document.getElementById('metode_pembayaran');
    const cashFields = document.getElementById('cashFields');
    const nonTunaiFields = document.getElementById('nonTunaiFields');
    const buktiTransferInput = document.getElementById('buktiTransferFile');
    const uangDiterimaInput = document.getElementById('uangDiterima');
    
    if (metodeSelect.value === 'Cash') {
        cashFields.style.display = 'block';
        nonTunaiFields.style.display = 'none';
        uangDiterimaInput.required = true;
        if (buktiTransferInput) buktiTransferInput.required = false;
    } else {
        cashFields.style.display = 'none';
        nonTunaiFields.style.display = 'block';
        uangDiterimaInput.required = false;
        if (buktiTransferInput) buktiTransferInput.required = true;
    }
}

// Initialize payment fields toggle on page load
document.addEventListener('DOMContentLoaded', function() {
    const metodeSelect = document.getElementById('metode_pembayaran');
    if (metodeSelect) {
        metodeSelect.addEventListener('change', togglePaymentFields);
        // Set initial state
        togglePaymentFields();
    }
});
</script>

<!-- Cart Functionality Script -->
<script src="{{ asset('assets/js/cart.js') }}"></script>

@endsection
