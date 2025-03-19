<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Accesseur pour utiliser 'nom' lorsque 'name' est appelé 
    protected function nom(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = explode(' ', $this->name, 2);
                return $parts[0] ?? $this->name;
            },
        );
    }

    // Accesseur pour utiliser 'prenom' comme deuxième partie de 'name'
    protected function prenom(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = explode(' ', $this->name, 2);
                return $parts[1] ?? '';
            },
        );
    }

    // Relations
    public function cours()
    {
        return $this->hasMany(Cours::class, 'professeur_id');
    }

    public function emargements()
    {
        return $this->hasMany(Emargement::class, 'professeur_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'destinataire_id');
    }

    // Méthodes utilitaires
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isProfesseur()
    {
        return $this->role === 'professeur';
    }

    public function isGestionnaire()
    {
        return $this->role === 'gestionnaire';
    }

    public function getNomComplet()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    /**
     * Check if the user is a student
     */
    public function isEtudiant()
    {
        return $this->role === 'etudiant';
    }

    /**
     * Get the courses where the user is a professor
     */
    public function coursEnseignes()
    {
        return $this->hasMany(Cours::class, 'professeur_id');
    }

    /**
     * Get the courses where the user is a student
     */
    public function coursSuivis()
    {
        return $this->belongsToMany(Cours::class, 'cours_user', 'user_id', 'cours_id');
    }
}
