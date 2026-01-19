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
        Schema::create('inst_ext_serv', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institucion_ext_id');
            $table->unsignedBigInteger('servicio_id');

            $table->text('observaciones')->nullable();
            $table->unsignedTinyInteger('estado_id');

            $table->string('usr_mod',255)->nullable();
            $table->timestamps();


            $table->softDeletes();

            // Índices
            $table->index('institucion_ext_id');
            $table->index('servicio_id');
            $table->index('usr_mod');

            // Foreign keys locales
            $table->foreign('estado_id')
                ->references('id')->on('estado_ocurrencias')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('institucion_ext_id')
                ->references('id')->on('institucion_externa')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('servicio_id')
                ->references('id')->on('servicio_externo')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inst_ext_serv', function (Blueprint $table) {
            $table->dropForeign(['institucion_ext_id']);
            $table->dropForeign(['servicio_id']);
        });
        Schema::dropIfExists('inst_ext_serv');
    }
};
