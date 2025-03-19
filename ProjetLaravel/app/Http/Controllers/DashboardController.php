<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // Données communes
        $data = [
            'user' => $user,
            'today' => $now->format('d/m/Y'),
        ];

        // Données spécifiques selon le rôle
        if ($user->role === 'professeur') {
            $data['prochains_cours'] = Cours::where('professeur_id', $user->id)
                ->where('date_heure', '>=', $now)
                ->orderBy('date_heure')
                ->take(5)
                ->get();

            $data['cours_non_signes'] = Cours::where('professeur_id', $user->id)
                ->where('date_heure', '<', $now)
                ->whereDoesntHave('emargements')
                ->orderBy('date_heure', 'desc')
                ->take(5)
                ->get();

            $data['derniers_emargements'] = Emargement::whereHas('cours', function ($query) use ($user) {
                $query->where('professeur_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        } elseif ($user->role === 'etudiant') {
            $data['prochains_cours'] = Cours::whereHas('etudiants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('date_heure', '>=', $now)
            ->orderBy('date_heure')
            ->take(5)
            ->get();

            $data['mes_emargements'] = Emargement::whereHas('cours.etudiants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        } elseif ($user->role === 'admin') {
            $data['total_cours'] = Cours::count();
            $data['total_emargements'] = Emargement::count();
            $data['total_etudiants'] = User::where('role', 'etudiant')->count();
            $data['total_professeurs'] = User::where('role', 'professeur')->count();

            $data['cours_aujourdhui'] = Cours::whereDate('date_heure', $now)->count();
            $data['emargements_aujourdhui'] = Emargement::whereDate('created_at', $now)->count();

            $data['derniers_cours'] = Cours::with(['professeur', 'salle'])
                ->orderBy('date_heure', 'desc')
                ->take(5)
                ->get();

            $data['derniers_emargements'] = Emargement::with(['cours.professeur'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('dashboard', $data);
    }
} 