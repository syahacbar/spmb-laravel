<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FormulirBerkasController;
use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaturanSignatureTest extends TestCase
{
    public function test_admin_dapat_membuka_tanda_tangan_private(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('pengaturan/tanda-tangan/ttd.png', 'tanda tangan');

        $controller = new class extends AdminController
        {
            protected function currentSignaturePath(): string
            {
                return 'pengaturan/tanda-tangan/ttd.png';
            }
        };

        $response = $controller->showSignature();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_pemilik_formulir_dapat_membuka_tanda_tangan_private_untuk_kartu(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('pengaturan/tanda-tangan/ttd.png', 'tanda tangan');

        $controller = new class extends FormulirBerkasController
        {
            protected function currentSignaturePath(): string
            {
                return 'pengaturan/tanda-tangan/ttd.png';
            }
        };
        $formulir = new Formulir(['nisn' => '1234567890']);
        $request = Request::create('/');
        $request->attributes->set('pengguna', new Pengguna([
            'id_pengguna' => '1234567890',
            'level' => 'User',
        ]));

        $response = $controller->signature($request, $formulir);

        $this->assertSame(200, $response->getStatusCode());
    }
}
