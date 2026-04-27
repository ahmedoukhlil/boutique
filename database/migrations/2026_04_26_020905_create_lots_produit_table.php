<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variante_id')->constrained('variantes_produit')->cascadeOnDelete();
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->string('numero_lot')->nullable();
            $table->string('numero_commande')->nullable();
            $table->integer('quantite_initiale');
            $table->integer('quantite_restante');
            $table->decimal('prix_achat_unitaire', 10, 2)->nullable();
            $table->date('date_reception');
            $table->date('date_fin_saison')->nullable(); // équivalent date expiration pour fin de saison
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots_produit');
    }
};
