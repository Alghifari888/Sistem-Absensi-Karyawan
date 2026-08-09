<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tilt.js -->
    <script src="https://cdn.jsdelivr.net/npm/vanilla-tilt@1.7.2/dist/vanilla-tilt.min.js"></script>

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .moving-gradient {
            background: linear-gradient(-45deg, #e0f2fe, #f1f5f9, #dbeafe, #f8fafc);
            background-size: 400% 400%;
            animation: moveBackground 15s ease infinite;
        }

        @keyframes moveBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        }

        .glass-dark {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(12px);
            color: white;
        }

        .glass-tilt:hover {
            box-shadow: 0 12px 40px rgba(100, 116, 139, 0.25);
        }
    </style>
</head>
<body class="antialiased text-gray-900 moving-gradient relative">
    
    <!-- Tombol Kembali (Floating Top Left) -->
    <a href="/" class="absolute top-4 left-4 sm:top-6 sm:left-6 flex items-center px-4 py-2 bg-white/60 backdrop-blur-md border border-white/40 shadow-sm rounded-full text-sm font-semibold text-gray-700 hover:bg-white/80 hover:shadow-md transition-all duration-300 z-50">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali
    </a>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-16 sm:pt-0">
        <div class="w-full sm:max-w-4xl mt-6 rounded-2xl overflow-hidden sm:grid sm:grid-cols-2"
             data-tilt data-tilt-max="10" data-tilt-speed="600" data-tilt-glare="true" data-tilt-max-glare="0.2">

            <!-- Kolom Kiri: Branding -->
            <div class="hidden sm:flex flex-col justify-center items-center p-10 glass-dark">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 h-24 rounded-full object-cover mb-6 border-4 border-gray-700">
                </a>
                <h2 class="text-3xl font-bold mb-2">{{ config('app.name', 'Laravel') }}</h2>
                <p class="text-sm text-gray-300 text-center">Selamat datang kembali <br>Silakan masuk untuk melanjutkan.</p>
            </div>

            <!-- Kolom Kanan: Form -->
            <div class="w-full px-6 py-12 sm:p-12 glass-card">
                <!-- Mobile Logo -->
                <div class="sm:hidden text-center mb-6">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 h-20 rounded-full object-cover mx-auto">
                    </a>
                    <h2 class="text-2xl font-bold mt-4">{{ config('app.name', 'Laravel') }}</h2>
                </div>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">Login ke Akun Anda</h3>
                <p class="text-sm text-gray-600 mb-6">Masukkan email dan password Anda.</p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password dengan Toggle Icon Mata -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative">
                            <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required autocomplete="current-password" />
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                <!-- Icon Mata Terbuka -->
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <!-- Icon Mata Tertutup -->
                                <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                        </label>
                    </div>

                    <!-- Tombol Login -->
                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button class="w-full justify-center">
                            {{ __('Log In') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi Vanilla Tilt
        VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
            max: 10,
            speed: 600,
            glare: true,
            "max-glare": 0.2
        });

        // Logika Toggle Password Visibility
        document.addEventListener('DOMContentLoaded', function () {
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            if (togglePasswordBtn) {
                togglePasswordBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.type === 'password';
                    
                    passwordInput.type = isPassword ? 'text' : 'password';
                    
                    if (isPassword) {
                        eyeIcon.classList.add('hidden');
                        eyeSlashIcon.classList.remove('hidden');
                    } else {
                        eyeIcon.classList.remove('hidden');
                        eyeSlashIcon.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>