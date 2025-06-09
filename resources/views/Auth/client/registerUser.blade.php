<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Sign Up | Velzon - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.ico') }}">

    <!-- CSS -->
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/admin/js/layout.js') }}"></script>
</head>

<body>
    <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center mt-sm-5 mb-4 text-white-50">
                        <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                            <img src="{{ asset('assets/admin/images/logo-light.png') }}" alt="" height="20">
                        </a>
                        <p class="mt-3 fs-15 fw-medium">Premium Admin & Dashboard Template</p>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Create New Account</h5>
                                    <p class="text-muted">Get your free velzon account now</p>
                                </div>

                                <div class="p-2 mt-4">
                                    <form class="needs-validation" novalidate action="{{ route('auth.register') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" id="useremail"
                                                name="email" placeholder="Enter email address"
                                                value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" placeholder="Enter name"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password-input">Password</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password"
                                                    class="form-control pe-5 @error('password') is-invalid @enderror password-input"
                                                    placeholder="Enter password" id="password-input" name="password"
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                <button
                                                    class="btn btn-link position-absolute end-0 top-0 text-decoration-none shadow-none text-muted password-addon"
                                                    type="button" id="password-addon"><i
                                                        class="ri-eye-fill align-middle"></i></button>
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="confirm-password-input">Confirm Password</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password"
                                                    class="form-control pe-5 @error('password_confirmation') is-invalid @enderror"
                                                    placeholder="Confirm password"
                                                    id="confirm-password-input"
                                                    name="password_confirmation" required>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @else
                                                    <div class="invalid-feedback">Please confirm your password</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <p class="mb-0 fs-12 text-muted fst-italic">By registering you agree to the
                                                Velzon <a href="#" class="text-primary text-decoration-underline fst-normal fw-medium">Terms of Use</a></p>
                                        </div>

                                        <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                            <h5 class="fs-13">Password must contain:</h5>
                                            <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                            <p id="pass-lower" class="invalid fs-12 mb-2">At <b>least one lowercase</b> letter (a-z)</p>
                                            <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>one uppercase</b> letter (A-Z)</p>
                                            <p id="pass-number" class="invalid fs-12 mb-0">At least <b>one number</b> (0-9)</p>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Sign Up</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title text-muted">Tạo tài khoản với</h5>
                                            </div>
                                            <div class="d-grid gap-3">
                                                <a href="#" class="btn btn-primary btn-lg w-100">
                                                    <i class="ri-facebook-fill fs-20 me-2"></i> Đăng ký bằng Facebook
                                                </a>
                                                <a href="#" class="btn btn-danger btn-lg w-100">
                                                    <i class="ri-google-fill fs-20 me-2"></i> Đăng ký bằng Google
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="mb-0">Already have an account? <a href="{{ route('auth.login') }}" class="fw-semibold text-primary text-decoration-underline"> Sign in </a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p class="mb-0 text-muted">&copy;
                            <script>
                                document.write(new Date().getFullYear())
                            </script> Velzon. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/admin/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('assets/admin/js/pages/particles.app.js') }}"></script>
    <script src="{{ asset('assets/admin/js/pages/passowrd-create.init.js') }}"></script>
</body>

</html>
