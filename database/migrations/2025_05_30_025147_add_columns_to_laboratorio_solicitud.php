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
        Schema::table('laboratorio_solicituds', function (Blueprint $table) {
            $table->string('usr_alta')->nullable();
            $table->string('usr_mod')->nullable();
            $table->string('observaciones_anulado', 1000)->nullable();
            $table->string('observaciones_cambioestado', 1000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratorio_solicituds', function (Blueprint $table) {
            $table->dropColumn('usr_alta');
            $table->dropColumn('usr_mod');
            $table->dropColumn('observaciones_anulado');
            $table->dropColumn('observaciones_cambioestado');
        });
    }
};
