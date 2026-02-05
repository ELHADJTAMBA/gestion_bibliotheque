<?php

namespace Database\Seeders;

use App\Models\Auteur;
use Illuminate\Database\Seeder;

class AuteurSeeder extends Seeder
{
    public function run(): void
    {
        $auteurs = [
            [
                'nom' => 'Hugo',
                'prenom' => 'Victor',
                'nationalite' => 'Française',
                'date_naissance' => '1802-02-26',
            ],
            [
                'nom' => 'Camus',
                'prenom' => 'Albert',
                'nationalite' => 'Française',
                'date_naissance' => '1913-11-07',
            ],
            [
                'nom' => 'Diop',
                'prenom' => 'Cheikh Anta',
                'nationalite' => 'Sénégalaise',
                'date_naissance' => '1923-12-29',
            ],
            [
                'nom' => 'Senghor',
                'prenom' => 'Léopold Sédar',
                'nationalite' => 'Sénégalaise',
                'date_naissance' => '1906-10-09',
            ],
            [
                'nom' => 'Kourouma',
                'prenom' => 'Ahmadou',
                'nationalite' => 'Ivoirienne',
                'date_naissance' => '1927-11-24',
            ],
        ];

        foreach ($auteurs as $auteur) {
            Auteur::create($auteur);
        }
    }
}