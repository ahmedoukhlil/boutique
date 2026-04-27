<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE factures MODIFY statut VARCHAR(30) NOT NULL DEFAULT 'payee'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE factures MODIFY statut ENUM('brouillon','payee','partielle','annulee') NOT NULL DEFAULT 'payee'");
    }
};
