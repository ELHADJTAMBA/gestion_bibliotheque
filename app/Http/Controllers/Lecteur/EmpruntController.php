<?php

namespace App\Http\Controllers\Lecteur;

use App\Http\Controllers\Controller;
use App\Models\Emprunt;
use App\Models\Livre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmpruntController extends Controller
{
    public function demander(Request $request, Livre $livre)
    {
        if (!$livre->disponible) {
            return back()->with('error', 'Ce livre n\'est pas disponible.');
        }

        // Vérifier si l'utilisateur n'a pas déjà emprunté ce livre
        $empruntExistant = Emprunt::where('user_id', Auth::id())
            ->where('livre_id', $livre->id)
            ->where('statut', 'en_cours')
            ->exists();

        if ($empruntExistant) {
            return back()->with('error', 'Vous avez déjà emprunté ce livre.');
        }

        // Créer la demande d'emprunt
        Emprunt::create([
            'user_id' => Auth::id(),
            'livre_id' => $livre->id,
            'date_emprunt' => Carbon::now(),
            'date_retour_prevue' => Carbon::now()->addDays(14), // 14 jours par défaut
            'statut' => 'en_cours',
        ]);

        // Marquer le livre comme non disponible
        if ($livre->emprunts()->where('statut', 'en_cours')->count() >= $livre->nombre_exemplaires) {
            $livre->update(['disponible' => false]);
        }

        return redirect()->route('lecteur.dashboard')
            ->with('success', 'Demande d\'emprunt enregistrée avec succès.');
    }

    public function mesEmprunts()
    {
        $emprunts = Auth::user()->emprunts()
            ->with(['livre.auteurs', 'penalite'])
            ->latest()
            ->paginate(10);

        return view('lecteur.emprunts.index', compact('emprunts'));
    }
}