<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\PengaturanSpmb;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RegistrationServiceHoursTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_cek_nisn_ditolak_di_luar_periode_layanan(): void
    {
        $this->aturPeriodeLayananTutup();

        $response = $this->postJson('/daftar/cek-nisn', [
            'nisn' => '1234567890',
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'Layanan pendaftaran dibuka tanggal 1 Juli 2026 sampai 3 Juli 2026 selama 24 jam WIT.');
    }

    public function test_pembuatan_akun_ditolak_di_luar_periode_layanan(): void
    {
        $this->aturPeriodeLayananTutup();
        $this->buatCalonSiswa();

        $response = $this
            ->withSession(['register_captcha_answer' => '10'])
            ->post('/daftar', [
                'nisn' => '1234567890',
                'no_wa' => '81234567890',
                'password' => 'secret',
                'password_confirmation' => 'secret',
                'captcha_answer' => '10',
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors([
                'nisn' => 'Layanan pendaftaran dibuka tanggal 1 Juli 2026 sampai 3 Juli 2026 selama 24 jam WIT.',
            ]);
        $this->assertDatabaseMissing('tb_pengguna', ['id_pengguna' => '1234567890']);
    }

    public function test_login_siswa_ditolak_di_luar_periode_layanan(): void
    {
        $this->aturPeriodeLayananTutup();
        $siswa = $this->buatPengguna();

        $response = $this
            ->withSession(['login_captcha_answer' => '10'])
            ->post('/login', [
                'nisn' => $siswa->id_pengguna,
                'password' => 'secret',
                'captcha_answer' => '10',
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors([
                'nisn' => 'Layanan pendaftaran dibuka tanggal 1 Juli 2026 sampai 3 Juli 2026 selama 24 jam WIT.',
            ]);
    }

    public function test_login_admin_tetap_diizinkan_di_luar_periode_layanan(): void
    {
        $this->aturPeriodeLayananTutup();
        $admin = $this->buatPengguna([
            'id_pengguna' => 'admin',
            'username' => 'admin',
            'level' => 'Administrator',
            'is_verified' => false,
            'is_active' => false,
        ]);

        $response = $this
            ->withSession(['login_captcha_answer' => '10'])
            ->post('/login', [
                'nisn' => $admin->id_pengguna,
                'password' => 'secret',
                'captcha_answer' => '10',
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedSession($admin->id_pengguna);
    }

    public function test_layanan_terbuka_selama_tanggal_satu_sampai_tiga_juli(): void
    {
        $this->aturPeriodeLayananTutup();

        $this->assertTrue(PengaturanSpmb::registrationServiceIsOpen(Carbon::parse('2026-07-02 12:00:00', 'Asia/Jayapura')));
        $this->assertFalse(PengaturanSpmb::registrationServiceIsOpen(Carbon::parse('2026-07-04 00:00:00', 'Asia/Jayapura')));
    }

    public function test_admin_dapat_menyimpan_periode_layanan(): void
    {
        $admin = $this->buatPengguna([
            'id_pengguna' => 'admin',
            'username' => 'admin',
            'level' => 'Administrator',
        ]);

        $response = $this
            ->withSession(['pengguna_id' => $admin->id_pengguna])
            ->post(route('admin.pengaturan.layanan-pendaftaran'), [
                'layanan_pendaftaran_aktif' => '1',
                'tanggal_buka_layanan_pendaftaran' => '2026-07-01',
                'tanggal_tutup_layanan_pendaftaran' => '2026-07-03',
                'jam_buka_layanan_pendaftaran' => '00:00',
                'jam_tutup_layanan_pendaftaran' => '23:59',
                'pesan_layanan_pendaftaran_tutup' => 'Pendaftaran SPMB dibuka 1-3 Juli 2026 selama 24 jam WIT.',
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('success', 'Periode layanan pendaftaran berhasil diperbarui.');
        $this->assertSame('2026-07-01', PengaturanSpmb::getValue('tanggal_buka_layanan_pendaftaran'));
        $this->assertSame('2026-07-03', PengaturanSpmb::getValue('tanggal_tutup_layanan_pendaftaran'));
        $this->assertSame('00:00', PengaturanSpmb::getValue('jam_buka_layanan_pendaftaran'));
        $this->assertSame('23:59', PengaturanSpmb::getValue('jam_tutup_layanan_pendaftaran'));
        $this->assertSame('Pendaftaran SPMB dibuka 1-3 Juli 2026 selama 24 jam WIT.', PengaturanSpmb::getValue('pesan_layanan_pendaftaran_tutup'));
    }

    public function test_pesan_custom_layanan_tutup_dipakai_saat_cek_nisn(): void
    {
        $this->aturPeriodeLayananTutup([
            'pesan_layanan_pendaftaran_tutup' => 'Mohon maaf, pendaftaran baru dapat dilakukan pada 1-3 Juli 2026.',
        ]);

        $response = $this->postJson('/daftar/cek-nisn', [
            'nisn' => '1234567890',
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('message', 'Mohon maaf, pendaftaran baru dapat dilakukan pada 1-3 Juli 2026.');
    }

    private function aturPeriodeLayananTutup(array $settings = []): void
    {
        PengaturanSpmb::setMany(array_merge([
            'layanan_pendaftaran_aktif' => '1',
            'tanggal_buka_layanan_pendaftaran' => '2026-07-01',
            'tanggal_tutup_layanan_pendaftaran' => '2026-07-03',
            'jam_buka_layanan_pendaftaran' => '00:00',
            'jam_tutup_layanan_pendaftaran' => '23:59',
            'pesan_layanan_pendaftaran_tutup' => '',
        ], $settings));

        Carbon::setTestNow(Carbon::parse('2026-06-30 15:00:00', 'Asia/Jayapura'));
    }

    private function buatCalonSiswa(): CalonSiswa
    {
        return CalonSiswa::create([
            'nisn' => '1234567890',
            'nama' => 'Budi Santoso',
            'tempat_lahir' => 'Bintuni',
            'tanggal_lahir' => '2010-01-01',
            'asal_sekolah' => 'SMP Negeri 1 Bintuni',
            'tahun_pendaftaran' => '2026',
            'is_active' => true,
        ]);
    }

    private function buatPengguna(array $attributes = []): Pengguna
    {
        return Pengguna::create(array_merge([
            'id_pengguna' => '1234567890',
            'nama_pengguna' => 'Pengguna Uji',
            'telpon' => '6281234567890',
            'username' => '1234567890',
            'password' => 'secret',
            'level' => 'User',
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => now(),
        ], $attributes));
    }

    private function assertAuthenticatedSession(string $penggunaId): void
    {
        $this->assertSame($penggunaId, session('pengguna_id'));
    }
}
