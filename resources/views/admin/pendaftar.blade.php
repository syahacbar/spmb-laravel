<x-layouts.app :pengguna="$pengguna" title="Data Registrasi">
    <style>
        .data-registrasi-table thead .filter-row th {
            background: #f8fafc;
            padding-top: .55rem;
            padding-bottom: .55rem;
        }
        .data-registrasi-table thead .filter-row th::after,
        .data-registrasi-table thead .filter-row th::before {
            display: none !important;
        }
        .data-registrasi-table .dt-column-title {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }
        .dt-search label,
        .dt-length label {
            color: #667085;
            font-weight: 700;
        }
        .dt-search input,
        .dt-length select,
        .column-filter {
            border-color: #d0d5dd;
            border-radius: .45rem;
        }
        .registration-photo {
            width: 54px;
            height: 68px;
            object-fit: cover;
            border: 2px solid #fff;
            border-radius: .75rem;
            box-shadow: 0 0 0 1px #d0d5dd, 0 4px 10px rgba(16, 24, 40, .12);
        }
        .registration-document-actions,
        .registration-row-actions {
            display: inline-flex;
            flex-direction: column;
            align-items: stretch;
            gap: .4rem;
        }
        .registration-document-actions .btn {
            min-width: 82px;
        }
        .registration-icon-button {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .registration-icon-button svg {
            width: 17px;
            height: 17px;
        }
    </style>

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Data Registrasi</h3>
            <div class="text-muted">Formulir yang sudah dikirim final oleh siswa.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="dataRegistrasiTable" class="table table-hover align-middle mb-0 data-registrasi-table w-100">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Asal Sekolah</th>
                        <th>Minat A</th>
                        <th>Minat B</th>
                        <th>Tanggal Kirim</th>
                        <th>Berkas</th>
                        <th>Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <select class="form-select form-select-sm column-filter" data-column="4" aria-label="Filter asal sekolah">
                                <option value="">Semua sekolah</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm column-filter" data-column="5" aria-label="Filter minat A">
                                <option value="">Semua minat A</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm column-filter" data-column="6" aria-label="Filter minat B">
                                <option value="">Semua minat B</option>
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($formulirs as $formulir)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ $formulir->berkasUrl('foto_selfie') }}"
                                   class="d-inline-block"
                                   data-document-preview
                                   data-document-title="Foto - {{ $formulir->nama }}"
                                   data-document-type="image"
                                   data-document-download="{{ $formulir->berkasDownloadUrl('foto_selfie') }}">
                                    <img src="{{ $formulir->berkasUrl('foto_selfie') }}"
                                         class="registration-photo"
                                         alt="Foto {{ $formulir->nama }}">
                                </a>
                            </td>
                            <td>{{ $formulir->nisn }}</td>
                            <td>{{ $formulir->nama }}</td>
                            <td>{{ $formulir->asal_sekolah }}</td>
                            <td>{{ $formulir->program_keahlian_1 }}</td>
                            <td>{{ $formulir->program_keahlian_2 }}</td>
                            @php($tanggalKirim = $formulir->submitted_at ?: $formulir->created_at)
                            <td data-order="{{ $tanggalKirim?->timestamp ?? 0 }}">{{ $tanggalKirim?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="registration-document-actions">
                                    @foreach([
                                        'surat_keterangan_lulus' => 'Ijazah',
                                        'kartu_keluarga' => 'KK',
                                        'foto_selfie' => 'Foto',
                                    ] as $field => $label)
                                        @if($formulir->berkasTersedia($field))
                                            <a href="{{ $formulir->berkasUrl($field) }}"
                                               class="btn btn-outline-primary btn-sm"
                                               data-document-preview
                                               data-document-title="{{ $label }} - {{ $formulir->nama }}"
                                               data-document-type="{{ $formulir->berkasIsImage($field) ? 'image' : 'pdf' }}"
                                               data-document-download="{{ $formulir->berkasDownloadUrl($field) }}">
                                                {{ $label }}
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                                {{ $label }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="registration-row-actions">
                                    <a href="{{ route('formulir.edit', $formulir) }}"
                                       class="btn btn-success btn-sm registration-icon-button"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="left"
                                       title="Edit"
                                       aria-label="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('formulir.cetak', $formulir) }}"
                                       class="btn btn-outline-success btn-sm registration-icon-button"
                                       target="_blank"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="left"
                                       title="Cetak"
                                       aria-label="Cetak">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M6 9V2h12v7"></path>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                            <rect width="12" height="8" x="6" y="14"></rect>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableElement = document.getElementById('dataRegistrasiTable');

            if (! tableElement || ! window.DataTable) {
                return;
            }

            const table = new DataTable(tableElement, {
                orderCellsTop: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[7, 'desc']],
                columnDefs: [
                    { orderable: false, searchable: false, targets: [0, 1, 8, 9] },
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ registrasi',
                    infoEmpty: 'Tidak ada registrasi yang ditampilkan',
                    infoFiltered: '(difilter dari _MAX_ total registrasi)',
                    zeroRecords: 'Data registrasi tidak ditemukan',
                    emptyTable: 'Belum ada data registrasi.',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya',
                    },
                },
            });

            [4, 5, 6].forEach(function (columnIndex) {
                const filter = tableElement.querySelector('.column-filter[data-column="' + columnIndex + '"]');

                if (! filter) {
                    return;
                }

                table
                    .column(columnIndex)
                    .data()
                    .unique()
                    .sort()
                    .each(function (value) {
                        const label = String(value).trim();

                        if (! label || label === '-') {
                            return;
                        }

                        const option = document.createElement('option');
                        option.value = label;
                        option.textContent = label;
                        filter.appendChild(option);
                    });
            });

            tableElement.querySelectorAll('.column-filter').forEach(function (select) {
                select.addEventListener('change', function () {
                    const columnIndex = Number(select.dataset.column);
                    const value = select.value;
                    const escapedValue = value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    table.column(columnIndex).search(value ? '^' + escapedValue + '$' : '', true, false).draw();
                });

                select.addEventListener('click', function (event) {
                    event.stopPropagation();
                });
            });

            table.on('order.dt search.dt draw.dt', function () {
                let index = 1;

                table
                    .cells(null, 0, { search: 'applied', order: 'applied' })
                    .every(function () {
                        this.data(index++);
                    });

                tableElement.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
                    bootstrap.Tooltip.getOrCreateInstance(element);
                });
            }).draw();
        });
    </script>
</x-layouts.app>
