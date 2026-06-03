<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitasAdmin;
use App\Models\ProfilRt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilRtController extends Controller
{
    public function edit(): View
    {
        return view('profil-rt', [
            'profil' => ProfilRt::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_ketua_rt' => ['required', 'string', 'max:120'],
            'nomor_rt' => ['required', 'string', 'max:3', 'regex:/^\d{1,3}$/'],
            'nomor_rw' => ['required', 'string', 'max:3', 'regex:/^\d{1,3}$/'],
            'kelurahan' => ['required', 'string', 'max:120'],
            'kecamatan' => ['required', 'string', 'max:120'],
            'kota' => ['required', 'string', 'max:120'],
            'provinsi' => ['required', 'string', 'max:120'],
            'kode_pos' => ['required', 'string', 'size:5', 'regex:/^\d{5}$/'],
        ], [
            'nomor_rt.regex' => 'Nomor RT hanya boleh berisi angka (maks. 3 digit).',
            'nomor_rw.regex' => 'Nomor RW hanya boleh berisi angka (maks. 3 digit).',
            'kode_pos.regex' => 'Kode pos harus 5 digit angka.',
        ]);

        $profil = ProfilRt::current();
        $profil->fill($validated);
        $profil->save();

        LogAktivitasAdmin::catat('memperbarui', 'profil RT');

        return redirect()->route('profil-rt.edit')->with('success', 'Profil RT berhasil disimpan.');
    }
}
