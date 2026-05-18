<?php
use App\ConsultaNefrologica;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set('America/Lima');
$mes = $messs;
$mes2 = ($messs==12?1:($messs+1));
$ano = $anooo;
$ano2 = ($messs==12?($anooo+1):$anooo);
$meses_ES = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SETIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
$mesl = $meses_ES[$mes-1];
$mes2 = $meses_ES[$mes2-1];
$user=Auth::user();
$uladech = $user->usertype_id;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if($value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	@if($uladech!==31&&$uladech!==32)
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr @if($value->baja=="S") style="color:red" title="DADO DE BAJA {{$value->fallecido=='S'?', FALLECIDO EL '.date('d-m-Y', strtotime($value->fechafallecido)):'NO FALLECIDO'}}"  @endif >
			<td width="2%" style="font-size:12px">{{ $contador }}</td>
			<td width="5%">{{ $value->numero }}</td>
			<td width="5%">{{ $value->dni }}</td>
			<td width="25%" style="font-size:12px">{{ $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres }}</td>
			<td width="20%" style="font-size:12px">
				<center>
					<a href="#" onclick='modal("consultanefrologica/cambiarDoctor?id={{$value->cid}}&doctor_id={{$value->doctor_id}}", "<b>EDITAR MÉDICO</b>", this);' class="btn btn-xs btn-success">
						<i class="fa fa-user"></i> Ver
					</a>
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								if($c1->estadoexamen==1) {
									$sms = '<b style="color:green;"><i class="fa fa-check"></i></b>';
								} else {
									$sms = '<b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
								}
								if($user->usertype_id==2) {
									/////////////REPORTE DE HISTORIAL///////////////
									echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exáms.</a>&nbsp;';
									////////////////////////////
								}
								echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=' . $c1->situacion . '&cid=' . $c1->id . '&anillo=' . $ano . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a> ' . $sms;
								


							} else {
								echo '-</center></td><td width="10%"><center>-</b>';
							}
						} else {
							echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=NUEVO&cid=' . $c1->id . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a></center></td><td width="16%"><center><b style="color:orange">NUEVO</b> <b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
						}
					?>
				</center>
			</td>
			@if($user->usertype_id==2||$user->usertype_id==1)
				<td width="10%"><center>
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								if($c1->estadoexamen==1) {
									$sms = '<b style="color:green;"><i class="fa fa-check"></i></b>';
								} else {
									$sms = '<b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
								}
								if($user->usertype_id==2) {
									/////////////REPORTE DE HISTORIAL///////////////
									echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exáms.</a>&nbsp;';
									////////////////////////////
								}
								echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=' . $c1->situacion . '&cid=' . $c1->id . '&anillo=' . $ano . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a> ' . $sms . '</center></td><td width="10%"><center><b style="color:green">' . $c1->situacion  . " (" . $c1->txtTipoDatos . ")" . '</b>';
								


							} else {
								echo '-</center></td><td width="10%"><center>-</b>';
							}
						} else {
							echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=NUEVO&cid=' . $c1->id . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a></center></td><td width="16%"><center><b style="color:orange">NUEVO</b> <b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
						}
					?></center>
				</td>
				<td width="10%"><center><b style="color:blue;">
				<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								echo $c1->situacion2 . " (" . ($c1->txtTipoDatos<5?($c1->txtTipoDatos+1):0) . ")";							
							} else {
								echo '-';
							}
						} else {
							echo '-';
						}
					?></b></center>
				</td>
			@endif
			@if($user->usertype_id==28||$user->usertype_id==29)
				<td width="10%"><center><b style="color:green;">
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								echo $c1->situacion . " (" . $c1->txtTipoDatos . ")";							
							} else {
								echo '-';
							}
						} else {
							echo '-';
						}
					?></b></center>
				</td>
				<td width="10%"><center><b style="color:blue;">
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								echo $c1->situacion2 . " (" . ($c1->txtTipoDatos<5?($c1->txtTipoDatos+1):0) . ")";							
							} else {
								echo '-';
							}
						} else {
							echo '-';
						}
					?></b></center>
				</td>
			@endif
			@if($user->usertype_id!==2)
				<td width="20%"><center>
					<?php
						echo '<a href="#" onclick="modal(\'consultamensual/reporte2?pid=' . $value->pid . '&mes=' . $mes . '&ano=' . $ano . '\', \'<b>ATENCIÓN NEFROLÓGICA DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-primary"><i class="fa fa-file"></i> Llenar atención</a>';
						$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
						if($c1 !== NULL) {
							echo '&nbsp;<a href="#" onclick="reporteConsultaMensual(2, ' . $c1->id . ');" class="btn btn-xs btn-default" title="Reporte de Atención mensual"><i class="fa fa-file"></i> Rpte. Atención</a>';						
							if($c1->tiempoenf==NULL) {
								echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
							} else {
								echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
							}

							if($user->usertype_id!==28&&$user->usertype_id!==29) {				
							
								/////////////REPORTE DE HISTORIAL///////////////
								echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exáms.</a>';
								////////////////////////////
							}
							echo '&nbsp;<a href="#" onclick="ImprimirReceta(\'' . $c1->id . '\');" class="btn btn-xs btn-success" title="Imprimir Receta"><i class="fa fa-print"></i> Receta</a>';
							if($user->usertype_id==1||$user->usertype_id==34) {
								echo '&nbsp;<a href="#" onclick="modal(\'historiaclinica/reporte?mensual=SI&prestacion=SI&formato=2&hid=' . $c1->id . '\', \'Formato de Atención de '. $value->apellidopaterno . ' ' . $value->apellidomaterno. ' ' . $value->nombres .'\', this);" class="btn btn-xs btn-danger" title="Formato de Atención"><i class="fa fa-file"></i> FUA</a>';
							
								if($c1->numeroformato!==NULL) {
									echo "&nbsp;<button class='btn btn-success btn-xs' title='Reporte de Formato de Atención' onclick='reporteformatoo(\"".$c1->id."\", 2);' type='button'><i class='fa fa-file'></i></button>";
									echo '&nbsp;<i class="fa fa-check" style="color:green"></i>';
								} else {
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
								}
							}
						} else {
							echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
							echo '&nbsp;<a title="Reporte de Atención mensual" href="#" onclick="#" class="btn btn-xs btn-default" disabled="disabled"><i class="fa fa-file"></i></a>';						
							echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Imprimir Receta" disabled="disabled"><i class="fa fa-print"></i></a>';
							if($user->usertype_id==1||$user->usertype_id==34) {
								echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-danger" title="Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
								echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
							}
						}					
					?></center>
				</td>
				<td width="23%"><center>
					<?php					
						$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->where('estado', '=', '1')->first();
						$situacion = '';
						$sms = '';
						if($c1 !== NULL) {
							if($c1->estadoprogramacion==1) {
								$sms = '<b style="color:green;"><i class="fa fa-check"></i></b>';
							} else {
								$sms = '<b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
							}
							echo '<a href="#" onclick="modal(\'consultanefrologica/programarmedicamentos?pid=' . $value->pid . '&situacion=' . $c1->situacion . '&cid=' . $c1->id . '\', \'<b>PROGRAMAR MEDICAMENTOS PARA ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;PARA EL MES: ' . $mesl .  ' DEL ' . $ano2 . '</b>\', this);" class="btn btn-xs btn-warning"><i class="fa fa-file"></i> Programar</a> ' . $sms . '</center>';							
						} else {
							echo '-';
						}
					?></center>
				</td>
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
	@endif
	@if($uladech==31)
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr @if($value->baja=="S") style="color:red" title="DADO DE BAJA {{$value->fallecido=='S'?', FALLECIDO EL '.date('d-m-Y', strtotime($value->fechafallecido)):'NO FALLECIDO'}}"  @endif >
			<td width="2%" style="font-size:12px">{{ $contador }}</td>
			<td width="5%">{{ $value->numero }}</td>
			<td width="5%">{{ $value->dni }}</td>
			<td width="25%" style="font-size:12px">{{ $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres }}</td>
			@if($user->usertype_id!==2)
				<td width="20%"><center>
					<?php
						$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
						if($c1 !== NULL) {
							echo '&nbsp;<a href="#" onclick="reporteConsultaMensual(2, ' . $c1->id . ');" class="btn btn-xs btn-default" title="Reporte de Atención mensual"><i class="fa fa-file"></i> Rpte. Atención</a>';						
							if($c1->tiempoenf==NULL) {
								echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
							} else {
								echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
							}

							if($user->usertype_id!==28&&$user->usertype_id!==29) {				
							
								/////////////REPORTE DE HISTORIAL///////////////
								echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exáms.</a>';
								////////////////////////////
							}
							echo '&nbsp;<a href="#" onclick="ImprimirReceta(\'' . $c1->id . '\');" class="btn btn-xs btn-success" title="Imprimir Receta"><i class="fa fa-print"></i> Receta</a>';
							if($user->usertype_id==1||$user->usertype_id==34) {
								echo '&nbsp;<a href="#" onclick="modal(\'historiaclinica/reporte?mensual=SI&prestacion=SI&formato=2&hid=' . $c1->id . '\', \'Formato de Atención de '. $value->apellidopaterno . ' ' . $value->apellidomaterno. ' ' . $value->nombres .'\', this);" class="btn btn-xs btn-danger" title="Formato de Atención"><i class="fa fa-file"></i> FUA</a>';
							
								if($c1->numeroformato!==NULL) {
									echo "&nbsp;<button class='btn btn-success btn-xs' title='Reporte de Formato de Atención' onclick='reporteformatoo(\"".$c1->id."\", 2);' type='button'><i class='fa fa-file'></i></button>";
									echo '&nbsp;<i class="fa fa-check" style="color:green"></i>';
								} else {
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
								}
							}
						} else {
							echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
							echo '&nbsp;<a title="Reporte de Atención mensual" href="#" onclick="#" class="btn btn-xs btn-default" disabled="disabled"><i class="fa fa-file"></i></a>';						
							echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Imprimir Receta" disabled="disabled"><i class="fa fa-print"></i></a>';
							if($user->usertype_id==1||$user->usertype_id==34) {
								echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-danger" title="Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
								echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
							}
						}					
					?></center>
				</td>
				<td width="23%"><center>
					<?php					
						$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->where('estado', '=', '1')->first();
						$situacion = '';
						$sms = '';
						if($c1 !== NULL) {
							if($c1->estadoprogramacion==1) {
								$sms = '<b style="color:green;"><i class="fa fa-check"></i></b>';
							} else {
								$sms = '<b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
							}
							echo '<a href="#" onclick="modal(\'consultanefrologica/programarmedicamentos?pid=' . $value->pid . '&situacion=' . $c1->situacion . '&cid=' . $c1->id . '\', \'<b>PROGRAMAR MEDICAMENTOS PARA ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;PARA EL MES: ' . $mesl .  ' DEL ' . $ano2 . '</b>\', this);" class="btn btn-xs btn-warning"><i class="fa fa-file"></i> Programar</a> ' . $sms . '</center>';							
						} else {
							echo '-';
						}
					?></center>
				</td>
			@endif			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
	@endif
	@if($uladech==32)
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr @if($value->baja=="S") style="color:red" title="DADO DE BAJA {{$value->fallecido=='S'?', FALLECIDO EL '.date('d-m-Y', strtotime($value->fechafallecido)):'NO FALLECIDO'}}"  @endif >
			<td width="2%" style="font-size:12px">{{ $contador }}</td>
			<td width="5%">{{ $value->numero }}</td>
			<td width="5%">{{ $value->dni }}</td>
			<td width="25%" style="font-size:12px">{{ $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres }}</td>
			<td width="20%" style="font-size:12px">
				<center>
					<a href="#" onclick='modal("consultanefrologica/cambiarDoctor?id={{$value->cid}}&doctor_id={{$value->doctor_id}}", "<b>EDITAR MÉDICO</b>", this);' class="btn btn-xs btn-success">
						<i class="fa fa-user"></i> Ver
					</a>
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								if($c1->estadoexamen==1) {
									$sms = '<b style="color:green;"><i class="fa fa-check"></i></b>';
								} else {
									$sms = '<b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
								}
								if($user->usertype_id==2) {
									/////////////REPORTE DE HISTORIAL///////////////
									echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exáms.</a>&nbsp;';
									////////////////////////////
								}
								echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=' . $c1->situacion . '&cid=' . $c1->id . '&anillo=' . $ano . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a> ' . $sms;
								


							} else {
								echo '-</center></td><td width="10%"><center>-</b>';
							}
						} else {
							echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=NUEVO&cid=' . $c1->id . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a></center></td><td width="16%"><center><b style="color:orange">NUEVO</b> <b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
						}
					?>
				</center>
			</td>
			@if($user->usertype_id==2||$user->usertype_id==1)
				<td width="10%"><center>
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								if($c1->estadoexamen==1) {
									$sms = '<b style="color:green;"><i class="fa fa-check"></i></b>';
								} else {
									$sms = '<b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
								}
								if($user->usertype_id==2) {
									/////////////REPORTE DE HISTORIAL///////////////
									echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exáms.</a>&nbsp;';
									////////////////////////////
								}
								echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=' . $c1->situacion . '&cid=' . $c1->id . '&anillo=' . $ano . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a> ' . $sms . '</center></td><td width="10%"><center><b style="color:green">' . $c1->situacion  . " (" . $c1->txtTipoDatos . ")" . '</b>';
								


							} else {
								echo '-</center></td><td width="10%"><center>-</b>';
							}
						} else {
							echo '<a href="#" onclick="modal(\'consultanefrologica/resultados?pid=' . $value->pid . '&situacion=NUEVO&cid=' . $c1->id . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a></center></td><td width="16%"><center><b style="color:orange">NUEVO</b> <b style="color:red;"><i class="glyphicon glyphicon-remove"></i></b>';
						}
					?></center>
				</td>
				<td width="10%"><center><b style="color:blue;">
				<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								echo $c1->situacion2 . " (" . ($c1->txtTipoDatos<5?($c1->txtTipoDatos+1):0) . ")";							
							} else {
								echo '-';
							}
						} else {
							echo '-';
						}
					?></b></center>
				</td>
			@endif
			@if($user->usertype_id==28||$user->usertype_id==29)
				<td width="10%"><center><b style="color:green;">
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								echo $c1->situacion . " (" . $c1->txtTipoDatos . ")";							
							} else {
								echo '-';
							}
						} else {
							echo '-';
						}
					?></b></center>
				</td>
				<td width="10%"><center><b style="color:blue;">
					<?php					
						$consultas = ConsultaNefrologica::where('persona_id', '=', $value->pid)->get();
						if(count($consultas) > 0) {
							$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							$situacion = '';	
							$sms = '';					
							if($c1 !== NULL) {
								echo $c1->situacion2 . " (" . ($c1->txtTipoDatos<5?($c1->txtTipoDatos+1):0) . ")";							
							} else {
								echo '-';
							}
						} else {
							echo '-';
						}
					?></b></center>
				</td>
			@endif
			@if($user->usertype_id!==2)
				<td width="20%"><center>
					<?php
						echo '<a href="#" onclick="modal(\'consultamensual/reporte2?pid=' . $value->pid . '&mes=' . $mes . '&ano=' . $ano . '\', \'<b>ATENCIÓN NEFROLÓGICA DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-primary"><i class="fa fa-file"></i> Llenar atención</a>';
						$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
						if($c1 !== NULL) {
							echo '&nbsp;<a href="#" onclick="reporteConsultaMensual(2, ' . $c1->id . ');" class="btn btn-xs btn-default" title="Reporte de Atención mensual"><i class="fa fa-file"></i> Rpte. Atención</a>';						
							if($c1->tiempoenf==NULL) {
								echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
							} else {
								echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
							}

							if($user->usertype_id!==28&&$user->usertype_id!==29) {				
							
								/////////////REPORTE DE HISTORIAL///////////////
								echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exáms.</a>';
								////////////////////////////
							}
							echo '&nbsp;<a href="#" onclick="ImprimirReceta(\'' . $c1->id . '\');" class="btn btn-xs btn-success" title="Imprimir Receta"><i class="fa fa-print"></i> Receta</a>';
							if($user->usertype_id==1||$user->usertype_id==34) {
								echo '&nbsp;<a href="#" onclick="modal(\'historiaclinica/reporte?mensual=SI&prestacion=SI&formato=2&hid=' . $c1->id . '\', \'Formato de Atención de '. $value->apellidopaterno . ' ' . $value->apellidomaterno. ' ' . $value->nombres .'\', this);" class="btn btn-xs btn-danger" title="Formato de Atención"><i class="fa fa-file"></i> FUA</a>';
							
								if($c1->numeroformato!==NULL) {
									echo "&nbsp;<button class='btn btn-success btn-xs' title='Reporte de Formato de Atención' onclick='reporteformatoo(\"".$c1->id."\", 2);' type='button'><i class='fa fa-file'></i></button>";
									echo '&nbsp;<i class="fa fa-check" style="color:green"></i>';
								} else {
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
								}
							}
						} else {
							echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
							echo '&nbsp;<a title="Reporte de Atención mensual" href="#" onclick="#" class="btn btn-xs btn-default" disabled="disabled"><i class="fa fa-file"></i></a>';						
							echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Imprimir Receta" disabled="disabled"><i class="fa fa-print"></i></a>';
							if($user->usertype_id==1||$user->usertype_id==34) {
								echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-danger" title="Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
								echo '&nbsp;<i class="glyphicon glyphicon-remove" style="color:red"></i>';
							}
						}					
					?></center>
				</td>
				<td width="23%"><center>
					<?php					
						$c1 = ConsultaNefrologica::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->where('estado', '=', '1')->first();
						$situacion = '';
						$sms = '';
						//if($c1 !== NULL) {						
						//} else {
							echo '-';
						//}
					?></center>
				</td>
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
	@endif
</table>
<script>

	function reporteConsultaMensual(num, id) {
		var url = 'consultamensual/pdfReporte1';
		if(num == '2') {
			url = 'consultamensual/pdfReporte2';
		} else if(num == '3') {
			url = 'consultamensual/pdfReporte3';
		} else if(num == '4') {
			url = 'consultamensual/pdfReporte4';
		}
		window.open(url+"?id="+id);
	}

	function ImprimirReceta(id) {
		window.open("consultamensual/ImprimirReceta?id="+id);
	}

	function reporteformatoo(id, tipo){
	    window.open("historia/reporteformato?id="+id+"&formatomensual=1&formatotipo="+tipo);
	}

	function historialResultadosPorPaciente(hid, anno){
        window.open("reporte/historialResultadosPorPaciente?historia_id="+hid+"&anno="+anno,"_blank");
    }

</script>
@endif