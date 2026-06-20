<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Pendaftaran {{ $nomorPendaftaran }}</title>
    <style>
        body { margin: 0; background: #eef2f6; color: #172033; font: 14px Arial, sans-serif; }
        .toolbar { padding: 16px; text-align: center; }
        .toolbar button { border: 0; border-radius: 6px; background: #b91c1c; color: #fff; padding: 10px 18px; font-weight: 700; cursor: pointer; }
        .card { width: 190mm; min-height: 260mm; margin: 0 auto 20px; box-sizing: border-box; background: #fff; border: 1px solid #aaa; padding: 12mm; }
        .header { border-bottom: 3px solid #111; padding-bottom: 10px; text-align: center; }
        .header h1, .header h2 { margin: 3px 0; }
        .title { margin: 18px 0; text-align: center; font-size: 18px; font-weight: 800; }
        .number { margin-bottom: 14px; font-size: 16px; font-weight: 800; }
        .identity { display: grid; grid-template-columns: 1fr 34mm; gap: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #777; padding: 7px; text-align: left; vertical-align: top; }
        th { width: 34%; background: #f4f4f4; }
        .photo { width: 34mm; height: 44mm; border: 1px solid #777; object-fit: cover; }
        .section { margin: 16px 0 6px; font-weight: 800; }
        .note { margin-top: 22px; border-top: 1px solid #aaa; padding-top: 10px; font-size: 12px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .card { width: auto; min-height: auto; margin: 0; border: 0; }
        }
    </style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">Cetak / Simpan PDF</button></div>
<main class="card">
    <div class="header">
        <h2>PEMERINTAH KABUPATEN TELUK BINTUNI</h2>
        <h1>SMK NEGERI 1 BINTUNI</h1>
        <div>Jl. Manimeri - Bintuni | smkn1bintuni.sch.id</div>
    </div>
    <div class="title">KARTU TANDA PENDAFTARAN SPMB<br>TAHUN PELAJARAN {{ $settings['tahun_pelajaran'] ?? '-' }}</div>
    <div class="number">No. Pendaftaran: {{ $nomorPendaftaran }}</div>
    <div class="identity">
        <table>
            <tr><th>NISN</th><td>{{ $formulir->nisn }}</td></tr>
            <tr><th>Nama Lengkap</th><td>{{ $formulir->nama }}</td></tr>
            <tr><th>Tempat, Tanggal Lahir</th><td>{{ $formulir->tempat_lahir }}, {{ $formulir->tanggal_lahir?->format('d/m/Y') }}</td></tr>
            <tr><th>Jenis Kelamin</th><td>{{ $formulir->jenis_kelamin }}</td></tr>
            <tr><th>Asal Sekolah</th><td>{{ $formulir->asal_sekolah }}</td></tr>
            <tr><th>No. HP/WA</th><td>{{ $formulir->hp }}</td></tr>
        </table>
        <div>
            @if($fotoDataUri)
                <img src="{{ $fotoDataUri }}" class="photo" alt="Pas foto">
            @else
                <div class="photo"></div>
            @endif
        </div>
    </div>
    <div class="section">Pilihan Program Keahlian</div>
    <table>
        <tr><th>Minat A</th><td>{{ $formulir->program_keahlian_1 }}</td></tr>
        <tr><th>Minat B</th><td>{{ $formulir->program_keahlian_2 }}</td></tr>
    </table>
    <div class="section">Jadwal Wawancara dan Pemetaan Jurusan</div>
    <table>
        <tr><th>Tanggal</th><td>{{ $settings['tanggal_tes'] ?? '-' }}</td></tr>
        <tr><th>Waktu</th><td>{{ $settings['waktu_tes'] ?? '-' }}</td></tr>
        <tr><th>Tempat</th><td>{{ $settings['tempat_tes'] ?? '-' }}</td></tr>
        <tr><th>Ruang</th><td>Ruang-{{ max(1, (int) ceil($formulir->id / 25)) }}</td></tr>
    </table>
    <div class="note">
        <strong>Perhatian:</strong>
        {!! nl2br(e($settings['catatan_kartu'] ?? 'Kartu ini digunakan sebagai bukti pendaftaran SPMB.')) !!}
    </div>
</main>
</body>
</html>
