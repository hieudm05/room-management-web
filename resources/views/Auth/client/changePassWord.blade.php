<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/admin/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/admin/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/admin/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .auth-pass-inputgroup {
            position: relative;
        }
        .password-addon {
            z-index: 5;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center mt-sm-5 mb-4 text-white-50">
                        <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                            <img src="{{ asset('assets/admin/images/logo-light.png') }}" alt="Logo" height="20">
                        </a>
                        <p class="mt-3 fs-15 fw-medium">Change your account password</p>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Change Password</h5>
                                    <p class="text-muted">Enter your current password and new password below.</p>
                                </div>

                                <div class="p-2">
                                    <form action="{{ route('password.change.update') }}" method="POST">
                                        @csrf

                                        <!-- Current password -->
                                        <div class="mb-3">
                                            <label class="form-label" for="current-password-input">Current Password</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" name="current_password"
                                                    class="form-control password-input"
                                                    placeholder="Enter current password" id="current-password-input" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon"
                                                    type="button">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- New password -->
                                        <div class="mb-3">
                                            <label class="form-label" for="new-password-input">New Password</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" name="new_password"
                                                    class="form-control password-input"
                                                    placeholder="Enter new password" id="new-password-input" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon"
                                                    type="button">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                            <div class="form-text">
                                                Must be at least 8 characters, include uppercase, lowercase, and a number.
                                            </div>
                                        </div>

                                        <!-- Confirm new password -->
                                        <div class="mb-3">
                                            <label class="form-label" for="new-password-confirm-input">Confirm New Password</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" name="new_password_confirmation"
                                                    class="form-control password-input"
                                                    placeholder="Confirm new password" id="new-password-confirm-input" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon"
                                                    type="button">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Change Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="mb-0">
                                <a href="{{ route('tenant.profile.edit') }}" class="fw-semibold text-primary text-decoration-underline">
                                    Back to profile
                                </a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <footer class="footer">
            <div class="container text-center">
                <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script> Your Company</p>
            </div>
        </footer>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/admin/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('assets/admin/js/pages/particles.app.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle password visibility
            document.querySelectorAll('.password-addon').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const group = btn.closest('.auth-pass-inputgroup');
                    const input = group.querySelector('.password-input');
                    const icon = btn.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('ri-eye-fill', 'ri-eye-off-fill');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('ri-eye-off-fill', 'ri-eye-fill');
                    }
                });
            });

            // SweetAlert messages
            @if(session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('status') }}',
                    confirmButtonText: 'OK'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>

</body>
</html>
