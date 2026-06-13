<x-layouts.app :pengguna="$pengguna" title="Formulir Registrasi">
    @php
        $isEdit = $formulir->exists;
        $programs = [
            'Teknik Kendaraan Ringan',
            'Desain Komunikasi Visual',
            'Teknik Jaringan dan Telekomunikasi',
            'Akuntansi dan Keuangan Lembaga',
        ];
        $pekerjaanAyah = ['PNS', 'TNI/Polri', 'Pedagang', 'Petani', 'Nelayan', 'Buruh Bangunan', 'Kontraktor', 'Pegawai Swasta', 'Wiraswasta', 'Tidak Ada'];
        $pekerjaanIbu = ['PNS', 'Ibu Rumah Tangga', 'Pedagang', 'Petani', 'Pegawai Swasta', 'Wiraswasta', 'Tidak Ada'];
    @endphp

    <div class="page-title">
        <div>
            <h3 class="fw-bold">{{ $isEdit ? 'Edit Formulir Registrasi' : 'Formulir Registrasi' }}</h3>
            <div class="text-muted">Lengkapi data diri, orang tua, pilihan jurusan, dan berkas pendaftaran.</div>
        </div>
    </div>

    @if(! $formulir->isSubmitted())
        <div class="alert alert-info">
            Data yang disimpan akan masuk ke halaman pemeriksaan sebelum dikirim final.
        </div>
    @endif

    <form method="post" action="{{ $isEdit ? route('formulir.update', $formulir) : route('formulir.store') }}" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('put')
        @endif

        <div class="card shadow-sm mb-3 form-section">
            <div class="card-header">
                <span class="section-number">1</span>
                <div>
                    <div class="fw-bold">Data Diri Peserta</div>
                    <div class="small text-muted">Identitas utama calon siswa.</div>
                </div>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">NISN</label>
                    <input class="form-control" value="{{ $formulir->nisn ?: $pengguna->id_pengguna }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input name="nama" value="{{ old('nama', $formulir->nama) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tempat Lahir</label>
                    <input name="tempat_lahir" value="{{ old('tempat_lahir', $formulir->tempat_lahir) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($formulir->tanggal_lahir)->format('Y-m-d') ?: $formulir->tanggal_lahir) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK</label>
                    <input name="nik" value="{{ old('nik', $formulir->nik) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value=""></option>
                        @foreach(['Laki-laki', 'Perempuan'] as $option)
                            <option value="{{ $option }}" @selected(old('jenis_kelamin', $formulir->jenis_kelamin) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Agama</label>
                    <select name="agama" class="form-select" required>
                        <option value=""></option>
                        @foreach(['Islam', 'Kristen Protestan', 'Kristen Katholik', 'Hindu', 'Budha'] as $option)
                            <option value="{{ $option }}" @selected(old('agama', $formulir->agama) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No HP / WA</label>
                    <input name="hp" value="{{ old('hp', $formulir->hp) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Asal Sekolah</label>
                    <input name="asal_sekolah" value="{{ old('asal_sekolah', $formulir->asal_sekolah) }}" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $formulir->alamat) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3 form-section">
            <div class="card-header">
                <span class="section-number">2</span>
                <div>
                    <div class="fw-bold">Data Orang Tua / Wali</div>
                    <div class="small text-muted">Data kontak dan alamat keluarga.</div>
                </div>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Ayah</label>
                    <input name="nama_ayah" value="{{ old('nama_ayah', $formulir->nama_ayah) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan Ayah</label>
                    <select name="pekerjaan_ayah" class="form-select" required>
                        <option value=""></option>
                        @foreach($pekerjaanAyah as $option)
                            <option value="{{ $option }}" @selected(old('pekerjaan_ayah', $formulir->pekerjaan_ayah) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Ibu</label>
                    <input name="nama_ibu" value="{{ old('nama_ibu', $formulir->nama_ibu) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan Ibu</label>
                    <select name="pekerjaan_ibu" class="form-select" required>
                        <option value=""></option>
                        @foreach($pekerjaanIbu as $option)
                            <option value="{{ $option }}" @selected(old('pekerjaan_ibu', $formulir->pekerjaan_ibu) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No HP / WA Ortu</label>
                    <input name="hp_ortu" value="{{ old('hp_ortu', $formulir->hp_ortu) }}" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Alamat Orang Tua</label>
                    <textarea name="alamat_ortu" class="form-control" rows="2" required>{{ old('alamat_ortu', $formulir->alamat_ortu) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3 form-section">
            <div class="card-header">
                <span class="section-number">3</span>
                <div>
                    <div class="fw-bold">Pilihan Program Keahlian</div>
                    <div class="small text-muted">Pilih dua program keahlian yang berbeda.</div>
                </div>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Program Keahlian Pilihan 1</label>
                    <select name="program_keahlian_1" class="form-select" required>
                        <option value=""></option>
                        @foreach($programs as $option)
                            <option value="{{ $option }}" @selected(old('program_keahlian_1', $formulir->program_keahlian_1) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program Keahlian Pilihan 2</label>
                    <select name="program_keahlian_2" class="form-select" required>
                        <option value=""></option>
                        @foreach($programs as $option)
                            <option value="{{ $option }}" @selected(old('program_keahlian_2', $formulir->program_keahlian_2) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3 form-section">
            <div class="card-header">
                <span class="section-number">4</span>
                <div>
                    <div class="fw-bold">Upload Dokumen</div>
                    <div class="small text-muted">Unggah gambar dokumen dengan ukuran maksimal 4 MB.</div>
                </div>
            </div>
            <div class="card-body row g-3">
                @foreach([
                    'surat_keterangan_lulus' => 'Ijazah / SKL',
                    'kartu_keluarga' => 'Kartu Keluarga',
                    'foto_selfie' => 'Pas Foto',
                ] as $field => $label)
                    <div class="col-md-4">
                        <div class="upload-box">
                            <label class="form-label fw-bold">{{ $label }}</label>
                            @if($isEdit && $formulir->{$field})
                                <div class="mb-2">
                                    <a href="{{ asset($formulir->{$field}) }}" target="_blank">
                                        <img src="{{ asset($formulir->{$field}) }}" class="doc-thumb" alt="{{ $label }}">
                                    </a>
                                </div>
                                <div class="small text-muted mb-2">Kosongkan jika tidak ingin mengganti berkas.</div>
                            @else
                                <div class="small text-muted mb-2">Format gambar, maksimal 4 MB.</div>
                            @endif
                            <input type="file" name="{{ $field }}" class="form-control" accept="image/*" @required(! $isEdit)>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="sticky-actions d-flex flex-column flex-sm-row gap-2 mb-4">
            <button class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan dan Periksa' : 'Simpan dan Periksa' }}</button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </form>
</x-layouts.app>
