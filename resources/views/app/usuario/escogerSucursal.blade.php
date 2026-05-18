<!-- Page-Title -->
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
$user = Auth::user();
$person_id = $user->person_id;
$persona = $user->person;
$nombrepersona = $persona->apellidopaterno.' '.$persona->apellidomaterno.' '.$persona->nombres;
?>

<div class="row">
	{!! Form::open(['route' => $ruta["guardarSucursal"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
	{!! Form::hidden('person_id', null, array('id' => 'person_id')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">		
		<div class="form-group">
			{!! Form::label('nombrepersona', 'USUARIO', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-10 col-md-10 col-sm-10">
				{!! Form::text('nombrepersona', $nombrepersona, array('class' => 'form-control input-sm', 'id' => 'nombrepersona')) !!}
			</div>				
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="glyphicon glyphicon-floppy-disk"></i> Guardar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnGuardarSucursal', 'onclick' => 'guardarSucursal();')) !!}
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>

<div id="modalAlertaG" class="modal fade" role="dialog" style="z-index: 1600;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="color:red;"><i class="fa fa-thumbs-o-down"></i> ¡Cuidado!</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img width="130px" height="150px" src="dist/img/rinon.gif" class="img-circle" alt="User Image">
                    </div>
                    <div class="col-md-8 text-center">
                        <h2 style="color:blue;" id="mensajeAlertaG"></h2>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>

    </div>
</div>

<div id="modalAlertaB" class="modal fade" role="dialog" style="z-index: 1600;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="color:green;"><i class="fa fa-thumbs-o-up"></i> ¡Correcto!</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img width="130px" height="150px" src="dist/img/rinon2.gif" class="img-circle" alt="User Image">
                    </div>
                    <div class="col-md-8 text-center">
                        <h2 style="color:blue;" id="mensajeAlertaB"></h2>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>

    </div>
</div>

<script>
	$(document).ready(function($) {
		$('#nombrepersona').val('').focus();
	});
	function guardarSucursal(){
		var person_id = $('#person_id').val();
		if(person_id === '') {
			alertaG('Por favor escribe un nombre correcto...');
			$('#nombrepersona').val('').focus();
			return false;
		}
		var sucursal_id = 1;
		var respuesta="";
		var ajax = $.ajax({
			"method": "POST",
			"url": "{{ url('/usuario/guardarSucursal') }}",
			"data": {
				"person_id" : person_id, 
				"sucursal_id" : sucursal_id,
				"_token": "{{ csrf_token() }}",
				},
			"beforeSend": function() {
				$('#btnGuardarSucursal').attr('disabled', 'disabled').html('Cargando...');
			}
		}).done(function(info){
			respuesta = info;
			$("#uuuu").html(respuesta);
			cerrarModal();
			alertaB('Usuario Actualizado a: ' + respuesta);
		});
	}

	var personas = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'person/employeesautocompleting/%QUERY',
			filter: function (personas) {
				return $.map(personas, function (movie) {
					return {
						value: movie.value,
						id: movie.id
					};
				});
			}
		}
	});
	personas.initialize();
	$('#nombrepersona').typeahead(null,{
		displayKey: 'value',
		source: personas.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$('#person_id').val(datum.id);
	});

	function alertaG(mensaje) {
        $('#mensajeAlertaG').html(mensaje);
        $('#modalAlertaG').modal('show');
    }

    function alertaB(mensaje) {
        $('#mensajeAlertaB').html(mensaje);
        $('#modalAlertaB').modal('show');
    }
</script>