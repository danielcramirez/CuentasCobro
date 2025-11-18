@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('contratista.dashboard') }}" class="text-gray-600 hover:text-blue-600 inline-flex items-center">
                        <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('contratista.cuentas.index') }}" class="ml-1 text-gray-600 hover:text-blue-600 md:ml-2">Cuentas de Cobro</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2" aria-current="page">Eliminar Cuenta</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <span class="gradient-primary bg-clip-text text-transparent">
                    Eliminar Cuenta de Cobro
                </span>
            </h1>
            <p class="text-gray-600">
                Esta acción no se puede deshacer. Revisa cuidadosamente antes de continuar.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Información de la cuenta -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Detalles de la cuenta -->
                <div class="glass-card bg-white/70 backdrop-blur-sm border border-white/20 rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Información de la Cuenta</h2>
                        <div class="px-3 py-1 rounded-full text-sm font-medium
                            @if($cuenta->estado === 'pendiente') bg-yellow-100 text-yellow-800
                            @elseif($cuenta->estado === 'aprobado') bg-green-100 text-green-800
                            @elseif($cuenta->estado === 'rechazado') bg-red-100 text-red-800
                            @elseif($cuenta->estado === 'pagado') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($cuenta->estado) }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Cuenta</label>
                            <div class="text-base font-semibold text-gray-900">#{{ str_pad($cuenta->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                            <div class="text-base text-gray-900">{{ $cuenta->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total</label>
                            <div class="text-lg font-bold text-green-600">${{ number_format($cuenta->valor, 0, ',', '.') }}</div>
                        </div>                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proyecto/Servicio</label>
                            <div class="text-base text-gray-900">{{ $cuenta->proyecto_servicio }}</div>
                        </div>

                        @if($cuenta->descripcion)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <div class="text-base text-gray-900 p-3 bg-gray-50 rounded-lg">{{ $cuenta->descripcion }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Advertencia -->
                <div class="glass-card bg-red-50/70 backdrop-blur-sm border border-red-200/50 rounded-xl p-6 shadow-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-red-800 mb-2">¿Estás seguro?</h3>
                            <div class="text-red-700 space-y-2">
                                <p><strong>Esta acción eliminará permanentemente:</strong></p>
                                <ul class="list-disc ml-5 space-y-1">
                                    <li>La cuenta de cobro #{{ str_pad($cuenta->id, 6, '0', STR_PAD_LEFT) }}</li>
                                    <li>Todos los archivos adjuntos asociados</li>
                                    <li>El historial de cambios de estado</li>
                                    <li>Los comentarios y observaciones</li>
                                </ul>
                                <p class="font-medium mt-3">Una vez eliminada, esta información no se puede recuperar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar de acciones -->
            <div class="space-y-6">
                <!-- Información del contratista -->
                <div class="glass-card bg-white/70 backdrop-blur-sm border border-white/20 rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Contratista</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <div class="text-base text-gray-900">{{ $cuenta->user->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="text-base text-gray-900">{{ $cuenta->user->email }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <div class="text-base text-gray-900">{{ $cuenta->user->telefono ?? 'No especificado' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="glass-card bg-white/70 backdrop-blur-sm border border-white/20 rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Impacto de la Eliminación</h3>
                    <div class="space-y-3">                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Archivos adjuntos</span>
                            <span class="font-medium text-gray-900">{{ $cuenta->ruta_archivo ? 1 : 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Estado actual</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($cuenta->estado) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Días desde creación</span>
                            <span class="font-medium text-gray-900">{{ $cuenta->created_at->diffInDays(now()) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="glass-card bg-white/70 backdrop-blur-sm border border-white/20 rounded-xl p-6 shadow-lg">
                    <div class="space-y-4">
                        <!-- Formulario de eliminación -->
                        <form method="POST" action="{{ route('contratista.cuentas.destroy', $cuenta->id) }}" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete()" 
                                class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-lg font-medium hover:from-red-700 hover:to-red-800 focus:ring-4 focus:ring-red-300 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Sí, Eliminar Cuenta
                            </button>
                        </form>

                        <!-- Botón cancelar -->
                        <a href="{{ route('contratista.cuentas.ver', $cuenta->id) }}" 
                           class="block w-full text-center bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 transition-all duration-200">
                            Cancelar
                        </a>

                        <!-- Link alternativo -->
                        <div class="text-center">
                            <a href="{{ route('contratista.cuentas.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                                ← Volver a la lista de cuentas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 para confirmación -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete() {
    Swal.fire({
        title: '¿Estás completamente seguro?',
        html: `
            <div class="text-left">
                <p class="mb-3">Esta acción eliminará <strong>permanentemente</strong>:</p>
                <ul class="text-sm text-gray-600 mb-4">
                    <li>• La cuenta de cobro #{{ str_pad($cuenta->id, 6, '0', STR_PAD_LEFT) }}</li>
                    <li>• Todos los archivos adjuntos</li>
                    <li>• El historial completo</li>
                    <li>• Comentarios y observaciones</li>
                </ul>
                <p class="text-red-600 font-medium">Esta acción no se puede deshacer.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'No, cancelar',
        focusCancel: true,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera mientras se elimina la cuenta',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit del formulario
            document.getElementById('deleteForm').submit();
        }
    });
}

// Confirmar antes de salir si hay cambios
window.addEventListener('beforeunload', function (e) {
    // Solo mostrar si no estamos en proceso de eliminación
    if (!document.getElementById('deleteForm').submitted) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>

<style>
.gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.glass-card {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.glass-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endsection
