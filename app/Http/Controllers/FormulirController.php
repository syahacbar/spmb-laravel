<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormulirController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');
        $formulir = Formulir::where('nisn', $pengguna->id_pengguna)->first();

        if ($formulir) {
            if ($formulir->isSubmitted()) {
                return redirect()->route('formulir.riwayat');
            }

            return redirect()->route('formulir.edit', $formulir);
        }

        return view('formulir.form', [
            'pengguna' => $pengguna,
            'formulir' => new Formulir(['nisn' => $pengguna->id_pengguna]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $data = $this->validatedData($request);
        $data['nisn'] = $pengguna->id_pengguna;
        $data['status'] = 'draft';
        $data['submitted_at'] = null;
        $data = array_merge($data, $this->storeUploads($request));

        $formulir = Formulir::create($data);

        return redirect()->route('formulir.periksa', $formulir)->with('success', 'Data formulir berhasil disimpan. Periksa kembali sebelum mengirim final.');
    }

    public function riwayat(Request $request): View
    {
        $pengguna = $request->attributes->get('pengguna');

        return view('formulir.riwayat', [
            'pengguna' => $pengguna,
            'formulirs' => Formulir::where('nisn', $pengguna->id_pengguna)->latest('id')->get(),
        ]);
    }

    public function edit(Request $request, Formulir $formulir): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if ($pengguna->level !== 'Administrator' && $formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('warning', 'Formulir yang sudah dikirim final tidak dapat diedit.');
        }

        return view('formulir.form', compact('pengguna', 'formulir'));
    }

    public function update(Request $request, Formulir $formulir): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if ($pengguna->level !== 'Administrator' && $formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('warning', 'Formulir yang sudah dikirim final tidak dapat diedit.');
        }

        $formulir->update(array_merge(
            $this->validatedData($request, false),
            $this->storeUploads($request),
        ));

        if ($pengguna->level === 'Administrator') {
            return redirect()->route('admin.pendaftar')->with('success', 'Formulir berhasil diperbarui.');
        }

        return redirect()->route('formulir.periksa', $formulir)
            ->with('success', 'Formulir berhasil diperbarui. Periksa kembali sebelum mengirim final.');
    }

    public function periksa(Request $request, Formulir $formulir): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        return view('formulir.periksa', compact('pengguna', 'formulir'));
    }

    public function kirim(Request $request, Formulir $formulir): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if ($formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('success', 'Formulir sudah dikirim final.');
        }

        $formulir->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('formulir.riwayat')->with('success', 'Formulir berhasil dikirim final. Kartu pendaftaran sudah dapat dicetak.');
    }

    public function cetak(Request $request, Formulir $formulir): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if (! $formulir->isSubmitted()) {
            return redirect()->route('formulir.periksa', $formulir)->with('warning', 'Kartu pendaftaran dapat dicetak setelah formulir dikirim final.');
        }

        return view('formulir.cetak', compact('pengguna', 'formulir'));
    }

    private function validatedData(Request $request, bool $requireFiles = true): array
    {
        $fileRule = $requireFiles ? ['required', 'image', 'max:4096'] : ['nullable', 'image', 'max:4096'];

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:-13 years', 'after_or_equal:-21 years'],
            'nik' => ['required', 'string', 'max:30'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'agama' => ['required', 'string', 'max:50'],
            'hp' => ['required', 'string', 'max:20'],
            'asal_sekolah' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
            'nama_ayah' => ['required', 'string', 'max:100'],
            'pekerjaan_ayah' => ['required', 'string', 'max:100'],
            'nama_ibu' => ['required', 'string', 'max:100'],
            'pekerjaan_ibu' => ['required', 'string', 'max:100'],
            'hp_ortu' => ['required', 'string', 'max:20'],
            'alamat_ortu' => ['required', 'string'],
            'program_keahlian_1' => ['required', 'string', 'max:100', 'different:program_keahlian_2'],
            'program_keahlian_2' => ['required', 'string', 'max:100'],
            'surat_keterangan_lulus' => $fileRule,
            'kartu_keluarga' => $fileRule,
            'foto_selfie' => $fileRule,
        ]);

        unset($data['surat_keterangan_lulus'], $data['kartu_keluarga'], $data['foto_selfie']);

        return $data;
    }

    private function storeUploads(Request $request): array
    {
        $paths = [];
        $dir = public_path('uploads/dokumen');

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        foreach (['surat_keterangan_lulus', 'kartu_keluarga', 'foto_selfie'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $name = $request->session()->get('pengguna_id').'_'.time().'_'.$field.'.'.$file->extension();
            $file->move($dir, $name);
            $paths[$field] = 'uploads/dokumen/'.$name;
        }

        return $paths;
    }

    private function authorizeFormulir($pengguna, Formulir $formulir): void
    {
        if ($pengguna->level !== 'Administrator' && $formulir->nisn !== $pengguna->id_pengguna) {
            abort(403);
        }
    }
}
