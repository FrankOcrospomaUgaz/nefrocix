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
			<td>{{ $contador }}</td>
			<td>{{ str_pad($value->numero,8,'0',STR_PAD_LEFT).'-'.date("Y",strtotime($value->fecha)) }}</td>
			<td>{{ date("d/m/Y H:i:s",strtotime($value->created_at)) }}</td>
			<td>{{ $value->responsable->nombres }}</td>
			<td style="font-size:12px;">{{ $value->comentario }}</td>
			<td>{{ ($value->situacion=='P'?'PENDIENTE':'DESPACHADO') }}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-eye-open"></div> Ver', array('onclick' => 'modal (\''.URL::route($ruta["show"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_ver.'\', this);', 'class' => 'btn btn-xs btn-info')) !!}</td>
			@if($value->situacion=='P')
				<td>{!! Form::button('<div class="glyphicon glyphicon-check"></div> Proveer', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}
				</td>
				<td>{!! Form::button('<div class="fa fa-print"></div> Detalles', array('onclick' => 'window.open(\'consultamensual/ImprimirReciboRequerimientoNormal?id=' . $value->id . '\',\'_blank\')', 'class' => 'btn btn-xs btn-primary')) !!}</td>	
			@else	
				@if($value->tipodocumento_id==23)
					<td>{!! Form::button('<div class="fa fa-print"></div> Receta', array('onclick' => 'window.open(\'consultamensual/ImprimirReceta?id=' . $value->movimiento_id . '\',\'_blank\')', 'class' => 'btn btn-xs btn-success')) !!}</td>
				@elseif($value->tipodocumento_id==15)
					<td>{!! Form::button('<div class="fa fa-print"></div> Recibo', array('onclick' => 'window.open(\'consultamensual/ImprimirReciboProgramacionDiaria?id=' . $value->id . '\',\'_blank\')', 'class' => 'btn btn-xs btn-info')) !!}</td>
				@else
					<td>{!! Form::button('<div class="fa fa-print"></div> Recibo', array('onclick' => 'window.open(\'consultamensual/ImprimirReciboRequerimientoNormal?id=' . $value->id . '\',\'_blank\')', 'class' => 'btn btn-xs btn-primary')) !!}</td>
				@endif			
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif