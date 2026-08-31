<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class RoleController extends Controller
{
    // Define the available permissions in the system
    private $availablePermissions = [
        'manage_users' => 'Manajemen Pengguna',
        'manage_roles' => 'Pengaturan Hak Akses (Role)',
        'manage_settings' => 'Konfigurasi Sistem',
        'manage_logs' => 'Log Keamanan & Aktivitas',
        'manage_master_data' => 'Master Data (Kategori, Bidang)',
        'manage_persuratan' => 'Manajemen Persuratan (Masuk/Keluar)',
        'manage_permohonan' => 'Manajemen Permohonan Informasi',
    ];

    public function index()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $availablePermissions = $this->availablePermissions;
        return view('admin.roles.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Create Role',
            'description' => "Dibuat role baru: {$role->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        $availablePermissions = $this->availablePermissions;
        return view('admin.roles.edit', compact('role', 'availablePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array'
        ]);

        if (!isset($validated['permissions'])) {
            $validated['permissions'] = [];
        }

        $role->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Update Role',
            'description' => "Diperbarui role: {$role->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }

        $name = $role->name;
        $role->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Delete Role',
            'description' => "Dihapus role: {$name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
