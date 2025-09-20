{{-- Success Messages --}}
@if(session('success'))
    <x-form.alert 
        type="success" 
        :message="session('success')" 
        dismissible="true"
        class="mb-6"
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
    />
@endif

{{-- Error Messages --}}
@if(session('error'))
    <x-form.alert 
        type="error" 
        :message="session('error')" 
        dismissible="true"
        class="mb-6"
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
    />
@endif

{{-- Warning Messages --}}
@if(session('warning'))
    <x-form.alert 
        type="warning" 
        :message="session('warning')" 
        dismissible="true"
        class="mb-6"
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
    />
@endif

{{-- Info Messages --}}
@if(session('info'))
    <x-form.alert 
        type="info" 
        :message="session('info')" 
        dismissible="true"
        class="mb-6"
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
    />
@endif

{{-- General Validation Errors --}}
@if($errors->has('general'))
    <x-form.alert 
        type="error" 
        :message="$errors->first('general')" 
        dismissible="true"
        class="mb-6"
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
    />
@endif

{{-- Multiple Validation Errors Summary --}}
@if($errors->any() && !$errors->has('general') && count($errors->all()) > 3)
    <x-form.alert 
        type="error" 
        title="Por favor, corrige los siguientes errores:"
        dismissible="true"
        class="mb-6"
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
    >
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-form.alert>
@endif

{{-- Auto-hide script for flash messages --}}
@push('scripts')
<script>
    // Auto-hide flash messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('[x-data*="show: true"]');
        
        flashMessages.forEach(function(message) {
            // Only auto-hide success and info messages
            if (message.classList.contains('bg-green-50') || message.classList.contains('bg-blue-50')) {
                setTimeout(function() {
                    // Trigger Alpine.js hide
                    if (message._x_dataStack && message._x_dataStack[0].show !== undefined) {
                        message._x_dataStack[0].show = false;
                    }
                }, 5000);
            }
        });
    });
</script>
@endpush