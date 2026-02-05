<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un admin
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@bibliotheque.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'Radmin',
        ]);

        // Créer un bibliothécaire
        User::create([
            'name' => 'Bibliothécaire Principal',
            'email' => 'biblio@bibliotheque.com',
            'password' => Hash::make('Biblio@123'),
            'role' => 'Rbibliothecaire',
        ]);

        // Créer un lecteur
        User::create([
            'name' => 'Jean Lecteur',
            'email' => 'lecteur@bibliotheque.com',
            'password' => Hash::make('Lecteur@123'),
            'role' => 'Rlecteur',
        ]);

        // Appeler les autres seeders
        $this->call([
            CategorieSeeder::class,
            AuteurSeeder::class,
        ]);
    }
}