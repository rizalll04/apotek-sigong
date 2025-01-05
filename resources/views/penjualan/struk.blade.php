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
                        <p style="margin: 0;">Terima kasih telah berbelanja</p>
                        <p style="margin: 0;">Tanggal: {{ now()->format('d M Y') }}</p>
                    </div>

                    <!-- Detail Pembayaran -->
                    <div style="margin-top: 20px;">
                        <table class="table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Nama Produk</th>
                                    <th style="width: 50%; text-align: right;">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualans as $penjualan)
                                    <tr>
                                        <td>{{ $penjualan->produk->nama_produk }}</td>
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

<!-- Script untuk Hitung Mundur Session -->
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

@endsection
