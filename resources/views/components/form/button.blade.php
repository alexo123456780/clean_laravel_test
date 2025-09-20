@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Cargando...',
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'target' => null,
    'class' => '',
    'fullWidth' => false
])

@php
    // Base button classes
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed';
    
    // Size variants
    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm leading-4',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-4 py-2 text-base',
        'xl' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm'
    };
    
    // Color variants
    $variantClasses = match($variant) {
        'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border border-transparent',
        'secondary' => 'bg-white hover:bg-gray-50 focus:ring-blue-500 text-gray-700 border border-gray-300',
        'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white border border-transparent',
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white border border-transparent',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white border border-transparent',
        'info' => 'bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500 text-white border border-transparent',
        'light' => 'bg-gray-100 hover:bg-gray-200 focus:ring-gray-500 text-gray-800 border border-gray-300',
        'dark' => 'bg-gray-800 hover:bg-gray-900 focus:ring-gray-500 text-white border border-transparent',
        'outline-primary' => 'bg-transparent hover:bg-blue-50 focus:ring-blue-500 text-blue-600 border border-blue-600',
        'outline-secondary' => 'bg-transparent hover:bg-gray-50 focus:ring-gray-500 text-gray-600 border border-gray-600',
        'ghost' => 'bg-transparent hover:bg-gray-100 focus:ring-gray-500 text-gray-600 border border-transparent',
        default => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white border border-transparent'
    };
    
    // Full width
    $widthClasses = $fullWidth ? 'w-full' : '';
    
    // Disabled state
    $disabledState = $disabled || $loading;
    
    // Combine all classes
    $finalClasses = trim($baseClasses . ' ' . $sizeClasses . ' ' . $variantClasses . ' ' . $widthClasses . ' ' . $class);
    
    // Icon classes
    $iconSizeClasses = match($size) {
        'xs' => 'h-3 w-3',
        'sm' => 'h-4 w-4',
        'md' => 'h-4 w-4',
        'lg' => 'h-5 w-5',
        'xl' => 'h-5 w-5',
        default => 'h-4 w-4'
    };
@endphp

@if($href)
    {{-- Render as link --}}
    <a 
        href="{{ $href }}" 
        @if($target) target="{{ $target }}" @endif
        class="{{ $finalClasses }}"
        @if($disabledState) 
            aria-disabled="true" 
            tabindex="-1"
            onclick="return false;"
        @endif
        {{ $attributes->except(['href', 'target']) }}
    >
        @if($loading)
            {{-- Loading spinner --}}
            <svg class="animate-spin -ml-1 mr-2 {{ $iconSizeClasses }} text-current" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ $loadingText }}
        @else
            {{-- Icon (left) --}}
            @if($icon && $iconPosition === 'left')
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="{{ $iconSizeClasses }} mr-2" />
            @endif
            
            {{-- Button content --}}
            {{ $slot }}
            
            {{-- Icon (right) --}}
            @if($icon && $iconPosition === 'right')
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="{{ $iconSizeClasses }} ml-2" />
            @endif
        @endif
    </a>
@else
    {{-- Render as button --}}
    <button 
        type="{{ $type }}"
        class="{{ $finalClasses }}"
        @if($disabledState) disabled @endif
        {{ $attributes }}
    >
        @if($loading)
            {{-- Loading spinner --}}
            <svg class="animate-spin -ml-1 mr-2 {{ $iconSizeClasses }} text-current" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ $loadingText }}
        @else
            {{-- Icon (left) --}}
            @if($icon && $iconPosition === 'left')
                <svg class="{{ $iconSizeClasses }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @switch($icon)
                        @case('login')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            @break
                        @case('user-plus')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            @break
                        @case('logout')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            @break
                        @case('home')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            @break
                        @case('user')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            @break
                        @case('cog')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            @break
                        @default
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    @endswitch
                </svg>
            @endif
            
            {{-- Button content --}}
            {{ $slot }}
            
            {{-- Icon (right) --}}
            @if($icon && $iconPosition === 'right')
                <svg class="{{ $iconSizeClasses }} ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @switch($icon)
                        @case('arrow-right')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            @break
                        @case('external-link')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            @break
                        @default
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    @endswitch
                </svg>
            @endif
        @endif
    </button>
@endif