{{-- Componente de breadcrumb reutilizable --}}
<nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors flex items-center">
        <i class="fas fa-home mr-1"></i>
        Inicio
    </a>
    
    @if(isset($items) && count($items) > 0)
        @foreach($items as $item)
            <i class="fas fa-chevron-right text-gray-300"></i>
            @if(isset($item['url']) && !$loop->last)
                <a href="{{ $item['url'] }}" class="hover:text-gray-700 transition-colors">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="text-gray-700 font-medium">{{ $item['label'] }}</span>
            @endif
        @endforeach
    @endif
</nav>
