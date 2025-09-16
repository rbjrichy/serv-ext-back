<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laboratorio_perfil_examens', function (Blueprint $table) {
            // Elimina las columnas existentes
            $table->dropColumn('usr_alta');
            $table->dropColumn('usr_mod');
            $table->dropColumn('observaciones_baja');
            $table->dropColumn('observaciones_reincorporar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratorio_perfil_examens', function (Blueprint $table) {
            // Vuelve a añadir las columnas (con las mismas definiciones que tenían)
            $table->string('usr_alta')->nullable();
            $table->string('usr_mod')->nullable();
            $table->string('observaciones_baja', 1000)->nullable();
            $table->string('observaciones_reincorporar', 1000)->nullable();
        });
    }
};
