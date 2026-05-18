<?php
if(!is_null($historia)){
	$txtHbHto1=NULL;
	$txtHbHto2=NULL;

	if($historia->txtHbHto!==NULL&&$historia->txtHbHto!=="") {
		$txtHbHto1=(!isset(explode("/", $historia->txtHbHto)[0])?"":explode("/", $historia->txtHbHto)[0]);
		$txtHbHto2=(!isset(explode("/", $historia->txtHbHto)[1])?"":explode("/", $historia->txtHbHto)[1]);
	}
?>
<style>
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
</style>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($historia, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
    {!! Form::hidden('modo', $modo, array('id' => 'modo')) !!}
    {!! Form::hidden('historia_id', $id, array('id' => 'historia_id')) !!}
    <div class="row" spellcheck="false">
    	<div class="col-lg-12 col-md-12 col-sm-12">
    		<ul class="nav nav-tabs navhi">
			  <li class="active"><a data-toggle="tab" href="#DatosGenerales">I. Datos Generales</a></li>
			  <li><a data-toggle="tab" href="#EvaluacionMedica">II. Evaluación Médica</a></li>
			  <li><a data-toggle="tab" href="#DatosMedico">III. Datos del Médico</a></li>
			</ul>
			<div class="tab-content">
				<div id="DatosGenerales" class="tab-pane fade in active">
					<!-- Main content -->
					<section class="content">
						<div class="panel-group">
						  	<div class="panel panel-success">
						  		<div class="panel-heading">1.1 DATOS DEL PACIENTE</div>
						    	<div class="panel-body">
									<div class="form-group">
										{!! Form::label('txtPaciente', 'Paciente', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtPaciente', $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres, array('class' => 'form-control input-sm', 'id' => 'txtPaciente', 'readonly')) !!}
										</div>
										{!! Form::label('txtDireccion', 'Dirección', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtDireccion', $historia->persona->direccion, array('class' => 'form-control input-sm', 'id' => 'txtDireccion', 'readonly')) !!}
										</div>														
										{!! Form::label('txtDNI', 'DNI/CE', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-1 col-md-1 col-sm-1">
											{!! Form::text('txtDNI', $historia->persona->dni, array('class' => 'form-control input-sm', 'id' => 'txtDNI', 'readonly')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtAfiliacion', 'Afiliación', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtAfiliacion', $historia->convenio->nombre, array('class' => 'form-control input-sm', 'id' => 'txtAfiliacion', 'readonly')) !!}
										</div>
										{!! Form::label('txtTelefonos', 'Teléfono(s)', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtTelefonos', $historia->persona->telefono . ($historia->persona->telefono2==NULL?'':', '. $historia->persona->telefono2), array('class' => 'form-control input-sm', 'id' => 'txtTelefonos', 'readonly')) !!}
										</div>
										{!! Form::label('txtIPRESS', 'IPRESS de procedencia', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtIPRESS', $historia->ipress, array('class' => 'form-control input-sm', 'id' => 'txtIPRESS', 'readonly')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtDepartamento', 'Depto.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::text('txtDepartamento', $historia->departamento2->nombre, array('class' => 'form-control input-sm', 'id' => 'txtDepartamento', 'readonly')) !!}
										</div>
										{!! Form::label('txtProvincia', 'Provincia', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::text('txtProvincia', $historia->provincia2->nombre, array('class' => 'form-control input-sm', 'id' => 'txtProvincia', 'readonly')) !!}
										</div>
										{!! Form::label('txtDistrito', 'Distrito', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::text('txtDistrito', $historia->distrito2->nombre, array('class' => 'form-control input-sm', 'id' => 'txtDistrito', 'readonly')) !!}
										</div>
									</div>
						    	</div>
						  	</div>
						  	<div class="panel panel-info">
						  		<div class="panel-heading">1.2 DIRECCIONES DE EMERGENCIA</div>
						    	<div class="panel-body">
									<div class="form-group">
										{!! Form::label('txtNombre2', 'Familiar', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtNombre2', $historia->persona2->apellidopaterno.' '.$historia->persona2->apellidomaterno.' '.$historia->persona2->nombres, array('class' => 'form-control input-sm', 'id' => 'txtNombre2', 'readonly')) !!}
										</div>
										{!! Form::label('txtDireccion2', 'Dirección', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtDireccion2', $historia->persona2->direccion, array('class' => 'form-control input-sm requerido', 'id' => 'txtDireccion2')) !!}
										</div>														
										{!! Form::label('txtDNI2', 'DNI/CE', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-1 col-md-1 col-sm-1">
											{!! Form::text('txtDNI2', $historia->persona2->dni, array('class' => 'form-control input-sm', 'id' => 'txtDNI2', 'readonly')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtTelefono2', 'Teléfono', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtTelefono2', $historia->persona2->telefono, array('class' => 'form-control input-sm requerido', 'id' => 'txtTelefono2')) !!}
										</div>
										{!! Form::label('txtTelefono22', 'Teléfono2', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtTelefono22', $historia->persona2->telefono2, array('class' => 'form-control input-sm', 'id' => 'txtTelefono22')) !!}
										</div>
										{!! Form::label('txtRelacion', 'Relación con paciente', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtRelacion', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtRelacion')) !!}
										</div>										
									</div>
									<div class="form-group">
										{!! Form::label('txtDepartamento2', 'Depto.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::select('txtDepartamento2', $cboDepa, ($historia->persona2->distrito2==NULL?null:$historia->persona2->distrito2->provincia->departamento->id), array('class' => 'form-control input-sm requerido', 'id' => 'txtDepartamento2')) !!}
										</div>										
										{!! Form::label('txtProvincia2', 'Provincia', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::select('txtProvincia2', array('Elija Provincia'), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtProvincia2')) !!}
										</div>
										{!! Form::label('txtDistrito2', 'Distrito', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::select('txtDistrito2', array('Elija Distrito'), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtDistrito2')) !!}
										</div>
									</div>
						    	</div>
						  	</div>
						</div>
					</section>
					<!-- /.content -->	
				</div>
				<div id="EvaluacionMedica" class="tab-pane fade">
					<section class="content">
						<div class="panel-group">
						  	<div class="panel panel-success">
						  		<div class="panel-heading">
						  			<div class="row">
							  			<div class="col-lg-10 col-md-10 col-sm-10">
							  				<div class="text-left">
							  					2.1 ANTECEDENTES PATOLÓGICOS
							  				</div>
							  			</div>
							  			<div class="col-lg-2 col-md-2 col-sm-2">
							  				<div class="text-right">
							  					<a href="#" data-toggle="collapse" data-target="#box_1" class="btn btn-sm btn-info"><i class="fa fa-plus-square-o"></i> Abrir/Cerrar</a>
							  				</div>						  				
							  			</div>
							  		</div>
						  		</div>
						    	<div class="panel-body collapse" id="box_1">
									<div class="form-group">
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtEnfermedad', 'Enfermedad o condición clínica que produjo la insuficiencia renal', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-8 col-md-8 col-sm-8">
													{{--{!! Form::textarea('txtEnfermedad', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtEnfermedad', 'rows' => '5')) !!}--}}
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															{!! Form::select('txtEtiologia1_id', $cboEtiologia, $historia->txtEtiologia1_id, array('class' => 'form-control input-sm requerido', 'id' => 'txtEtiologia1_id', 'rows' => '6', 'onchange' => 'cargarEtiologia(this.value)')) !!}
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															{!! Form::select('txtEtiologia2_id', array('---Selecciona una Etiología---'), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtEtiologia2_id', 'rows' => '6')) !!}
														</div>
													</div>
												</div>
											</div>											
											<div class="form-group">
												{!! Form::label('txtFechaPrimeraHemodialisis', 'Fecha de Primera Hemodiálisis', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-8 col-md-8 col-sm-8">
													{!! Form::date('txtFechaPrimeraHemodialisis', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaPrimeraHemodialisis')) !!}
												</div>												
											</div>
											<div class="form-group">
												{!! Form::label('txtGrupoSangre', 'Grupo de Sangre', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtGrupoSangre', $historia->gruposanguineo, array('class' => 'form-control input-sm', 'id' => 'txtGrupoSangre', 'readonly')) !!}
												</div>	
												{!! Form::label('txtNumeroTransfusiones', 'Número de Transfusiones', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtNumeroTransfusiones', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtNumeroTransfusiones')) !!}
												</div>									
											</div>											
											<div class="form-group">
												{!! Form::label('txtSubsistemaSaludInicioTRR', 'Subsistema de Salud de Inicio de TRR', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-8 col-md-8 col-sm-8">
													{!! Form::select('txtSubsistemaSaludInicioTRR', $cboSubsistemaSaludInicioTRR, $historia->txtSubsistemaSaludInicioTRR===NULL?'':$historia->txtSubsistemaSaludInicioTRR, array('class' => 'form-control input-sm requerido', 'id' => 'txtSubsistemaSaludInicioTRR')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtModalidadInicioTRR', 'Modalidad de Inicio de TRR', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::select('txtModalidadInicioTRR', $cboModalidadInicioTRR, $historia->txtModalidadInicioTRR===NULL?'':$historia->txtModalidadInicioTRR, array('class' => 'form-control input-sm requerido', 'id' => 'txtModalidadInicioTRR')) !!}
												</div>
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaModalidadInicioTRR', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaModalidadInicioTRR')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtDiuresis1', 'Diúresis Residual en 24 horas', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-8 col-md-8 col-sm-8">
													<div class="form-group">
														<div class="col-lg-4 col-md-4 col-sm-4">
															{!! Form::text('txtDiuresis1', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtDiuresis1')) !!}
														</div>
														{!! Form::label('txtDiuresis2', 'cc', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', 'style'=>'text-align:left;')) !!}
														<div class="col-lg-4 col-md-4 col-sm-4">
															{!! Form::text('txtDiuresis2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtDiuresis2')) !!}
														</div>
														{!! Form::label('txtDiuresis2', 'h', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', 'style'=>'text-align:left;')) !!}
													</div>
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtAlergia', 'Alergia a Medicamentos', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													<label class="switch">
													  	<input type="checkbox" id="cbxAlergia">
													  	<span class="slider round"></span>
													</label>
												</div>
												<div class="col-lg-6 col-md-6 col-sm-6">
													{!! Form::text('txtAlergia', null, array('class' => 'form-control input-sm', 'id' => 'txtAlergia', 'style'=>'display:none;')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtTipoAccesoInicio', 'Tipo de Acceso de Inicio', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::select('txtTipoAccesoInicio', $cboTipoAccesoInicio, $historia->txtTipoAccesoInicio===NULL?'':$historia->txtTipoAccesoInicio, array('class' => 'form-control input-sm requerido', 'id' => 'txtTipoAccesoInicio')) !!}
												</div>
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaTipoAccesoInicio', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaTipoAccesoInicio')) !!}
												</div>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtComorbilidades', 'Comorbilidades', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												{!! Form::hidden('txtComorbilidades', $historia->txtComorbilidades===NULL?'':$historia->txtComorbilidades, array('id' => 'txtComorbilidades')) !!}
												<div class="col-lg-8 col-md-8 col-sm-8">
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb1" value="1">
													  	<span class="slider round"></span>
													</label> a) Enfermedades Ateroscleróticas. <br> 
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb2" value="2">
													  	<span class="slider round"></span>
													</label> b) Insuficiencia cardíaca congestiva. <br>
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb3" value="3">
													  	<span class="slider round"></span>
													</label> c) Enfermedad vascular periférica. <br>
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb4" value="4">
													  	<span class="slider round"></span>
													</label> d) Accidente cerebrovascular/isquémico. <br>
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb5" value="5">
													  	<span class="slider round"></span>
													</label> e) Cáncer. <br>
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb6" value="6">
													  	<span class="slider round"></span>
													</label> f) Diabetes. <br>
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb7" value="7">
													  	<span class="slider round"></span>
													</label> g) Hipertensión. <br>
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb8" value="8">
													  	<span class="slider round"></span>
													</label> h) Tuberculosis. <br>
													<label class="switch">
													  	<input type="checkbox" class="comorb comorb9" value="9">
													  	<span class="slider round"></span>
													</label> i) Otra. <br>
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtMedicacion', 'Medicación que recibe', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-8 col-md-8 col-sm-8">
													{!! Form::textarea('txtMedicacion', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtMedicacion', 'rows' => '3')) !!}
												</div>									
											</div>
											<div class="form-group">
												{!! Form::label('txtIntervencionesQuirurgicas', 'Intervenciones Quirúrgicas', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-8 col-md-8 col-sm-8">
													{!! Form::textarea('txtIntervencionesQuirurgicas', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtIntervencionesQuirurgicas', 'rows' => '3')) !!}
												</div>												
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12">
													<div class="text-center">
														<h4 style="font-weight:bold;color:green;">Inmunización Contra Hepatitis B</h4>
													</div>
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtCantDosis', 'Cant. de Dosis', array('class' => 'col-lg-10 col-md-10 col-sm-10 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtCantDosis', null, array('class' => 'form-control input-sm', 'id' => 'txtCantDosis', 'readonly')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtFechaCantDosis1', 'Fecha 1', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis1', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis1')) !!}
												</div>	
												{!! Form::label('txtFechaCantDosis2', 'Fecha 2', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis2', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis2')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtFechaCantDosis3', 'Fecha 3', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis3', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis3')) !!}
												</div>	
												{!! Form::label('txtFechaCantDosis4', 'Fecha 4', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis4', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis4')) !!}
												</div>	
											</div>
											<div class="form-group">
												{!! Form::label('txtFechaCantDosis5', 'Fecha 5', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis5', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis5')) !!}
												</div>	
												{!! Form::label('txtFechaCantDosis6', 'Fecha 6', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis6', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis6')) !!}
												</div>	
											</div>
											<div class="form-group">
												{!! Form::label('txtFechaCantDosis7', 'Fecha 7', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis7', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis7')) !!}
												</div>	
												{!! Form::label('txtFechaCantDosis8', 'Fecha 8', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis8', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis8')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtFechaCantDosis9', 'Fecha 9', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis9', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis9')) !!}
												</div>	
												{!! Form::label('txtFechaCantDosis10', 'Fecha 10', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis10', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis10')) !!}
												</div>	
											</div>
											<div class="form-group">
												{!! Form::label('txtFechaCantDosis11', 'Fecha 11', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis11', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis11')) !!}
												</div>	
												{!! Form::label('txtFechaCantDosis12', 'Fecha 12', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCantDosis12', null, array('class' => 'cantDosis form-control input-sm', 'id' => 'txtFechaCantDosis12')) !!}
												</div>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12">
													<div class="text-center">
														<h4 style="font-weight:bold;color:green;">Otras Terapias Previas de Reemplazo Renal</h4>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12">
													<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diálisis Peritoneal Continua Ambulatoria</h5>
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtFechaDialisisPeritoneal1', 'Desde', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaDialisisPeritoneal1', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaDialisisPeritoneal1')) !!}
												</div>	
												{!! Form::label('txtFechaDialisisPeritoneal2', 'Hasta', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaDialisisPeritoneal2', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaDialisisPeritoneal2')) !!}
												</div>
											</div>
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12">
													<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transplante Renal</h5>
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtTransplanteRenal1', 'Desde', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtTransplanteRenal1', null, array('class' => 'form-control input-sm', 'id' => 'txtTransplanteRenal1')) !!}
												</div>	
												{!! Form::label('txtTransplanteRenal2', 'Hasta', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtTransplanteRenal2', null, array('class' => 'form-control input-sm', 'id' => 'txtTransplanteRenal2')) !!}
												</div>
											</div>
										</div>									
									</div>
						    	</div>
						  	</div>
						  	<div class="panel panel-info">
						  		<div class="panel-heading">
						  			<div class="row">
						  				<div class="col-lg-10 col-md-10 col-sm-10">
							  				<div class="text-left">
							  					2.2 OTROS ANTECEDENTES PATOLÓGICOS DE IMPORTANCIA
							  				</div>
							  			</div>
							  			<div class="col-lg-2 col-md-2 col-sm-2">
							  				<div class="text-right">
							  					<a href="#" data-toggle="collapse" data-target="#box_2" class="btn btn-sm btn-info"><i class="fa fa-plus-square-o"></i> Abrir/Cerrar</a>
							  				</div>						  				
							  			</div>
						  			</div>						  			
						  		</div>
						    	<div class="panel-body collapse" id="box_2">
									<div class="form-group">
										{!! Form::label('txtAntecedentesPMedicos', 'Médicos', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtAntecedentesPMedicos', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtAntecedentesPMedicos', 'rows' => '4')) !!}
										</div>
										{!! Form::label('txtAntecedentesPQuirurgicos', 'Quirúrgicos', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtAntecedentesPQuirurgicos', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtAntecedentesPQuirurgicos', 'rows' => '4')) !!}
										</div>
									</div>
						    	</div>
						  	</div>
						  	<div class="panel panel-success">
						  		<div class="panel-heading">
						  			<div class="row">
						  				<div class="col-lg-10 col-md-10 col-sm-10">
							  				<div class="text-left">
							  					2.3 ENFERMEDAD ACTUAL
							  				</div>
							  			</div>
							  			<div class="col-lg-2 col-md-2 col-sm-2">
							  				<div class="text-right">
							  					<a href="#" data-toggle="collapse" data-target="#box_3" class="btn btn-sm btn-info"><i class="fa fa-plus-square-o"></i> Abrir/Cerrar</a>
							  				</div>						  				
							  			</div>
						  			</div>
						  		</div>
						    	<div class="panel-body collapse" id="box_3">
									<div class="form-group">
										{!! Form::label('txtSintomasEnfermedadActual', 'Síntomas', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-10 col-md-10 col-sm-10">
											{!! Form::textarea('txtSintomasEnfermedadActual', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtSintomasEnfermedadActual', 'rows' => '4')) !!}
										</div>
									</div>
						    	</div>
						  	</div>
						  	<div class="panel panel-info">
						  		<div class="panel-heading">						  			
						  			<div class="row">
						  				<div class="col-lg-10 col-md-10 col-sm-10">
							  				<div class="text-left">
							  					2.4 EXAMEN CLÍNICO
							  				</div>
							  			</div>
							  			<div class="col-lg-2 col-md-2 col-sm-2">
							  				<div class="text-right">
							  					<a href="#" data-toggle="collapse" data-target="#box_4" class="btn btn-sm btn-info"><i class="fa fa-plus-square-o"></i> Abrir/Cerrar</a>
							  				</div>						  				
							  			</div>
						  			</div>
						  		</div>
						    	<div class="panel-body collapse" id="box_4">
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Funciones Vitales</h5>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtPresionArterial1', 'Presión Arterial', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::text('txtPresionArterial1', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtPresionArterial1')) !!}
												</div>
												{!! Form::label('txtPresionArterial2', '/', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::text('txtPresionArterial2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtPresionArterial2')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtFC', 'Frecuencia Cardíaca', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::text('txtFC', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFC')) !!}
												</div>
												{!! Form::label('txtFC', 'latidos por minuto', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label', 'style'=>'text-align:left;')) !!}
											</div>
											<div class="form-group">
												{!! Form::label('txtFR', 'Frecuencia Respiratoria', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::text('txtFR', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFR')) !!}
												</div>
												{!! Form::label('txtFR', 'resp. por minuto', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label', 'style'=>'text-align:left;')) !!}
											</div>											
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtPeso', 'Peso', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtPeso', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtPeso')) !!}
												</div>
												{!! Form::label('txtPeso', 'Kg.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
												{!! Form::label('txtTalla', 'Talla', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtTalla', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtTalla')) !!}
												</div>
												{!! Form::label('txtTalla', 'm.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
											</div>
											<div class="form-group">
												{!! Form::label('txtPiel', 'Piel', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-10 col-md-10 col-sm-10">
													{!! Form::textarea('txtPiel', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtPiel', 'rows' => '5')) !!}
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acceso Vascular</h5>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtNumeroAccesoVascular', 'Número de acceso vasculares previos', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::text('txtNumeroAccesoVascular', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtNumeroAccesoVascular')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtTiempoPermanenciaAccesosVasculares', 'Tiempo promedio de permanencia de los accesos vasculares', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::text('txtTiempoPermanenciaAccesosVasculares', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtTiempoPermanenciaAccesosVasculares')) !!}
												</div>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtCambioPerdida', 'Causa de cambio y/o pérdida', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-10 col-md-10 col-sm-10">
													{!! Form::textarea('txtCambioPerdida', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtCambioPerdida', 'rows' => '5')) !!}
												</div>
											</div>
										</div>
									</div>
									<hr>											
									<div class="form-group">
										<div class="col-lg-4 col-md-4 col-sm-4">
											<div class="panel panel-default">
  												<div class="panel-body">
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<h5 style="font-weight:bold;color:orange;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Responsable de la realización</h5>
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															{!! Form::hidden('cbxDescripcionResponsableRealizacion2', 'Cirujano Cardiovascular', array('id' => 'cbxDescripcionResponsableRealizacion2')) !!}												
															<label class="container">Cirujano cardiovascular
																<input type="radio" checked="checked" name="cbxDescripcionResponsableRealizacion" id="cbxDescripcionResponsableRealizacion" value="Cirujano Cardiovascular">
																<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Cirujano general
															    <input type="radio" name="cbxDescripcionResponsableRealizacion" id="cbxDescripcionResponsableRealizacion" value="Cirujano General">
															  	<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Nefrólogo
															  	<input type="radio" name="cbxDescripcionResponsableRealizacion" id="cbxDescripcionResponsableRealizacion" value="Nefrologo">
															  	<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Otro
															  	<input type="radio" name="cbxDescripcionResponsableRealizacion" id="cbxDescripcionResponsableRealizacion" value="Otro">
															  	<span class="checkmark radiorequerido"></span>
															</label>
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtDescripcionResponsableRealizacion', 'Descripción', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
														<div class="col-lg-7 col-md-7 col-sm-7">
															{!! Form::text('txtDescripcionResponsableRealizacion', null, array('class' => 'form-control input-sm', 'id' => 'txtDescripcionResponsableRealizacion')) !!}
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtFechaAccesoVascularActual', 'Fecha de realización de acceso vascular actual', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
														<div class="col-lg-7 col-md-7 col-sm-7">
															{!! Form::date('txtFechaAccesoVascularActual', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaAccesoVascularActual')) !!}
														</div>
													</div>	
												</div>
											</div>
										</div>	
										<div class="col-lg-4 col-md-4 col-sm-4">
											<div class="panel panel-default">
  												<div class="panel-body">
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<h5 style="font-weight:bold;color:orange;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ubicación</h5>
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															{!! Form::hidden('cbxUbicacionVascularActual2', 'Radial', array('id' => 'cbxUbicacionVascularActual2')) !!}
															<label class="container">Radial
																<input type="radio" checked="checked" name="cbxUbicacionVascularActual" id="cbxUbicacionVascularActual" value="Radial">
																<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Humeral
															    <input type="radio" name="cbxUbicacionVascularActual" id="cbxUbicacionVascularActual" value="Humeral">
															  	<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Otros
															  	<input type="radio" name="cbxUbicacionVascularActual" id="cbxUbicacionVascularActual" value="Otros">
															  	<span class="checkmark radiorequerido"></span>
															</label>
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtUbicacionVascularActual', 'Especificar', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
														<div class="col-lg-8 col-md-8 col-sm-8">
															{!! Form::text('txtUbicacionVascularActual', null, array('class' => 'form-control input-sm', 'id' => 'txtUbicacionVascularActual')) !!}
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<h5 style="font-weight:bold;color:orange;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Thrill</h5>
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															{!! Form::hidden('cbxThill2', 'Bueno', array('id' => 'cbxThill2')) !!}
															<label class="container">Bueno
																<input type="radio" checked="checked" name="cbxThill" id="cbxThill" value="Bueno">
																<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Regular
															    <input type="radio" name="cbxThill" id="cbxThill" value="Regular">
															  	<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Malo
															    <input type="radio" name="cbxThill" id="cbxThill" value="Malo">
															  	<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">No aplica
															    <input type="radio" name="cbxThill" id="cbxThill" value="No aplica">
															  	<span class="checkmark radiorequerido"></span>
															</label>
														</div>
													</div>
												</div>
											</div>
										</div>	
										<div class="col-lg-4 col-md-4 col-sm-4">
											<div class="panel panel-default">
  												<div class="panel-body">
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<h5 style="font-weight:bold;color:orange;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo</h5>
														</div>
													</div>
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12">
															{!! Form::hidden('cbxTipoDescripcionAccesoVascularActual2', 'Fístula', array('id' => 'cbxTipoDescripcionAccesoVascularActual2')) !!}
															<label class="container">Fístula
																<input type="radio" checked="checked" name="cbxTipoDescripcionAccesoVascularActual" id="cbxTipoDescripcionAccesoVascularActual" value="Fístula">
																<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Injerto
															    <input type="radio" name="cbxTipoDescripcionAccesoVascularActual" id="cbxTipoDescripcionAccesoVascularActual" value="Injerto">
															  	<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Cateter temporal
															  	<input type="radio" name="cbxTipoDescripcionAccesoVascularActual" id="cbxTipoDescripcionAccesoVascularActual" value="Cateter temporal">
															  	<span class="checkmark radiorequerido"></span>
															</label>
															<label class="container">Cateter permanente
															  	<input type="radio" name="cbxTipoDescripcionAccesoVascularActual" id="cbxTipoDescripcionAccesoVascularActual" value="Cateter permanente">
															  	<span class="checkmark radiorequerido"></span>
															</label>
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtTipoDescripcionAccesoVascularActual', 'Detalle', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
														<div class="col-lg-8 col-md-8 col-sm-8">
															{!! Form::text('txtTipoDescripcionAccesoVascularActual', null, array('class' => 'form-control input-sm', 'id' => 'txtTipoDescripcionAccesoVascularActual')) !!}
														</div>
													</div>
												</div>
											</div>
										</div>										
									</div>
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acceso Cardiovascular</h5>
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtCorazon', 'Corazón', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtCorazon', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtCorazon', 'rows' => '5')) !!}
										</div>
										{!! Form::label('txtPulsosPerifericos', 'Pulsos Periféricos', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtPulsosPerifericos', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtPulsosPerifericos', 'rows' => '5')) !!}
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-2 col-md-2 col-sm-2">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aparato Respiratorio</h5>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtAparatoRespiratorio', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtAparatoRespiratorio', 'rows' => '5')) !!}
										</div>
										<div class="col-lg-2 col-md-2 col-sm-2">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Abdomen</h5>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtAbdomen', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtAbdomen', 'rows' => '5')) !!}
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-2 col-md-2 col-sm-2">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Neurológico</h5>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtNeurologicos', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtNeurologicos', 'rows' => '5')) !!}
										</div>
										<div class="col-lg-2 col-md-2 col-sm-2">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Osteomuscular</h5>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtOsteomuscular', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtOsteomuscular', 'rows' => '5')) !!}
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-2 col-md-2 col-sm-2">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado Nutricional</h5>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtEstadoNutricional', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtEstadoNutricional', 'rows' => '5')) !!}
										</div>
										<div class="col-lg-2 col-md-2 col-sm-2">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Índice de Karnofski</h5>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::textarea('txtKarnofski', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtKarnofski', 'rows' => '5')) !!}
										</div>
									</div>
								</div>
						  	</div>
						  	<div class="panel panel-success">
						  		<div class="panel-heading">
						  			<div class="row">
						  				<div class="col-lg-10 col-md-10 col-sm-10">
							  				<div class="text-left">
							  					2.5 EVALUACIÓN BIOLÓGICA
							  				</div>
							  			</div>
							  			<div class="col-lg-2 col-md-2 col-sm-2">
							  				<div class="text-right">
							  					<a href="#" data-toggle="collapse" data-target="#box_5" class="btn btn-sm btn-info"><i class="fa fa-plus-square-o"></i> Abrir/Cerrar</a>
							  				</div>						  				
							  			</div>
						  			</div>						  			
						  		</div>
						    	<div class="panel-body collapse" id="box_5">
						    		<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hematología</h5>
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtGrupoSanguineoLetra', 'Grupo Sanguíneo', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtGrupoSanguineoLetra', substr($historia->gruposanguineo, 0, strlen($historia->gruposanguineo)-1), array('class' => 'form-control input-sm', 'id' => 'txtGrupoSanguineoLetra', 'readonly')) !!}
										</div>
										{!! Form::label('txtFechaGrupoSanguineoLetra', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::date('txtFechaGrupoSanguineoLetra', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaGrupoSanguineoLetra')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtGrupoSanguineoSigno', 'Factor RH', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtGrupoSanguineoSigno', substr($historia->gruposanguineo, strlen($historia->gruposanguineo)-1, strlen($historia->gruposanguineo)), array('class' => 'form-control input-sm', 'id' => 'txtGrupoSanguineoSigno', 'readonly')) !!}
										</div>
										{!! Form::label('txtFechaGrupoSanguineoSigno', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::date('txtFechaGrupoSanguineoSigno', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaGrupoSanguineoSigno')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtHbHto', 'Hb/Hto', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}

										<div class="col-lg-1 col-md-1 col-sm-1">
											{!! Form::text('txtHbHto1', $txtHbHto1, array('class' => 'form-control input-sm requerido', 'id' => 'txtHbHto')) !!}
										</div>
										{!! Form::label('labelHbHto', 'gr/L', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}

										<div class="col-lg-1 col-md-1 col-sm-1">
											{!! Form::text('txtHbHto2', $txtHbHto2, array('class' => 'form-control input-sm requerido', 'id' => 'txtHbHto')) !!}
										</div>
										{!! Form::label('labelHbHto', '%', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}

										{!! Form::label('txtFechaHbHto', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::date('txtFechaHbHto', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaHbHto')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtTiempoHemodialisis', 'Tiempo de Hemodiálisis', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::text('txtTiempoHemodialisis', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtTiempoHemodialisis')) !!}
										</div>
										{!! Form::label('txtTransfusionesPrevias', 'Transfusiones Previas', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-1 col-md-1 col-sm-1">
											{{--
											<input type="radio" name="cbxTransfusionesPrevias" data="si"> Si
											<input type="radio" name="cbxTransfusionesPrevias" data="no" checked> No
											--}}
											<label class="switch">
											  	<input type="checkbox" id="cbxTransfusionesPrevias">
											  	<span class="slider round"></span>
											</label>
										</div>
										{!! Form::label('txtTransfusionesPrevias', 'Número', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-1 col-md-1 col-sm-1">
											{!! Form::text('txtTransfusionesPrevias', null, array('class' => 'form-control input-sm', 'id' => 'txtTransfusionesPrevias', 'style'=>'display:none;')) !!}
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bioquímica</h5>
										</div>
									</div>
									<div class="form-group">										
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtGlicemia', 'Glicemia (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtGlicemia', $historia->txtGlicemia==""||$historia->txtGlicemia==NULL?"":$historia->txtGlicemia, array('class' => 'form-control input-sm', 'id' => 'txtGlicemia')) !!}
												</div>
												{!! Form::label('txtFechaGlicemia', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaGlicemia', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaGlicemia')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtDepuracionCreatina', 'Depuración de Creatina (mil/min x 1.73 m²)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDepuracionCreatina', $historia->txtDepuracionCreatina==""||$historia->txtDepuracionCreatina==NULL?"":$historia->txtDepuracionCreatina, array('class' => 'form-control input-sm', 'id' => 'txtDepuracionCreatina')) !!}
												</div>
												{!! Form::label('txtFechaDepuracionCreatina', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaDepuracionCreatina', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaDepuracionCreatina')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtEndogena', 'Endogena (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtEndogena', $historia->txtEndogena==""||$historia->txtEndogena==NULL?"":$historia->txtEndogena, array('class' => 'form-control input-sm', 'id' => 'txtEndogena')) !!}
												</div>
												{!! Form::label('txtFechaEndogena', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaEndogena', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaEndogena')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtUremia', 'Uremia (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtUremia', $historia->txtUremia==""||$historia->txtUremia==NULL?"":$historia->txtUremia, array('class' => 'form-control input-sm', 'id' => 'txtUremia')) !!}
												</div>
												{!! Form::label('txtFechaUremia', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaUremia', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaUremia')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtCreatinina', 'Creatinina (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtCreatinina', $historia->txtCreatinina==""||$historia->txtCreatinina==NULL?"":$historia->txtCreatinina, array('class' => 'form-control input-sm', 'id' => 'txtCreatinina')) !!}
												</div>
												{!! Form::label('txtFechaCreatinina', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCreatinina', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaCreatinina')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtAcidoUrico', 'Ácido Úrico (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtAcidoUrico', $historia->txtAcidoUrico==""||$historia->txtAcidoUrico==NULL?"":$historia->txtAcidoUrico, array('class' => 'form-control input-sm', 'id' => 'txtAcidoUrico')) !!}
												</div>
												{!! Form::label('txtFechaAcidoUrico', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaAcidoUrico', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaAcidoUrico')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtProteinas', 'Proteínas Totales (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtProteinas', $historia->txtProteinas==""||$historia->txtProteinas==NULL?"":$historia->txtProteinas, array('class' => 'form-control input-sm', 'id' => 'txtProteinas')) !!}
												</div>
												{!! Form::label('txtFechaProteinas', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaProteinas', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaProteinas')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtAlbumina', 'Albúmina (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtAlbumina', $historia->txtAlbumina==""||$historia->txtAlbumina==NULL?"":$historia->txtAlbumina, array('class' => 'form-control input-sm', 'id' => 'txtAlbumina')) !!}
												</div>
												{!! Form::label('txtFechaAlbumina', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaAlbumina', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaAlbumina')) !!}
												</div>
											</div>											
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6">
											{{--<div class="text-center">
												<img width="500px" height="700px" src="dist/img/bioquimica.jpg" class="img-thumbnail img-responsive">
											</div>--}}
											<div class="form-group">
												{!! Form::label('txtCalcio', 'Calcio (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtCalcio', $historia->txtCalcio==""||$historia->txtCalcio==NULL?"":$historia->txtCalcio, array('class' => 'form-control input-sm', 'id' => 'txtCalcio')) !!}
												</div>
												{!! Form::label('txtFechaCalcio', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaCalcio', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaCalcio')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtFosforo', 'Fósforo (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtFosforo', $historia->txtFosforo==""||$historia->txtFosforo==NULL?"":$historia->txtFosforo, array('class' => 'form-control input-sm', 'id' => 'txtFosforo')) !!}
												</div>
												{!! Form::label('txtFechaFosforo', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaFosforo', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaFosforo')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtTGO', 'TGO (UI/L)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtTGO', $historia->txtTGO==""||$historia->txtTGO==NULL?"":$historia->txtTGO, array('class' => 'form-control input-sm', 'id' => 'txtTGO')) !!}
												</div>
												{!! Form::label('txtFechaTGO', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaTGO', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaTGO')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtTGP', 'TGP (UI/L)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtTGP', $historia->txtTGP==""||$historia->txtTGP==NULL?"":$historia->txtTGP, array('class' => 'form-control input-sm', 'id' => 'txtTGP')) !!}
												</div>
												{!! Form::label('txtFechaTGP', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaTGP', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaTGP')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtBilirrubina', 'Bilirrubina Total (MG/DL)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtBilirrubina', $historia->txtBilirrubina==""||$historia->txtBilirrubina==NULL?"":$historia->txtBilirrubina, array('class' => 'form-control input-sm', 'id' => 'txtBilirrubina')) !!}
												</div>
												{!! Form::label('txtFechaBilirrubina', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaBilirrubina', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaBilirrubina')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtHierroSerico', 'Hierro sérico (µg/dl)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtHierroSerico', $historia->txtHierroSerico==""||$historia->txtHierroSerico==NULL?"":$historia->txtHierroSerico, array('class' => 'form-control input-sm', 'id' => 'txtHierroSerico')) !!}
												</div>
												{!! Form::label('txtFechaHierroSerico', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaHierroSerico', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaHierroSerico')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtTransferrina', 'Saturación de Transferrina (%)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtTransferrina', $historia->txtTransferrina==""||$historia->txtTransferrina==NULL?"":$historia->txtTransferrina, array('class' => 'form-control input-sm', 'id' => 'txtTransferrina')) !!}
												</div>
												{!! Form::label('txtFechaTransferrina', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaTransferrina', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaTransferrina')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtParatohormona', 'Dosaje de Paratohormona (PG/ML)', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtParatohormona', $historia->txtParatohormona==""||$historia->txtParatohormona==NULL?"":$historia->txtParatohormona, array('class' => 'form-control input-sm', 'id' => 'txtParatohormona')) !!}
												</div>
												{!! Form::label('txtFechaParatohormona', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaParatohormona', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaParatohormona')) !!}
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serología</h5>
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtSerologicasLues', 'Serológicas para  Lúes', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::hidden('cbxSerologicasLues2', 'Positivo', array('id' => 'cbxSerologicasLues2')) !!}
													<div class="form-group">
														<label class="container">Positivo
															<input type="radio" checked="checked" name="cbxSerologicasLues" id="cbxSerologicasLues" value="Positivo">
															<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Negativo
														    <input type="radio" name="cbxSerologicasLues" id="cbxSerologicasLues" value="Negativo">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Desconocido
														    <input type="radio" name="cbxSerologicasLues" id="cbxSerologicasLues" value="Desconocido">
														  	<span class="checkmark radiorequerido"></span>
														</label>
													</div>
												</div>
												{!! Form::label('txtFechaSerologicasLues', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaSerologicasLues', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaSerologicasLues')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtAgHbs', 'AgHbs', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::hidden('cbxAgHbs2', 'Positivo', array('id' => 'cbxAgHbs2')) !!}
													<div class="form-group">
														<label class="container">Positivo
															<input type="radio" checked="checked" name="cbxAgHbs" id="cbxAgHbs" value="Positivo">
															<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Negativo
														    <input type="radio" name="cbxAgHbs" id="cbxAgHbs" value="Negativo">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Desconocido
														    <input type="radio" name="cbxAgHbs" id="cbxAgHbs" value="Desconocido">
														  	<span class="checkmark radiorequerido"></span>
														</label>
													</div>
												</div>
												{!! Form::label('txtFechaAgHbs', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaAgHbs', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaAgHbs')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtAcHVC', 'AcHVC', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::hidden('cbxAcHVC2', 'Positivo', array('id' => 'cbxAcHVC2')) !!}
													<div class="form-group">
														<label class="container">Positivo
															<input type="radio" checked="checked" name="cbxAcHVC" id="cbxAcHVC" value="Positivo">
															<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Negativo
														    <input type="radio" name="cbxAcHVC" id="cbxAcHVC" value="Negativo">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Desconocido
														    <input type="radio" name="cbxAcHVC" id="cbxAcHVC" value="Desconocido">
														  	<span class="checkmark radiorequerido"></span>
														</label>
													</div>
												</div>
												{!! Form::label('txtFechaAcHVC', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaAcHVC', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaAcHVC')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtAcHbc', 'AcHbc', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::hidden('cbxAcHbc2', 'Positivo', array('id' => 'cbxAcHbc2')) !!}
													<div class="form-group">
														<label class="container">Positivo
															<input type="radio" checked="checked" name="cbxAcHbc" id="cbxAcHbc" value="Positivo">
															<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Negativo
														    <input type="radio" name="cbxAcHbc" id="cbxAcHbc" value="Negativo">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Desconocido
														    <input type="radio" name="cbxAcHbc" id="cbxAcHbc" value="Desconocido">
														  	<span class="checkmark radiorequerido"></span>
														</label>
													</div>
												</div>
												{!! Form::label('txtFechaAcHbc', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaAcHbc', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaAcHbc')) !!}
												</div>
											</div>											
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												{!! Form::label('txtAcHbs', 'AcHbs', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::hidden('cbxAcHbs2', 'Positivo', array('id' => 'cbxAcHbs2')) !!}
													<div class="form-group">
														<label class="container">Positivo
															<input type="radio" checked="checked" name="cbxAcHbs" id="cbxAcHbs" value="Positivo">
															<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Negativo
														    <input type="radio" name="cbxAcHbs" id="cbxAcHbs" value="Negativo">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Desconocido
														    <input type="radio" name="cbxAcHbs" id="cbxAcHbs" value="Desconocido">
														  	<span class="checkmark radiorequerido"></span>
														</label>
													</div>
												</div>
												<div class="col-lg-6 col-md-6 col-sm-6">
													<div class="form-group">
														{!! Form::label('txtFechaAcHbs', 'Fecha', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
														<div class="col-lg-8 col-md-8 col-sm-8">
															{!! Form::date('txtFechaAcHbs', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaAcHbs')) !!}
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtTituloAcHbs', 'Título', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
														<div class="col-lg-8 col-md-8 col-sm-8">
															{!! Form::select('txtTituloAcHbs', array('1'=>'< 10 UI/L', '2'=>'> 10 UI/L', '3'=>'Desconocido'), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtTituloAcHbs')) !!}
														</div>
													</div>
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtHIV', 'HIV', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::hidden('cbxHIV2', 'Positivo', array('id' => 'cbxHIV2')) !!}
													<div class="form-group">
														<label class="container">Positivo
															<input type="radio" checked="checked" name="cbxHIV" id="cbxHIV" value="Positivo">
															<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Negativo
														    <input type="radio" name="cbxHIV" id="cbxHIV" value="Negativo">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Desconocido
														    <input type="radio" name="cbxHIV" id="cbxHIV" value="Desconocido">
														  	<span class="checkmark radiorequerido"></span>
														</label>
													</div>
												</div>
												{!! Form::label('txtFechaHIV', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaHIV', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFechaHIV')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtVacunacionHepatitisB', 'Vacunación de Hepatitis B', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
												<div class="col-lg-9 col-md-9 col-sm-9">
													{!! Form::hidden('cbxVacunacionHepatitisB2', 'Completa', array('id' => 'cbxVacunacionHepatitisB2')) !!}
													<div class="form-group">
														<label class="container">Completa
															<input type="radio" checked="checked" name="cbxVacunacionHepatitisB" id="cbxVacunacionHepatitisB" value="Completa">
															<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">Incompleta
														    <input type="radio" name="cbxVacunacionHepatitisB" id="cbxVacunacionHepatitisB" value="Incompleta">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">En Proceso
														    <input type="radio" name="cbxVacunacionHepatitisB" id="cbxVacunacionHepatitisB" value="En Proceso">
														  	<span class="checkmark radiorequerido"></span>
														</label>
														<label class="container">No inició Esquema
														    <input type="radio" name="cbxVacunacionHepatitisB" id="cbxVacunacionHepatitisB" value="No inició Esquema">
														  	<span class="checkmark radiorequerido"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
									</div>
									<hr>											
									<div class="form-group">
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12">
													<div class="text-center">
														<h4 style="font-weight:bold;color:green;">Ecografía Renal</h4>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::label('cbxEcografiaRenal', 'Seleccionar', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												</div>
												<div class="col-lg-3 col-md-3 col-sm-3">
													{{--<input type="radio" name="cbxEcografiaRenal" data="si"> Si
													<input type="radio" data="no" checked> No--}}
													<label class="switch">
													  	<input type="checkbox" name="cbxEcografiaRenal">
													  	<span class="slider round"></span>
													</label>
												</div>
												{!! Form::label('txtFechaEcografiaRenal', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label labeltxtFechaEcografiaRenal')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaEcografiaRenal', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaEcografiaRenal', 'style'=>'display:none;')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtObservacionEcografiaRenal', 'Observación', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label labeltxtFechaEcografiaRenal')) !!}
												<div class="col-lg-10 col-md-10 col-sm-10">
													{!! Form::textarea('txtObservacionEcografiaRenal', null, array('class' => 'form-control input-sm', 'id' => 'txtObservacionEcografiaRenal', 'rows' => '6', 'style'=>'display:none;')) !!}
												</div>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6">
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12">
													<div class="text-center">
														<h4 style="font-weight:bold;color:green;">RX de Tórax</h4>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-lg-3 col-md-3 col-sm-3">
													{!! Form::label('cbxRXTorax', 'Seleccionar', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												</div>
												<div class="col-lg-3 col-md-3 col-sm-3">
													<label class="switch">
													  	<input type="checkbox" name="cbxRXTorax">
													  	<span class="slider round"></span>
													</label>
													{{--<input type="radio" name="cbxRXTorax" data="si"> Si
													<input type="radio" name="cbxRXTorax" data="no" checked> No--}}
												</div>
												{!! Form::label('txtFechaRXTorax', 'Fecha', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label labeltxtFechaRXTorax')) !!}
												<div class="col-lg-4 col-md-4 col-sm-4">
													{!! Form::date('txtFechaRXTorax', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaRXTorax', 'style'=>'display:none;')) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtObservacionRXTorax', 'Observación', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label labeltxtFechaRXTorax')) !!}
												<div class="col-lg-10 col-md-10 col-sm-10">
													{!! Form::textarea('txtObservacionRXTorax', null, array('class' => 'form-control input-sm', 'id' => 'txtObservacionRXTorax', 'rows' => '6', 'style'=>'display:none;')) !!}
												</div>
											</div>
										</div>
									</div>
						    	</div>
						  	</div>
						</div>
		            </section>
				</div>
				<div id="DatosMedico" class="tab-pane fade">
					<section class="content">
						<div class="panel-group">
						  	<div class="panel panel-success">
						  		<div class="panel-heading">3.1 DATOS DEL MÉDICO</div>
						    	<div class="panel-body">
									<div class="form-group">										
										{!! Form::hidden('txtIdDoctor', null, array('id' => 'txtIdDoctor')) !!}
										{!! Form::label('txtNombreDoctor', 'Apellidos y Nombres', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtNombreDoctor', null, array('class' => 'form-control input-sm', 'id' => 'txtNombreDoctor')) !!}
										</div>
										{!! Form::label('txtEspecialidadDoctor', 'Especialidad', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-3 col-md-3 col-sm-3">
											{!! Form::text('txtEspecialidadDoctor', null, array('class' => 'form-control input-sm', 'id' => 'txtEspecialidadDoctor', 'readonly')) !!}
										</div>														
										{!! Form::label('txtDNIDoctor', 'DNI/CE', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-1 col-md-1 col-sm-1">
											{!! Form::text('txtDNIDoctor', null, array('class' => 'form-control input-sm', 'id' => 'txtDNIDoctor', 'readonly')) !!}
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtCMP', 'N° CMP', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtCMP', null, array('class' => 'form-control input-sm', 'id' => 'txtCMP', 'readonly')) !!}
										</div>
										{!! Form::label('txtRNE', 'N° RNE', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtRNE', null, array('class' => 'form-control input-sm', 'id' => 'txtRNE', 'readonly')) !!}
										</div>
									</div>
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12 text-right">
								            {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarHistoria("' . $entidad . '", this);')) !!}
								            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
										</div>
									</div>
						    	</div>
						  	</div>
						</div>
		            </section>
				</div>
			</div>
    	</div>
    </div>		    
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1200');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').inputmask("99999999");

    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtDiuresis1"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtDiuresis2"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtNumeroTransfusiones"]').inputmask('decimal', { radixPoint: "", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 0 });
	@if($historia==NULL)    
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtCantDosis"]').val('0');
    @endif
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtPresionArterial1"]').inputmask('decimal', { radixPoint: "", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 0 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtPresionArterial2"]').inputmask('decimal', { radixPoint: "", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 0 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtFC"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtFR"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtPeso"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtTalla"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtNumeroAccesoVascular"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 0 });
    //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtTiempoPermanenciaAccesosVasculares"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtTiempoHemodialisis"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtTransfusionesPrevias"]').inputmask('decimal', { radixPoint: "", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 0 });

    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtGlicemia"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtDepuracionCreatina"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtEndogena"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtUremia"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtCreatinina"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtAcidoUrico"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });   
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtProteinas"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });   
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtAlbumina"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });   
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtCalcio"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtFosforo"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtTGO"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtTGP"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtBilirrubina"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtHierroSerico"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtTransferrina"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtParatohormona"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="txtHbHto"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });  

    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').focus();
    $('input').attr('placeholder', 'Escriba aquí.');
    $('textarea').attr('placeholder', 'Escriba aquí.');
    
    @if($historia->txtEtiologia2_id!==NULL)
    	cargarEtiologia($('#txtEtiologia1_id').val(), 'SI');
    @else
    	cargarEtiologia($('#txtEtiologia1_id').val());
    @endif
    $(".labeltxtFechaEcografiaRenal").hide();
    $(".labeltxtFechaRXTorax").hide();
}); 

$('#txtDepartamento2').change(function(){
	var depa = $('#txtDepartamento2').val();
	$.ajax({
        type: "GET",
        url: "historia/buscaProv/"+depa,
        success: function(a) {
            $('#txtProvincia2').html(a);
        }
    });
});

$('#txtProvincia2').change(function(){
	var prov = $('#txtProvincia2').val();
	$.ajax({
        type: "GET",
        url: "historia/buscaDist/"+prov,
        success: function(a) {
            $('#txtDistrito2').html(a);            
        }
    });
});

$('.cantDosis').change(function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();
	var cantDosis = 0;
	$('.cantDosis').each(function(index, el) {
		if(($(this).val()).length==10) {
			cantDosis++;
		}
	});
	$('#txtCantDosis').val(cantDosis);
});

$('input[id=cbxAlergia]').change(function(e) {
	if($(this).prop('checked')) {
		$('#txtAlergia').removeAttr('style').addClass('requerido').focus();
	} else {
		$('#txtAlergia').attr('style', 'display:none;').removeClass('requerido').val("");
	}
});

$('input[name=cbxDescripcionResponsableRealizacion]').click(function(e) {
	$('#txtDescripcionResponsableRealizacion').focus();
});

$('input[name=cbxUbicacionVascularActual]').click(function(e) {
	$('#txtUbicacionVascularActual').focus();
});

$('input[name=cbxTipoDescripcionAccesoVascularActual]').click(function(e) {
	$('#txtTipoDescripcionAccesoVascularActual').focus();
});

$('input[id=cbxTransfusionesPrevias]').click(function(e) {
	$('#txtTransfusionesPrevias').focus();
});

$('input[id=cbxTransfusionesPrevias]').click(function(e) {
	if($(this).prop('checked')) {
		$('#txtTransfusionesPrevias').removeAttr('style').addClass('requerido').focus();
	} else {
		$('#txtTransfusionesPrevias').attr('style', 'display:none;').removeClass('requerido').val("");
	}
});

$('input[name=cbxEcografiaRenal]').change(function(e) {
	if($(this).prop('checked')) {
		$('#txtObservacionEcografiaRenal').removeAttr('style').addClass('requerido');
		$('#txtFechaEcografiaRenal').removeAttr('style').addClass('requerido').focus();
		$('.labeltxtFechaEcografiaRenal').show();
	} else {
		$('#txtObservacionEcografiaRenal').attr('style', 'display:none;').removeClass('requerido').val("");
		$('#txtFechaEcografiaRenal').attr('style', 'display:none;').removeClass('requerido').val("");
		$('.labeltxtFechaEcografiaRenal').hide();
	}
});

$('input[name=cbxRXTorax]').click(function(e) {
	if($(this).prop('checked')) {
		$('#txtObservacionRXTorax').removeAttr('style').addClass('requerido');
		$('#txtFechaRXTorax').removeAttr('style').addClass('requerido').focus();
		$('.labeltxtFechaRXTorax').show();
	} else {
		$('#txtObservacionRXTorax').attr('style', 'display:none;').removeClass('requerido').val("");
		$('#txtFechaRXTorax').attr('style', 'display:none;').removeClass('requerido').val("");
		$('.labeltxtFechaRXTorax').hide();
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
	if(($('#txtDNIDoctor').val()).length==0) {
		a = false;
        $('#txtNombreDoctor').addClass('requerido2');
	} else {
		$('#txtNombreDoctor').removeClass('requerido2');
	}
	return a;
}

function cargarEtiologia(value, p = ''){
	if(value === '') {
		$('#txtEtiologia2_id').html('<option value="">---Seleccione una Etiología---</option>');
		return false;
	} else {
		$.ajax({
	        type: "GET",
	        url: "historia/buscaEtiologia?id="+value,
	        success: function(a) {
	            $('#txtEtiologia2_id').html(a);
	            if(p === 'SI') {
					$('#txtEtiologia2_id').val('{{$historia->txtEtiologia2_id}}');
	            }
	        }
	    });
	}		
}

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
	}
});

function guardarHistoria (entidad, idboton) {
	if(!validarInputs()) {
		a = 'Corrige los campos en rojo y vuelve a enviar.';
		alertaG(a);
		return false;
	} else {
		//llenamos las comorbilidades
		var comorb = '';
		$('.comorb').each(function(index, el) {
			if($(this).prop('checked')) {
				comorb += $(this).attr('value') + ';';
			}
		});
		$('#txtComorbilidades').val(comorb);
		$('#cbxDescripcionResponsableRealizacion2').val($('input[name=cbxDescripcionResponsableRealizacion]:checked').val());
		$('#cbxUbicacionVascularActual2').val($('input[name=cbxUbicacionVascularActual]:checked').val());
		$('#cbxTipoDescripcionAccesoVascularActual2').val($('input[name=cbxTipoDescripcionAccesoVascularActual]:checked').val());
		$('#cbxThill2').val($('input[name=cbxThill]:checked').val());
		$('#cbxSerologicasLues2').val($('input[name=cbxSerologicasLues]:checked').val());
		$('#cbxAgHbs2').val($('input[name=cbxAgHbs]:checked').val());
		$('#cbxAcHbs2').val($('input[name=cbxAcHbs]:checked').val());
		$('#cbxAcHbc2').val($('input[name=cbxAcHbc]:checked').val());
		$('#cbxAcHVC2').val($('input[name=cbxAcHVC]:checked').val());
		$('#cbxHIV2').val($('input[name=cbxHIV]:checked').val());
		$('#cbxVacunacionHepatitisB2').val($('input[name=cbxVacunacionHepatitisB]:checked').val());
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
			}else{
			  //alert(respuesta);
	            var dat = JSON.parse(respuesta);
				if (dat[0]!==undefined && (dat[0].respuesta=== 'OK')) {
					cerrarModal();
					a = 'Historia Inicial de {{ $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres }} ';
					@if(!is_null($historia))
						a += 'Modificada';
					@else
						a += 'Generada';
					@endif 
					a += ' Correctamente';             
	                alertaB(a);
	                buscar('Historia');
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	}
}

var doctores = new Bloodhound({
    datumTokenizer: function (d) {
        return Bloodhound.tokenizers.whitespace(d.value);
    },
    limit: 10,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: 'medico/medicoautocompletar/%QUERY',
        filter: function (doctores) {
            return $.map(doctores, function (movie) {
                return {
                    value: movie.value,
                    id: movie.id,
                    especialidad:movie.especialidad,
                    dni:movie.dni,
                    cmp:movie.cmp,
                    rne:movie.rne
                };
            });
        }
    }
});

doctores.initialize();
$('#txtNombreDoctor').typeahead(null,{
    displayKey: 'value',
    source: doctores.ttAdapter()
}).on('typeahead:selected', function (object, datum) {
    $('#txtNombreDoctor').val(datum.value);
    $('#txtIdDoctor').val(datum.id);
    $('#txtEspecialidadDoctor').val(datum.especialidad);
    $('#txtDNIDoctor').val(datum.dni);
    $('#txtCMP').val(datum.cmp);
    $('#txtRNE').val(datum.rne);
});
setInterval(quitarPadding, 4000);

@if($historia->persona2->distrito2!==NULL)
	function cargarProv(){
		var depa = $('#txtDepartamento2').val();
		$.ajax({
	        type: "GET",
	        url: "historia/buscaProv/"+depa,
	        success: function(a) {
	            $('#txtProvincia2').html(a);
	            $('#txtProvincia2').val('{{ $historia->persona2->distrito2->provincia->id }}');
	        }
	    });
	}
	function cargarDist(){
		var prov = '{{ $historia->persona2->distrito2->provincia->id }}';
		$.ajax({
	        type: "GET",
	        url: "historia/buscaDist/"+prov,
	        success: function(a) {
	            $('#txtDistrito2').html(a);
	            $('#txtDistrito2').val('{{ $historia->persona2->distrito2->id }}');
	        }
	    });
	}
	function cargarCantDosis() {
		var cantDosis = 0;
		$('.cantDosis').each(function(index, el) {
			if(($(this).val()).length==10) {
				cantDosis++;
			}
		});
		$('#txtCantDosis').val(cantDosis);
	}
	cargarProv();
	cargarDist();
	cargarCantDosis();
@endif
@if($historia->txtAlergia!==NULL&&$historia->txtAlergia!=="")
	$('input[id=cbxAlergia]').prop('checked', true);
	$('#txtAlergia').removeAttr('style');
@endif	
@if($historia->cbxDescripcionResponsableRealizacion!==NULL&&$historia->cbxDescripcionResponsableRealizacion!=="")
	$('input[name=cbxDescripcionResponsableRealizacion]').removeAttr('checked');
	$('input[name=cbxDescripcionResponsableRealizacion]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxDescripcionResponsableRealizacion }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxUbicacionVascularActual!==NULL&&$historia->cbxUbicacionVascularActual!=="")
	$('input[name=cbxUbicacionVascularActual]').removeAttr('checked');
	$('input[name=cbxUbicacionVascularActual]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxUbicacionVascularActual }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxTipoDescripcionAccesoVascularActual!==NULL&&$historia->cbxTipoDescripcionAccesoVascularActual!=="")
	$('input[name=cbxTipoDescripcionAccesoVascularActual]').removeAttr('checked');
	$('input[name=cbxTipoDescripcionAccesoVascularActual]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxTipoDescripcionAccesoVascularActual }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxTipoDescripcionAccesoVascularActual!==NULL&&$historia->cbxTipoDescripcionAccesoVascularActual!=="")
	$('input[name=cbxTipoDescripcionAccesoVascularActual]').removeAttr('checked');
	$('input[name=cbxTipoDescripcionAccesoVascularActual]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxTipoDescripcionAccesoVascularActual }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxThill!==NULL&&$historia->cbxThill!=="")
	$('input[name=cbxThill]').removeAttr('checked');
	$('input[name=cbxThill]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxThill }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxSerologicasLues!==NULL&&$historia->cbxSerologicasLues!=="")
	$('input[name=cbxSerologicasLues]').removeAttr('checked');
	$('input[name=cbxSerologicasLues]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxSerologicasLues }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxAgHbs!==NULL&&$historia->cbxAgHbs!=="")
	$('input[name=cbxAgHbs]').removeAttr('checked');
	$('input[name=cbxAgHbs]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxAgHbs }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxAcHbs!==NULL&&$historia->cbxAcHbs!=="")
	$('input[name=cbxAcHbs]').removeAttr('checked');
	$('input[name=cbxAcHbs]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxAcHbs }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif
@if($historia->cbxAcHbc!==NULL&&$historia->cbxAcHbc!=="")
	$('input[name=cbxAcHbc]').removeAttr('checked');
	$('input[name=cbxAcHbc]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxAcHbc }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif	
@if($historia->cbxAcHVC!==NULL&&$historia->cbxAcHVC!=="")
	$('input[name=cbxAcHVC]').removeAttr('checked');
	$('input[name=cbxAcHVC]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxAcHVC }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif	
@if($historia->cbxHIV!==NULL&&$historia->cbxHIV!=="")
	$('input[name=cbxHIV]').removeAttr('checked');
	$('input[name=cbxHIV]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxHIV }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif	
@if($historia->cbxVacunacionHepatitisB!==NULL&&$historia->cbxVacunacionHepatitisB!=="")
	$('input[name=cbxVacunacionHepatitisB]').removeAttr('checked');
	$('input[name=cbxVacunacionHepatitisB]').each(function(index, el) {
		if($(this).attr('value')==='{{ $historia->cbxVacunacionHepatitisB }}') {
			$(this).prop('checked', true);
			return false;
		}
	});
@endif	
@if($historia->txtObservacionEcografiaRenal!==NULL&&$historia->txtObservacionEcografiaRenal!=="")
	$('input[name=cbxEcografiaRenal]').prop('checked', true);
	$('#txtFechaEcografiaRenal').removeAttr('style');
	$('#txtObservacionEcografiaRenal').removeAttr('style');
@endif
@if($historia->txtObservacionRXTorax!==NULL&&$historia->txtObservacionRXTorax!=="")
	$('input[name=cbxRXTorax]').prop('checked', true);
	$('#txtFechaRXTorax').removeAttr('style');
	$('#txtObservacionRXTorax').removeAttr('style');
@endif
@if($historia->txtTransfusionesPrevias!==NULL&&$historia->txtTransfusionesPrevias!=="")
	$('input[id=cbxTransfusionesPrevias]').prop('checked', true);
	$('#txtTransfusionesPrevias').removeAttr('style');
@endif
@if($historia->txtComorbilidades!==NULL&&$historia->txtComorbilidades!=="")
	var comorb = '{{ $historia->txtComorbilidades }}';
	var arraycomorb = comorb.split(';');
	for (var i = 0; i < arraycomorb.length - 1; i++) {
		$('.comorb'+arraycomorb[i]).prop('checked', true);
	}
@endif
@if($historia->txtIdDoctor!==NULL&&$historia->txtIdDoctor!=="")
	$('#txtNombreDoctor').val('{{ $historia->doctor->apellidopaterno . ' ' . $historia->doctor->apellidomaterno . ' ' . $historia->doctor->nombres }}');
	$('#txtIdDoctor').val('{{ $historia->doctor->id }}');
	$('#txtEspecialidadDoctor').val('{{ $historia->doctor->especialidad->nombre }}');
	$('#txtDNIDoctor').val('{{ $historia->doctor->dni }}');
	$('#txtCMP').val('{{ $historia->doctor->cmp }}');
	$('#txtRNE').val('{{ $historia->doctor->rne }}');

@endif
</script>
<?php } ?>