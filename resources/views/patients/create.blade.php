<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Patient — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9' },
                        secondary: { 500:'#0694a2',600:'#047481' },
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e1035; position: fixed; left: 0; top: 0; z-index: 50; display: flex; flex-direction: column; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 10px; color: rgba(255,255,255,0.55); font-size: 0.875rem; font-weight: 500; text-decoration: none; margin-bottom: 2px; transition: all 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: white; }
        .nav-item.active { background: linear-gradient(135deg, #7c3aed 0%, #047481 100%); color: white; }
        .nav-item .icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.08); font-size: 0.8rem; }
        .nav-section-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; color: rgba(255,255,255,0.3); padding: 0.5rem 0.75rem; margin-top: 0.5rem; text-transform: uppercase; }
        .bg-gradient-main { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .input-field { width: 100%; px: 1rem; padding: 0.65rem 1rem; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.875rem; color: #374151; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
        .input-field:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div style="padding: 1.5rem 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.07);">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-main flex items-center justify-center">
                <i class="fas fa-heartbeat text-white text-sm"></i>
            </div>
            <span class="text-white font-bold text-lg">{{ config('app.name', 'MediCore') }}</span>
        </a>
        <p class="text-xs mt-1 ml-10" style="color:rgba(255,255,255,0.3)">Medical Dashboard</p>
    </div>
    <nav style="flex:1; padding: 1.25rem 0.75rem; overflow-y:auto;">
        <p class="nav-section-label">Main</p>
        <a href="{{ route('medical.dashboard') }}" class="nav-item"><span class="icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
        <a href="{{ route('patients.index') }}" class="nav-item"><span class="icon"><i class="fas fa-user-injured"></i></span> All Patients</a>
        <a href="{{ route('patients.admission') }}" class="nav-item"><span class="icon"><i class="fas fa-bed"></i></span> Admissions</a>
        <a href="{{ route('patients.discharge') }}" class="nav-item"><span class="icon"><i class="fas fa-sign-out-alt"></i></span> Discharges</a>
        <p class="nav-section-label">Clinical</p>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-notes-medical"></i></span> Clinical Notes</a>
        <a href="#" class="nav-item"><span class="icon"><i class="fas fa-file-prescription"></i></span> Prescriptions</a>
        <p class="nav-section-label">Ward</p>
        <a href="{{ route('patients.create') }}" class="nav-item active"><span class="icon"><i class="fas fa-user-plus"></i></span> Register Patient</a>
    </nav>
    <div style="padding: 1rem 0.75rem; border-top: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3" style="padding:0.75rem; border-radius:10px; background:rgba(255,255,255,0.05);">
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs truncate capitalize" style="color:rgba(255,255,255,0.4)">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm" style="color:rgba(255,255,255,0.4)" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="main-content">
    <!-- Topbar -->
    <header style="background:white; border-bottom:1px solid #ede9fe; padding: 0 2rem; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40;">
        <div>
            <h1 class="font-bold text-gray-800">Register New Patient</h1>
            <p class="text-xs text-gray-400">Fill in the patient details below</p>
        </div>
        <a href="{{ route('medical.dashboard') }}" class="text-sm text-primary-600 hover:text-primary-700 flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> Back to Dashboard
        </a>
    </header>

    <main class="p-6">
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle mr-1"></i> Please fix the following:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('patients.store') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Personal Information -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-primary-100 p-6">
                    <h2 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <i class="fas fa-user text-primary-500"></i> Personal Information
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Agnes Mwangi"
                                   class="input-field" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="input-field" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Gender <span class="text-red-500">*</span></label>
                            <select name="gender" class="input-field" required>
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender')==='male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender')==='female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender')==='other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+255 700 000 000"
                                   class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                   placeholder="e.g. John Mwangi" class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Emergency Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                   placeholder="+255 700 000 000" class="input-field">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g. Kariakoo, Dar es Salaam"
                                   class="input-field">
                        </div>
                    </div>
                </div>

                <!-- Admission Details -->
                <div class="bg-white rounded-2xl border border-primary-100 p-6">
                    <h2 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <i class="fas fa-bed text-primary-500"></i> Admission Details
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Ward</label>
                            <select name="ward" class="input-field">
                                <option value="">Select ward</option>
                                @foreach(['General','ICU','Pediatric','Maternity','Surgical'] as $ward)
                                    <option value="{{ $ward }}" {{ old('ward')===$ward ? 'selected' : '' }}>{{ $ward }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Admission Status</label>
                            <select name="status" class="input-field">
                                <option value="admitted" {{ old('status')==='admitted' ? 'selected' : '' }}>Admitted</option>
                                <option value="observation" {{ old('status')==='observation' ? 'selected' : '' }}>Observation</option>
                                <option value="critical" {{ old('status')==='critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Diagnosis <span class="text-red-500">*</span></label>
                            <textarea name="diagnosis" rows="4" placeholder="Primary diagnosis or presenting complaint..."
                                      class="input-field" style="resize:none;" required>{{ old('diagnosis') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit -->
            <div class="mt-6 flex items-center gap-4">
                <button type="submit"
                        class="px-8 py-3 rounded-xl text-white font-semibold bg-gradient-main hover:opacity-90 transition-opacity flex items-center gap-2">
                    <i class="fas fa-user-plus"></i> Register Patient
                </button>
                <a href="{{ route('medical.dashboard') }}"
                   class="px-8 py-3 rounded-xl text-gray-600 font-semibold bg-gray-100 hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </main>
</div>

</body>
</html>
