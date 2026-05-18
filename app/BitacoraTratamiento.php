<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BitacoraTratamiento extends Model
{
    protected $table = 'bitacoratratamiento';

    public function person()
    {
        return $this->belongsTo('App\Person', 'person_id');
    }

    public function historiaclinica()
    {
        return $this->belongsTo('App\HistoriaClinica', 'historiaclinica_id');
    }

    public function scopeNumeroSigue($query){
        $rs=$query->select(DB::raw("max((CASE WHEN numero IS NULL THEN 0 ELSE numero END)*1) AS maximo"))->first();
        return str_pad($rs->maximo+1,8,'0',STR_PAD_LEFT);  
    }
}
