<?php

namespace App\Http\Controllers\Bibliothecaire;

use App\Http\Controllers\Controller;
use App\Models\Emprunt;
use App\Models\Livre;
use App\Models\User;
use App\Models\Penalite;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmpruntController extends Controller
{
    public function index(Request $request)
    {
        $query = Emprunt::with(['user', 'livre']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('livre', function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%");
            });
        }

        $emprunts = $query->latest()->paginate(15);

        return view('bibliothecaire.emprunts.index', compact('emprunts'));
    }

    public function create()
    {
        $lecteurs = User::where('role', 'Rlecteur')->get();
        $livres = Livre::where('disponible', true)->get();
        return view('bibliothecaire.emprunts.create', compact('lecteurs', 'livres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'livre_id' => 'required|exists:livres,id',
            'date_emprunt' => 'required|date',
            'date_retour_prevue' => 'required|date|after:date_emprunt',
        ]);

        $livre = Livre::findOrFail($request->livre_id);

        if (!$livre->disponible) {
            return back()->with('error', 'Ce livre n\'est pas disponible.');
        }

        $validated['statut'] = 'en_cours';
        Emprunt::create($validated);

        // Marquer le livre comme non disponible s'il n'y a plus d'exemplaires
        if ($livre->emprunts()->where('statut', 'en_cours')->count() >= $livre->nombre_exemplaires) {
            $livre->update(['disponible' => false]);
        }

        return redirect()->route('bibliothecaire.emprunts.index')
            ->with('success', 'Emprunt créé avec succès.');
    }

    public function show(Emprunt $emprunt)
    {
        $emprunt->load(['user', 'livre', 'penalite']);
        return view('bibliothecaire.emprunts.show', compact('emprunt'));
    }

    public function validerRetour(Emprunt $emprunt)
    {
        if ($emprunt->statut !== 'en_cours') {
            return back()->with('error', 'Cet emprunt n\'est pas en cours.');
        }

        $dateRetourEffective = Carbon::now();
        $emprunt->update([
            'date_retour_effective' => $dateRetourEffective,
            'statut' => 'retourne',
        ]);

        // Calculer pénalité si retard
        if ($dateRetourEffective->isAfter($emprunt->date_retour_prevue)) {
            $joursRetard = $dateRetourEffective->diffInDays($emprunt->date_retour_prevue);
            $montant = $joursRetard * 500; // 500 FCFA par jour de retard

            Penalite::create([
                'emprunt_id' => $emprunt->id,
                'montant' => $montant,
                'payee' => false,
            ]);
        }

        // Remettre le livre disponible
        $livre = $emprunt->livre;
        $livre->update(['disponible' => true]);

        return redirect()->route('bibliothecaire.emprunts.index')
            ->with('success', 'Retour validé avec succès.');
    }

    public function destroy(Emprunt $emprunt)
    {
        if ($emprunt->statut === 'en_cours') {
            return back()->with('error', 'Impossible de supprimer un emprunt en cours.');
        }

        $emprunt->delete();

        return redirect()->route('bibliothecaire.emprunts.index')
            ->with('success', 'Emprunt supprimé avec succès.');
    }
}