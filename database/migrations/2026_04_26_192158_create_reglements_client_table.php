<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('reglements_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('facture_id')->nullable()->constrained('factures')->onDelete('set null');
            $table->decimal('montant', 12, 2);
            $table->string('type', 20); // 'dette' ou 'reglement'
            $table->string('mode_paiement', 60)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('reglements_client');
    }
};
