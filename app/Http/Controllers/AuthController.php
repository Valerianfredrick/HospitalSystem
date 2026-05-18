<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────
    //  LOGIN
    // ─────────────────────────────────────────────

    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Throttle: max 5 attempts per minute per email+IP
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => __("Too many login attempts. Please try again in {$seconds} seconds."),
                ]);
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => __('These credentials do not match our records.'),
                ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return $this->redirectToDashboard();
    }

    // ─────────────────────────────────────────────
    //  REGISTER
    // ─────────────────────────────────────────────

    /**
     * Show the registration form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'string', 'in:doctor,nurse,pharmacist,admin'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return $this->redirectToDashboard();
    }

    // ─────────────────────────────────────────────
    //  DASHBOARD REDIRECT
    // ─────────────────────────────────────────────

    /**
     * Redirect the authenticated user to their role-appropriate dashboard.
     */
    public function redirectToDashboard(): RedirectResponse
    {
        $role = Auth::user()->role;

        return match($role) {
            'admin'                  => redirect()->route('admin.dashboard'),
            'pharmacist'             => redirect()->route('pharmacy.index'),
            'doctor', 'nurse'        => redirect()->route('medical.dashboard'),
            default                  => redirect()->route('login'),
        };
    }

    // ─────────────────────────────────────────────
    //  LOGOUT
    // ─────────────────────────────────────────────

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
