@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Détails de l'utilisateur</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Informations détaillées sur l'utilisateur.
                    </p>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <!-- Informations de base -->
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <h4 class="text-sm font-medium text-gray-500">Nom complet</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->nom }} {{ $user->prenom }}</p>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <h4 class="text-sm font-medium text-gray-500">Adresse email</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <h4 class="text-sm font-medium text-gray-500">Rôle</h4>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                           ($user->role === 'professeur' ? 'bg-green-100 text-green-800' : 
                                            'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <h4 class="text-sm font-medium text-gray-500">Date d'inscription</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Informations spécifiques au rôle -->
                        @if($user->role === 'professeur')
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Cours enseignés</h4>
                                @if($user->coursEnseignes->count() > 0)
                                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                                        <ul class="divide-y divide-gray-200">
                                            @foreach($user->coursEnseignes as $cours)
                                                <li class="px-4 py-3">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">{{ $cours->matiere }}</p>
                                                            <p class="text-sm text-gray-500">{{ $cours->date_heure->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                        <a href="{{ route('cours.show', $cours) }}" class="text-sm text-blue-600 hover:text-blue-900">
                                                            Voir le cours
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Aucun cours enseigné pour le moment.</p>
                                @endif
                            </div>
                        @endif

                        @if($user->role === 'etudiant')
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Cours suivis</h4>
                                @if($user->coursSuivis->count() > 0)
                                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                                        <ul class="divide-y divide-gray-200">
                                            @foreach($user->coursSuivis as $cours)
                                                <li class="px-4 py-3">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">{{ $cours->matiere }}</p>
                                                            <p class="text-sm text-gray-500">{{ $cours->date_heure->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                        <div class="flex items-center space-x-4">
                                                            @php
                                                                $emargement = $cours->emargements->where('user_id', $user->id)->first();
                                                            @endphp
                                                            @if($emargement)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                    {{ $emargement->isPresent() ? 'bg-green-100 text-green-800' : 
                                                                       ($emargement->isRetard() ? 'bg-yellow-100 text-yellow-800' : 
                                                                        'bg-red-100 text-red-800') }}">
                                                                    {{ $emargement->isPresent() ? 'Présent' : 
                                                                       ($emargement->isRetard() ? 'En retard' : 'Absent') }}
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                    Non émargé
                                                                </span>
                                                            @endif
                                                            <a href="{{ route('cours.show', $cours) }}" class="text-sm text-blue-600 hover:text-blue-900">
                                                                Voir le cours
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Aucun cours suivi pour le moment.</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                        <a href="{{ route('users.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Retour à la liste
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 