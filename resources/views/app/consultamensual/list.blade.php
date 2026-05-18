<?php
	use App\ConsultaSaludMental;
	use App\ConsultaNefrologica;
	use App\ConsultaServicioSocial;
	use App\ConsultaNutricion;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Auth;
	date_default_timezone_set('America/Lima');
	$mes = $messs;
	$ano = $anooo;
	$meses_ES = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SETIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
	$mesl = $meses_ES[$mes-1];
	$usertype_id = Auth::user()->usertype_id;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<?php

			$baja = "NO";
			$color = "color:none;";
			if($value->baja=="S") {
				$baja = "SI";
				$color = "color:#FA5858;";
			}

			?>
		<tr style="{{ $color }}">
			<td width="2%">{{ $contador }}</td>
			<td width="5%">{{ $value->numero }}</td>
			<td width="5%">{{ $value->dni }}</td>
			<td width="30%">{{ $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres }}</td>
			<td width="4%">{{ $baja }}</td>
			
			@if($tipoconsulta=="2")
				<td width="20%">
					<center class="ccc ccc2">
						@if($usertype_id==36||$usertype_id==1||$usertype_id==2||$usertype_id==28||$usertype_id==34)
						<?php
							echo '<a href="#" onclick="modal(\'consultamensual/reporte1?pid=' . $value->pid . '&mes=' . $mes . '&ano=' . $ano . '\', \'<b>ATENCIÓN EN SALUD MENTAL DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-success"><i class="fa fa-file"></i> Llenar Atención</a>';
							$c1 = ConsultaSaludMental::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							if($c1!==NULL) {
								if($c1->estadoatencion==2) {
									echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
								} else {
									echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
								}							
							} else {
								echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
							}
							if($c1 !== NULL) {
								echo '&nbsp;<a href="#" onclick="reporteConsultaMensual(1, ' . $c1->id . ');" class="btn btn-xs btn-default" title="Reporte de Atención mensual"><i class="fa fa-file"></i> Reporte Atención</a>';
								if($usertype_id==1||$usertype_id==34) {
									echo '&nbsp;<a href="#" onclick="modal(\'historiaclinica/reporte?mensual=SI&formato=1&hid=' . $c1->id . '\', \'Formato de Atención de '. $value->apellidopaterno . ' ' . $value->apellidomaterno. ' ' . $value->nombres .'\', this);" class="btn btn-xs btn-danger" title="Formato de Atención"><i class="fa fa-file"></i></a>';
								
									if($c1->numeroformato!==""&&$c1->numeroformato!==NULL) {
										echo "&nbsp;<button class='btn btn-success btn-xs' title='Reporte de Formato de Atención' onclick='reporteformatoo(\"".$c1->id."\", 1);' type='button'><i class='fa fa-file'></i></button>";
										echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
									} else {
										echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
										echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
									}
								}
							} else {
								echo '&nbsp;<a title="Reporte de Atención mensual" href="#" onclick="#" class="btn btn-xs btn-default" disabled="disabled"><i class="fa fa-file"></i> Reporte Atención</a>';
								if($usertype_id==1||$usertype_id==34) {
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-danger" title="Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
								}
							}					
						?>
						@else
						-
						@endif
					</center>
				</td>
			@endif		
		
			@if($tipoconsulta=="3")
				<td width="20%">
					<center class="ccc ccc3">
						@if($usertype_id==37||$usertype_id==1||$usertype_id==2||$usertype_id==28||$usertype_id==34)
						<?php
							echo '<a href="#" onclick="modal(\'consultamensual/reporte3?pid=' . $value->pid . '&mes=' . $mes . '&ano=' . $ano . '\', \'<b>ATENCIÓN DE SERVICIO SOCIAL DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-warning"><i class="fa fa-file"></i> Llenar Atención</a>';
							$c1 = ConsultaServicioSocial::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							if($c1!==NULL) {
								if($c1->estadoatencion==2) {
									echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
								} else {
									echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
								}
							} else {
								echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
							}
							if($c1 !== NULL) {
								echo '&nbsp;<a href="#" onclick="reporteConsultaMensual(3, ' . $c1->id . ');" class="btn btn-xs btn-default" title="Reporte de Atención mensual"><i class="fa fa-file"></i> Reporte Atención</a>';
								if($usertype_id==1||$usertype_id==34) {
									echo '&nbsp;<a href="#" onclick="modal(\'historiaclinica/reporte?mensual=SI&formato=3&hid=' . $c1->id . '\', \'Formato de Atención de '. $value->apellidopaterno . ' ' . $value->apellidomaterno. ' ' . $value->nombres .'\', this);" class="btn btn-xs btn-danger" title="Formato de Atención"><i class="fa fa-file"></i></a>';
								
									if($c1->numeroformato!==""&&$c1->numeroformato!==NULL) {
										echo "&nbsp;<button class='btn btn-success btn-xs' title='Reporte de Formato de Atención' onclick='reporteformatoo(\"".$c1->id."\", 3);' type='button'><i class='fa fa-file'></i></button>";
										echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
									} else {
										echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
										echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
									}
								}
							} else {
								echo '&nbsp;<a title="Reporte de Atención mensual" href="#" onclick="#" class="btn btn-xs btn-default" disabled="disabled"><i class="fa fa-file"></i> Reporte Atención</a>';
								if($usertype_id==1||$usertype_id==34) {
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-danger" title="Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
								}
							}					
						?>
						@else
						-
						@endif
					</center>
				</td>
			@endif		
		
			@if($tipoconsulta=="1")
				<td width="20%">
					<center class="ccc ccc1">
						@if($usertype_id==35||$usertype_id==1||$usertype_id==2||$usertype_id==28||$usertype_id==34)
						<?php
							echo '<a href="#" onclick="modal(\'consultamensual/reporte4?pid=' . $value->pid . '&mes=' . $mes . '&ano=' . $ano . '&hid=' . $value->hid . '\', \'<b>ATENCIÓN DE NUTRICIÓN DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-primary"><i class="fa fa-file"></i> Llenar Atención</a>';
							$c1 = ConsultaNutricion::where('persona_id', '=', $value->pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();
							if($c1!==NULL) {
								if($c1->estadoatencion==2) {
									echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
								} else {
									echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
								}
							} else {
								echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
							}
							if($c1 !== NULL) {
								echo '&nbsp;<a href="#" onclick="reporteConsultaMensual(4, ' . $c1->id . ');" class="btn btn-xs btn-default" title="Reporte de Atención mensual"><i class="fa fa-file"></i> Reporte Atención</a>';
								if($usertype_id==1||$usertype_id==34) {
									echo '&nbsp;<a href="#" onclick="modal(\'historiaclinica/reporte?mensual=SI&formato=4&hid=' . $c1->id . '\', \'Formato de Atención de '. $value->apellidopaterno . ' ' . $value->apellidomaterno. ' ' . $value->nombres .'\', this);" class="btn btn-xs btn-danger" title="Formato de Atención"><i class="fa fa-file"></i></a>';
								
									if($c1->numeroformato!==""&&$c1->numeroformato!==NULL) {
										echo "&nbsp;<button class='btn btn-success btn-xs' title='Reporte de Formato de Atención' onclick='reporteformatoo(\"".$c1->id."\", 4);' type='button'><i class='fa fa-file'></i></button>";
										echo '&nbsp;<i style="color:green;" class="glyphicon glyphicon-check"></i>';
									} else {
										echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
										echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
									}
								}
							} else {
								echo '&nbsp;<a title="Reporte de Atención mensual" href="#" onclick="#" class="btn btn-xs btn-default" disabled="disabled"><i class="fa fa-file"></i> Reporte Atención</a>';
								if($usertype_id==1||$usertype_id==34) {
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-danger" title="Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<a href="#" onclick="#" class="btn btn-xs btn-success" title="Reporte Formato de Atención" disabled="disabled"><i class="fa fa-file"></i></a>';
									echo '&nbsp;<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
								}
							}
							/////////////REPORTE DE HISTORIAL///////////////
							echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-warning" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exámenes</a>';
							////////////////////////////				
						?>
						@else
						-
						@endif
					</center>
				</td>
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
<script>

	tapar();
	function tapar() {
    	/*var tp = $("#tipoconsulta").val();
    	$(".ccc").addClass("hide");
    	if(tp=="1") {
    		$(".ccc1").removeClass("hide");
    	} else if(tp=="2") {
    		$(".ccc2").removeClass("hide");
    	} else if(tp=="3") {
    		$(".ccc3").removeClass("hide");
    	}*/
    }

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

	function reporteformatoo(id, tipo){
	    window.open("historia/reporteformato?id="+id+"&formatomensual=1&formatotipo="+tipo);
	}

	function historialResultadosPorPaciente(hid, anno){
        window.open("reporte/historialResultadosPorPaciente?historia_id="+hid+"&anno="+anno,"_blank");
    }
</script>
@endif