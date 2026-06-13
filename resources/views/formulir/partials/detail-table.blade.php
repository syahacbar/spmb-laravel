<div class="table-responsive">
    <table class="table table-bordered mb-0">
        <tbody>
        @foreach([
            'nisn' => 'NISN',
            'nama' => 'Nama',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'nik' => 'NIK',
            'jenis_kelamin' => 'Jenis Kelamin',
            'agama' => 'Agama',
            'hp' => 'No HP / WA',
            'asal_sekolah' => 'Asal Sekolah',
            'alamat' => 'Alamat',
            'nama_ayah' => 'Nama Ayah',
            'pekerjaan_ayah' => 'Pekerjaan Ayah',
            'nama_ibu' => 'Nama Ibu',
            'pekerjaan_ibu' => 'Pekerjaan Ibu',
            'hp_ortu' => 'No HP / WA Ortu',
            'alamat_ortu' => 'Alamat Orang Tua',
            'program_keahlian_1' => 'Program Keahlian 1',
            'program_keahlian_2' => 'Program Keahlian 2',
        ] as $field => $label)
            <tr>
                <th style="width: 230px">{{ $label }}</th>
                <td>{{ $formulir->{$field} }}</td>
            </tr>
        @endforeach
        <tr>
            <th>Status</th>
            <td>
                @if($formulir->isSubmitted())
                    Terkirim Final
                @else
                    Draft
                @endif
            </td>
        </tr>
        <tr>
            <th>Tanggal Simpan</th>
            <td>{{ $formulir->created_at?->format('d/m/Y H:i') }}</td>
        </tr>
        @if($formulir->submitted_at)
            <tr>
                <th>Tanggal Kirim Final</th>
                <td>{{ $formulir->submitted_at->format('d/m/Y H:i') }}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
