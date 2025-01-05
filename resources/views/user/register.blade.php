<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - StraightRay</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/seodashlogo.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
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
                                <p class="text-center">Create Your Account</p>

                                @if($errors->any())
                                @foreach($errors->all() as $err)
                                <p class="alert alert-danger">{{ $err }}</p>
                                @endforeach
                                @endif

                                <form action="{{ route('register.action') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="name" value="{{ old('name') }}" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="username" value="{{ old('username') }}" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input class="form-control" type="password" name="password" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password Confirmation<span class="text-danger">*</span></label>
                                        <input class="form-control" type="password" name="password_confirm" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role <span class="text-danger">*</span></label>
                                        <select class="form-control" name="role" required>
                                            <option value="manajer" {{ old('role') == 'manajer' ? 'selected' : '' }}>Manajer</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary w-100 py-8 fs-4 mb-4">Register</button>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-bold">Already have an Account?</p>
                                        <a class="text-primary fw-bold ms-2" href="{{ route('login') }}">Sign In</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>
