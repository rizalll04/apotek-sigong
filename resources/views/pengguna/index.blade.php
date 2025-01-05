@extends('app')

@section('content')
    <div style="padding: 20px;">
        <!-- Menampilkan nama pengguna yang sedang login -->
        <p style="font-size: 18px; font-weight: bold;">Welcome <b>{{ Auth::user()->name }}</b></p>

        <!-- Tombol untuk mengubah password -->
        <a class="btn btn-primary" href="{{ route('password') }}" style="margin-right: 10px;">Change Password</a>

        <!-- Tombol untuk logout -->
        <a class="btn btn-danger" href="{{ route('logout') }}" style="margin-right: 10px;">Logout</a>

        <!-- Tombol untuk login jika belum login -->
        @if(Auth::guest())
            <a class="btn btn-primary" href="{{ route('login') }}" style="margin-right: 10px;">Login</a>
        @endif

 
            <a class="btn btn-info" href="{{ route('register') }}" style="margin-right: 10px;">Register</a>
   

            <a class="btn btn-success" href="{{ route('profil.create') }}" style="margin-top: 20px;">Create Profile</a>


       
    </div>
@endsection
