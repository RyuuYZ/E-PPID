<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => ['nullable', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($user->profile_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Update Profil',
            'description' => "Pengguna memperbarui profilnya",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateSignature(Request $request)
    {
        $request->validate([
            'signature_data' => 'required|string'
        ]);

        $user = auth()->user();

        // Decode base64 image
        $image_parts = explode(";base64,", $request->signature_data);
        if (count($image_parts) < 2) {
            return back()->with('error', 'Format data tanda tangan tidak valid.');
        }
        
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $filename = 'signature_' . time() . '_' . uniqid() . '.' . $image_type;
        $ttdPath = 'signatures/' . $filename;
        
        \Illuminate\Support\Facades\Storage::disk('public')->put($ttdPath, $image_base64);

        // Delete old signature if exists
        if ($user->signature_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->signature_path);
        }

        $user->update(['signature_path' => $ttdPath]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Update TTE',
            'description' => "Pengguna memperbarui tanda tangan elektronik",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.profile.index')->with('success', 'Tanda tangan elektronik berhasil disimpan.');
    }
}
