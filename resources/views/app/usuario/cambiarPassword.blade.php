<!-- Page-Title -->
<?php
$nombrepersona = $persona->apellidopaterno.' '.$persona->apellidomaterno.' '.$persona->nombres;
?>

<div class="row">
	<form action="#" accept-charset="UTF-8" class="form-horizontal">
		<div class="col-lg-12 col-md-12 col-sm-12">		
			<div class="form-group">
				<center><h2 style="font-weight: bold; color: blue;">MODIFICAR CONTRASEÑA</h2></center>				
			</div>
			<div class="form-group">
				<div class="col-lg-5 col-md-5 col-sm-5">
					<img src="dist/img/user2-160x160.jpg" alt="" height="100%" width="100%">
				</div>
				<div class="col-lg-7 col-md-7 col-sm-7">
					<div class="form-group">
						{!! Form::label('nombrepersona', 'USUARIO', array('class' => 'col-lg-12 col-md-12 col-sm-12')) !!}
					</div>
					<div class="form-group">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<p class="form-control-static">{{ $nombrepersona }}</p>
						</div>				
					</div>
					<div class="form-group">
						{!! Form::label('contrasena', 'NUEVA CONTRASEÑA', array('class' => 'col-lg-12 col-md-12 col-sm-12')) !!}
					</div>
					<div class="form-group">
						<div class="col-lg-12 col-md-12 col-sm-12">
							{!! Form::password('contrasena', array('class' => 'form-control', 'id' => 'contrasena', 'placeholder'=>'Digite nueva contraseña', "onkeyup"=>"$('#mensajecambiocontra').html('');")) !!}
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<h6 class="form-control-static" style="color: green;">* Si olvidaste tu password, contáctate con el administrador para que te la inicialice.</h6>
						</div>				
					</div>
					<div class="form-group">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<p class="form-control-static" id="mensajecambiocontra"></p>
						</div>				
					</div>
					<div class="form-group">
						<div class="col-lg-12 col-md-12 col-sm-12 text-right">
							{!! Form::button('<i class="glyphicon glyphicon-floppy-disk"></i> Guardar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'onclick' => 'confirmarCambioPassword();')) !!}
							{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		configurarAnchoModal('800');
		$(".closdat").remove();
    	$(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
		$("#contrasena").focus();
	}); 
	function confirmarCambioPassword(){
		var contra = $("#contrasena").val();
		if(contra === ''||contra.length < 6) {
			$("#mensajecambiocontra").css("color", "red").html('Asegúrate de escribir una contraseña de mínimo 6 caracteres...');
			$('#contrasena').focus();
			return false;
		} else {
			$.ajax({
				"method": "GET",
				"url": "{{ url('/usuario/confirmarCambioPassword') }}",
				"data": {
					"contra" : contra,
					},
				"beforeSend": function() {
					$("#mensajecambiocontra").css("color", "green").html('Cargando...');
				}
			}).done(function(info){
				if (info === "OK") {
					$("#mensajecambiocontra").css("color", "blue").html('Contraseña Actualizada');
				} else {
					$("#mensajecambiocontra").css("color", "red").html('No pudiste actualizar tu contraseña. Problemas de Red...');
				}
				$("#contrasena").val("");			
			}).fail(function() {
				$("#mensajecambiocontra").css("color", "red").html('No pudiste actualizar tu contraseña. Problemas de Red...');
			});
		}
	}
</script>