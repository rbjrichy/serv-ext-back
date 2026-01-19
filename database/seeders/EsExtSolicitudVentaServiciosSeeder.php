<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EsExtSolicitudVentaServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('esext_solicitudventaservicios')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::connection('sqlsrv_origen')
            ->table('ESEXT_SOLICITUDVENTASERVICIOS')
            ->orderBy('COD_SOLICITUD')
            ->chunkById(1000, function ($registros) {
                foreach ($registros as $registro) {
                    $validaciones = [
                        'clasificacion_servicios' => DB::table('clasificacion_servicios')->where('id', $registro->COD_TIPOSERVICIO)->exists(),
                        'esext_servicio_institucion_id' => DB::table('esext_servicios_instituciones')->where('id', $registro->COD_INST_SER)->exists(),
                        'estado_ocurrencias' => DB::table('estado_ocurrencias')->where('id', $registro->COD_ESTADO)->exists(),
                        'consulta_externas' => DB::table('consulta_externas')->where('id', $registro->COD_CONEX)->exists(),
                    ];

                    // Encuentra el fi_med_esps_id de la base de datos 'sistema_ssu'.
                    $fiMedEspsId = DB::connection('mysql_sistema_ssu')
                        ->table('fi_med_esps')
                        ->where('medico_id', $registro->COD_MEDSOLICITANTE)
                        ->where('subespecialidad_id', $registro->COD_ESPECIALIDAD)
                        ->value('id');

                    // Agregamso al array de validaciones
                    $validaciones['fi_med_esps_id'] = ($fiMedEspsId !== null);

                    $valido = !in_array(false, $validaciones);

                    if (!$valido) {
                        Log::warning("❌ Claves inválidas para la solicitud ID {$registro->COD_SOLICITUD}. Errores: " . json_encode(array_keys(array_filter($validaciones, fn($value) => $value === false))));
                        continue;
                    }

                    // Encuentra el nombre de usuario y el nombre del modificador
                    $nombreUsuario = DB::connection('sqlsrv_origen')
                        ->table('USUARIOS')
                        ->where('COD_USUARIO', $registro->USUARIO)
                        ->value('NOMBRES');

                    $nombreModificador = DB::connection('sqlsrv_origen')
                        ->table('USUARIOS')
                        ->where('COD_USUARIO', $registro->USU_MODIFICADO)
                        ->value('NOMBRES');

                    DB::table('esext_solicitudventaservicios')->insert([
                        'id' => $registro->COD_SOLICITUD,
                        'fecha_solicitud' => $registro->FECHA_SOLICITUD,
                        'entidad_id' => $registro->COD_ENTIDAD,
                        'titular_persona_id' => $registro->CODPER_TITULAR,
                        'paciente_persona_id' => $registro->COD_PER_PACIENTE,
                        'contrato_id' => $registro->COD_CONTRATO,
                        'fi_med_esps_id' => $fiMedEspsId,
                        'clasificacion_servicio_id' => $registro->COD_TIPOSERVICIO,
                        'esext_servicio_institucion_id' => $registro->COD_INST_SER,
                        'observaciones' => $registro->OBSERVACIONES,
                        'estado_id' => $registro->COD_ESTADO,
                        'fecha_registro' => $registro->FECHA_REGISTRO ? Carbon::parse($registro->FECHA_REGISTRO) : null,
                        'usuario' => $nombreUsuario ?? 'id=' . $registro->USUARIO,
                        'solicitante' => $registro->SOLICITANTE,
                        'cargo' => $registro->CARGO,
                        'diagnostico' => $registro->DIAGNOSTICO,
                        'fecha_modificado' => $registro->FECHA_MODIFICADO ? Carbon::parse($registro->FECHA_MODIFICADO) : null,
                        'usr_mod' => $nombreModificador ?? 'id=' . $registro->USU_MODIFICADO,
                        'justificacion_mod' => $registro->JUST_MODIFICADO,
                        'consulta_externa_id' => $registro->COD_CONEX,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }, 'COD_SOLICITUD');

        // Ajusta AUTO_INCREMENT
        $maxId = DB::table('esext_solicitudventaservicios')->max('id');
        if ($maxId) {
            DB::statement("ALTER TABLE esext_solicitudventaservicios AUTO_INCREMENT = " . ($maxId + 1));
        }
    }
}
