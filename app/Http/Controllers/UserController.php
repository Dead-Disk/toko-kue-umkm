<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Pilihan role akun yang bisa dibuat/diubah admin.
     *
     * @var list<string>
     */
    private const ROLES = ['admin', 'staff'];

    /**
     * Daftar semua akun admin/staf.
     */
    public function index(Request $request): View
    {
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->get();
        $totalUsers = User::count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalStaff = User::where('role', 'staff')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalAdmin', 'totalStaff'));
    }

    /**
     * Form tambah akun baru.
     */
    public function create(): View
    {
        return view('admin.users.create', ['roles' => self::ROLES]);
    }

    /**
     * Simpan akun baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(self::ROLES)],
        ]);

        $user = User::create($data);

        ActivityLog::record(
            'tambah',
            'Menambahkan akun pengguna baru: '.$user->name.' ('.$user->username.', Role: '.$user->role.')',
            $user
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Form edit akun.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => self::ROLES]);
    }

    /**
     * Perbarui akun, termasuk reset password bila diisi.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(self::ROLES)],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        ActivityLog::record(
            'ubah',
            'Mengubah data pengguna: '.$user->name.' ('.$user->username.', Role: '.$user->role.')',
            $user
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus akun staf. Akun yang sedang login tidak boleh dihapus.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'Tidak bisa menghapus akun yang sedang login.']);
        }

        ActivityLog::record(
            'hapus',
            'Menghapus akun pengguna: '.$user->name.' ('.$user->username.')',
            $user
        );

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
