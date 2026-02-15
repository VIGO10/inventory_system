<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ]);

        // Detect email or username
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find user
        $user = User::where($field, $request->identifier)->first();

        $credentials = [
            $field    => $request->identifier,
            'password' => $request->password,
        ];

        // ❌ User not found
        if (!$user) {
            throw ValidationException::withMessages([
                'identifier' => ['There is no user with this username or email.'],
            ]);
        }

        // ❌ Password incorrect
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The password you enter is incorrect.'],
            ]);
        }

        // ✅ Login success
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on role (example)
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }   

            return redirect()
                    ->route('auth.login')
                    ->with('fail', 'Session expired. Please login again.');
        }
    }

    /**
     * Show the application's registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'fullname'              => ['required', 'string', 'max:255'],
            'username'              => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'confirmed'],
        ]);

        // Create the new user
        $user = User::create([
            'fullname'          => $request['fullname'],
            'username'      => $request['username'],
            'email'         => $request['email'],
            'role'          => 'client',        // default role
            'password'      => Hash::make($request['password']),
            'is_verified'   => false,           // new users start unverified
        ]);

        // Redirect to login
        return redirect()->route('login')
            ->with('success', 'Registration successful! Welcome to the Inventory System.');    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}