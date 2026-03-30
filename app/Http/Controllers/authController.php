<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class authController extends Controller
{
   
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('Auth.login');
    }

    public function doLogin(Request $request)
    {
        $credentials = $request->validate(
            [
                'email_or_username' => 'required|string|min:3|max:255',
                'password' => 'required|string|min:6',
            ],
            [
                'email_or_username.required' => 'Email atau username harus diisi',
                'email_or_username.string' => 'Format email atau username tidak valid',
                'email_or_username.min' => 'Email atau username minimal 3 karakter',
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password minimal 6 karakter',
            ]
        );

        $user = User::where('email', $credentials['email_or_username'])
            ->orWhere('username', $credentials['email_or_username'])
            ->first();

        if (!$user) {
            Log::warning('Login attempt dengan user tidak ditemukan', [
                'identifier' => $credentials['email_or_username'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);

            throw ValidationException::withMessages([
                'email_or_username' => 'Email/username atau password salah.',
            ]);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            Log::warning('Login attempt dengan password salah', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);

            throw ValidationException::withMessages([
                'email_or_username' => 'Email/username atau password salah.',
            ]);
        }

        if (!$user->is_active) {
            Log::warning('Login attempt user inactive', ['user_id' => $user->id]);
            throw ValidationException::withMessages([
                'email_or_username' => 'User sudah dinonaktifkan. Hubungi administrator.',
            ]);
        }

        Auth::login($user, remember: $request->boolean('remember'));
        $request->session()->regenerate();

        Log::info('User berhasil login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        logActivity('Login Ke Aplikasi');

        return $this->redirectByRole($user);
    }

    private function redirectByRole($user)
    {
        
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard')
                ->with('success', 'Selamat datang, ' . $user->full_name . '!'),
            'owner' => redirect()->route('owner.dashboard')
                ->with('success', 'Selamat datang, ' . $user->full_name . '!'),
            'kasir' => redirect()->route('kasir.dashboard')
                ->with('success', 'Selamat datang, ' . $user->full_name . '!'),
            default => redirect()->route('dashboard')
                ->with('success', 'Login berhasil!'),
        };
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Log::info('User logout', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
        }
        
        logActivity('Logout Dari Aplikasi');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout. Sampai jumpa!');
    }
}
