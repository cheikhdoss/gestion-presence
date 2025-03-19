<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Index pour la table emargements
        Schema::table('emargements', function (Blueprint $table) {
            $table->index('cours_id');
            $table->index('user_id');
            $table->index('statut');
            $table->index('date_signature');
            $table->index(['cours_id', 'user_id']); // Index composite pour les recherches fréquentes
        });

        // Index pour la table cours
        Schema::table('cours', function (Blueprint $table) {
            $table->index('professeur_id');
            $table->index('salle_id');
            $table->index('date_heure');
            $table->index(['professeur_id', 'date_heure']); // Index composite pour les recherches par professeur et date
        });

        // Index pour la table users
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('email');
            $table->index(['role', 'email']); // Index composite pour les recherches par rôle et email
        });
    }

    public function down()
    {
        // Suppression des index de la table emargements
        Schema::table('emargements', function (Blueprint $table) {
            $table->dropIndex(['cours_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['statut']);
            $table->dropIndex(['date_signature']);
            $table->dropIndex(['cours_id', 'user_id']);
        });

        // Suppression des index de la table cours
        Schema::table('cours', function (Blueprint $table) {
            $table->dropIndex(['professeur_id']);
            $table->dropIndex(['salle_id']);
            $table->dropIndex(['date_heure']);
            $table->dropIndex(['professeur_id', 'date_heure']);
        });

        // Suppression des index de la table users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['email']);
            $table->dropIndex(['role', 'email']);
        });
    }
}; 