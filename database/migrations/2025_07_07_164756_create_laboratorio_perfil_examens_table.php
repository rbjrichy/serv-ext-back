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
        Schema::create('laboratorio_perfil_examens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laboratorio_perfil_id');
            $table->unsignedBigInteger('laboratorio_subcategoria_id');
            $table->string('usr_alta')->nullable();
            $table->string('usr_mod')->nullable();
            $table->string('observaciones_baja', 1000)->nullable();
            $table->string('observaciones_reincorporar', 1000)->nullable();
            $table->timestamps();

            $table->foreign('laboratorio_perfil_id')->references('id')->on('laboratorio_perfils')->onDelete('cascade');
            $table->foreign('laboratorio_subcategoria_id')->references('id')->on('laboratorio_subcategorias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorio_perfil_examens');
    }
};
