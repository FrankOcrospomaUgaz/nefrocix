<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultanefrologicaAnalisisAdicionalTable extends Migration
{
    public function up()
    {
        Schema::create('consultanefrologica_analisis_adicional', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consultanefrologica_id');
            $table->string('nombre', 200);
            $table->string('resultado', 200)->nullable();
            $table->string('unidad', 50)->nullable();
            $table->string('rango_referencial', 300)->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->index('consultanefrologica_id', 'cn_aa_cn_idx');
        });
    }

    public function down()
    {
        Schema::drop('consultanefrologica_analisis_adicional');
    }
}
