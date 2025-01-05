@extends('app')

@section('content')
<div class="row">
    <div class="col-md-6">
        @if(session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
        @endif
        <form action="{{ route('profil.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Alamat</label>
                <input class="form-control" type="text" name="alamat" required />
            </div>
            <div class="mb-3">
                <label>Tanggal Lahir</label>
                <input class="form-control" type="date" name="tanggal_lahir" required />
            </div>
            <div class="mb-3">
                <label>Foto</label>
                <input class="form-control" type="file" name="foto" />
            </div>
            <div class="mb-3">
                <button class="btn btn-primary">Simpan Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection
