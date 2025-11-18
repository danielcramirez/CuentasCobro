{{-- Componente de tarjeta de estadísticas reutilizable --}}
<div class="glass-card p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-600 mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-800">{{ $value }}</p>
            @if(isset($subtitle))
                <p class="text-sm {{ $subtitleColor ?? 'text-gray-600' }} flex items-center mt-1">
                    @if(isset($icon))
                        <i class="fas fa-{{ $icon }} mr-1"></i>
                    @endif
                    {{ $subtitle }}
                </p>
            @endif
        </div>
        <div class="bg-gradient-to-br {{ $gradientFrom ?? 'from-blue-400' }} {{ $gradientTo ?? 'to-blue-600' }} w-12 h-12 rounded-xl flex items-center justify-center">
            <i class="fas fa-{{ $cardIcon }} text-white"></i>
        </div>
    </div>
</div>
