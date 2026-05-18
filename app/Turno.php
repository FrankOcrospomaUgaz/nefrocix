<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turno extends Model
{
	use SoftDeletes;
    protected $table = 'turno';
    protected $dates = ['deleted_at'];
}
