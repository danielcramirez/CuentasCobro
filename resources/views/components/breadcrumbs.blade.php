@props(['items' => []])

<nav class="mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-sm text-gray-600">
        @foreach($items as $index => $item)
            @if($index > 0)
                <li>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                </li>
            @endif
            
            <li class="flex items-center">
                @if(isset($item['route']) && $index < count($items) - 1)
                    <a href="{{ route($item['route']) }}" 
                       class="hover:text-blue-600 transition-colors duration-200 flex items-center">
                        @if($index === 0)
                            <i class="fas fa-home mr-1"></i>
                        @endif
                        {{ $item['name'] }}
                    </a>
                @else
                    <span class="text-gray-800 font-medium flex items-center">
                        @if($index === 0 && !isset($item['route']))
                            <i class="fas fa-home mr-1"></i>
                        @endif
                        {{ $item['name'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
