<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create("marques", function (Blueprint $table) {
            $table->id();
            $table->string("nom");
            $table->string("slug")->unique();
            $table->string("logo")->nullable();
            $table->string("pays_origine")->nullable();
            $table->boolean("actif")->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("marques"); }
};