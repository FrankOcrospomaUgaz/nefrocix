<?php
use App\ConsultaNefrologica;
date_default_timezone_set('America/Lima');
$paciente = '';
$pid = '';
$direccion = '';
$sexo = '';
$dni = '';
$afiliacion = '';
$telefono = '';
$ipress = '';
$dpto = '';
$provincia = '';
$distrito = '';

$epo = "";
$hierro = "";
$vita = "";

$fechasMes = "";

$cadenaepo = array();
$cadenahierro = array();
$cadenavita = array();

if($historia !== null) {
	$paciente = $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres;
	$pid = $historia->persona->id;
	$sexo = $historia->persona->sexo;
	$sexo=='M'?$sexo='MASCULINO':$sexo='FEMENINO';
	$direccion = $historia->persona->direccion;
	$dni = $historia->persona->dni;
	$afiliacion = $historia->carnet;
	$telefono = $historia->persona->telefono;
	$ipress = $historia->ipress;
	$dpto = $historia->departamento2->nombre;
	$provincia = $historia->provincia2->nombre;
	$distrito = $historia->distrito2->nombre;
}

if ($hc!==NULL) {
	$epo = $hc->c2;
	$hierro = $hc->c3;
	$vita = $hc->c4;
	$cadenaepo = explode("**", $hc->cadenaepo);
	$cadenahierro = explode("**", $hc->cadenahierro);
	$cadenavita = explode("**", $hc->cadenavita);
}

//cantidad a la semana

$ordencitas = explode(';', $historia->ordencitas);
$frecuencia = count($ordencitas)-1;

//Calculo proximo mes

$mesactual = (int) date("m", strtotime($hc->fecha));
$anito = (int) date("Y", strtotime($hc->fecha));
$messiguiente = "";
//$messiguienten = $mesactual+1;
//SOLO CAMBIO LO QUE ERA PARA EL OTRO MES COMO DE ESTE MES
$messiguienten = $mesactual;

/*if($mesactual == 12) {
    $anito++;
    $messiguienten = 1;
}*/

//cantidad al mes actual

$diasenmes = cal_days_in_month(CAL_GREGORIAN, $messiguienten, $anito);

for ($i = 1; $i <= 31; $i++) { 
    $fechadetratamiento = $anito . "-" . $messiguienten . "-" . $i;
    if (checkdate($messiguienten, $i, $anito)) {
        $var = (date("w", strtotime($fechadetratamiento))==0?7:date("w", strtotime($fechadetratamiento)));
        foreach ($ordencitas as $diacita) {
            if($var == ((int) $diacita)) {
                $fechasMes .= date("d-m-Y", strtotime($fechadetratamiento)) . ";";
            }
        }
    } else {    	
        break;
    }                  
}

$fechasMes = substr($fechasMes, 0, strlen($fechasMes)-1);
$arrayfechasMes = explode(";", $fechasMes);

?>
<style>
	.control-label {
		font-size: 12px;
	}
	.panel {
	  	filter: drop-shadow(2px 2px 2px #333);
	}
	input, select, textarea {
	  	filter: drop-shadow(1px 1px 1px #333);
	}
	.requerido2 { 
		border: 1px solid #f00; 
		background-color: #FFD6CE;
		color: red;
	}
	/* The container */
	.container {
	  	display: block;
	  	position: relative;
	  	padding-left: 30px;
	  	margin-bottom: 6px;
	  	cursor: pointer;
	  	font-size: 15px;
	  	-webkit-user-select: none;
	  	-moz-user-select: none;
	  	-ms-user-select: none;
	  	user-select: none;
	}

	/* Hide the browser's default radio button */
	.container input {
	  	position: absolute;
	  	opacity: 0;
	  	cursor: pointer;
	}

	.slider {
	  	position: absolute;
	  	cursor: pointer;
	  	top: 0;
	  	left: 0;
	  	right: 0;
	  	bottom: 0;
	  	background-color: #ccc;
	  	-webkit-transition: .4s;
	  	transition: .4s;
	}

	.slider:before {
	  	position: absolute;
	  	content: "";
	  	height: 17px;
	  	width: 17px;
	  	left: 2px;
	  	bottom: 2px;
	  	background-color: white;
	  	-webkit-transition: .4s;
	  	transition: .4s;
	}

	input:checked + .slider {
	  	background-color: green;
	}

	input:focus + .slider {
	  	box-shadow: 0 0 1px #2196F3;
	}

	input:checked + .slider:before {
	  	-webkit-transform: translateX(24px);
	  	-ms-transform: translateX(24px);
	  	transform: translateX(24px);
	}

	/* Rounded sliders */
	.slider.round {
	  	border-radius: 34px;
	}

	.slider.round:before {
	  	border-radius: 50%;
	}
	input, select, textarea {
	  	text-transform:uppercase;
	}
	.modal-title {
		color:red;
		font-family: Monospace;
	}

	table {
	    width: 100%;
	    border: 1px solid #000;
	}
	th, td {
	    text-align: left;
	    vertical-align: top;
	    border: 1px solid #000;
	    border-collapse: collapse;
	    padding: 0.3em;
	    caption-side: bottom;
	}
	th {
	    background: #eee;
	}
</style>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($hc, $formData) !!}	
	{!! Form::hidden('idcn', $hc->id, array('id' => 'idcn')) !!}
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('cadenahierro', $hc->cadenahierro, array('id' => 'cadenahierro')) !!}
	{!! Form::hidden('cadenavita', $hc->cadenavita, array('id' => 'cadenavita')) !!}
	{!! Form::hidden('cadenaepo', $hc->cadenaepo, array('id' => 'cadenaepo')) !!}
	<div class="panel-group">
		<div class="form-group">
			<div class="col-lg-4 col-md-4 col-sm-4">
			  	<div class="panel panel-warning">
			  		<div class="panel-heading">
			  			<div class="form-group">
							{!! Form::label('', "EPOETINA", array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
							{!! Form::label('haycadenaepo', "HAY", array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								{!! Form::text('haycadenaepo', $epo, array('class' => 'form-control input-sm', 'id' => 'haycadenaepo', "readonly")) !!}
							</div>
							{!! Form::label('acumcadenaepo', "ACUM.", array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								{!! Form::text('acumcadenaepo', null, array('class' => 'form-control input-sm', 'id' => 'acumcadenaepo', "readonly")) !!}
							</div>							
						</div>
			  		</div>
			    	<div class="panel-body">
			    		<table border="1">
			    			<tr>
			    				<td width="70%" style="text-align: center; font-weight: bold;">FECHA</td>
			    				<td width="30%" style="text-align: center; font-weight: bold;">CANTIDAD</td>
			    			</tr>
					    	@for($i=0;$i<count($arrayfechasMes);$i++)				    		
				    			<tr>
				    				<td style="text-align: center;">{{$arrayfechasMes[$i]}}</td>
				    				<td style="text-align: center;">
				    					{!! Form::text('', ($hc->cadenaepo!==NULL&&$hc->cadenaepo!==""?(explode(";", $cadenaepo[$i])[1]):""), array('class' => 'form-control cadenaepo numerillo input-xs', "onkeyup"=>"sumaUnidades('cadenaepo');", "data-fecha"=>date("d", strtotime($arrayfechasMes[$i])))) !!}
				    				</td>
				    			</tr>
							@endfor
						</table>
			    	</div>
			  	</div>
		    </div>
		    <div class="col-lg-4 col-md-4 col-sm-4">
			  	<div class="panel panel-success">
			  		<div class="panel-heading">
			  			<div class="form-group">
							{!! Form::label('', "HIERRO", array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
							{!! Form::label('haycadenahierro', "HAY", array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								{!! Form::text('haycadenahierro', $hierro, array('class' => 'form-control input-sm', 'id' => 'haycadenahierro', "readonly")) !!}
							</div>
							{!! Form::label('acumcadenahierro', "ACUM.", array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								{!! Form::text('acumcadenahierro', null, array('class' => 'form-control input-sm', 'id' => 'acumcadenahierro', "readonly")) !!}
							</div>							
						</div>
			  		</div>
			    	<div class="panel-body">
						<table border="1">
			    			<tr>
			    				<td width="70%" style="text-align: center; font-weight: bold;">FECHA</td>
			    				<td width="30%" style="text-align: center; font-weight: bold;">CANTIDAD</td>
			    			</tr>
					    	@for($i=0;$i<count($arrayfechasMes);$i++)				    		
				    			<tr>
				    				<td style="text-align: center;">{{$arrayfechasMes[$i]}}</td>
				    				<td style="text-align: center;">
				    					{!! Form::text('', ($hc->cadenahierro!==NULL&&$hc->cadenahierro!==""?(explode(";", $cadenahierro[$i])[1]):""), array('class' => 'form-control cadenahierro numerillo input-xs', "onkeyup"=>"sumaUnidades('cadenahierro');", "data-fecha"=>date("d", strtotime($arrayfechasMes[$i])))) !!}
				    				</td>
				    			</tr>
							@endfor
						</table>
			    	</div>
			  	</div>
		    </div>
		    <div class="col-lg-4 col-md-4 col-sm-4">
			  	<div class="panel panel-danger">
			  		<div class="panel-heading">			  			
			  			<div class="form-group">
							{!! Form::label('', "VITAMINA B12", array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							{!! Form::label('haycadenavita', "HAY", array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								{!! Form::text('haycadenavita', $vita, array('class' => 'form-control input-sm', 'id' => 'haycadenavita', "readonly")) !!}
							</div>
							{!! Form::label('acumcadenavita', "ACUM.", array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								{!! Form::text('acumcadenavita', null, array('class' => 'form-control input-sm', 'id' => 'acumcadenavita', "readonly")) !!}
							</div>							
						</div>
			  		</div>
			    	<div class="panel-body">
						<table border="1">
			    			<tr>
			    				<td width="70%" style="text-align: center; font-weight: bold;">FECHA</td>
			    				<td width="30%" style="text-align: center; font-weight: bold;">CANTIDAD</td>
			    			</tr>
					    	@for($i=0;$i<count($arrayfechasMes);$i++)				    		
				    			<tr>
				    				<td style="text-align: center;">{{$arrayfechasMes[$i]}}</td>
				    				<td style="text-align: center;">
				    					{!! Form::text('', ($hc->cadenavita!==NULL&&$hc->cadenavita!==""?(explode(";", $cadenavita[$i])[1]):""), array('class' => 'form-control cadenavita numerillo input-xs', "onkeyup"=>"sumaUnidades('cadenavita');", "data-fecha"=>date("d", strtotime($arrayfechasMes[$i])))) !!}
				    				</td>
				    			</tr>
							@endfor
						</table>
			    	</div>
			  	</div>
		    </div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarResultados();')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1400');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
	$('.numerillo').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	sumaUnidades("cadenaepo");
	sumaUnidades("cadenavita");
	sumaUnidades("cadenahierro");
});

function validarInputs() {
	var a = true;
	if($("#haycadenaepo").val()!==$("#acumcadenaepo").val()) {
		a = false;
	}
	if($("#haycadenavita").val()!==$("#acumcadenavita").val()) {
		a = false;
	}
	if($("#haycadenahierro").val()!==$("#acumcadenahierro").val()) {
		a = false;
	}
	return a;
}

function sumaUnidades(clase) {
	var sum = 0;
	var cadenahide = "";
	$("."+clase).each(function(index, el) {
		if($(this).val()!=="") {
			sum += parseFloat($(this).val());			
		}
		cadenahide += $(this).data("fecha") + ";" + $(this).val() + "**";
	});
	$("#acum"+clase).val(sum);
	$("#"+clase).val(cadenahide);
}

function registrarResultados() {
	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	if(!validarInputs()) {
		a = 'Corrige las Cantidades de lo que hay para el mes y lo acumulado';
		alertaG(a);
		$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Guardar');
		return false;
	} else {
		$.ajax({
	        type: "POST",
	        url: "consultanefrologica/storeprogramarmedicamentos",
	        data: $('#formMantenimiento{{ $entidad }}').serialize(),
	        beforeSend: function() {
	        	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	        },
	        success: function(a) {
	        	if(a == 'OK') {
	        		alertaB('PROGRAMACION REGISTRADA CORRECTAMENTE...');
	        		cerrarModal();
	        		buscar('ConsultaNefrologica');
	        	}else{
	        		alertaG('OCURRIÓ UN ERROR AL GUARDAR, VUELVA A INTENTAR...');
	        	}
	        },
			error: function() {
				alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
		    }
	    });
	}
}

</script>