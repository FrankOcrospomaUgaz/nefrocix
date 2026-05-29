<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLaboratorioEssaludFieldsToConsultanefrologica extends Migration
{
    public function up()
    {
        Schema::table('consultanefrologica', function (Blueprint $table) {
            $table->double('txtLeucocitos', 10, 3)->nullable();
            $table->double('txtHematies', 10, 3)->nullable();
            $table->double('txtPlaquetas', 10, 3)->nullable();
            $table->double('txtVcm', 10, 3)->nullable();
            $table->double('txtHcm', 10, 3)->nullable();
            $table->double('txtCcmh', 10, 3)->nullable();
            $table->double('txtRdw', 10, 3)->nullable();
            $table->double('txtRdwSd', 10, 3)->nullable();
            $table->double('txtVpm', 10, 3)->nullable();
            $table->double('txtAbastonados', 10, 3)->nullable();
            $table->double('txtSegmentados', 10, 3)->nullable();
            $table->double('txtEosinofilos', 10, 3)->nullable();
            $table->double('txtBasofilos', 10, 3)->nullable();
            $table->double('txtMonocitos', 10, 3)->nullable();
            $table->double('txtLinfocitos', 10, 3)->nullable();
            $table->double('txtColesterol', 10, 3)->nullable();
            $table->double('txtTrigliceridos', 10, 3)->nullable();
            $table->double('txtHdl', 10, 3)->nullable();
            $table->double('txtLdl', 10, 3)->nullable();
            $table->double('txtVitaminaB12', 10, 3)->nullable();
            $table->double('txtAcidoFolico', 10, 3)->nullable();
            $table->double('txtAcidoUrico', 10, 3)->nullable();
        });
    }

    public function down()
    {
        Schema::table('consultanefrologica', function (Blueprint $table) {
            $table->dropColumn([
                'txtLeucocitos',
                'txtHematies',
                'txtPlaquetas',
                'txtVcm',
                'txtHcm',
                'txtCcmh',
                'txtRdw',
                'txtRdwSd',
                'txtVpm',
                'txtAbastonados',
                'txtSegmentados',
                'txtEosinofilos',
                'txtBasofilos',
                'txtMonocitos',
                'txtLinfocitos',
                'txtColesterol',
                'txtTrigliceridos',
                'txtHdl',
                'txtLdl',
                'txtVitaminaB12',
                'txtAcidoFolico',
                'txtAcidoUrico',
            ]);
        });
    }
}
