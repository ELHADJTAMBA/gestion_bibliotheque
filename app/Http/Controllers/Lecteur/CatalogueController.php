<?php

namespace App\Http\Controllers\Lecteur;

use App\Http\Controllers\Controller;
use App\Models\Livre;
use App\Models\Categorie;
use App\Models\Auteur;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $query = Livre::with(['categorie', 'auteurs']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('auteurs', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie_id', $request->categorie);
        }

        // Filtre par disponibilité
        if ($request->filled('disponible')) {
            $query->where('disponible', $request->disponible);
        }

        $livres = $query->latest()->paginate(12);
        $categories = Categorie::all();

        return view('lecteur.catalogue.index', compact('livres', 'categories'));
    }

    public function show(Livre $livre)
    {
        $livre->load(['categorie', 'auteurs']);
        return view('lecteur.catalogue.show', compact('livre'));
    }
}