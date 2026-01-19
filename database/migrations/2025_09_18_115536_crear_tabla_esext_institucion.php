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
    Schema::create('esext_instituciones', function (Blueprint $table) {
        $table->id(); // Equivale a BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
        $table->string('institucion', 80);
        $table->string('telefono', 80)->nullable();
        $table->string('direccion', 200)->nullable();
        $table->string('observaciones', 200)->nullable();
        $table->string('observaciones_baja', 200)->nullable();
        $table->string('observaciones_reincorporar', 200)->nullable();
        $table->string('usr_alta',50)->nullable();
        $table->string('usr_mod',50)->nullable();
        $table->unsignedTinyInteger('estado_id')->nullable(); // FK hacia estado_ocurrencias.id
        $table->timestamp('fecha_registro')->nullable();
        $table->timestamps();

        // Clave foránea
        $table->foreign('estado_id')
              ->references('id')
              ->on('estado_ocurrencias')
              ->onDelete('set null'); // Opcional: define comportamiento al eliminar estado

        // Asegura que el motor sea InnoDB
        $table->engine = 'InnoDB';
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esext_instituciones');
    }
};
