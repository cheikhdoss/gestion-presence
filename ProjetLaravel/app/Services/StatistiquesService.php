<?php

namespace App\Services;

use App\Models\Emargement;
use App\Models\Cours;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StatistiquesService
{
    // Durée de cache par défaut : 1 heure
    const CACHE_DURATION = 3600;

    /**
     * Récupère les statistiques globales avec mise en cache
     */
    public function getStatistiquesGlobales()
    {
        return Cache::remember('statistiques.globales', self::CACHE_DURATION, function () {
            return [
                'total_emargements' => Emargement::count(),
                'total_presents' => Emargement::where('statut', 'present')->count(),
                'total_absents' => Emargement::where('statut', 'absent')->count(),
                'total_retards' => Emargement::where('statut', 'retard')->count(),
            ];
        });
    }

    /**
     * Récupère les statistiques par cours avec mise en cache
     */
    public function getStatistiquesCours($cours_id)
    {
        return Cache::remember("statistiques.cours.{$cours_id}", self::CACHE_DURATION, function () use ($cours_id) {
            $emargements = Emargement::where('cours_id', $cours_id)->get();
            
            return [
                'total' => $emargements->count(),
                'presents' => $emargements->where('statut', 'present')->count(),
                'absents' => $emargements->where('statut', 'absent')->count(),
                'retards' => $emargements->where('statut', 'retard')->count(),
                'taux_presence' => $emargements->count() > 0 
                    ? round(($emargements->where('statut', 'present')->count() / $emargements->count()) * 100, 2)
                    : 0,
            ];
        });
    }

    /**
     * Récupère les statistiques par étudiant avec mise en cache
     */
    public function getStatistiquesEtudiant($user_id)
    {
        return Cache::remember("statistiques.etudiant.{$user_id}", self::CACHE_DURATION, function () use ($user_id) {
            $emargements = Emargement::where('user_id', $user_id)->get();
            
            return [
                'total' => $emargements->count(),
                'presents' => $emargements->where('statut', 'present')->count(),
                'absents' => $emargements->where('statut', 'absent')->count(),
                'retards' => $emargements->where('statut', 'retard')->count(),
                'taux_presence' => $emargements->count() > 0 
                    ? round(($emargements->where('statut', 'present')->count() / $emargements->count()) * 100, 2)
                    : 0,
            ];
        });
    }

    /**
     * Récupère les tendances sur les 30 derniers jours avec mise en cache
     */
    public function getTendances()
    {
        return Cache::remember('statistiques.tendances', self::CACHE_DURATION, function () {
            $debut = Carbon::now()->subDays(30);
            $fin = Carbon::now();
            
            $emargements = Emargement::whereBetween('date_signature', [$debut, $fin])
                ->selectRaw('DATE(date_signature) as date, statut, COUNT(*) as total')
                ->groupBy('date', 'statut')
                ->get();

            $dates = [];
            $current = $debut->copy();
            while ($current <= $fin) {
                $dateStr = $current->format('Y-m-d');
                $dates[$dateStr] = [
                    'presents' => 0,
                    'absents' => 0,
                    'retards' => 0
                ];
                $current->addDay();
            }

            foreach ($emargements as $emargement) {
                $dates[$emargement->date][$emargement->statut] = $emargement->total;
            }

            return $dates;
        });
    }

    /**
     * Invalide le cache des statistiques
     */
    public function invalidateCache($type = null, $id = null)
    {
        if ($type === null) {
            Cache::forget('statistiques.globales');
            Cache::forget('statistiques.tendances');
            return;
        }

        if ($type === 'cours') {
            Cache::forget("statistiques.cours.{$id}");
        } elseif ($type === 'etudiant') {
            Cache::forget("statistiques.etudiant.{$id}");
        }

        // Invalide aussi les statistiques globales
        Cache::forget('statistiques.globales');
        Cache::forget('statistiques.tendances');
    }
} 