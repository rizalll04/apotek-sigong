
@extends('app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <!-- Total User -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total User</h5>
                    <p class="card-text">{{ $totalUser }}</p>
                </div>
            </div>
        </div>

        <!-- Total Produk -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Produk</h5>
                    <p class="card-text">{{ $totalProduk }}</p>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Pendapatan</h5>
                    <p class="card-text">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>


    </div>


    @endsection