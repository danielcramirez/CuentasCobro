@extends('layouts.dashboard')

@section('title', 'Cuentas de Cobro - Ordenador de Gasto')

@section('breadcrumb')
    @include('components.navigation.breadcrumb', [
        'items' => [
            ['label' => 'Dashboard', 'route' => 'ordenador-gasto.dashboard'],
            ['label' => 'Cuentas de Cobro']
        ]
    ])
@endsection

@section('dashboard-header')
    <div class="glass-card p-6 slide-up">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="gradient-secondary w-16 h-16 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-signature text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Cuentas de Cobro</h1>
                    <p class="text-gray-600">Gestión de aprobaciones finales</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('dashboard-content')
    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="glass-card p-4">
            <div class="text-sm text-gray-600 mb-1">Total</div>
            <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
        </div>
        <div class="glass-card p-4">
            <div class="text-sm text-gray-600 mb-1">Pendientes</div>
            <div class="text-2xl font-bold text-orange-600">{{ $pendientes }}</div>
        </div>
        <div class="glass-card p-4">
            <div class="text-sm text-gray-600 mb-1">Aprobadas</div>
            <div class="text-2xl font-bold text-green-600">{{ $aprobadas }}</div>
        </div>
        <div class="glass-card p-4">
            <div class="text-sm text-gray-600 mb-1">Valor Pendiente</div>
            <div class="text-xl font-bold text-blue-600">${{ number_format($valor_pendiente, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="glass-card p-6 mb-6">
        <form method="GET" action="{{ route('ordenador-gasto.cuentas') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Contratista, proyecto..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="pendiente_supervisor" {{ request('estado') == 'pendiente_supervisor' ? 'selected' : '' }}>Pendiente Supervisor</option>
                        <option value="pendiente_contratacion" {{ request('estado') == 'pendiente_contratacion' ? 'selected' : '' }}>Pendiente Contratación</option>
                        <option value="pendiente_tesoreria" {{ request('estado') == 'pendiente_tesoreria' ? 'selected' : '' }}>Pendiente Tesorería</option>
                        <option value="pendiente_ordenador" {{ request('estado') == 'pendiente_ordenador' ? 'selected' : '' }}>Pendiente Ordenador</option>
                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
                    <input type="month" 
                           name="mes" 
                           value="{{ request('mes') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
                <a href="{{ route('ordenador-gasto.cuentas') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times mr-2"></i>Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de cuentas -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contratista</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cuentas as $cuenta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">#{{ $cuenta->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $cuenta->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $cuenta->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ Str::limit($cuenta->proyecto_servicio, 40) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">${{ number_format($cuenta->valor, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $cuenta->fecha_emision ? \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClass = match($cuenta->estado) {
                                        'borrador' => 'bg-gray-100 text-gray-800',
                                        'pendiente_supervisor' => 'bg-yellow-100 text-yellow-800',
                                        'pendiente_contratacion' => 'bg-blue-100 text-blue-800',
                                        'pendiente_tesoreria' => 'bg-purple-100 text-purple-800',
                                        'pendiente_ordenador' => 'bg-orange-100 text-orange-800',
                                        'aprobada' => 'bg-green-100 text-green-800',
                                        'rechazada' => 'bg-red-100 text-red-800',
                                        'pagada' => 'bg-indigo-100 text-indigo-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $estadoTexto = match($cuenta->estado) {
                                        'borrador' => 'Borrador',
                                        'pendiente_supervisor' => 'Pend. Supervisor',
                                        'pendiente_contratacion' => 'Pend. Contratación',
                                        'pendiente_tesoreria' => 'Pend. Tesorería',
                                        'pendiente_ordenador' => 'Pend. Ordenador',
                                        'aprobada' => 'Aprobada',
                                        'rechazada' => 'Rechazada',
                                        'pagada' => 'Pagada',
                                        default => ucfirst($cuenta->estado)
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $estadoTexto }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('ordenador-gasto.show', $cuenta->id) }}" 
                                       class="text-blue-600 hover:text-blue-800" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($cuenta->archivo_adjunto)
                                        <a href="{{ Storage::url($cuenta->archivo_adjunto) }}" 
                                           target="_blank" 
                                           class="text-green-600 hover:text-green-800" 
                                           title="Descargar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No se encontraron cuentas de cobro</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cuentas->hasPages())
            <div class="px-6 py-4 bg-gray-50">
                {{ $cuentas->links() }}
            </div>
        @endif
    </div>
@endsection
