<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TriageService;
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

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

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

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => [
                'required',
                'string',
                'in:doctor,nurse,pharmacist,lab_attendant,accountant,mortuary_attendant,receptionist',
                // admin is intentionally excluded — created via seeder/DB only
            ],
            'specialty' => [
                // Only required when registering as a doctor — used to
                // auto-route patients to the right specialist later.
                'required_if:role,doctor',
                'nullable',
                'string',
                'in:' . implode(',', array_keys(TriageService::labels())),
            ],
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
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'specialty' => $validated['role'] === 'doctor' ? $validated['specialty'] : null,
            'password'  => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectToDashboard();
    }

    // ─────────────────────────────────────────────
    //  DASHBOARD REDIRECT
    // ─────────────────────────────────────────────

    public function redirectToDashboard(): RedirectResponse
    {
        $role = Auth::user()->role;

        return match($role) {
            'admin'                => redirect()->route('admin.dashboard'),
            'doctor', 'nurse'      => redirect()->route('medical.dashboard'),
            'receptionist'         => redirect()->route('receptionist.dashboard'),
            'pharmacist'           => redirect()->route('pharmacy.index'),
            'lab_attendant'        => redirect()->route('lab.dashboard'),
            'accountant'           => redirect()->route('billing.index'),
            'mortuary_attendant'   => redirect()->route('mortuary.index'),
            default                => redirect()->route('login'),
        };
    }

    // ─────────────────────────────────────────────
    //  LOGOUT
    // ─────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
