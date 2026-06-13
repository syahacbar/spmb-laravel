<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Kartu Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f4f6;
            color: #111827;
            font-size: 14px;
        }
        .print-actions {
            max-width: 880px;
            margin: 24px auto 12px;
        }
        .card-print {
            max-width: 880px;
            margin: 0 auto 32px;
            background: #fff;
            border: 1px solid #111827;
            padding: 24px 28px;
        }
        .school-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }
        .card-title {
            border-top: 3px solid #111827;
            border-bottom: 1px solid #111827;
            padding: 10px 0;
            margin: 14px 0 18px;
            text-align: center;
        }
        .registration-number {
            border: 1px solid #111827;
            padding: 8px 12px;
            font-weight: 700;
            background: #f9fafb;
        }
        .photo-box {
            width: 132px;
            height: 172px;
            border: 1px solid #111827;
            object-fit: cover;
            background: #f9fafb;
        }
        .info-table th {
            width: 170px;
            background: #f9fafb;
            font-weight: 700;
        }
        .info-table th,
        .info-table td {
            padding: 7px 10px;
            border-color: #d1d5db;
        }
        .section-label {
            font-weight: 800;
            margin: 18px 0 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #111827;
        }
        .doc-thumb-print {
            height: 108px;
            width: 100%;
            object-fit: cover;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }
        .signature-box {
            min-height: 112px;
        }
        .signature-image {
            max-height: 54px;
            max-width: 150px;
            object-fit: contain;
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .card-print {
                max-width: none;
                margin: 0;
                border: 0;
                padding: 0;
            }
            a { color: inherit; text-decoration: none; }
        }
    </style>
</head>
<body>
@php
    $nomorPendaftaran = 'SPMB-'.str_pad((string) $formulir->id, 5, '0', STR_PAD_LEFT);
@endphp

<div class="print-actions no-print d-flex justify-content-between align-items-center">
    <a href="{{ route('formulir.riwayat') }}" class="btn btn-outline-secondary">Kembali</a>
    <button class="btn btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<div class="card-print">
    <div class="d-flex align-items-center gap-3">
        <img src="{{ asset('images/logobintuni.jpeg') }}" class="school-logo" alt="Logo sekolah">
        <div class="text-center flex-fill">
            <div class="fw-bold fs-5">PEMERINTAH KABUPATEN TELUK BINTUNI</div>
            <div class="fw-bold fs-5">SMK NEGERI 1 BINTUNI</div>
            <div class="small">SISTEM PENERIMAAN MURID BARU</div>
        </div>
        <div style="width: 72px"></div>
    </div>

    <div class="card-title">
        <div class="fw-bold fs-5">KARTU PENDAFTARAN SPMB</div>
        <div class="small">Tahun Pelajaran {{ now()->year }}/{{ now()->addYear()->year }}</div>
    </div>

    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div class="registration-number">No. Pendaftaran: {{ $nomorPendaftaran }}</div>
        <div class="text-end small">
            <div>Status: <strong>Terkirim Final</strong></div>
            <div>Tanggal Kirim: {{ $formulir->submitted_at?->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="section-label">Identitas Peserta</div>
            <table class="table table-bordered info-table mb-0">
                <tbody>
                <tr>
                    <th>NISN</th>
                    <td>{{ $formulir->nisn }}</td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $formulir->nama }}</td>
                </tr>
                <tr>
                    <th>Tempat, Tanggal Lahir</th>
                    <td>{{ $formulir->tempat_lahir }}, {{ $formulir->tanggal_lahir?->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>{{ $formulir->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <th>Asal Sekolah</th>
                    <td>{{ $formulir->asal_sekolah }}</td>
                </tr>
                <tr>
                    <th>No HP / WA</th>
                    <td>{{ $formulir->hp }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $formulir->alamat }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4 text-center">
            <div class="section-label">Foto Peserta</div>
            <img src="{{ asset($formulir->foto_selfie) }}" class="photo-box" alt="Foto peserta">
        </div>
    </div>

    <div class="section-label">Pilihan Program Keahlian</div>
    <table class="table table-bordered info-table">
        <tbody>
        <tr>
            <th>Pilihan 1</th>
            <td>{{ $formulir->program_keahlian_1 }}</td>
        </tr>
        <tr>
            <th>Pilihan 2</th>
            <td>{{ $formulir->program_keahlian_2 }}</td>
        </tr>
        </tbody>
    </table>

    <div class="section-label">Data Orang Tua / Wali</div>
    <table class="table table-bordered info-table">
        <tbody>
        <tr>
            <th>Nama Ayah</th>
            <td>{{ $formulir->nama_ayah }}</td>
            <th>Pekerjaan Ayah</th>
            <td>{{ $formulir->pekerjaan_ayah }}</td>
        </tr>
        <tr>
            <th>Nama Ibu</th>
            <td>{{ $formulir->nama_ibu }}</td>
            <th>Pekerjaan Ibu</th>
            <td>{{ $formulir->pekerjaan_ibu }}</td>
        </tr>
        <tr>
            <th>No HP Orang Tua</th>
            <td colspan="3">{{ $formulir->hp_ortu }}</td>
        </tr>
        </tbody>
    </table>

    <div class="section-label">Berkas Terunggah</div>
    <div class="row g-3">
        @foreach([
            'surat_keterangan_lulus' => 'Ijazah / SKL',
            'kartu_keluarga' => 'Kartu Keluarga',
        ] as $field => $label)
            <div class="col-6">
                <div class="small fw-bold mb-2">{{ $label }}</div>
                <img src="{{ asset($formulir->{$field}) }}" class="doc-thumb-print" alt="{{ $label }}">
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-6">
            <div class="small">
                Kartu ini dicetak dari sistem SPMB dan digunakan sebagai bukti pendaftaran.
            </div>
        </div>
        <div class="col-6 text-center signature-box">
            <div>Bintuni, {{ now()->format('d/m/Y') }}</div>
            <div>Panitia SPMB</div>
            <img src="{{ asset('images/ttdketua.png') }}" class="signature-image my-2" alt="Tanda tangan panitia">
            <div class="fw-bold text-decoration-underline">Petugas SPMB</div>
        </div>
    </div>
</div>
</body>
</html>
