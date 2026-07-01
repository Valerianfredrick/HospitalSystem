<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Roles an admin can assign — both when creating a new account and
     * when changing an existing user's role. 'admin' is included here
     * deliberately: self-registration excludes it (see AuthController),
     * so this panel is the intended channel for granting admin access.
     */
    private const ROLES = [
        'admin', 'doctor', 'nurse', 'pharmacist',
        'lab_attendant', 'accountant', 'mortuary_attendant',
    ];

    public function dashboard()
    {
        $totalUsers    = User::count();
        $doctors       = User::where('role', 'doctor')->count();
        $totalPatients = Patient::count();
        $stockItems    = StockItem::count();

        // Preview list for the dashboard table — full management lives on
        // the dedicated admin.users page (see users() below).
        $users = User::withCount('patients')
            ->orderBy('role')
            ->orderBy('name')
            ->take(10)
            ->get();

        $todayAdmissions    = Patient::whereDate('admitted_at', today())->count();
        $todayDischarges    = Patient::whereDate('discharged_at', today())->count();
        $todayPrescriptions = Prescription::whereDate('created_at', today())->count();
        $todayDispensed     = Prescription::where('is_dispensed', true)
            ->whereDate('dispensed_at', today())
            ->count();

        return view('admin.dashboard', compact(
            'totalUsers', 'doctors', 'totalPatients', 'stockItems', 'users',
            'todayAdmissions', 'todayDischarges', 'todayPrescriptions', 'todayDispensed'
        ));
    }

    public function users(Request $request)
    {
        $users = User::withCount('patients')
            ->when($request->search, function ($q) use ($request) {
                $term = $request->search;
                $q->where(function ($q2) use ($term) {
                    $q2->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $roles = self::ROLES;

        return view('admin.users', compact('users', 'roles'));
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'string', 'in:' . implode(',', self::ROLES)],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->uncompromised(),
            ],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', "User \"{$validated['name']}\" created successfully.");
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:' . implode(',', self::ROLES)],
        ]);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $validated['role']]);

        return back()->with(
            'success',
            "{$user->name}'s role was updated to " . ucwords(str_replace('_', ' ', $validated['role'])) . '.'
        );
    }
}
