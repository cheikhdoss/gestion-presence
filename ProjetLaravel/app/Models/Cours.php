<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'matiere',
        'description',
        'date_heure',
        'salle_id',
        'professeur_id'
    ];

    protected $casts = [
        'date_heure' => 'datetime'
    ];

    // Accesseur pour utiliser 'nom' à la place de 'matiere'
    protected function nom(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->matiere,
        );
    }

    // Accesseur pour utiliser 'date_cours' au lieu de 'date_heure'
    protected function dateCours(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_heure,
        );
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    public function emargements(): HasMany
    {
        return $this->hasMany(Emargement::class);
    }

    // Vérifie si le professeur est disponible pour ce créneau
    public function isProfesseurDisponible($date, $heure_debut, $heure_fin)
    {
        return !Cours::where('professeur_id', $this->professeur_id)
            ->where('date', $date)
            ->where(function ($query) use ($heure_debut, $heure_fin) {
                $query->where(function ($q) use ($heure_debut, $heure_fin) {
                    $q->where('heure_debut', '>=', $heure_debut)
                      ->where('heure_debut', '<', $heure_fin);
                })->orWhere(function ($q) use ($heure_debut, $heure_fin) {
                    $q->where('heure_fin', '>', $heure_debut)
                      ->where('heure_fin', '<=', $heure_fin);
                });
            })->exists();
    }
} 