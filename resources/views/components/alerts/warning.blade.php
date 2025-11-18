{{-- Componente de alerta de advertencia --}}
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg slide-up">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">{{ $message }}</p>
        </div>
    </div>
</div>
