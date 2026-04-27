<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variantes_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->string('taille')->nullable();    // S, M, L, XL, 38, 39, 40...
            $table->string('couleur')->nullable();   // Rouge, Bleu, Noir...
            $table->string('code_couleur')->nullable(); // #FF0000
            $table->string('code_barre')->nullable()->unique();
            $table->decimal('prix_supplement', 8, 2)->default(0);
            $table->integer('quantite_stock')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variantes_produit');
    }
};
