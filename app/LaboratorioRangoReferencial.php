<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaboratorioRangoReferencial extends Model
{
	use SoftDeletes;
	protected $table = 'laboratorio_rangos_referenciales';
	protected $dates = ['deleted_at'];
}
