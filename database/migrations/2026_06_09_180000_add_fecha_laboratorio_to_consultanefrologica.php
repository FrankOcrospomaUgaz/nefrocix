<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFechaLaboratorioToConsultanefrologica extends Migration
{
    public function up()
    {
        Schema::table('consultanefrologica', function (Blueprint $table) {
            $table->date('txtFechaLaboratorio')->nullable()->after('txtTipoDatos');
        });
    }

    public function down()
    {
        Schema::table('consultanefrologica', function (Blueprint $table) {
            $table->dropColumn('txtFechaLaboratorio');
        });
    }
}
