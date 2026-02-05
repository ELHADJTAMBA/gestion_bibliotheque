<?php

namespace App\Http\Controllers\Bibliothecaire;

use App\Http\Controllers\Controller;
use App\Models\Auteur;
use Illuminate\Http\Request;

class AuteurController extends Controller
{
    public function index()
    {
        $auteurs = Auteur::withCount('livres')->latest()->paginate(15);
        return view('bibliothecaire.auteurs.index', compact('auteurs'));
    }

    public function create()
    {
        return view('bibliothecaire.auteurs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'nationalite' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'biographie' => 'nullable|string',
        ]);

        Auteur::create($validated);

        return redirect()->route('bibliothecaire.auteurs.index')
            ->with('success', 'Auteur ajouté avec succès.');
    }

    public function show(Auteur $auteur)
    {
        $auteur->load('livres.categorie');
        return view('bibliothecaire.auteurs.show', compact('auteur'));
    }

    public function edit(Auteur $auteur)
    {
        return view('bibliothecaire.auteurs.edit', compact('auteur'));
    }

    public function update(Request $request, Auteur $auteur)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'nationalite' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'biographie' => 'nullable|string',
        ]);

        $auteur->update($validated);

        return redirect()->route('bibliothecaire.auteurs.index')
            ->with('success', 'Auteur mis à jour avec succès.');
    }

    public function destroy(Auteur $auteur)
    {
        if ($auteur->livres()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un auteur ayant des livres associés.');
        }

        $auteur->delete();

        return redirect()->route('bibliothecaire.auteurs.index')
            ->with('success', 'Auteur supprimé avec succès.');
    }
}