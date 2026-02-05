<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Bibliothecaire\DashboardController as BibliothecaireDashboard;
use App\Http\Controllers\Bibliothecaire\LivreController;
use App\Http\Controllers\Bibliothecaire\AuteurController;
use App\Http\Controllers\Bibliothecaire\CategorieController;
use App\Http\Controllers\Bibliothecaire\EmpruntController as BiblioEmpruntController;
use App\Http\Controllers\Lecteur\DashboardController as LecteurDashboard;
use App\Http\Controllers\Lecteur\CatalogueController;
use App\Http\Controllers\Lecteur\EmpruntController as LecteurEmpruntController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isBibliothecaire()) {
        return redirect()->route('bibliothecaire.dashboard');
    } else {
        return redirect()->route('lecteur.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes Admin
Route::middleware(['auth', 'role:Radmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
});

// Routes Bibliothécaire
Route::middleware(['auth', 'role:Rbibliothecaire'])->prefix('bibliothecaire')->name('bibliothecaire.')->group(function () {
    Route::get('/dashboard', [BibliothecaireDashboard::class, 'index'])->name('dashboard');
    
    Route::resource('livres', LivreController::class);
    Route::resource('auteurs', AuteurController::class);
    Route::resource('categories', CategorieController::class);
    Route::resource('emprunts', BiblioEmpruntController::class);
    
    Route::post('/emprunts/{emprunt}/valider-retour', [BiblioEmpruntController::class, 'validerRetour'])
        ->name('emprunts.valider-retour');
});

// Routes Lecteur
Route::middleware(['auth', 'role:Rlecteur'])->prefix('lecteur')->name('lecteur.')->group(function () {
    Route::get('/dashboard', [LecteurDashboard::class, 'index'])->name('dashboard');
    
    Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue.index');
    Route::get('/catalogue/{livre}', [CatalogueController::class, 'show'])->name('catalogue.show');
    
    Route::post('/emprunter/{livre}', [LecteurEmpruntController::class, 'demander'])->name('emprunter');
    Route::get('/mes-emprunts', [LecteurEmpruntController::class, 'mesEmprunts'])->name('emprunts.index');
});

require __DIR__.'/auth.php';