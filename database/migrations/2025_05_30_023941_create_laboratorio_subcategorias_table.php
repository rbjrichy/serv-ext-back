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
        Schema::create('laboratorio_subcategorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('usr_alta')->nullable();
            $table->string('usr_mod')->nullable();
            $table->string('observaciones_baja', 1000)->nullable();
            $table->string('observaciones_reincorporar', 1000)->nullable();
            $table->boolean('examen_externo')->default(false);
            $table->integer('tiempo_procesamiento');
            $table->unsignedBigInteger('laboratorio_categoria_id');
            $table->unsignedTinyInteger('estado_id')->default(17);
            $table->timestamps();

            $table->foreign('laboratorio_categoria_id')->references('id')->on('laboratorio_categorias')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('estado_ocurrencias')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorio_subcategorias');
    }
};
