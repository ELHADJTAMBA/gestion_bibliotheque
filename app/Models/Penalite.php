<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalite extends Model
{
    use HasFactory;

    protected $fillable = [
        'emprunt_id',
        'montant',
        'payee',
    ];

    protected $casts = [
        'payee' => 'boolean',
        'montant' => 'decimal:2',
    ];

    public function emprunt()
    {
        return $this->belongsTo(Emprunt::class);
    }
}