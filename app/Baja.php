<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Baja extends Model
{
	use SoftDeletes;
    protected $table = 'baja';
    protected $dates = ['deleted_at'];

    public function historia()
    {
        return $this->belongsTo('App\Historia', 'historia_id');
    }

    public function baja()
    {
        return $this->belongsTo('App\Baja', 'baja_id');
    }

    public function usuario()
    {
        return $this->belongsTo('App\Person', 'usuario_id');
    }
}
