<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caisse_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->nullable()->constrained('factures')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['VENTE', 'REMBOURSEMENT', 'ENTREE_CAISSE', 'SORTIE_CAISSE']);
            $table->decimal('montant', 10, 2);
            $table->enum('mode_paiement', ['especes', 'carte', 'cheque', 'mixte'])->default('especes');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->date('date_operation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caisse_operations');
    }
};
