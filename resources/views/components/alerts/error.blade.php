{{-- Componente de alerta de error --}}
<div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg slide-up">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-700">{{ $message }}</p>
        </div>
    </div>
</div>
