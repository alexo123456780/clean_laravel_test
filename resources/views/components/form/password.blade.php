@props([
    'name',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'autocomplete' => 'current-password',
    'id' => null,
    'class' => '',
    'labelClass' => '',
    'inputClass' => '',
    'containerClass' => '',
    'helpText' => null,
    'showError' => true,
    'showToggle' => true,
    'strengthMeter' => false
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
    
    // Adjust padding for toggle button
    $paddingClasses = $showToggle ? 'pr-10' : '';
    
    // Combine all classes
    $finalInputClasses = $baseInputClasses . ' ' . $errorClasses . ' ' . $disabledClasses . ' ' . $paddingClasses . ' ' . $inputClass;
    
    // Label classes
    $baseLabelClasses = 'block text-sm font-medium mb-1';
    $labelErrorClasses = $hasError ? 'text-red-700' : 'text-gray-700';
    $finalLabelClasses = $baseLabelClasses . ' ' . $labelErrorClasses . ' ' . $labelClass;
@endphp

<div class="space-y-1 {{ $containerClass }}" @if($showToggle || $strengthMeter) x-data="passwordField()" @endif>
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
            @if($showToggle || $strengthMeter) x-bind:type="showPassword ? 'text' : 'password'" @else type="password" @endif
            name="{{ $name }}"
            id="{{ $inputId }}"
            class="{{ $finalInputClasses }} {{ $class }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($inputValue !== null) value="{{ $inputValue }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
            @if($helpText && !$hasError) aria-describedby="{{ $inputId }}-help" @endif
            @if($strengthMeter) x-on:input="checkStrength($event.target.value)" @endif
            {{ $attributes }}
        >
        
        {{-- Toggle Password Visibility Button --}}
        @if($showToggle)
            <button
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                tabindex="-1"
            >
                <svg x-show="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                </svg>
            </button>
        @endif
        
        {{-- Error Icon (only if no toggle button) --}}
        @if($hasError && !$showToggle)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Password Strength Meter --}}
    @if($strengthMeter)
        <div x-show="password.length > 0" x-transition class="mt-2">
            <div class="flex items-center space-x-2">
                <div class="flex-1">
                    <div class="bg-gray-200 rounded-full h-2">
                        <div 
                            class="h-2 rounded-full transition-all duration-300"
                            :class="{
                                'bg-red-500 w-1/4': strength === 'weak',
                                'bg-yellow-500 w-2/4': strength === 'medium',
                                'bg-green-500 w-3/4': strength === 'strong',
                                'bg-green-600 w-full': strength === 'very-strong'
                            }"
                        ></div>
                    </div>
                </div>
                <span 
                    class="text-xs font-medium"
                    :class="{
                        'text-red-600': strength === 'weak',
                        'text-yellow-600': strength === 'medium',
                        'text-green-600': strength === 'strong',
                        'text-green-700': strength === 'very-strong'
                    }"
                    x-text="strengthText"
                ></span>
            </div>
            
            {{-- Password Requirements --}}
            <div class="mt-2 text-xs text-gray-600">
                <div class="grid grid-cols-2 gap-1">
                    <div :class="requirements.length ? 'text-green-600' : 'text-gray-400'">
                        <span x-text="requirements.length ? '✓' : '○'"></span> 8+ caracteres
                    </div>
                    <div :class="requirements.uppercase ? 'text-green-600' : 'text-gray-400'">
                        <span x-text="requirements.uppercase ? '✓' : '○'"></span> Mayúscula
                    </div>
                    <div :class="requirements.lowercase ? 'text-green-600' : 'text-gray-400'">
                        <span x-text="requirements.lowercase ? '✓' : '○'"></span> Minúscula
                    </div>
                    <div :class="requirements.number ? 'text-green-600' : 'text-gray-400'">
                        <span x-text="requirements.number ? '✓' : '○'"></span> Número
                    </div>
                </div>
            </div>
        </div>
    @endif

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

@if($showToggle || $strengthMeter)
    @push('scripts')
    <script>
        function passwordField() {
            return {
                showPassword: false,
                password: '',
                strength: 'weak',
                strengthText: 'Débil',
                requirements: {
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    number: false,
                    special: false
                },
                
                checkStrength(password) {
                    this.password = password;
                    
                    // Check requirements
                    this.requirements.length = password.length >= 8;
                    this.requirements.uppercase = /[A-Z]/.test(password);
                    this.requirements.lowercase = /[a-z]/.test(password);
                    this.requirements.number = /\d/.test(password);
                    this.requirements.special = /[!@#$%^&*(),.?":{}|<>]/.test(password);
                    
                    // Calculate strength
                    const score = Object.values(this.requirements).filter(Boolean).length;
                    
                    if (score < 2) {
                        this.strength = 'weak';
                        this.strengthText = 'Débil';
                    } else if (score < 3) {
                        this.strength = 'medium';
                        this.strengthText = 'Media';
                    } else if (score < 4) {
                        this.strength = 'strong';
                        this.strengthText = 'Fuerte';
                    } else {
                        this.strength = 'very-strong';
                        this.strengthText = 'Muy fuerte';
                    }
                }
            }
        }
    </script>
    @endpush
@endif