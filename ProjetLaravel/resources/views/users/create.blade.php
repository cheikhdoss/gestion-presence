@extends('layouts.app')

@section('title', 'Créer un utilisateur')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Créer un utilisateur</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Ajoutez un nouvel utilisateur en remplissant les informations ci-dessous.
                    </p>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <!-- Nom -->
                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                                <div class="mt-1">
                                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nom') border-red-500 @enderror"
                                        required>
                                </div>
                                @error('nom')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prénom -->
                            <div>
                                <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                                <div class="mt-1">
                                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('prenom') border-red-500 @enderror"
                                        required>
                                </div>
                                @error('prenom')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
                                <div class="mt-1">
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror"
                                        required>
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mot de passe -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                                <div class="mt-1">
                                    <input type="password" name="password" id="password"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('password') border-red-500 @enderror"
                                        required>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmation du mot de passe -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                                <div class="mt-1">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        required>
                                </div>
                            </div>

                            <!-- Rôle -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                                <div class="mt-1">
                                    <select name="role" id="role"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('role') border-red-500 @enderror"
                                        required>
                                        <option value="">Sélectionnez un rôle</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                        <option value="professeur" {{ old('role') === 'professeur' ? 'selected' : '' }}>Professeur</option>
                                        <option value="etudiant" {{ old('role') === 'etudiant' ? 'selected' : '' }}>Étudiant</option>
                                    </select>
                                </div>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <a href="{{ route('users.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Créer l'utilisateur
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 