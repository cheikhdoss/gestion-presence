<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SalleController extends Controller
{
    public function index(): View
    {
        $salles = Salle::all();
        return view('salles.index', compact('salles'));
    }

    public function create(): View
    {
        return view('salles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'capacite' => 'nullable|integer|min:1',
            'equipements' => 'nullable|string'
        ]);

        Salle::create($validated);

        return redirect()->route('salles.index')
            ->with('success', 'Salle créée avec succès.');
    }

    public function show(Salle $salle): View
    {
        return view('salles.show', compact('salle'));
    }

    public function edit(Salle $salle): View
    {
        return view('salles.edit', compact('salle'));
    }

    public function update(Request $request, Salle $salle): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'capacite' => 'nullable|integer|min:1',
            'equipements' => 'nullable|string'
        ]);

        $salle->update($validated);

        return redirect()->route('salles.index')
            ->with('success', 'Salle mise à jour avec succès.');
    }

    public function destroy(Salle $salle): RedirectResponse
    {
        $salle->delete();

        return redirect()->route('salles.index')
            ->with('success', 'Salle supprimée avec succès.');
    }

    public function checkDisponibilite(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'salle_id' => 'required|exists:salles,id'
        ]);

        $salle = Salle::find($request->salle_id);
        $disponible = $salle->isDisponible(
            $request->date,
            $request->heure_debut,
            $request->heure_fin
        );

        return response()->json(['disponible' => $disponible]);
    }
} 