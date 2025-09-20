@props([
    'type' => 'text',
    'name',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
    'id' => null,
    'class' => '',
    'labelClass' => '',
    'inputClass' => '',
    'containerClass' => '',
    'helpText' => null,
    'showError' => true
])

@php
    $inputId = $id ?? $name;
    $hasError = $errors->has($name);
    $inputValue = old($name, $value);
    
    // Base classes for the input
    $baseInputClasses = 'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 sm:text-sm transition-colors duration-200';
    
    // Error state classes
    $errorClasses = $hasError 
        ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' 
        : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500';
    
    // Disabled state classes
    $disabledClasses = $disabled 
        ? 'bg-gray-50 text-gray-500 cursor-not-allowed' 
        : 'bg-white';
    
    // Combine all classes
    $finalInputClasses = $baseInputClasses . ' ' . $errorClasses . ' ' . $disabledClasses . ' ' . $inputClass;
    
    // Label classes
    $baseLabelClasses = 'block text-sm font-medium mb-1';
    $labelErrorClasses = $hasError ? 'text-red-700' : 'text-gray-700';
    $finalLabelClasses = $baseLabelClasses . ' ' . $labelErrorClasses . ' ' . $labelClass;
@endphp

<div class="space-y-1 {{ $containerClass }}">
    {{-- Label --}}
    @if($label)
        <label for="{{ $inputId }}" class="{{ $finalLabelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-1" aria-label="Campo obligatorio">*</span>
            @endif
        </label>
    @endif

    {{-- Input Field --}}
    <div class="relative">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="{{ $finalInputClasses }} {{ $class }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($inputValue !== null) value="{{ $inputValue }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
            @if($helpText && !$hasError) aria-describedby="{{ $inputId }}-help" @endif
            {{ $attributes }}
        >
        
        {{-- Error Icon --}}
        @if($hasError)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Help Text --}}
    @if($helpText && !$hasError)
        <p id="{{ $inputId }}-help" class="text-sm text-gray-500">
            {{ $helpText }}
        </p>
    @endif

    {{-- Error Message --}}
    @if($showError && $hasError)
        <div id="{{ $inputId }}-error" class="flex items-start space-x-1">
            <svg class="h-4 w-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-red-600">
                {{ $errors->first($name) }}
            </p>
        </div>
    @endif
</div>