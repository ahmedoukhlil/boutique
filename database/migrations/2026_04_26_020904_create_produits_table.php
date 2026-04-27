<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->unique();
            $table->string('code_barre')->nullable()->unique();
            $table->text('description')->nullable();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('marque_id')->nullable()->constrained('marques')->nullOnDelete();
            $table->decimal('prix_vente', 10, 2);
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->integer('stock_alerte')->default(5);
            $table->string('saison')->nullable();
            $table->string('genre')->nullable(); // Homme, Femme, Enfant, Unisexe
            $table->string('image')->nullable();
            $table->boolean('actif')->default(true);
            $table->boolean('has_variantes')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
