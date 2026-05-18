<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultaNefrologica extends Model
{
    use SoftDeletes;
    protected $table = 'consultanefrologica';
    protected $dates = ['deleted_at'];

    public function reponsableformato()
    {
        return $this->belongsTo('App\Person', 'responsableformato_id');
    }

    public function persona()
    {
        return $this->belongsTo('App\Person', 'persona_id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Person', 'doctor_id');
    }
}
