<?php

namespace App\Http\Controllers\Bibliothecaire;

use App\Http\Controllers\Controller;
use App\Models\Livre;
use App\Models\Emprunt;
use App\Models\Categorie;
use App\Models\Auteur;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_livres' => Livre::count(),
            'livres_disponibles' => Livre::where('disponible', true)->count(),
            'total_categories' => Categorie::count(),
            'total_auteurs' => Auteur::count(),
            'emprunts_en_cours' => Emprunt::where('statut', 'en_cours')->count(),
            'emprunts_en_retard' => Emprunt::where('statut', 'en_retard')->count(),
        ];

        $emprunts_recents = Emprunt::with(['user', 'livre'])
            ->latest()
            ->take(5)
            ->get();

        $emprunts_en_retard = Emprunt::with(['user', 'livre'])
            ->where('statut', 'en_retard')
            ->orWhere(function($query) {
                $query->where('statut', 'en_cours')
                      ->where('date_retour_prevue', '<', Carbon::now());
            })
            ->latest()
            ->take(10)
            ->get();

        return view('bibliothecaire.dashboard', compact('stats', 'emprunts_recents', 'emprunts_en_retard'));
    }
}