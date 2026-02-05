<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'role:Radmin']);

Route::get('/bibliothecaire', function () {
    return view('bibliothecaire.dashboard');
})->middleware(['auth', 'role:Rbibliothecaire']);

Route::get('/lecteur', function () {
    return view('lecteur.dashboard');
})->middleware(['auth', 'role:Rlecteur']);

