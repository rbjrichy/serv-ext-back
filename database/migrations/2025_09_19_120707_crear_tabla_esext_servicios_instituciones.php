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
    Schema::create('esext_servicios_instituciones', function (Blueprint $table) {
        $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
        $table->unsignedBigInteger('esext_institucion_id'); // FK hacia esext_instituciones.id
        $table->unsignedBigInteger('esext_servicio_id');    // FK hacia esext_servicios.id
        $table->decimal('costo', 10, 2)->nullable();
        $table->string('observaciones', 300)->nullable();
        $table->string('observaciones_baja', 200)->nullable();
        $table->string('observaciones_reincorporar', 200)->nullable();
        $table->string('usr_alta',50)->nullable();
        $table->string('usr_mod',50)->nullable();
        $table->date('fecha_inicio')->nullable();
        $table->date('fecha_fin')->nullable();
        $table->timestamp('fecha_registro')->nullable();
        $table->unsignedTinyInteger('estado_id')->nullable(); // FK hacia estado_ocurrencias.id
        $table->unsignedTinyInteger('entidad_id')->nullable(); // FK hacia entidads.id
        $table->timestamps();

        // Claves foráneas con comportamiento explícito
        $table->foreign('esext_institucion_id')
              ->references('id')->on('esext_instituciones')
              ->onDelete('cascade');

        $table->foreign('esext_servicio_id')
              ->references('id')->on('esext_servicios')
              ->onDelete('cascade');

        $table->foreign('estado_id')
              ->references('id')->on('estado_ocurrencias')
              ->onDelete('set null');

        $table->foreign('entidad_id')
              ->references('id')->on('entidads')
              ->onDelete('set null');

        // Asegura que el motor sea InnoDB
        $table->engine = 'InnoDB';
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esext_servicios_instituciones');
    }
};
