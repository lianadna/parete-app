<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\LogAktivitasAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminRegisterController extends Controller
{
    public function index(): View
    {
        return view('admin.register', [
            'admins' => Admin::query()->orderBy('username')->get(),
            'authAdminId' => (string) Auth::id(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => ['required', 'string', 'min:4', 'max:100'],
            'nama' => ['required', 'string', 'max:120'],
        ], [
            'username.regex' => 'Username hanya boleh huruf, angka, titik, strip, dan underscore.',
        ]);

        if (Admin::query()->where('username', $validated['username'])->exists()) {
            return back()
                ->withInput($request->only('username', 'nama'))
                ->withErrors(['username' => 'Username sudah digunakan.']);
        }

        Admin::query()->create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'nama' => $validated['nama'],
        ]);

        LogAktivitasAdmin::catat('menambah', 'admin '.$validated['username']);

        return redirect()->route('admin.register')->with('success', 'Admin berhasil didaftarkan.');
    }

    public function destroy(string $admin): RedirectResponse
    {
        $model = Admin::query()->findOrFail($admin);

        if ((string) $model->getKey() === (string) Auth::id()) {
            return redirect()->route('admin.register')->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $username = $model->username;
        $model->delete();

        LogAktivitasAdmin::catat('menghapus', 'admin '.$username);

        return redirect()->route('admin.register')->with('success', 'Admin berhasil dihapus.');
    }

    public function reveal(Request $request, string $admin): JsonResponse
    {
        $validated = $request->validate([
            'field' => ['required', 'in:username,password'],
            'verification_password' => ['required', 'string', 'max:100'],
        ]);

        $model = Admin::query()->findOrFail($admin);

        if (! hash_equals((string) $model->password, $validated['verification_password'])) {
            return response()->json([
                'message' => 'Kata sandi admin salah.',
            ], 422);
        }

        $value = $validated['field'] === 'username'
            ? $model->username
            : $model->password;

        return response()->json(['value' => $value]);
    }
}
