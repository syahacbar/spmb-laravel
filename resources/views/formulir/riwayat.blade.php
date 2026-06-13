<x-layouts.app :pengguna="$pengguna" title="Riwayat Registrasi">
    <div class="page-title">
        <div>
            <h3 class="fw-bold">Riwayat Registrasi</h3>
            <div class="text-muted">Lihat status formulir dan cetak kartu pendaftaran setelah final.</div>
        </div>
    </div>
    <div class="row g-3">
        @forelse($formulirs as $formulir)
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            @if($formulir->isSubmitted())
                                <span class="badge text-bg-success">Terkirim Final</span>
                                @if($formulir->submitted_at)
                                    <span class="text-muted small">pada {{ $formulir->submitted_at->format('d/m/Y H:i') }}</span>
                                @endif
                            @else
                                <span class="badge text-bg-warning">Draft</span>
                            @endif
                        </div>
                        @include('formulir.partials.detail-table', ['formulir' => $formulir])
                        <div class="mt-3 d-flex gap-2">
                            @if($formulir->isSubmitted())
                                <a href="{{ route('formulir.cetak', $formulir) }}" class="btn btn-outline-success btn-sm" target="_blank">Cetak Kartu</a>
                            @else
                                <a href="{{ route('formulir.edit', $formulir) }}" class="btn btn-success btn-sm">Edit Data</a>
                                <a href="{{ route('formulir.periksa', $formulir) }}" class="btn btn-primary btn-sm">Periksa dan Kirim</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-lg-8">
                <div class="alert alert-info">Belum ada formulir registrasi.</div>
            </div>
        @endforelse
    </div>
</x-layouts.app>
