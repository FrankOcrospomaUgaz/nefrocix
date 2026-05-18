<?php
date_default_timezone_set('America/Lima');
$hoy = date("Y-m-d");
$hora = date("H:m");
if($historia->fechaformatoenfermeria!==NULL&&$historia->fechaformatoenfermeria!=="") {
	$hoy = date("Y-m-d", strtotime($historia->fechaformatoenfermeria));
	$hora = date("H:m", strtotime($historia->fechaformatoenfermeria));
}
?>
<style>
	input, select, textarea, p {
	  	filter: drop-shadow(1px 1px 1px #333);
	}
	input, select, textarea {
	  	text-transform:uppercase;
	}
	.cabeza {
		color: blue;
		font-weight: bold;
		text-align: left;
	}	
	.requerido2 { 
		border: 1px solid #f00; 
		background-color: #FFD6CE;
		color: red;
	}
</style>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($historia, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
    {!! Form::hidden('modo', $modo, array('id' => 'modo')) !!}
    {!! Form::hidden('id', $id, array('id' => 'id')) !!}
    <div class="row" spellcheck="false">
    	<div class="col-lg-12 col-md-12 col-sm-12">
    		<ul class="nav nav-tabs">
			  <li class="active"><a data-toggle="tab" href="#RegistroGeneral">Datos Generales</a></li>
			  <li><a data-toggle="tab" href="#Antecedentes">Antecedentes</a></li>
			  <li><a data-toggle="tab" href="#SaludActual">Estado de Salud Actual</a></li>
			  <li><a data-toggle="tab" href="#AccesoVascular">Acceso Vascular</a></li>
			</ul>
			<div class="tab-content">
				<div id="RegistroGeneral" class="tab-pane fade in active">
					<!-- Main content -->
					<section class="content">
						<div class="form-group">
							<div class="col-lg-6 col-md-6 col-sm-6">
							    <div class="form-group">
									{!! Form::label('nombres', 'Nombres y Apellidos:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::text('nombres', $historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres, array('class' => 'form-control input-sm', 'id' => 'nombres', "readonly")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('edad', 'Edad:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::text('edad', $edad." años", array('class' => 'form-control input-sm', 'id' => 'edad', "readonly")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('direccion', 'Direccion:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::text('direccion', $historia->persona->direccion, array('class' => 'form-control input-sm', 'id' => 'direccion', "readonly")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('telefono', 'Telef. 1:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('telefono', $historia->persona->telefono, array('class' => 'form-control input-sm', 'id' => 'telefono', "readonly")) !!}
									</div>
							        {!! Form::label('telefono2', 'Telef. 2:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('telefono2', $historia->persona->telefono2, array('class' => 'form-control input-sm', 'id' => 'telefono2', "readonly")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('estadocivil', 'Estado Civil:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::text('estadocivil', $historia->estadocivil, array('class' => 'form-control input-sm', 'id' => 'estadocivil', "readonly")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('fechaformatoenfermeria', 'Fecha:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
									<div class="col-lg-4 col-md-4 col-sm-4">
										{!! Form::date('fechaformatoenfermeria', $hoy, array('class' => 'form-control input-sm', 'id' => 'fechaformatoenfermeria')) !!}
									</div>
									{!! Form::label('horaformatoenfermeria', 'Hora:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
									<div class="col-lg-4 col-md-4 col-sm-4">
										{!! Form::time('horaformatoenfermeria', $hora, array('class' => 'form-control input-sm', 'id' => 'horaformatoenfermeria')) !!}
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6">
								<div class="form-group">
									{!! Form::label('ocupacion', 'Prefesión u ocupación:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::text('ocupacion', $historia->ocupacion, array('class' => 'form-control input-sm', 'id' => 'ocupacion')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('gradoinstruccion', 'Grado de Instrucción:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::text('gradoinstruccion', $historia->gradoinstruccion, array('class' => 'form-control input-sm', 'id' => 'gradoinstruccion', "readonly")) !!}
									</div>
								</div>
							    <div class="form-group">
									{!! Form::label('nombres2', 'Familiar Responsable:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::text('nombres2', $historia->persona2->apellidopaterno . " " . $historia->persona2->apellidomaterno . " " . $historia->persona2->nombres, array('class' => 'form-control input-sm', 'id' => 'nombres2', "readonly")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('telefono21', 'Telef. 1:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('telefono21', $historia->persona2->telefono, array('class' => 'form-control input-sm', 'id' => 'telefono21', "readonly")) !!}
									</div>
							        {!! Form::label('telefono22', 'Telef. 2:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('telefono22', $historia->persona2->telefono2, array('class' => 'form-control input-sm', 'id' => 'telefono22', "readonly")) !!}
									</div>
								</div>
								<div class="form-group">
								{!! Form::label('direccion2', 'Direccion:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
								<div class="col-lg-8 col-md-8 col-sm-8">
									{!! Form::text('direccion2', $historia->persona2->direccion, array('class' => 'form-control input-sm', 'id' => 'direccion2', "readonly")) !!}
								</div>
								</div>	
							</div>	
						</div>		
					</section>
				</div>
				<div id="Antecedentes" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="form-group">
							<div class="col-lg-6 col-md-6 col-sm-6">
							    <div class="form-group">
									{!! Form::label('antecedentesfamiliares', 'Familiares:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::textarea('antecedentesfamiliares', $historia->antecedentesfamiliares, array('class' => 'form-control input-sm', 'id' => 'antecedentesfamiliares', "rows" => "6")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('antecedentesmedicamentos', 'Medicamentos:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::textarea('antecedentesmedicamentos', $historia->antecedentesmedicamentos, array('class' => 'form-control input-sm', 'id' => 'antecedentesmedicamentos', "rows" => "6")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('antecedentesfarma', 'Tratamiento Farmacológico Actual:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::textarea('antecedentesfarma', $historia->antecedentesfarma, array('class' => 'form-control input-sm', 'id' => 'antecedentesfarma', "rows" => "6")) !!}
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6">
							    <div class="form-group">
									{!! Form::label('antecedentestransfusiones', 'Transfusiones Sanguíneas (fechas actuales, en un período no mayor a 6 meses):', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::textarea('antecedentestransfusiones', $historia->antecedentestransfusiones, array('class' => 'form-control input-sm', 'id' => 'antecedentestransfusiones', "rows" => "7")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('antecedenteshospital', 'Hospitalizaciones y Operaciones (fechas actuales, en un período no mayor a 6 meses y establecimiento de salud donde se hospitalizó):', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-8 col-md-8 col-sm-8">
										{!! Form::textarea('antecedenteshospital', $historia->antecedenteshospital, array('class' => 'form-control input-sm', 'id' => 'antecedenteshospital', "rows" => "7")) !!}
									</div>
								</div>
							</div>	
						</div>		
					</section>
				</div>
				<div id="SaludActual" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="form-group">
							<div class="col-lg-4 col-md-4 col-sm-4">
								<div class="form-group">
									<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Funciones Vitales</h5>
								</div>
								<div class="form-group">
									{!! Form::label('txtPresionArterial1', 'Presión Arterial', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('txtPresionArterial1', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtPresionArterial1')) !!}
									</div>
									{!! Form::label('txtPresionArterial2', '/', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('txtPresionArterial2', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtPresionArterial2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtFC', 'Frecuencia Cardíaca', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('txtFC', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtFC')) !!}
									</div>
									{!! Form::label('txtFC', 'latidos por minuto', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label', 'style'=>'text-align:left;')) !!}
								</div>
								<div class="form-group">
									{!! Form::label('txtFR', 'Frecuencia Respiratoria', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('txtFR', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtFR')) !!}
									</div>
									{!! Form::label('txtFR', 'resp. por minuto', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label', 'style'=>'text-align:left;')) !!}
								</div>	
								<div class="form-group">
									{!! Form::label('txtTemperatura', 'Temperatura:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::text('txtTemperatura', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'txtTemperatura')) !!}
									</div>
									{!! Form::label('txtTemperatura', '°C', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label', 'style'=>'text-align:left;')) !!}
								</div>										
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4">
								<div class="form-group">
									<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serología</h5>
								</div>
								<div class="form-group">
									{!! Form::label('txtacbe2', 'AcHBe', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::text('txtacbe2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtacbe2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtaghbs2', 'AgHBs', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::text('txtaghbs2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtaghbs2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtachcv', 'AcHCV', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::text('txtachcv', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtachcv')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtvih', 'VIH', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::text('txtvih', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtvih')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtvdrl', 'VDRL', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::text('txtvdrl', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtvdrl')) !!}
									</div>
								</div>										
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4">
								<div class="form-group">
									<h5 style="font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inmunizaciones Hepatitis B</h5>
								</div>	
								<div class="form-group">
									{!! Form::label('txtFechaCantDosis1', '1° Dosis', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::date('txtFechaCantDosis1', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaCantDosis1')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtFechaCantDosis2', '2° Dosis', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::date('txtFechaCantDosis2', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaCantDosis2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtFechaCantDosis3', '3° Dosis', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::date('txtFechaCantDosis3', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaCantDosis3')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtFechaCantDosis4', 'Refuerzo', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::date('txtFechaCantDosis4', null, array('class' => 'form-control input-sm', 'id' => 'txtFechaCantDosis4')) !!}
									</div>
								</div>									
							</div>
						</div>
					</section>
				</div>
				<div id="AccesoVascular" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="form-group">
							<div class="col-lg-6 col-md-6 col-sm-6">
								<div class="form-group">
									{!! Form::label('txttipoaccesovascular2', 'Tipo de Acceso Vascular', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::select('txttipoaccesovascular2', array("FÍSTULA ARTERIOVENOSA"=>"FÍSTULA ARTERIOVENOSA", "INJERTO"=>"INJERTO", "CATÉTER VENOSO CENTRAL TEMPORAL"=>"CATÉTER VENOSO CENTRAL TEMPORAL", "CATÉTER VENOSO CENTRAL PERMANENTE"=>"CATÉTER VENOSO CENTRAL PERMANENTE"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txttipoaccesovascular2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtubicacionaccesovascular2', 'Ubicación', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::text('txtubicacionaccesovascular2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtubicacionaccesovascular2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtfechainicioaccesovascular2', 'Fecha de inicio de A. V.', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::date('txtfechainicioaccesovascular2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtfechainicioaccesovascular2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtpresenciathrill2', 'Presencia de Thrill', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::text('txtpresenciathrill2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtpresenciathrill2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtpresenciasoplo2', 'Presencia de Soplo', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::text('txtpresenciasoplo2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtpresenciasoplo2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtpresenciapseudo2', 'Presencia de Pseudoaneurisma', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::text('txtpresenciapseudo2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtpresenciapseudo2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtcondicioneshigiene2', 'Condiciones de Higiene', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
									<div class="col-lg-7 col-md-7 col-sm-7">
										{!! Form::text('txtcondicioneshigiene2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtcondicioneshigiene2')) !!}
									</div>
								</div>
								<hr>
								<div class="form-group">
									{!! Form::label('txtformulacion2', 'Formulación de los cuidados del acceso vascular que presenta en ese momento', array('class' => 'col-lg-12 col-md-12 col-sm-12')) !!}
								</div>
								<div class="form-group">
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::textarea('txtformulacion2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtformulacion2', "rows"=>"3")) !!}
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::textarea('txtformulacion22', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtformulacion22', "rows"=>"3")) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtplaneacion2', 'Planeación de acciones de enfermería', array('class' => 'col-lg-12 col-md-12 col-sm-12')) !!}
								</div>
								<div class="form-group">
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::textarea('txtplaneacion2', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtplaneacion2', "rows"=>"3")) !!}
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6">
										{!! Form::textarea('txtplaneacion22', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtplaneacion22', "rows"=>"3")) !!}
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6">
								<div class="form-group">
									{!! Form::label('txtidentifica2', 'Conocimiento de signos y síntomas de riesgo a pérdida y/o infección de acceso vascular:', array('class' => 'col-lg-12 col-md-12 col-sm-12 cabeza')) !!}
								</div>
								<div class="form-group">
									{!! Form::label('txtidentifica2', '** a) Identifica disminución o ausencia de thrill en fístula arteriovenoso:', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtidentifica2', array("SI"=>"SI", "NO"=>"NO"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtidentifica2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtidentifica22', '** b) Identifica presencia de prurito y dolor en zona de acceso vascular:', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtidentifica22', array("SI"=>"SI", "NO"=>"NO"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtidentifica22')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtconocimiento2', 'Conocimiento de acciones ante una ruptura accidental de fístula arteriovenosa o una migración o ruptura de catéter venoso central:', array('class' => 'col-lg-9 col-md-9 col-sm-9 cabeza')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtconocimiento2', array("CONOCE"=>"CONOCE", "DESCONOCE"=>"DESCONOCE"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtconocimiento2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtconocimiento22', 'Conocimiento de signos y síntomas ante una emergencia dialítica:', array('class' => 'col-lg-12 col-md-12 col-sm-12 cabeza')) !!}
								</div>
								<div class="form-group">
									{!! Form::label('txtconocimiento22', '** a) Hiperkalemia:', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtconocimiento22', array("SI"=>"SI", "NO"=>"NO"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtconocimiento22')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtedema2', '** b) Edema agudo del pulmón:', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtedema2', array("SI"=>"SI", "NO"=>"NO"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtedema2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtconocimiento23', 'Conocimiento y cumplimiento de la medición:', array('class' => 'col-lg-12 col-md-12 col-sm-12 cabeza')) !!}
								</div>
								<div class="form-group">
									{!! Form::label('txtconocimiento23', '° Menciona horarios de administración de medicamentos:', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtconocimiento23', array("SI"=>"SI", "NO"=>"NO"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtconocimiento23')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtmedicamentos2', '° Menciona medicamentos de mayor consumo:', array('class' => 'col-lg-9 col-md-9 col-sm-9 control-label')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtmedicamentos2', array("SI"=>"SI", "NO"=>"NO"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtmedicamentos2')) !!}
									</div>
								</div>
								<div class="form-group">
									{!! Form::label('txtconocimiento24', 'Conocimiento de los cuidados del acceso vascular que presenta en ese momento:', array('class' => 'col-lg-9 col-md-9 col-sm-9 cabeza')) !!}
									<div class="col-lg-3 col-md-3 col-sm-3">
										{!! Form::select('txtconocimiento24', array("SI"=>"SI", "NO"=>"NO"), null, array('class' => 'form-control input-sm requerido', 'id' => 'txtconocimiento24')) !!}
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
					</section>
				</div>
			</div>
    	</div>
    </div>
		    
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1200');
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').inputmask("99999999");
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni2"]').inputmask("99999999");
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').focus();    
    $('.tdMensaje').css('font-weight', 'bold');   
    $('.modal-header').append('<button type="button" class="close closdat" data-dismiss="modal" aria-hidden="true" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
    $("input").attr("placeholder", "ESCRIBA AQUÍ");
    $(".numerin").inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 0 });
    $(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
}); 

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
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

function guardarHistoria (entidad, idboton) {
	if(!validarInputs()) {
		alertaG('Corrige los campos en rojo y vuelve a enviar.');
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
			}else{
			  //alert(respuesta);
	            var dat = JSON.parse(respuesta);
				if (dat[0]!==undefined && (dat[0].respuesta=== 'OK')) {
					cerrarModal();
	                alertaB("FORMATO DE ENFERMERÍA REGISTRADO CORRECTAMENTE");
	                if(dat[0].id!==undefined){
	                	window.open("historia/reporteHistoriaEnfermeria?id="+dat[0].id,"_blank");
	                }                
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
				}
				buscar('Historia');
			}
		});
	}
}
setInterval(quitarPadding, 4000);
</script>