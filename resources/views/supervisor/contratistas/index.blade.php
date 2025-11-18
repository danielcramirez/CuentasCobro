@extends('layouts.dashboard')

@section('title', 'Contratistas - Supervisor')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard Supervisor', 'url' => route('supervisor.dashboard')],
            ['label' => 'Contratistas']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Contratistas</h1>
                    <p class="text-gray-600">Gestión y supervisión de contratistas activos</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-800">{{ count($contratistas) }}</div>
                    <div class="text-sm text-gray-600">Contratistas Activos</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Filtros y búsqueda -->
    <div class="glass-card p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Lista de Contratistas</h2>
            
            <form method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Buscar por nombre o email..."
                           class="w-full sm:w-80 px-4 py-2 pl-10 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-medium transition-all">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrar
                </button>
                @if(request('search'))
                    <a href="{{ route('supervisor.contratistas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl font-medium transition-all">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Lista de contratistas -->
    @if(count($contratistas) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($contratistas as $contratista)
                <div class="glass-card p-6 hover:shadow-lg transition-all duration-300">
                    <!-- Header del contratista -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $contratista->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $contratista->email }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-circle text-green-500 text-xs mr-1"></i>
                            Activo
                        </span>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-xl font-bold text-blue-600">{{ $contratista->total_cuentas }}</div>
                            <div class="text-xs text-gray-600">Total Cuentas</div>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <div class="text-xl font-bold text-orange-600">{{ $contratista->pendientes }}</div>
                            <div class="text-xs text-gray-600">Pendientes</div>
                        </div>
                    </div>

                    <!-- Valor total y aprobadas -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Cuentas Aprobadas</span>
                            <span class="text-sm font-semibold text-green-600">{{ $contratista->aprobadas }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Valor Total</span>
                            <span class="text-sm font-bold text-gray-800">${{ number_format($contratista->valor_total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Última actividad -->
                    @if($contratista->ultima_actividad)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Última actividad</div>
                            <div class="text-sm text-gray-800">
                                {{ \Carbon\Carbon::parse($contratista->ultima_actividad)->format('d/m/Y') }}
                                <span class="text-gray-500">
                                    ({{ \Carbon\Carbon::parse($contratista->ultima_actividad)->diffForHumans() }})
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="flex space-x-2">
                        <a href="{{ route('supervisor.contratistas.show', $contratista->id) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-all text-sm">
                            <i class="fas fa-eye mr-2"></i>
                            Ver Detalles
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Resumen estadístico -->
        <div class="mt-8 glass-card p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Resumen General</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <div class="text-2xl font-bold text-blue-600">{{ count($contratistas) }}</div>
                    <div class="text-sm text-gray-600">Contratistas Totales</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="text-2xl font-bold text-green-600">{{ $contratistas->sum('total_cuentas') }}</div>
                    <div class="text-sm text-gray-600">Cuentas Generadas</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-xl">
                    <div class="text-2xl font-bold text-orange-600">{{ $contratistas->sum('pendientes') }}</div>
                    <div class="text-sm text-gray-600">Cuentas Pendientes</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-xl">
                    <div class="text-2xl font-bold text-purple-600">${{ number_format($contratistas->sum('valor_total'), 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-600">Valor Total</div>
                </div>
            </div>
        </div>

    @else
        <!-- Estado vacío -->
        <div class="glass-card p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay contratistas</h3>
                <p class="text-gray-600 mb-4">
                    @if(request('search'))
                        No se encontraron contratistas que coincidan con tu búsqueda "{{ request('search') }}".
                    @else
                        Aún no hay contratistas registrados en el sistema.
                    @endif
                </p>
                @if(request('search'))
                    <a href="{{ route('supervisor.contratistas.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Ver todos los contratistas
                    </a>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
// Auto-submit form on search input with debounce
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) {
                this.form.submit();
            }
        }, 500);
    });
}
</script>
@endpush
