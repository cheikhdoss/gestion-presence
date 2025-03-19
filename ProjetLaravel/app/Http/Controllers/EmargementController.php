<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmargementsExport;
use App\Services\StatistiquesService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EmargementController extends Controller
{
    protected $statistiquesService;

    public function __construct(StatistiquesService $statistiquesService)
    {
        $this->statistiquesService = $statistiquesService;
    }

    public function index(Request $request): View
    {
        $query = Emargement::with(['cours.professeur', 'cours.salle', 'user'])
            ->when($request->filled('cours_id'), function ($query) use ($request) {
                $query->where('cours_id', $request->cours_id);
            })
            ->when($request->filled('statut'), function ($query) use ($request) {
                $query->where('statut', $request->statut);
            })
            ->when($request->filled('date_debut'), function ($query) use ($request) {
                $query->whereDate('date_signature', '>=', $request->date_debut);
            })
            ->when($request->filled('date_fin'), function ($query) use ($request) {
                $query->whereDate('date_signature', '<=', $request->date_fin);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            });

        // Filtrage selon le rôle de l'utilisateur
        if (Auth::user()->role === 'professeur') {
            $query->whereHas('cours', function ($q) {
                $q->where('professeur_id', Auth::id());
            });
        } elseif (Auth::user()->role === 'etudiant') {
            $query->where('user_id', Auth::id());
        }

        $emargements = $query->latest('date_signature')->paginate(15)->withQueryString();
        
        // Récupération des données pour les filtres
        $cours = Cache::remember('cours_list', 3600, function () {
            return Cours::with('professeur')->get(['id', 'matiere', 'professeur_id']);
        });

        return view('emargements.index', compact('emargements', 'cours'));
    }

    public function create(): View
    {
        $cours = Cours::with('professeur')->get(['id', 'matiere', 'professeur_id']);
        $etudiants = User::where('role', 'etudiant')->get(['id', 'name', 'email']);
        
        return view('emargements.create', compact('cours', 'etudiants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cours_id' => 'required|exists:cours,id',
            'user_id' => 'required|exists:users,id',
            'statut' => 'required|in:present,absent,retard',
            'commentaire' => 'nullable|string|max:255',
            'date_signature' => 'required|date',
        ]);

        // Vérification des doublons
        $existant = Emargement::where('cours_id', $validated['cours_id'])
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($existant) {
            return back()->withErrors(['message' => 'Un émargement existe déjà pour cet étudiant dans ce cours.']);
        }

        $emargement = Emargement::create($validated);

        $this->statistiquesService->invalidateCache('cours', $emargement->cours_id);
        $this->statistiquesService->invalidateCache('etudiant', $emargement->user_id);

        return redirect()->route('emargements.index')
            ->with('success', 'Émargement créé avec succès.');
    }

    public function show(Emargement $emargement): View
    {
        return view('emargements.show', compact('emargement'));
    }

    public function edit(Emargement $emargement): View
    {
        $emargement->load('cours.professeur', 'user');
        
        return view('emargements.edit', compact('emargement'));
    }

    public function update(Request $request, Emargement $emargement): RedirectResponse
    {
        if ($request->user()->cannot('update', $emargement)) {
            abort(403);
        }

        $validated = $request->validate([
            'statut' => 'required|in:present,absent,retard',
            'commentaire' => 'nullable|string|max:255',
            'date_signature' => 'required|date',
        ]);

        $emargement->update($validated);

        $this->statistiquesService->invalidateCache('cours', $emargement->cours_id);
        $this->statistiquesService->invalidateCache('etudiant', $emargement->user_id);

        return redirect()->route('emargements.index')
            ->with('success', 'Émargement mis à jour avec succès.');
    }

    public function destroy(Emargement $emargement): RedirectResponse
    {
        if (auth()->user()->cannot('delete', $emargement)) {
            abort(403);
        }

        $cours_id = $emargement->cours_id;
        $user_id = $emargement->user_id;

        $emargement->delete();

        $this->statistiquesService->invalidateCache('cours', $cours_id);
        $this->statistiquesService->invalidateCache('etudiant', $user_id);

        return redirect()->route('emargements.index')
            ->with('success', 'Émargement supprimé avec succès.');
    }

    public function rapport()
    {
        $professeurs = User::where('role', 'professeur')->get();
        $statistiques = [];

        foreach ($professeurs as $professeur) {
            $statistiques[$professeur->id] = [
                'nom' => $professeur->getNomComplet(),
                'present' => Emargement::where('professeur_id', $professeur->id)
                    ->where('statut', 'present')->count(),
                'absent' => Emargement::where('professeur_id', $professeur->id)
                    ->where('statut', 'absent')->count(),
                'retard' => Emargement::where('professeur_id', $professeur->id)
                    ->where('statut', 'retard')->count(),
            ];
        }

        return view('emargements.rapport', compact('statistiques'));
    }

    public function exportPdf(Request $request)
    {
        $query = $this->buildExportQuery($request);
        $emargements = $query->get();

        $pdf = Pdf::loadView('emargements.export-pdf', compact('emargements'));
        
        return $pdf->download('emargements.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new EmargementsExport($request), 'emargements.xlsx');
    }

    protected function buildExportQuery(Request $request)
    {
        return Emargement::with(['cours.professeur', 'cours.salle', 'user'])
            ->when($request->filled('cours_id'), function ($query) use ($request) {
                $query->where('cours_id', $request->cours_id);
            })
            ->when($request->filled('statut'), function ($query) use ($request) {
                $query->where('statut', $request->statut);
            })
            ->when($request->filled('date_debut'), function ($query) use ($request) {
                $query->whereDate('date_signature', '>=', $request->date_debut);
            })
            ->when($request->filled('date_fin'), function ($query) use ($request) {
                $query->whereDate('date_signature', '<=', $request->date_fin);
            })
            ->when(Auth::user()->role === 'professeur', function ($query) {
                $query->whereHas('cours', function ($q) {
                    $q->where('professeur_id', Auth::id());
                });
            })
            ->when(Auth::user()->role === 'etudiant', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('date_signature', 'desc');
    }

    public function statistiques(Request $request)
    {
        $stats = [
            'globales' => $this->statistiquesService->getStatistiquesGlobales(),
            'tendances' => $this->statistiquesService->getTendances(),
        ];

        if ($request->has('cours_id')) {
            $stats['cours'] = $this->statistiquesService->getStatistiquesCours($request->cours_id);
        }

        if ($request->has('user_id')) {
            $stats['etudiant'] = $this->statistiquesService->getStatistiquesEtudiant($request->user_id);
        }

        $cours = Cache::remember('cours_list', 3600, function () {
            return Cours::with('professeur')->get(['id', 'matiere', 'professeur_id']);
        });

        return view('emargements.statistiques', compact('stats', 'cours'));
    }
} 