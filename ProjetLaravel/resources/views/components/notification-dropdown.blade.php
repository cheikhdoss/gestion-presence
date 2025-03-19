@props(['notifications'])

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
        <span class="sr-only">Notifications</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($notifications->where('lu', false)->count() > 0)
        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false"
        class="absolute right-0 mt-2 w-80 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5"
        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
        
        <div class="px-4 py-2 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                <a href="{{ route('notifications.index') }}" class="text-xs text-indigo-600 hover:text-indigo-900">
                    Voir tout
                </a>
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications->take(5) as $notification)
            <div class="px-4 py-2 hover:bg-gray-50 {{ !$notification->lu ? 'bg-indigo-50' : '' }}">
                <p class="text-sm text-gray-900">{{ $notification->message }}</p>
                <p class="mt-1 text-xs text-gray-500">
                    {{ \Carbon\Carbon::parse($notification->date_envoi)->diffForHumans() }}
                </p>
            </div>
            @empty
            <div class="px-4 py-2 text-sm text-gray-500 text-center">
                Aucune notification
            </div>
            @endforelse
        </div>

        <div class="px-4 py-2 border-t border-gray-100">
            <a href="{{ route('notifications.preferences') }}" class="text-xs text-gray-500 hover:text-gray-700">
                Gérer les préférences de notification
            </a>
        </div>
    </div>
</div> 