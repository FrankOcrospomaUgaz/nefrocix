<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('url' => 'consultanefrologica2/guardarFirmaLaboratorio', 'method' => 'POST', 'onsubmit' => 'return false;', 'files' => true, 'id' => 'formMantenimiento'.$entidad)) !!}
	{!! Form::hidden('listar', 'NO', array('id' => 'listar')) !!}
	<div class="form-group">
		<p class="text-muted" style="margin-bottom:10px;">Imagen de firma y sello que aparece al final del PDF de laboratorio (antes del pie de página). Formatos: PNG, JPG o GIF.</p>
		@if(!empty($tieneFirma))
		<div class="text-center" style="margin-bottom:12px;">
			<img src="consultanefrologica2/firmaLaboratorioImagen?t={{ time() }}" alt="Firma actual" style="max-width:100%;max-height:180px;border:1px solid #ddd;padding:4px;">
			<p class="text-success" style="margin-top:6px;"><i class="fa fa-check"></i> Firma configurada</p>
		</div>
		@else
		<p class="text-warning"><i class="fa fa-exclamation-triangle"></i> No hay firma cargada. El PDF se generará sin firma.</p>
		@endif
	</div>
	<div class="form-group">
		{!! Form::label('archivo', 'Seleccionar imagen:', array('class' => 'control-label')) !!}
		{!! Form::file('archivo', array('class' => 'form-control input-xs', 'id' => 'archivo', 'accept' => 'image/png,image/jpeg,image/gif')) !!}
	</div>
	<div class="form-group">
		<div class="text-right">
			{!! Form::button('<i class="fa fa-upload fa-lg"></i> Guardar firma', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarFirma', 'onclick' => 'guardarFirmaLaboratorio(\''.$entidad.'\')')) !!}
			@if(!empty($tieneFirma))
			{!! Form::button('<i class="fa fa-trash fa-lg"></i> Quitar firma', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnEliminarFirma', 'onclick' => 'eliminarFirmaLaboratorio(\''.$entidad.'\')')) !!}
			@endif
			{!! Form::button('<i class="fa fa-times fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('480');
	$(".closdat").remove();
	$(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
});

function guardarFirmaLaboratorio(entidad) {
	var idformulario = '#formMantenimiento' + entidad;
	var archivo = $(idformulario + ' :input[id="archivo"]')[0].files[0];
	if (!archivo) {
		$('#divMensajeError' + entidad).html('<div class="alert alert-warning">Seleccione una imagen para subir.</div>');
		return;
	}
	var btn = $('#btnGuardarFirma');
	btn.button('loading');
	var data = new FormData($(idformulario)[0]);
	$.ajax({
		url: $(idformulario).attr('action'),
		type: 'POST',
		data: data,
		processData: false,
		contentType: false
	}).done(function(msg) {
		if (msg === 'OK') {
			cerrarModal();
			modal('consultanefrologica2/configuracionFirmaLaboratorio', 'Firma PDF Laboratorio', null);
		} else {
			mostrarErrores(msg, idformulario, entidad);
		}
	}).fail(function() {
		$('#divMensajeError' + entidad).html('<div class="alert alert-danger">Error al subir la imagen.</div>');
	}).always(function() {
		btn.button('reset');
	});
}

function eliminarFirmaLaboratorio(entidad) {
	if (!confirm('¿Quitar la firma del PDF de laboratorio?')) {
		return;
	}
	var btn = $('#btnEliminarFirma');
	btn.button('loading');
	$.post('consultanefrologica2/eliminarFirmaLaboratorio', {_token: '{{ csrf_token() }}'}, function(msg) {
		if (msg === 'OK') {
			cerrarModal();
			modal('consultanefrologica2/configuracionFirmaLaboratorio', 'Firma PDF Laboratorio', null);
		} else {
			$('#divMensajeError' + entidad).html('<div class="alert alert-danger">' + msg + '</div>');
		}
	}).fail(function() {
		$('#divMensajeError' + entidad).html('<div class="alert alert-danger">No se pudo quitar la firma.</div>');
	}).always(function() {
		btn.button('reset');
	});
}
</script>
