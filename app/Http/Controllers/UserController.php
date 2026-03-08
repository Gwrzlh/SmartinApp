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
        ->paginate(5) // Menampilkan 10 data per halaman
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
            'password'  => 'required|min:6',
            'role'      => 'required|in:admin,kasir,owner', 
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

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
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
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,owner,kasir',
            'username' => 'required|unique:users,username,' . $user->id,
            // 'is_active' => 'required|boolean',
        ]);
 
        $isActive = $request->has('active') ? 1 : 0;
        
        $user->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role' => $request->role,
            'username' => $request->username,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        // Cek jika request datang dari AJAX/Fetch
        if (request()->ajax()) {
            return response()->json([
                'username'   => $user->username,
                'full_name'  => $user->full_name,
                'email'      => $user->email,
                'role'       => $user->role,
                'is_active'  => $user->is_active,
                // Format tanggal agar bagus dibaca JS
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ]);
        }

        // Jika diakses biasa (bukan AJAX), bisa diarahkan ke view lain atau abort
        return abort(404);
    }
  
}
