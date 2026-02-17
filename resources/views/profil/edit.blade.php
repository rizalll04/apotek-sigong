@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h4 class="mb-4 text-center">Edit Profil Pengguna</h4>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Form untuk mengedit profil -->
                    <form action="{{ route('profil.update', Auth::id()) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT') <!-- Untuk mengindikasikan update -->

                        <div class="mb-4">
                            <label for="alamat" class="form-label text-muted">Alamat</label>
                            <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ old('alamat', $profil->alamat) }}" placeholder="Masukkan alamat lengkap" required>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tanggal_lahir" class="form-label text-muted">Tanggal Lahir</label>
                            <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $profil->tanggal_lahir) }}" required>
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label text-muted">Foto Profil</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto">
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tampilkan foto lama jika ada -->
                        @if($profil->foto)
                        <div class="mb-4 text-center">
                            <strong class="d-block text-muted mb-2">Foto Profil Lama:</strong>
                            <img src="{{ asset('storage/' . $profil->foto) }}" alt="Foto Profil" class="rounded-circle img-fluid" style="max-width: 150px;">
                        </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Perbarui Profil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Validasi -->
<script>
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
@endsection
