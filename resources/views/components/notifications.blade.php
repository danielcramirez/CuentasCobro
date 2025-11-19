{{-- Componente de Notificaciones --}}
@php
    $user = Auth::user();
    $unreadNotifications = $user->unreadNotifications()->take(5)->get();
    $unreadCount = $user->unreadNotifications()->count();
@endphp

<div class="relative" x-data="{ open: false }">
    {{-- Botón de notificaciones --}}
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Panel desplegable de notificaciones --}}
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 md:w-96 bg-white rounded-xl shadow-2xl z-50 max-h-[32rem] overflow-hidden"
         style="display: none;">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-bell mr-2 text-blue-600"></i>
                Notificaciones
                @if($unreadCount > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs bg-blue-600 text-white rounded-full">{{ $unreadCount }}</span>
                @endif
            </h3>
            @if($unreadCount > 0)
                <button onclick="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Marcar todas como leídas
                </button>
            @endif
        </div>

        {{-- Lista de notificaciones --}}
        <div class="overflow-y-auto max-h-96">
            @forelse($unreadNotifications as $notification)
                <div class="px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 cursor-pointer"
                     onclick="markAsRead({{ $notification->id }}, '{{ $notification->cuenta_cobro_id ? route('cuentas-cobro.ver', $notification->cuenta_cobro_id) : '#' }}')">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ 
                                $notification->tipo === 'nueva_cuenta' ? 'bg-blue-100' :
                                ($notification->tipo === 'requiere_aprobacion' ? 'bg-yellow-100' :
                                ($notification->tipo === 'aprobada' ? 'bg-green-100' : 'bg-red-100'))
                            }}">
                                <i class="fas {{ 
                                    $notification->tipo === 'nueva_cuenta' ? 'fa-file-invoice text-blue-600' :
                                    ($notification->tipo === 'requiere_aprobacion' ? 'fa-clock text-yellow-600' :
                                    ($notification->tipo === 'aprobada' ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600'))
                                }}"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $notification->titulo }}</p>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $notification->mensaje }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">No tienes notificaciones nuevas</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($unreadNotifications->isNotEmpty())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('notifications.index') }}" class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Ver todas las notificaciones
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Marcar una notificación como leída y redirigir
    function markAsRead(notificationId, redirectUrl) {
        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && redirectUrl !== '#') {
                window.location.href = redirectUrl;
            } else if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Marcar todas como leídas
    function markAllAsRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Actualizar contador de notificaciones cada 30 segundos
    setInterval(function() {
        fetch('/notifications/unread')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }, 30000);
</script>
@endpush
