<?php

namespace App\Policies;

use App\Models\Emargement;
use App\Models\User;
use App\Models\Cours;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmargementPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir la liste
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Emargement $emargement): bool
    {
        // Un professeur peut voir les émargements de ses cours
        if ($user->isProfesseur()) {
            return $emargement->cours->professeur_id === $user->id;
        }

        // Un étudiant peut voir ses propres émargements
        if ($user->isEtudiant()) {
            return $emargement->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Cours $cours, User $etudiant): bool
    {
        // Un professeur peut créer des émargements pour ses cours
        if ($user->isProfesseur()) {
            return $cours->professeur_id === $user->id;
        }

        // Un étudiant peut créer son propre émargement
        if ($user->isEtudiant()) {
            return $user->id === $etudiant->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Emargement $emargement): bool
    {
        // Un professeur peut modifier les émargements de ses cours
        if ($user->isProfesseur()) {
            return $emargement->cours->professeur_id === $user->id;
        }

        // Un étudiant peut modifier son émargement uniquement s'il n'est pas encore signé
        if ($user->isEtudiant()) {
            return $emargement->user_id === $user->id && !$emargement->date_signature;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Emargement $emargement): bool
    {
        // Seul un professeur peut supprimer les émargements de ses cours
        if ($user->isProfesseur()) {
            return $emargement->cours->professeur_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can export emargements.
     */
    public function export(User $user, ?Cours $cours = null): bool
    {
        // Si un cours est spécifié, vérifier si l'utilisateur est le professeur du cours
        if ($cours) {
            if ($user->isProfesseur()) {
                return $cours->professeur_id === $user->id;
            }
        }

        // Seuls les admins peuvent exporter tous les émargements
        return $user->isAdmin();
    }
}
