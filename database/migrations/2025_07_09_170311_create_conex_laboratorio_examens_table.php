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
        Schema::create('conex_laboratorio_examens', function (Blueprint $table) {
            $table->id();
            $table->string('resultado')->nullable();
            $table->string('usr_alta')->nullable();
            $table->string('usr_mod')->nullable();
            $table->string('observaciones_baja', 1000)->nullable();
            $table->unsignedBigInteger('laboratorio_solicitud_id');
            $table->unsignedBigInteger('laboratorio_examen_id');
            $table->unsignedTinyInteger('estado_id')->default(17);
            $table->timestamps();

            $table->foreign('laboratorio_solicitud_id')->references('id')->on('laboratorio_solicituds')->onDelete('cascade');
            $table->foreign('laboratorio_examen_id')->references('id')->on('laboratorio_examens')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('estado_ocurrencias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conex_laboratorio_examens');
    }
};
