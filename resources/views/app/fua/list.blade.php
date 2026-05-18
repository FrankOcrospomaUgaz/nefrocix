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
		<?php 

		$estado = 'LLAMANDO';
        $colors = 'black';

        if($value->estado == 'F') {
            $estado = 'FINALIZADO';
            $colors = 'green';
        }

        if($value->estado == 'P') {
            $estado = 'PENDIENTE';
        }

        if($value->estado == 'A') {
            $estado = 'ATENDIÉNDOSE';
        	$colors = 'blue';
        }

        else if($value->estado == 'C') {
            $estado = 'CANCELADO';
            $colors = 'red';
        }
        else if($value->estado == 'N') {
            $estado = 'AUSENTE';
            $colors = 'orange';
        }

        $doctor = $value->doctor;

		?>
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ date("d-m-Y H:i", strtotime($value->fecha_atencion)) }}</td>
			<td>{{ $value->historia->persona->apellidopaterno . " " . $value->historia->persona->apellidomaterno . " " . $value->historia->persona->nombres }}</td>
			<td style="font-weight:bold; color:{{ $colors }};">{{ $estado }}</td>
			<td>{{ $doctor === NULL ? '-' : ($doctor->apellidopaterno . ' ' . $doctor->nombres) }}</td>
			<td>
				<?php 

					$tabla = "<button class='btn btn-warning btn-xs' onclick='modal(\"historiaclinica/reporte?hid=".$value->id."\", \"Formato de Atención de " . $value->historia->persona->apellidopaterno . ' ' . $value->historia->persona->apellidomaterno . ' ' . $value->historia->persona->nombres . "\", this)' type='button'><i class='fa fa-diamond fa-lg'></i> Formato</button>&nbsp;";

					if($value->numeroformato !== NULL && $value->numeroformato !== '') {
                        $tabla .= "<button class='btn btn-success btn-xs' onclick='reporteformatoo(\"".$value->id."\");' type='button'><i class='fa fa-file fa-lg'></i></button>&nbsp;";
                        $tabla .= '<i style="color:green;" class="glyphicon glyphicon-check"></i>';
                    } else {
                        $tabla .= "<button class='btn btn-success btn-xs' type='button' disabled='disabled'><i class='fa fa-file fa-lg'></i></button>&nbsp;";
                        $tabla .= '<i style="color:red;" class="glyphicon glyphicon-remove"></i>';
                    }

                    echo $tabla;

				?>
			</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
<script>
	function reporteformatoo(id){
	    window.open("historia/reporteformato?id="+id+"&formatomensual=2&formatotipo=0");
	}
</script>
@endif