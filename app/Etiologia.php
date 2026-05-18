<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etiologia extends Model
{
	use SoftDeletes;
    protected $table = 'etiologia';
    protected $dates = ['deleted_at'];
    
    public function etiologia(){
        return $this->belongsTo('App\Etiologia', 'etiologia_id');
    }
}
