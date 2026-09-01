<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function index(): View
    {
        $jabatanGroups = Jabatan::query()
            ->active()
            ->ordered()
            ->whereHas('pegawais', fn ($q) => $q->published())
            ->with(['pegawais' => fn ($q) => $q->published()->ordered()])
            ->get();

        $totalPegawai = Pegawai::query()->published()->count();

        return view('public.pegawai.index', compact('jabatanGroups', 'totalPegawai'));
    }
}
