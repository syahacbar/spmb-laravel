<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;

class PenggunaWhatsappController extends Controller
{
    public function __invoke(Pengguna $pengguna): RedirectResponse
    {
        if ($pengguna->level === 'Administrator') {
            abort(403);
        }

        if (! $pengguna->is_verified || ! $pengguna->is_active) {
            return back()->with('warning', 'Notifikasi WhatsApp hanya dapat dikirim untuk akun yang sudah terverifikasi dan aktif.');
        }

        $phone = $pengguna->whatsappPhone();

        if (! preg_match('/^62[0-9]{8,13}$/', $phone)) {
            return back()->with('warning', 'Nomor WhatsApp calon siswa tidak valid atau belum tersedia.');
        }

        return redirect()->away($pengguna->verificationWhatsappUrl());
    }
}
