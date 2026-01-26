<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function showDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show the user management page.
     */
    public function index()
    {
        $users = User::query()
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Verify a user.
     */
    public function _verifyUser(User $user)
    {
        // Verify the user
        $user->is_verified = true;
        $user->save();

        return redirect()->route('admin.user.index')->with('success', 'User verified successfully.');
    }

    /**
     * Delete a user.
     */
    public function _deleteUser(User $user)
    {
        // Delete the user
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');
    }
}