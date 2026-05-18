@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<script>
	function tablaCita(historia_id, nombrepaciente){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/tablaCita') }}",
			"data": {
				"historia_id" : historia_id, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#exampleModal').modal('show');
			$('#tablaCitas').html(info);
			$('#nombrepaciente').html(nombrepaciente);
		});
	}

	function ver(cita_id){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/ver') }}",
			"data": {
				"cita_id" : cita_id, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#verCita').html(info);
			$('#exampleModal1').modal('show');
		});
	}	
	function anadirComentario(cita_id) {
		var comentario = $('#anadirComentario').val();
		if(comentario == '') {
			$('#anadirComentario').focus();
			return 0;
		}
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/anadirComentario') }}",
			"data": {
				"cita_id" : cita_id, 
				"comentario" : $('#anadirComentario').val(),
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			if(info == '1') {
				alert('Comentario Añadido');
			} else {
				alert('No se pudo añadir comentario');
			}			
		});
	}

	function verHistoriaInicial(id){
        window.open("historia/reporteHistoriaInicial?id="+id);
    }

    function verHistoriaEnfermeria(id){
        window.open("historia/reporteHistoriaEnfermeria?id="+id);
    }
</script>
<style>
	td, th {
		font-size: 12px;
	}
</style>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
		    <div class="modal-header" id="encabeCita"><h3 align="center">Historias de Citas de <font id="nombrepaciente" color="blue" style="font-weight: bold"></font></h3></div>
		    <div class="modal-body" id="tablaCitas"></div>
	        <div class="modal-footer">
	            <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
	        </div>
	    </div>
	</div>
</div>
<div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
		    <div class="modal-body" id="verCita"></div>
	        <div class="modal-footer">
	            <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
	        </div>
	    </div>
	</div>
</div>

@if($vistamedico != "SI")
{!! $paginacion or '' !!}
@endif

<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th class="text-center" @if($value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<?php
			if($value->baja=="S"){
				$color="background-color: rgba(29, 119, 162, 0.52);";
				$title="Dado de baja el ".date("d-m-Y", strtotime($value->fechafallecido)) . ($value->fallecido=="S"?" - FALLECIDO":"").($value->fallecido=="H"?" - HOSPITALIZADO":"").($value->fallecido=="O"?" - OTROS MOTIVOS":"");
			}else{
				$color="";
				$title="";
			}
		?>
		<tr style="{{ $color }}" title="{{ $title }}">
			<td>{{ $contador }}</td>
			<td>{{ $value->nacionalidad }}</td>
			<td>{{ $value->numero }}</td>
			{{--<td>{{ $value->numero2 }}</td>--}}
            <td>{{ $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres }}</td>
            {{--<td>{{ $value->persona->dni }}</td>--}}
            <td align="center">{{ $value->tipopaciente  . ($value->convenio!==NULL?(" - " . $value->convenio->nombre):"") }}</td>
            <td>{{ $value->persona->telefono }}</td>
            <td>{{ date('d-m-Y', strtotime($value->persona->fechanacimiento)) }}</td>
            <td>{{ $value->persona->direccion }}</td>            
            @if($vistamedico != "SI")
	            @if($value->baja=="S")
					<td> - </td>
					<td> - </td>
					<td> - </td>
					<td> - </td>
					<td> - </td>
					<td> - </td>
					<td>{!! Form::button('<i class="glyphicon glyphicon-search"></i>', array('class' => 'btn btn-success btn-xs', 'id' => 'btnSeguimiento', 'title' => 'Seguimiento', 'onclick' => 'seguimiento(\''.$entidad.'\','.$value->id.')')) !!}</td>
		            <td>{!! Form::button('<i class="glyphicon glyphicon-print"></i>', array('class' => 'btn btn-info btn-xs', 'id' => 'btnImprimir', 'title' => 'Imprimir', 'onclick' => 'imprimirHistoria(\''.$entidad.'\','.$value->id.')')) !!}</td>
		            <td>{!! Form::button('<i class="glyphicon glyphicon-print"></i>', array('class' => 'btn btn-success btn-xs', 'id' => 'btnImprimir2', 'title' => 'Imprimir Citas', 'onclick' => 'imprimirHistoria2(\''.$entidad.'\','.$value->id.')')) !!}</td>
		            <td>{!! Form::button('<i class="glyphicon glyphicon-check"></i>', array('class' => 'btn btn-danger btn-xs', 'id' => 'btnActivar', 'title' => 'Activar paciente', 'onclick' => 'modal (\''.URL::route($ruta["activar"], array($value->id, 'SI')).'\', \'Activar Paciente\', this);')) !!}</td>
	            @else
	            	<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div>', array( 'title' => 'Editar Historia Clínica Inicial', 'onclick' => 'modal (\''.URL::route($ruta["createhcinicial"], array('id'=>$value->id, 'listar'=>'SI')).'\', \''.'HISTORIA CLÍNICA INICIAL<button type="button" class="close closdat" data-dismiss="modal" aria-hidden="true" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>'.'\', this);', 'class' => 'btn btn-xs btn-success')) !!}</td>
	            	@if($value->estado=='S')
		            	<td>{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array( 'title' => 'Imprimir Historia Clínica Inicial', 'onclick' => 'verHistoriaInicial("' . $value->id . '")', 'class' => 'btn btn-xs')) !!}</td>
		            @else
		            	<td>{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array('class' => 'btn btn-xs btn-primary', "disabled")) !!}</td>
		            @endif		            
		            <td style="font-weight:bold; color:{{ $value->estado=='S'?'green;':'red;' }}">{{ $value->estado=='S'?'COMPLETA':'PENDIENTE' }}</td>        



		            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div>', array( 'title' => 'Editar Historia de Enfermería', 'onclick' => 'modal (\''.URL::route($ruta["createhenfermeria"], array('id'=>$value->id, 'listar'=>'SI')).'\', \''.'FORMATO DE ENFERMERÍA EN LA ADMISIÓN DEL PACIENTE'.'\', this);', 'class' => 'btn btn-xs btn-success')) !!}</td>	
		            @if($value->estado3=='S')
		            	<td>{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array( 'title' => 'Imprimir Historia de Enfermería', 'onclick' => 'verHistoriaEnfermeria("' . $value->id . '")', 'class' => 'btn btn-xs btn-secondary')) !!}</td>
		            @else
		            	<td>{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array('class' => 'btn btn-xs btn-primary', "disabled")) !!}</td>
		            @endif   
		            <td style="font-weight:bold; color:{{ $value->estado3=='S'?'green;':'red;' }}">{{ $value->estado3=='S'?'COMPLETA':'PENDIENTE' }}</td>

					<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div>', array( 'title' => 'Editar Historia', 'onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
					<td>{!! Form::button('<i class="glyphicon glyphicon-print"></i>', array('class' => 'btn btn-info btn-xs', 'id' => 'btnImprimir', 'title' => 'Imprimir Historia', 'onclick' => 'imprimirHistoria(\''.$entidad.'\','.$value->id.')')) !!}</td>			
					<!--<td>{!! Form::button('<i class="glyphicon glyphicon-screenshot"></i>', array('class' => 'btn btn-danger btn-xs', 'id' => 'btnFallecido', 'title' => 'Dar de baja', 'onclick' => 'modal (\''.URL::route($ruta["fallecido"], array($value->id, 'SI')).'\', \'Dar de baja\', this);')) !!}</td>-->
					@if($user->usertype_id==1 || $user->usertype_id==2)						
						<td>{!! Form::button('<div class="glyphicon glyphicon-trash"></div>', array( 'title' => 'Eliminar', 'onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
					@else
						<td> - </td>
					@endif		
					<td class="text-center"> - </td>		
				@endif
				<td>{!! Form::button('<div class="glyphicon glyphicon-eye-open"></div>', array( 'title' => 'Observaciones', "onclick" => 'modal (\''.URL::route($ruta["observaciones"], array($value->id, 'SI')).'\', \''.$value->persona->apellidopaterno . " " . $value->persona->apellidomaterno . " " . $value->persona->nombres .'\', this);', 'class' => 'btn btn-xs btn-primary')) !!}</td>
			@else
				<!--<td>{!! Form::button('<div class="glyphicon glyphicon-eye-open"></div>', array( 'title' => 'Historias Clínicas', 'onclick' => 'tablaCita(' . $value->id . ', "' . $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres . '");', 'class' => 'btn btn-xs btn-primary')) !!}</td>-->
				<td> - </td>
				<td> - </td>
				<td> - </td>
				<td> - </td>
				<td> - </td>
				<td> - </td>
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
@endif