<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('username', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User, 'roles' => UserRole::cases()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user, 'roles' => UserRole::cases()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->schedules()->exists()) {
            return back()->with('error', 'Pengguna masih memiliki jadwal mengajar.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user?->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
        ]);
    }
}
