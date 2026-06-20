<?php

namespace Tests\Feature;

use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Ekstensi pdo_sqlite diperlukan untuk test database laporan.');
        }

        parent::setUp();
    }

    public function test_administrator_dapat_membuka_dan_memfilter_laporan(): void
    {
        $admin = $this->buatPengguna('admin000001', 'Administrator');
        $this->buatFormulir([
            'nisn' => '1234567890',
            'nama' => 'Siswa Final',
            'status' => 'submitted',
            'submitted_at' => '2026-06-18 10:00:00',
        ]);
        $this->buatFormulir([
            'nisn' => '1234567891',
            'nama' => 'Siswa Draf',
            'status' => 'draft',
        ]);

        $this->withSession(['pengguna_id' => $admin->id_pengguna])
            ->get(route('admin.laporan', [
                'status' => 'submitted',
                'minat_a' => 'Teknik Komputer Jaringan',
                'tanggal_pendaftaran' => '18/06/2026',
            ]))
            ->assertOk()
            ->assertSee('Laporan SPMB')
            ->assertSee('Siswa Final')
            ->assertDontSee('Siswa Draf');
    }

    public function test_administrator_dapat_mengekspor_laporan_csv(): void
    {
        $admin = $this->buatPengguna('admin000001', 'Administrator');
        $this->buatFormulir([
            'nisn' => '1234567890',
            'nama' => 'Siswa Ekspor',
            'status' => 'submitted',
            'submitted_at' => '2026-06-18 10:00:00',
            'alamat' => 'Jalan Pendidikan',
            'alamat_kelurahan' => 'Bintuni Timur',
            'alamat_kecamatan' => 'Bintuni',
            'alamat_kabupaten' => 'Teluk Bintuni',
            'alamat_ortu' => 'Jalan Keluarga',
            'alamat_ortu_kelurahan' => 'Bintuni Barat',
            'alamat_ortu_kecamatan' => 'Bintuni',
            'alamat_ortu_kabupaten' => 'Teluk Bintuni',
        ]);

        $response = $this->withSession(['pengguna_id' => $admin->id_pengguna])
            ->get(route('admin.laporan.export', ['status' => 'submitted']));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Siswa Ekspor', $response->streamedContent());
        $this->assertStringContainsString('Nama Ayah', $response->streamedContent());
        $this->assertStringContainsString('Minat A', $response->streamedContent());
        $this->assertStringContainsString('Jalan Pendidikan, Kel. Bintuni Timur, Kec. Bintuni, Kab. Teluk Bintuni', $response->streamedContent());
        $this->assertStringContainsString('Jalan Keluarga, Kel. Bintuni Barat, Kec. Bintuni, Kab. Teluk Bintuni', $response->streamedContent());
        $this->assertStringNotContainsString('Kabupaten Siswa', $response->streamedContent());
    }

    public function test_siswa_tidak_dapat_membuka_laporan(): void
    {
        $siswa = $this->buatPengguna('1234567890', 'User');

        $this->withSession(['pengguna_id' => $siswa->id_pengguna])
            ->get(route('admin.laporan'))
            ->assertForbidden();
    }

    private function buatPengguna(string $id, string $level): Pengguna
    {
        return Pengguna::create([
            'id_pengguna' => $id,
            'nama_pengguna' => $level,
            'telpon' => '628123456789',
            'username' => $id,
            'password' => 'password',
            'level' => $level,
            'is_verified' => true,
            'is_active' => true,
        ]);
    }

    private function buatFormulir(array $attributes = []): Formulir
    {
        return Formulir::create(array_merge([
            'nisn' => '1234567890',
            'nama' => 'Siswa Uji',
            'tempat_lahir' => 'Bintuni',
            'tanggal_lahir' => '2010-01-01',
            'nik' => '9206010101100001',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Kristen Protestan',
            'hp' => '628123456789',
            'asal_sekolah' => 'SMP Negeri 1 Bintuni',
            'alamat' => 'Bintuni',
            'nama_ayah' => 'Ayah',
            'pekerjaan_ayah' => 'Petani',
            'nama_ibu' => 'Ibu',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'hp_ortu' => '628123456780',
            'alamat_ortu' => 'Bintuni',
            'program_keahlian_1' => 'Teknik Komputer Jaringan',
            'program_keahlian_2' => 'Desain Komunikasi Visual',
            'surat_keterangan_lulus' => 'dokumen/ijazah.pdf',
            'kartu_keluarga' => 'dokumen/kk.pdf',
            'foto_selfie' => 'dokumen/foto.jpg',
            'status' => 'draft',
            'submitted_at' => null,
        ], $attributes));
    }
}
