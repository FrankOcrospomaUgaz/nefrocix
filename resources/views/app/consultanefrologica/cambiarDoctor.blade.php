<!-- Page-Title -->
<?php

use App\Person;
$doctor = Person::find($doctor_id);
$nombredoctor = "";
if($doctor!==NULL) {
	$nombredoctor = $doctor->apellidopaterno.' '.$doctor->apellidomaterno.' '.$doctor->nombres;
}

?>

<div class="row">
	{!! Form::hidden('id', $id, array('id' => 'id')) !!}
	{!! Form::hidden('doctor_id', $doctor_id, array('id' => 'doctor_id')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">		
		<div class="form-group">
			{!! Form::label('nombredoctor', 'MÉDICO', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-10 col-md-10 col-sm-10">
				{!! Form::text('nombredoctor', $nombredoctor, array('class' => 'form-control input-sm', 'id' => 'nombredoctor')) !!}
			</div>				
		</div>
		<hr>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="glyphicon glyphicon-floppy-disk"></i> Guardar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnGuardarDoc', 'onclick' => 'guardarDoctor2();')) !!}
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar', 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
	</div>
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
		$('#nombredoctor').focus();
		$(".closdat").remove();
    	$(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
	});
	function guardarDoctor2(){
		var doctor_id = $('#doctor_id').val();
		var id = $('#id').val();
		if(doctor_id === '') {
			alertaG('Por favor escribe un nombre correcto...');
			$('#nombredoctor').val('').focus();
			return false;
		}
		var ajax = $.ajax({
			"method": "POST",
			"url": "{{ url('/consultanefrologica/cambiarDoctor2') }}",
			"data": {
				"doctor_id" : doctor_id, 
				"id" : id,
				"_token": "{{ csrf_token() }}",
				},
			"beforeSend": function() {
				$('#btnGuardarDoc').attr('disabled', 'disabled').html('Cargando...');
			}
		}).done(function(info){
			cerrarModal();
			buscar('ConsultaNefrologica')
			alertaB('Doctor Establecido');
		});
	}

	var doctor = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'person/doctorautocompleting/%QUERY',
			filter: function (doctor) {
				return $.map(doctor, function (movie) {
					return {
						value: movie.value,
						id: movie.id
					};
				});
			}
		}
	});
	doctor.initialize();
	$('#nombredoctor').typeahead(null,{
		displayKey: 'value',
		source: doctor.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$('#doctor_id').val(datum.id);
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