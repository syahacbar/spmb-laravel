<x-layouts.app :pengguna="$pengguna" title="Periksa Formulir">
    <div class="page-title">
        <div>
            <h3 class="fw-bold">Periksa Formulir Pendaftaran</h3>
            <div class="text-muted">Pastikan data dan berkas sudah benar sebelum dikirim final.</div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-9">
            <div class="card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                        <div>
                            <div class="fw-bold">Ringkasan Data Pendaftaran</div>
                            <div class="small text-muted">Cek ulang semua informasi sebelum final.</div>
                        </div>
                        @if($formulir->isSubmitted())
                            <span class="badge text-bg-success align-self-start">Terkirim Final</span>
                        @else
                            <span class="badge text-bg-warning align-self-start">Draft</span>
                        @endif
                    </div>

                    @include('formulir.partials.detail-table', ['formulir' => $formulir])

                    <div class="row mt-4 g-3">
                        @foreach([
                            'surat_keterangan_lulus' => 'Ijazah / SKL',
                            'kartu_keluarga' => 'Kartu Keluarga',
                            'foto_selfie' => 'Pas Foto',
                        ] as $field => $label)
                            <div class="col-md-4 mb-3">
                                <div class="small fw-bold mb-2">{{ $label }}</div>
                                <a href="{{ asset($formulir->{$field}) }}" target="_blank">
                                    <img src="{{ asset($formulir->{$field}) }}" class="img-fluid border rounded" alt="{{ $label }}">
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if($formulir->isSubmitted())
                        <div class="alert alert-success mb-0">
                            Formulir sudah dikirim final pada {{ $formulir->submitted_at?->format('d/m/Y H:i') }}.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Pastikan seluruh data dan berkas sudah benar sebelum dikirim final.
                        </div>
                        <div class="sticky-actions d-flex flex-column flex-sm-row gap-2">
                            <a href="{{ route('formulir.edit', $formulir) }}" class="btn btn-outline-secondary">Edit Data</a>
                            <form method="post" action="{{ route('formulir.kirim', $formulir) }}" class="mb-0">
                                @csrf
                                <button class="btn btn-primary w-100" data-confirm="Kirim formulir final? Data tidak dapat diedit lagi oleh siswa setelah dikirim.">Kirim Final</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
