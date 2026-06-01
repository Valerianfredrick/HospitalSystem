<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\MortuaryController;
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

            Route::get('/',           [PatientController::class, 'index'])->name('index');
            Route::get('/create',     [PatientController::class, 'create'])->name('create');
            Route::post('/',          [PatientController::class, 'store'])->name('store');
            Route::get('/admissions', [PatientController::class, 'admission'])->name('admission');
            // Inside patients prefix group — replace the old discharge.submit
            Route::put('/discharge', [PatientController::class, 'dischargeSubmit'])->name('discharge.submit');

            Route::get('/{patient}',      [PatientController::class, 'show'])->name('show');
            Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
            Route::put('/{patient}',      [PatientController::class, 'update'])->name('update');

            Route::get('/{patient}/discharge',  [PatientController::class, 'dischargeForm'])->name('discharge.form');
            Route::put('/{patient}/discharge',  [PatientController::class, 'dischargeSubmit'])->name('discharge.submit');

            Route::get('/{patient}/notes/create', fn($patient) => view('patients.notes_create', compact('patient')))->name('notes.create');
            Route::post('/{patient}/notes',       [PatientController::class, 'addNote'])->name('notes.store');

            Route::get('/{patient}/prescriptions/create', fn($patient) => view('patients.prescriptions_create', compact('patient')))->name('prescriptions.create');
            Route::post('/{patient}/prescriptions',       [PatientController::class, 'addPrescription'])->name('prescriptions.store');

            // ── Lab Requests (nested under patient, created by doctor/nurse) ──
            Route::prefix('/{patient}/lab')->name('lab.')->group(function () {
                Route::get('/create', [LabController::class, 'create'])->name('create');
                Route::post('/',      [LabController::class, 'store'])->name('store');
            });
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

    // ── Lab Attendant + Doctor/Nurse routes ────────────────────────────
    Route::middleware('role:lab_attendant,doctor,nurse')->prefix('lab')->name('lab.')->group(function () {
        Route::get('/dashboard',                       [LabController::class, 'index'])->name('dashboard');
        Route::get('/requests',                        [LabController::class, 'requests'])->name('requests');
        Route::get('/requests/{labRequest}',           [LabController::class, 'show'])->name('show');
        Route::patch('/requests/{labRequest}/start',   [LabController::class, 'startTest'])->name('start');
        Route::patch('/requests/{labRequest}/results', [LabController::class, 'submitResults'])->name('results');
    });
});

Route::middleware('role:accountant,admin')->prefix('billing')->name('billing.')->group(function () {
    Route::get('/',                   [BillingController::class, 'index'])->name('index');
    Route::get('/{bill}',             [BillingController::class, 'show'])->name('show');
    Route::patch('/{bill}/process',   [BillingController::class, 'process'])->name('process');
    Route::patch('/{bill}/waive',     [BillingController::class, 'waive'])->name('waive');
});

// ── Mortuary routes ────────────────────────────────────────────────────
Route::middleware('role:mortuary,admin')->prefix('mortuary')->name('mortuary.')->group(function () {
    Route::get('/',                        [MortuaryController::class, 'index'])->name('index');
    Route::get('/{record}',                [MortuaryController::class, 'show'])->name('show');
    Route::patch('/{record}/receive',      [MortuaryController::class, 'receive'])->name('receive');
    Route::patch('/{record}/release',      [MortuaryController::class, 'release'])->name('release');
});
