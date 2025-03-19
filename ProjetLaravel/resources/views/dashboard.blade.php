@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Bonjour {{ $user->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $today }} - {{ ucfirst($user->role) }}
                </p>
            </div>
        </div>

        @if($user->role === 'admin')
            <!-- Statistiques pour admin -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total des cours</dt>
                                    <dd class="text-lg font-bold text-gray-900">{{ $total_cours }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total des émargements</dt>
                                    <dd class="text-lg font-bold text-gray-900">{{ $total_emargements }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Étudiants</dt>
                                    <dd class="text-lg font-bold text-gray-900">{{ $total_etudiants }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Professeurs</dt>
                                    <dd class="text-lg font-bold text-gray-900">{{ $total_professeurs }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Derniers cours et émargements -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Derniers cours</h3>
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul class="-my-4 divide-y divide-gray-200">
                                    @foreach($derniers_cours as $cours)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $cours->matiere }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $cours->professeur->name }} - {{ $cours->salle->libelle }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $cours->date_heure->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Derniers émargements</h3>
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul class="-my-4 divide-y divide-gray-200">
                                    @foreach($derniers_emargements as $emargement)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $emargement->cours->matiere }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $emargement->cours->professeur->name }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        Signé le {{ $emargement->date_signature->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $emargement->isPresent() ? 'bg-green-100 text-green-800' : 
                                                           ($emargement->isAbsent() ? 'bg-red-100 text-red-800' : 
                                                            'bg-yellow-100 text-yellow-800') }}">
                                                        {{ ucfirst($emargement->statut) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($user->role === 'professeur')
            <!-- Vue professeur -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Prochains cours</h3>
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul class="-my-4 divide-y divide-gray-200">
                                    @forelse($prochains_cours as $cours)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $cours->matiere }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $cours->date_heure->format('d/m/Y H:i') }} - {{ $cours->salle->libelle }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <a href="{{ route('cours.show', $cours) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Voir détails
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4 text-center text-gray-500">
                                            Aucun cours à venir
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Cours non signés</h3>
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul class="-my-4 divide-y divide-gray-200">
                                    @forelse($cours_non_signes as $cours)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $cours->matiere }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $cours->date_heure->format('d/m/Y H:i') }} - {{ $cours->salle->libelle }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <a href="{{ route('emargements.create', ['cours_id' => $cours->id]) }}" 
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                        Signer
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4 text-center text-gray-500">
                                            Tous les cours sont signés
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($user->role === 'etudiant')
            <!-- Vue étudiant -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Mes prochains cours</h3>
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul class="-my-4 divide-y divide-gray-200">
                                    @forelse($prochains_cours as $cours)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $cours->matiere }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $cours->date_heure->format('d/m/Y H:i') }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $cours->professeur->name }} - {{ $cours->salle->libelle }}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4 text-center text-gray-500">
                                            Aucun cours à venir
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Mes derniers émargements</h3>
                        <div class="mt-5">
                            <div class="flow-root">
                                <ul class="-my-4 divide-y divide-gray-200">
                                    @forelse($mes_emargements as $emargement)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $emargement->cours->matiere }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $emargement->date_signature->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $emargement->isPresent() ? 'bg-green-100 text-green-800' : 
                                                           ($emargement->isAbsent() ? 'bg-red-100 text-red-800' : 
                                                            'bg-yellow-100 text-yellow-800') }}">
                                                        {{ ucfirst($emargement->statut) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4 text-center text-gray-500">
                                            Aucun émargement
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection 