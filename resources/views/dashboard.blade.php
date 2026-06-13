<x-layouts.app :pengguna="$pengguna" title="Dashboard">
    <div class="page-title">
        <div>
            <h3 class="fw-bold">Dashboard</h3>
            <div class="text-muted">Ringkasan aktivitas SPMB SMK Negeri 1 Bintuni</div>
        </div>
    </div>

    <div class="row g-3">
        @if($pengguna->level === 'Administrator')
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Pendaftar Final</div>
                                <div class="display-6 fw-bold">{{ $totalFormulir }}</div>
                            </div>
                            <div class="stat-icon">F</div>
                        </div>
                        <a href="{{ route('admin.pendaftar') }}" class="small fw-bold text-decoration-none">Lihat data</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Draft Formulir</div>
                                <div class="display-6 fw-bold">{{ $totalDraft }}</div>
                            </div>
                            <div class="stat-icon">D</div>
                        </div>
                        <div class="small text-muted">Belum dikirim final</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Akun Menunggu</div>
                                <div class="display-6 fw-bold">{{ $totalMenungguVerifikasi }}</div>
                            </div>
                            <div class="stat-icon">V</div>
                        </div>
                        <a href="{{ route('admin.pengguna') }}" class="small fw-bold text-decoration-none">Verifikasi akun</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Total Siswa</div>
                                <div class="display-6 fw-bold">{{ $totalPengguna }}</div>
                            </div>
                            <div class="stat-icon">S</div>
                        </div>
                        <div class="small text-muted">{{ $totalTerverifikasi }} akun terverifikasi</div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Status Formulir</h5>
                            <div class="text-muted">Pantau proses pendaftaran dan lanjutkan dari langkah terakhir.</div>
                        </div>
                        <span class="badge text-bg-success">Akun Terverifikasi</span>
                    </div>
                    @if($formulirSaya)
                        @if($formulirSaya->isSubmitted())
                            <div class="alert alert-success">Formulir Anda sudah dikirim final pada {{ $formulirSaya->submitted_at?->format('d/m/Y H:i') }}.</div>
                            <a href="{{ route('formulir.riwayat') }}" class="btn btn-success">Lihat Riwayat</a>
                            <a href="{{ route('formulir.cetak', $formulirSaya) }}" class="btn btn-outline-success" target="_blank">Cetak Kartu</a>
                        @else
                            <div class="alert alert-warning">Formulir Anda masih draft. Periksa kembali data sebelum dikirim final.</div>
                            <a href="{{ route('formulir.periksa', $formulirSaya) }}" class="btn btn-primary">Periksa dan Kirim</a>
                            <a href="{{ route('formulir.edit', $formulirSaya) }}" class="btn btn-outline-secondary">Edit Data</a>
                        @endif
                    @else
                        <div class="alert alert-info">Anda belum mengisi formulir registrasi.</div>
                        <a href="{{ route('formulir.create') }}" class="btn btn-primary">Isi Formulir</a>
                    @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
