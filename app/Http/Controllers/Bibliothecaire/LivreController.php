<?php

namespace App\Http\Controllers\Bibliothecaire;

use App\Http\Controllers\Controller;
use App\Models\Livre;
use App\Models\Categorie;
use App\Models\Auteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivreController extends Controller
{
    public function index(Request $request)
    {
        $query = Livre::with(['categorie', 'auteurs']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie_id', $request->categorie);
        }

        $livres = $query->latest()->paginate(12);
        $categories = Categorie::all();

        return view('bibliothecaire.livres.index', compact('livres', 'categories'));
    }

    public function create()
    {
        $categories = Categorie::all();
        $auteurs = Auteur::all();
        return view('bibliothecaire.livres.create', compact('categories', 'auteurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'isbn' => 'required|string|unique:livres',
            'resume' => 'nullable|string',
            'nombre_exemplaires' => 'required|integer|min:1',
            'categorie_id' => 'required|exists:categories,id',
            'auteurs' => 'required|array|min:1',
            'auteurs.*' => 'exists:auteurs,id',
            'editeur' => 'nullable|string|max:255',
            'annee_publication' => 'nullable|integer|min:1000|max:' . date('Y'),
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('livres', 'public');
        }

        $livre = Livre::create($validated);
        $livre->auteurs()->attach($request->auteurs);

        return redirect()->route('bibliothecaire.livres.index')
            ->with('success', 'Livre ajouté avec succès.');
    }

    public function show(Livre $livre)
    {
        $livre->load(['categorie', 'auteurs', 'emprunts.user']);
        return view('bibliothecaire.livres.show', compact('livre'));
    }

    public function edit(Livre $livre)
    {
        $categories = Categorie::all();
        $auteurs = Auteur::all();
        return view('bibliothecaire.livres.edit', compact('livre', 'categories', 'auteurs'));
    }

    public function update(Request $request, Livre $livre)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'isbn' => 'required|string|unique:livres,isbn,' . $livre->id,
            'resume' => 'nullable|string',
            'nombre_exemplaires' => 'required|integer|min:1',
            'categorie_id' => 'required|exists:categories,id',
            'auteurs' => 'required|array|min:1',
            'auteurs.*' => 'exists:auteurs,id',
            'editeur' => 'nullable|string|max:255',
            'annee_publication' => 'nullable|integer|min:1000|max:' . date('Y'),
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'disponible' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($livre->image) {
                Storage::disk('public')->delete($livre->image);
            }
            $validated['image'] = $request->file('image')->store('livres', 'public');
        }

        $livre->update($validated);
        $livre->auteurs()->sync($request->auteurs);

        return redirect()->route('bibliothecaire.livres.index')
            ->with('success', 'Livre mis à jour avec succès.');
    }

    public function destroy(Livre $livre)
    {
        if ($livre->emprunts()->where('statut', 'en_cours')->exists()) {
            return back()->with('error', 'Impossible de supprimer un livre actuellement emprunté.');
        }

        if ($livre->image) {
            Storage::disk('public')->delete($livre->image);
        }

        $livre->delete();

        return redirect()->route('bibliothecaire.livres.index')
            ->with('success', 'Livre supprimé avec succès.');
    }
}