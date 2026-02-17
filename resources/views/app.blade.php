<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Apotek Sigong</title>
  <link rel="stylesheet" href="{{asset('assets/css/styles.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <link rel="shortcut icon" type="image/png" href="{{ asset('public/assets/images/apotek.png') }}" />

  <!-- Tambahkan di bagian <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.iconify.design/2/2.0.3/iconify.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


<!-- Tambahkan di bagian sebelum penutupan </body> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
    <div>
        <!-- LOGO -->
        <div class="brand-logo d-flex align-items-center justify-content-between px-3 py-3">
    <a href="{{ route('admin.index') }}"
       class="text-nowrap logo-img fw-bold fs-4 text-dark text-uppercase d-flex align-items-center gap-2">
        
        <img src="{{ asset('images/apotek.png') }}"
             alt="Logo Apotek"
             style="height: 32px; width: auto;">

        <span>Apotek Sigong</span>
    </a>

    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-6"></i>
    </div>
</div>


        <!-- SIDEBAR MENU -->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">

                {{-- ================= DASHBOARD ================= --}}
                @if(Auth::check() && in_array(Auth::user()->role, ['admin','owner']))
                <li class="nav-small-cap">
                    <span class="hide-menu">MAIN</span>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.index') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.index') }}">
                        <iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                @endif

                {{-- ================= TRANSAKSI ================= --}}
                @if(Auth::check() && in_array(Auth::user()->role, ['admin','kasir']))
                <li class="nav-small-cap">
                    <span class="hide-menu">TRANSAKSI</span>
                </li>

                <li class="sidebar-item {{ request()->routeIs('keranjang.*') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('keranjang.index') }}">
                        <iconify-icon icon="mdi:cash-register" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Penjualan</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('penjualan.*') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('penjualan.index') }}">
                        <iconify-icon icon="mdi:history" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Riwayat Penjualan</span>
                    </a>
                </li>
                @endif

                {{-- ================= MASTER DATA ================= --}}
                @if(Auth::check() && in_array(Auth::user()->role, ['admin','kasir']))
                <li class="nav-small-cap">
                    <span class="hide-menu">MASTER DATA</span>
                </li>

                <li class="sidebar-item {{ request()->routeIs('produk.*') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('produk.index') }}">
                        <iconify-icon icon="mdi:package" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Data Obat</span>
                    </a>
                </li>
                @endif

                {{-- ================= PEMBELIAN & PREDIKSI ================= --}}
                @if(Auth::check() && in_array(Auth::user()->role, ['admin','apoteker']))
                <li class="nav-small-cap">
                    <span class="hide-menu">STOK & ANALISIS</span>
                </li>

                <li class="sidebar-item {{ request()->routeIs('pembelian.*') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('pembelian.index') }}">
                        <iconify-icon icon="solar:cart-bold-duotone" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Pembelian</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('pembelian.riwayat') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('pembelian.riwayat') }}">
                        <iconify-icon icon="mdi:history" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Riwayat Pembelian</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('manajemen.*') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('manajemen.index') }}">
                        <iconify-icon icon="mdi:chart-line" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Peramalan TES</span>
                    </a>
                </li>
                @endif

                {{-- ================= ADMIN ================= --}}
                @if(Auth::check() && Auth::user()->role == 'admin')
                <li class="nav-small-cap">
                    <span class="hide-menu">ADMIN</span>
                </li>

                <li class="sidebar-item {{ request()->routeIs('user.*') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('user.index') }}">
                        <iconify-icon icon="mdi:account-group" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Data Pegawai</span>
                    </a>
                </li>
                @endif

                {{-- ================= LAPORAN ================= --}}
                @if(Auth::check() && in_array(Auth::user()->role, ['admin','owner']))
                <li class="sidebar-item {{ request()->routeIs('penjualan.laporan') ? 'selected' : '' }}">
                    <a class="sidebar-link" href="{{ route('penjualan.laporan') }}">
                        <iconify-icon icon="mdi:file-chart" class="fs-6"></iconify-icon>
                        <span class="hide-menu">Laporan</span>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>

           

            
         
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          
          
          
          
            
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              {{-- <a href="#" target="_blank"
                class="btn btn-primary me-2"><span class="d-none d-md-block">Check Pro Version</span> <span class="d-block d-md-none">Pro</span></a>
              <a href="#" target="_blank"
                class="btn btn-success"><span class="d-none d-md-block">Download Free </span> <span class="d-block d-md-none">Free</span></a> --}}
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="../assets/images/profile/user-3.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="{{route('profile.show')}}" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Account</p>
                    </a>
              
                  
                    <a href="{{ route('logout') }}" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
        
        @yield('content')

        <br>
        <br><br><br>
        <div class="py-6 px-6 text-center">
            <p class="mb-0 fs-4">Design and Developed by <a href="https://adminmart.com/" target="_blank"
                class="pe-1 text-primary text-decoration-underline"> Apotek Sigong </a>Distributed by <a href="" target="_blank"
                class="pe-1 text-primary text-decoration-underline"> Apotek Sigong </a></p>
          </div>
        </div>
      </div>
    </div>
    <script src="{{asset('assets/libs/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/apexcharts/dist/apexcharts.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/dist/simplebar.js')}}"></script>
    <script src="{{asset('assets/js/sidebarmenu.js')}}"></script>
    <script src="{{asset('assets/js/app.min.js')}}"></script>
    <script src="{{asset('assets/js/dashboard.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  </body>
  
  </html>
  