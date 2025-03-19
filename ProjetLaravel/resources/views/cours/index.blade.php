@extends('layouts.app')

@section('title', 'Liste des cours')

@section('content')
    <div class="space-y-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Cours</h1>
                <p class="mt-2 text-sm text-gray-700">Liste de tous les cours disponibles.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('cours.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Ajouter un cours
                </a>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <form action="{{ route('cours.index') }}" method="GET" class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-4">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Rechercher</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Rechercher par matière">
                        </div>
                    </div>

                    <div>
                        <label for="professeur" class="sr-only">Professeur</label>
                        <select name="professeur" id="professeur"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Tous les professeurs</option>
                            @foreach($professeurs as $professeur)
                                <option value="{{ $professeur->id }}" {{ request('professeur') == $professeur->id ? 'selected' : '' }}>
                                    {{ $professeur->nom }} {{ $professeur->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="salle" class="sr-only">Salle</label>
                        <select name="salle" id="salle"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Toutes les salles</option>
                            @foreach($salles as $salle)
                                <option value="{{ $salle->id }}" {{ request('salle') == $salle->id ? 'selected' : '' }}>
                                    {{ $salle->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date" class="sr-only">Date</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Filtrer
                        </button>
                        @if(request()->hasAny(['search', 'professeur', 'salle', 'date']))
                            <a href="{{ route('cours.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Liste des cours -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('cours.index', array_merge(request()->query(), ['sort' => 'matiere', 'direction' => request('sort') === 'matiere' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Matière</span>
                                    @if(request('sort') === 'matiere')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if(request('direction') === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('cours.index', array_merge(request()->query(), ['sort' => 'date_heure', 'direction' => request('sort') === 'date_heure' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center space-x-1 hover:text-gray-700">
                                    <span>Date et heure</span>
                                    @if(request('sort') === 'date_heure')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if(request('direction') === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Salle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Professeur
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cours as $course)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $course->matiere }}</div>
                                    @if($course->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->date_heure->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->salle->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->professeur->nom }} {{ $course->professeur->prenom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('cours.show', $course) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        Voir
                                    </a>
                                    <a href="{{ route('cours.edit', $course) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Modifier
                                    </a>
                                    <form action="{{ route('cours.destroy', $course) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Aucun cours trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cours->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $cours->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection 