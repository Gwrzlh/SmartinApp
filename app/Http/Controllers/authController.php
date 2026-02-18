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
        // Jika sudah login, redirect ke dashboard
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

        // 5. CEGAH LOGIN USER INACTIVE (opsional, jika ada column is_active)
        // if (!$user->is_active) {
        //     Log::warning('Login attempt user inactive', ['user_id' => $user->id]);
        //     throw ValidationException::withMessages([
        //         'email_or_username' => 'User sudah dinonaktifkan. Hubungi administrator.',
        //     ]);
        // }

        // 6. LOGIN USER (create session)
        Auth::login($user, remember: $request->boolean('remember'));

        // 7. REGENERATE SESSION (prevent session fixation attack)
        $request->session()->regenerate();

        // 8. LOG SUCCESSFUL LOGIN
        Log::info('User berhasil login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        // 9. REDIRECT BASED ON ROLE
        return $this->redirectByRole($user);
    }

    /**
     * Redirect ke dashboard sesuai role
     */
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

    /**
     * Logout user
     * Route: POST /logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout activity
        if ($user) {
            Log::info('User logout', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
        }

        // Logout
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout. Sampai jumpa!');
    }
}
