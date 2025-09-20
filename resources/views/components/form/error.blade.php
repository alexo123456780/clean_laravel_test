@props([
    'field' => null,
    'message' => null,
    'class' => '',
    'showIcon' => true,
    'id' => null
])

@php
    // Determine the error message to display
    $errorMessage = null;
    
    if ($message) {
        // Use provided message
        $errorMessage = $message;
    } elseif ($field && $errors->has($field)) {
        // Use field-specific error from validation
        $errorMessage = $errors->first($field);
    } elseif ($errors->has('general')) {
        // Use general error
        $errorMessage = $errors->first('general');
    }
    
    // Base classes
    $baseClasses = 'flex items-start space-x-2 text-sm text-red-600';
    $finalClasses = trim($baseClasses . ' ' . $class);
    
    // Generate ID if not provided
    $errorId = $id ?? ($field ? $field . '-error' : 'error-message');
@endphp

@if($errorMessage)
    <div 
        id="{{ $errorId }}" 
        class="{{ $finalClasses }}"
        role="alert"
        aria-live="polite"
        {{ $attributes }}
    >
        @if($showIcon)
            <svg class="h-4 w-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        @endif
        
        <div class="flex-1">
            <p>{{ $errorMessage }}</p>
            
            {{-- Additional content slot --}}
            @if($slot->isNotEmpty())
                <div class="mt-1">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
@endif