<?php

namespace App\Http\Controllers\Lecteur;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'emprunts_en_cours' => $user->empruntsEnCours()->count(),
            'emprunts_total' => $user->emprunts()->count(),
            'emprunts_en_retard' => $user->empruntsEnRetard()->count(),
        ];

        $emprunts_actifs = $user->empruntsEnCours()
            ->with('livre.auteurs')
            ->latest()
            ->get();

        $historique = $user->emprunts()
            ->with('livre.auteurs')
            ->where('statut', '!=', 'en_cours')
            ->latest()
            ->take(5)
            ->get();

        return view('lecteur.dashboard', compact('stats', 'emprunts_actifs', 'historique'));
    }
}