<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EsExtServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('esext_servicios')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $estados = [
            3 => 33,
            6 => 32
        ];
        $personas = [
            2  => 'gmolina',
            3  => 'goku',
            5  => 'pcanseco',
            6  => 'mvelasco',
            7  => 'squisbert',
            8  => 'lgarcia',
            9  => 'mazurduy',
            10 => 'lvargas',
            11 => 'kcaballero',
            12 => 'mvelasco',
            13 => 'dmotos',
            14 => 'mdominguez',
            15 => 'mazurduy',
            16 => 'acaceres',
            17 => 'web',
            18 => 'web',
            19 => 'squisbert',
            20 => 'consulta',
            21 => 'ncalderon',
            22 => 'enfermeriassue',
            23 => 'biometrico',
            26 => 'biometrico',
            27 => 'jzambrana',
            28 => 'consultassue',
            30 => 'angelica',
            31 => 'izubieta',
            32 => 'jcampos',
            33 => 'czambrana',
            35 => 'cmaquera',
            36 => 'dchabarria',
            37 => 'knava',
            38 => 'enfermeriassu',
            41 => 'laboratorio',
            42 => 'guardiassu',
            43 => 'guardiassue',
            44 => 'fsantos',
            45 => 'equispe',
            46 => 'sdiaz',
            47 => 'informaciones',
            48 => 'avargas',
            49 => 'egardeazabal.fic',
            50 => 'drengifo',
            53 => 'vdaza',
            54 => 'lrodriguez',
            55 => 'ichavez',
            56 => 'fabiola.santos',
            58 => 'wcuellar',
            59 => 'ymendez',
            60 => 'cnava',
        ];

        // Leer desde la base SQL Server (conexión 'origen')
        $registros = DB::connection('sqlsrv_origen')
            ->table('ESEXT_SERVICIO')
            ->orderBy('COD_SERVICIO')
            ->get();

        foreach ($registros as $registro) {
            // Buscar nombre del usuario desde tabla de usuarios en SQL Server
            $nombreUsuario = DB::connection('sqlsrv_origen')
                ->table('USUARIOS') // Ajusta si el nombre real es distinto
                ->where('COD_USUARIO', $registro->USUARIO)
                ->value('USUARIO'); // según el campo disponible

            DB::table('esext_servicios')->insert([
                'id' => $registro->COD_SERVICIO,
                'servicio' => $registro->SERVICIO,
                'observaciones' => $registro->OBSERVACIONES,
                'estado_id' => $estados[$registro->ESTADO],
                'usr_alta' => $nombreUsuario ?? $personas[$registro->USUARIO],
                'fecha_registro' => $registro->FECHA_REGISTRO
                    ? Carbon::parse($registro->FECHA_REGISTRO)
                    : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ajustar el AUTO_INCREMENT si es necesario
        $maxId = DB::table('esext_servicios')->max('id');
        DB::statement("ALTER TABLE esext_servicios AUTO_INCREMENT = " . ($maxId + 1));

    }
}
