<x-layouts.app title="Daftar Akun">
    <div class="auth-page d-flex align-items-center py-4 py-lg-5">
        <div class="container">
            <div class="row align-items-center justify-content-center g-4 g-xl-5">
                <div class="col-lg-5 auth-copy">
                    <img src="{{ asset('images/logobintuni.jpeg') }}" alt="Logo" class="auth-logo mb-4">
                    <h1 class="fw-bold">Buat akun pendaftaran siswa baru</h1>
                    <p class="mt-3 mb-4">Gunakan NISN, email aktif, dan nomor WhatsApp yang dapat dihubungi panitia sekolah.</p>
                    <div class="auth-feature">
                        <span class="auth-feature-mark">!</span>
                        <span>Akun baru akan masuk daftar verifikasi admin sebelum dapat login ke dashboard siswa.</span>
                    </div>
                </div>

                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card auth-panel">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <div class="text-muted small text-uppercase fw-bold">Registrasi Akun</div>
                                <h4 class="fw-bold mb-1">Daftar Akun SPMB</h4>
                                <div class="text-muted small">Isi data akun awal untuk mengakses formulir pendaftaran.</div>
                            </div>
                            <form method="post" action="{{ route('register.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">NISN</label>
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-control form-control-lg" maxlength="10" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No WA</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+62</span>
                                        <input type="text" name="no_wa" value="{{ old('no_wa') }}" class="form-control form-control-lg" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control form-control-lg" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg" required>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-lg flex-fill">Daftar</button>
                                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg flex-fill">Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="text-center text-muted small mt-4">
                        Gunakan email aktif untuk menerima informasi verifikasi.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
