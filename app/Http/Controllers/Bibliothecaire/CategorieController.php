<?php

namespace App\Http\Controllers\Bibliothecaire;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('livres')->paginate(15);
        return view('bibliothecaire.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('bibliothecaire.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Categorie::create($validated);

        return redirect()->route('bibliothecaire.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Categorie $categorie)
    {
        return view('bibliothecaire.categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255|unique:categories,libelle,' . $categorie->id,
            'description' => 'nullable|string',
        ]);

        $categorie->update($validated);

        return redirect()->route('bibliothecaire.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->livres()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie contenant des livres.');
        }

        $categorie->delete();

        return redirect()->route('bibliothecaire.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}