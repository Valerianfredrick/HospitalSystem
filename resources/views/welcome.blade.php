<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MediCore HMS') }} - Hospital Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
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
                        'float-slow': 'float 6s ease-in-out infinite',
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
        html { scroll-behavior: smooth; scroll-padding-top: 80px; }
        body { font-family: 'Inter', sans-serif; }
        .bg-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); }
        .text-gradient { background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .blob { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        .blob-2 { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
        .blob-3 { border-radius: 40% 60% 70% 30% / 40% 40% 60% 50%; }
        .glass-effect { background: rgba(255,255,255,0.25); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.18); }
        .noise-bg { background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); background-repeat: repeat; background-size: 200px 200px; opacity: 0.05; }
        .marquee { overflow: hidden; white-space: nowrap; }
        .marquee-content { display: inline-block; animation: marquee 20s linear infinite; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .scroll-indicator { animation: scroll-down 2s ease-in-out infinite; }
        @keyframes scroll-down { 0%, 100% { transform: translateY(0); opacity: 0.8; } 50% { transform: translateY(10px); opacity: 0.4; } }
        .nav-link { position: relative; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: -4px; left: 0; background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); transition: width 0.3s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .feature-card { transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .testimonial-card { transition: all 0.3s ease; }
        .testimonial-card:hover { transform: scale(1.03); }
        .btn-primary { position: relative; overflow: hidden; z-index: 1; transition: all 0.3s ease; }
        .btn-primary::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: all 0.6s ease; z-index: -1; }
        .btn-primary:hover::before { left: 100%; }
        .scroll-to-top { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; visibility: hidden; transition: all 0.3s ease; z-index: 100; }
        .scroll-to-top.visible { opacity: 1; visibility: visible; }
        .preloader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; display: flex; align-items: center; justify-content: center; z-index: 9999; transition: opacity 0.5s ease, visibility 0.5s ease; }
        .preloader.hidden { opacity: 0; visibility: hidden; }
        .loader { width: 80px; height: 80px; border-radius: 50%; border: 4px solid #f3f3f3; border-top: 4px solid #6d28d9; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .mobile-menu { position: fixed; top: 0; right: -100%; width: 80%; max-width: 300px; height: 100%; background: white; z-index: 1000; transition: right 0.3s ease; box-shadow: -5px 0 15px rgba(0,0,0,0.1); overflow-y: auto; }
        .mobile-menu.open { right: 0; }
        .mobile-menu-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .mobile-menu-overlay.open { opacity: 1; visibility: visible; }
        .typing-animation::after { content: '|'; animation: blink 1s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

        /* Status badges */
        .status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
        .status-admitted { background: #d1fae5; color: #065f46; }
        .status-observation { background: #fef3c7; color: #92400e; }
        .status-critical { background: #fee2e2; color: #991b1b; }
        .status-discharged { background: #f3f4f6; color: #374151; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        .dot-green { background: #10b981; }
        .dot-amber { background: #f59e0b; }
        .dot-red { background: #ef4444; }
        .dot-gray { background: #9ca3af; }

        /* Patient table */
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table th { background: #f5f3ff; color: #6d28d9; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 1rem; text-align: left; }
        .patient-table td { padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.85rem; }
        .patient-table tr:last-child td { border-bottom: none; }
        .patient-table tr:hover td { background: #faf9ff; }
        .avatar-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; flex-shrink: 0; }

        /* Journey step connector */
        .step-connector { position: relative; }
        .step-connector::after { content: ''; position: absolute; top: 2rem; right: -1rem; width: 2rem; height: 2px; background: linear-gradient(135deg, #6d28d9 0%, #0694a2 100%); z-index: 1; }

        /* Vitals card */
        .vital-card { background: linear-gradient(135deg, rgba(109,40,217,0.05) 0%, rgba(6,148,162,0.05) 100%); border: 1px solid rgba(109,40,217,0.15); }
        .vital-val-warn { color: #d97706; font-weight: 700; }
        .vital-val-crit { color: #dc2626; font-weight: 700; }
        .vital-val-ok { color: #059669; font-weight: 700; }
    </style>
</head>
<body class="font-sans bg-gray-50 text-gray-900">

<!-- Preloader -->
<div class="preloader">
    <div class="loader"></div>
</div>

<!-- Scroll to Top -->
<div class="scroll-to-top" id="scrollToTop">
    <i class="fas fa-arrow-up"></i>
</div>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="p-6">
        <div class="flex justify-between items-center mb-8">
            <span class="text-2xl font-bold text-gradient">{{ config('app.name', 'MediCore HMS') }}</span>
            <button id="closeMenu" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav>
            <ul class="space-y-4">
                <li><a href="#patients" class="block py-2 text-gray-700 hover:text-primary-600 transition-colors mobile-nav-link">Patient Management</a></li>
                <li><a href="#journey" class="block py-2 text-gray-700 hover:text-primary-600 transition-colors mobile-nav-link">Patient Journey</a></li>
                <li><a href="#modules" class="block py-2 text-gray-700 hover:text-primary-600 transition-colors mobile-nav-link">Modules</a></li>
                <li><a href="#roles" class="block py-2 text-gray-700 hover:text-primary-600 transition-colors mobile-nav-link">Staff Roles</a></li>
                <li><a href="#contact" class="block py-2 text-gray-700 hover:text-primary-600 transition-colors mobile-nav-link">Contact</a></li>
            </ul>
        </nav>
        <div class="mt-8 space-y-4">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="block w-full px-4 py-2 text-primary-600 font-medium border border-primary-600 rounded-full text-center hover:bg-primary-50 transition-colors">Login</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block w-full px-4 py-2 bg-primary-600 text-white font-medium rounded-full text-center hover:bg-primary-700 transition-colors">Register</a>
            @endif
        </div>
    </div>
</div>

<!-- Hero Section -->
<section id="hero" class="relative overflow-hidden min-h-screen flex items-center">
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-20 left-10 w-64 h-64 bg-primary-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse-slow"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-secondary-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse-slow" style="animation-delay:1s"></div>
        <div class="absolute -bottom-10 left-1/3 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse-slow" style="animation-delay:2s"></div>
        <div class="noise-bg absolute inset-0"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="navbar">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="{{ url('/') }}" class="text-2xl font-bold text-gradient">{{ config('app.name', 'MediCore HMS') }}</a>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#patients" class="text-gray-700 hover:text-primary-600 font-medium nav-link">Patients</a>
                    <a href="#journey" class="text-gray-700 hover:text-primary-600 font-medium nav-link">Journey</a>
                    <a href="#modules" class="text-gray-700 hover:text-primary-600 font-medium nav-link">Modules</a>
                    <a href="#roles" class="text-gray-700 hover:text-primary-600 font-medium nav-link">Staff Roles</a>
                    <a href="#contact" class="text-gray-700 hover:text-primary-600 font-medium nav-link">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="hidden md:inline-block px-4 py-2 bg-primary-600 text-white font-medium rounded-full hover:bg-primary-700 transition-colors btn-primary">Dashboard</a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="hidden md:inline-block px-4 py-2 text-primary-600 font-medium border border-primary-600 rounded-full hover:bg-primary-50 transition-colors">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden md:inline-block px-4 py-2 bg-primary-600 text-white font-medium rounded-full hover:bg-primary-700 transition-colors btn-primary">Register</a>
                        @endif
                    @endauth
                    <button id="openMenu" class="md:hidden text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Content -->
    <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-right" data-aos-duration="1000">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                    Patient Care,<br><span class="text-gradient typing-animation" id="typingText">Simplified</span>
                </h1>
                <p class="mt-6 text-lg md:text-xl text-gray-600 max-w-lg">
                    A complete Hospital Management System for frontline staff — register patients, track their journey through every ward, and manage discharge, all in one place.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient text-white font-medium rounded-full hover:opacity-90 transition-all text-center shadow-lg transform hover:scale-105 btn-primary">
                            Get Started
                        </a>
                    @endif
                    <a href="#journey" class="px-8 py-4 bg-white text-gray-700 font-medium rounded-full border border-gray-300 hover:bg-gray-50 transition-all text-center flex items-center justify-center transform hover:scale-105">
                        <i class="fas fa-route mr-2 text-primary-600"></i> Patient Journey
                    </a>
                </div>
                <div class="mt-8 flex items-center space-x-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-bold text-primary-600 counter" data-target="1200">0</span>+ hospital staff using MediCore
                    </div>
                </div>
                <div class="mt-12">
                    <p class="text-sm text-gray-500 mb-4">TRUSTED BY LEADING HOSPITALS & CLINICS</p>
                    <div class="marquee">
                        <div class="marquee-content flex space-x-12">
                            @foreach(['City Hospital','St. Mary\'s Clinic','Regional Medical','Unity Health','Central Hospital','City Hospital','St. Mary\'s Clinic','Regional Medical','Unity Health','Central Hospital'] as $partner)
                                <img src="https://placehold.co/120x40/f5f3ff/ddd6fe?text={{ urlencode($partner) }}" alt="{{ $partner }}" class="h-8 grayscale hover:grayscale-0 transition-all duration-300">
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero live patient widget -->
            <div class="relative" data-aos="fade-left" data-aos-duration="1000">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-200 to-secondary-200 rounded-3xl transform rotate-3 scale-105 opacity-30 blur-xl"></div>
                <div class="relative bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                    <!-- Mini ward dashboard -->
                    <div class="bg-gradient p-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-hospital text-white"></i>
                            <span class="text-white font-semibold text-sm">Ward 3B — Live Patient Board</span>
                        </div>
                        <span class="text-white/70 text-xs">{{ now()->format('d M Y') }}</span>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="patient-table">
                            <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Bed</th>
                                <th>Doctor</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach([
                                ['initials'=>'AM','name'=>'Agnes Mwangi','bg'=>'bg-primary-100','text'=>'text-primary-700','bed'=>'Bed 4','doctor'=>'Dr. Omondi','status'=>'admitted'],
                                ['initials'=>'JK','name'=>'James Kimani','bg'=>'bg-secondary-100','text'=>'text-secondary-700','bed'=>'Bed 7','doctor'=>'Dr. Aisha','status'=>'observation'],
                                ['initials'=>'FO','name'=>'Fatuma Omar','bg'=>'bg-red-100','text'=>'text-red-700','bed'=>'ICU-2','doctor'=>'Dr. Chen','status'=>'critical'],
                                ['initials'=>'BN','name'=>'Brian Njoroge','bg'=>'bg-gray-100','text'=>'text-gray-600','bed'=>'Bed 1','doctor'=>'Dr. Omondi','status'=>'discharged'],
                            ] as $p)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar-circle {{ $p['bg'] }} {{ $p['text'] }}">{{ $p['initials'] }}</div>
                                            <span class="font-medium text-gray-800 text-xs">{{ $p['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-gray-500 text-xs">{{ $p['bed'] }}</td>
                                    <td class="text-gray-500 text-xs">{{ $p['doctor'] }}</td>
                                    <td>
                                        @if($p['status'] === 'admitted')
                                            <span class="status-badge status-admitted"><span class="status-dot dot-green"></span>Admitted</span>
                                        @elseif($p['status'] === 'observation')
                                            <span class="status-badge status-observation"><span class="status-dot dot-amber"></span>Observation</span>
                                        @elseif($p['status'] === 'critical')
                                            <span class="status-badge status-critical"><span class="status-dot dot-red"></span>Critical</span>
                                        @else
                                            <span class="status-badge status-discharged"><span class="status-dot dot-gray"></span>Discharged</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-white/80 backdrop-blur-sm p-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-nurse text-primary-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium">Nurse Mary Achieng</p>
                                    <p class="text-xs text-gray-500">On duty — Ward 3B</p>
                                </div>
                            </div>
                            <a href="{{ route('register') }}" class="px-3 py-1 bg-primary-600 text-white text-xs rounded-full transform hover:scale-105 transition-transform">Add Patient</a>
                        </div>
                    </div>
                </div>
                <div class="absolute -top-6 -left-6 w-20 h-20 bg-secondary-100 blob animate-float shadow-lg flex items-center justify-center">
                    <i class="fas fa-heartbeat text-2xl text-secondary-600"></i>
                </div>
                <div class="absolute -bottom-8 -right-8 w-24 h-24 bg-primary-100 blob animate-float shadow-lg flex items-center justify-center" style="animation-delay:1s">
                    <i class="fas fa-procedures text-3xl text-primary-600"></i>
                </div>
                <div class="absolute -bottom-16 -left-8 glass-effect rounded-lg p-4 shadow-lg max-w-xs animate-float" style="animation-delay:2s">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Patient Admitted</p>
                            <p class="text-xs text-gray-600">Agnes Mwangi — Ward 3B, Bed 4</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-center">
            <div class="scroll-indicator text-primary-600"><i class="fas fa-chevron-down"></i></div>
            <p class="text-sm text-gray-500 mt-2">Scroll to explore</p>
        </div>
    </div>
</section>

<!-- Patient Management Section -->
<section id="patients" class="py-20 bg-white relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-primary-100 blob-2 opacity-50"></div>
        <div class="absolute bottom-20 -left-20 w-80 h-80 bg-secondary-100 blob-3 opacity-50"></div>
        <div class="noise-bg absolute inset-0"></div>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-600 text-sm font-medium mb-4">PATIENT MANAGEMENT</span>
            <h2 class="text-3xl md:text-4xl font-bold">Every Patient. Every Detail. One System.</h2>
            <p class="mt-4 text-lg text-gray-600">From the moment a patient walks through the door to the moment they are discharged, MediCore keeps your staff in full control.</p>
        </div>

        <!-- Patient record preview -->
        <div class="mt-16 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden" data-aos="fade-up">
            <!-- Record header -->
            <div class="bg-gradient p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xl">AM</div>
                    <div>
                        <h3 class="text-white font-bold text-lg">Agnes Mwangi</h3>
                        <p class="text-white/70 text-sm">MRN-20240512 &nbsp;·&nbsp; DOB: 14 Mar 1985 (39 yrs) &nbsp;·&nbsp; Female</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-medium"><i class="fas fa-bed mr-1"></i>Ward 3B — Bed 4</span>
                    <span class="px-3 py-1 bg-green-400/30 text-white rounded-full text-xs font-medium"><i class="fas fa-circle mr-1 text-xs"></i>Admitted</span>
                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-medium"><i class="fas fa-user-md mr-1"></i>Dr. Omondi</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <!-- Demographics -->
                <div class="p-6">
                    <h4 class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-4">Demographics</h4>
                    <div class="space-y-3">
                        @foreach([
                            ['icon'=>'fa-id-card','label'=>'NHIF No.','val'=>'NHIF-883-20194'],
                            ['icon'=>'fa-phone','label'=>'Phone','val'=>'+255 712 000 000'],
                            ['icon'=>'fa-map-marker-alt','label'=>'Address','val'=>'Kariakoo, Dar es Salaam'],
                            ['icon'=>'fa-users','label'=>'Next of Kin','val'=>'Peter Mwangi (Husband)'],
                            ['icon'=>'fa-tint','label'=>'Blood Group','val'=>'B+'],
                            ['icon'=>'fa-exclamation-triangle','label'=>'Allergies','val'=>'Penicillin'],
                        ] as $field)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $field['icon'] }} text-primary-500 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">{{ $field['label'] }}</p>
                                    <p class="text-sm font-medium text-gray-800">{{ $field['val'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Vitals -->
                <div class="p-6">
                    <h4 class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-4">Current Vitals</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['label'=>'Blood Pressure','val'=>'142/88','unit'=>'mmHg','status'=>'warn','icon'=>'fa-heart'],
                            ['label'=>'Temperature','val'=>'37.4','unit'=>'°C','status'=>'ok','icon'=>'fa-thermometer-half'],
                            ['label'=>'SpO₂','val'=>'96','unit'=>'%','status'=>'ok','icon'=>'fa-lungs'],
                            ['label'=>'Heart Rate','val'=>'78','unit'=>'bpm','status'=>'ok','icon'=>'fa-heartbeat'],
                            ['label'=>'Weight','val'=>'68','unit'=>'kg','status'=>'ok','icon'=>'fa-weight'],
                            ['label'=>'Glucose','val'=>'7.2','unit'=>'mmol/L','status'=>'warn','icon'=>'fa-vial'],
                        ] as $v)
                            <div class="vital-card rounded-xl p-3">
                                <div class="flex items-center gap-1 mb-1">
                                    <i class="fas {{ $v['icon'] }} text-primary-400 text-xs"></i>
                                    <p class="text-xs text-gray-400">{{ $v['label'] }}</p>
                                </div>
                                <p class="text-lg font-bold vital-val-{{ $v['status'] }}">{{ $v['val'] }} <span class="text-xs font-normal text-gray-400">{{ $v['unit'] }}</span></p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Timeline -->
                <div class="p-6">
                    <h4 class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-4">Care Timeline</h4>
                    <ol class="relative border-l-2 border-primary-100 space-y-4 pl-5">
                        @foreach([
                            ['date'=>'May 10 · 08:42','text'=>'Registered & triaged. Ward 3B, Bed 4 assigned.','done'=>true],
                            ['date'=>'May 10 · 10:15','text'=>'Dr. Omondi: Hypertension diagnosed. BP 158/96.','done'=>true],
                            ['date'=>'May 11 · 09:00','text'=>'Blood panel ordered. Results received 11:30 AM.','done'=>true],
                            ['date'=>'May 12 · 07:30','text'=>'Amlodipine 5mg prescribed. Dispensed by pharmacy.','done'=>true],
                            ['date'=>'May 14 (planned)','text'=>'Discharge — pending BP stabilisation.','done'=>false],
                        ] as $event)
                            <li class="relative">
                                <div class="absolute -left-[1.35rem] top-1 w-3 h-3 rounded-full border-2 {{ $event['done'] ? 'bg-primary-600 border-primary-600' : 'bg-white border-primary-300' }}"></div>
                                <p class="text-xs text-gray-400 mb-0.5">{{ $event['date'] }}</p>
                                <p class="text-xs text-gray-700 leading-relaxed">{{ $event['text'] }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            <!-- Quick actions bar -->
            <div class="bg-gray-50 border-t border-gray-100 p-4 flex flex-wrap gap-3">
                @foreach([
                    ['icon'=>'fa-file-prescription','label'=>'New Prescription','color'=>'primary'],
                    ['icon'=>'fa-flask','label'=>'Order Lab Test','color'=>'secondary'],
                    ['icon'=>'fa-notes-medical','label'=>'Add Nursing Note','color'=>'primary'],
                    ['icon'=>'fa-sign-out-alt','label'=>'Initiate Discharge','color'=>'secondary'],
                    ['icon'=>'fa-print','label'=>'Print Summary','color'=>'primary'],
                ] as $action)
                    <button class="flex items-center gap-2 px-4 py-2 bg-{{ $action['color'] }}-50 text-{{ $action['color'] }}-600 border border-{{ $action['color'] }}-200 rounded-lg text-xs font-medium hover:bg-{{ $action['color'] }}-100 transition-colors">
                        <i class="fas {{ $action['icon'] }}"></i> {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach([
                ['icon'=>'fa-procedures','target'=>50000,'label'=>'Patients Managed'],
                ['icon'=>'fa-bed','target'=>1200,'label'=>'Beds Tracked'],
                ['icon'=>'fa-user-md','target'=>850,'label'=>'Registered Doctors'],
                ['icon'=>'fa-file-medical','target'=>200000,'label'=>'Records Created'],
            ] as $stat)
                <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-xl hover:-translate-y-2 transition-all" data-aos="fade-up">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas {{ $stat['icon'] }} text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 counter" data-target="{{ $stat['target'] }}">0</h3>
                    <p class="text-gray-600 mt-2">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Patient Journey Section -->
<section id="journey" class="py-20 bg-gray-50 relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-40 left-20 w-72 h-72 bg-primary-100 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-pulse-slow"></div>
        <div class="absolute bottom-20 right-20 w-80 h-80 bg-secondary-100 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-pulse-slow" style="animation-delay:1.5s"></div>
        <div class="noise-bg absolute inset-0"></div>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-600 text-sm font-medium mb-4">PATIENT JOURNEY</span>
            <h2 class="text-3xl md:text-4xl font-bold">Arrival to Discharge — Every Step Tracked</h2>
            <p class="mt-4 text-lg text-gray-600">MediCore guides your staff through each phase of the patient's in-hospital stay with no steps missed.</p>
        </div>
        <div class="mt-16 relative">
            <div class="hidden md:block absolute top-1/2 left-0 right-0 h-1 bg-gradient transform -translate-y-1/2 z-0"></div>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 relative z-10">
                @foreach([
                    ['step'=>1,'icon'=>'fa-door-open','title'=>'Registration','desc'=>'Capture full patient demographics, insurance, and triage category. Print wristband ID.'],
                    ['step'=>2,'icon'=>'fa-bed','title'=>'Ward & Bed','desc'=>'Assign patient to ward, bed, and responsible doctor. View live bed availability.'],
                    ['step'=>3,'icon'=>'fa-stethoscope','title'=>'Assessment','desc'=>'Doctor records diagnosis, vitals, and clinical notes — time-stamped and attributed.'],
                    ['step'=>4,'icon'=>'fa-flask','title'=>'Investigations','desc'=>'Order lab tests and imaging. Results auto-attach to the patient record when ready.'],
                    ['step'=>5,'icon'=>'fa-pills','title'=>'Treatment','desc'=>'Issue prescriptions, track medication rounds, and log nursing notes per patient.'],
                    ['step'=>6,'icon'=>'fa-sign-out-alt','title'=>'Discharge','desc'=>'Generate discharge summary, follow-up plan, and billing. Bed freed instantly.'],
                ] as $step)
                    <div class="bg-white rounded-2xl shadow-lg p-5 text-center hover:shadow-xl hover:-translate-y-2 transition-all" data-aos="fade-up" data-aos-delay="{{ $step['step'] * 80 }}">
                        <div class="w-14 h-14 rounded-full bg-primary-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas {{ $step['icon'] }} text-xl text-primary-600"></i>
                        </div>
                        <div class="text-xs font-bold text-primary-400 mb-1">STEP {{ $step['step'] }}</div>
                        <h3 class="text-sm font-bold mb-2">{{ $step['title'] }}</h3>
                        <p class="text-gray-500 text-xs leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mt-16 text-center" data-aos="fade-up">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient text-white font-medium rounded-full hover:opacity-90 transition-opacity inline-block shadow-lg transform hover:scale-105 btn-primary">
                    Start Managing Patients
                </a>
            @endif
        </div>
    </div>
</section>

<!-- Modules Section -->
<section id="modules" class="py-20 bg-white relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-primary-100 blob-2 opacity-50"></div>
        <div class="absolute bottom-20 -left-20 w-80 h-80 bg-secondary-100 blob-3 opacity-50"></div>
        <div class="noise-bg absolute inset-0"></div>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-600 text-sm font-medium mb-4">SYSTEM MODULES</span>
            <h2 class="text-3xl md:text-4xl font-bold">Everything Your Hospital Needs</h2>
            <p class="mt-4 text-lg text-gray-600">Integrated modules that work together — so staff spend less time on paperwork and more time on care.</p>
        </div>
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['color'=>'teal','icon'=>'fa-user-injured','title'=>'Patient Records','items'=>['Full demographic & medical history','Real-time vitals chart & nursing notes','Allergy alerts & drug interaction flags','Searchable patient archive','Printable patient summary sheet']],
                ['color'=>'blue','icon'=>'fa-procedures','title'=>'Ward & Bed Management','items'=>['Live bed occupancy across all wards','One-click patient transfer between wards','Housekeeping workflow on discharge','Bed reservation & scheduling','Ward census & daily reports']],
                ['color'=>'green','icon'=>'fa-file-prescription','title'=>'Prescriptions & Pharmacy','items'=>['Digital prescriptions from doctor to pharmacy','Medication administration records','Drug stock levels & reorder alerts','Controlled drug logging','Prescription audit trail']],
                ['color'=>'purple','icon'=>'fa-flask','title'=>'Laboratory & Radiology','items'=>['Test ordering from within the patient chart','Results auto-attached to patient records','Flagged abnormal results','Imaging upload & viewing','Lab turnaround time tracking']],
                ['color'=>'red','icon'=>'fa-receipt','title'=>'Billing & Insurance','items'=>['Auto-generated itemised invoices','NHIF & insurance pre-authorisation','Payment tracking & receipts','Discharge billing clearance','Revenue reports & summaries']],
                ['color'=>'yellow','icon'=>'fa-chart-bar','title'=>'Reporting & Analytics','items'=>['Daily census & occupancy reports','Disease pattern & outbreak tracking','Staff performance dashboards','Export to PDF or Excel','Ministry of Health report formats']],
            ] as $card)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden feature-card border border-gray-100" data-aos="fade-up">
                    <div class="h-3 bg-{{ $card['color'] }}-500"></div>
                    <div class="p-8">
                        <div class="w-14 h-14 rounded-full bg-{{ $card['color'] }}-100 flex items-center justify-center mb-6">
                            <i class="fas {{ $card['icon'] }} text-2xl text-{{ $card['color'] }}-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-4">{{ $card['title'] }}</h3>
                        <ul class="space-y-3">
                            @foreach($card['items'] as $item)
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-{{ $card['color'] }}-500 mt-1 mr-2"></i>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Staff Roles Section -->
<section id="roles" class="py-20 bg-gray-50 relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-40 right-20 w-72 h-72 bg-primary-100 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-pulse-slow"></div>
        <div class="noise-bg absolute inset-0"></div>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-600 text-sm font-medium mb-4">STAFF ROLES</span>
            <h2 class="text-3xl md:text-4xl font-bold">The Right Access for Every Team Member</h2>
            <p class="mt-4 text-lg text-gray-600">Role-based access ensures each staff member sees only what they need — keeping data secure and workflows efficient.</p>
        </div>
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['img'=>'men/32','name'=>'Admin / Receptionist','role'=>'Hospital Administrator','color'=>'teal','quote'=>'I register every incoming patient, assign them a bed, and make sure all their details are captured correctly from the first minute they arrive.','stars'=>5],
                ['img'=>'women/44','name'=>'Nurse Mary Achieng','role'=>'Ward Nurse — 3B','color'=>'blue','quote'=>'I record vitals, nursing notes, and medication rounds directly in the system. Alerts tell me immediately when a patient\'s readings go out of range.','stars'=>5],
                ['img'=>'men/68','name'=>'Dr. Samuel Omondi','role'=>'General Physician','color'=>'purple','quote'=>'I access the full patient history the moment they come in, issue digital prescriptions, order lab tests, and write my clinical notes — all in one place.','stars'=>5],
                ['img'=>'women/75','name'=>'James Muthoni','role'=>'Pharmacist','color'=>'green','quote'=>'Digital prescriptions arrive in my queue instantly. I dispense, mark as given, and the patient record is updated automatically — no paper, no confusion.','stars'=>5],
                ['img'=>'men/55','name'=>'Lab Tech Amina','role'=>'Laboratory Technician','color'=>'red','quote'=>'Test requests come in from the ward, I process them and enter results directly — they appear in the patient file the moment I save them.','stars'=>5],
                ['img'=>'women/60','name'=>'Billing Clerk Ruth','role'=>'Finance Department','color'=>'yellow','quote'=>'The system auto-generates itemised invoices based on services rendered. Insurance claims and discharge clearance are handled in one step.','stars'=>5],
            ] as $t)
                <div class="bg-white rounded-2xl shadow-lg p-8 relative testimonial-card" data-aos="fade-up">
                    <div class="absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4">
                        <div class="w-16 h-16 rounded-full bg-{{ $t['color'] }}-100 flex items-center justify-center">
                            <i class="fas fa-quote-right text-2xl text-{{ $t['color'] }}-500"></i>
                        </div>
                    </div>
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/{{ $t['img'] }}.jpg" alt="{{ $t['name'] }}" class="w-14 h-14 rounded-full mr-4">
                        <div>
                            <h3 class="font-bold">{{ $t['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $t['role'] }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600">"{{ $t['quote'] }}"</p>
                    <div class="mt-4 flex text-yellow-400">
                        @for($i = 0; $i < $t['stars']; $i++)<i class="fas fa-star"></i>@endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient text-white relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold">Ready to Digitalise Your Hospital?</h2>
            <p class="mt-4 text-lg text-white/80 max-w-2xl mx-auto">Join hospitals and clinics already using {{ config('app.name', 'MediCore HMS') }} to deliver faster, safer, and more accountable patient care.</p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-primary-600 font-medium rounded-full hover:bg-gray-100 transition-colors shadow-lg transform hover:scale-105">
                        Create Your Account
                    </a>
                @endif
                <a href="#contact" class="px-8 py-4 bg-transparent text-white font-medium rounded-full border border-white hover:bg-white/10 transition-colors transform hover:scale-105">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="noise-bg absolute inset-0"></div>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-100 text-primary-600 text-sm font-medium mb-4">CONTACT US</span>
            <h2 class="text-3xl md:text-4xl font-bold">Get in Touch</h2>
            <p class="mt-4 text-lg text-gray-600">Want to deploy MediCore HMS in your hospital? We'll walk you through the setup.</p>
        </div>
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-12">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden" data-aos="fade-right">
                <form class="p-8">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" placeholder="Your name" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" placeholder="Your email" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hospital / Clinic Name</label>
                        <input type="text" name="hospital" placeholder="Your hospital name" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Beds</label>
                        <select name="beds" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                            <option value="">Select bed count</option>
                            <option>1 – 50 beds</option>
                            <option>51 – 200 beds</option>
                            <option>201 – 500 beds</option>
                            <option>500+ beds</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea name="message" rows="4" placeholder="Tell us about your needs" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 px-4 bg-gradient text-white font-medium rounded-lg hover:opacity-90 transition-opacity shadow-md transform hover:scale-105">
                        Send Message
                    </button>
                </form>
            </div>
            <div data-aos="fade-left">
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h3 class="text-xl font-bold mb-4">Contact Information</h3>
                    <div class="space-y-4">
                        @foreach([
                            ['icon'=>'fa-map-marker-alt','label'=>'Address','value'=>'123 Healthcare Ave, Medical District, CA 90210'],
                            ['icon'=>'fa-phone-alt','label'=>'Phone','value'=>'+1 (555) 123-4567'],
                            ['icon'=>'fa-envelope','label'=>'Email','value'=>'support@medicorehms.com'],
                        ] as $info)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center mr-4">
                                    <i class="fas {{ $info['icon'] }} text-primary-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium">{{ $info['label'] }}</p>
                                    <p class="text-gray-600">{{ $info['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold mb-4">Support Hours</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between"><span class="text-gray-600">Monday – Friday</span><span class="font-medium">8:00 AM – 6:00 PM</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Saturday</span><span class="font-medium">9:00 AM – 1:00 PM</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Emergency Support</span><span class="font-medium text-green-600">24 / 7</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-bold text-gradient mb-4">{{ config('app.name', 'MediCore HMS') }}</h3>
                <p class="text-gray-400">A complete hospital management system built for frontline healthcare workers.</p>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    @foreach(['patients'=>'Patient Management','journey'=>'Patient Journey','modules'=>'System Modules','roles'=>'Staff Roles','contact'=>'Contact'] as $anchor => $label)
                        <li><a href="#{{ $anchor }}" class="text-gray-400 hover:text-white transition-colors">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-4">Account</h4>
                <ul class="space-y-2">
                    @if (Route::has('login'))
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors">Login</a></li>
                    @endif
                    @if (Route::has('register'))
                        <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition-colors">Register</a></li>
                    @endif
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-4">Newsletter</h4>
                <p class="text-gray-400 mb-4">Subscribe for updates, clinical tips, and system news.</p>
                <div class="flex">
                    <input type="email" placeholder="Your email" class="flex-1 px-4 py-2 rounded-l-lg focus:outline-none bg-gray-800 border-gray-700 text-white">
                    <button class="px-4 py-2 bg-primary-600 rounded-r-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-gray-800 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'MediCore HMS') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 800, easing: 'ease-out-cubic' });

    // Preloader
    window.addEventListener('load', function() {
        setTimeout(() => document.querySelector('.preloader').classList.add('hidden'), 500);
    });

    // Mobile Menu
    const openMenuBtn = document.getElementById('openMenu');
    const closeMenuBtn = document.getElementById('closeMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    openMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.add('open');
        mobileMenuOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    });

    function closeMenu() {
        mobileMenu.classList.remove('open');
        mobileMenuOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    closeMenuBtn.addEventListener('click', closeMenu);
    mobileMenuOverlay.addEventListener('click', closeMenu);
    document.querySelectorAll('.mobile-nav-link').forEach(l => l.addEventListener('click', closeMenu));

    // Navbar scroll
    const navbar = document.getElementById('navbar');
    const scrollToTopBtn = document.getElementById('scrollToTop');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            navbar.classList.add('bg-white/80', 'backdrop-blur-md', 'shadow-sm');
            scrollToTopBtn.classList.add('visible');
        } else {
            navbar.classList.remove('bg-white/80', 'backdrop-blur-md', 'shadow-sm');
            scrollToTopBtn.classList.remove('visible');
        }
    });

    scrollToTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
        });
    });

    // Counter animation
    const counters = document.querySelectorAll('.counter');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                let count = 0;
                const increment = Math.ceil(target / 200);
                const timer = setInterval(() => {
                    count = Math.min(count + increment, target);
                    counter.innerText = count.toLocaleString();
                    if (count >= target) clearInterval(timer);
                }, 10);
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));

    // Typing animation
    const typingText = document.getElementById('typingText');
    const words = ['Simplified', 'Digitalised', 'Transformed'];
    let wordIndex = 0, charIndex = 0, isDeleting = false;

    function typeEffect() {
        const currentWord = words[wordIndex];
        typingText.textContent = currentWord.substring(0, charIndex);
        typingText.classList.add('typing-animation');

        if (!isDeleting && charIndex < currentWord.length) {
            charIndex++;
            setTimeout(typeEffect, 150);
        } else if (isDeleting && charIndex > 0) {
            charIndex--;
            setTimeout(typeEffect, 100);
        } else {
            isDeleting = !isDeleting;
            typingText.classList.remove('typing-animation');
            if (!isDeleting) wordIndex = (wordIndex + 1) % words.length;
            setTimeout(typeEffect, isDeleting ? 1000 : 500);
        }
    }
    setTimeout(typeEffect, 1000);
</script>
</body>
</html>
