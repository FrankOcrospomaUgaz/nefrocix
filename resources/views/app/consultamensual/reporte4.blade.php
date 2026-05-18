<?php
date_default_timezone_set('America/Lima');
$paciente = '';
$pid = '';
$cid = '0';
$direccion = '';
$numero = '';
$dni = '';
$afiliacion = '';
$telefono = '';
$ipress = '';
$dpto = '';
$provincia = '';
$distrito = '';
$hisnu = '';
$resultimo = '';
$diagnostico = '';
$diagnostico2 = '';
$recoge = '';
$recodi = '';
$fecha = date('Y-m-d');
$hora = date('H:i');
$dia = "";

if($historia !== null) {
	$paciente = $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres;
	$pid = $historia->persona->id;
	$numero = $historia->numero;
	$direccion = $historia->persona->direccion;
	$dni = $historia->persona->dni;
	$afiliacion = $historia->carnet;
	$telefono = $historia->persona->telefono;
	$ipress = $historia->ipress;
	$dpto = $historia->departamento2->nombre;
	$provincia = $historia->provincia2->nombre;
	$distrito = $historia->distrito2->nombre;
}

if($hc !== null) {
	$cid = $hc->id;
	$hisnu = $hc->txtHistoriaNutricional;
	$resultimo = $hc->txtReultimo;
	$diagnostico = $hc->txtDiagnostico;
	$diagnostico2 = $hc->txtDiagnostico2;
	$recoge = $hc->txtRecoge;
	$recodi = $hc->txtRedie;
	$dia = date('d', strtotime($hc->fecha_atencion));
	$fecha = date('m/Y', strtotime($hc->fecha));
	$hora = date('H:i:s', strtotime($hc->fecha_atencion));
}
?>
<style>
	.input-group-addon {
		padding: 5px;
	}
	.control-label {
		font-size: 12px;
	}
	.panel {
	  	filter: drop-shadow(2px 2px 2px #333);
	}
	input, select, textarea, .input-group-addon {
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

	/* Create a custom radio button */
	.checkmark {
	  	position: absolute;
	  	top: 0;
	  	left: 0;
	  	height: 20px;
	  	width: 20px;
	  	background-color: #eee;
	  	border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.container:hover input ~ .checkmark {
	  	background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.container input:checked ~ .checkmark {
	  	background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
	  	content: "";
	  	position: absolute;
	  	display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.container input:checked ~ .checkmark:after {
	  	display: block;
	}

	/* Style the indicator (dot/circle) */
	.container .checkmark:after {
	 	top: 6px;
		left: 6px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}

	/* PARA LOS CHECKBOX */
	.switch {
	  	position: relative;
	  	display: inline-block;
	  	width: 45px;
	  	height: 21px;
	  	margin-top: 15px;
	}

	.switch input { 
	  	opacity: 0;
	  	width: 0;
	  	height: 0;
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
		color:blue;
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
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="panel-group">
	  	<div class="form-group">
	  		<div class="col-lg-4 col-md-4 col-sm-4">
			  	<div class="panel panel-warning">
			  		<div class="panel-heading">DATOS DE FILIACIÓN DEL PACIENTE</div>
			    	<div class="panel-body">
						<div class="form-group">
							{!! Form::label('txtPaciente', 'Paciente', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-10 col-md-10 col-sm-10">
								{!! Form::text('txtPaciente', $paciente, array('class' => 'form-control input-sm', 'id' => 'txtPaciente', 'readonly')) !!}
								{!! Form::hidden('id4', $cid, array('id' => 'id4')) !!}
								{!! Form::hidden('persona_id', $pid, array('id' => 'persona_id')) !!}
							</div>							
						</div>
						<div class="form-group">
							{!! Form::label('txtDireccion', 'Dirección', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-10 col-md-10 col-sm-10">
								{!! Form::text('txtDireccion', $direccion, array('class' => 'form-control input-sm', 'id' => 'txtDireccion', 'readonly')) !!}
							</div>														
							
						</div>
						<div class="form-group">
							{!! Form::label('txtDNI', 'DNI/CE', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::text('txtDNI', $dni, array('class' => 'form-control input-sm', 'id' => 'txtDNI', 'readonly')) !!}
							</div>
							{!! Form::label('txtAfiliacion', 'Afiliación', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::text('txtAfiliacion', $afiliacion, array('class' => 'form-control input-sm', 'id' => 'txtAfiliacion', 'readonly')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('txtTelefonos', 'Teléfono', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::text('txtTelefonos', $telefono, array('class' => 'form-control input-sm', 'id' => 'txtTelefonos', 'readonly')) !!}
							</div>
							{!! Form::label('txtIPRESS', 'Proced.', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::text('txtIPRESS', $ipress, array('class' => 'form-control input-sm', 'id' => 'txtIPRESS', 'readonly')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('txtDepartamento', 'Depto.', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-10 col-md-10 col-sm-10">
								{!! Form::text('txtDepartamento', $dpto, array('class' => 'form-control input-sm', 'id' => 'txtDepartamento', 'readonly')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('txtProvincia', 'Provincia', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-10 col-md-10 col-sm-10">
								{!! Form::text('txtProvincia', $provincia, array('class' => 'form-control input-sm', 'id' => 'txtProvincia', 'readonly')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('txtDistrito', 'Distrito', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-10 col-md-10 col-sm-10">
								{!! Form::text('txtDistrito', $distrito, array('class' => 'form-control input-sm', 'id' => 'txtDistrito', 'readonly')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('diaconsulta1', 'Día Consulta', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('diaconsulta1', $dia, array('class' => 'form-control input-sm requerido', 'id' => 'diaconsulta1')) !!}
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('fecha1', $fecha, array('class' => 'form-control input-sm', 'id' => 'fecha1', "readonly")) !!}
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::time('hora1', $hora, array('class' => 'form-control input-sm', 'id' => 'hora1')) !!}
							</div>
						</div>
						<hr>
						<div class="form-group">
							{!! Form::label('diaconsulta1', 'Consolidado Medicamentos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								<button class="btn btn-info btn-sm" title="Visualizar" onclick="modal('consultamensual/verconsolidadomedicamentos?id={{ $pid }}&anno={{$year}}', 'Consolidado Medicamentos<button type=\'button\' class=\'close closdat\' onclick=\'cerrarModal();\' title=\'Cerrar Ventana\'><i style=\'font-weight:bold;color:red; font-weight: bold;\' class=\'glyphicon glyphicon-remove-circle\'></i></button>', this);"><i class="fa fa-print"></i> </button>
								<button class="btn btn-success btn-sm" title="Descargar" onclick="consolidadoMedicamentos('{{ $pid }}');"><i class="fa fa-download"></i> </button>
							</div>
							{!! Form::label('diaconsulta1', 'Consolidado Resultados', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								<button class="btn btn-warning btn-sm" title="Visualizar" onclick="modal('consultamensual/verresultadosporpaciente?historia_id={{$hid}}&anno={{$year}}', 'Consolidado Resultados<button type=\'button\' class=\'close closdat\' onclick=\'cerrarModal();\' title=\'Cerrar Ventana\'><i style=\'font-weight:bold;color:red; font-weight: bold;\' class=\'glyphicon glyphicon-remove-circle\'></i></button>', this);"><i class="fa fa-print"></i> </button>
								<button class="btn btn-success btn-sm" title="Descargar" onclick="historialResultadosPorPaciente({{$hid}}, {{$year}})"><i class="fa fa-download"></i> </button>
							</div>
						</div>
			    	</div>
			  	</div>
		    </div>
		    <div class="col-lg-8 col-md-8 col-sm-8">
		    	<div class="panel panel-success">
			  		<div class="panel-heading">CONSULTA</div>
			    	<div class="panel-body">
			    		<div class="form-group">
							{!! Form::label('txtHistoria', 'Historia Clínica', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::text('txtHistoria', $numero, array('class' => 'form-control input-sm requerido', 'id' => 'txtHistoria', 'readonly')) !!}
							</div>
							{!! Form::label('txtHistoriaNutricional', 'Historia Nutricional', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::text('txtHistoriaNutricional', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtHistoriaNutricional')) !!}
							</div>
						</div>
						<strong style="color: red;">MEDIDAS ANTROPOMÉTRICAS</strong>
						<br><br>
						<div class="form-group">
							{!! Form::label('txtPesoseco', 'Peso Seco', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtPesoseco', null, array('onkeyup' => 'IMC();', 'class' => 'form-control input-sm requerido numerin', 'id' => 'txtPesoseco')) !!}
									<span class="input-group-addon">Kg</span>
								</div>								
							</div>
							{!! Form::label('txtPesoactual', 'Peso Actual', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtPesoactual', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtPesoactual')) !!}
									<span class="input-group-addon">Kg</span>
								</div>								
							</div>
							{!! Form::label('txtPesousual', 'Peso Usual', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtPesousual', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtPesousual')) !!}
									<span class="input-group-addon">Kg</span>
								</div>								
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('txtPesoideal', 'Peso Ideal', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtPesoideal', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtPesoideal')) !!}
									<span class="input-group-addon">Kg</span>
								</div>								
							</div>
							{!! Form::label('txtTalla', 'Talla', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtTalla', null, array('onkeyup' => 'IMC();', 'class' => 'form-control input-sm requerido numerin', 'id' => 'txtTalla')) !!}
									<span class="input-group-addon">m</span>
								</div>								
							</div>
							{!! Form::label('txtIMC', 'IMC', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								{!! Form::text('txtIMC', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtIMC', 'readonly')) !!}
							</div>
						</div>
						<strong style="color: red;">RESULTADOS BIOQUÍMICOS</strong>
						<br><br>
						<div class="form-group">
							{!! Form::label('txtHemoglobina', 'Hemoglobina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtHemoglobina', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtHemoglobina')) !!}
									<span class="input-group-addon">g/dl</span>
								</div>								
							</div>
							{!! Form::label('txtHematocrito', 'Hematocrito', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtHematocrito', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtHematocrito')) !!}
									<span class="input-group-addon">%</span>
								</div>								
							</div>
							{!! Form::label('txtUreapost', 'Urea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtUreapost', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtUreapost')) !!}
									<span class="input-group-addon">mg/dl</span>
								</div>								
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('', '', array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label')) !!}
							{!! Form::label('txtUreapre', 'Urea Pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtUreapre', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtUreapre')) !!}
									<span class="input-group-addon">mg/dl</span>
								</div>								
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('txtCreatinina', 'Creatinina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtCreatinina', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtCreatinina')) !!}
									<span class="input-group-addon">mg/dl</span>
								</div>								
							</div>
							{!! Form::label('txtCalcio', 'Calcio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtCalcio', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtCalcio')) !!}
									<span class="input-group-addon">mg/dl</span>
								</div>								
							</div>
							{!! Form::label('txtFosforo', 'Fósforo', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="input-group">
									{!! Form::text('txtFosforo', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtFosforo')) !!}
									<span class="input-group-addon">mg/dl</span>
								</div>								
							</div>
						</div>
						<strong style="color: red;">OTROS DATOS</strong>						
						<br><br>
						<div class="form-group">
							{!! Form::label('txtReultimo', 'Resultado del último Malnutrition Inflammation Score', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::textarea('txtReultimo', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtReultimo', 'rows'=>'5')) !!}
							</div>
							{!! Form::label('txtDiagnostico4', 'Diagnóstico nutricional', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::textarea('txtDiagnostico4', $diagnostico, array('class' => 'form-control input-sm requerido', 'id' => 'txtDiagnostico4', 'rows'=>'5')) !!}
							</div>
							{!! Form::label('txtRecoge', 'Recomenda- ciones generales', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::textarea('txtRecoge', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtRecoge', 'rows'=>'5')) !!}
							</div>
						</div>
						<div class="form-group">							
							{!! Form::label('txtRedie', 'Recomenda- ciones dietéticas', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::textarea('txtRedie', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtRedie', 'rows'=>'5')) !!}
							</div>
							{!! Form::label('txtDiagnostico44', 'Cie 10', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('txtDiagnostico44', '', array('class' => 'form-control input-sm', 'id' => 'txtDiagnostico44')) !!}
								{!! Form::hidden('cadenacies4', $diagnostico2, array('id' => 'cadenacies4')) !!}
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4">
								<table style="width:100%" border="1">
									<thead id="cabeceracie4">
										<tr>
											<th width="90%" style="font-size: 13px !important;">Descripción</th>
											<th width="10%" style="font-size: 13px !important;">Eliminar</th>
										</tr>
									</thead>
									<tbody id="detallecie4"></tbody>
								</table>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('txtIntervencion', 'Intervención (Para consolidado)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::textarea('txtIntervencion', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtIntervencion', 'rows'=>'5')) !!}
							</div>
							{!! Form::label('txtObservacion2', 'Observación (Para consolidado)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::textarea('txtObservacion2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtObservacion2', 'rows'=>'5')) !!}
							</div>
						</div>
			    	</div>
			  	</div>
		    </div>
	  	</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarReporte4();')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1300');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
	$('.numerin').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
}); 

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
	}
});

function eliminarDetalleCie(comp,tipo){
	(($(comp).parent()).parent()).remove();
	var cadenacies = '';
	$('#detallecie4 tr').each(function(index, el) {
		cadenacies += $(this).data('id') + ';';
	});
	$("#cadenacies4").val(cadenacies);
}

var cie10s1 = new Bloodhound({
	datumTokenizer: function (d) {
		return Bloodhound.tokenizers.whitespace(d.value);
	},
	limit: 5,
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
		url: 'historiaclinica/cie10autocompletar/%QUERY',
		filter: function (cie10s1) {
			return $.map(cie10s1, function (cie10) {
				return {
					value: cie10.value,
					id: cie10.id,
				};
			});
		}
	}
});

cie10s1.initialize();
$("#txtDiagnostico44").typeahead(null,{
	displayKey: 'value',
	source: cie10s1.ttAdapter()
}).on('typeahead:selected', function (object, datum) {
	$("#txtDiagnostico44").val("");
	$('#txtDiagnostico44').typeahead('val','');
	var cie_id = datum.id;
	var existe = false;
	$("#detallecie4 tr").each(function(){
		if(cie_id == this.id){
			existe = true;
		}
	});
	if(!existe){
		fila =  '<tr data-id="'+ datum.id +'" align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
		$("#detallecie4").append(fila);
		var cadenacies = '';
		$('#detallecie4 tr').each(function(index, el) {
			cadenacies += $(this).data('id') + ';';
		});
		$("#cadenacies4").val(cadenacies);
	}

});  

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

function registrarReporte4() {
	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	if($("#cadenacies4").val() === '') {
		a = 'Debes seleccionar al menos un CIE10 en diagnóstico.';
		alertaG(a);
		$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Registrar');
		return false;
	}
	if(!validarInputs()) {
		a = 'Corrige los campos en rojo y vuelve a enviar.';
		alertaG(a);
		$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Registrar');
		return false;
	} else {
		$.ajax({
	        type: "POST",
	        url: "consultamensual/storereporte4",
	        data: $('#formMantenimiento{{ $entidad }}').serialize(),
	        beforeSend: function() {
	        	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	        },
	        success: function(a) {
	        	if(a == 'OK') {
	        		alertaB('ATENCIÓN EN NUTRICIÓN FINALIZADA CORRECTAMENTE...');
	        		cerrarModal();
	        		buscar('ConsultaMensual');
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

function inicializarTablaCies(cies) {
	$.ajax({
		url: "historiaclinica/inicializarTablaCies",
		data: {cies: cies},
		beforeSend: function() {
			$("#detallecie4").html('<tr><td colspan="2">Cargando...</td></tr>');
		}, 
	})
	.done(function(a) {
		$("#detallecie4").html(a);
	});    	
}

function IMC() {
	var peso = $('#txtPesoseco').val();
	var talla = $('#txtTalla').val();
	if(peso !== '') { peso = parseFloat(peso); } else { peso = 0; }
	if(talla !== '') { talla = parseFloat(talla); } else { talla = 0; }
	var imc = (parseFloat((peso/(talla*talla))).toFixed(2)).toString();
	$('#txtIMC').val(imc);
}

@if($hc !== NULL)
	inicializarTablaCies('{{ $hc->txtDiagnostico2 }}');
@endif
</script>