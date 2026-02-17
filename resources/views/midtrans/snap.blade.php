@extends('app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Sedang memproses pembayaran...</h4>
    <button id="pay-button" class="btn btn-success">Bayar Sekarang</button>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        window.snap.pay("{{ $snapToken }}", {
            onSuccess: function(result){
                alert("Pembayaran berhasil!");
                window.location.href = "/keranjang"; // redirect ke halaman setelah bayar
            },
            onPending: function(result){
                alert("Menunggu pembayaran...");
                window.location.href = "/keranjang"; 
            },
            onError: function(result){
                alert("Pembayaran gagal!");
                console.log(result);
            }
        });
    });
</script>
@endsection
