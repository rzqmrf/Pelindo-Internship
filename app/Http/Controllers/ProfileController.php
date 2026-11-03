<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['internProfile.division', 'internProfile.supervisor', 'supervisorProfile.division']);
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($user->isIntern() && $user->internProfile) {
            $rules = array_merge($rules, [
                'institution' => 'nullable|string|max:255',
                'major' => 'nullable|string|max:255',
                'student_id_number' => 'nullable|string|max:50',
                'nik_number' => 'nullable|string|max:30',
                'bank_name' => 'nullable|string|max:100',
                'bank_account_number' => 'nullable|string|max:50',
                'bank_account_holder' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:1000',
            ]);
        }

        $request->validate($rules);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path('storage/' . $user->avatar))) {
                @unlink(public_path('storage/' . $user->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        if ($user->isIntern() && $user->internProfile) {
            $user->internProfile->update([
                'institution' => $request->institution,
                'major' => $request->major,
                'student_id_number' => $request->student_id_number,
                'nik_number' => $request->nik_number,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
                'address' => $request->address,
            ]);
        }

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLogger::record('profile.password_change', 'auth', 'Pengguna memperbarui kata sandi akunnya.', Auth::user());

        return redirect()->back()->with('success', 'Password Anda berhasil diperbarui.');
    }
}
