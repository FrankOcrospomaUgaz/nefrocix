<?php 
date_default_timezone_set('America/Lima');
$fecha = date("Y-m-d");
?>
<style>
	.requerido2 { 
		border: 1px solid #f00; 
		background-color: #FFD6CE;
		color: red;
	}
	input, select, textarea {
	  	text-transform:uppercase;
	}
</style>
<div class="box">
	<div class="box-header">
		<div class="row">
			<div class="col-xs-6">
				<div id="divMensajeError{!! $entidad !!}"></div>
				{!! Form::model($baja, $formData) !!}	
					{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
					<div class="form-group">
						{!! Form::label('paciente', 'Paciente:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
						<div class="col-lg-9 col-md-9 col-sm-9">
							{!! Form::text('paciente', null, array('class' => 'form-control', 'id' => 'paciente', 'placeholder' => 'Ingrese paciente')) !!}
							{!! Form::hidden('historia_id', null, array('id' => 'historia_id')) !!}							
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('fecha', 'Fecha Baja/Alta:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
						<div class="col-lg-9 col-md-9 col-sm-9">
							{!! Form::date('fecha', $fecha, array('class' => 'form-control', 'id' => 'fecha', 'placeholder' => 'Ingrese fecha')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('estadoanterior', 'Estado Anterior:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label parteEstadoAnterior')) !!}
						<div class="col-lg-3 col-md-3 col-sm-3">
							{!! Form::text('estadoanterior', null, array('class' => 'form-control parteEstadoAnterior', 'id' => 'estadoanterior', "readonly")) !!}
						</div>
						{!! Form::label('estadoactual', 'Estado Actual:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label parteEstadoAnterior')) !!}
						<div class="col-lg-3 col-md-3 col-sm-3">
							{!! Form::select('estadoactual', array("H" => "HOSPITALIZADO", "F" => "FALLECIDO", "O" => "OTRO", "A" => "ALTA"), $baja==null?"H":($baja->estado=="H"?"HOSPITALIZADO":($baja->estado=="F"?"FALLECIDO":($baja->estado=="O"?"OTRO":"ALTA"))), array('class' => 'form-control', 'id' => 'estadoactual', "onchange" => "mostraripress();")) !!}
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6">
							{!! Form::text('estadoactual2', $baja==null?"H":($baja->estado=="H"?"HOSPITALIZADO":($baja->estado=="F"?"FALLECIDO":($baja->estado=="O"?"OTRO":"ALTA"))), array('class' => 'form-control', 'id' => 'estadoactual2')) !!}
						</div>
					</div>
					<div class="form-group"  id="mostrarmotivo2">
						{!! Form::label('motivo2', 'Motivo General:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
						<div class="col-lg-9 col-md-9 col-sm-9">
							{!! Form::select('motivo2', array("ABANDONO" => "ABANDONO", "TRASPLANTE RENAL" => "TRASPLANTE RENAL", "CAMBIO DE TERAPIA DE DIÁLISIS" => "CAMBIO DE TERAPIA DE DIÁLISIS", "SIS INACTIVO" => "SIS INACTIVO", "TRASLADO A OTRA IPRESS" => "TRASLADO A OTRA IPRESS", "OTROS" => "OTROS"), null, array('class' => 'form-control', 'id' => 'motivo2', "onchange" => "mostraripress();")) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('motivo', 'Motivo Específico:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
						<div class="col-lg-9 col-md-9 col-sm-9">
							{!! Form::textarea('motivo', null, array('class' => 'form-control requerido', 'id' => 'motivo', 'placeholder' => 'Ingrese motivo', 'rows' => '5')) !!}
						</div>
					</div>
					<div class="form-group hide" id="mostraripress">
						{!! Form::label('IPRESS', 'IPRESS Hospitalización:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
						<div class="col-lg-9 col-md-9 col-sm-9">
							{!! Form::text('IPRESS', null, array('class' => 'form-control', 'id' => 'IPRESS', 'placeholder' => 'Ingrese IPRESS')) !!}
						</div>
					</div>
				{!! Form::close() !!}
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<h3 class="text-center" style="color: red;">Historial de Movimientos del Paciente</h3>
				</div>
				<table width="100%" height="100%" class="table table-bordered">
					<thead>
						<tr style="background-color: yellow;">
							<th width="20%">Fecha</th>
							<th width="20%">Estado</th>
							<th width="60%">Motivo</th>
						</tr>
					</thead>
					<tbody id="tablaBajas"></tbody>
				</table>
				<div class="form-group">
	        		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
	        			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarBaja("' . $entidad . '", this);')) !!}
	        			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	        		</div>
	        	</div>
			</div>
		</div>
	</div>
</div>	
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('1200');
		$(".closdat").remove();
    	$(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').focus();
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');		
		mostraripress();
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="estadoactual2"]').hide();
		@if($baja != null)
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val('<?=($baja->historia->persona->apellidopaterno . " " . $baja->historia->persona->apellidomaterno . " " . $baja->historia->persona->nombres)?>');
        	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(<?=$baja->historia->id?>);
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').attr("readonly", "readonly");
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').attr("readonly", "readonly");
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="estadoactual2"]').attr("readonly", "readonly");
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="estadoactual"]').hide();
			$('.parteEstadoAnterior').hide();
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="estadoactual2"]').show();
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="estadoactual"]').val("{{$baja->estado}}");
			evaluarPaciente(<?=$baja->historia->id?>);
			mostraripress();
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="IPRESS"]').val("{{$baja->ipresshospitalizacion}}");
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="fecha"]').val("{{$baja->fecha}}");
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="motivo2"]').removeAttr("onchange");
		@endif
	});

	var personas = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
	    limit: 10,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'baja/personautocompletar/%QUERY',
			filter: function (personas) {
				return $.map(personas, function (movie) {
					return {
						value: movie.value,
						id: movie.id,
					};
				});
			}
		}
	});
	personas.initialize();
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').typeahead(null,{
		displayKey: 'value',
		source: personas.ttAdapter()
	}).on('typeahead:selected', function (event, datum) {
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val(datum.value);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(datum.id);
        evaluarPaciente(datum.id);
	}); 

	function evaluarPaciente(id) {
		$.ajax({
			url: 'baja/evaluarPaciente?id='+id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				$("#estadoanterior").val("Cargando...");
				$("#tablaBajas").html("Cargando...");
			},
			success: function(e) {
				$("#estadoanterior").val(e.estado);
				$("#tablaBajas").html(e.tabla);
				//alert(e.selectito);
				$("#estadoactual").html(e.selectito);
			}
		});
	}

	function validarInputs() {
		var a = true;
		$('.requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
		return a;
	}

	function mostraripress() {
		$("#mostraripress").addClass("hide");
		$("#mostrarmotivo2").addClass("hide");
		$("#IPRESS").val("").removeClass('requerido');
		if($("#estadoactual").val() == "H") {
			$("#mostraripress").removeClass("hide");
			$("#IPRESS").addClass('requerido');
		}
		if($("#estadoactual").val() == "O") {
			$("#mostrarmotivo2").removeClass("hide");
		}
	}
	function registrarBaja(entidad, idboton) {
		if($("#historia_id").val()=="") {
			alertaG("Te falta elegir a un paciente.");
			return false;
		} else if(!validarInputs()) {
			a = 'Corrige los campos en rojo y vuelve a enviar.';
			alertaG(a);
			return false;
		} else {
			var idformulario = IDFORMMANTENIMIENTO + entidad;
			var data         = submitForm(idformulario);
			var respuesta    = '';
			var btn = $(idboton);
			btn.button('loading');
			data.done(function(msg) {
				respuesta = msg;
			}).fail(function(xhr, textStatus, errorThrown) {
				respuesta = 'ERROR';
			}).always(function() {
				btn.button('reset');
				if(respuesta === 'ERROR'){
				} else {
				  //alert(respuesta);
		            var dat = JSON.parse(respuesta);
					if (dat[0]!==undefined && (dat[0].respuesta=== 'OK')) {
						cerrarModal();
						a = "Movimiento Registrado Correctamente";
		                alertaB(a);
		                buscar('Baja');
					} else {
						mostrarErrores(respuesta, idformulario, entidad);
					}
				}
			});
		}
	}
	setInterval(quitarPadding, 4000);
</script>