<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servicio_externo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_servicio', 255);
            // Si tienes una tabla tipo_servicio, convertir esto en FK más adelante
            $table->unsignedBigInteger('tipo_servicio_id')->nullable();

            $table->text('observaciones')->nullable();
            $table->unsignedTinyInteger('estado_id');

            // Referencia externa (usuario creador)
            $table->string('usr_alta',255)->nullable();
            $table->timestamps();

            $table->softDeletes();
            $table->index('usr_alta');

            $table->foreign('estado_id')
                ->references('id')->on('estado_ocurrencias')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        DB::statement('
        ALTER TABLE servicio_externo
        ADD COLUMN nombre_servicio_normalizado VARCHAR(255)
        GENERATED ALWAYS AS (LOWER(TRIM(nombre_servicio))) STORED
        ');

        DB::statement('
        ALTER TABLE servicio_externo
        ADD COLUMN activo TINYINT UNSIGNED
        GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, 0)) STORED
        ');

        // Finalmente agregar la constraint unique
        DB::statement('
        ALTER TABLE servicio_externo
        ADD UNIQUE INDEX uniq_nombre_servicio_activo (nombre_servicio_normalizado, activo)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_externo');
    }
};
