<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Livre;
use App\Models\Emprunt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_lecteurs' => User::where('role', 'Rlecteur')->count(),
            'total_bibliothecaires' => User::where('role', 'Rbibliothecaire')->count(),
            'total_livres' => Livre::count(),
            'total_emprunts' => Emprunt::count(),
            'emprunts_en_cours' => Emprunt::where('statut', 'en_cours')->count(),
            'emprunts_en_retard' => Emprunt::where('statut', 'en_retard')->count(),
        ];

        $derniers_emprunts = Emprunt::with(['user', 'livre'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'derniers_emprunts'));
    }
}