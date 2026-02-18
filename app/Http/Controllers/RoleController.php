<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Dashboard berdasarkan role user
     */
    public function dashboard()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin' => view('Admin.dashboard', ['user' => $user]),
            'owner' => view('Owner.dashboard', ['user' => $user]),
            'kasir' => view('Kasir.dashboard', ['user' => $user]),
            default => abort(403)
        };
    }

    /**
     * Update role user (admin only)
     */
    // public function updateRole(Request $request, $userId)
    // {
    //     $request->validate(['role' => 'required|in:admin,mentor,student']);

    //     User::find($userId)->update(['role' => $request->role]);

    //     return back()->with('success', 'Role berhasil diubah');
    // }
}

