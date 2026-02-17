@extends('app')

@section('content')
<div class="container-fluid " style="background-color: #f8f9fa; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="container">
        <h1 class="text-center mb-4">Pembelian Produk</h1>
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
      

        <!-- Form Pembelian -->
        <form action="{{ route('pembelian.store') }}" method="POST" class="p-4" style="background: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            @csrf
            <!-- Pilih Produk -->
            <div class="form-group mb-3">
                <label for="produk" class="fw-bold">Pilih Produk</label>
                <div class="input-group">
                    <input type="text" id="search" class="form-control" placeholder="Cari produk..." autocomplete="off">
                    <select name="id_produk" id="produk" class="form-control" required>
                        <option value="">Pilih Produk</option>
                    </select>
                </div>
            </div>

            <!-- Tanggal Pembelian -->
            <div class="form-group mb-3">
                <label for="tanggal" class="fw-bold">Tanggal Pembelian</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <!-- Jumlah -->
            <div class="form-group mb-3">
                <label for="jumlah" class="fw-bold">Jumlah</label>
                <input type="number" name="jumlah" class="form-control" min="1" required>
            </div>

            <!-- Harga Satuan -->
            <div class="form-group mb-3">
                <label for="harga_satuan" class="fw-bold">Harga Satuan</label>
                <input type="number" name="harga_satuan" id="harga_satuan" class="form-control d-none" min="0" required>
                <input type="text" id="harga_satuan_formatted" class="form-control" disabled>
            </div>

            <!-- Supplier -->
            <div class="form-group mb-3">
                <label for="supplier" class="fw-bold">Supplier</label>
                <input type="text" name="supplier" class="form-control" required>
            </div>
            <div class="mb-3 text-center">
            <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
            </div>
            {{-- <div class="mb-3 text-center">
                <a href="{{route('pembelian.riwayat')}}" class="btn btn-secondary">Riwayat Pembelian</a>
            </div> --}}
               
        </form>
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
</div>
@endsection
