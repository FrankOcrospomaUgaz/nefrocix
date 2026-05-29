<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLaboratorioRangosReferencialesTable extends Migration
{
    public function up()
    {
        Schema::create('laboratorio_rangos_referenciales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clave', 60)->unique();
            $table->string('seccion', 100);
            $table->string('nombre', 150);
            $table->string('unidad', 50);
            $table->string('rango_referencial', 255);
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $now = date('Y-m-d H:i:s');
        $datos = array(
            array('clave' => 'leucocitos', 'seccion' => 'HEMOGRAMA COMPLETO', 'nombre' => 'Recuento de Leucocitos', 'unidad' => '10^9/L', 'rango_referencial' => '4.00 - 10.00', 'orden' => 1, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hematies', 'seccion' => 'HEMOGRAMA COMPLETO', 'nombre' => 'Recuento de Hematies', 'unidad' => '10^12/L', 'rango_referencial' => '3.50 - 5.50', 'orden' => 2, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'plaquetas', 'seccion' => 'HEMOGRAMA COMPLETO', 'nombre' => 'Recuento de Plaquetas', 'unidad' => '10^9/L', 'rango_referencial' => '140 - 440', 'orden' => 3, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hemoglobina', 'seccion' => 'HEMOGRAMA COMPLETO', 'nombre' => 'Hemoglobina', 'unidad' => 'g/dL', 'rango_referencial' => 'M: (12.5 - 15.1), H: (12.5 - 15.8)', 'orden' => 4, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hematocrito', 'seccion' => 'HEMOGRAMA COMPLETO', 'nombre' => 'Hematocrito', 'unidad' => '%', 'rango_referencial' => 'M: (36 - 47), H: (38 - 48)', 'orden' => 5, 'created_at' => $now, 'updated_at' => $now),

            array('clave' => 'vcm', 'seccion' => 'CONSTANTES CORPUSCULARES', 'nombre' => 'VCM', 'unidad' => 'fL', 'rango_referencial' => '80 - 100', 'orden' => 6, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hcm', 'seccion' => 'CONSTANTES CORPUSCULARES', 'nombre' => 'HCM', 'unidad' => 'pg', 'rango_referencial' => '27 - 34', 'orden' => 7, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'ccmh', 'seccion' => 'CONSTANTES CORPUSCULARES', 'nombre' => 'CCMH', 'unidad' => 'g/dL', 'rango_referencial' => '32 - 36', 'orden' => 8, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'rdw', 'seccion' => 'CONSTANTES CORPUSCULARES', 'nombre' => 'RDW - Indice de Anisocitosis (%)', 'unidad' => '%', 'rango_referencial' => '11 - 16', 'orden' => 9, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'rdw_sd', 'seccion' => 'CONSTANTES CORPUSCULARES', 'nombre' => 'RDW - Indice de Anisocitosis (SD)', 'unidad' => 'fL', 'rango_referencial' => '35 - 56', 'orden' => 10, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'vpm', 'seccion' => 'CONSTANTES CORPUSCULARES', 'nombre' => 'VPM', 'unidad' => 'fL', 'rango_referencial' => '7 - 11', 'orden' => 11, 'created_at' => $now, 'updated_at' => $now),

            array('clave' => 'abastonados_rel', 'seccion' => 'FORMULA LEUCOCITARIA (REL)', 'nombre' => 'Abastonados', 'unidad' => '%', 'rango_referencial' => '0.0 - 5.0', 'orden' => 12, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'segmentados_rel', 'seccion' => 'FORMULA LEUCOCITARIA (REL)', 'nombre' => 'Segmentados', 'unidad' => '%', 'rango_referencial' => '45.0 - 74.0', 'orden' => 13, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'eosinofilos_rel', 'seccion' => 'FORMULA LEUCOCITARIA (REL)', 'nombre' => 'Eosinofilos', 'unidad' => '%', 'rango_referencial' => '0.0 - 4.4', 'orden' => 14, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'basofilos_rel', 'seccion' => 'FORMULA LEUCOCITARIA (REL)', 'nombre' => 'Basofilos', 'unidad' => '%', 'rango_referencial' => '1.0 - 1.2', 'orden' => 15, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'monocitos_rel', 'seccion' => 'FORMULA LEUCOCITARIA (REL)', 'nombre' => 'Monocitos', 'unidad' => '%', 'rango_referencial' => '0.7 - 7.5', 'orden' => 16, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'linfocitos_rel', 'seccion' => 'FORMULA LEUCOCITARIA (REL)', 'nombre' => 'Linfocitos', 'unidad' => '%', 'rango_referencial' => '22.3 - 49.9', 'orden' => 17, 'created_at' => $now, 'updated_at' => $now),

            array('clave' => 'abastonados_abs', 'seccion' => 'FORMULA LEUCOCITARIA (ABS)', 'nombre' => 'Abastonados', 'unidad' => '10^9/L', 'rango_referencial' => '0.15 - 0.40', 'orden' => 18, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'segmentados_abs', 'seccion' => 'FORMULA LEUCOCITARIA (ABS)', 'nombre' => 'Segmentados', 'unidad' => '10^9/L', 'rango_referencial' => '2.00 - 7.80', 'orden' => 19, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'eosinofilos_abs', 'seccion' => 'FORMULA LEUCOCITARIA (ABS)', 'nombre' => 'Eosinofilos', 'unidad' => '10^9/L', 'rango_referencial' => '0.02 - 0.35', 'orden' => 20, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'basofilos_abs', 'seccion' => 'FORMULA LEUCOCITARIA (ABS)', 'nombre' => 'Basofilos', 'unidad' => '10^9/L', 'rango_referencial' => '0.01 - 0.05', 'orden' => 21, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'monocitos_abs', 'seccion' => 'FORMULA LEUCOCITARIA (ABS)', 'nombre' => 'Monocitos', 'unidad' => '10^9/L', 'rango_referencial' => '0.1 - 0.5', 'orden' => 22, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'linfocitos_abs', 'seccion' => 'FORMULA LEUCOCITARIA (ABS)', 'nombre' => 'Linfocitos', 'unidad' => '10^9/L', 'rango_referencial' => '0.80 - 4.00', 'orden' => 23, 'created_at' => $now, 'updated_at' => $now),

            array('clave' => 'urea_pre', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Urea Pre', 'unidad' => 'mg/dL', 'rango_referencial' => '10 - 50', 'orden' => 24, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'urea_post', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Urea Post', 'unidad' => 'mg/dL', 'rango_referencial' => '10 - 50', 'orden' => 25, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'creatinina_pre', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Creatinina Pre', 'unidad' => 'mg/dL', 'rango_referencial' => 'M: (0.5-0.9), H: (0.6-1.1)', 'orden' => 26, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'creatinina_post', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Creatinina Post', 'unidad' => 'mg/dL', 'rango_referencial' => 'M: (0.5-0.9), H: (0.6-1.1)', 'orden' => 27, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'tgo', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'TGO', 'unidad' => 'U/L', 'rango_referencial' => 'M: Hasta 31, H: Hasta 37', 'orden' => 28, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'tgp', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'TGP', 'unidad' => 'U/L', 'rango_referencial' => 'M: Hasta 34, H: Hasta 45', 'orden' => 29, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'proteinas_totales', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Proteinas Totales', 'unidad' => 'g/dL', 'rango_referencial' => '6.4 - 8.3', 'orden' => 30, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'albumina', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Albumina', 'unidad' => 'g/dL', 'rango_referencial' => '3.5 - 5.2', 'orden' => 31, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'fal', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Fosfatasa alcalina', 'unidad' => 'U/L', 'rango_referencial' => 'M: (42-141), H: (53-128)', 'orden' => 32, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'ferritina', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Ferritina', 'unidad' => 'ng/mL', 'rango_referencial' => 'M: (10-124), H: (16-220)', 'orden' => 33, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hierro', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Hierro', 'unidad' => 'ug/dL', 'rango_referencial' => 'M: (50-170), H: (65-175)', 'orden' => 34, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'transferrina', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Transferrina', 'unidad' => 'ug/dL', 'rango_referencial' => '250.00 - 400.00', 'orden' => 35, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'sat_transferrina', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Saturacion de Transferrina', 'unidad' => '%', 'rango_referencial' => '20.0 - 55.0', 'orden' => 36, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'parathormona', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Parathormona', 'unidad' => 'pg/mL', 'rango_referencial' => '15.00 - 65.00', 'orden' => 37, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'pcr', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Proteina C Reactiva', 'unidad' => 'mg/L', 'rango_referencial' => 'Positivo > 5.0, Negativo < 5.0', 'orden' => 38, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'colesterol', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Colesterol', 'unidad' => 'mg/dL', 'rango_referencial' => 'Optimo: <200, Moderado: 200-239, Alto: >239', 'orden' => 39, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'trigliceridos', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Trigliceridos', 'unidad' => 'mg/dL', 'rango_referencial' => 'Normal: <150, Alto: 200-499, Muy alto: >500', 'orden' => 40, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hdl', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'HDL Colesterol', 'unidad' => 'mg/dL', 'rango_referencial' => 'Bajo: < 40, Alto: >= 60', 'orden' => 41, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'ldl', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'LDL Colesterol', 'unidad' => 'mg/dL', 'rango_referencial' => 'Optimo: <100, Moderado: 130-159, Alto: 160-189, Muy Alto: >189', 'orden' => 42, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'calcio', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Calcio', 'unidad' => 'mg/dL', 'rango_referencial' => '8.5 - 10.5', 'orden' => 43, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'fosforo', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Fosforo', 'unidad' => 'mg/dL', 'rango_referencial' => '2.5 - 5.5', 'orden' => 44, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hbsag', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'HBsAg (Antigeno de superficie)', 'unidad' => 'COI', 'rango_referencial' => '(No reactivo < .09) (Indeterminado 0.9-1.1) (Reactivo > 1.0)', 'orden' => 45, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'hcv', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Anti HCV (Hepatitis C anticuerpos)', 'unidad' => 'COI', 'rango_referencial' => '(No reactivo < .09) (Indeterminado 0.9-1.1) (Reactivo > 1.0)', 'orden' => 46, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'anti_hbc', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Anti HBc Core Total', 'unidad' => 'COI', 'rango_referencial' => 'Reactivo <= 1.0, No Reactivo > 1.0', 'orden' => 47, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'anti_hbs', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Anti HBsAg', 'unidad' => 'mIU/mL', 'rango_referencial' => 'Estado inmune: > 10', 'orden' => 48, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'ac_urico', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Acido urico', 'unidad' => 'mg/dL', 'rango_referencial' => 'Mujeres: 2.6 - 6.0, Hombres: 3.4 - 7.2', 'orden' => 49, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'ac_folico', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Acido folico', 'unidad' => 'ng/mL', 'rango_referencial' => 'Mujeres: 4.8 - 37.3, Hombres: 4.5 - 32.2', 'orden' => 50, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'vitamina_b12', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'Vitamina B12', 'unidad' => 'pg/mL', 'rango_referencial' => '211-946', 'orden' => 51, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'vih', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'VIH', 'unidad' => 'COI', 'rango_referencial' => 'Negativo: 0 - 0.9, Indeterminado 0.9 - 1.1, Positivo: > 1.1', 'orden' => 52, 'created_at' => $now, 'updated_at' => $now),
            array('clave' => 'vdrl', 'seccion' => 'OTROS ANALISIS', 'nombre' => 'VDRL', 'unidad' => 'DILS', 'rango_referencial' => 'Reactivo >= 2', 'orden' => 53, 'created_at' => $now, 'updated_at' => $now),
        );

        DB::table('laboratorio_rangos_referenciales')->insert($datos);
    }

    public function down()
    {
        Schema::drop('laboratorio_rangos_referenciales');
    }
}
