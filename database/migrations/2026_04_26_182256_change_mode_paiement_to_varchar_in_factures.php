<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE factures MODIFY mode_paiement VARCHAR(60) NOT NULL DEFAULT 'especes'");
        DB::statement("ALTER TABLE caisse_operations MODIFY mode_paiement VARCHAR(60) NOT NULL DEFAULT 'especes'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE factures MODIFY mode_paiement ENUM('especes','carte','cheque','mixte','credit') NOT NULL DEFAULT 'especes'");
        DB::statement("ALTER TABLE caisse_operations MODIFY mode_paiement ENUM('especes','carte','cheque','mixte') NOT NULL DEFAULT 'especes'");
    }
};
