<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsultaNefrologicaAnalisisAdicional extends Model
{
    protected $table = 'consultanefrologica_analisis_adicional';

    public function consultaNefrologica()
    {
        return $this->belongsTo('App\ConsultaNefrologica', 'consultanefrologica_id');
    }
}
