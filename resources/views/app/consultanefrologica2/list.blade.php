<?php
use App\ConsultaNefrologica;
use Illuminate\Support\Facades\DB;
date_default_timezone_set('America/Lima');
$mes = $messs;
$mes2 = ($messs==12?1:($messs+1));
$ano = $anooo;
$ano2 = ($messs==12?($anooo+1):$anooo);
$meses_ES = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SETIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
$mesl = $meses_ES[$mes-1];
$mes2 = $meses_ES[$mes2-1];
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
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td style="font-size:12px">{{ $contador }}</td>
			<td>{{ $value->numero }}</td>
			<td>{{ $value->dni }}</td>
			<td style="font-size:12px">{{ $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres }}</td>
			<td><center>
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
							echo '<a href="#" onclick="modal(\'consultanefrologica2/resultados?pid=' . $value->pid . '&situacion=' . $c1->situacion . '&cid=' . $c1->id . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a> ' . $sms;

							echo '&nbsp;<a href="#" onclick="window.open(\'consultanefrologica2/pdfLaboratorio?id=' . $c1->id . '\', \'_blank\')" class="btn btn-xs btn-default" title="PDF Laboratorio Clinico"><i class="fa fa-file-pdf-o"></i> PDF</a>';
						} else {
							echo '-';
						}
					} else {
						echo '<a href="#" onclick="modal(\'consultanefrologica2/resultados?pid=' . $value->pid . '&situacion=NUEVO&cid=' . $c1->id . '\', \'<b>RESULTADOS DE EXÁMENES DE LABORATORIO DE ' . $value->apellidopaterno . ' ' . $value->apellidomaterno . ' ' . $value->nombres . '&nbsp;|&nbsp;MES: ' . $mesl .  ' DEL ' . $ano . '</b>\', this);" class="btn btn-xs btn-danger"><i class="fa fa-file"></i> Llenar</a>';
					}
					/////////////REPORTE DE HISTORIAL///////////////
						echo '&nbsp;<a href="#" onclick="historialResultadosPorPaciente2(' . $value->hid . ', '.$ano.')" class="btn btn-xs btn-success" title="Historial de Resultados - '.$ano.'"><i class="fa fa-file-excel-o"></i> Historial Exámenes</a>';
						////////////////////////////
				?></center>
			</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
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

	function historialResultadosPorPaciente2(hid, anno){
        window.open("reporte/historialResultadosPorPaciente2?historia_id="+hid+"&anno="+anno,"_blank");
    }

</script>
@endif
