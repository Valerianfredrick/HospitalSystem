<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — {{ config('app.name', 'MediCore HMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd',
                            400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9',
                            800: '#5b21b6', 900: '#4c1d95',
                        },
                        secondary: {
                            50: '#edfafa', 100: '#d5f5f6', 200: '#afecef', 300: '#7edce2',
                            400: '#16bdca', 500: '#0694a2', 600: '#047481', 700: '#036672',
                            800: '#05505c', 900: '#014451',
                        },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .text-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .blob { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        .glass-card { background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.6); }
        .noise-bg { background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); background-repeat: repeat; background-size: 200px 200px; opacity: 0.04; }
        .btn-primary { position: relative; overflow: hidden; z-index: 1; transition: all 0.3s ease; }
        .btn-primary::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: all 0.6s ease; z-index: -1; }
        .btn-primary:hover::before { left: 100%; }
        .role-option input[type="radio"] { display: none; }
        .role-option label {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 6px; padding: 0.75rem 0.5rem;
            border: 1.5px solid #e5e7eb; border-radius: 0.75rem;
            text-align: center; cursor: pointer;
            font-size: 0.78rem; font-weight: 500; color: #6b7280;
            transition: all 0.2s ease;
        }
        .role-option label i { font-size: 1.1rem; }
        .role-option input[type="radio"]:checked + label {
            border-color: #7c3aed;
            background: linear-gradient(135deg, rgba(109,40,217,0.08) 0%, rgba(6,148,162,0.08) 100%);
            color: #6d28d9;
        }
        .role-option label:hover { border-color: #c4b5fd; color: #6d28d9; background: #f5f3ff; }
        .strength-segment { height: 3px; flex: 1; border-radius: 9999px; background: #e5e7eb; transition: background 0.3s; }
        .strength-segment.weak   { background: #ef4444; }
        .strength-segment.fair   { background: #f59e0b; }
        .strength-segment.good   { background: #4ade80; }
        .strength-segment.strong { background: #16a34a; }
        .form-input:focus { border-color: #a78bfa; box-shadow: 0 0 0 3px rgba(167,139,250,0.2); outline: none; }
        .form-input { transition: border-color 0.15s, box-shadow 0.15s; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans relative overflow-x-hidden">

<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 left-10 w-64 h-64 bg-primary-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-pulse-slow"></div>
    <div class="absolute top-40 right-10 w-72 h-72 bg-secondary-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-pulse-slow" style="animation-delay:1s"></div>
    <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-pulse-slow" style="animation-delay:2s"></div>
    <div class="noise-bg absolute inset-0"></div>
</div>

<nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-gradient">{{ config('app.name', 'MediCore HMS') }}</a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Already have an account?</span>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="px-4 py-2 text-primary-600 font-medium border border-primary-600 rounded-full hover:bg-primary-50 transition-colors text-sm">Log in</a>
                @endif
            </div>
        </div>
    </div>
</nav>

<div class="relative z-10 min-h-screen flex items-center justify-center px-4 pt-24 pb-12">
    <div class="w-full max-w-lg">
        <div class="relative">
            <div class="absolute -top-8 -left-8 w-16 h-16 bg-secondary-100 blob animate-float shadow-md flex items-center justify-center pointer-events-none">
                <i class="fas fa-user-plus text-secondary-600"></i>
            </div>
            <div class="absolute -top-6 -right-6 w-14 h-14 bg-primary-100 blob animate-float shadow-md flex items-center justify-center pointer-events-none" style="animation-delay:1.5s">
                <i class="fas fa-hospital text-primary-600 text-sm"></i>
            </div>

            <div class="glass-card rounded-3xl shadow-2xl overflow-hidden">
                <div class="bg-gradient p-6 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 rounded-2xl mb-3">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Create an account</h1>
                    <p class="text-white/70 text-sm mt-1">Join {{ config('app.name', 'MediCore HMS') }} and start managing patients</p>
                </div>

                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                            <div class="flex items-center gap-2 font-medium mb-1">
                                <i class="fas fa-exclamation-circle"></i> Please fix the following:
                            </div>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        {{--
                            ROLE SELECTOR
                            Admin is intentionally excluded — admin accounts must be
                            created directly in the database or via a seeder.
                        --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">I am a:</label>

                            {{-- Row 1: Doctor, Nurse, Receptionist --}}
                            <div class="grid grid-cols-3 gap-3 mb-3">
                                @foreach([
                                    ['val' => 'doctor',       'label' => 'Doctor',       'icon' => 'fa-user-md'],
                                    ['val' => 'nurse',        'label' => 'Nurse',        'icon' => 'fa-user-nurse'],
                                    ['val' => 'receptionist', 'label' => 'Receptionist', 'icon' => 'fa-bell-concierge'],
                                ] as $r)
                                    <div class="role-option">
                                        <input type="radio" id="role_{{ $r['val'] }}" name="role" value="{{ $r['val'] }}"
                                               data-needs-specialty="{{ $r['val'] === 'doctor' ? '1' : '0' }}"
                                            {{ old('role', 'doctor') === $r['val'] ? 'checked' : '' }}>
                                        <label for="role_{{ $r['val'] }}">
                                            <i class="fas {{ $r['icon'] }}"></i>
                                            {{ $r['label'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Row 2: Pharmacist, Lab Attendant, Accountant --}}
                            <div class="grid grid-cols-3 gap-3 mb-3">
                                @foreach([
                                    ['val' => 'pharmacist',     'label' => 'Pharmacist',     'icon' => 'fa-pills'],
                                    ['val' => 'lab_attendant',  'label' => 'Lab Attendant',  'icon' => 'fa-flask'],
                                    ['val' => 'accountant',     'label' => 'Accountant',     'icon' => 'fa-calculator'],
                                ] as $r)
                                    <div class="role-option">
                                        <input type="radio" id="role_{{ $r['val'] }}" name="role" value="{{ $r['val'] }}"
                                               data-needs-specialty="0"
                                            {{ old('role') === $r['val'] ? 'checked' : '' }}>
                                        <label for="role_{{ $r['val'] }}">
                                            <i class="fas {{ $r['icon'] }}"></i>
                                            {{ $r['label'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Row 3: Mortuary Attendant (alone, matching grid) --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div class="role-option">
                                    <input type="radio" id="role_mortuary_attendant" name="role" value="mortuary_attendant"
                                           data-needs-specialty="0"
                                        {{ old('role') === 'mortuary_attendant' ? 'checked' : '' }}>
                                    <label for="role_mortuary_attendant">
                                        <i class="fas fa-bed"></i>
                                        Mortuary Attendant
                                    </label>
                                </div>
                            </div>

                            @error('role')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{--
                            SPECIALTY SELECTOR (doctors only)
                            Shown/hidden by JS based on the role selected above.
                            Determines which patients get auto-routed to this doctor.
                        --}}
                        <div id="specialty-wrapper" class="{{ old('role', 'doctor') === 'doctor' ? '' : 'hidden' }}">
                            <label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">
                                Medical specialty
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-stethoscope"></i></span>
                                <select id="specialty" name="specialty"
                                        class="form-input w-full pl-9 pr-4 py-2.5 border {{ $errors->has('specialty') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm bg-white/70">
                                    @foreach (\App\Services\TriageService::labels() as $value => $label)
                                        <option value="{{ $value }}" {{ old('specialty', 'general') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">
                                Patients are automatically routed to a doctor with a matching specialty.
                            </p>
                            @error('specialty')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-xs text-gray-400 font-medium">PERSONAL INFO</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-user"></i></span>
                                <input id="name" type="text" name="name"
                                       value="{{ old('name') }}" required autocomplete="name"
                                       placeholder="Jane Smith"
                                       class="form-input w-full pl-9 pr-4 py-2.5 border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm bg-white/70">
                            </div>
                            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" name="email"
                                       value="{{ old('email') }}" required autocomplete="username"
                                       placeholder="you@hospital.com"
                                       class="form-input w-full pl-9 pr-4 py-2.5 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm bg-white/70">
                            </div>
                            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-xs text-gray-400 font-medium">SECURITY</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" name="password"
                                       required autocomplete="new-password"
                                       placeholder="Min. 8 characters"
                                       oninput="updateStrength(this.value)"
                                       class="form-input w-full pl-9 pr-10 py-2.5 border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm bg-white/70">
                                <button type="button" onclick="togglePwd('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="flex gap-1 mt-2">
                                <div class="strength-segment" id="seg1"></div>
                                <div class="strength-segment" id="seg2"></div>
                                <div class="strength-segment" id="seg3"></div>
                                <div class="strength-segment" id="seg4"></div>
                            </div>
                            <span class="text-xs text-gray-400 mt-1 block" id="strength-label"></span>
                            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-lock"></i></span>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                       required autocomplete="new-password"
                                       placeholder="••••••••"
                                       class="form-input w-full pl-9 pr-10 py-2.5 border {{ $errors->has('password_confirmation') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm bg-white/70">
                                <button type="button" onclick="togglePwd('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit"
                                class="btn-primary w-full py-3 bg-gradient text-white font-semibold rounded-xl shadow-lg hover:opacity-90 transition-all transform hover:scale-[1.01] text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus"></i> Create account
                        </button>
                    </form>

                    @if (Route::has('login'))
                        <p class="text-center text-sm text-gray-500 mt-6">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700 underline underline-offset-2">Log in</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateStrength(val) {
        const segs = ['seg1','seg2','seg3','seg4'].map(id => document.getElementById(id));
        const label = document.getElementById('strength-label');
        let score = 0;
        if (val.length >= 8)          score++;
        if (/[A-Z]/.test(val))        score++;
        if (/[0-9]/.test(val))        score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const classes = ['','weak','fair','good','strong'];
        const labels  = ['','Weak','Fair','Good','Strong'];
        segs.forEach((s, i) => {
            s.className = 'strength-segment';
            if (i < score && val.length > 0) s.classList.add(classes[score]);
        });
        label.textContent = val.length > 0 ? labels[score] : '';
    }
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.querySelector('i').className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
    }

    // Show the specialty dropdown only when "Doctor" is selected.
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const specialtyWrapper = document.getElementById('specialty-wrapper');

    function syncSpecialtyVisibility() {
        const checked = document.querySelector('input[name="role"]:checked');
        const needsSpecialty = checked && checked.dataset.needsSpecialty === '1';
        specialtyWrapper.classList.toggle('hidden', !needsSpecialty);
    }

    roleRadios.forEach(r => r.addEventListener('change', syncSpecialtyVisibility));
    syncSpecialtyVisibility();
</script>
</body>
</html>
