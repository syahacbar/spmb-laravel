<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SPMB SMKN 1 Bintuni' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --spmb-red: #b91c1c;
            --spmb-red-dark: #7f1d1d;
            --spmb-ink: #172033;
            --spmb-muted: #667085;
            --spmb-line: #e5e7eb;
            --spmb-soft: #f6f8fb;
            --spmb-sidebar: #101828;
        }
        body { background: var(--spmb-soft); color: var(--spmb-ink); }
        .navbar { background: #fff; border-bottom: 1px solid var(--spmb-line); }
        .navbar-brand { color: var(--spmb-red) !important; letter-spacing: 0; }
        .sidebar-link { color: #cbd5e1; display: flex; align-items: center; gap: .65rem; padding: .72rem .85rem; text-decoration: none; border-radius: .5rem; font-weight: 600; }
        .sidebar-link:hover, .sidebar-link.active { background: #243044; color: #fff; }
        .sidebar-link.active { box-shadow: inset 3px 0 0 #ef4444; }
        .app-shell { min-height: calc(100vh - 56px); }
        .sidebar { background: var(--spmb-sidebar); }
        .card { border: 1px solid var(--spmb-line); border-radius: .5rem; }
        .card-header { background: #fff; border-bottom-color: var(--spmb-line); }
        .table td, .table th { vertical-align: middle; }
        .table thead th { color: #475467; font-size: .82rem; text-transform: uppercase; }
        .btn { border-radius: .45rem; font-weight: 600; }
        .btn-primary { background: var(--spmb-red); border-color: var(--spmb-red); }
        .btn-primary:hover { background: var(--spmb-red-dark); border-color: var(--spmb-red-dark); }
        .btn-danger { background: var(--spmb-red); border-color: var(--spmb-red); }
        .btn-danger:hover { background: var(--spmb-red-dark); border-color: var(--spmb-red-dark); }
        .page-title { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; }
        .page-title h3 { margin: 0; }
        .stat-card { min-height: 132px; }
        .stat-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: .5rem; background: #fee2e2; color: var(--spmb-red); font-weight: 800; }
        .muted-label { color: var(--spmb-muted); font-size: .9rem; }
        .doc-thumb { width: 86px; height: 86px; object-fit: cover; border-radius: .35rem; border: 1px solid #e5e7eb; }
        .form-section {
            overflow: hidden;
        }
        .form-section .card-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1rem 1.15rem;
        }
        .section-number {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #fee2e2;
            color: var(--spmb-red);
            font-weight: 800;
        }
        .form-control,
        .form-select,
        .input-group-text {
            border-color: #d0d5dd;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 .2rem rgba(239, 68, 68, .16);
        }
        .upload-box {
            min-height: 100%;
            border: 1px solid var(--spmb-line);
            border-radius: .5rem;
            padding: 1rem;
            background: #fff;
        }
        .sticky-actions {
            position: sticky;
            bottom: 0;
            z-index: 5;
            border: 1px solid var(--spmb-line);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(8px);
            padding: .85rem;
            box-shadow: 0 -8px 24px rgba(16, 24, 40, .08);
        }
        .auth-page {
            min-height: calc(100vh - 56px);
            position: relative;
            overflow: hidden;
        }
        .auth-page::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(185, 28, 28, .94), rgba(127, 29, 29, .84)),
                url("{{ asset('images/kop.jpg') }}") center/cover;
            z-index: -2;
        }
        .auth-page::after {
            content: "";
            position: absolute;
            inset: auto -10% -30% -10%;
            height: 52%;
            background: #f6f8fb;
            transform: skewY(-4deg);
            transform-origin: left top;
            z-index: -1;
        }
        .auth-panel {
            border: 0;
            box-shadow: 0 24px 70px rgba(16, 24, 40, .22);
        }
        .auth-logo {
            width: 86px;
            height: 86px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            padding: .5rem;
            box-shadow: 0 12px 28px rgba(16, 24, 40, .18);
        }
        .auth-copy {
            color: rgba(255, 255, 255, .88);
        }
        .auth-copy h1 {
            color: #fff;
            font-size: clamp(1.75rem, 3vw, 2.55rem);
            line-height: 1.12;
            margin: 0;
        }
        .auth-feature {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            color: rgba(255, 255, 255, .9);
            font-size: .95rem;
        }
        .auth-feature-mark {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            font-weight: 800;
        }
        .captcha-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            border: 1px solid var(--spmb-line);
            border-radius: .5rem;
            background: #f9fafb;
            padding: .75rem .9rem;
        }
        .captcha-question {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--spmb-red);
            letter-spacing: 0;
        }
        @media (max-width: 767.98px) {
            .sidebar { min-height: auto !important; }
            .page-title { align-items: flex-start; flex-direction: column; }
            main { padding: 1rem !important; }
            .sticky-actions { margin-left: -1rem; margin-right: -1rem; border-radius: 0; border-left: 0; border-right: 0; }
            .auth-page { min-height: calc(100vh - 56px); }
            .auth-copy h1 { font-size: 1.6rem; }
            .auth-page::after { height: 64%; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">SPMB SMK N 1 BINTUNI</a>
        @isset($pengguna)
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <div class="small fw-bold text-dark">{{ $pengguna->nama_pengguna ?: $pengguna->id_pengguna }}</div>
                    <div class="small text-muted">{{ $pengguna->level }}</div>
                </div>
                <form action="{{ route('logout') }}" method="post" class="mb-0">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm" data-confirm="Apakah anda yakin akan keluar?">Logout</button>
                </form>
            </div>
        @endisset
    </div>
</nav>

@isset($pengguna)
    <div class="container-fluid app-shell">
        <div class="row min-vh-100">
            <aside class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-white mb-3">
                    <div class="fw-bold">{{ $pengguna->nama_pengguna ?: $pengguna->id_pengguna }}</div>
                    <span class="badge text-bg-light text-dark">{{ $pengguna->level }}</span>
                </div>
                <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Beranda</a>
                @if($pengguna->level === 'Administrator')
                    <a class="sidebar-link {{ request()->routeIs('admin.pendaftar') ? 'active' : '' }}" href="{{ route('admin.pendaftar') }}">Data Registrasi</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}" href="{{ route('admin.pengguna') }}">Data User</a>
                @else
                    <a class="sidebar-link {{ request()->routeIs('formulir.create', 'formulir.edit', 'formulir.periksa') ? 'active' : '' }}" href="{{ route('formulir.create') }}">Formulir Registrasi</a>
                    <a class="sidebar-link {{ request()->routeIs('formulir.riwayat') ? 'active' : '' }}" href="{{ route('formulir.riwayat') }}">Riwayat Registrasi</a>
                @endif
            </aside>
            <main class="col-md-9 col-lg-10 p-4">
                @include('partials.flash')
                {{ $slot }}
            </main>
        </div>
    </div>
@else
    <main class="{{ request()->routeIs('login', 'register') ? '' : 'container py-5' }}">
        @include('partials.flash')
        {{ $slot }}
    </main>
@endisset

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="confirmModalMessage">
                Apakah anda yakin?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmModalButton">Ya, lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('confirmModal');
        const modalMessage = document.getElementById('confirmModalMessage');
        const modalButton = document.getElementById('confirmModalButton');

        if (! modalElement || ! modalMessage || ! modalButton) {
            return;
        }

        const confirmModal = new bootstrap.Modal(modalElement);
        let confirmedTarget = null;

        document.querySelectorAll('[data-confirm]').forEach(function (element) {
            element.addEventListener('click', function (event) {
                if (element.dataset.confirmed === 'true') {
                    element.dataset.confirmed = 'false';
                    return;
                }

                event.preventDefault();
                confirmedTarget = element;
                modalMessage.textContent = element.dataset.confirm || 'Apakah anda yakin?';
                confirmModal.show();
            });
        });

        modalButton.addEventListener('click', function () {
            if (! confirmedTarget) {
                return;
            }

            confirmedTarget.dataset.confirmed = 'true';
            confirmModal.hide();
            confirmedTarget.click();
        });
    });
</script>
</body>
</html>
