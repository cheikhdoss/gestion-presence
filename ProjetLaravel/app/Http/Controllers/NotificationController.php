<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('destinataire_id', Auth::id())
            ->orderBy('date_envoi', 'desc')
            ->get();
        return view('notifications.index', compact('notifications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'destinataire_id' => 'required|exists:users,id',
        ]);

        $validated['date_envoi'] = now();
        
        Notification::create($validated);

        return redirect()->back()
            ->with('success', 'Notification envoyée avec succès.');
    }

    public function marquerCommeLu(Notification $notification)
    {
        if ($notification->destinataire_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette notification.');
        }

        $notification->marquerCommeLu();

        return redirect()->back()
            ->with('success', 'Notification marquée comme lue.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->destinataire_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette notification.');
        }

        $notification->delete();

        return redirect()->back()
            ->with('success', 'Notification supprimée avec succès.');
    }

    public function envoyerNotificationCours(Request $request)
    {
        $validated = $request->validate([
            'cours_id' => 'required|exists:cours,id',
            'message' => 'required|string'
        ]);

        $cours = Cours::findOrFail($validated['cours_id']);
        
        Notification::create([
            'message' => $validated['message'],
            'destinataire_id' => $cours->professeur_id,
            'date_envoi' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Notification envoyée au professeur.');
    }
} 