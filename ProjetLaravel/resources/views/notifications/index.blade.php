@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Mes notifications</h1>
            
            @if($notifications->where('lu', false)->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                    Tout marquer comme lu
                </button>
            </form>
            @endif
        </div>

        <div class="space-y-4">
            @forelse($notifications as $notification)
            <div class="bg-white shadow-md rounded-lg p-4 {{ !$notification->lu ? 'border-l-4 border-indigo-500' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $notification->message }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($notification->date_envoi)->diffForHumans() }}
                        </p>
                    </div>
                    
                    <div class="ml-4 flex-shrink-0 flex items-center space-x-4">
                        @if(!$notification->lu)
                        <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-900">
                                Marquer comme lu
                            </button>
                        </form>
                        @endif
                        
                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>

                @if($notification->cours)
                <div class="mt-2 text-sm">
                    <a href="{{ route('cours.show', $notification->cours) }}" class="text-indigo-600 hover:text-indigo-900">
                        Voir le cours associé
                    </a>
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <p class="text-gray-500">Aucune notification</p>
            </div>
            @endforelse

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 