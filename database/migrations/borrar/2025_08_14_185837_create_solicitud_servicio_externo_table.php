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
        Schema::create('solicitud_servicio_externo', function (Blueprint $table) {
            $table->id();
            // Fecha de solicitud (por defecto ahora)
            $table->timestamp('fecha_solicitud')->useCurrent();

            // Referencias externas (no FK, vienen de otros módulos)
            $table->unsignedBigInteger('persona_afiliado_entidads_id')->nullable();
            $table->unsignedBigInteger('fi_med_esp_id')->nullable();

            // Relación local hacia inst_ext_serv
            $table->unsignedBigInteger('inst_ext_serv_id');

            $table->text('diagnostico')->nullable();

            // Referencia externa a entidad
            $table->unsignedBigInteger('entidads_id')->nullable();

            $table->string('tipo_solicitud', 20)->default('manual'); // medico, manual, laboratorio

            $table->integer('codigo_consulta')->nullable(); // sólo para tipo_solicitud = 'medico'

            // Usuario creador (externo)
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();

            $table->timestamps();

            $table->softDeletes();

            // Índices para consultas rápidas
            $table->index('persona_afiliado_entidads_id');
            $table->index('fi_med_esp_id');
            $table->index('inst_ext_serv_id');
            $table->index('entidads_id');
            $table->index('tipo_solicitud');

            // FK local hacia inst_ext_serv
            $table->foreign('inst_ext_serv_id')
                ->references('id')->on('inst_ext_serv')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_servicio_externo', function (Blueprint $table) {
            $table->dropForeign(['inst_ext_serv_id']);
        });

        Schema::dropIfExists('solicitud_servicio_externo');
    }
};
