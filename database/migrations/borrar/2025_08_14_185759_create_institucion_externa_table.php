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
        Schema::create('institucion_externa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('telefono', 50)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('observaciones', 1000)->nullable();
            $table->string('observaciones_baja', 1000)->nullable();
            $table->string('observaciones_reincorporar', 1000)->nullable();
            $table->string('usr_alta',50)->nullable();
            $table->string('usr_mod',50)->nullable();
            $table->unsignedTinyInteger('estado_id');
            $table->timestamps();
            $table->softDeletes();

            // Índices básicos
            $table->index('usr_alta');

            $table->foreign('estado_id')
                ->references('id')->on('estado_ocurrencias')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        // Ahora agregar las columnas generadas con SQL directo
        DB::statement('
        ALTER TABLE institucion_externa
        ADD COLUMN nombre_normalizado VARCHAR(255)
        GENERATED ALWAYS AS (LOWER(TRIM(nombre))) STORED
        ');

        DB::statement('
        ALTER TABLE institucion_externa
        ADD COLUMN activo TINYINT UNSIGNED
        GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, 0)) STORED
        ');

        // Finalmente agregar la constraint unique
        DB::statement('
        ALTER TABLE institucion_externa
        ADD UNIQUE INDEX uniq_nombre_activo (nombre_normalizado, activo)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institucion_externa');
    }
};
