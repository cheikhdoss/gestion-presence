<?php

namespace App\Console\Commands;

use App\Models\Cours;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEmargementsManquants extends Command
{
    protected $signature = 'notifications:emargements-manquants';
    protected $description = 'Vérifie les émargements manquants et envoie des notifications';

    public function handle()
    {
        // Récupère tous les cours terminés aujourd'hui sans émargement
        $coursNonSigned = Cours::where('date_heure', '<=', now())
            ->whereDate('date_heure', today())
            ->whereDoesntHave('emargement')
            ->get();

        foreach ($coursNonSigned as $cours) {
            // Notifie le professeur
            if ($cours->professeur->notif_signature_manquante) {
                Notification::create([
                    'message' => "Vous n'avez pas encore signé l'émargement pour votre cours de {$cours->matiere} " .
                                "qui a eu lieu à " . Carbon::parse($cours->date_heure)->format('H:i') . ".",
                    'destinataire_id' => $cours->professeur_id,
                    'cours_id' => $cours->id,
                    'date_envoi' => now(),
                ]);

                // Envoie un email si l'option est activée
                if ($cours->professeur->email_signature_manquante) {
                    // TODO: Implémenter l'envoi d'email
                    // Mail::to($cours->professeur->email)->send(new EmargementManquant($cours));
                }
            }

            // Notifie les gestionnaires
            $gestionnaires = User::where('role', 'gestionnaire')->get();
            foreach ($gestionnaires as $gestionnaire) {
                if ($gestionnaire->notif_signature_manquante) {
                    Notification::create([
                        'message' => "Émargement manquant pour le cours de {$cours->matiere} " .
                                    "({$cours->professeur->nom} {$cours->professeur->prenom}) " .
                                    "qui a eu lieu à " . Carbon::parse($cours->date_heure)->format('H:i') . ".",
                        'destinataire_id' => $gestionnaire->id,
                        'cours_id' => $cours->id,
                        'date_envoi' => now(),
                    ]);

                    // Envoie un email si l'option est activée
                    if ($gestionnaire->email_signature_manquante) {
                        // TODO: Implémenter l'envoi d'email
                        // Mail::to($gestionnaire->email)->send(new EmargementManquantGestionnaire($cours));
                    }
                }
            }
        }

        $this->info('La vérification des émargements manquants a été effectuée avec succès.');
    }
} 