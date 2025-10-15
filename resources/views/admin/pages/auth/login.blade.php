@extends('admin.layout.base')

@php
    $bodyClass = 'authentication-background';
@endphp

@section('body')
    <div class="authentication-basic-background">
        <img src="assets/images/media/backgrounds/9.png" alt="">
    </div>

    <div class="container">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-6 col-sm-8 col-12">
                <div class="card custom-card border-0 my-4">
                    <form class="card-body p-5" id="login-form" action="login">
                        <div class="mb-4">
                            <a href="index">
                                <img src="assets/images/brand-logos/toggle-logo.png" alt="logo" class="desktop-dark">
                            </a>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-semibold">Xin chào, Chào mừng trở lại!</h4>
                            <p class="mb-4 text-muted fw-normal">Vui lòng nhập thông tin xác thực của bạn</p>
                        </div>
                        <div class="row gy-3">
                            <div class="col-xl-12">
                                <label for="signin-email" class="form-label text-default">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter Email"
                                    required>
                            </div>
                            <div class="col-xl-12 mb-2">
                                <label for="signin-password" class="form-label text-default d-block">Password</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter Password" required>
                                    <a href="javascript:void(0);" class="show-password-button text-muted"
                                        onclick="createpassword('password',this)">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </a>
                                </div>
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Ghi nhớ
                                        </label>
                                        <a href="javascript::void(0);" class="float-end link-danger fw-medium fs-12">
                                            Quên mật khẩu?
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <x-button-submit text="Đăng nhập" icon="ti ti-login-2" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="assets/js/auth/show-password.js"></script>
    <script src="assets/js/auth/login.js"></script>
@endsection
