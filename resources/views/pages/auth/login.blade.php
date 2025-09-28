@extends('pages.auth._partials.layout')

@section('content')
    <section class="auth d-flex">
        <div class="auth-left bg-main-50 flex-center p-24" style="background-image: url('assets/images/bg/bg-login.webp'); background-size: cover;">

        </div>
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <a href="index.html" class="">
                    <img src="assets/images/logo/b-learninglogo.webp" alt="">
                </a>
                {{-- <h2 class="mb-8">Welcome to Back! &#128075;</h2> --}}
                <p class="text-gray-600 text-15 mb-32" style="text-align: center">Silahkan Login Menggunakan Akun HRIS Anda</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-24">
                        <label for="email" class="form-label mb-8 h6">Email</label>
                        <div class="position-relative">
                            <input type="text" class="form-control py-11 ps-40" id="email" name="email"
                                placeholder="Type your NIK">
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                                    class="ph ph-user"></i></span>
                        </div>
                    </div>
                    <div class="mb-24">
                        <label for="current-password" class="form-label mb-8 h6">Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control py-11 ps-40" id="current-password"
                                placeholder="Enter Current Password" name="password">
                            <span
                                class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash"
                                id="#current-password"></span>
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                                    class="ph ph-lock"></i></span>
                        </div>
                    </div>
                    <div class="mb-32 flex-between flex-wrap gap-8">
                        <div class="form-check mb-0 flex-shrink-0">
                            <input class="form-check-input flex-shrink-0 rounded-4" type="checkbox" value=""
                                id="remember">
                            <label class="form-check-label text-15 flex-grow-1" for="remember">Remember Me </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-main rounded-pill w-100">Sign In</button>
                </form>
            </div>
        </div>
    </section>
@endsection
