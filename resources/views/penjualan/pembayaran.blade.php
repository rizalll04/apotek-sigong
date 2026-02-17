@extends('app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow rounded">
                <div class="card-body" style="font-family: 'Courier New', monospace;">

                    <!-- Header -->
                    <div class="text-center mb-3">
                        <h4 class="fw-bold">PEMBAYARAN NON TUNAI</h4>
                        <p class="mb-0">Silakan lakukan pembayaran melalui QRIS, Transfer Bank, atau metode lainnya.</p>
                        <small class="text-muted">Tanggal: {{ now()->format('d M Y') }}</small>
                    </div>

                    <!-- Nama Kasir -->
                    <div class="text-center mb-3" style="font-size: 14px;">
                        <p>Kasir: <strong>{{ Auth::user()->name }}</strong></p>
                    </div>

                    <!-- Detail Produk -->
                    <div class="mb-3">
                        <table class="table table-sm table-striped no-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualans as $penjualan)
                                    @if ($penjualan->payment_status === 'pending')
                                        <tr>
                                            <td>{{ $penjualan->produk->nama }}</td>
                                            <td class="text-end">Rp {{ number_format($penjualan->total_harga, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Total -->
                    <div class="text-center mb-3">
                        <h5 class="fw-bold">Total Transaksi</h5>
                        <p class="fs-5 text-success fw-bold">Rp {{ number_format($totalTransaksi, 2) }}</p>
                    </div>

                    <!-- Tombol Bayar Sekarang -->
                    <!-- Tombol Bayar Sekarang -->
                    <div class="text-center mb-3">
                        <form action="{{ route('bayar.midtrans') }}" method="POST">
                            @csrf
                        
                            <input type="hidden" name="amount" value="{{ $totalTransaksi }}">
                            
                            @php
                                $ids = $penjualans->where('payment_status', 'pending')->pluck('id_penjualan')->implode(',');
                            @endphp
                            <input type="hidden" name="penjualan_ids" value="{{ $ids }}">
                        
                            @if($ids)
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                                    Bayar Sekarang
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" disabled>
                                    Tidak Ada Pembayaran
                                </button>
                            @endif
                        </form>
                        
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn {
            display: none;
        }
    }
</style>
@endsection
