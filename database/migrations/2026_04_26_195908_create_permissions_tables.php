<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();
            $table->string('label');
            $table->string('module');
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->string('role', 30);
            $table->string('permission_cle');
            $table->primary(['role', 'permission_cle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
