<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livre extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'isbn',
        'resume',
        'nombre_exemplaires',
        'disponible',
        'categorie_id',
        'editeur',
        'annee_publication',
        'image',
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'nombre_exemplaires' => 'integer',
        'annee_publication' => 'integer',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function auteurs()
    {
        return $this->belongsToMany(Auteur::class, 'livre_auteur');
    }

    public function emprunts()
    {
        return $this->hasMany(Emprunt::class);
    }

    public function empruntActuel()
    {
        return $this->hasOne(Emprunt::class)->where('statut', 'en_cours')->latest();
    }
}