<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livres', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('isbn')->unique();
            $table->text('resume')->nullable();
            $table->integer('nombre_exemplaires')->default(1);
            $table->boolean('disponible')->default(true);
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->string('editeur')->nullable();
            $table->integer('annee_publication')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livres');
    }
};