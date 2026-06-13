<x-layouts.app title="Login SPMB">
    <div class="auth-page d-flex align-items-center py-4 py-lg-5">
        <div class="container">
            <div class="row align-items-center justify-content-center g-4 g-xl-5">
                <div class="col-lg-6 auth-copy">
                    <img src="{{ asset('images/logobintuni.jpeg') }}" alt="Logo" class="auth-logo mb-4">
                    <h1 class="fw-bold">Portal SPMB SMK Negeri 1 Bintuni</h1>
                    <p class="mt-3 mb-4">Kelola pendaftaran siswa baru secara online, mulai dari verifikasi akun, pengisian formulir, hingga cetak kartu pendaftaran.</p>
                    <div class="vstack gap-3">
                        <div class="auth-feature">
                            <span class="auth-feature-mark">1</span>
                            <span>Akun siswa diverifikasi admin sekolah sebelum dapat mengisi formulir.</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">2</span>
                            <span>Data pendaftaran dapat diperiksa ulang sebelum dikirim final.</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">3</span>
                            <span>Kartu pendaftaran dapat dicetak setelah formulir final.</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 col-lg-5 col-xl-4">
                    <div class="card auth-panel">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <div class="text-muted small text-uppercase fw-bold">Login SPMB</div>
                                <h4 class="fw-bold mb-1">Masuk ke akun</h4>
                                <div class="text-muted small">Gunakan NISN dan password yang sudah terdaftar.</div>
                            </div>
                            <form method="post" action="{{ route('login.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">NISN</label>
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-control form-control-lg" required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control form-control-lg" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Captcha</label>
                                    <div class="captcha-box mb-2">
                                        <span class="text-muted small">Hitung</span>
                                        <span class="captcha-question">{{ session('login_captcha_question') }} = ?</span>
                                    </div>
                                    <input type="number" name="captcha_answer" class="form-control form-control-lg" inputmode="numeric" placeholder="Masukkan hasil" required>
                                </div>
                                <button class="btn btn-primary btn-lg w-100">Login</button>
                            </form>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Daftar akun</a>
                                <span class="text-muted small">SPMB Online</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center text-muted small mt-4">
                        Panitia SPMB SMK Negeri 1 Bintuni
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
