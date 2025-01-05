<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - StraightRay</title>
    <link rel="stylesheet" href="../assets/css/styles.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="./index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <span style="font-size: 2rem; font-weight: bold; color: #007bff; text-transform: uppercase; text-decoration: none; letter-spacing: 2px; font-family: 'Arial', sans-serif;">
                                        StraightRay.co
                                    </span>
                                </a>

                                @if(session('success'))
                                <p class="alert alert-success">{{ session('success') }}</p>
                                @endif
                                @if($errors->any())
                                @foreach($errors->all() as $err)
                                <p class="alert alert-danger">{{ $err }}</p>
                                @endforeach
                                @endif

                                <p class="text-center">Your Social Campaigns</p>
                                <form action="{{ route('login.action') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="username" value="{{ old('username') }}" id="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input class="form-control" type="password" name="password" id="password" required>
                                    </div>
                                    {{-- <div class="mb-3 form-check">
                                        <input class="form-check-input" type="checkbox" name="is_admin" value="1" id="is_admin">
                                        <label class="form-check-label" for="is_admin">
                                            Login as Admin
                                        </label>
                                    </div> --}}
                                    <div class="mb-3">
                                        <button class="btn btn-primary w-100 py-8 fs-4 mb-4">Login</button>
                                    </div>
                                </form>
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="fs-4 mb-0 fw-bold">New to StraightRay?</p>
                                    <a class="text-primary fw-bold ms-2" href="{{route('register')}}">Create an account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
