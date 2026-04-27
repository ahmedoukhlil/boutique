<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lignes_facture', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('factures')->cascadeOnDelete();
            $table->foreignId('variante_id')->nullable()->constrained('variantes_produit')->nullOnDelete();
            $table->string('designation');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('remise_pourcent', 5, 2)->default(0);
            $table->decimal('total_ligne', 10, 2);
            $table->enum('type', ['produit', 'service'])->default('produit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_facture');
    }
};
