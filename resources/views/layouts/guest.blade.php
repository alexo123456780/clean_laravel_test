<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <x-layout.header 
            :showAuth="false" 
            :showNavigation="false"
            class="bg-white shadow-sm"
        />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <!-- Logo/Brand -->
                <div class="flex justify-center mb-6">
                    <a href="/" class="flex items-center space-x-2">
                        <svg class="h-10 w-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xl font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>

                <!-- Flash Messages -->
                @include('partials.flash-messages')

                <!-- Page Content -->
                {{ $slot }}
            </div>

            <!-- Additional Links -->
            <div class="mt-6 text-center">
                <div class="flex items-center justify-center space-x-4 text-sm text-gray-600">
                    @if(request()->routeIs('login'))
                        <span>¿No tienes cuenta?</span>
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200">
                            Regístrate aquí
                        </a>
                    @elseif(request()->routeIs('register'))
                        <span>¿Ya tienes cuenta?</span>
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200">
                            Inicia sesión
                        </a>
                    @endif
                </div>
            </div>
        </main>

        <!-- Footer -->
        <x-layout.footer 
            :showLinks="false" 
            class="mt-8"
        />
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>