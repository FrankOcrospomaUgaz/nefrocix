<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Person;
$entidad='Producto';
use App\Tiposervicio;
date_default_timezone_set('America/Lima');
$fechahoy = date('j-m-Y');
$user = Auth::user();
$person = Person::find(Session::get('person_id'));
if ($person === null && $user !== null && $user->person_id !== null) {
	$person = Person::find($user->person_id);
}
Session::set('sucursal_id', 1);
$nombreusuario = $person !== null
	? trim($person->apellidopaterno.' '.$person->apellidomaterno.' '.$person->nombres)
	: ($user !== null ? $user->login : '');
?>
@if($user != null)
@if($user->usertype_id == 39 || $user->usertype_id == 30 || $user->usertype_id == 31 || $user->usertype_id == 1 || $user->usertype_id == 2 || $user->usertype_id == 28 || $user->usertype_id == 29)
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'SIGHO') }}</title>
    <link rel="icon" href="{{ asset('dist/img/user2-160x160.jpg') }}" sizes="16x16 32x32 48x48 64x64" type="image/vnd.microsoft.icon">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    {!! Html::style('bootstrap/css/bootstrap.min.css') !!}
    <!-- Font Awesome -->
    {!! Html::style('dist/css/font-awesome.min.css') !!}
    <!-- Ionicons -->
    {!! Html::style('dist/css/ionicons.min.css') !!}
    <!-- DataTables -->
    {!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
    <!-- Theme style -->
    {!! Html::style('dist/css/AdminLTE.min.css') !!}
    {!! Html::style('css/custom.css') !!}
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    {!! Html::style('dist/css/skins/_all-skins.min.css') !!}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]-->
    {!! Html::script('dist/js/html5shiv.min.js') !!}
    {!! Html::script('dist/js/respond.min.js') !!}
    <!--[endif]-->
    {{-- bootstrap-datetimepicker: para calendarios --}}
    {!! HTML::style('dist/css/bootstrap-datetimepicker.min.css', array('media' => 'screen')) !!}

    {{-- typeahead.js-bootstrap: para autocompletar --}}
    {!! HTML::style('dist/css/typeahead.js-bootstrap.css', array('media' => 'screen')) !!}

    <style>
    	body {
    		font-style:italic;
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
		.caption {
		    padding: 0.3em;
		    color: #fff;
		    background: #000;
		}
		th {
		    background: #eee;
		}
		.form-group, hr {
			padding: 5px;
			margin: 0;
		}
		.panel {
		  	filter: drop-shadow(2px 2px 2px #333);
		}
		input, select, textarea {
		  	filter: drop-shadow(1px 1px 1px #333);
		  	text-transform:uppercase;;
		}
		.requerido2 { 
			border: 1px solid #f00; 
			background-color: #FFD6CE;
			color: red;
		}
	    #vSeg{
	      	width: 90% !important;
	    }
	    .fade-scale { 		 
		    transform: scale(0); 		 
		    opacity: 0; 		 
		    -webkit-transition: all .25s linear; 		 
		    -o-transition: all .25s linear; 		 
		    transition: all .25s linear; 		 
		}  
		 
		.fade-scale.in { 		 
		    opacity: 1; 		 
		    transform: scale(1); 		 
		}
  	</style>

</head>
<body spellcheck="false" class="hold-transition skin-blue sidebar-mini">
	<form action="#" id="formHistoriaClinica">
		{!! Form::hidden('historia_id', '', array('id' => 'historia_id')) !!}
		{!! Form::hidden('_token', csrf_token()) !!}
	    <div class="wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>
					VISTA MÉDICO
					<small>Gestión Hospitalaria</small>
					<a href="#" onclick="modal('usuario/escogerSucursal', 'Cambiar Usuario', this);" class="btn btn-sm btn-success">Cambiar Usuario</a>
					 | <font id="uuuu" style="font-size: 15px;">{{$nombreusuario}}</font>			
				</h1>
			</section>
			<br>
			<hr>
			<ul class="nav nav-tabs">
			  {{--<li><a data-toggle="tab" href="#Farmacia">Farmacia</a></li>
			  <li><a data-toggle="tab" href="#Tarifario">Tarifario</a></li>--}}
			  <li><a data-toggle="tab" href="#Historias">Historias</a></li>
			  <li><a data-toggle="tab" href="#cie">CIE 10</a></li>
			  <li class="active" id="pestanaPacienteCola"><a data-toggle="tab" href="#cola">Pacientes en cola</a></li>
			  <li id="pestanaAtenciones"><a data-toggle="tab" href="#atendidos">Atenciones del día</a></li>
			  <li id="pestanaAtencion"><a data-toggle="tab" href="#atencion">Atención de Paciente</a></li>
			</ul>
			<div class="tab-content">

				<div id="Historias" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												<div class="form-inline">
													<?php

													$cboTipoPaciente  = array("" => "Todos","Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");

													?>

													{!! Form::hidden('pageh', 1, array('id' => 'pageh')) !!}
													{!! Form::hidden('accionh', 'listar', array('id' => 'accionh')) !!}
													<div class="form-group">
														{!! Form::label('nombreh', 'Apellidos y Nombres:') !!}
														{!! Form::text('nombreh', '', array('class' => 'form-control input-sm', 'id' => 'nombreh')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('dni', 'DNI/CE:') !!}
														{!! Form::text('dni', '', array('class' => 'form-control input-sm', 'id' => 'dni')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('numeroh', 'Historia:') !!}
														{!! Form::text('numeroh', '', array('class' => 'form-control input-sm', 'id' => 'numeroh')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('tipopaciente', 'Tipo Paciente:') !!}
														{!! Form::select('tipopaciente', $cboTipoPaciente, null, array('class' => 'form-control input-sm', 'id' => 'tipopaciente','onchange' =>'buscarHistoria();')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('filash', 'Filas a mostrar:')!!}
														{!! Form::selectRange('filash', 1, 30, 20, array('class' => 'form-control input-sm', 'onchange' => 'buscarHistoria();')) !!}
													</div>

													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscarHistoria();')) !!}
												</div>
											</div>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body" id="listadoh{{ $entidad }}">
									</div>
									<!-- /.box-body -->
								</div>
								<!-- /.box -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</section>
					<!-- /.content -->	
				</div>

				<div id="cie" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												<div class="form-inline">
													<div class="form-group">
														{!! Form::label('cie10', 'Cie10:') !!}
														{!! Form::text('cie10', '', array('class' => 'form-control input-sm', 'id' => 'cie10')) !!}
													</div>
													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar2', 'onclick' => 'buscar3(\''.$entidad.'\')')) !!}
												</div>
											</div>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body" id="listado3{{ $entidad }}">
									</div>
									<!-- /.box-body -->
								</div>
								<!-- /.box -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</section>
					<!-- /.content -->	
				</div>

				<div id="cola" class="tab-pane fade in active">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												<div class="line">												
													{{--<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:blue'>CONSULTAS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoConsultas"></div>
													</div>
													<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:red'>EMERGENCIAS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoEmergencias"></div>
													</div>
													<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:#3498DB'>FONDO DE OJOS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoOjos"></div>
													</div>
													<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:green'>LECTURA DE RESULTADOS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoLectura"></div>
													</div>--}}
													<div class="col-sm-12">
														<div id="listadoConsultas"></div>
													</div>
												</div>
											</div>
											{{--<div class="col-xs-4">
												<strong>SIGUIENTE PACIENTE: </strong>
												<div class="box-body" id="atender">
												</div>
												<strong>BUSCAR PACIENTE: </strong>
												<div class="col-sm-12">
													<div class=" col-sm-9">
														{!! Form::text('buscarPaciente', '', array('class' => 'form-control input-sm', 'id' => 'buscarPaciente', 'placeholder' => 'Ingrese paciente',  'onkeyup' => 'if(event.keyCode == 13) llamarPacienteNombre();')) !!}
													</div>
													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn-xs btn btn-success btn-sm col-sm-3', 'id' => 'btnBuscarPaciente', 'onclick' => 'llamarPacienteNombre();')) !!}
												</div>
												<div class="box-body" id="resultadoBusquedaPaciente">
												<h5 style="color: red; font-weight: bold;" id="mensajeBusquedaPaciente"></h5>
												</div>
											</div>--}}
										</div>
									</div>
									<!-- /.box-header -->
								</div>
								<!-- /.box -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</section>
					<!-- /.content -->	
				</div>

				<div id="atendidos" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="form-horizontal">
											<div class="form-group">
												{!! Form::label('nombre_atendido', 'Nombre:', array('class' => 'col-sm-1 control-label', 'style' => 'text-align:right;')) !!}
												<div class="col-sm-4">
													{!! Form::text('nombre_atendido', '', array('class' => 'form-control input-sm', 'id' => 'nombre_atendido')) !!}
												</div>
												{!! Form::label('fechaatencion', 'Fecha:', array('class' => 'col-sm-1 control-label', 'style' => 'text-align:right;')) !!}
												<div class="col-sm-2">
													{!! Form::date('fechaatencion', date("d/m/Y"), array('class' => 'form-control input-sm', 'id' => 'fechaatencion')) !!}
												</div>
												<div class="col-sm-2">
													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success', 'id' => 'btnAtendidos', 'onclick' => 'tablaAtendidos(1);')) !!}
												</div>												
											</div>
										</div>
										<div class="col-xs-12">
											<h3 class='text-center' style='font-weight:bold;color:blue'>ATENCIONES DEL DÍA <font id="titulofecha">{{ date('d-m-Y') }}</font></h3>
											<div id="tablaAtendidos">
											</div>
										</div>
									</div>
									<!-- /.box-header -->
								</div>
								<!-- /.box -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</section>
					<!-- /.content -->	
				</div>

				<div id="atencion" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												<div class="form-horizontal">
													<div class="col-sm-4" style="position: fixed;">
														<div class="panel panel-default">
  															<div class="panel-body">
																<div class="form-group">
																	<div class="col-sm-12">
																		<div style="text-align:center;border-style:dotted;padding-top: 5px;padding-bottom: 5px;">
																			<strong>¿El paciente está presente?</strong>
																			{!! Form::button('<i class="glyphicon glyphicon-ok"></i> SI', array('class' => 'btn btn-success btn-sm', 'id' => 'btnSi', 'onclick' => 'presente("SI");')) !!}
																			{!! Form::button('<i class="glyphicon glyphicon-remove"></i> NO', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnNo', 'onclick' => 'presente("NO");')) !!}
																		</div>
																	</div>
																	<!--<div class="col-sm-2">
																		<div style="text-align:center; padding-top: 5px;padding-bottom: 5px;">
																			{!! Form::button('<i class="glyphicon glyphicon-screenshot"></i>', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnCerradoEspecial', 'onclick' => 'javascript:{}', 'data-toggle'=>'modal', 'data-target'=>'#modalCerradoEspecial', "title"=>"Cancelar Atención")) !!}
																		</div>
																	</div>-->
																</div>	
																<?php
																$hoy = date("Y-m-d");
																?>
																<div class="form-group">										
																	<div class="col-md-12">
																		<div class="form-group">
																			{!! Form::label('fecha_atencion', 'Fecha:', array('class' => 'col-sm-2 control-label')) !!}
																			<div class="col-sm-8">
																				{!! Form::date('fecha_atencion', $hoy, array('class' => 'form-control input-sm col-sm-3', 'id' => 'fecha_atencion', 'readonly' => 'readonly')) !!}
																			</div>
																			<input type="hidden" id="id_hc" name="id_hc">
																			<div class="col-sm-2">
																				<button title="Información del Paciente" style="text-align: center;" onclick="" id="btnInfoPaciente" data-toggle='modal' data-target='#exampleModal3' class="btn btn-sm btn-primary" type="button"><div class="glyphicon glyphicon-eye-open"></div></button>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-md-12">
																		<div class="form-group">
																			{!! Form::label('paciente', 'Paciente', array('class' => 'col-sm-2 control-label')) !!}
																			<div class="col-sm-10">
																				{!! Form::text('paciente', '', array('class' => 'form-control input-sm', 'id' => 'paciente', 'readonly' => 'readonly')) !!}
																			</div>
																		</div>
																	</div>		
																</div>
																<div class="form-group">
																	<div class="col-sm-6 col-xs-6 col-lg-6">
																		<div class="form-group">
																			{!! Form::label('historia', 'Historia', array('class' => 'col-sm-4 control-label')) !!}
																			<div class="col-sm-8">
																				{!! Form::text('historia', '', array('class' => 'form-control input-sm', 'id' => 'historia', 'readonly' => 'readonly')) !!}
																			</div>
																		</div>
																	</div>
																	<div class="col-sm-6 col-xs-6 col-lg-6">
																		<div class="form-group">
																			{!! Form::label('plan_susalud', '', array('class' => 'col-sm-4 control-label', "id"=>"labelconvenio")) !!}
																			<div class="col-sm-8">
																				{!! Form::text('plan_susalud', '', array('class' => 'form-control input-sm', 'id' => 'plan_susalud', 'readonly' => 'readonly')) !!}
																			</div>
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-6">
																		<div class="form-group">
																			{!! Form::label('nsesion', 'Sesión', array('class' => 'col-sm-4 control-label')) !!}
																			<div class="col-sm-8">
																				{!! Form::text('nsesion', '', array('class' => 'form-control input-sm', 'id' => 'nsesion', 'readonly' => 'readonly')) !!}
																			</div>
																		</div>
																	</div>
																	<div class="col-sm-6">
																		<div class="form-group">
																			{!! Form::label('frecuencia', 'Frec.', array('class' => 'col-sm-4 control-label')) !!}
																			<div class="col-sm-8">
																				{!! Form::text('frecuencia', '', array('class' => 'form-control input-sm', 'id' => 'frecuencia', 'readonly' => 'readonly')) !!}
																			</div>
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-md-6">
																		<div class="form-group">
																			{!! Form::label('turno', 'Turno', array('class' => 'col-sm-4 control-label')) !!}
																			<div class="col-sm-8">
																				{!! Form::hidden('turno', '', array('id' => 'turno')) !!}
																				{!! Form::text('romano', '', array('class' => 'form-control input-sm', 'id' => 'romano', 'readonly' => 'readonly')) !!}
																			</div>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			{!! Form::label('citaproxima', 'Próxima cita:', array('class' => 'col-sm-4 control-label')) !!}
																			<div class="col-sm-8">
																				{!! Form::text('citaproxima', '', array('class' => 'form-control input-sm', 'id' => 'citaproxima' , 'name' => 'citaproxima', 'readonly'=>'readonly')) !!}
																			</div>
																		</div>
																	</div>
																		
																		
																</div>
																<div class="form-group">
																	@if($user->usertype_id == 39 || $user->usertype_id == 30 || $user->usertype_id == 31 || $user->usertype_id == 1 || $user->usertype_id == 2 || $user->usertype_id == 28 || $user->usertype_id == 29)
																	<div class="col-sm-6 text-center">
																		{!! Form::button('<i class="glyphicon glyphicon-check"></i> Guardado Temporal', array('class' => 'btn btn-primary btn-sm', 'id' => 'btnGuardarT', 'onclick' => 'registrarHistoriaClinica2();')) !!}
																	</div>
																	<div class="col-sm-6 text-center">
																		{!! Form::button('<i class="glyphicon glyphicon-check"></i> Cerrar Atención', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarHistoriaClinica();')) !!}
																	</div>
																	@else
																		<div class="col-sm-12 text-center">
																		{!! Form::button('<i class="glyphicon glyphicon-check"></i> Guardado Temporal', array('class' => 'btn btn-primary btn-sm', 'id' => 'btnGuardarT', 'onclick' => 'registrarHistoriaClinica2();')) !!}
																	</div>
																	@endif
																</div>	
																<h5 style="color: red; font-weight: bold;" id="mensajeHistoriaClinica"></h5>
															</div>
														</div>
													</div>
													<div class="col-sm-4">
													</div>												
													<div class="col-sm-8">
														<div id="EvaluacionMedica" class="tab-pane fade active in">
															<div class="panel-group">
															  	<div class="panel panel-success">
															  		<div class="panel-heading">
															  			<div class="row">
																  			<div class="col-lg-10 col-md-10 col-sm-10">
																  				<div class="text-left">
																  					I. PARTE DE ATENCIÓN MÉDICA
																  				</div>
																  			</div>
																  			<div class="col-lg-2 col-md-2 col-sm-2">
																  				<div class="text-right">
																  					<a href="#" data-toggle="collapse" data-target="#box_1" class="btn btn-sm btn-info abrircerrar hidden"><i class="fa fa-plus-square-o"></i> Abrir/Cerrar</a>
																  				</div>						  				
																  			</div>
																  		</div>
															  		</div>
															    	<div class="panel-body collapse" id="box_1">
															    		<strong style="color:blue;">Evaluación Previa</strong>
																		<div class="form-group">
																			<div class="col-lg-6 col-md-6 col-sm-6">		
																				<div class="form-group">
																					<label for="txtEvoSigSin" class="col-lg-4 col-md-4 col-sm-4 control-label">Evolución, Signos y Síntomas</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::textarea('txtEvoSigSin', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtEvoSigSin', 'rows' => '4')) !!}
																					</div>								
																				</div>
																				<div class="form-group">
																					<label for="txtPA" class="col-lg-2 col-md-2 col-sm-2 control-label">P.A.</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::text('txtPA', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtPA')) !!}
																					</div>
																					<label for="txtFC" class="col-lg-2 col-md-2 col-sm-2 control-label">F.C.</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::text('txtFC', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtFC')) !!}
																					</div>	
																				</div>
																				<div class="form-group">
																					<label for="txtFR" class="col-lg-2 col-md-2 col-sm-2 control-label">F.R.</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::text('txtFR', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtFR')) !!}
																					</div>
																					<label for="txtHoraEvaluacionPrevia" class="col-lg-2 col-md-2 col-sm-2 control-label">Hora</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::time('txtHoraEvaluacionPrevia', date("H:i"), array('class' => 'form-control input-sm', 'id' => 'txtHoraEvaluacionPrevia')) !!}
																					</div>		
																				</div>
																			</div>	
																			<div class="col-lg-6 col-md-6 col-sm-6">	
																				<div class="form-group">
																					{!! Form::label('cie102', 'Cie10:', array('class' => 'col-sm-2 control-label')) !!}
																					<div class="col-sm-10">
																						{!! Form::text('cie102', '', array('class' => 'form-control input-sm', 'id' => 'cie102')) !!}
																						{!! Form::hidden('cadenacies', '', array('id' => 'cadenacies')) !!}
																					</div>							
																				</div>
																				<div class="form-group">
																					<div class="col-lg-12 col-md-12 col-sm-12">
																						<table style="width:100%" border="1">
																							<thead id="cabeceracie">
																								<tr>
																									<th width='80%' style="font-size: 13px !important;">Descripción</th>
																									<th width='20%' style="font-size: 13px !important;">Eliminar</th>
																								</tr>
																							</thead>
																							<tbody id="detallecie"></tbody>
																						</table>
																					</div>
																				</div>	
																			</div>							
																		</div>		
																		<hr>
																		<strong style="color:blue;">Prescripción para máquina de hemodiálisis</strong>	
																		<div class="form-group">
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtHorasHemodialisis" class="col-lg-4 col-md-4 col-sm-4 control-label">Horas Hemod.</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtHorasHemodialisis', '', array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtHorasHemodialisis')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPesoInicial" class="col-lg-4 col-md-4 col-sm-4 control-label">Peso Inicial</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						<div class="input-group">
																							{!! Form::text('txtPesoInicial', '', array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtPesoInicial', "readonly", "onkeyup"=>"calculartxtUltrafiltrado();")) !!}
																							<span class="input-group-addon">Kg.</span>
																						</div>	
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtQb" class="col-lg-4 col-md-4 col-sm-4 control-label">Qb</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtQb', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtQb')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtNaInicial" class="col-lg-4 col-md-4 col-sm-4 control-label">Na inicial</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtNaInicial', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtNaInicial')) !!}
																					</div>
																				</div>
																			</div>							
																		</div>
																		<div class="form-group">
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtDosisHepa" class="col-lg-4 col-md-4 col-sm-4 control-label">Dosis Hepar.</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						<div class="input-group">
																							{!! Form::text('txtDosisHepa', '', array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtDosisHepa')) !!}
																							<span class="input-group-addon">UI.</span>
																						</div>
																						
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPesoFinal" class="col-lg-4 col-md-4 col-sm-4 control-label">Peso final</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						<div class="input-group">
																							{!! Form::text('txtPesoFinal', '', array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtPesoFinal')) !!}
																							<span class="input-group-addon">Kg.</span>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtQd" class="col-lg-4 col-md-4 col-sm-4 control-label">Qd.</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtQd', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtQd')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtNaFinal" class="col-lg-4 col-md-4 col-sm-4 control-label">Na final</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtNaFinal', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtNaFinal')) !!}
																					</div>
																				</div>
																			</div>							
																		</div>
																		<div class="form-group">
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPesoSeco" class="col-lg-4 col-md-4 col-sm-4 control-label">Peso seco</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						<div class="input-group">
																							{!! Form::text('txtPesoSeco', '', array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtPesoSeco', "onkeyup"=>"calculartxtUltrafiltrado();")) !!}
																							<span class="input-group-addon">Kg.</span>
																						</div>
																						
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPerfilUF" class="col-lg-4 col-md-4 col-sm-4 control-label">Perfil UF</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtPerfilUF', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtPerfilUF')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtBufer" class="col-lg-4 col-md-4 col-sm-4 control-label">Bufer</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtBufer', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtBufer')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPerfilNa" class="col-lg-4 col-md-4 col-sm-4 control-label">Perfil Na</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtPerfilNa', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtPerfilNa')) !!}
																					</div>
																				</div>
																			</div>							
																		</div>
																		<div class="form-group">
																			<div class="col-lg-6 col-md-6 col-sm-6">		
																				<div class="form-group">
																					<label for="txtMedicacion" class="col-lg-2 col-md-2 col-sm-2 control-label">Medicación</label>
																					<div class="col-lg-10 col-md-10 col-sm-10">
																						{!! Form::textarea('txtMedicacion', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtMedicacion', 'rows' => '6')) !!}
																					</div>								
																				</div>
																			</div>	
																			<div class="col-lg-6 col-md-6 col-sm-6">	
																				<div class="form-group">
																					<div class="col-lg-6 col-md-6 col-sm-6">
																						<div class="form-group">
																							<label for="txtTemperatura" class="col-lg-4 col-md-4 col-sm-4 control-label">T°</label>
																							<div class="col-lg-8 col-md-8 col-sm-8">
																								<div class="input-group">
																									{!! Form::text('txtTemperatura', '', array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtTemperatura')) !!}
																									<span class="input-group-addon" style="font-size: 8px;">°C</span>
																								</div>									
																							</div>
																						</div>
																					</div>
																					<div class="col-lg-6 col-md-6 col-sm-6">
																						<div class="form-group">
																							<label for="txtUltrafiltrado" class="col-lg-4 col-md-4 col-sm-4 control-label">Exc.Peso</label>
																							<div class="col-lg-8 col-md-8 col-sm-8">
																								<div class="input-group">
																									{!! Form::text('txtUltrafiltrado', '', array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtUltrafiltrado', "readonly")) !!}
																									<span class="input-group-addon" style="font-size: 8px;">Kg.</span>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="form-group">
																					<div class="col-lg-6 col-md-6 col-sm-6">
																						<div class="form-group">
																							<label for="txtUltrafiltadoProgramado2" class="col-lg-4 col-md-4 col-sm-4 control-label">Ultrafilt. program.</label>
																							<div class="col-lg-8 col-md-8 col-sm-8">
																								{!! Form::text('txtUltrafiltadoProgramado2', '', array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtUltrafiltadoProgramado2')) !!}
																							</div>
																						</div>
																					</div>
																					<div class="col-lg-6 col-md-6 col-sm-6">
																						<div class="form-group">
																							<label for="txtConductividad" class="col-lg-4 col-md-4 col-sm-4 control-label">Conduct.</label>
																							<div class="col-lg-8 col-md-8 col-sm-8">
																								{!! Form::text('txtConductividad', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtConductividad')) !!}
																							</div>	
																						</div>
																					</div>
																				</div>
																			</div>							
																		</div>
																		<hr>
																		<strong style="color:blue;">Prescripción para dializador</strong>
																		<div class="form-group">
																			<div class="col-lg-6 col-md-6 col-sm-6">		
																				<div class="form-group">
																					<label for="txtCondicionClinicaFinal" class="col-lg-4 col-md-4 col-sm-4 control-label">Condición clínica del paciente al finalizar la hemodiálisis</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::textarea('txtCondicionClinicaFinal', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtCondicionClinicaFinal', 'rows' => '4')) !!}
																					</div>								
																				</div>
																			</div>
																			<div class="col-lg-6 col-md-6 col-sm-6">	
																				<div class="form-group">
																					<label for="txtAreaDializador" class="col-lg-6 col-md-6 col-sm-6 control-label">Área de dializador</label>
																					<div class="col-lg-6 col-md-6 col-sm-6">
																						<div class="input-group">
																							{!! Form::text('txtAreaDializador', null, array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtAreaDializador', 'rows' => '4')) !!}
																							<span class="input-group-addon">m<sup>2</sup> </span>
																						</div>
																						
																					</div>								
																				</div>
																			</div>
																			<div class="col-lg-6 col-md-6 col-sm-6">		
																				<div class="form-group">
																					<label for="txtMembranaDializador" class="col-lg-6 col-md-6 col-sm-6 control-label">Membrana de dializador</label>
																					<div class="col-lg-6 col-md-6 col-sm-6">
																						{!! Form::text('txtMembranaDializador', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtMembranaDializador')) !!}
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
																  					II. PARTE DE ATENCIÓN DE ENFERMERÍA
																  				</div>
																  			</div>
																  			<div class="col-lg-2 col-md-2 col-sm-2">
																  				<div class="text-right">
																  					<a href="#" data-toggle="collapse" data-target="#box_2" class="btn btn-sm btn-info abrircerrar hidden"><i class="fa fa-plus-square-o"></i> Abrir/Cerrar</a>
																  				</div>						  				
																  			</div>
															  			</div>						  			
															  		</div>
															    	<div class="panel-body collapse" id="box_2">
																		<div class="form-group">
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPAInicial" class="col-lg-4 col-md-4 col-sm-4 control-label">P.A. inicial</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtPAInicial', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtPAInicial')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPAFinal" class="col-lg-4 col-md-4 col-sm-4 control-label">P.A. final</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtPAFinal', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtPAFinal')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPesoInicial2" class="col-lg-4 col-md-4 col-sm-4 control-label">Peso Inicial</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						<div class="input-group">
																							{!! Form::text('txtPesoInicial2', '', array('class' => 'form-control input-sm numerin', 'id' => 'txtPesoInicial2', "onkeyup"=>"calculartxtUltrafiltrado();")) !!}
																							<span class="input-group-addon">Kg.</span>
																						</div>
																						
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtPesoFinal2" class="col-lg-4 col-md-4 col-sm-4 control-label">Peso final</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						<div class="input-group">
																							{!! Form::text('txtPesoFinal2', '', array('class' => 'form-control input-sm numerin requerido', 'id' => 'txtPesoFinal2')) !!}
																							<span class="input-group-addon">Kg.</span>
																						</div>
																						
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtUltrafiltadoProgramado" class="col-lg-4 col-md-4 col-sm-4 control-label">Ultrafilt. exces.</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtUltrafiltadoProgramado', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtUltrafiltadoProgramado', "readonly")) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtUltrafiltadoProgramado3" class="col-lg-4 col-md-4 col-sm-4 control-label">Ultrafilt. efectivo</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtUltrafiltadoProgramado3', '', array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtUltrafiltadoProgramado3')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtNPuesto" class="col-lg-4 col-md-4 col-sm-4 control-label">N° Puesto</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::select('txtNPuesto', array(""=>"", "1"=>"1", "2"=>"2", "3"=>"3", "4"=>"4", "5"=>"5", "6"=>"6", "7"=>"7", "8"=>"8", "9"=>"9", "10"=>"10", "11"=>"11", "12"=>"12", "13"=>"13", "14"=>"14", "15"=>"15", "16"=>"16", "17"=>"17"), '', array('class' => 'form-control input-sm requerido', 'id' => 'txtNPuesto')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtNMAquina" class="col-lg-4 col-md-4 col-sm-4 control-label">N° máquina</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::select('txtNMAquina', array(""=>"", "1"=>"1", "2"=>"2", "3"=>"3", "4"=>"4", "5"=>"5", "6"=>"6", "7"=>"7", "8"=>"8", "9"=>"9", "10"=>"10", "11"=>"11", "12"=>"12", "13"=>"13", "14"=>"14", "15"=>"15", "16"=>"16", "17"=>"17", "18"=>"18", "19"=>"19"), '', array('class' => 'form-control input-sm requerido', 'id' => 'txtNMAquina')) !!}
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-3 col-md-3 col-sm-3">
																				<div class="form-group">
																					<label for="txtMarcaModeloMaquina" class="col-lg-4 col-md-4 col-sm-4 control-label">Marca máquina</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::text('txtMarcaModeloMaquina', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtMarcaModeloMaquina')) !!}
																					</div>
																				</div>
																			</div>		
																		</div>
																		<div class="form-group">		
																			<div class="col-lg-6 col-md-6 col-sm-6">
																				<div class="form-group">
																					<label for="txtMarcaModeloMaquina2" class="col-lg-2 col-md-2 col-sm-2 control-label">Modelo máquina</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::text('txtMarcaModeloMaquina2', 'NIPRO DIAMAX', array('class' => 'form-control input-sm requerido', 'id' => 'txtMarcaModeloMaquina2')) !!}
																					</div>
																					<label for="txtAreaMembranaFiltro" class="col-lg-2 col-md-2 col-sm-2 control-label">Área, membrana filtro</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::select('txtAreaMembranaFiltro', array(""=>"", "1.7"=>"1.7", "1.8"=>"1.8", "2.0"=>"2.0", "2.2"=>"2.2"), '', array('class' => 'form-control input-sm requerido', 'id' => 'txtAreaMembranaFiltro')) !!}
																					</div>
																				</div>
																				<div class="form-group">
																					<label for="txtLoteSerieFiltro" class="col-lg-2 col-md-2 col-sm-2 control-label">Lote de filtro</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::text('txtLoteSerieFiltro', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtLoteSerieFiltro')) !!}
																					</div>
																					<label for="txtLoteSerieFiltro2" class="col-lg-2 col-md-2 col-sm-2 control-label">Serie de filtro</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::text('txtLoteSerieFiltro2', '', array('class' => 'form-control input-sm requerido', 'id' => 'txtLoteSerieFiltro2')) !!}
																					</div>
																				</div>
																				<div class="form-group">
																					<label for="txtAccesoVascularArterial" class="col-lg-2 col-md-2 col-sm-2 control-label">Acceso vascular arterial</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::select('txtAccesoVascularArterial', array(""=>"", "1"=>"FAV", "2"=>"Autoinjerto", "3"=>"Injerto", "4"=>"CVCP", "5"=>"CVCT", "6"=>"VP"), '', array('class' => 'form-control input-sm', 'id' => 'txtAccesoVascularArterial')) !!}
																					</div>
																					<label for="txtAccesoVascularVenoso" class="col-lg-2 col-md-2 col-sm-2 control-label">Accesos vascular Venoso</label>
																					<div class="col-lg-4 col-md-4 col-sm-4">
																						{!! Form::select('txtAccesoVascularVenoso', array(""=>"", "1"=>"FAV", "2"=>"Autoinjerto", "3"=>"Injerto", "4"=>"CVCP", "5"=>"CVCT", "6"=>"VP"), '', array('class' => 'form-control input-sm', 'id' => 'txtAccesoVascularVenoso')) !!}
																					</div>
																				</div>
																				<div class="form-group">
																					<label for="txtValoracionEnfermeria" class="col-lg-3 col-md-3 col-sm-3 control-label">Valoración de Enfermería</label>
																					<div class="col-lg-9 col-md-9 col-sm-9">
																						{!! Form::textarea('txtValoracionEnfermeria', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtValoracionEnfermeria', 'rows' => '5')) !!}
																					</div>
																				</div>
																			</div>	
																			<div class="col-lg-6 col-md-6 col-sm-6">
																				<table style="width:100%" border="1">
																                    <thead>
																                    	<tr>
																                    		<th colspan="2" style="text-align: center;">Administración de Medicamentos endovenosos</th>
																                    	</tr>
																                        <tr>
																                            <th class="text-center" width="55%">Programación</th>
																                            <th class="text-center" width="10%">Cantidad</th>
																                        </tr>
																                    </thead>
																                    <tbody>
																                    	<tr>
																                    		<td>
																                    			<input id="txtAdmiMedic11s" type="text" class="form-control input-xs" value="EPOETINA ALFA 2000 UI/ML. INY 1 ML." readonly="readonly">
																                    			<input id="txtAdmiMedic11" name="txtAdmiMedic11" type="hidden" class="txtAdmiMedic1" value="1">
																                    		</td>
																                    		<td><input id="txtAdmiMedic12" name="txtAdmiMedic12" type="text" class="form-control input-xs numerin requerido txtAdmiMedic1"></td>
																                    	</tr>
																                    	<tr>
																                    		<td>
																                    			<input id="txtAdmiMedic21s" type="text" class="form-control input-xs" value="HIERRO 20MG FE/ML. INY 5 ML." readonly="readonly">
																                    			<input id="txtAdmiMedic21" name="txtAdmiMedic21" type="hidden" class="txtAdmiMedic2" value="2">
																                    		</td>
																                    		<td><input id="txtAdmiMedic22" name="txtAdmiMedic22" type="text" class="form-control cuadrin input-xs numerin requerido txtAdmiMedic2"></td>
																                    	</tr>
																                    	<tr>
																                    		<td>
																                    			<input id="txtAdmiMedic31s" type="text" class="form-control input-xs" value="VITAMINA B12 HIDROXICOBALAMINA 1MG7ML INY 1 ML" readonly="readonly">
																                    			<input id="txtAdmiMedic31" name="txtAdmiMedic31" type="hidden" class="txtAdmiMedic3" value="3">
																                    		</td>
																                    		<td><input id="txtAdmiMedic32" name="txtAdmiMedic32" type="text" class="form-control cuadrin input-xs numerin requerido txtAdmiMedic3"></td>
																                    	</tr>
																                    	<tr>
																                    		<td>
																                    			<input id="txtAdmiMedic41s" type="text" class="form-control cuadrin input-xs txtAdmiMedic4" value="">
																                    			<input id="txtAdmiMedic41" name="txtAdmiMedic41" type="hidden" class="form-control cuadrin input-xs txtAdmiMedic4" value="">
																                    		</td>
																                    		<td><input id="txtAdmiMedic42" name="txtAdmiMedic42" type="text" class="form-control cuadrin input-xs numerin txtAdmiMedic4"></td>
																                    	</tr>
																                    	<tr>
																                    		<td>
																                    			<input id="txtAdmiMedic51s" type="text" class="form-control cuadrin input-xs txtAdmiMedic5" value="">
																                    			<input id="txtAdmiMedic51" name="txtAdmiMedic51" type="hidden" class="form-control cuadrin input-xs txtAdmiMedic5" value="">
																                    		</td>
																                    		<td><input id="txtAdmiMedic52" name="txtAdmiMedic52" type="text" class="form-control cuadrin input-xs numerin txtAdmiMedic5"></td>
																                    	</tr>
																                    	<tr>
																                    		<td>
																                    			<input id="txtAdmiMedic61s" type="text" class="form-control cuadrin input-xs txtAdmiMedic6" value="">
																                    			<input id="txtAdmiMedic61" name="txtAdmiMedic61" type="hidden" class="form-control cuadrin input-xs txtAdmiMedic6" value="">
																                    		</td>
																                    		<td><input id="txtAdmiMedic62" name="txtAdmiMedic62" type="text" class="form-control cuadrin input-xs numerin txtAdmiMedic6"></td>
																                    	</tr>
																                    </tbody>
																                </table>
																			</div>				
																		</div>
																		
																		<hr>
																		<strong style="color:blue;">Evaluación de tratamiento de hemodiálisis</strong>
																		<table style="width:100%" border="1">
														                    <thead>
														                        <tr>
														                            <th class="text-center" width="8%">HORA</th>
														                            <th class="text-center" width="8%">P.A.</th>
														                            <th class="text-center" width="8%">PULSO</th>
														                            <th class="text-center" width="8%">Qb</th>
														                            <th class="text-center" width="8%">CND</th>
														                            <th class="text-center" width="8%">R.A.</th>
														                            <th class="text-center" width="8%">R.V.</th>
														                            <th class="text-center" width="8%">PTM</th>
														                            <th class="text-center" width="18%">SOL./HEMODERIVADOS</th>
														                            <th class="text-center" width="18%">OBSERVACIONES</th>
														                        </tr>
														                    </thead>
														                    <tbody>
														                    	@for ($i = 1; $i <= 8; $i++)
															                    	<tr>
															                    		<td>
															                    			<input type="time" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}1" id="txtEvalHemodialisis{{ $i }}1">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}2" id="txtEvalHemodialisis{{ $i }}2">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}3" id="txtEvalHemodialisis{{ $i }}3">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}4" id="txtEvalHemodialisis{{ $i }}4">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}5" id="txtEvalHemodialisis{{ $i }}5">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}6" id="txtEvalHemodialisis{{ $i }}6">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}7" id="txtEvalHemodialisis{{ $i }}7">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}8" id="txtEvalHemodialisis{{ $i }}8">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} norequired" name="txtEvalHemodialisis{{ $i }}9" id="txtEvalHemodialisis{{ $i }}9">
															                    		</td>
															                    		<td>
															                    			<input type="text" class="form-control input-xs trat{{$i}} cuadrin" name="txtEvalHemodialisis{{ $i }}10" id="txtEvalHemodialisis{{ $i }}10">
															                    		</td>
															                    	</tr>
														                    	@endfor
														                    </tbody>
														                </table>
														                <hr>
														                <hr>
														                <strong style="color:blue;">Valoración Final</strong>
														                <div class="form-group">
																			<div class="col-lg-6 col-md-6 col-sm-6">		
																				<div class="form-group">
																					<label for="txtObservacionFinal" class="col-lg-4 col-md-4 col-sm-4 control-label">Observación final</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::textarea('txtObservacionFinal', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtObservacionFinal', 'rows' => '4')) !!}
																					</div>								
																				</div>
																			</div>
																			<div class="col-lg-6 col-md-6 col-sm-6">		
																				<div class="form-group">
																					<label for="txtAspectoFiltro" class="col-lg-4 col-md-4 col-sm-4 control-label">Aspecto de filtro</label>
																					<div class="col-lg-8 col-md-8 col-sm-8">
																						{!! Form::textarea('txtAspectoFiltro', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtAspectoFiltro', 'rows' => '4')) !!}
																					</div>								
																				</div>
																			</div>					
																		</div>
																		<hr>
														                <hr>
														                <strong style="color:blue;">Muestra de análisis (opcional)</strong>
														                <div class="form-group">
																			<div class="col-lg-12 col-md-12 col-sm-12">		
																				<div class="form-group">
																					<label for="txtMuestraAnalisis" class="col-lg-3 col-md-3 col-sm-3 control-label">Muestra de Análisis</label>
																					<div class="col-lg-9 col-md-9 col-sm-9">
																						{!! Form::text('txtMuestraAnalisis', null, array('class' => 'form-control input-sm', 'id' => 'txtMuestraAnalisis', 'style' => 'color:red;')) !!}
																					</div>								
																				</div>
																			</div>				
																		</div>
															    	</div>
															  	</div>
															</div>
												        </div>									
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- /.box-header -->
								</div>
								<!-- /.box -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</section>
					<!-- /.content -->	
				</div>				
	        </div>

			<!-- Modal -->
			<div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				    <div class="modal-content">
					    <div class="modal-body" id="verCita"></div>
				        <div class="modal-footer">
				            <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="exampleModal12" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" id="vSeg" role="document">
				    <div class="modal-content">
					    <div class="modal-body" id="verSeguimiento"></div>
				        <div class="modal-footer">
				            <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="tituloeditar" aria-hidden="true">
				<div class="modal-dialog" role="document">
				    <div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title" id="tituloeditar">Editar Atención</h3>
						</div>
						<div class="form-horizontal">
							<input type="hidden" id="atencion_id" name="atencion_id">
							<div class="col-sm-12">
								<div class="form-group col-sm-4">
									<label for="fechaeditar" class="col-sm-4 control-label">Fecha:</label>
									<div class="col-sm-8">
										<input class="form-control input-sm" id="fechaeditar" readonly name="fechaeditar" type="text">
									</div>
								</div>
								<div class="form-group col-sm-4">
									<label for="historiaeditar" class="col-sm-4 control-label">Historia:</label>
									<div class="col-sm-8">
										<input class="form-control input-sm" id="historiaeditar" readonly name="historiaeditar" type="text">
									</div>
								</div>
								<div class="form-group col-sm-4">
									<label for="numeroeditar" class="col-sm-4 control-label">Tratam.:</label>
									<div class="col-sm-8">
										<input class="form-control input-sm" id="numeroeditar" readonly name="numeroeditar" type="text">
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group col-sm-6">
									<label for="pacienteeditar" class="col-sm-3 control-label">Paciente:</label>
									<div class="col-sm-9">
										<input class="form-control input-sm" id="pacienteeditar" readonly name="pacienteeditar" type="text">
									</div>
								</div>
								<div class="form-group col-sm-6">
									<label for="doctoreditar" class="col-sm-3 control-label">Médico:</label>
									<div class="col-sm-9">
										<input class="form-control input-sm" id="doctoreditar" readonly name="doctoreditar" type="text">
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group col-sm-4">
									<label for="fondoeditar" class="col-sm-9 control-label">Fondo de ojos:</label>
									<div class="col-sm-3">
										<input disabled type="checkbox" id="fondoeditar"><br>
									</div>
								</div>		
								<div class="form-group col-sm-5">
									{!! Form::label('citaproximaeditar', 'Cita prox.:', array('class' => 'col-sm-4 control-label', 'style' => 'text-align:left;' )) !!}
									<div class="col-sm-6">
										{!! Form::date('citaproximaeditar', '', array('class' => 'form-control input-sm', 'id' => 'citaproximaeditar' , 'style' => 'margin-left: -25px;' , 'onchange' => 'cantidadCitasFechaEditar();' )) !!}
									</div>
									<div class="col-sm-2">
										{!! Form::text('citaseditar', '', array('class' => 'form-control input-sm', 'id' => 'citaseditar', 'readOnly')) !!}
									</div>
								</div>	
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									{!! Form::label('motivoeditar', 'Motivo:') !!}
									<textarea class="form-control input-sm" id="motivoeditar" cols="10" rows="3"></textarea>
								</div>	
								<div class="form-group">
									{!! Form::label('antecedenteseditar', 'Antecedentes:') !!}
									<textarea class="form-control input-sm" id="antecedenteseditar" cols="10" rows="3"></textarea>
								</div>
								<div class="form-group">
									{!! Form::label('diagnosticoeditar', 'Diagnostico:') !!}
									<textarea class="form-control input-sm" id="diagnosticoeditar" cols="10" rows="3"></textarea>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									{!! Form::label('tratamientoeditar', 'Tratamiento:') !!}
									<textarea class="form-control input-sm" id="tratamientoeditar" cols="10" rows="4"></textarea>
								</div>
								<div class="form-group">
									{!! Form::label('exploracion_fisicaeditar', 'Exploración Física:') !!}
									<textarea class="form-control input-sm" id="exploracion_fisicaeditar" cols="10" rows="6"></textarea>
								</div>
																		
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									{!! Form::label('cie102editar', 'Cie10:', array('class' => 'col-sm-2 control-label')) !!}
									<div class="col-sm-10">
										{!! Form::text('cie102editar', '', array('class' => 'form-control input-sm', 'id' => 'cie102editar')) !!}
										{!! Form::hidden('cantcieeditar', 0, array('id' => 'cantcieeditar')) !!}
									</div>
									<div>
										<table class="table table-striped table-bordered col-lg-12 col-md-12 col-sm-12 ">
											<thead id="cabeceracieeditar">
												<tr>
													<th width='80%'>Descripción</th>
													<th width='20%'>Eliminar</th>
												</tr>
											</thead>
											<tbody id="detallecieeditar"></tbody>
										</table>
									</div>
								</div>

								<div class="form-group">
									{!! Form::label('exameneseditar', 'Exámenes:', array('class' => 'col-sm-3 control-label', 'style' => 'margin-left: -15px;')) !!}
									<div class="col-sm-9">
										{!! Form::text('exameneseditar', '', array('class' => 'form-control input-sm', 'id' => 'exameneseditar')) !!}
									</div>
									<div>
										<table class="table table-striped table-bordered col-lg-12 col-md-12 col-sm-12">
											<thead id="cabecera">
												<tr>
													<th width='80%'>Descripción</th>
													<th width='20%'>Eliminar</th>
												</tr>
											</thead>
											<tbody id="detalleeditar"></tbody>
										</table>
									</div>										
								</div>	

							</div>
						</div>
				        <div class="modal-footer">
							<button type="button" id="btnGuardarEditar" class="btn btn-success"><i class="glyphicon glyphicon-check"></i> Guardar</button>
				            <button type="button" id="btnCerrarModalEditar" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>
			<!-- Modal -->
			<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				    <div class="modal-content">
					    <div class="modal-body" id="infoPaciente"></div>
				        <div class="modal-footer">
				            <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>
			<!-- Modal -->
			<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				    <div class="modal-content">
					    <div class="modal-body">
					    	<h3 class="text-center" id="tituloantecedentes" style="color: green; font-weight: bold;"></h3>
					    	<textarea placeholder="Pega los antecedentes del anterior sistema." class="form-control" name="infoAntecedentes" id="infoAntecedentes" cols="30" rows="20"></textarea>
					    </div>
				        <div class="modal-footer">
				            <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
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
									<div id="faltaMedico">
										<div class="form-group">
											<label for="inputFaltaMedico" class="col-lg-3 col-md-3 col-sm-3 control-label">Médico</label>
											<div class="col-lg-9 col-md-9 col-sm-9">
												{!! Form::text('inputFaltaMedico', null, array('class' => 'form-control input-sm', 'id' => 'inputFaltaMedico')) !!}
											</div>	
											<input type="hidden" name="idInputFaltaMedico" id="idInputFaltaMedico">
										</div>
										<br>
										<br>
										<div class="form-group">
											<div class="col-lg-12 col-md-12 col-sm-12">
												<div class="text-right">
													<button type="button" data-dismiss="modal" class="btn btn-success btn-sm form-control" onclick="registrarHistoriaClinica();" type="button"><i class="fa fa-ckeck"></i> Reenviar</button>
												</div>
											</div>
										</div>
									</div>
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

			<div id="modalCargando" class="modal fade" role="dialog" style="z-index: 1600;">
			    <div class="modal-dialog">
			        <!-- Modal content-->
			        <div class="modal-content">
			            <div class="modal-header">
			                <h2 class="modal-title text-center" style="color:blue;"><i class="fa fa-thumbs-o-up"></i> Cargando</h2>
			            </div>
			            <div class="modal-body">
			                <div class="row">
			                    <div class="col-md-12 text-center">
			                        <h2 style="color:blue;" id="mensajeCargando"></h2>
			                    </div>
			                </div>
			            </div>
			        </div>
			    </div>
			</div>

			<div id="modalCerradoEspecial" class="modal fade" role="dialog" style="z-index: 1600;">
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
			                        <h2 style="color:blue;">¿Estás seguro de Cancelar esta Atención?</h2>
			                        <h4 style="color:red;">Se creará una Atención Alternativa para este paciente.</h4>
			                    </div>
			                </div>
			            </div>
			            <div class="modal-footer">
			                <button type="button" class="btn btn-success" data-dismiss="modal">Cancelar</button>
			                <button type="button" class="btn btn-danger" onclick="cerradoEspecial();">Aceptar</button>
			            </div>
			        </div>

			    </div>
			</div>

	        <!-- /.content-wrapper -->
	        <footer class="navbar-default navbar-fixed-bottom" style="display: none;">
	            <div class="container-fluid">
	    			<div class="pull-right hidden-xs">
	    				<b>Version</b> 2.3.8
	    			</div>
	    			<strong>Copyright © 2018 <a href="#">GARZASOFT</a>.</strong> All rights
	    			reserved.
	            </div>
			</footer>
	    </div>
	    {!! Form::hidden('actualizado', 'NO', array('id' => 'actualizado')) !!}
	</form>
    <!-- ./wrapper -->
    <!-- jQuery 2.2.3 -->
    {!! Html::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
    <!-- Bootstrap 3.3.6 -->
    {!! Html::script('bootstrap/js/bootstrap.min.js') !!}
    {!! Html::script('plugins/datatables/jquery.dataTables.min.js') !!}
    {!! Html::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
    <!-- Slimscroll -->
    {!! Html::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
    <!-- FastClick -->
    {!! Html::script('plugins/fastclick/fastclick.js') !!}
    <!-- AdminLTE App -->
    {!! Html::script('dist/js/app.min.js') !!}
    <!-- AdminLTE for demo purposes -->
    {!! Html::script('dist/js/demo.js') !!}
    <!-- bootbox code -->
    {!! Html::script('dist/js/bootbox.min.js') !!}
    {{-- Funciones propias --}}
    {!! Html::script('dist/js/funciones.js') !!}
    {{-- jquery.inputmask: para mascaras en cajas de texto --}}
    {!! Html::script('plugins/input-mask/jquery.inputmask.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.date.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.numeric.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.phone.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.regex.extensions.js') !!}
    {{-- bootstrap-datetimepicker: para calendarios --}}
    {!! HTML::script('dist/js/moment-with-locales.min.js') !!}
    {!! HTML::script('dist/js/bootstrap-datetimepicker.min.js') !!}
    {{-- typeahead.js-bootstrap: para autocompletar --}}
    {!! HTML::script('dist/js/typeahead.bundle.min.js') !!}
    {!! HTML::script('dist/js/bloodhound.min.js') !!}
    
</body>
</html>
<script>
	$(document).ready(function () {
		//buscar('{{ $entidad }}');}
		comprobarCitasHoy();
		//HACER VISIBLE LO INVISIBLE
		/*$('input').each(function(index, el) {
			$(this).attr('type', 'text');
		});*/
		testearIPPendientes();
		$("#nombre").keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar2('{{ $entidad }}');
			}
		});
		$("#cie10").keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar3('{{ $entidad }}');
			}
		});
		buscar4();
		tablaAtendidos();
		$('#pestanaAtencion').css('display', 'none');
		//$("#sintomas").prop('disabled', true);
		$("#antecedentes").prop('disabled', true);
		$("#diagnostico").prop('disabled', true);
		$("#tratamiento").prop('disabled', true);
		$("#exploracion_fisica").prop('disabled', true);
		$("#examenes").prop('disabled', true);
		$("#motivo").prop('disabled', true);
		//$("#citaproxima").prop('disabled', true);
		$("#btnGuardar").prop('disabled', true);
		$("#btnGuardarT").prop('disabled', true);
		$("#fondo").prop('disabled', true);
		$('#fondo').prop('checked', false);
		$('.numerin').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		//$('#txtAreaDializador').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		//$('#txtUltrafiltrado').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		//$('#txtConductividad').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		//$('#txtUltrafiltadoProgramado').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$('#fechaatencion').val('{{ date("Y-m-d", strtotime($fechahoy)) }}');
		$("#txtAdmiMedic11").val("1");
	});

	$(document).on('keyup', '#txtPesoInicial2', function(event) {
		event.preventDefault();
		val = $(this).val();
		$("#txtPesoInicial").val(val);
	});

	function calculartxtUltrafiltrado() {
		pi = $("#txtPesoInicial2").val();
		ps = $("#txtPesoSeco").val();
		if(pi=="") {pi=0;}
		if(ps=="") {ps=0;}
		$("#txtUltrafiltrado").val(((parseFloat(pi).toFixed(2)*100/100)-(parseFloat(ps).toFixed(2)*100/100)).toFixed(2));
		$("#txtUltrafiltadoProgramado").val(((parseFloat(pi).toFixed(2)*100/100)-(parseFloat(ps).toFixed(2)*100/100)).toFixed(2));
	}	

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
	$('#inputFaltaMedico').typeahead(null,{
		displayKey: 'value',
		source: doctores.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$('#idInputFaltaMedico').val(datum.id);
	});

	var cie10s = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/cie10autocompletar/%QUERY',
				filter: function (cie10s) {
					return $.map(cie10s, function (cie10) {
						return {
							value: cie10.value,
							id: cie10.id,
						};
					});
				}
			}
		});
		cie10s.initialize();
		$("#cie102").typeahead(null,{
			displayKey: 'value',
			source: cie10s.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#cie102").val("");
			var cantcie = $("#cantcie").val();
			var cie_id = datum.id;
			var existe = false;
			$("#detallecie tr").each(function(){
				if(cie_id == this.id){
					existe = true;
				}
			});
			if(!existe){
				fila =  '<tr data-id="'+ datum.id +'" align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
				$("#detallecie").append(fila);
				cantcie++;
				var cadenacies = '';
				$('#detallecie tr').each(function(index, el) {
					cadenacies += $(this).data('id') + ';';
				});
				$("#cadenacies").val(cadenacies);
			}

		});   

	var cie10seditar = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/cie10autocompletar/%QUERY',
				filter: function (cie10seditar) {
					return $.map(cie10seditar, function (cie10) {
						return {
							value: cie10.value,
							id: cie10.id,
						};
					});
				}
			}
		});
		cie10seditar.initialize();
		$("#cie102editar").typeahead(null,{
			displayKey: 'value',
			source: cie10seditar.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#cie102editar").val("");
			var cantcie = $("#cantcieeditar").val();
			var cie_id = datum.id;
			var existe = false;
			$("#detallecieeditar tr").each(function(){
				if(cie_id == this.id){
					existe = true;
				}
			});
			if(!existe){
				fila =  '<tr align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this,2)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
				$("#detallecieeditar").append(fila);
				cantcie++;
				$("#cantcieeditar").val(cantcie);
			}
		});   



	var examenes = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/examenesAutocompletar/%QUERY',
				filter: function (examenes) {
					return $.map(examenes, function (examen) {
						return {
							value: examen.value,
							id: examen.id,
						};
					});
				}
			}
		});
		examenes.initialize();
		$("#examenes").typeahead(null,{
			displayKey: 'value',
			source: examenes.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#examenes").val("");
			var examen_id = datum.id;
			var existe = false;

			$("#detalle tr").each(function(){
				if(examen_id == this.id){
					existe = true;
				}
			});

			if(!existe){
				fila =  '<tr align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalle(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
				$("#detalle").append(fila);
			}
		});

	
	var exameneseditar = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/examenesAutocompletar/%QUERY',
				filter: function (exameneseditar) {
					return $.map(exameneseditar, function (exameneditar) {
						return {
							value: exameneditar.value,
							id: exameneditar.id,
						};
					});
				}
			}
		});
		exameneseditar.initialize();
		$("#exameneseditar").typeahead(null,{
			displayKey: 'value',
			source: exameneseditar.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#exameneseditar").val("");
			var examen_id = datum.id;
			var existe = false;

			$("#detalleeditar tr").each(function(){
				if(examen_id == this.id){
					existe = true;
				}
			});

			if(!existe){
				fila =  '<tr align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalle(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
				$("#detalleeditar").append(fila);
			}
		});

	//AlertaMal

	function alertaG(mensaje) {
        $('#mensajeAlertaG').html(mensaje);
        $('#modalAlertaG').modal('show');
        faltaMedico("1");
    }

    //AlertaBien

    function alertaB(mensaje) {
        $('#mensajeAlertaB').html(mensaje);
        $('#modalAlertaB').modal('show');
        faltaMedico("1");
    } 

    function alertaC(mensaje) {
        $('#mensajeCargando').html(mensaje);
        $('#modalCargando').modal('show');
        faltaMedico("1");
    }

	function eliminarDetalle(comp){
		(($(comp).parent()).parent()).remove();		
	}

	function eliminarDetalleCie(comp,tipo){
		(($(comp).parent()).parent()).remove();
		var cadenacies = '';
		$('#detallecie tr').each(function(index, el) {
			cadenacies += $(this).data('id') + ';';
		});
		$("#cadenacies").val(cadenacies);
	}

	/*function buscar2(){
		$.ajax({
	        type: "POST",
	        url: "producto/vistamedico",
	        data: "producto="+$("#nombrep").val()+"&_token=<?php echo csrf_token(); ?>",
	        beforeSend: function() {
	        	$("#listado{{ $entidad }}").html('Buscando...');
	        },
	        success: function(a) {
	        	$("#listado{{ $entidad }}").html(a);
	        }
	    });
	}


	$(document).ready(function(){     
	      $("#nombrep").keypress(function(e) {
	        if(e.which == 13) {
	            buscar2();
	        }	
	    });		
	});*/

	function buscar3(){
		$.ajax({
	        type: "POST",
	        url: "producto/cie10",
	        data: "cie="+$("#cie10").val()+"&_token=<?php echo csrf_token(); ?>",
	        beforeSend: function() {
	        	$("#listado3{{ $entidad }}").html('Buscando...');
	        },
	        success: function(a) {
	        	$("#listado3{{ $entidad }}").html(a);
	        	$("#txtAdmiMedic11").val("1");
	        }
	    });
	}

	function comprobarCitasHoy(){
		$.ajax({
			type: "POST",
			url: "ventaadmision/comprobarCitasHoy",
			data: "_token=<?php echo csrf_token(); ?>"
		});
	}

	function buscar4(){
		$.ajax({
				type: "POST",
				url: "ventaadmision/colamedico",
				data: {
					//"actualizado" : actualizado, 
					"_token": "{{ csrf_token() }}",
				},
				dataType: 'json',
				success: function(a) {
					$("#listadoConsultas").html(a.rpta);
					//$("#listadoEmergencias").html(a.emergencias);
					//$("#listadoOjos").html(a.ojos);
					//$("#listadoLectura").html(a.lectura);
					tablaAtendidos();
					$("#txtAdmiMedic11").val("1");
				}
			});
		setInterval( 
			function(){
				$('.llamando').fadeTo(500, .1).fadeTo(500, 1);
				quitarPadding();
			}
		, 1000);
	}
    setInterval(buscar4, 4000);

    function quitarPadding() {
        $('.skin-blue').removeAttr('style');
    }
	
	function tablaAtendidos(a = ''){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/tablaAtendidos') }}",
			"data": {
				"_token": "{{ csrf_token() }}",
				"fechaatencion": $("#fechaatencion").val(),
				"nombre_atendido": $("#nombre_atendido").val(),
				},
			"dataType": 'JSON',
			"beforeSend": function() {
				if(a == '1') {
					$('#tablaAtendidos').html('Cargando...');					
				}
			},
		}).done(function(info){
			$('#tablaAtendidos').html(info['tabla']);
			$('#titulofecha').html(info['fecha']);
			//alert(info['fecha']);
		});
	}	

	$(document).ready(function(){     
	      $("#nombre_servicio").keypress(function(e) {
	        if(e.which == 13) {
	            buscarServicio();
	        }	
	    });		
	});

	$(document).ready(function(){     
	      $("#nombre_atendido").keypress(function(e) {
	        if(e.which == 13) {
	            tablaAtendidos(1)
	        }	
	    });		
	});

	function buscarServicio(){
		$.ajax({
	        type: "POST",
	        url: "servicio/buscar",
	        data: "nombre=" + $("#nombre_servicio").val() + "&tipopago=" + $("#tipopago").val() + "&page=" + $("#page").val() + "&filas=" + $("#filas").val() + "&vistamedico=" + "SI" + "&tiposervicio=" + $("#tiposervicio").val() + "&_token=<?php echo csrf_token(); ?>",
	        beforeSend: function(a) {
	        	$("#listado2{{ $entidad }}").html('Buscando...');
	        },
	        success: function(a) {
	        	$("#listado2{{ $entidad }}").html(a);
	        	$("#txtAdmiMedic11").val("1");
	        }
	    });
	}

	function buscarHistoria(){
		$.ajax({
	        type: "POST",
	        url: "historia/buscar",
	        data: "nombre=" + $("#nombreh").val() + "&dni=" + $("#dni").val() + "&numero=" + $("#numeroh").val() + "&page=" + $("#pageh").val() + "&filas=" + $("#filash").val() + "&vistamedico=" + "SI" + "&tipopaciente=" + $("#tipopaciente").val() + "&_token=<?php echo csrf_token(); ?>",
	        beforeSend: function() {
	        	$("#listadoh{{ $entidad }}").html('Buscando...');
	        },
	        success: function(a) {
	        	$("#listadoh{{ $entidad }}").html(a);
	        	$("#txtAdmiMedic11").val("1");
	        }
	    });
	}

	$(document).ready(function(){     
      	$("#nombreh").keypress(function(e) {
	        if(e.which == 13) {
	            buscarHistoria();
	        }	
	    });		
	    $("#dni").keypress(function(e) {
	        if(e.which == 13) {
	            buscarHistoria();
	        }	
	    });	
	    $("#numeroh").keypress(function(e) {
	        if(e.which == 13) {
	            buscarHistoria();
	        }	
	    });	
	});

	/*function buscarAtendido(){
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/tablaAtendidos",
	        data: "nombre=" + $("#nombre_atendido").val() + "&_token=<?php echo csrf_token(); ?>",
	        beforeSend:function() {
	        	$('#tablaAtendidos').html('Cargando...');
	        },
	        success: function(a) {
	        	$('#tablaAtendidos').html(a);
	        }
	    });
	}*/

	$(document).ready(function(){     
	      $("#nombre_atendido").keypress(function(e) {
	        if(e.which == 13) {
	            tablaAtendidos();
	        }	
	    });		
	});


	function tablaCita(historia_id){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/tablaCita') }}",
			"data": {
				"historia_id" : historia_id, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#tablaCita').html(info);
		});
	}

	function llamarPacienteNombre(){

		$('#mensajeBusquedaPaciente').html('');

		var paciente = $("#buscarPaciente").val();

		if(paciente != ""){

			$.ajax({
				"method": "POST",
				"url": "{{ url('/ventaadmision/llamarPacienteNombre') }}",
				"data": {
					"paciente" : paciente, 
					"_token": "{{ csrf_token() }}",
				},
				beforeSend:function() {
					$('#resultadoBusquedaPaciente').html('Buscando...');
				},
			}).done(function(info){
				$('#resultadoBusquedaPaciente').html(info);
			});

		}else{
			$('#mensajeBusquedaPaciente').html('Ingrese paciente');
			$('#resultadoBusquedaPaciente').html('');
		}
	}

	function cambiarEstadoIP(historia_id, cid, action='0') {
		//alert(cid);
		$.ajax({
			url: 'historiaclinica/cambiarEstadoIP',
			type: 'POST',
			data: {
				"historia_id": historia_id,
				"cid": cid,
				"action":action,
				"_token": "{{ csrf_token() }}",
			},
		});		
	}

	function testearIPPendientes() {
		$.ajax({
			url: 'historiaclinica/testearIPPendientes',
			type: 'POST',
			data: {
				"_token": "{{ csrf_token() }}",
			},
			dataType: 'json',
			beforeSend: function(){
				alertaC("<center><img src=\"{{ asset('dist/img/cargando.gif') }}\" height='100' width='100' alt='' /></center>");
			},
			success: function(a) {
				if(a.action === 'SI') {
					$("li").removeClass('in active');
					$('#cie').removeClass('in active');
					$('#cola').removeClass('in active');
					$('#atencion').addClass('in active');
	  				$("#pestanaAtencion").css('display', '').addClass('active');
	  				$("#pestanaPacienteCola").removeClass('active');	
	  				$('#historia_id').val(a.historia_id);
	  				$('#id_hc').val(a.cid);
	  				$('#historia').val(a.numhistoria);
	  				$('#paciente').val(a.paciente);
					$('#citaproxima').val(a.citaproxima);
					$('#plan_susalud').val(a.plan_susalud);
					$("#fecha_atencion").val(a.fecha_atencion);
					$('#nsesion').val(a.numsesion);
					$('#frecuencia').val(a.frecuencia);
					$('#turno').val(a.turno);
					if(a.estado == 'A') {
						$('#btnGuardar').removeAttr('disabled');
						$('#btnGuardarT').removeAttr('disabled');
						$('.collapse').collapse('show');
						$('.abrircerrar').removeClass('hidden');
					}

					/////////////////////////////

					$('#txtEvoSigSin').val(a.txtEvoSigSin);
	            	$('#txtPA').val(a.txtPA);
	            	$('#txtFC').val(a.txtFC);
	            	$('#txtFR').val(a.txtFR);
	            	$('#txtHorasHemodialisis').val(a.txtHorasHemodialisis);
	            	$('#txtPesoInicial').val(a.txtPesoInicial);
	            	$('#txtQb').val(a.txtQb);
	            	$('#txtNaInicial').val(a.txtNaInicial);
	            	$('#txtDosisHepa').val(a.txtDosisHepa);
	            	$('#txtPesoFinal').val(a.txtPesoFinal);
	            	$('#txtQd').val(a.txtQd);
	            	$('#txtNaFinal').val(a.txtNaFinal);
	            	$('#txtPesoSeco').val(a.txtPesoSeco);
	            	$('#txtPerfilUF').val(a.txtPerfilUF);
	            	$('#txtBufer').val(a.txtBufer);
	            	//alert(a.txtBufer);
	            	$('#txtPerfilNa').val(a.txtPerfilNa);
	            	$('#txtMedicacion').val(a.txtMedicacion);
	            	$('#txtUltrafiltrado').val(a.txtUltrafiltrado);
	            	$('#txtConductividad').val(a.txtConductividad);
	            	$('#txtAreaDializador').val(a.txtAreaDializador);
	            	$('#txtMembranaDializador').val(a.txtMembranaDializador);
	            	$('#txtCondicionClinicaFinal').val(a.txtCondicionClinicaFinal);
	            	$('#txtPAInicial').val(a.txtPAInicial);
	            	$('#txtNPuesto').val(a.txtNPuesto);
	            	$('#txtPesoInicial2').val(a.txtPesoInicial2);
	            	$('#txtMarcaModeloMaquina').val(a.txtMarcaModeloMaquina);
	            	$('#txtMarcaModeloMaquina2').val(a.txtMarcaModeloMaquina2);
	            	$('#txtUltrafiltadoProgramado').val(a.txtUltrafiltadoProgramado);
	            	$('#txtUltrafiltadoProgramado3').val(a.txtUltrafiltadoProgramado3);
	            	$('#txtUltrafiltadoProgramado2').val(a.txtUltrafiltadoProgramado2);
	            	$('#txtLoteSerieFiltro').val(a.txtLoteSerieFiltro);
	            	$('#txtLoteSerieFiltro2').val(a.txtLoteSerieFiltro2);
	            	$('#txtAccesoVascularArterial').val(a.txtAccesoVascularArterial);
	            	$('#txtAccesoVascularVenoso').val(a.txtAccesoVascularVenoso);
	            	$('#txtPAFinal').val(a.txtPAFinal);
	            	$('#txtNMAquina').val(a.txtNMAquina);
	            	$('#txtTemperatura').val(a.txtTemperatura);
	            	$('#txtPesoFinal2').val(a.txtPesoFinal2);
	            	$('#txtAreaMembranaFiltro').val(a.txtAreaMembranaFiltro);
	            	$('#txtValoracionEnfermeria').val(a.txtValoracionEnfermeria);                	
	            	$('#txtObservacionFinal').val(a.txtObservacionFinal);
	            	$('#txtAspectoFiltro').val(a.txtAspectoFiltro);                	
	            	$('#txtMuestraAnalisis').val(a.txtMuestraAnalisis);
	            	$('#txtHoraEvaluacionPrevia').val(a.txtHoraEvaluacionPrevia);
	            	$('#cadenacies').val(a.txtCies);
	            	$('#labelconvenio').text(a.convenio);
	            	$('#romano').val(a.romano);

	            	//inicializo la Tabla de Cies
	            	inicializarTablaCies(a.txtCies);

                	//Inicializo Administración de Medicamentos
                	inicializarTablaMedicamentos(a.txtAdmiMedic);

                	//incializo tabla Evaluación de Hemodiálisis
                	inicializarTablaEvaluacion(a.txtEvalHemodialisis);

					///////////////////////////////	

					$('#btnNo').prop('disabled', true);
					$('#btnSi').prop('disabled', true);		
					calculartxtUltrafiltrado();	
					$("#txtAdmiMedic11").val("1");	
				}
				$('#modalCargando').modal('hide');
			}
		});		
	}

    $(document).on('click', '.btnLlamarPaciente', function(event) {
    	event.preventDefault();
    	var histo = $('#historia_id').val();
    	if(histo!=='') {
    		alertaG('Tienes que atender una consulta a la vez.');
    		$("li").removeClass('in active');
			$('#cie').removeClass('in active');
			$('#cola').removeClass('in active');
			$('#atencion').addClass('in active');
			$("#pestanaAtencion").css('display', '').addClass('active');
			$("#pestanaPacienteCola").removeClass('active');
    		return false;
    	}
    	//inicializo inputs y cuadros
    	$('.requerido').val('');
    	$('.cuadrin').val('');
    	var historia_id = $(this).data('id');
    	var cid = $(this).data('cid');
    	$("#id_hc").val(cid);    	
    	$.ajax({
	        type: "POST",
	        url: "historiaclinica/nuevaHistoriaClinica",
	        data: {
	        	"_token":"<?php echo csrf_token(); ?>",
	        	"historia_id":historia_id,
	        	"inicial": "1",
	        },
	        dataType: "json",
	        beforeSend: function() {
				$(this).html('Cargando...');
				$('.btnLlamarPaciente').attr('disabled', 'disabled');
			},
	        success: function(a) {
	        	$("li").removeClass('in active');
				$('#cie').removeClass('in active');
				$('#cola').removeClass('in active');
				$('#atencion').addClass('in active');
  				$("#pestanaAtencion").css('display', '').addClass('active');

  				$('.abrircerrar').addClass('hidden');
  				$('.collapse').collapse('hide');  				

  				$("#pestanaPacienteCola").removeClass('active');	
  				$('#historia_id').val(a.historia_id);
  				$('#historia').val(a.numhistoria);
  				$('#paciente').val(a.paciente);
				$('#citaproxima').val(a.citaproxima);
				$('#plan_susalud').val(a.plan_susalud);
				$('#nsesion').val(a.numsesion);
				$('#frecuencia').val(a.frecuencia);
				$('#turno').val(a.turno);
				$('#romano').val(a.romano);

				$("#txtPesoInicial2").val("");
				$("#txtAccesoVascularArterial").val("");
				$("#txtAccesoVascularVenoso").val("");

	            $("#txtMembranaDializador").val("POLISULFONA");  
	            $("#txtBufer").val("BICARBONATO");
	            $("#txtMedicacion").val("* EPOETINA ALFA 2000 UI/ML. INY 1 ML. \n* HIERRO 20MG FE/ML. INY 5 ML. \n* VITAMINA B12 HIDROXICOBALAMINA 1MG7ML INY 1 ML. ");
	            $("#txtMarcaModeloMaquina").val("NIPRO");
	            $("#txtMarcaModeloMaquina2").val("DIAMAX");

	            $(".norequired").val("");

				$("#detallecie").html('');
				$("#cadenacies").val('');

				$(this).html('<i class="fa fa-check fa-lg"></i>Llamar');
				$('.btnLlamarPaciente').removeAttr('disabled');

				$('#txtMuestraAnalisis').val('');

				$('#btnGuardar').prop('disabled', true);
  				$('#btnGuardarT').prop('disabled', true);

  				$('.requerido2').removeClass('requerido2');

  				$('#btnNo').prop('disabled', false);
				$('#btnSi').prop('disabled', false);
				$("#txtAdmiMedic11").val("1");

				$("#btnCerradoEspecial").prop("disabled", true);

				//Inicializo Administración de Medicamentos
                inicializarTablaMedicamentos(a.txtAdmiMedic);

                //alert(a.txtAdmiMedic);

				cambiarEstadoIP(historia_id, cid);
	        },
			error: function() {
				$("#btnCerradoEspecial").prop("disabled", false);
				alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
		    }
	    });
    });

    function validarInputs10() {
		var a = true;
		$('.requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});

		for (var i = 1; i <= 9; i++) {
			var tot = 0;
			$('.trat'+i).each(function(index, el) {
				if($(this).val().length==0 && !$(this).hasClass("norequired")) {
                	tot++;	
				}
			});

			if(tot != 9 && tot != 0) {
				a = false;
				$('.trat'+i).each(function(index, el) {
					if($(this).val().length==0 && !$(this).hasClass("norequired")) {
	                	$(this).addClass('requerido2');	 
	                	tot++;               	
					} else {
						$(this).removeClass('requerido2');						
					}
				});
			} else {
				$('.trat'+i).each(function(index, el) {
					$(this).removeClass('requerido2');
				});
			}
		}

		for (var i = 4; i <= 6; i++) {
			var tot = 0;
			$('.txtAdmiMedic'+i).each(function(index, el) {
				if(!$(this).hasClass('tt-hint')&&!$(this).hasClass('tt-input')) {
					if($(this).val().length==0) {                	
						tot++;
					}
				}
			});

			if(tot === 1) {
				a = false;
				$('.txtAdmiMedic'+i).each(function(index, el) {
					if(!$(this).hasClass('tt-hint')&&!$(this).hasClass('tt-input')) {
						if($(this).val().length==0) {
		                	$(this).addClass('requerido2');
						} else {
							$(this).removeClass('requerido2');
						}
					}
				});
			} else {
				$('.txtAdmiMedic'+i).each(function(index, el) {
					$(this).removeClass('requerido2');
				});
			}
		}
		return a;
	}

	$(document).on('keyup', '.requerido2', function(event) {
		event.preventDefault();
		var palabra = $(this).val();
		if(palabra !== '') {
            $(this).removeClass('requerido2');
		}
	});

    function registrarHistoriaClinica() {
    	comprobarMedico();
    	$("#btnGuardar").prop('disabled', true).html('Cargando...');
    	if($("#cadenacies").val() === '') {
    		a = 'Debes seleccionar al menos un CIE10.';
			alertaG(a);
			$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Cerrar Atención');
			return false;
    	}    	
    	if(!validarInputs10()) {
			a = 'Corrige los campos en rojo y vuelve a enviar.';
			alertaG(a);
			$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Cerrar Atención');
			return false;
		} 
		if($("#idInputFaltaMedico").val() === '') {    		
    		a = 'Debes ingresar un médico responsable';
			alertaG(a);
			$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Cerrar Atención');
			faltaMedico("2");
			return false;
    	} else {
			$.ajax({
		        type: "POST",
		        url: "historiaclinica/registrarHistoriaClinica",
		        data: $('#formHistoriaClinica').serialize(),
		        beforeSend: function() {
		        	$("#btnGuardar").prop('disabled', true).html('Cargando...');
		        },
		        success: function(a) {
		        	if(a == 'OK') {
		        		alertaB('TRATAMIENTO FINALIZADO CORRECTAMENTE...');
		        		$("li").removeClass('in active');
						$('#cola').addClass('in active');
						$('#atencion').removeClass('in active');
		  				$("#pestanaAtencion").css('display', 'none').removeClass('active');
		  				$("#pestanaPacienteCola").addClass('active');
						$("#divpresente").css('display','');
						$("#btnGuardar").prop('disabled', true).html('<i class="glyphicon glyphicon-check"></i> Cerrar Atención');
						$('.requerido').val('');
    					$('.cuadrin').val('');
    					$('#historia_id').val('');
						tablaAtendidos();
		        	}else{
		        		alertaG('OCURRIÓ UN ERROR AL GUARDAR, VUELVA A INTENTAR...');
		        		$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Cerrar Atención');
		        	}
		        	$("#txtAdmiMedic11").val("1");
		        },
				error: function() {
					$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Cerrar Atención');
					alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
			    }
		    });
		}
	}

	/*function ver(cita_id){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/ver') }}",
			"data": {
				"cita_id" : cita_id, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#verCita').html(info);
		});
	}*/

	function ver(id){
        window.open("historia/pdfhistoriaclinica?id="+id);
    }

    function verSeguimiento(id){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/verSeguimiento') }}",
			"data": {
				"id" : id, 
				"_token": "{{ csrf_token() }}",
				},
			"beforeSend": function() {
				$('#verSeguimiento').html('Cargando...');
			}
		}).done(function(info){
			$('#verSeguimiento').html(info);
		});
	}

	/*function editar(cita_id){
		return false;
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/editarCita",
	        data: "_token=<?php echo csrf_token(); ?>" + "&cita_id=" + cita_id,
	        dataType: "json",
	        success: function(a) {
				$('#atencion_id').val(a.atencion_id);
	        	$('#fechaeditar').val(a.fecha);
				$('#doctoreditar').val(a.doctor);
				$('#historiaeditar').val(a.numhistoria);
				$('#pacienteeditar').val(a.paciente);
				$('#numeroeditar').val(a.numero);
				//$('#cie10editar').val(a.cie10);
				$('#motivoeditar').val(a.motivo.replace(/<BR>/g,"\n"));
				console.log(a.citaproxima);
				$('#citaproximaeditar').val(a.citaproxima);
				//ANTECEDENTES
				$('#antecedenteseditar').val(a.antecedentes.replace(/<BR>/g,"\n"));
				$('#tratamientoeditar').val(a.tratamiento.replace(/<BR>/g,"\n"));
				$('#diagnosticoeditar').val(a.diagnostico.replace(/<BR>/g,"\n"));
				$('#exploracion_fisicaeditar').val(a.exploracion_fisica.replace(/<BR>/g,"\n"));
				//$('#exameneseditar').val(a.examenes);
				console.log(a.examenes);
				var arr = a.examenes;
				$.each(arr, function (index, value) {
					var fila =  '<tr align="center" id="'+ value.servicio_id +'" ><td style="vertical-align: middle; text-align: left;">'+ value.nombre +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalle(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
					$("#detalleeditar").append(fila);
				});

				console.log(a.cies);
				var arrcies = a.cies;
				$.each(arrcies, function (index, value) {
					var fila =  '<tr align="center" id="'+ value.cie_id +'" ><td style="vertical-align: middle; text-align: left;">'+ value.descripcion +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
					$("#detallecieeditar").append(fila);
				});
				$('#cantcieeditar').val(a.cantcies);
				if(a.fondo == "SI"){
					$('#fondoeditar').prop('checked', true);
				}else{
					$('#fondoeditar').prop('checked', false);
				}
				cantidadCitasFechaEditar();
				$("#txtAdmiMedic11").val("1");
	        }
	    });
	}*/	
	
	$(document).on('click', '#btnCerrarModalEditar', function(event) {
		$('#exameneseditar').val('');
		$('#cie102editar').val('');
		$('#detalleeditar').html('');
		$('#detallecieeditar').html('');
		$('#exampleModal2').modal('hide');
	});


	$(document).on('click', '#btnGuardarEditar', function(event) {	

		if($('#cantcieeditar').val() == 0) {
    		$('#cie102editar').focus();
    		alertaG('Debes ingresar mínimo un CIE 10.');
    		return 0;
    	}

		var cita_id = $("#atencion_id").val();
		var tratamiento = $('#tratamientoeditar').val().replace(/\r?\n/g, "<BR>");
    	var antecedentes = $('#antecedenteseditar').val().replace(/\r?\n/g, "<BR>");
    	var diagnostico = $('#diagnosticoeditar').val().replace(/\r?\n/g, "<BR>");
		//var examenes = $('#exameneseditar').val().replace(/\r?\n/g, "<BR>");
    	var motivo = $('#motivoeditar').val().replace(/\r?\n/g, "<BR>");
    	var exploracion_fisica = $('#exploracion_fisicaeditar').val().replace(/\r?\n/g, "<BR>");
		var citaproxima = $('#citaproximaeditar').val();

		/*//editar datos

		tratamiento = tratamiento.replace(',', "<br>");
    	antecedentes = antecedentes.replace(',', "<br>");
    	diagnostico = diagnostico.replace(',', "<br>");
		//var examenes = $('#exameneseditar').val().replace(',', "<br>");
    	motivo = motivo.replace(',', "<br>");
    	exploracion_fisica = exploracion_fisica.replace(',', "<br>");*/

		
		//detalle
		var data = [];
		$("#detalleeditar tr").each(function(){
			var element = $(this); // <-- en la variable element tienes tu elemento
			var id = element.attr('id');
			data.push(
				{ "id": id }
			);
		});
		var detalle = {"data": data};
		var json = JSON.stringify(detalle);

		//var cita_id = $('#cita_id').val();

		//fin detalle


		//detalle
		var datacie = [];
		$("#detallecieeditar tr").each(function(){
			var element = $(this); // <-- en la variable element tienes tu elemento
			var id = element.attr('id');
			datacie.push(
				{ "id": id }
			);
		});
		var detallecie = {"data": datacie};
		var jsoncie = JSON.stringify(detallecie);
		//fin detalle

/*
		var fondo = "NO";
		if( $('#fondoeditar').prop('checked') ){
			fondo = "SI";
		}

*/
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/guardarEditado') }}",
			"data": {
				"cita_id" : cita_id, 
				"tratamiento" : tratamiento,
				"antecedentes" : antecedentes,
				"diagnostico" : diagnostico,
				"examenes" : json,
				"cies" : jsoncie,
				"citaproxima" : citaproxima,
				"motivo" : motivo,
	//			"fondo" : fondo,
				"exploracion_fisica" : exploracion_fisica,
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			if(info == 'OK') {
				alertaB('TRATAMIENTO REGISTRADO CORRECTAMENTE...');
				$('#exampleModal2').modal('hide');
				$('#citaseditar').val("");
				$('#detalleeditar').html('');
				$('#detallecieeditar').html('');
				tablaAtendidos();
			}else{
				alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
        	}
		});

	});

	function presente(estado){
		var historia_id = $('#historia_id').val();
		$("#btnCerradoEspecial").prop("disabled", false);
		if(estado == "SI"){
			$("#btnGuardar").prop('disabled', false);
			$("#btnGuardarT").prop('disabled', false);
			//Habilitar inputs			
			cambiarEstadoIP(historia_id, $("#id_hc").val(), '1');
			$('.abrircerrar').removeClass('hidden');
			$('.collapse').collapse('show');
			$("#txtMembranaDializador").val("POLISULFONA");  
            $("#txtBufer").val("BICARBONATO");
            $("#txtMedicacion").val("* EPOETINA ALFA 2000 UI/ML. INY 1 ML. \n* HIERRO 20MG FE/ML. INY 5 ML. \n* VITAMINA B12 HIDROXICOBALAMINA 1MG7ML INY 1 ML. ");
            $("#txtMarcaModeloMaquina").val("NIPRO");
            $("#txtMarcaModeloMaquina2").val("DIAMAX");
            $("#btnCerradoEspecial").prop("disabled", false);
		}else{
			cambiarEstadoIP(historia_id, $("#id_hc").val(), '2');
			$('.collapse').collapse('hide');
			$('.abrircerrar').addClass('hidden');
			$("#divpresente").css('display','');
			$("li").removeClass('in active');
			$('#cie').removeClass('in active');
			$('#cola').addClass('in active');
			$('#atencion').removeClass('in active');
			$("#pestanaAtencion").css('display', 'none').removeClass('active');
			$("#pestanaPacienteCola").addClass('active');
			$("#btnGuardar").prop('disabled', true);
			$("#btnGuardarT").prop('disabled', true);
			$('.requerido').val('');
    		$('.cuadrin').val('');
    		$('#historia_id').val('');
		}
	}

	function cantidadCitasFecha(){
		
		var fecha = $('#citaproxima').val();

		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/cantidadCitasFecha') }}",
			"data": {
				"fecha" : fecha, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#citas').val(info);
		});

	}

	function cantidadCitasFechaEditar(){
		
		var fecha = $('#citaproximaeditar').val();

		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/cantidadCitasFecha') }}",
			"data": {
				"fecha" : fecha, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#citaseditar').val(info);
		});

	}

	$(document).on('click', '#btnInfoPaciente', function(event) {
		var historia = $('#historia_id').val();
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/infoPaciente') }}",
			"data": {
				"historia" : historia, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#infoPaciente').html(info);
		});
	});

	$(document).on('click', '#btnInfoAntecedentes', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var historia = $('#historia').val();
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/infoAntecedentes') }}",
			"data": {
				"historia" : historia, 
				"_token": "{{ csrf_token() }}",
				},
            beforeSend:function() {
            	$('#infoAntecedentes').attr('readonly', 'readonly').val('');
            }
		}).done(function(info){
			$('#infoAntecedentes').removeAttr('readonly').val(info).focus();
		});
	});

	function abrirModalAntecedentesPasados(id, paciente) {
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/infoAntecedentes') }}",
			"data": {
				"historia" : id, 
				"_token": "{{ csrf_token() }}",
				},
			beforeSend:function() {
				$('#infoAntecedentes').attr('readonly', 'readonly').val('');
				$('#historia').val(id);
			}
		}).done(function(info){			
			$('#tituloantecedentes').html('Antecedentes de '+paciente);
			$('#infoAntecedentes').removeAttr('readonly').val(info).focus();
			//alert(info);
		}).fail(function() {
			alertaG('Ha ocurrido un error.' + id);
		});
	}

	$(document).on('keyup', '#infoAntecedentes', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var historia = $('#historia').val();
		var antecedentes = $(this).val();
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/actualizarAntecedentes') }}",
			"data": {
				"historia" : historia, 
				"_token": "{{ csrf_token() }}",
				"antecedentes": antecedentes,
				}
		});
	});

	function registrarHistoriaClinica2() {
    	$("#btnGuardarT").prop('disabled', true).html('Cargando...');    	
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/registrarHistoriaClinica2",
	        data: $('#formHistoriaClinica').serialize(),
	        beforeSend: function() {
	        	$("#btnGuardarT").prop('disabled', true).html('Cargando...');
	        },
	        success: function(a) {
	        	if(a == 'OK') {
	        		alertaB('TRATAMIENTO GUARDADO CORRECTAMENTE...');
	        		$("li").removeClass('in active');
					$('#cola').addClass('in active');
					$('#atencion').removeClass('in active');
	  				$("#pestanaAtencion").css('display', 'none').removeClass('active');
	  				$("#pestanaPacienteCola").addClass('active');
					$("#divpresente").css('display','');
					$("#btnGuardarT").prop('disabled', true).html('<i class="glyphicon glyphicon-check"></i> Guardado Temporal');
					$('.requerido').val('');
					$('.cuadrin').val('');
					$('#historia_id').val('');
					tablaAtendidos();
	        	}else{
	        		$("#btnGuardarT").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Guardado Temporal');
	        		alertaG('OCURRIÓ UN ERROR AL GUARDAR, VUELVA A INTENTAR...');
	        	}
	        	$("#txtAdmiMedic11").val("1");
	        },
			error: function() {
				$("#btnGuardarT").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Guardado Temporal');
				alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
		    }
	    });
	}

	$(document).on('click', '.btnLlamarPaciente2', function(event) {
    	event.preventDefault();
    	var histo = $('#historia_id').val();
    	var cid = $(this).data("hid");
    	//alert(cid);
    	if(histo!=='') {
    		alertaG('Tienes que atender una consulta a la vez.');
    		$("li").removeClass('in active');
			$('#cie').removeClass('in active');
			$('#cola').removeClass('in active');
			$('#atendidos').removeClass('in active');
			$('#atencion').addClass('in active');
			$("#pestanaAtencion").css('display', '').addClass('active');
			$("#pestanaAtenciones").removeClass('active');
    		return false;
	    } else {
	    	//inicializo inputs y cuadros
	    	$('.requerido').val('');
	    	$('.cuadrin').val('');
	    	var historia_id = $(this).data('id');
	    	$("#id_hc").val(cid);	    	
	    	$.ajax({
		        type: "POST",
		        url: "historiaclinica/nuevaHistoriaClinica",
		        data: {
		        	"_token":"<?php echo csrf_token(); ?>",
		        	"historia_id":historia_id,
		        	"cid":cid,
		        },
		        dataType: "json",
		        beforeSend: function() {
					$(this).html('Cargando...');
					$('.btnLlamarPaciente2').attr('disabled', 'disabled');
				},
		        success: function(a) {

		        	//////////////////
					$('#btnGuardar').removeAttr('disabled');
					$('#btnGuardarT').removeAttr('disabled');
					$('.collapse').collapse('show');
					$('.abrircerrar').removeClass('hidden');
		        	//////////////

		        	$('#detallecie').html('');

		        	$("li").removeClass('in active');
					$('#cie').removeClass('in active');
					$('#cola').removeClass('in active');
					$('#atendidos').removeClass('in active');
					$('#atencion').addClass('in active');
					$("#pestanaAtenciones").removeClass('active');
	  				$("#pestanaAtencion").css('display', '').addClass('active');	  					
	  				$('#historia_id').val(a.historia_id);
	  				$("#fecha_atencion").val(a.fecha_atencion);
	  				$('#historia').val(a.numhistoria);
	  				$('#paciente').val(a.paciente);
					$('#citaproxima').val(a.citaproxima);
					$('#plan_susalud').val(a.plan_susalud);
					$('#nsesion').val(a.numsesion);
					$('#frecuencia').val(a.frecuencia);
					$('#turno').val(a.turno);
					$('#btnGuardarT').removeAttr('disabled');
					$('#txtEvoSigSin').val(a.txtEvoSigSin);
                	$('#txtPA').val(a.txtPA);
                	$('#txtFC').val(a.txtFC);
                	$('#txtFR').val(a.txtFR);
                	$('#txtHorasHemodialisis').val(a.txtHorasHemodialisis);
                	$('#txtPesoInicial').val(a.txtPesoInicial);
                	$('#txtQb').val(a.txtQb);
                	$('#txtNaInicial').val(a.txtNaInicial);
                	$('#txtDosisHepa').val(a.txtDosisHepa);
                	$('#txtPesoFinal').val(a.txtPesoFinal);
                	$('#txtQd').val(a.txtQd);
                	$('#txtNaFinal').val(a.txtNaFinal);
                	$('#txtPesoSeco').val(a.txtPesoSeco);
                	$('#txtPerfilUF').val(a.txtPerfilUF);
                	$('#txtBufer').val(a.txtBufer);
                	//alert(a.txtBufer);
                	$('#txtPerfilNa').val(a.txtPerfilNa);
                	$('#txtMedicacion').val(a.txtMedicacion);
                	$('#txtUltrafiltrado').val(a.txtUltrafiltrado);
                	$('#txtConductividad').val(a.txtConductividad);
                	$('#txtAreaDializador').val(a.txtAreaDializador);
                	$('#txtMembranaDializador').val(a.txtMembranaDializador);
                	$('#txtCondicionClinicaFinal').val(a.txtCondicionClinicaFinal);
                	$('#txtPAInicial').val(a.txtPAInicial);
                	$('#txtNPuesto').val(a.txtNPuesto);
                	$('#txtPesoInicial2').val(a.txtPesoInicial2);
                	$('#txtMarcaModeloMaquina').val(a.txtMarcaModeloMaquina);
                	$('#txtMarcaModeloMaquina2').val(a.txtMarcaModeloMaquina2);
                	$('#txtUltrafiltadoProgramado').val(a.txtUltrafiltadoProgramado);
                	$('#txtUltrafiltadoProgramado2').val(a.txtUltrafiltadoProgramado2);
                	$('#txtUltrafiltadoProgramado3').val(a.txtUltrafiltadoProgramado3);
                	$('#txtLoteSerieFiltro').val(a.txtLoteSerieFiltro);
                	$('#txtLoteSerieFiltro2').val(a.txtLoteSerieFiltro2);
                	$('#txtAccesoVascularArterial').val(a.txtAccesoVascularArterial);
                	$('#txtAccesoVascularVenoso').val(a.txtAccesoVascularVenoso);
                	$('#txtPAFinal').val(a.txtPAFinal);
                	$('#txtNMAquina').val(a.txtNMAquina);
                	$('#txtTemperatura').val(a.txtTemperatura);
                	$('#txtPesoFinal2').val(a.txtPesoFinal2);
                	$('#txtAreaMembranaFiltro').val(a.txtAreaMembranaFiltro);
                	$('#txtValoracionEnfermeria').val(a.txtValoracionEnfermeria);                	
                	$('#txtObservacionFinal').val(a.txtObservacionFinal);
                	$('#txtAspectoFiltro').val(a.txtAspectoFiltro);                	
                	$('#txtMuestraAnalisis').val(a.txtMuestraAnalisis);
                	$('#txtHoraEvaluacionPrevia').val(a.txtHoraEvaluacionPrevia);
                	$('#cadenacies').val(a.txtCies);
                	$('#labelconvenio').text(a.convenio);
                	$('#romano').val(a.romano);

                	//inicializo la Tabla de Cies
                	inicializarTablaCies(a.txtCies);

                	//Inicializo Administración de Medicamentos
                	inicializarTablaMedicamentos(a.txtAdmiMedic);

                	//incializo tabla Evaluación de Hemodiálisis
                	inicializarTablaEvaluacion(a.txtEvalHemodialisis);

					$(this).html('<i class="fa fa-check fa-lg"></i>Llamar Paciente');
					$('.btnLlamarPaciente2').removeAttr('disabled');
					$('.requerido2').removeClass('requerido2');
					$('#btnNo').prop('disabled', true);
					$('#btnSi').prop('disabled', true);

					calculartxtUltrafiltrado();
					$("#txtAdmiMedic11").val("1");

					$("#btnCerradoEspecial").prop("disabled", false);
					cambiarEstadoIP(historia_id, cid);
		        },
				error: function() {
					$("#btnCerradoEspecial").prop("disabled", false);
					alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
			    }
		    });
	    }
    });

	function inicializarTablaCies(cies) {
		$.ajax({
			url: "historiaclinica/inicializarTablaCies",
			data: {cies: cies},
			beforeSend: function() {
				$("#detallecie").html('<tr><td colspan="2">Cargando...</td></tr>');
			}, 
		})
		.done(function(a) {
			$("#detallecie").html(a);
		});    	
	}

	function inicializarTablaMedicamentos(medicamentos) {
		if(medicamentos !== null) {
			var medicamentos1 = medicamentos.split('&iliu&');
			for (var i = 0; i <= medicamentos1.length-1; i++) {
				var a = i+1;
				var medicamentos2 = medicamentos1[i].split('&ilid&');
				for (var j = 0; j <= medicamentos2.length-1; j++) {
					var b = j+1;
					if(b === 1 && a !== 1 && a !== 2 && a !== 3) {
						$('#txtAdmiMedic'+a+b+'s').val(nomMedicamento(medicamentos2[j]));
					}
					$('#txtAdmiMedic'+a+b).val(medicamentos2[j]);
				}			
			}
		}			
	}

	function nomMedicamento(id) {
		return JSON.parse($.ajax({
	        url: 'historiaclinica/nomMedicamento?id='+id,
	        type: 'GET',
	        async: false,
	        dataType: 'json',
	        success: function(result) {
	            return result;
	            $("#txtAdmiMedic11").val("1");
	        }
	    }).responseText);
	}

	function inicializarTablaEvaluacion(evaluacion) {
		if(evaluacion !== null) {
			var evaluacion1 = evaluacion.split('&iliu&');
			for (var i = 0; i <= evaluacion1.length-1; i++) {
				var a = i+1;
				var evaluacion2 = evaluacion1[i].split('&ilid&');
				for (var j = 0; j <= evaluacion2.length-1; j++) {
					var b = j+1;
					$('#txtEvalHemodialisis'+a+b).val(evaluacion2[j]);
				}			
			}
		}
	}

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
	$("#txtAdmiMedic41s").typeahead(null,{
		displayKey: 'value',
		source: p1.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$("#txtAdmiMedic41s").val(datum.value);
		$("#txtAdmiMedic41").val(datum.id);
	});

	var p2 = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		limit: 5,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'producto/productoAutocompletar/%QUERY',
			filter: function (p2) {
				return $.map(p2, function (p_2) {
					return {
						value: p_2.value,
						id: p_2.id,
					};
				});
			}
		}
	});
	p2.initialize();
	$("#txtAdmiMedic51s").typeahead(null,{
		displayKey: 'value',
		source: p2.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$("#txtAdmiMedic51s").val(datum.value);
		$("#txtAdmiMedic51").val(datum.id);
	});

	var p3 = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		limit: 5,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'producto/productoAutocompletar/%QUERY',
			filter: function (p3) {
				return $.map(p3, function (p_3) {
					return {
						value: p_3.value,
						id: p_3.id,
					};
				});
			}
		}
	});
	p3.initialize();
	$("#txtAdmiMedic61s").typeahead(null,{
		displayKey: 'value',
		source: p3.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$("#txtAdmiMedic61s").val(datum.value);
		$("#txtAdmiMedic61").val(datum.id);
	});

	function reporteformatoo(id){
	    window.open("historia/reporteformato?id="+id+"&formatomensual=2&formatotipo=0");
	}

	function cerradoEspecial() {
		var id = $("#historia_id").val();
		$.ajax({
			url: 'historiaclinica/cerradoEspecial?hid=' + id,			
			success: function() {
				$("#modalCerradoEspecial").modal("hide");
				alertaB("Atención cancelada Correctamente.");
				$("li").removeClass('in active');
				$('#cola').addClass('in active');
				$('#atencion').removeClass('in active');
  				$("#pestanaAtencion").css('display', 'none').removeClass('active');
  				$("#pestanaPacienteCola").addClass('active');
				$("#divpresente").css('display','');
				$("#btnGuardarT").prop('disabled', true).html('<i class="glyphicon glyphicon-check"></i> Guardado Temporal');
				$('.requerido').val('');
				$('.cuadrin').val('');
				$('#historia_id').val('');
				tablaAtendidos();
			},
		}).error(function() {
			$("#btnGuardarT").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Guardado Temporal');
			alertaG("No se pudo cancelar la Atención.");
		});	
	}

	function comprobarMedico() {
		var id = $("#historia_id").val();
		$.ajax({
			url: 'historiaclinica/comprobarMedico?id=' + id,
			dataType: "JSON",		
			success: function(a) {
				$('#inputFaltaMedico').val(a.nombremedico);
				$('#idInputFaltaMedico').val(a.idmedico);
			},
		});
	}

	function faltaMedico(v) {
		if(v == "2"){ $("#faltaMedico").removeAttr("style"); }
		else { $("#faltaMedico").attr("style", "display:none"); }
	}
	
</script>
@endif
@endif
