@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1" style="font-weight: 700; color: #0d6efd;">
                    <i class="bi bi-cart3"></i> Penjualan
                </h2>
                <p class="text-muted mb-0">Pilih produk untuk ditambahkan ke transaksi</p>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Container -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-plus"></i> Form Pembelian</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pembelian.store') }}" method="POST">
                        @csrf

                        <!-- Pilih Produk -->
                        <div class="mb-4">
                            <label for="search" class="form-label fw-600">
                                <i class="bi bi-search me-1"></i>Pilih Produk
                            </label>
                            <div class="mb-3">
                                <input type="text" id="search" class="form-control" placeholder="Cari nama produk...">
                            </div>
                            <select name="id_produk" id="produk" class="form-select" required>
                                <option value="">Pilih Produk</option>
                            </select>
                            @error('id_produk')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Tanggal Pembelian -->
                        <div class="mb-4">
                            <label for="tanggal" class="form-label fw-600">
                                <i class="bi bi-calendar me-1"></i>Tanggal Pembelian
                            </label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                            @error('tanggal')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Jumlah -->
                        <div class="mb-4">
                            <label for="jumlah" class="form-label fw-600">
                                <i class="bi bi-boxes me-1"></i>Jumlah
                            </label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required placeholder="00">
                            @error('jumlah')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Harga Satuan -->
                        <div class="mb-4">
                            <label for="harga_satuan_formatted" class="form-label fw-600">
                                <i class="bi bi-tag me-1"></i>Harga Satuan
                            </label>
                            <input type="hidden" name="harga_satuan" id="harga_satuan">
                            <input type="text" id="harga_satuan_formatted" class="form-control" disabled placeholder="Rp 0">
                        </div>

                        <!-- Supplier -->
                        <div class="mb-4">
                            <label for="supplier" class="form-label fw-600">
                                <i class="bi bi-shop me-1"></i>Supplier
                            </label>
                            <input type="text" name="supplier" id="supplier" class="form-control" required placeholder="Nama supplier...">
                            @error('supplier')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Simpan Pembelian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ajax untuk mencari produk & populate awal -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function formatRupiah(angka) {
        return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Initial product list passed from the controller
    var initialProducts = @json($produk ?? []);

    function populateProducts(list){
        $('#produk').empty();
        $('#produk').append('<option value="">Pilih Produk</option>');
        list.forEach(function(produk){
            $('#produk').append('<option value="' + produk.id + '" data-harga="' + produk.harga_beli + '">' + produk.nama + '</option>');
        });
    }

    // Populate on page load with initial products (show some entries without requiring search)
    $(document).ready(function(){
        if(initialProducts.length){
            populateProducts(initialProducts);
        }
    });

    $('#search').on('keyup', function() {
        var query = $(this).val().trim();
        if (query.length === 0) {
            // if search is empty, restore the initial list
            populateProducts(initialProducts);
            return;
        }

        // perform search on any input length (responsibly)
        $.ajax({
            url: "{{ route('produk.search') }}",
            method: 'GET',
            data: { query: query },
            success: function(data) {
                if(data && data.length){
                    populateProducts(data);
                } else {
                    $('#produk').empty();
                    $('#produk').append('<option value="">Tidak ada produk</option>');
                }
            },
            error: function() {
                alert('Gagal mengambil data produk.');
            }
        });
    });

    $('#produk').on('change', function() {
        var hargaSatuan = $('#produk option:selected').data('harga');
        $('#harga_satuan').val(hargaSatuan);
        $('#harga_satuan_formatted').val(formatRupiah(hargaSatuan));
    });

    $('#harga_satuan').on('input', function() {
        var angka = $(this).val().replace(/\D/g, '');
        $(this).val(angka);
        $('#harga_satuan_formatted').val(formatRupiah(angka));
    });
</script>
@endsection
