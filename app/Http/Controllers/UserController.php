<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $users = User::when($search, function ($query) use ($search) {
            return $query->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(5)
        ->withQueryString();
        return view('Admin.Users.index', compact('users'));
    }
    public function create()
    {
        return view('Admin.Users.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'username'  => 'required|unique:users,username',
            'full_name' => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'role'      => 'required|in:admin,kasir,owner', 
            
        ],
        [
            'password.confirmed' => 'Konfirmasi password tidak sesuai dengan password utama.',
    
        ]);


        $isActive = $request->has('active') ? 1 : 0;

        User::create([
            'username'  => $request->username,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => $request->role,
            'is_active' => $isActive,
        ]);

        logActivity('Menambah User Baru', 'User: ' . $request->username);

        if(auth()->user()->role == 'admin'){
            return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
        }else{
            return redirect()->route('owner.manajemenStaff')->with('success', 'User berhasil ditambahkan!');
        }
    }
    public function edit(User $user)
    {
        return view('Admin.Users.update', compact('user'));
    }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'username' => 'required|unique:users,username,' . $user->id,
            // 'is_active' => 'required|boolean',
        ],
        [
            'password.confirmed' => 'Konfirmasi password tidak sesuai dengan password utama.',
    
    ]);
        $isActive = $request->has('active') ? 1 : 0;
        $user->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'username' => $request->username,
            'is_active' => $isActive,
        ]);

        logActivity('Mengubah Data User', 'User: ' . $request->username);

        if(auth()->user()->role == 'admin'){
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        }else{
            return redirect()->route('owner.manajemenStaff')->with('success', 'User updated successfully.');
        }
    }
    public function destroy(User $user)
    {
        $username = $user->username;
        $user->delete();
        logActivity('Menghapus User', 'User: ' . $username);
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        if (request()->ajax()) {
            return response()->json([
                'username'   => $user->username,
                'full_name'  => $user->full_name,
                'email'      => $user->email,
                'role'       => $user->role,
                'is_active'  => $user->is_active,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ]);
        }

        // Jika diakses biasa (bukan AJAX), bisa diarahkan ke view lain atau abort
        return abort(404);
    }
    public function manajemenStaff(Request $request)
    {
        $search = $request->get('search');

        $users = User::whereIn('role', ['admin', 'kasir', 'owner'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Menambahkan statistik untuk setiap user
        $users->getCollection()->transform(function ($user) {
            // Last Login dari ActivityLog
            $lastLogin = \App\Models\ActivityLog::where('user_id', $user->id)
                ->where('action', 'Login Ke Aplikasi')
                ->latest()
                ->first();
            $user->last_login_at = $lastLogin ? $lastLogin->created_at : null;

            // Statistik nominal transaksi untuk Kasir
            if ($user->role === 'kasir') {
                $user->total_revenue = \App\Models\transactions::where('user_id', $user->id)
                    ->where('status_pembayaran', 'paid')
                    ->sum('total_bayar');
            } else {
                $user->total_revenue = 0;
            }

            return $user;
        });

        return view('Owner.manajemanStaff', compact('users'));
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        logActivity('Mengubah Status User', "User {$user->username} telah {$status}");

        return back()->with('success', "Status user {$user->username} berhasil {$status}.");
    }
}
