<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function emprunts()
    {
        return $this->hasMany(Emprunt::class);
    }

    public function empruntsEnCours()
    {
        return $this->hasMany(Emprunt::class)->where('statut', 'en_cours');
    }

    public function empruntsEnRetard()
    {
        return $this->hasMany(Emprunt::class)->where('statut', 'en_retard');
    }

    // Méthodes de vérification de rôle
    public function isAdmin()
    {
        return $this->role === 'Radmin';
    }

    public function isBibliothecaire()
    {
        return $this->role === 'Rbibliothecaire';
    }

    public function isLecteur()
    {
        return $this->role === 'Rlecteur';
    }

    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }
}