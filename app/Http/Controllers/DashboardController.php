<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Pengguna;
use App\Models\ProgramKeahlian;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $pengguna = $request->attributes->get('pengguna');
        $programs = ProgramKeahlian::query()->where('is_active', true)->ordered()->get();
        $programCounts = $pengguna->level === 'Administrator'
            ? $this->programCounts($programs)
            : collect();

        return view('dashboard', [
            'pengguna' => $pengguna,
            'totalPengguna' => Pengguna::where('level', 'User')->count(),
            'totalMenungguVerifikasi' => Pengguna::where('level', 'User')->where('is_verified', false)->count(),
            'totalTerverifikasi' => Pengguna::where('level', 'User')->where('is_verified', true)->count(),
            'totalFormulir' => Formulir::where('status', 'submitted')->count(),
            'totalDraft' => Formulir::where('status', 'draft')->count(),
            'programCounts' => $programCounts,
            'formulirSaya' => $pengguna->level === 'User'
                ? Formulir::where('nisn', $pengguna->id_pengguna)->latest('id')->first()
                : null,
        ]);
    }

    private function programCounts($programs)
    {
        $submitted = Formulir::where('status', 'submitted')
            ->get(['program_keahlian_1', 'program_keahlian_2']);

        return collect($programs)->map(function (ProgramKeahlian $program) use ($submitted): array {
            $kuota = (int) $program->kuota;
            $aliases = $program->aliases ?? [];
            $acceptedNames = collect([$program->nama, ...$aliases])->map(fn (string $name) => $this->normalizeProgramName($name))->all();
            $minatA = $submitted->filter(fn (Formulir $formulir) => in_array($this->normalizeProgramName($formulir->program_keahlian_1), $acceptedNames, true))->count();
            $minatB = $submitted->filter(fn (Formulir $formulir) => in_array($this->normalizeProgramName($formulir->program_keahlian_2), $acceptedNames, true))->count();
            $total = $minatA + $minatB;

            return [
                'nama' => $program->nama,
                'minat_a' => $minatA,
                'minat_b' => $minatB,
                'total' => $total,
                'kuota' => $kuota,
                'persen' => $kuota > 0 ? min(100, round(($minatA / $kuota) * 100)) : 0,
            ];
        })->values();
    }

    private function normalizeProgramName(?string $program): string
    {
        return trim(preg_replace('/\s*\([A-Z]+\)\s*$/', '', (string) $program));
    }
}
