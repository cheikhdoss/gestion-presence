<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emargement extends Model
{
    use HasFactory;

    protected $fillable = [
        'cours_id',
        'date_signature',
        'statut',
        'commentaire'
    ];

    protected $dates = [
        'date_signature',
        'created_at',
        'updated_at'
    ];

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class);
    }

    public function professeur()
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    // Méthodes utilitaires pour le statut
    public function isPresent()
    {
        return $this->statut === 'present';
    }

    public function isAbsent()
    {
        return $this->statut === 'absent';
    }

    public function isRetard()
    {
        return $this->statut === 'retard';
    }
} 