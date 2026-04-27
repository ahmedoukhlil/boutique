<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('sous_total', 10, 2)->default(0);
            $table->decimal('remise_montant', 10, 2)->default(0);
            $table->decimal('remise_pourcent', 5, 2)->default(0);
            $table->decimal('total_ttc', 10, 2)->default(0);
            $table->decimal('montant_recu', 10, 2)->default(0);
            $table->decimal('monnaie_rendue', 10, 2)->default(0);
            $table->enum('mode_paiement', ['especes', 'carte', 'cheque', 'mixte', 'credit'])->default('especes');
            $table->enum('statut', ['en_cours', 'payee', 'annulee', 'remboursee'])->default('en_cours');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
