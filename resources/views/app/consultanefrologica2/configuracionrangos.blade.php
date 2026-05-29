<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('url' => 'consultanefrologica2/guardarRangosReferenciales', 'method' => 'POST', 'onsubmit' => 'return false;', 'id' => 'formMantenimiento'.$entidad)) !!}

<p class="text-muted" style="margin-bottom:8px;">Edite nombre, unidad o rango referencial. Los cambios se reflejan en los PDFs de laboratorio.</p>

@foreach($secciones as $seccion => $items)
<h4 style="background:#337ab7;color:#fff;padding:5px 8px;margin-top:10px;margin-bottom:4px;font-size:13px;">{{ $seccion }}</h4>
<table class="table table-bordered table-condensed" style="font-size:11px;margin-bottom:4px;">
<thead>
<tr style="background:#f5f5f5;">
	<th width="30%">Nombre del examen</th>
	<th width="15%">Unidad</th>
	<th width="55%">Rango Referencial</th>
</tr>
</thead>
<tbody>
@foreach($items as $item)
<tr>
	<td><input type="text" name="nombre[{{ $item->clave }}]" value="{{ $item->nombre }}" class="form-control input-xs" style="font-size:11px;"></td>
	<td><input type="text" name="unidad[{{ $item->clave }}]" value="{{ $item->unidad }}" class="form-control input-xs" style="font-size:11px;"></td>
	<td><input type="text" name="rango[{{ $item->clave }}]" value="{{ $item->rango_referencial }}" class="form-control input-xs" style="font-size:11px;"></td>
</tr>
@endforeach
</tbody>
</table>
@endforeach

<div class="form-group text-right" style="margin-top:10px;">
	{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar todo', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarRangos', 'onclick' => 'guardarRangosReferenciales(\''.$entidad.'\')')) !!}
	{!! Form::button('<i class="fa fa-times fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'onclick' => 'cerrarModal();')) !!}
</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('800');
	$(".closdat").remove();
	$(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
});

function guardarRangosReferenciales(entidad) {
	var idformulario = '#formMantenimiento' + entidad;
	var btn = $('#btnGuardarRangos');
	btn.button('loading');
	$.ajax({
		url: $(idformulario).attr('action'),
		type: 'POST',
		data: $(idformulario).serialize()
	}).done(function(msg) {
		if (msg === 'OK') {
			cerrarModal();
		} else {
			$('#divMensajeError' + entidad).html('<div class="alert alert-danger">' + msg + '</div>');
		}
	}).fail(function() {
		$('#divMensajeError' + entidad).html('<div class="alert alert-danger">Error al guardar los rangos.</div>');
	}).always(function() {
		btn.button('reset');
	});
}
</script>
