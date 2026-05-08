<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Redirect to main landing page instead of legacy Login.vue
        return redirect()->route('landing');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();

            // ✅ Redirect user based on their role
            switch ($user->role) {
                case 'customer':
                    return redirect()->route('customer.dashboard');
                
                case 'retailer':
                    return redirect()->route('retailer.dashboard');
                
                case 'delivery_partner':
                case 'delivery-partner': // Support both naming conventions
                    return redirect()->route('delivery.dashboard');
                
                case 'admin':
                    return redirect()->route('admin.dashboard');
                
                default:
                    // 🔸 Default fallback for undefined roles
                    return redirect()->route('landing');
            }
        }

        // ❌ Invalid login
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // 🔹 ADDED: Simple Admin Login for AdminLanding page
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check hardcoded admin credentials
        if ($request->email === 'admin@gmail.com' && $request->password === 'admin123') {
            // Store admin data in session
            session([
                'admin' => [
                    'email' => 'admin@gmail.com',
                    'fullName' => 'Tankonek Administrator',
                    'role' => 'admin',
                    'permissions' => ['manage_users', 'view_reports', 'manage_orders', 'system_settings']
                ]
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid admin credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}