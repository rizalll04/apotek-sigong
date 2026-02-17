@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body" style="font-family: 'Courier New', monospace;">

                    <!-- Header Struk -->
                    <div style="text-align: center;">
                        <h4 style="font-weight: bold;">STRUK PEMBAYARAN</h4>
                        <p style="margin: 0;">Terima kasih telah berbelanja Di Apotek Sigong</p>
                        <p style="margin: 0;">Tanggal: {{ now()->format('d M Y') }}</p>
                    </div>
                    <!-- Nama Kasir -->
                    <div style="margin-top: 10px; font-size: 14px; text-align: center;">
                        <p>Kasir: {{ Auth::user()->name }}</p> <!-- Menampilkan nama kasir sesuai yang login -->
                    </div>

                    <!-- Detail Pembayaran -->
                    <div style="margin-top: 20px;">
                        <table class="table no-datatable" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Nama Produk</th>
                                    <th style="width: 50%; text-align: right;">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualans as $penjualan)
                                    <tr>
                                        <td>{{ $penjualan->produk->nama }}</td>
                                        <td style="text-align: right;">Rp {{ number_format($penjualan->total_harga, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Pembayaran -->
                    <div style="margin-top: 20px; font-size: 16px;">
                        <p style="font-weight: bold; text-align: center;">Total Pembayaran: 
                        Rp {{ number_format($totalTransaksi, 2) }}</p>
                    </div>

                    <!-- Info Pembayaran (Uang Diterima & Kembalian) -->
                    @php
                        $firstSale = is_array($penjualans)
                            ? (count($penjualans) ? $penjualans[0] : null)
                            : ($penjualans ? $penjualans->first() : null);
                        $metode = $firstSale ? $firstSale->metode_pembayaran : null;
                        $uangDiterima = $firstSale ? $firstSale->uang_diterima : null;
                        $kembalian = $uangDiterima !== null ? max(0, $uangDiterima - $totalTransaksi) : null;
                    @endphp
                    <div style="margin-top: 10px; font-size: 14px;">
                        <p style="margin: 0;">Metode Pembayaran: <strong>{{ $metode ?? '-' }}</strong></p>
                        @if($metode === 'Cash')
                            <p style="margin: 0;">Uang Diterima: <strong>Rp {{ number_format($uangDiterima ?? 0, 2) }}</strong></p>
                            <p style="margin: 0;">Kembalian: <strong>Rp {{ number_format($kembalian ?? 0, 2) }}</strong></p>
                        @endif
                    </div>

                    <!-- Pemberitahuan Session -->
                    <div id="session-timer" style="margin-top: 20px; font-size: 16px; text-align: center;">
                        <p id="countdown-message">Session akan kedaluwarsa dalam <span id="countdown">02:00</span> menit.</p>
                    </div>

                  
                     <!-- Tombol Cetak -->
                     <div style="text-align: center; margin-top: 20px;">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak Struk</button>
                    </div>

                    <!-- Tombol Kembali -->
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="{{ route('keranjang.index') }}" class="btn btn-secondary btn-sm">
                            Kembali ke Keranjang
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let countdownTime = 120; // Waktu countdown dalam detik (2 menit)
    
    // Menampilkan countdown di halaman
    const countdownDisplay = document.getElementById('countdown');

    function updateCountdown() {
        const minutes = Math.floor(countdownTime / 60);
        const seconds = countdownTime % 60;
        countdownDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        if (countdownTime <= 0) {
            clearInterval(countdownInterval);
            window.location.href = '{{ route('keranjang.index') }}'; // Arahkan kembali ke halaman keranjang
        }

        countdownTime--;
    }

    // Update countdown setiap detik
    const countdownInterval = setInterval(updateCountdown, 1000);
</script>
<style>
    /* Hanya menampilkan struk saat pencetakan */
    @media print {
        .btn {
            display: none; /* Menyembunyikan tombol */
        }

        #session-timer {
            display: none; /* Menyembunyikan pemberitahuan session */
        }
    }
</style>

@endsection
