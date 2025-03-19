@extends('layouts.app')

@section('title', 'Modifier l\'émargement')

@section('content')
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h1 class="text-2xl font-bold text-gray-900">Modifier l'émargement</h1>
        </div>
        <div class="border-t border-gray-200">
            <form action="{{ route('emargements.update', $emargement) }}" method="POST" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="cours_id" class="block text-sm font-medium text-gray-700">Cours</label>
                    <select name="cours_id" id="cours_id" required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Sélectionnez un cours</option>
                        @foreach($cours as $course)
                            <option value="{{ $course->id }}" {{ old('cours_id', $emargement->cours_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->matiere }} - {{ $course->date_heure->format('d/m/Y H:i') }} - {{ $course->professeur->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('cours_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_signature" class="block text-sm font-medium text-gray-700">Date de signature</label>
                    <input type="datetime-local" name="date_signature" id="date_signature" 
                        value="{{ old('date_signature', $emargement->date_signature ? date('Y-m-d\TH:i', strtotime($emargement->date_signature)) : '') }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('date_signature')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="statut" id="statut" required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="present" {{ old('statut', $emargement->statut) == 'present' ? 'selected' : '' }}>Présent</option>
                        <option value="absent" {{ old('statut', $emargement->statut) == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="retard" {{ old('statut', $emargement->statut) == 'retard' ? 'selected' : '' }}>Retard</option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="commentaire" class="block text-sm font-medium text-gray-700">Commentaire</label>
                    <textarea name="commentaire" id="commentaire" rows="3"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('commentaire', $emargement->commentaire) }}</textarea>
                    @error('commentaire')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('emargements.index') }}" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-500 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection 