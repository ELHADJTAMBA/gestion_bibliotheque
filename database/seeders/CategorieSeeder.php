<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['libelle' => 'Roman', 'description' => 'Romans et littérature générale'],
            ['libelle' => 'Science-Fiction', 'description' => 'Science-fiction et fantasy'],
            ['libelle' => 'Histoire', 'description' => 'Livres d\'histoire'],
            ['libelle' => 'Sciences', 'description' => 'Sciences et technologies'],
            ['libelle' => 'Philosophie', 'description' => 'Philosophie et essais'],
            ['libelle' => 'Jeunesse', 'description' => 'Littérature jeunesse'],
            ['libelle' => 'Biographie', 'description' => 'Biographies et mémoires'],
            ['libelle' => 'Poésie', 'description' => 'Poésie et théâtre'],
        ];

        foreach ($categories as $categorie) {
            Categorie::create($categorie);
        }
    }
}