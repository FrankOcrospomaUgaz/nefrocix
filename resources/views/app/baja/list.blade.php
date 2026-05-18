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
		$estado = $value->estado;
		switch ($estado) {
			case 'F':
				$estado="<font style='font-weight:bold; color:red;'><i class='glyphicon glyphicon-screenshot'></i> FALLECIDO</font>";
				break;			
			case 'H':
				$estado="<font style='font-weight:bold; color:blue;'><i class='fa fa-ambulance'></i> HOSPITALIZADO</font>";
				break;
			case 'A':
				$estado="<font style='font-weight:bold; color:green;'><i class='fa fa-check'></i> ALTA</font>";
				break;
			case 'O':
				$estado="<font style='font-weight:bold; color:orange;'><i class='fa fa-ambulance'></i> OTRO</font>";
				break;
		}
		?>
		<tr>
			<td class="text-center">{{ $contador }}</td>
			<td>{{ $value->apellidopaterno." ".$value->apellidomaterno." ".$value->nombres }}</td>
			<td class="text-center">{{ date("d-m-Y", strtotime($value->fecha)) }}</td>
			<td class="text-center"><?php echo $estado ?></td>
			<td>{{ $value->motivo }}</td>
			<td>{{ $value->ipresshospitalizacion }}</td>			
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif