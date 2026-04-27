<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('solde', 12, 2)->default(0)->after('points_fidelite');
        });
    }
    public function down(): void {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('solde');
        });
    }
};
