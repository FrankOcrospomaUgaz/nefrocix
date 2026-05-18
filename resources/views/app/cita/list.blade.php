@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<div class="table-responsive">
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th style='font-size:12px' class="text-center" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		$title = '';
		$color = '';
		?>
		@foreach ($lista as $key => $value)
		<?php 

		$txtTipoAccesoInicio = '-';

		$numTAV = $value->txtTipoAccesoInicio;

		if($numTAV=='1') {
			$txtTipoAccesoInicio = 'FAV';
		} elseif ($numTAV=='2') {
			$txtTipoAccesoInicio = 'Autoinjerto';
		} elseif ($numTAV=='3') {
			$txtTipoAccesoInicio = 'Injerto';
		} elseif ($numTAV=='4') {
			$txtTipoAccesoInicio = 'CVCP';
		} elseif ($numTAV=='5') {
			$txtTipoAccesoInicio = 'CVCT';
		} elseif ($numTAV=='6') {
			$txtTipoAccesoInicio = 'Cperitoneal';
		}

		?>
		<tr>
			<td style='font-size:12px'>{{ $contador }}</td>
            <td style='font-size:12px'>{{ $value->tipopaciente }}</td>
            <td style='font-size:12px'>{{ $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres }}</td>
            <td style='font-size:12px'>{{ $value->persona->telefono }}</td>
            <td style='font-size:12px'>{{ $value->distrito2->nombre .' / '.$value->provincia2->nombre.' / '.$value->departamento2->nombre }}</td>
            <td style='font-size:12px'>{{ strtoupper($txtTipoAccesoInicio) }}</td>
            <td style='font-size:12px'>{{ $value->gruposanguineo }}</td>
            <td style='font-size:12px'>{{ $value->numero }}</td>
            <td style='font-size:12px'>{{ $value->turno->hora }}</td>
            <td style="font-weight:bold; color:{{ $value->estado=='S'?'green;':'red;' }}">{{ $value->estado=='S'?'COMPLETA':'PENDIENTE' }}</td>
            <td style='font-size:12px'>{{ $value->persona2==NULL?'':($value->persona2->apellidopaterno.' '.$value->persona2->apellidomaterno.' '.$value->persona2->nombres) }}</td>
            @if($value->situacion=='P')
	  			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning', 'title' => 'Editar')) !!}</td>
	  			<td>{!! Form::button('<div class="glyphicon glyphicon-minus"></div>', array('onclick' => 'modal (\''.URL::route($ruta["anular"], array($value->id, 'SI')).'\', \''.$titulo_anular.'\', this);', 'class' => 'btn btn-xs btn-info', 'title' => 'Anular')) !!}</td>
	  			@if($user->usertype_id==1 || $user->usertype_id==7)
					<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger', 'title' => 'Eliminar')) !!}</td>
				@else
					<td> - </td>
					<td> - </td>
				@endif
			@else
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div>', array( 'title' => 'Crear HC Inicial', 'onclick' => 'modal (\''.URL::route($ruta["createhcinicial"], array('id'=>$value->id, 'listar'=>'SI')).'\', \''.'HISTORIA CLÍNICA INICIAL'.'\', this);', 'class' => 'btn btn-xs btn-success')) !!}</td>
				<td> - </td>
				<td> - </td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
</div>
@endif