<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'destinataire_id',
        'date_envoi',
        'lu'
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'lu' => 'boolean'
    ];

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    // Marquer comme lu
    public function marquerCommeLu()
    {
        $this->lu = true;
        $this->save();
    }
} 