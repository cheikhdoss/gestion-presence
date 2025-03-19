<?php

namespace App\Console\Commands;

use App\Models\Cours;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCoursRappels extends Command
{
    protected $signature = 'notifications:cours-rappels';
    protected $description = 'Envoie des rappels pour les cours qui auront lieu dans 24h';

    public function handle()
    {
        $demain = Carbon::tomorrow();
        
        // Récupère tous les cours qui ont lieu demain
        $cours = Cours::whereDate('date_heure', $demain->toDateString())->get();

        foreach ($cours as $cours) {
            // Crée une notification pour le professeur
            if ($cours->professeur->notif_rappel_cours) {
                Notification::create([
                    'message' => "Rappel : Vous avez un cours de {$cours->matiere} demain à " . 
                                Carbon::parse($cours->date_heure)->format('H:i') . 
                                " dans la salle {$cours->salle->libelle}.",
                    'destinataire_id' => $cours->professeur_id,
                    'cours_id' => $cours->id,
                    'date_envoi' => now(),
                ]);

                // Envoie un email si l'option est activée
                if ($cours->professeur->email_rappel_cours) {
                    // TODO: Implémenter l'envoi d'email
                    // Mail::to($cours->professeur->email)->send(new RappelCours($cours));
                }
            }
        }

        $this->info('Les rappels de cours ont été envoyés avec succès.');
    }
} 