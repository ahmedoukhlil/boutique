<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variante_id')->constrained('variantes_produit')->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots_produit')->nullOnDelete();
            $table->unsignedBigInteger('ligne_facture_id')->nullable();
            $table->enum('type', ['ENTREE', 'SORTIE', 'RETOUR', 'AJUSTEMENT']);
            $table->integer('quantite');
            $table->string('motif')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
