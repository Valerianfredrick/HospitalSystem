<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — {{ config('app.name', 'MediCore HMS') }}</title>
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
        .form-input:focus { border-color: #a78bfa; box-shadow: 0 0 0 3px rgba(167,139,250,0.2); outline: none; }
        .form-input { transition: border-color 0.15s, box-shadow 0.15s; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans relative overflow-x-hidden">

<!-- Background blobs -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 left-10 w-64 h-64 bg-primary-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-pulse-slow"></div>
    <div class="absolute top-40 right-10 w-72 h-72 bg-secondary-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-pulse-slow" style="animation-delay:1s"></div>
    <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-pulse-slow" style="animation-delay:2s"></div>
    <div class="noise-bg absolute inset-0"></div>
</div>

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-gradient">{{ config('app.name', 'MediCore HMS') }}</a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Don't have an account?</span>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2 text-primary-600 font-medium border border-primary-600 rounded-full hover:bg-primary-50 transition-colors text-sm">Register</a>
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- Page body -->
<div class="relative z-10 min-h-screen flex items-center justify-center px-4 pt-24 pb-12">
    <div class="w-full max-w-md">

        <div class="relative">
            <!-- Floating blobs near card -->
            <div class="absolute -top-8 -left-8 w-16 h-16 bg-secondary-100 blob animate-float shadow-md flex items-center justify-center pointer-events-none">
                <i class="fas fa-hospital text-secondary-600"></i>
            </div>
            <div class="absolute -top-6 -right-6 w-14 h-14 bg-primary-100 blob animate-float shadow-md flex items-center justify-center pointer-events-none" style="animation-delay:1.5s">
                <i class="fas fa-lock text-primary-600 text-sm"></i>
            </div>

            <!-- Glass card -->
            <div class="glass-card rounded-3xl shadow-2xl overflow-hidden">

                <!-- Card top gradient bar -->
                <div class="bg-gradient p-6 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 rounded-2xl mb-3">
                        <i class="fas fa-sign-in-alt text-white text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Welcome back</h1>
                    <p class="text-white/70 text-sm mt-1">Log in to your {{ config('app.name', 'MediCore HMS') }} account</p>
                </div>

                <div class="p-8">

                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                            <div class="flex items-center gap-2 font-medium mb-1"><i class="fas fa-exclamation-circle"></i> Please fix the following:</div>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" name="email"
                                       value="{{ old('email') }}" required autofocus autocomplete="username"
                                       placeholder="you@hospital.com"
                                       class="form-input w-full pl-9 pr-4 py-2.5 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm bg-white/70">
                            </div>
                            @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" name="password"
                                       required autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="form-input w-full pl-9 pr-10 py-2.5 border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm bg-white/70">
                                <button type="button" onclick="togglePwd('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember + Forgot --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                                class="rounded accent-primary-600">
                                Remember me
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-sm text-primary-600 hover:text-primary-700 underline underline-offset-2 font-medium">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="btn-primary w-full py-3 bg-gradient text-white font-semibold rounded-xl shadow-lg hover:opacity-90 transition-all transform hover:scale-[1.01] text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i> Log in
                        </button>
                    </form>

                    {{-- Footer --}}
                    @if (Route::has('register'))
                        <p class="text-center text-sm text-gray-500 mt-6">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:text-primary-700 underline underline-offset-2">Register</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.querySelector('i').className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
</script>
</body>
</html>
