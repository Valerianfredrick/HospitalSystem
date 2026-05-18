<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Hospital Management System
|--------------------------------------------------------------------------
*/

// ── Public landing page ────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Guest-only auth routes ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Authenticated Routes ───────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard — redirects to role-appropriate view
    Route::get('/dashboard', [AuthController::class, 'redirectToDashboard'])->name('dashboard');

    // ── Doctor / Nurse routes ──────────────────────────────────────────
    Route::middleware('role:doctor,nurse')->group(function () {

        Route::get('/medical/dashboard', [PatientController::class, 'dashboard'])->name('medical.dashboard');

        // ── Patients ──────────────────────────────────────────────────
        Route::prefix('patients')->name('patients.')->group(function () {

            // ── Static routes FIRST (before any /{patient} wildcard) ──
            Route::get('/',           [PatientController::class, 'index'])->name('index');
            Route::get('/create',     [PatientController::class, 'create'])->name('create');
            Route::post('/',          [PatientController::class, 'store'])->name('store');
            Route::get('/admissions', [PatientController::class, 'admission'])->name('admission');
            Route::get('/discharges', [PatientController::class, 'discharge'])->name('discharge');

            // ── Wildcard routes AFTER static routes ──
            Route::get('/{patient}',      [PatientController::class, 'show'])->name('show');
            Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
            Route::put('/{patient}',      [PatientController::class, 'update'])->name('update');

            // Discharge
            Route::get('/{patient}/discharge', [PatientController::class, 'dischargeForm'])->name('discharge.form');
            Route::put('/{patient}/discharge', [PatientController::class, 'dischargeSubmit'])->name('discharge.submit');

            // Clinical notes
            Route::get('/{patient}/notes/create', fn($patient) => view('patients.notes_create', compact('patient')))->name('notes.create');
            Route::post('/{patient}/notes',       [PatientController::class, 'addNote'])->name('notes.store');

            // Prescriptions
            Route::get('/{patient}/prescriptions/create', fn($patient) => view('patients.prescriptions_create', compact('patient')))->name('prescriptions.create');
            Route::post('/{patient}/prescriptions',       [PatientController::class, 'addPrescription'])->name('prescriptions.store');
        });
    });

    // ── Pharmacy routes ────────────────────────────────────────────────
    Route::middleware('role:pharmacist,admin')->prefix('pharmacy')->name('pharmacy.')->group(function () {
        Route::get('/',                      [PharmacyController::class, 'index'])->name('index');
        Route::get('/create',                [PharmacyController::class, 'create'])->name('create');
        Route::post('/',                     [PharmacyController::class, 'store'])->name('store');
        Route::get('/{stockItem}/edit',      [PharmacyController::class, 'edit'])->name('edit');
        Route::put('/{stockItem}',           [PharmacyController::class, 'update'])->name('update');
        Route::get('/{stockItem}/restock',   [PharmacyController::class, 'showRestock'])->name('restock');
        Route::post('/{stockItem}/restock',  [PharmacyController::class, 'restock'])->name('restock.submit');
        Route::put('/prescriptions/{prescription}/dispense', [PharmacyController::class, 'dispense'])->name('dispense');
    });

    // ── Admin routes ───────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users',     [AdminController::class, 'users'])->name('users');
        Route::post('/users',    [AdminController::class, 'createUser'])->name('users.store');
        Route::put('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
    });
});
