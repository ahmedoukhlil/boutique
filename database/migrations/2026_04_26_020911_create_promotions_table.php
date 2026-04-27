<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->nullable()->unique();
            $table->enum('type', ['pourcent', 'montant_fixe', 'gratuit'])->default('pourcent');
            $table->decimal('valeur', 8, 2);
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('produit_id')->nullable()->constrained('produits')->nullOnDelete();
            $table->datetime('date_debut');
            $table->datetime('date_fin')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
