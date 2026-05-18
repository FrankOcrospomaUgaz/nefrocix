<?php
date_default_timezone_set('America/Lima');

use App\Cie;


$ciesdeconsultaadicionales = "";
?>
<style>
	.panel {
	  	filter: drop-shadow(2px 2px 2px #333);
	}
	input, select, textarea {
	  	filter: drop-shadow(1px 1px 1px #333);
	}
	.requerido222 { 
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
<meta charset="utf-8">
{!! Form::model($hc, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('idformato', $id, array('id' => 'idformato')) !!}
	{!! Form::hidden('_token', csrf_token()) !!}
	{!! Form::hidden('formatomensual', $mens) !!}
	{!! Form::hidden('formatotipo', $tip) !!}
	<div class="form-group">
		<div class="col-lg-6 col-md-6 col-sm-6">
			<div class="form-group">
				<div class="col-lg-2 col-md-2 col-sm-2">
					{!! Form::label('n', 'Número', array('class' => 'control-label')) !!}
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3">
					{!! Form::label('nn', '00025388', array('class' => 'form-control input-sm')) !!}
				</div>	
				<div class="col-lg-1 col-md-1 col-sm-1">
					{!! Form::label('nnn', '-', array('class' => 'control-label')) !!}
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2">
					{!! Form::label('nnnn', substr($codigoano, 2, 2), array('class' => 'form-control input-sm')) !!}
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1">
					{!! Form::label('nnnnn', '-', array('class' => 'control-label')) !!}
				</div>			
				<div class="col-lg-3 col-md-3 col-sm-3">
					{!! Form::text('numeroformato', $hc->numeroformato, array('style' => 'color:red;font-weight:bold', 'class' => 'form-control requerido222', 'id' => 'numeroformato', 'placeholder' => 'Ingrese numeroformato')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('txtReconsideracion', 'Reconsideración', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-2 col-md-2 col-sm-2">
					<label class="switch">
					  	<input type="checkbox" data-aa="0" id="cbxReconsideracion">
					  	<span class="slider round"></span>
					</label>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6">
					{!! Form::text('txtReconsideracion', $hc->numeroconsideracion, array('class' => 'form-control', 'id' => 'txtReconsideracion', 'style'=>'display:none;')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('td', 'TD', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-3 col-md-3 col-sm-3">
					{!! Form::text('td', ($hc->td !== NULL && $hc->td !== '') ? $hc->td : 1, array('class' => 'form-control', 'id' => 'td', 'placeholder' => 'Ingrese td')) !!}
				</div>
				{!! Form::label('fecha_atencion', 'Fecha:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-5 col-md-5 col-sm-5">
					{!! Form::text('fecha_atencion', ($hc->fecha_atencion !== NULL && $hc->fecha_atencion !== '') ? date('d-m-Y', strtotime($hc->fecha_atencion)) : date('d-m-Y'), array('class' => 'form-control', 'id' => 'fecha_atencion', 'readonly'=>'readonly')) !!}
				</div>
			</div>
			@if($mens !== 1)
				<div class="form-group">
					{!! Form::label('td', 'Res. Mens.', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
					<div class="col-lg-3 col-md-3 col-sm-3">
						{!! Form::select('mensuales2', array('2'=>'NO', '1'=>'SI'), null, array('class' => 'form-control', 'id' => 'mensuales2')) !!}
					</div>
					{!! Form::label('td', 'Datos', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
					<div class="col-lg-5 col-md-5 col-sm-5">
						{!! Form::select('mensuales', array('1'=>'NUEVO', '2'=>'NUEVO - MENSUAL', '3'=>'MENSUALES', '4'=>'BIMENSUALES', '5'=>'TRIMESTRALES', '6'=>'SEMESTRALES'), null, array('class' => 'form-control', 'id' => 'mensuales')) !!}
					</div>
				</div>
			@endif
			@if($tip !== 0)
			<div class="form-group">
				<div class="col-lg-5 col-md-5 col-sm-5"></div>
				{!! Form::label('fechaformato', 'Fecha FUA:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-5 col-md-5 col-sm-5">
					{!! Form::date('fechaformato', ($hc->fecha_atencion !== NULL && $hc->fecha_atencion !== '') ? date('Y-m-d', strtotime($hc->fecha_atencion)) : date('Y-m-d'), array('class' => 'form-control', 'id' => 'fechaformato')) !!}
				</div>
			</div>
			@endif
			@if($tip === 0)
			<div class="form-group">
				<div class="col-lg-5 col-md-5 col-sm-5"></div>
				{!! Form::label('horaformato', 'Hora FUA:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-5 col-md-5 col-sm-5">
					{!! Form::time('horaformato', ($hc->fecha_atencion !== NULL && $hc->fecha_atencion !== '') ? date('H:i', strtotime($hc->fecha_atencion)) : date('Y-m-d'), array('class' => 'form-control', 'id' => 'horaformato')) !!}
				</div>
			</div>
			@endif
			<div class="form-group">
				{!! Form::label('nombreformato', 'Paciente', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::text('nombreformato', $paciente->apellidopaterno .' '.$paciente->apellidomaterno.' '.$paciente->nombres, array('class' => 'form-control', 'id' => 'nombreformato', 'placeholder' => 'Ingrese nombreformato', 'readonly'=>'readonly')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('historiaformato', 'Historia', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-4 col-md-4 col-sm-4">
					{!! Form::text('historiaformato', $historia->numero, array('class' => 'form-control', 'id' => 'historiaformato', 'placeholder' => 'Ingrese historiaformato', 'readonly'=>'readonly')) !!}
				</div>
				{!! Form::label('dniformato', 'DNI/CE', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-4 col-md-4 col-sm-4">
					{!! Form::text('dniformato', $paciente->dni, array('class' => 'form-control', 'id' => 'dniformato', 'placeholder' => 'Ingrese dniformato', 'readonly'=>'readonly')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('prestacionformato', 'Prestación', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::select('prestacionformato2', array('1'=>'Consulta Externa', '2'=>'Atención de procedimientos Ambulatorios'), $prestacion, array('class' => 'form-control', 'id' => 'prestacionformato2', "disabled")) !!}
					{!! Form::hidden('prestacionformato', $prestacion, array('id' => 'prestacionformato')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('regimenformato', 'Régimen', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::text('regimenformato', $historia->regimen, array('class' => 'form-control', 'id' => 'regimenformato', 'placeholder' => 'Ingrese regimenformato', 'readonly'=>'readonly')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('observacionformato1', 'Obs. 1', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::textarea('observacionformato1', $hc->observacionformato1==""||$hc->observacionformato1==null?$historia->observaciones:$hc->observacionformato1, array('class' => 'form-control', 'id' => 'observacionformato1', 'placeholder' => 'Ingrese observación', 'rows'=>'2')) !!}
				</div>
			</div>	
			<div class="form-group">
				{!! Form::label('observacionformato2', 'Obs. 2', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::textarea('observacionformato2', $hc->observacionformato2, array('class' => 'form-control', 'id' => 'observacionformato2', 'placeholder' => 'Ingrese observación', 'rows'=>'2')) !!}
				</div>
			</div>	
			<div class="form-group">
				{!! Form::label('observacionformato3', 'Obs. 3', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::textarea('observacionformato3', $hc->observacionformato3, array('class' => 'form-control', 'id' => 'observacionformato3', 'placeholder' => 'Ingrese observación', 'rows'=>'2')) !!}
				</div>
			</div>	
			<div class="form-group">
				{!! Form::label('observacionformato4', 'Obs. 4', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::textarea('observacionformato4', $hc->observacionformato4, array('class' => 'form-control', 'id' => 'observacionformato4', 'placeholder' => 'Ingrese observación', 'rows'=>'2')) !!}
				</div>
			</div>					
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 text-right">
			<div class="form-group">
				{!! Form::label('medico', 'Medico', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::text('medico', $hc->doctor == NULL ? "" : ($hc->doctor->apellidopaterno .' '.$hc->doctor->apellidomaterno.' '.$hc->doctor->nombres), array('class' => 'form-control', 'id' => 'medico', 'placeholder' => 'Ingrese medico')) !!}
					<input type="hidden" name="medico_id" id="medico_id" value="{{ $hc->doctor == NULL ? "" : $hc->doctor->id }}">
				</div>
			</div>
			<!--<div class="form-group">
				{!! Form::label('colegiaturaformato', 'Colegiatura', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-4 col-md-4 col-sm-4">
					{!! Form::text('colegiaturaformato', $doctor->cmp, array('class' => 'form-control', 'id' => 'colegiaturaformato', 'placeholder' => 'Ingrese colegiaturaformato', 'readonly'=>'readonly')) !!}
				</div>
				{!! Form::label('dniformato2', 'DNI/CE', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-4 col-md-4 col-sm-4">
					{!! Form::text('dniformato2', $doctor->dni, array('class' => 'form-control', 'id' => 'dniformato2', 'placeholder' => 'Ingrese dniformato2', 'readonly'=>'readonly')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('especialidadformato', 'Especial.', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
				<div class="col-lg-10 col-md-10 col-sm-10">
					{!! Form::text('especialidadformato', $doctor->especialidad === NULL?'-':$doctor->especialidad->nombre, array('class' => 'form-control', 'id' => 'especialidadformato', 'placeholder' => 'Ingrese especialidadformato', 'readonly'=>'readonly')) !!}
				</div>
			</div>
			-->		
			<div class="form-group">
				{!! Form::label('cie1022', 'Cie10', array('class' => 'col-sm-2 control-label')) !!}
				<div class="col-sm-10">
					{!! Form::text('cie1022', '', array('class' => 'form-control input-sm', 'id' => 'cie1022')) !!}
					{!! Form::hidden('cadenacies22', '5184,D;12422,D;1579,D;', array('id' => 'cadenacies22')) !!}
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<table style="width:100%" border="1">
						<thead id="cabeceracie22">
							<tr>
								<th width='80%' style="font-size: 13px !important;">Descripción</th>
								<th width='10%' style="font-size: 13px !important;">Tipo Dx</th>
								<th width='10%' style="font-size: 13px !important;">X</th>
							</tr>
						</thead>
						<tbody id="detallecie22">
							@if($hc->ciesformato===NULL)
								<tr data-id="5184" align="center" id="5184">
									<td style="vertical-align: middle; text-align: left;">N18.0 - Insuficiencia renal terminal</td>
									<td style="vertical-align: middle; text-align: left;">
										<select name="form-control" class="selectito" id="t5184">
											<option value="P">P</option>
											<option value="D" selected="selected">D</option>
											<option value="R">R</option>
										</select>
									</td>
									<td style="vertical-align: middle;">
										<a onclick="eliminarDetalleCie22(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a>
									</td>
								</tr>
								<tr data-id="12422" align="center" id="12422">
									<td style="vertical-align: middle; text-align: left;">Z99.2 - Dependencia de diálisis renal</td>
									<td style="vertical-align: middle; text-align: left;">
										<select name="form-control" class="selectito" id="t12422">
											<option value="P">P</option>
											<option value="D" selected="selected">D</option>
											<option value="R">R</option>
										</select>
									</td>
									<td style="vertical-align: middle;">
										<a onclick="eliminarDetalleCie22(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a>
									</td>
								</tr>
								<tr data-id="1579" align="center" id="1579">
									<td style="vertical-align: middle; text-align: left;">D63.8* - Anemia en otras enfermedades crónicas clasificadas en otra parte</td>
									<td style="vertical-align: middle; text-align: left;">
										<select name="form-control" class="selectito" id="t1579">
											<option value="P">P</option>
											<option value="D" selected="selected">D</option>
											<option value="R">R</option>
										</select>
									</td>
									<td style="vertical-align: middle;">
										<a onclick="eliminarDetalleCie22(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a>
									</td>
								</tr>
								<?php 

								$ciesdeconsulta = NULL;

								//Solo en salud Mental es txtDiagnostico, en los otros es txtDiagnostico2

								if($tip === "1") {
									if($hc->txtDiagnostico!=="") {
										$ciesdeconsulta = explode(';', $hc->txtDiagnostico);
									}
								} else if($tip === "3" || $tip === "4") {
									if($hc->txtDiagnostico2!=="") {
										$ciesdeconsulta = explode(';', $hc->txtDiagnostico2);
									}
								}

								?>

								@if(count($ciesdeconsulta) > 0)
									@foreach($ciesdeconsulta as $ciie)
										@if(trim($ciie)<>"")
											<?php 
												$ciieinf = Cie::find(trim($ciie));
											?>
											@if($ciieinf<>NULL)
												<?php  
													$ciesdeconsultaadicionales .= $ciie . ",D;"
												?>
												<tr data-id="{{ $ciie }}" align="center" id="{{ $ciie }}">
													<td style="vertical-align: middle; text-align: left;">{{ $ciieinf->codigo . " - " . $ciieinf->descripcion }}</td>
													<td style="vertical-align: middle; text-align: left;">
														<select name="form-control" class="selectito" id="t{{ $ciie }}">
															<option value="P">P</option>
															<option value="D" selected="selected">D</option>
															<option value="R">R</option>
														</select>
													</td>
													<td style="vertical-align: middle;">
														<a onclick="eliminarDetalleCie22(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a>
													</td>
												</tr>
											@endif
										@endif
									@endforeach
								@endif
							@endif
						</tbody>
					</table>
				</div>
			</div>

			<!-- -->

			@if($mens == "2")

				<div class="form-group">
					{!! Form::label('medicamentos', 'Medicam.', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-10">
						{!! Form::text('medicamentos', '', array('class' => 'form-control input-sm', 'id' => 'medicamentos')) !!}
						<input name="cadenamedicamentos" id="cadenamedicamentos" type="hidden" value="<?php echo html_entity_decode($hc->txtAdmiMedic); ?>">
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<table style="width:100%" border="1">
							<thead id="cabeceramedicamentos">
								<tr>
									<th width='80%' style="font-size: 13px !important;">Descripción</th>
									<th width='10%' style="font-size: 13px !important;">Cantidad</th>
									<th width='10%' style="font-size: 13px !important;">Eliminar</th>
								</tr>
							</thead>
							<tbody id="detallemedicamentos">
							</tbody>
						</table>
					</div>
				</div>
			@endif

			<!-- -->
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar'.$entidad, 'onclick' => 'registrarFormato();')) !!}
			@if($hc->numeroformato !== '' && $hc->numeroformato !== NULL)
			{!! Form::button('<i class="fa fa-file fa-lg"></i> Reporte de Formato', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnReporte'.$entidad, 'onclick' => 'reporteformato()')) !!}
			@else
			{!! Form::button('<i class="fa fa-file fa-lg"></i> Reporte de Formato', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnReporte'.$entidad, 'onclick' => 'reporteformato()', 'disabled'=>'disabled')) !!}
			@endif
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1000');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$('#numeroformato').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	$('#td').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	$('.numerin').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	@if($mens == "2")
		cargarMedicamentos();
	@endif
	@if(trim($ciesdeconsultaadicionales)!=="")
		$('#cadenacies22').val($('#cadenacies22').val()+"{{ $ciesdeconsultaadicionales }}");
	@endif
}); 
$(document).on('click', '.switch', function(event) {
	event.preventDefault();
	event.stopImmediatePropagation();
	var a = true;
	var t = '1';
	if($('#cbxReconsideracion').attr('data-aa') == '1') {
		a = false;
		t = '0';
		$("#txtReconsideracion").val("");
	}
	$('#cbxReconsideracion').prop('checked', a);
	$('#cbxReconsideracion').attr('data-aa', t);
	if(t === '1') { $('#txtReconsideracion').removeAttr('style').addClass('requerido222').focus(); } else { $('#txtReconsideracion').attr('style', 'display:none;').removeClass('requerido222'); }
});

$("#mensuales2").on("change", function() {
	llenarobservacion1();
});

$("#mensuales").on("change", function() {
	llenarobservacion1();
});

function llenarobservacion1() {
	$("#observacionformato1").val("");
	if($("#mensuales2").val() == "1") {
		var mens = $("#mensuales").val();
		if(mens == "1") { mens = "NUEVOS"; }
		if(mens == "2") { mens = "NUEVOS Y MENSUALES"; }
		if(mens == "3") { mens = "MENSUALES"; }
		if(mens == "4") { mens = "BIMENSUALES"; }
		if(mens == "5") { mens = "TRIMESTRALES"; }
		if(mens == "6") { mens = "SEMESTRALES"; }

		var mensaje = "TOMA DE MUESTRAS PARA ANÁLISIS " + mens;
		$("#observacionformato1").val(mensaje);
	}
}

function llenarobservacion2(id) {
	if(id == 1) {
		$("#observacionformato2").val("");
		var cant = $("#ttt1").val();
		if(cant != "" && cant != "1" && cant != "0") {
			var mensaje = "SE INDICA " + $("#ttt1").val() + " ERITROPOYETINAS POR ANEMIA, INDICACIÓN MÉDICA";
			$("#observacionformato2").val(mensaje);
		}
	}		
}

function reporteformato(){
    window.open("historia/reporteformato?id="+$('#idformato').val()+"&formatomensual={{$mens}}&formatotipo={{$tip}}");
}
var cie10s22 = new Bloodhound({
	datumTokenizer: function (d) {
		return Bloodhound.tokenizers.whitespace(d.value);
	},
	limit: 5,
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
		url: 'historiaclinica/cie10autocompletar/%QUERY',
		filter: function (cie10s22) {
			return $.map(cie10s22, function (cie10) {
				return {
					value: cie10.value,
					id: cie10.id,
				};
			});
		}
	}
});
cie10s22.initialize();
$("#cie1022").typeahead(null,{
	displayKey: 'value',
	source: cie10s22.ttAdapter()
}).on('typeahead:selected', function (object, datum) {
	$("#cie1022").val("");
	var cie_id = datum.id;
	var existe = false;
	$("#detallecie22 tr").each(function(){
		if(cie_id == this.id){
			existe = true;
		}
	});
	if(!existe){
		fila =  '<tr data-id="'+ datum.id +'" align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle; text-align: left;"><select name="form-control" class="selectito" id="t'+ datum.id +'"><option value="P">P</option><option value="D" selected="selected">D</option><option value="R">R</option></select></td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie22(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
		$("#detallecie22").append(fila);
		var cadenacies = '';
		$('#detallecie22 tr').each(function(index, el) {
			cadenacies += $(this).data('id') + ',' + $('#t' + $(this).data('id')).val() + ';';
		});
		$("#cadenacies22").val(cadenacies);
		$('#cie1022').val('');
		$('#cie1022').typeahead('val','');
	}
});

var doctores = new Bloodhound({
	datumTokenizer: function (d) {
		return Bloodhound.tokenizers.whitespace(d.value);
	},
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
		url: 'person/doctorautocompleting/%QUERY',
		filter: function (doctores) {
			return $.map(doctores, function (movie) {
				return {
					value: movie.value,
					id: movie.id
				};
			});
		}
	}
});
doctores.initialize();
$('#medico').typeahead(null,{
	displayKey: 'value',
	source: doctores.ttAdapter()
}).on('typeahead:selected', function (object, datum) {
	$('#medico_id').val(datum.id);
});

var p1 = new Bloodhound({
	datumTokenizer: function (d) {
		return Bloodhound.tokenizers.whitespace(d.value);
	},
	limit: 5,
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
		url: 'producto/productoAutocompletar/%QUERY',
		filter: function (p1) {
			return $.map(p1, function (p_1) {
				return {
					value: p_1.value,
					id: p_1.id,
				};
			});
		}
	}
});
p1.initialize();
$("#medicamentos").typeahead(null,{
	displayKey: 'value',
	source: p1.ttAdapter()
}).on('typeahead:selected', function (object, datum) {
	$("#medicamentos").val(datum.value);

	//--------------------
	var medicamento_id = datum.id;
	var existe = false;
	$("#detallemedicamentos tr").each(function(){
		if(medicamento_id == this.id){
			existe = true;
		}
	});
	if(!existe) {
		fila =  '<tr data-id="'+ datum.id +'" align="center" id="ee'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle; text-align: left;"><input type="text" id="ttt'+ datum.id +'" onkeyup="llenarobservacion2(' + datum.id + ');" class="form-control input-xs inputcito numerin" /></td><td style="vertical-align: middle;"><a onclick="eliminarDetalleMedicamento(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
		$("#detallemedicamentos").append(fila);
		var cadenamedicamentos = '';
		contador = 0;
		$('#detallemedicamentos tr').each(function(index, el) {
			cadenamedicamentos += $(this).data('id') + '&ilid&' + $('#ttt' + $(this).data('id')).val() + '&iliu&';
			contador++;;
		});
		if(contador<6) {
			for (var i = (contador+1); i <= 6; i++) {
				cadenamedicamentos += '&ilid&' + '&iliu&';
			}
		}
		$("#cadenamedicamentos").val(cadenamedicamentos);
		$('#medicamentos').val('');
		$('#medicamentos').typeahead('val','');
	}

	//--------------------
});

function eliminarDetalleCie22(comp,tipo) {
	(($(comp).parent()).parent()).remove();
	var cadenacies = '';
	$('#detallecie22 tr').each(function(index, el) {
		cadenacies += $(this).data('id') + ',' + $('#t' + $(this).data('id')).val() + ';';
	});
	$("#cadenacies22").val(cadenacies);
}

function eliminarDetalleMedicamento(comp,tipo) {
	(($(comp).parent()).parent()).remove();
	var id = (($(comp).parent()).parent()).data("id");
	if(id == "1") {
		$("#observacionformato2").val("");
	}
	var cadenamedicamentos = '';
	contador = 0;
	$('#detallemedicamentos tr').each(function(index, el) {
		cadenamedicamentos += $(this).data('id') + '&ilid&' + $('#ttt' + $(this).data('id')).val() + '&iliu&';
		contador++;;
	});
	if(contador<6) {
		for (var i = (contador+1); i <= 6; i++) {
			cadenamedicamentos += '&ilid&' + '&iliu&';
		}
	}
	$("#cadenamedicamentos").val(cadenamedicamentos);
}

$(document).on('change', '.selectito', function(event) {
	event.preventDefault();
	event.stopImmediatePropagation();
	var cadenacies = '';
	$('#detallecie22 tr').each(function(index, el) {
		cadenacies += $(this).data('id') + ',' + $('#t' + $(this).data('id')).val() + ';';
	});
	$("#cadenacies22").val(cadenacies);
});

$(document).on('keyup', '.inputcito', function(event) {
	event.preventDefault();
	event.stopImmediatePropagation();
	var cadenamedicamentos = '';
	var contador = 0;
	$('#detallemedicamentos tr').each(function(index, el) {
		cadenamedicamentos += $(this).data('id') + '&ilid&' + $('#ttt' + $(this).data('id')).val() + '&iliu&';
		contador++;;
	});
	if(contador<6) {
		for (var i = (contador+1); i <= 6; i++) {
			cadenamedicamentos += '&ilid&' + '&iliu&';
		}
	}
	$("#cadenamedicamentos").val(cadenamedicamentos);
});

function registrarFormato() {
	$("#btnGuardar{{$entidad}}").prop('disabled', true).html('Cargando...');
	if($("#cadenacies22").val() === '') {
		a = 'Debes seleccionar al menos un CIE10.';
		alertaG(a);
		$("#btnGuardar{{$entidad}}").prop('disabled', false).html('<i class="fa fa-check fa-lg"></i> Guardar');
		return false;
	}
	if($("#cadenamedicamentos").val() === '') {
		a = 'Debes seleccionar al menos un medicamento.';
		alertaG(a);
		$("#btnGuardar{{$entidad}}").prop('disabled', false).html('<i class="fa fa-check fa-lg"></i> Guardar');
		return false;
		alerta($("#cadenamedicamentos").val());
	}
	if($("#medico_id").val() === '') {
		a = 'Debes seleccionar un doctor.';
		alertaG(a);
		$("#btnGuardar{{$entidad}}").prop('disabled', false).html('<i class="fa fa-check fa-lg"></i> Guardar');
		return false;
	}
	if(!validarInputs()) {
		a = 'Corrige los campos en rojo y vuelve a enviar.';
		alertaG(a);
		$("#btnGuardar{{$entidad}}").prop('disabled', false).html('<i class="fa fa-check fa-lg"></i> Guardar');
		return false;
	} else {
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/registrarFormato",
	        data: $('#formMantenimiento{{$entidad}}').serialize(),
	        beforeSend: function() {
	        	$("#btnGuardar{{$entidad}}").prop('disabled', true).html('Cargando...');
	        },
	        success: function(a) {
	        	if(a == 'OK') {
	        		alertaB('Datos Listos Para el Formato de Atención.');					
					$('.requerido222').val('');					
	        	}else{
	        		alertaG('OCURRIÓ UN ERROR AL GUARDAR, VUELVA A INTENTAR...');
	        	}
	        	$("#btnReporteHC").prop('disabled', false);
	        	$("#btnGuardar{{$entidad}}").prop('disabled', false).html('<i class="fa fa-check fa-lg"></i> Guardar');	        	
	        	buscar("Fua");
	        	buscar('ConsultaNefrologica');
	        	buscar("ConsultaMensual");
	        },
			error: function() {
				$("#btnGuardar{{$entidad}}").prop('disabled', false).html('<i class="fa fa-check fa-lg"></i> Guardar');
				alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
		    }
	    });
	}
}

function validarInputs() {
	var a = true;
	$('.requerido222').each(function(index, el) {
		if($(this).val().length==0) {
        	a = false;
        	$(this).addClass('requerido222');
		} else {
			$(this).removeClass('requerido222');
		}
	});
	return a;
}

function cargarMedicamentos() {
	$.ajax({
		url: "historiaclinica/cargarMedicamentos",
		data: {id: "{{$hc->id}}"},
		beforeSend: function() {
			$("#detallemedicamentos").html('<tr><td colspan="3">Cargando...</td></tr>');
		}, 
	})
	.done(function(a) {
		$("#detallemedicamentos").html('');
		$("#detallemedicamentos").html(a);
	}); 
}

@if($hc->ciesformato !== NULL && $hc->ciesformato !== '')

	function inicializarTablaCies2(cies) {
		$.ajax({
			url: "historiaclinica/inicializarTablaCies2",
			data: {cies: cies},
			beforeSend: function() {
				$("#detallecie22").html('<tr><td colspan="3">Cargando...</td></tr>');
			}, 
		})
		.done(function(a) {
			$("#detallecie22").html('');
			$("#detallecie22").html(a);
			$('#cadenacies22').val('{{$hc->ciesformato}}');
		});    	
	}

	inicializarTablaCies2('{{$hc->ciesformato}}');

@endif

@if($hc->numeroconsideracion !== NULL && $hc->numeroconsideracion !== '')

	$('#cbxReconsideracion').prop('checked', true);
	$('#cbxReconsideracion').attr('data-aa', '1');
	$('#txtReconsideracion').removeAttr('style').addClass('requerido222');

@endif
</script>