<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'unit_pengolah'])->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $unitPengolahs = \App\Models\UnitPengolah::orderBy('nama_bidang')->get();
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $hasSuperAdmin = $superAdminRole ? User::where('role_id', $superAdminRole->id)->exists() : false;
        
        return view('admin.users.index', compact('users', 'roles', 'unitPengolahs', 'hasSuperAdmin', 'superAdminRole'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
            'role_id' => 'required|exists:roles,id',
            'unit_pengolah_id' => 'nullable|exists:unit_pengolahs,id',
            'is_active' => 'boolean'
        ]);

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && $validated['role_id'] == $superAdminRole->id) {
            if (User::where('role_id', $superAdminRole->id)->exists()) {
                return back()->withInput()->withErrors(['role_id' => 'Role Super Admin sudah dipakai. Sistem hanya mengizinkan 1 akun Super Admin.']);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        $validated['must_change_password'] = true;

        $user = User::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Create User',
            'description' => "Dibuat pengguna baru: {$user->username}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
            'role_id' => 'required|exists:roles,id',
            'unit_pengolah_id' => 'nullable|exists:unit_pengolahs,id',
            'is_active' => 'boolean'
        ]);

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && $validated['role_id'] == $superAdminRole->id) {
            if (User::where('role_id', $superAdminRole->id)->where('id', '!=', $user->id)->exists()) {
                return back()->withInput()->withErrors(['role_id' => 'Role Super Admin sudah dipakai. Sistem hanya mengizinkan 1 akun Super Admin.']);
            }
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
            $validated['must_change_password'] = true; // reset so they have to change if admin resets it
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Update User',
            'description' => "Diperbarui pengguna: {$user->username}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $username = $user->username;
        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Delete User',
            'description' => "Dihapus pengguna: {$username}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function reset2fa(User $user)
    {
        $user->update(['google2fa_secret' => null]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Reset 2FA',
            'description' => "2FA direset untuk: {$user->username}",
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', '2FA untuk pengguna ini berhasil direset.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'diblokir';
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Toggle User Status',
            'description' => "Pengguna {$user->username} " . $status,
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', "Pengguna berhasil {$status}.");
    }
}
