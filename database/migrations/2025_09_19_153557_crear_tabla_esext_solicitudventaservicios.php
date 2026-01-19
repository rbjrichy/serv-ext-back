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
    Schema::create('esext_solicitudventaservicios', function (Blueprint $table) {
        $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

        $table->date('fecha_solicitud')->nullable();
        $table->unsignedTinyInteger('entidad_id')->nullable();
        $table->unsignedBigInteger('titular_persona_id')->nullable();
        $table->unsignedBigInteger('paciente_persona_id')->nullable();
        $table->unsignedInteger('contrato_id')->nullable();
        $table->unsignedInteger('fi_med_esps_id')->nullable();
        $table->unsignedTinyInteger('clasificacion_servicio_id')->nullable();
        $table->unsignedBigInteger('esext_servicio_institucion_id')->nullable();
        $table->string('observaciones', 800)->nullable();
        $table->unsignedTinyInteger('estado_id')->nullable();
        $table->timestamp('fecha_registro')->nullable();
        $table->string('usuario')->nullable();
        $table->string('solicitante', 200)->nullable();
        $table->string('cargo', 200)->nullable();
        $table->string('diagnostico', 500)->nullable();
        $table->timestamp('fecha_modificado')->nullable();
        $table->string('usr_mod')->nullable();
        $table->string('justificacion_mod', 500)->nullable();
        $table->unsignedBigInteger('consulta_externa_id')->nullable();
        $table->timestamps();

        // Claves foráneas con comportamiento explícito
        $table->foreign('entidad_id')
              ->references('id')->on('entidads')
              ->onDelete('set null');

        $table->foreign('estado_id')
              ->references('id')->on('estado_ocurrencias')
              ->onDelete('set null');

        $table->foreign('clasificacion_servicio_id')
              ->references('id')->on('clasificacion_servicios')
              ->onDelete('set null');

        $table->foreign('esext_servicio_institucion_id', 'solventa_servinst_fk')
              ->references('id')->on('esext_servicios_instituciones')
              ->onDelete('set null');

        $table->foreign('consulta_externa_id')
              ->references('id')->on('consulta_externas')
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
        Schema::dropIfExists('esext_solicitudventaservicios');
    }
};
