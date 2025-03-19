<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Salle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Exports\EmargementsExport;
use App\Services\PdfService;
use Maatwebsite\Excel\Facades\Excel;

class CoursController extends Controller
{
    public function index(Request $request): View
    {
        $query = Cours::with(['salle', 'professeur']);

        // Filtre par matière
        if ($request->filled('search')) {
            $query->where('matiere', 'like', '%' . $request->search . '%');
        }

        // Filtre par professeur
        if ($request->filled('professeur')) {
            $query->where('professeur_id', $request->professeur);
        }

        // Filtre par salle
        if ($request->filled('salle')) {
            $query->where('salle_id', $request->salle);
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('date_heure', $request->date);
        }

        // Tri
        $sort = $request->input('sort', 'date_heure');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $cours = $query->paginate(10)->withQueryString();
        $professeurs = User::where('role', 'professeur')->get();
        $salles = Salle::all();

        return view('cours.index', compact('cours', 'professeurs', 'salles'));
    }

    public function create(): View
    {
        $salles = Salle::all();
        $professeurs = User::all(); // À modifier plus tard pour ne sélectionner que les professeurs
        return view('cours.create', compact('salles', 'professeurs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'matiere' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_heure' => 'required|date',
            'salle_id' => 'required|exists:salles,id',
            'professeur_id' => 'required|exists:users,id'
        ]);

        Cours::create($validated);

        return redirect()->route('cours.index')
            ->with('success', 'Cours créé avec succès.');
    }

    public function show(Cours $cours): View
    {
        $cours->load(['salle', 'professeur', 'emargements']);
        return view('cours.show', compact('cours'));
    }

    public function edit(Cours $cours): View
    {
        $salles = Salle::all();
        $professeurs = User::all(); // À modifier plus tard pour ne sélectionner que les professeurs
        return view('cours.edit', compact('cours', 'salles', 'professeurs'));
    }

    public function update(Request $request, Cours $cours): RedirectResponse
    {
        $validated = $request->validate([
            'matiere' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_heure' => 'required|date',
            'salle_id' => 'required|exists:salles,id',
            'professeur_id' => 'required|exists:users,id'
        ]);

        $cours->update($validated);

        return redirect()->route('cours.index')
            ->with('success', 'Cours mis à jour avec succès.');
    }

    public function destroy(Cours $cours): RedirectResponse
    {
        $cours->delete();

        return redirect()->route('cours.index')
            ->with('success', 'Cours supprimé avec succès.');
    }

    public function exportEmargements(Cours $cours, string $format)
    {
        if (!auth()->user()->isAdmin() && !$cours->professeur_id === auth()->id()) {
            abort(403);
        }

        $filename = "emargements_{$cours->id}_{$cours->date_heure->format('Y-m-d')}";

        if ($format === 'excel') {
            return Excel::download(new EmargementsExport($cours), $filename . '.xlsx');
        } elseif ($format === 'pdf') {
            $pdfService = new PdfService();
            $pdf = $pdfService->generateEmargementsPdf($cours);

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"');
        }

        abort(404);
    }
} 