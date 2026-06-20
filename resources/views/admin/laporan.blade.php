<x-layouts.app :pengguna="$pengguna" title="Laporan SPMB">
    <style>
        .report-section {
            margin-bottom: 1.5rem;
        }
        .report-section-title {
            margin-bottom: .25rem;
            font-weight: 800;
        }
        .report-table th,
        .report-table td {
            white-space: nowrap;
        }
        .report-chart-wrap {
            position: relative;
            min-height: 340px;
        }
        .report-date-picker {
            position: relative;
        }
        .report-date-picker .form-control {
            padding-right: 3rem;
            cursor: pointer;
        }
        .report-date-picker-button {
            position: absolute;
            top: 50%;
            right: .35rem;
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transform: translateY(-50%);
            border: 0;
            border-radius: .4rem;
            background: transparent;
            color: #667085;
        }
        .report-date-picker-button:hover {
            background: #f2f4f7;
            color: var(--spmb-red);
        }
        .report-date-picker-button svg {
            width: 18px;
            height: 18px;
        }
        .report-native-date {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
        .report-print-only {
            display: none;
        }
        .report-filter-summary {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .report-filter-summary span {
            border-radius: 999px;
            background: #eef2f6;
            padding: .35rem .7rem;
            color: #475467;
            font-size: .82rem;
            font-weight: 700;
        }
        @media print {
            @page {
                size: landscape;
                margin: 8mm;
            }
            body {
                background: #fff;
                font-size: 8px;
            }
            .navbar,
            .sidebar,
            .report-screen-content,
            .dt-container {
                display: none !important;
            }
            .app-shell,
            main {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .report-print-only {
                display: block !important;
            }
            .report-print-table {
                width: 100%;
                border-collapse: collapse;
            }
            .report-print-table th,
            .report-print-table td {
                border: 1px solid #777;
                padding: 3px;
                vertical-align: top;
            }
            .report-print-table th {
                background: #eee !important;
                font-size: 7px;
            }
        }
    </style>

    <div class="report-print-only">
        <div class="text-center mb-3">
            <h4 class="fw-bold mb-1">Laporan Lengkap SPMB SMK Negeri 1 Bintuni</h4>
            <div>Dicetak {{ now()->translatedFormat('d F Y H:i') }}</div>
        </div>
        <table class="report-print-table">
            <thead>
            <tr>
                @foreach($reportHeadings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($reportRows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="report-screen-content">
        <div class="page-title">
            <div>
                <h3 class="fw-bold">Laporan SPMB</h3>
                <div class="text-muted">Rekap, statistik, dan arsip formulir pendaftaran.</div>
            </div>
        </div>

        <section class="card shadow-sm report-section">
            <div class="card-header">
                <h5 class="report-section-title">Filter dan Rincian Formulir</h5>
                <div class="text-muted small">Hasil filter digunakan untuk tabel, cetak, CSV, grafik, dan statistik asal sekolah.</div>
            </div>
            <div class="card-body border-bottom">
                <form method="get" action="{{ route('admin.laporan') }}" class="row g-3 align-items-end">
                    <div class="col-md-6 col-xl">
                        <label for="tanggal_pendaftaran" class="form-label">Tanggal Pendaftaran</label>
                        <div class="report-date-picker">
                            <input
                                type="text"
                                id="tanggal_pendaftaran"
                                name="tanggal_pendaftaran"
                                value="{{ $filters['tanggal_pendaftaran'] ?? '' }}"
                                class="form-control"
                                placeholder="dd/mm/yyyy"
                                inputmode="numeric"
                                maxlength="10"
                                pattern="\d{2}/\d{2}/\d{4}"
                                autocomplete="off"
                            >
                            <input
                                type="date"
                                id="tanggal_pendaftaran_picker"
                                value="{{ isset($filters['tanggal_pendaftaran_query']) ? $filters['tanggal_pendaftaran_query'] : '' }}"
                                class="report-native-date"
                                tabindex="-1"
                                aria-hidden="true"
                            >
                            <button type="button" class="report-date-picker-button" id="tanggalPendaftaranButton" aria-label="Pilih tanggal pendaftaran" title="Pilih tanggal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M16 3v4M8 3v4M3 11h18"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl">
                        <label for="status" class="form-label">Status Formulir</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua status</option>
                            <option value="submitted" @selected(($filters['status'] ?? '') === 'submitted')>Final</option>
                            <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draf</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-xl">
                        <label for="minat_a" class="form-label">Minat A</label>
                        <select id="minat_a" name="minat_a" class="form-select">
                            <option value="">Semua Minat A</option>
                            @foreach($minatAOptions as $program)
                                <option value="{{ $program }}" @selected(($filters['minat_a'] ?? '') === $program)>{{ $program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-xl">
                        <label for="minat_b" class="form-label">Minat B</label>
                        <select id="minat_b" name="minat_b" class="form-select">
                            <option value="">Semua Minat B</option>
                            @foreach($minatBOptions as $program)
                                <option value="{{ $program }}" @selected(($filters['minat_b'] ?? '') === $program)>{{ $program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-xl">
                        <label for="asal_sekolah" class="form-label">Asal Sekolah</label>
                        <select id="asal_sekolah" name="asal_sekolah" class="form-select">
                            <option value="">Semua sekolah</option>
                            @foreach($schoolOptions as $school)
                                <option value="{{ $school }}" @selected(($filters['asal_sekolah'] ?? '') === $school)>{{ $school }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <button class="btn btn-primary" type="submit">Terapkan Filter</button>
                        <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary">Reset</a>
                        <button type="button" class="btn btn-outline-secondary ms-xl-auto" id="printReportButton">Cetak Hasil Filter</button>
                        <a href="{{ route('admin.laporan.export', request()->query()) }}" class="btn btn-success">Ekspor CSV</a>
                    </div>
                </form>
            </div>
            <div class="card-body pb-0">
                <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                    <div class="report-filter-summary">
                        <span>Total {{ number_format($summary['total']) }} formulir</span>
                        <span>{{ number_format($summary['submitted']) }} final</span>
                        <span>{{ number_format($summary['draft']) }} draf</span>
                    </div>
                    <div class="text-muted small">Cetakan dan CSV memuat biodata siswa, data orang tua, dan pilihan jurusan.</div>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table id="laporanTable" class="table table-hover align-middle mb-0 report-table w-100">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Asal Sekolah</th>
                        <th>Minat A</th>
                        <th>Minat B</th>
                        <th>Status</th>
                        <th>Tanggal Pendaftaran</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($formulirs as $formulir)
                        @php($tanggal = $formulir->submitted_at ?: $formulir->created_at)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $formulir->nisn }}</td>
                            <td>{{ $formulir->nama }}</td>
                            <td>{{ $formulir->jenis_kelamin }}</td>
                            <td>{{ $formulir->asal_sekolah }}</td>
                            <td>{{ $formulir->program_keahlian_1 }}</td>
                            <td>{{ $formulir->program_keahlian_2 }}</td>
                            <td><span class="badge {{ $formulir->status === 'submitted' ? 'text-bg-success' : 'text-bg-warning' }}">{{ $formulir->status === 'submitted' ? 'Final' : 'Draf' }}</span></td>
                            <td data-order="{{ $tanggal?->timestamp ?? 0 }}">{{ $tanggal?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card shadow-sm report-section">
            <div class="card-header">
                <h5 class="report-section-title">Download Berkas Pendaftar</h5>
                <div class="text-muted small">Unduh paket ZIP berisi surat kelulusan, kartu keluarga, dan foto pendaftar final.</div>
            </div>
            <div class="card-body">
                <form method="get" action="{{ route('admin.laporan.berkas') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="berkas_minat_a" class="form-label">Minat A</label>
                        <select id="berkas_minat_a" name="minat_a" class="form-select">
                            <option value="">Semua Minat A</option>
                            @foreach($minatAOptions as $program)
                                <option value="{{ $program }}">{{ $program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="berkas_minat_b" class="form-label">Minat B</label>
                        <select id="berkas_minat_b" name="minat_b" class="form-select">
                            <option value="">Semua Minat B</option>
                            @foreach($minatBOptions as $program)
                                <option value="{{ $program }}">{{ $program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">Download ZIP</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="report-section">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <h5 class="report-section-title">Grafik Komposisi Pendaftar</h5>
                            <div class="text-muted small">Berdasarkan jenis kelamin.</div>
                        </div>
                        <div class="card-body report-chart-wrap">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <h5 class="report-section-title">Grafik Rekap Minat Program Keahlian</h5>
                            <div class="text-muted small">Rincian Minat A, Minat B, dan gabungan seluruh peminatan.</div>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-xl-4">
                                    <h6 class="text-center fw-bold mb-2">Minat A</h6>
                                    <div class="report-chart-wrap"><canvas id="programChartA"></canvas></div>
                                </div>
                                <div class="col-xl-4">
                                    <h6 class="text-center fw-bold mb-2">Minat B</h6>
                                    <div class="report-chart-wrap"><canvas id="programChartB"></canvas></div>
                                </div>
                                <div class="col-xl-4">
                                    <h6 class="text-center fw-bold mb-2">Gabungan</h6>
                                    <div class="report-chart-wrap"><canvas id="programChartCombined"></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card shadow-sm report-section">
            <div class="card-header">
                <h5 class="report-section-title">Rekap Statistik Asal Sekolah Pendaftar</h5>
                <div class="text-muted small">Jumlah pendaftar berdasarkan asal sekolah sesuai hasil filter.</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th style="width: 80px">No</th>
                        <th>Asal Sekolah</th>
                        <th class="text-center" style="width: 180px">Jumlah Pendaftar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($schoolSummary as $school)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $school['asal_sekolah'] }}</td>
                            <td class="text-center fw-bold">{{ number_format($school['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada data asal sekolah.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableElement = document.getElementById('laporanTable');

            if (tableElement && window.DataTable) {
                new DataTable(tableElement, {
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[8, 'desc']],
                    columnDefs: [{ orderable: false, searchable: false, targets: [0] }],
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ formulir',
                        infoEmpty: 'Tidak ada formulir yang ditampilkan',
                        infoFiltered: '(difilter dari _MAX_ total formulir)',
                        zeroRecords: 'Data formulir tidak ditemukan',
                        emptyTable: 'Belum ada data formulir.',
                        paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' },
                    },
                });
            }

            document.getElementById('printReportButton')?.addEventListener('click', function () {
                window.print();
            });

            const registrationDate = document.getElementById('tanggal_pendaftaran');
            const registrationDatePicker = document.getElementById('tanggal_pendaftaran_picker');
            const registrationDateButton = document.getElementById('tanggalPendaftaranButton');

            function openRegistrationDatePicker() {
                if (! registrationDatePicker) {
                    return;
                }

                if (typeof registrationDatePicker.showPicker === 'function') {
                    registrationDatePicker.showPicker();
                } else {
                    registrationDatePicker.click();
                }
            }

            registrationDate?.addEventListener('input', function () {
                const digits = registrationDate.value.replace(/\D/g, '').slice(0, 8);
                const parts = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 8)].filter(Boolean);
                registrationDate.value = parts.join('/');
            });

            registrationDate?.addEventListener('click', openRegistrationDatePicker);
            registrationDateButton?.addEventListener('click', openRegistrationDatePicker);
            registrationDatePicker?.addEventListener('change', function () {
                if (! registrationDatePicker.value || ! registrationDate) {
                    return;
                }

                const [year, month, day] = registrationDatePicker.value.split('-');
                registrationDate.value = [day, month, year].join('/');
            });

            if (! window.Chart) {
                return;
            }

            new Chart(document.getElementById('genderChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: @json([$summary['laki_laki'], $summary['perempuan']]),
                        backgroundColor: ['#2563eb', '#ec4899'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                },
            });

            const programNames = @json($programSummary->pluck('nama'));
            const programAbbreviations = @json($programAbbreviations);
            const programLabels = programNames.map(function (program) {
                return programAbbreviations[program] || program;
            });
            const minatAValues = @json($programSummary->pluck('minat_a'));
            const minatBValues = @json($programSummary->pluck('minat_b'));
            const combinedValues = minatAValues.map(function (value, index) {
                return Number(value) + Number(minatBValues[index] || 0);
            });

            function programColor(program) {
                const name = String(program).toUpperCase();

                if (name.includes('AKUNTANSI') || name.includes('AKL')) return '#fde68a';
                if (name.includes('KOMPUTER') || name.includes('JARINGAN') || name.includes('TKJ')) return '#991b1b';
                if (name.includes('KENDARAAN RINGAN') || name.includes('TKR')) return '#6b7280';
                if (name.includes('SEPEDA MOTOR') || name.includes('TSM')) return '#1e3a8a';
                if (name.includes('DESAIN KOMUNIKASI VISUAL') || name.includes('DKV')) return '#7dd3fc';

                return '#94a3b8';
            }

            const programColors = programLabels.map(programColor);
            const programChartOptions = {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { ticks: { maxRotation: 45, minRotation: 20 } },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return programNames[context.dataIndex] + ': ' + context.parsed.y;
                            },
                        },
                    },
                },
            };

            [
                ['programChartA', minatAValues],
                ['programChartB', minatBValues],
                ['programChartCombined', combinedValues],
            ].forEach(function ([elementId, values]) {
                new Chart(document.getElementById(elementId), {
                    type: 'bar',
                    data: {
                        labels: programLabels,
                        datasets: [{
                            data: values,
                            backgroundColor: programColors,
                            borderRadius: 5,
                        }],
                    },
                    options: programChartOptions,
                });
            });
        });
    </script>
</x-layouts.app>
