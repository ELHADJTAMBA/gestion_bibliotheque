<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auteur extends Model
{
    use HasFactory;

    protected $table = 'auteurs';

    protected $fillable = [
        'nom',
        'prenom',
        'nationalite',
        'date_naissance',
        'biographie',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function livres()
    {
        return $this->belongsToMany(Livre::class, 'livre_auteur');
    }

    public function getNomCompletAttribute()
    {
        return $this->prenom ? "{$this->prenom} {$this->nom}" : $this->nom;
    }
}