<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\MortuaryController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\ReceptionistController;

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
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
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

        Route::get('/medical/dashboard', [PatientController::class, 'dashboard'])
            ->name('medical.dashboard');

        // Doctor's filtered "my patients" list (patients where doctor_id = Auth::id())
        Route::get('/my-patients', [PatientController::class, 'myPatients'])
            ->name('patients.mine');

        // ── Patients ──────────────────────────────────────────────────
        Route::prefix('patients')->name('patients.')->group(function () {

            // Static routes FIRST (must be before /{patient})
            Route::get('/',           [PatientController::class, 'index'])->name('index');
            Route::get('/create',     [PatientController::class, 'create'])->name('create');
            Route::post('/',          [PatientController::class, 'store'])->name('store');
            Route::get('/admissions', [PatientController::class, 'admission'])->name('admission');
            Route::get('/discharges', [PatientController::class, 'discharge'])->name('discharge');

            // Dynamic {patient} routes
            Route::get('/{patient}',      [PatientController::class, 'show'])->name('show');
            Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
            Route::put('/{patient}',      [PatientController::class, 'update'])->name('update');

            // Discharge
            Route::get('/{patient}/discharge', [PatientController::class, 'dischargeForm'])->name('discharge.form');
            Route::put('/{patient}/discharge', [PatientController::class, 'dischargeSubmit'])->name('discharge.submit');

            // Mortuary transfer (from patient list — doctor/nurse initiates)
            Route::post('/{patient}/mortuary', [PatientController::class, 'transferToMortuary'])->name('mortuary.transfer');

            // Generate bill (doctor sends to accountant)
            Route::post('/{patient}/generate-bill', [PatientController::class, 'generateBill'])->name('generate_bill');

            // Bed assignment
            Route::patch('/{patient}/assign-bed', [WardController::class, 'assignBed'])->name('assign_bed');

            // Clinical notes
            Route::get('/{patient}/notes/create', fn($patient) => view('patients.notes_create', compact('patient')))->name('notes.create');
            Route::post('/{patient}/notes',       [PatientController::class, 'addNote'])->name('notes.store');

            Route::patch('/{patient}/reassign', [PatientController::class, 'reassign'])->name('reassign');

            // Prescriptions
            Route::get('/{patient}/prescriptions/create', [PatientController::class, 'createPrescription'])->name('prescriptions.create');
            Route::post('/{patient}/prescriptions',       [PatientController::class, 'addPrescription'])->name('prescriptions.store');

            // Lab requests (nested under patient)
            Route::prefix('/{patient}/lab')->name('lab.')->group(function () {
                Route::get('/create', [LabController::class, 'create'])->name('create');
                Route::post('/',      [LabController::class, 'store'])->name('store');
            });
        });

        // ── Ward Overview ────────────────────────────────────────────
        Route::prefix('wards')->name('wards.')->group(function () {
            Route::get('/',                                [WardController::class, 'index'])->name('index');
            Route::get('/{ward}',                          [WardController::class, 'show'])->name('show');
            Route::patch('/beds/{bed}/maintenance',         [WardController::class, 'setMaintenance'])->name('beds.maintenance');
            Route::patch('/beds/{bed}/clear-maintenance',   [WardController::class, 'clearMaintenance'])->name('beds.clear_maintenance');
        });
    });

    // ── Receptionist routes ─────────────────────────────────────────────
    Route::middleware('role:receptionist')->group(function () {

        Route::get('/receptionist/dashboard', [ReceptionistController::class, 'dashboard'])
            ->name('receptionist.dashboard');

        Route::prefix('receptionist/patients')->name('receptionist.patients.')->group(function () {
            Route::get('/create',     [ReceptionistController::class, 'create'])->name('create');
            Route::post('/',          [ReceptionistController::class, 'store'])->name('store');
            Route::get('/',           [ReceptionistController::class, 'index'])->name('index');
            Route::get('/admissions', [ReceptionistController::class, 'admission'])->name('admission');
        });
    });

    // ── Pharmacy routes ────────────────────────────────────────────────
    Route::middleware('role:pharmacist,admin')
        ->prefix('pharmacy')
        ->name('pharmacy.')
        ->group(function () {
            Route::get('/',                                      [PharmacyController::class, 'index'])->name('index');
            Route::get('/prescriptions',                         [PharmacyController::class, 'prescriptions'])->name('prescriptions');
            Route::put('/prescriptions/{prescription}/dispense', [PharmacyController::class, 'dispense'])->name('dispense');
            Route::get('/create',                                [PharmacyController::class, 'create'])->name('create');
            Route::post('/',                                     [PharmacyController::class, 'store'])->name('store');
            Route::get('/{stockItem}/edit',                      [PharmacyController::class, 'edit'])->name('edit');
            Route::put('/{stockItem}',                           [PharmacyController::class, 'update'])->name('update');
            Route::get('/{stockItem}/restock',                   [PharmacyController::class, 'showRestock'])->name('restock');
            Route::post('/{stockItem}/restock',                  [PharmacyController::class, 'restock'])->name('restock.submit');
        });

    // ── Admin routes ───────────────────────────────────────────────────
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard',         [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/users',             [AdminController::class, 'users'])->name('users');
            Route::post('/users',            [AdminController::class, 'createUser'])->name('users.store');
            Route::put('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
        });

    // ── Lab routes (lab attendant + doctor + nurse) ────────────────────
    Route::middleware('role:lab_attendant,doctor,nurse')
        ->prefix('lab')
        ->name('lab.')
        ->group(function () {
            Route::get('/dashboard',                       [LabController::class, 'index'])->name('dashboard');
            Route::get('/requests',                        [LabController::class, 'requests'])->name('requests');
            Route::get('/requests/{labRequest}',           [LabController::class, 'show'])->name('show');
            Route::patch('/requests/{labRequest}/start',   [LabController::class, 'startTest'])->name('start');
            Route::patch('/requests/{labRequest}/results', [LabController::class, 'submitResults'])->name('results');
        });

    // ── Billing / Accountant routes ────────────────────────────────────
    Route::middleware('role:accountant,admin')
        ->prefix('billing')
        ->name('billing.')
        ->group(function () {
            Route::get('/',                 [BillingController::class, 'index'])->name('index');
            Route::get('/{bill}',           [BillingController::class, 'show'])->name('show');
            Route::patch('/{bill}/process', [BillingController::class, 'process'])->name('process');
            Route::patch('/{bill}/waive',   [BillingController::class, 'waive'])->name('waive');
        });

    // ── Mortuary routes ────────────────────────────────────────────────
    // Role fixed: mortuary_attendant (was 'mortuary' — must match the registered role value)
    Route::middleware('role:mortuary_attendant,admin')
        ->prefix('mortuary')
        ->name('mortuary.')
        ->group(function () {
            Route::get('/',                   [MortuaryController::class, 'index'])->name('index');
            Route::get('/{record}',           [MortuaryController::class, 'show'])->name('show');
            Route::patch('/{record}/receive', [MortuaryController::class, 'receive'])->name('receive');
            Route::patch('/{record}/release', [MortuaryController::class, 'release'])->name('release');
        });
});
