@props([
    'title' => config('app.name', 'Laravel'),
    'showAuth' => true,
    'showNavigation' => true,
    'user' => null,
    'class' => ''
])

@php
    $currentUser = $user ?? Auth::user();
    $isAuthenticated = Auth::check();
@endphp

<header class="bg-white shadow-sm border-b border-gray-200 {{ $class }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo/Brand --}}
            <div class="flex items-center">
                <a href="{{ $isAuthenticated ? route('dashboard') : '/' }}" class="flex items-center space-x-2">
                    {{-- Logo Icon --}}
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    
                    {{-- Brand Name --}}
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-semibold text-gray-900">{{ $title }}</h1>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            @if($showNavigation && $isAuthenticated)
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                        Dashboard
                    </a>
                    
                    {{-- Additional navigation items can be added here --}}
                    {{ $slot }}
                </nav>
            @endif

            {{-- Auth Section --}}
            @if($showAuth)
                <div class="flex items-center space-x-4">
                    @if($isAuthenticated && $currentUser)
                        {{-- User Menu --}}
                        <div class="relative" x-data="{ open: false }">
                            {{-- User Button --}}
                            <button 
                                @click="open = !open"
                                @click.away="open = false"
                                class="flex items-center space-x-2 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 p-2 hover:bg-gray-50 transition-colors duration-200"
                                aria-expanded="false"
                                aria-haspopup="true"
                            >
                                {{-- User Avatar --}}
                                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ strtoupper(substr($currentUser->nombre ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                
                                {{-- User Name (hidden on mobile) --}}
                                <span class="hidden md:block text-gray-700 font-medium">
                                    {{ $currentUser->nombre ?? 'Usuario' }}
                                </span>
                                
                                {{-- Dropdown Arrow --}}
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div 
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                role="menu"
                                aria-orientation="vertical"
                            >
                                {{-- User Info --}}
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ $currentUser->nombre ?? 'Usuario' }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $currentUser->email ?? '' }}</p>
                                </div>
                                
                                {{-- Menu Items --}}
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" role="menuitem">
                                    <div class="flex items-center space-x-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Mi Perfil</span>
                                    </div>
                                </a>
                                
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" role="menuitem">
                                    <div class="flex items-center space-x-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Configuración</span>
                                    </div>
                                </a>
                                
                                <div class="border-t border-gray-100 my-1"></div>
                                
                                {{-- Logout --}}
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors duration-200" role="menuitem">
                                        <div class="flex items-center space-x-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Cerrar Sesión</span>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Guest Links --}}
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" 
                               class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                                Registrarse
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Mobile Menu Button --}}
            @if($showNavigation && $isAuthenticated)
                <div class="md:hidden">
                    <button 
                        type="button" 
                        class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 p-2 rounded-md"
                        aria-controls="mobile-menu" 
                        aria-expanded="false"
                        x-data="{ open: false }"
                        @click="open = !open"
                    >
                        <span class="sr-only">Abrir menú principal</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Mobile Menu --}}
    @if($showNavigation && $isAuthenticated)
        <div class="md:hidden" id="mobile-menu" x-data="{ open: false }" x-show="open" x-transition>
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" 
                   class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                    Dashboard
                </a>
            </div>
        </div>
    @endif
</header>