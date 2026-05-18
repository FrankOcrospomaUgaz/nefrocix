<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = 'distrito';
    protected $dates = ['deleted_at'];

    public function provincia(){
        return $this->belongsTo('App\Provincia', 'provincia_id');
    }
}
