<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Salle extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'capacite',
        'equipements'
    ];

    // Accesseur pour utiliser 'nom' à la place de 'libelle'
    protected function nom(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->libelle,
        );
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class);
    }

    // Vérifie si la salle est disponible pour un créneau horaire donné
    public function isDisponible($date, $heure_debut, $heure_fin)
    {
        return !$this->cours()
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