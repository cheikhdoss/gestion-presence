@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Préférences de notification</h1>
            <a href="{{ route('notifications.index') }}" class="text-indigo-600 hover:text-indigo-900">
                Retour aux notifications
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('notifications.update-preferences') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Notifications par email</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Choisissez quand vous souhaitez recevoir des notifications par email.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="email_rappel_cours" id="email_rappel_cours"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ auth()->user()->email_rappel_cours ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="email_rappel_cours" class="text-sm font-medium text-gray-700">
                                    Rappels de cours
                                </label>
                                <p class="text-sm text-gray-500">
                                    Recevoir un email de rappel 24h avant chaque cours
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="email_signature_manquante" id="email_signature_manquante"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ auth()->user()->email_signature_manquante ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="email_signature_manquante" class="text-sm font-medium text-gray-700">
                                    Signatures manquantes
                                </label>
                                <p class="text-sm text-gray-500">
                                    Recevoir un email pour les émargements non signés
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="email_modification_cours" id="email_modification_cours"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ auth()->user()->email_modification_cours ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="email_modification_cours" class="text-sm font-medium text-gray-700">
                                    Modifications de cours
                                </label>
                                <p class="text-sm text-gray-500">
                                    Recevoir un email lors de la modification d'un cours
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <h3 class="text-lg font-medium text-gray-900">Notifications dans l'application</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Gérez les notifications que vous recevez dans l'application.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notif_rappel_cours" id="notif_rappel_cours"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ auth()->user()->notif_rappel_cours ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="notif_rappel_cours" class="text-sm font-medium text-gray-700">
                                    Rappels de cours
                                </label>
                                <p class="text-sm text-gray-500">
                                    Recevoir une notification avant chaque cours
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="notif_signature_manquante" id="notif_signature_manquante"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ auth()->user()->notif_signature_manquante ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="notif_signature_manquante" class="text-sm font-medium text-gray-700">
                                    Signatures manquantes
                                </label>
                                <p class="text-sm text-gray-500">
                                    Recevoir une notification pour les émargements non signés
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Enregistrer les préférences
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 