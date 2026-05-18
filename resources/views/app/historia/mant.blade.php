<?php
date_default_timezone_set('America/Lima');
if(is_null($historia)){
	$id=null;
	$numero=null;
	$convenio=null;
	$regimen=null;
	$nacionalidad='Peruano';
	$gradoinstruccion=null;
	$gruposanguineo=null;
	$antecedentes2=null;
	$provincia=null;
	$distrito=null;
	$familiar=null;
	$ipress=null;
	$fechainicio=date('Y-m-d');
	$horacita=strftime("%H:%M");
	$ordencitas=null;
	$accesovascular=null;
	$dni2=null;
	$nacionalidad2='Peruano';
	$raza=null;
	$nombres2=null;
	$apellidomaterno2=null;
	$apellidopaterno2=null;
	$direccion2=null;
	$telefono22=null;
	$telefono21=null;
	$direccion2=null;
	$email=null;
}else{
	$id=$historia->id;
	$numero=$historia->numero;
	$convenio=$historia->convenio_id;
	$regimen=$historia->regimen;
	$gradoinstruccion=$historia->gradoinstruccion;
	$gruposanguineo=$historia->gruposanguineo;
	$antecedentes2=$historia->antecedentes2;
	$provincia=$historia->provincia;
	$distrito=$historia->distrito;
	$familiar=$historia->familiar;
	$ipress=$historia->ipress;
	$fechainicio=$historia->fechainicio;
	$horacita=$historia->horacita;
	$ordencitas=$historia->ordencitas;
	$accesovascular=$historia->txtTipoAccesoInicio;
	$email=$historia->email;
	$raza=$historia->persona->raza;
	$nacionalidad=$historia->persona->nacionalidad;

	$dni2=$historia->persona2->dni;
	$nombres2=$historia->persona2->nombres;
	$apellidomaterno2=$historia->persona2->apellidomaterno;
	$apellidopaterno2=$historia->persona2->apellidopaterno;
	$direccion2=$historia->persona2->direccion;
	$telefono22=$historia->persona2->telefono2;
	$telefono21=$historia->persona2->telefono;
	$nacionalidad2=$historia->persona2->nacionalidad;
}
$cboNacionalidad = [
	'Peruano'=>'Peruano',
	'Extranjero'=>'Extranjero'
];
?>
<style>
	input, select, textarea, p {
	  	filter: drop-shadow(1px 1px 1px #333);
	}
	input, select, textarea {
	  	text-transform:uppercase;
	}
</style>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($historia, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
    {!! Form::hidden('modo', $modo, array('id' => 'modo')) !!}
    {!! Form::hidden('id', $id, array('id' => 'id')) !!}
    {!! Form::hidden('checktotal', $ordencitas, array('id' => 'checktotal')) !!}
    {!! Form::hidden('checktotal2', $ordencitas, array('id' => 'checktotal2')) !!}
    <div class="row" spellcheck="false">
    	<div class="col-lg-7 col-md-7 col-sm-7">
    		<ul class="nav nav-tabs">
			  <li class="active"><a data-toggle="tab" href="#RegistroGeneral">Datos Personales</a></li>
			  <li><a data-toggle="tab" href="#ConfigCitas">Configuración de Citas</a></li>
			</ul>
			<div class="tab-content">
				<div id="RegistroGeneral" class="tab-pane fade in active">
					<!-- Main content -->
					<section class="content">
						<div class="form-group">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<h5 style="color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Información de Paciente</h5>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('numero', 'Historia:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('numero', $numero, array('class' => 'form-control input-sm', 'id' => 'numero', 'readonly' => 'readonly')) !!}
							</div>
							{!! Form::label('tipopaciente', 'Tipo Pac.:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('tipopaciente', $cboTipoPaciente, null, array('class' => 'form-control input-sm', 'id' => 'tipopaciente','onchange' =>'mostrarConvenio(this.value)')) !!}
							</div>
						</div>
					    <div class="form-group" data="divConvenio">
					    	{!! Form::label('convenio', 'Convenio:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('convenio', $cboConvenio, $convenio, array('class' => 'form-control input-sm', 'id' => 'convenio')) !!}
							</div>
							{!! Form::label('regimen', 'Régimen:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('regimen', $cboRegimen, $regimen, array('class' => 'form-control input-sm', 'id' => 'regimen')) !!}
							</div>
						</div>
						<div class="form-group" data="divConvenio">
							{!! Form::label('carnet', 'Cod. Asegurado:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('carnet', null, array('class' => 'form-control input-sm', 'id' => 'carnet')) !!}
							</div>
							{!! Form::label('plan_susalud', 'N°Plan:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('plan_susalud', null, array('class' => 'form-control input-sm', 'id' => 'plan_susalud')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('nacionalidad', 'Nacionalidad:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('nacionalidad', $cboNacionalidad, $nacionalidad, array('class' => 'form-control input-sm', 'id' => 'nacionalidad')) !!}
							</div>
							{!! Form::label('dni', ($nacionalidad=="Peruano"?'DNI:':'Carnet Extr.'), array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label labelnacionalidad')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('dni', null, array('class' => 'form-control input-sm', 'id' => 'dni', 'placeholder' => 'Ingrese dni', 'onkeyup'=>'validarDNI(this.value)')) !!}
							</div>							
						</div>
					    <div class="form-group">
							{!! Form::label('apellidopaterno', 'Ap. Paterno:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('apellidopaterno', null, array('class' => 'form-control input-sm', 'id' => 'apellidopaterno', 'placeholder' => 'Ingrese Apellido Paterno')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('apellidomaterno', 'Ap. Materno:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('apellidomaterno', null, array('class' => 'form-control input-sm', 'id' => 'apellidomaterno', 'placeholder' => 'Ingrese Apellido Materno')) !!}
							</div>
						</div>
					    <div class="form-group">
							{!! Form::label('nombres', 'Nombres:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('nombres', null, array('class' => 'form-control input-sm', 'id' => 'nombres', 'placeholder' => 'Ingrese nombres')) !!}
							</div>
						</div>
					    <div class="form-group">
							{!! Form::label('categoria', 'Categoria:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('categoria', $cboCategoria, null, array('class' => 'form-control input-sm', 'id' => 'categoria')) !!}
							</div>
							{!! Form::label('detallecategoria', 'Detalle:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('detallecategoria', null, array('class' => 'form-control input-sm', 'id' => 'detallecategoria', 'placeholder' => 'Ingrese detalle')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('telefono', 'Telef. 1:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('telefono', null, array('class' => 'form-control input-sm', 'id' => 'telefono', 'placeholder' => 'Ingrese telefono')) !!}
							</div>
					        {!! Form::label('telefono2', 'Telef. 2:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('telefono2', null, array('class' => 'form-control input-sm', 'id' => 'telefono2', 'placeholder' => 'Ingrese telefono')) !!}
							</div>
						</div>
					    <div class="form-group">
							{!! Form::label('departamento', 'Departamento:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('departamento', $cboDepa, null, array('class' => 'form-control input-sm', 'id' => 'departamento')) !!}
							</div>
							{!! Form::label('provincia', 'Provincia:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('provincia', array('Elija Departamento'), null, array('class' => 'form-control input-sm', 'id' => 'provincia')) !!}
							</div>
						</div>
					    <div class="form-group">
					    	{!! Form::label('distrito', 'Distrito:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('distrito', array('Elija Provincia'), null, array('class' => 'form-control input-sm', 'id' => 'distrito')) !!}
							</div>
							{!! Form::label('raza', 'Raza:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('raza', array("caucásico"=>"caucásico", "africano"=>"africano", "oriental"=>"oriental", "latino"=>"latino"), $raza, array('class' => 'form-control input-sm', 'id' => 'raza')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('direccion', 'Direccion:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('direccion', null, array('class' => 'form-control input-sm', 'id' => 'direccion', 'placeholder' => 'Ingrese direccion')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('gradoinstruccion', 'Grado de Instrucción:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('gradoinstruccion', $cboGrado, $gradoinstruccion, array('class' => 'form-control input-sm', 'id' => 'gradoinstruccion')) !!}
							</div>
							{!! Form::label('grupos', 'Grupo Sang:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('grupos', $cboGrupo, $gruposanguineo, array('class' => 'form-control input-sm', 'id' => 'grupos')) !!}
							</div>
						</div>
					    <div class="form-group">
							{!! Form::label('fechanacimiento', 'Fecha Nac.:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::date('fechanacimiento', null, array('class' => 'form-control input-sm', 'id' => 'fechanacimiento')) !!}
							</div>
							{!! Form::label('sexo', 'Sexo:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('sexo', $cboSexo, null, array('class' => 'form-control input-sm', 'id' => 'sexo')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('ipress', 'IPRESS Proc.:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('ipress', $ipress, array('class' => 'form-control input-sm', 'id' => 'ipress', 'placeholder' => 'Ingrese ipress responsable')) !!}
							</div>
							{!! Form::label('accesovascular', 'Acceso Vascular:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('accesovascular', $cboAccesoV, $accesovascular, array('class' => 'form-control input-sm', 'id' => 'accesovascular')) !!}
							</div>
						</div>
					    <div class="form-group">
							{!! Form::label('estadocivil', 'Estado Civil:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::select('estadocivil', $cboEstadoCivil, null, array('class' => 'form-control input-sm', 'id' => 'estadocivil')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('emailh', 'Email:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('emailh', $email, array('class' => 'form-control input-sm', 'id' => 'emailh', 'placeholder' => 'Ingresa un e-mail')) !!}
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<h5 style="color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Información de Familiar</h5>
							</div>
						</div>
						{{-- INFO DEL FAMILIAR --}}
						<div class="form-group">
							{!! Form::label('nacionalidad2', 'Nacionalidad:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::select('nacionalidad2', $cboNacionalidad, $nacionalidad2, array('class' => 'form-control input-sm', 'id' => 'nacionalidad2')) !!}
							</div>
							{!! Form::label('dni2', ($nacionalidad2=="Peruano"?'DNI:':'Carnet Extr.'), array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label labelnacionalidad2')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('dni2', $dni2, array('class' => 'form-control input-sm', 'id' => 'dni2', 'placeholder' => 'Ingrese dni', 'onkeyup'=>'validarDNI2(this.value)')) !!}
							</div>
						</div>
					    <div class="form-group">
							{!! Form::label('apellidopaterno2', 'Ap. Paterno:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('apellidopaterno2', $apellidopaterno2, array('class' => 'form-control input-sm', 'id' => 'apellidopaterno2', 'placeholder' => 'Ingrese Apellido Paterno')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('apellidomaterno2', 'Ap. Materno:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('apellidomaterno2', $apellidomaterno2, array('class' => 'form-control input-sm', 'id' => 'apellidomaterno2', 'placeholder' => 'Ingrese Apellido Materno')) !!}
							</div>
						</div>
					    <div class="form-group">
							{!! Form::label('nombres2', 'Nombres:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('nombres2', $nombres2, array('class' => 'form-control input-sm', 'id' => 'nombres2', 'placeholder' => 'Ingrese nombres')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('telefono21', 'Telef. 1:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('telefono21', $telefono21, array('class' => 'form-control input-sm', 'id' => 'telefono21', 'placeholder' => 'Ingrese telefono')) !!}
							</div>
					        {!! Form::label('telefono22', 'Telef. 2:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::text('telefono22', $telefono22, array('class' => 'form-control input-sm', 'id' => 'telefono22', 'placeholder' => 'Ingrese telefono')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('direccion2', 'Direccion:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
							<div class="col-lg-8 col-md-8 col-sm-8">
								{!! Form::text('direccion2', $direccion2, array('class' => 'form-control input-sm', 'id' => 'direccion2', 'placeholder' => 'Ingrese direccion')) !!}
							</div>
						</div>

						{{-- FIN DE INFO DEL FAMILIAR --}}						
					</section>
					<!-- /.content -->	
				</div>
				<div id="ConfigCitas" class="tab-pane fade">
					<section class="content">
	                    <div class="form-group">
							{!! Form::label('fechainicio', 'Fecha de Inicio de Citas:', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::date('fechainicio', $fechainicio, array('onkeyup' => 'cargarCantidadEquipos();', 'class' => 'form-control input-sm', 'id' => 'fechainicio')) !!}
							</div>
						</div>      
						<div class="form-group">
							{{--{!! Form::label('horacita', 'Hora de Citas:', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label')) !!}
							<div class="col-lg-3 col-md-3 col-sm-3">
								{!! Form::time('horacita', $horacita, array('class' => 'form-control input-sm', 'id' => 'horacita')) !!}
							</div>--}}
							{!! Form::label('horacita', 'Turno de Citas:', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label')) !!}
							<div class="col-lg-4 col-md-4 col-sm-4">
								{!! Form::select('horacita', $cboTurno, $horacita, array('onchange' => 'cargarCantidadEquipos();', 'class' => 'form-control input-sm', 'id' => 'horacita')) !!}
							</div>
						</div>                   
	                    <div class="table-responsive">
	                        <table id="example1" class="table table-bordered table-striped table-condensed table-hover">
	                            <thead>
	                                <tr>
	                                    <th class="text-center" width="10%">Oficial</th>
	                                    <th class="text-center" width="10%">Opcional</th>
	                                    <th class="text-center" width="10%">Nro.</th>
	                                    <th class="text-center" width="30%">Día</th>
	                                    <th class="text-center" width="20%">Equipos Disponibles</th>
	                                    <th class="text-center" width="20%">Equipos Ocupados</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                                <tr>
	                                    <td class="text-center"><input onchange="dispNoDisp();" onclick="reducirCantEquipos('Lunes', this);" disabled="" class="checkParcial 1" value="1" type="checkbox"></td>
	                                    <td class="text-center"><input onchange="dispNoDisp();" class="checkParcial2 11" value="1" type="checkbox"></td>
	                                    <td class="text-center">1</td>
	                                    <td>LUNES</td>
	                                    <td class="tdMensaje text-center" id="dispLunes"></td>
	                                    <td class="tdMensaje text-center" id="ocpLunes"></td>
	                                </tr>
	                                <tr>
	                                    <td class="text-center"><input onchange="dispNoDisp();" onclick="reducirCantEquipos('Martes', this);" disabled="" class="checkParcial 2" value="2" type="checkbox"></td>
	                                    <td class="text-center"><input onchange="dispNoDisp();" class="checkParcial2 22" value="2" type="checkbox"></td>
	                                    <td class="text-center">2</td>
	                                    <td>MARTES</td>
	                                    <td class="tdMensaje text-center" id="dispMartes"></td>
	                                    <td class="tdMensaje text-center" id="ocpMartes"></td>
	                                </tr>
	                                <tr>
	                                    <td class="text-center"><input onchange="dispNoDisp();" onclick="reducirCantEquipos('Miercoles', this);" disabled="" class="checkParcial 3" value="3" type="checkbox"></td>
	                                    <td class="text-center"><input onchange="dispNoDisp();" class="checkParcial2 33" value="3" type="checkbox"></td>
	                                    <td class="text-center">3</td>
	                                    <td>MIÉRCOLES</td>
	                                    <td class="tdMensaje text-center" id="dispMiercoles"></td>
	                                    <td class="tdMensaje text-center" id="ocpMiercoles"></td>
	                                </tr>
	                                <tr>
	                                    <td class="text-center"><input onchange="dispNoDisp();" onclick="reducirCantEquipos('Jueves', this);" disabled="" class="checkParcial 4" value="4" type="checkbox"></td>
	                                    <td class="text-center"><input onchange="dispNoDisp();" class="checkParcial2 44" value="4" type="checkbox"></td>
	                                    <td class="text-center">4</td>
	                                    <td>JUEVES</td>
	                                    <td class="tdMensaje text-center" id="dispJueves"></td>
	                                    <td class="tdMensaje text-center" id="ocpJueves"></td>
	                                </tr>
	                                <tr>
	                                    <td class="text-center"><input onchange="dispNoDisp();" onclick="reducirCantEquipos('Viernes', this);" disabled="" class="checkParcial 5" value="5" type="checkbox"></td>
	                                    <td class="text-center"><input onchange="dispNoDisp();" class="checkParcial2 55" value="5" type="checkbox"></td>
	                                    <td class="text-center">5</td>
	                                    <td>VIERNES</td>
	                                    <td class="tdMensaje text-center" id="dispViernes"></td>
	                                    <td class="tdMensaje text-center" id="ocpViernes"></td>
	                                </tr>
	                                <tr>
	                                    <td class="text-center"><input onchange="dispNoDisp();" onclick="reducirCantEquipos('Sabado', this);" disabled="" class="checkParcial 6" value="6" type="checkbox"></td>
	                                    <td class="text-center"><input onchange="dispNoDisp();" class="checkParcial2 66" value="6" type="checkbox"></td>
	                                    <td class="text-center">6</td>
	                                    <td>SÁBADO</td>
	                                    <td class="tdMensaje text-center" id="dispSabado"></td>
	                                    <td class="tdMensaje text-center" id="ocpSabado"></td>
	                                </tr>
	                                <tr>
	                                    <td class="text-center"><input onchange="dispNoDisp();" onclick="reducirCantEquipos('Domingo', this);" disabled="" class="checkParcial 7" value="7" type="checkbox"></td>
	                                    <td class="text-center"><input onchange="dispNoDisp();" class="checkParcial2 77" value="7" type="checkbox"></td>
	                                    <td class="text-center">7</td>
	                                    <td>DOMINGO</td>
	                                    <td class="tdMensaje text-center" id="dispDomingo"></td>
	                                    <td class="tdMensaje text-center" id="ocpDomingo"></td>
	                                </tr>
	                            </tbody>
	                        </table>
	                    </div>
		            </section>
				</div>
			</div>
    	</div>
    	<div class="col-lg-5 col-md-5 col-sm-5">
    		<p style="color: red; font-weight: bold; font-size: 30px;" class="text-center">ANTECEDENTES CLÍNICOS:</p>
    		<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::textarea('antecedentesclinicos', $antecedentes2, array('style'=>'height:600px; font-size:15px; font-weight:bold;','class' => 'form-control input-sm', 'id' => 'antecedentesclinicos', 'placeholder'=>'Ingresa los antecedentes clínicos del paciente.')) !!}
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		            @if($modo=="popup")
		                {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarHistoria(\''.$entidad.'\', this)')) !!}
					@else
		                {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarHistoria(\''.$entidad.'\', this)')) !!}
		            @endif
		            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
				</div>
			</div>
    	</div>
    </div>
		    
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1200');
	<?php if($nacionalidad=="Peruano") { ?>
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').inputmask("99999999");
	<?php } else { ?>
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').inputmask("999999999");
	<?php } ?>
	<?php if($nacionalidad2=="Peruano") { ?>
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni2"]').inputmask("99999999");
    <?php } else { ?>
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni2"]').inputmask("999999999");
	<?php } ?>
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').focus();    
    $('.tdMensaje').css('font-weight', 'bold');
    @if(!is_null($historia))
		mostrarConvenio('{{$historia->tipopaciente}}');
		cargarProv();
		cargarDist();
		cargarConfCitas();
		$('.checkParcial').removeAttr('disabled');
	@endif
	cargarCantidadEquipos();
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
}); 

$(document).on('change', '#nacionalidad', function(event) {
	event.preventDefault();
	var nacionalidad = $(this).val();
	$('.labelnacionalidad').html('Carnet Extr.');
	$('#dni').attr('placeholder', 'Ingrese CE');
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').inputmask("999999999");
	if(nacionalidad === 'Peruano') {
        $('.labelnacionalidad').html('DNI');
        $('#dni').attr('placeholder', 'Ingrese DNI');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').inputmask("99999999");
	}
	validarDNI($('#dni').val());
});

$(document).on('change', '#nacionalidad2', function(event) {
	event.preventDefault();
	var nacionalidad = $(this).val();
	$('.labelnacionalidad2').html('Carnet Extr.');
	$('#dni2').attr('placeholder', 'Ingrese CE');
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni2"]').inputmask("999999999");
	if(nacionalidad === 'Peruano') {
        $('.labelnacionalidad2').html('DNI');
        $('#dni2').attr('placeholder', 'Ingrese DNI');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni2"]').inputmask("99999999");
	}
	validarDNI2($('#dni2').val());
});

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
	}
});

function validarFormatoFecha(campo) {
    var RegExPattern = /^\d{2,4}\-\d{1,2}\-\d{1,2}$/;
    if ((campo.match(RegExPattern)) && (campo!='')) {

        return true;
    } else {
        return false;
   }
}

function existeFecha(fecha){
    var fechaf = fecha.split("-");
    var year = fechaf[0];
    var month = fechaf[1];
    var day = fechaf[2];
    var date = new Date(year,month,'0');
    if((day-0)>(date.getDate()-0)){
        return false;
    }
    return true;
}

$('#departamento').change(function(){
	var depa = $('#departamento').val();
	$.ajax({
        type: "GET",
        url: "historia/buscaProv/"+depa,
        success: function(a) {
            $('#provincia').html(a);
        }
    });
});

function cargarCantidadEquipos() {
	var fechainicio = $('#fechainicio').val();

	var horacita = $('#horacita').val();
	if(fechainicio===''||horacita==='') {
		resetearCbx();
		return false;		
	}
	if(validarFormatoFecha(fechainicio)){
	    if(!existeFecha(fechainicio)){
	    	resetearCbx();
	        return false;
	    }
	}else{
		resetearCbx();
	    return false;
	}
	$.ajax({
		url: 'historia/cargarCantidadEquipos',
		type: 'GET',
		dataType: 'JSON',
		data: {fechainicio:fechainicio, horacita:horacita},
	})
	.done(function(a) {
		$('.checkParcial').prop('checked', false);
		//seteamos los inputs
		$('#dispLunes').html(a['cantidadcitaslunes2']);
		$('#ocpLunes').html(a['cantidadcitaslunes']);
		mensajeCheckParcial('Lunes', a['cantidadcitaslunes2'], '1');
		$('#dispMartes').html(a['cantidadcitasmartes2']);
		$('#ocpMartes').html(a['cantidadcitasmartes']);
		mensajeCheckParcial('Martes', a['cantidadcitasmartes2'], '2');
		$('#dispMiercoles').html(a['cantidadcitasmiercoles2']);
		$('#ocpMiercoles').html(a['cantidadcitasmiercoles']);
		mensajeCheckParcial('Miercoles', a['cantidadcitasmiercoles2'], '3');
		$('#dispJueves').html(a['cantidadcitasjueves2']);
		$('#ocpJueves').html(a['cantidadcitasjueves']);
		mensajeCheckParcial('Jueves', a['cantidadcitasjueves2'], '4');
		$('#dispViernes').html(a['cantidadcitasviernes2']);
		$('#ocpViernes').html(a['cantidadcitasviernes']);
		mensajeCheckParcial('Viernes', a['cantidadcitasviernes2'], '5');
		$('#dispSabado').html(a['cantidadcitassabado2']);
		$('#ocpSabado').html(a['cantidadcitassabado']);
		mensajeCheckParcial('Sabado', a['cantidadcitassabado2'], '6');
		$('#dispDomingo').html(a['cantidadcitasdomingo2']);
		$('#ocpDomingo').html(a['cantidadcitasdomingo']);
		mensajeCheckParcial('Domingo', a['cantidadcitasdomingo2'], '7');		
		@if(!is_null($historia))
			if($('#horacita').val()==='{{$horacita}}') {
				habilitarChecksEditar();
			}			
		@endif
		dispNoDisp();
	})
	.fail(function() {
		alertaG('Ocurrió un error al Cargar Equipos.');
		$('.checkParcial').prop('checked', false);
		$('#checktotal').val('');
		$('#checktotal2').val('');
		$('.checkParcial').attr('disabled', true);
		$('.tdMensaje').html('');
	});	
}

function resetearCbx() {
	$('.checkParcial').prop('checked', false);
	$('#checktotal').val('');
	$('#checktotal2').val('');
	$('.checkParcial').attr('disabled', true);
	$('.tdMensaje').html('');
}

function mensajeCheckParcial(value, cantidad, cbxclass) {
	if(cantidad == 0) {
		$('#disp'+value).css('color', 'red');
		$('.'+cbxclass).attr('disabled', true);
	} else {
		$('#disp'+value).css('color', 'green');
		$('.'+cbxclass).removeAttr('disabled');
	}
	dispNoDisp();
}

@if(!is_null($historia))
function habilitarChecksEditar() {
	var cadena = '{{ $historia->ordencitas }}';
	recorrido = cadena.split(';');
	for (var i = 0; i < (recorrido.length-1); i++) {
		$('.'+recorrido[i]).removeAttr('disabled').prop('checked', true);
	}
	@if($historia->ordencitasopcional!==NULL&&$historia->ordencitasopcional!=="")
		var cadena = '{{ $historia->ordencitasopcional }}';
		recorrido = cadena.split(';');
		for (var i = 0; i < (recorrido.length-1); i++) {
			$('.'+recorrido[i]+recorrido[i]).removeAttr('disabled').prop('checked', true);
		}
	@endif
}
@endif

function reducirCantEquipos(dia, cbx) {
	var cantidadCitas = parseInt($('#disp'+dia).html());
	var cantidadCitas2 = parseInt($('#ocp'+dia).html());
	if(cbx.checked) {
		cantidadCitas--;
		cantidadCitas2++;
		if(cantidadCitas === 0) {
			$('#disp'+dia).css('color', 'red');
		}
	} else {
		cantidadCitas++;
		cantidadCitas2--;
		$('#disp'+dia).css('color', 'green');
	}
	$('#disp'+dia).html(cantidadCitas);
	$('#ocp'+dia).html(cantidadCitas2);
}

function dispNoDisp() {
	var ck = '';
	var ck2 = '';
	$('.checkParcial').each(function(index, el) {
    	if(this.checked) {
	        ck += this.value + ';';
	    }
    });
    $('.checkParcial2').each(function(index, el) {
    	if(this.checked&&$('.checkParcial')[index].checked) {
	        ck2 += this.value + ';';
	    }
    });
    $('#checktotal').val(ck);
    $('#checktotal2').val(ck2);
}

function cargarProv(){
	var depa = $('#departamento').val();
	$.ajax({
        type: "GET",
        url: "historia/buscaProv/"+depa,
        success: function(a) {
            $('#provincia').html(a);
            $('#provincia').val('{{ $provincia }}');
        }
    });
}

$('#provincia').change(function(){
	var prov = $('#provincia').val();
	$.ajax({
        type: "GET",
        url: "historia/buscaDist/"+prov,
        success: function(a) {
            $('#distrito').html(a);            
        }
    });
});

function cargarDist(){
	var prov = '{{ $provincia }}';
	$.ajax({
        type: "GET",
        url: "historia/buscaDist/"+prov,
        success: function(a) {
            $('#distrito').html(a);
            $('#distrito').val('{{ $distrito }}');
        }
    });
}

function cargarConfCitas() {
	var citas = '{{ $ordencitas }}';
	@if(!is_null($historia))		
		citas = citas.split(';');
		for (var i = citas.length - 2; i >= 0; i--) {
			$('.'+String(citas[i])).prop('checked', true);
		}
	@endif
	$('#checktotal').val(citas);
}

function mostrarConvenio(idtipopaciente){
    if(idtipopaciente=="Convenio"){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} div[data="divConvenio"]').css("display","");
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} div[data="divConvenio"]').css("display","none");
    }
}

function validarDNI(dni){
    dni = dni.replace("_","");
    cant_ = dni.split('_');
    for (var i = cant_.length - 1; i >= 0; i--) {
    	dni = dni.replace("_","");
    }
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} input[id="numero"]').val(dni);
    var nacionalidad = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} input[id="nacionalidad"]').val();
    var leng = (nacionalidad==="Peruano"?8:9);
    if(dni.length===leng){
        $.ajax({
            type: "POST",
            url: "historia/validarDNI",
            data: "dni="+dni+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                data = JSON.parse(a);
                if(data[0].msg=="S" && data[0].modo=="Registrado"){
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidopaterno"]').val(data[0].apellidopaterno);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidomaterno"]').val(data[0].apellidomaterno);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombres"]').val(data[0].nombres);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono"]').val(data[0].telefono);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val(data[0].direccion);
                }else if(data[0].msg=="N"){
                	a = "El DNI ingresado ya tiene historia";
                	alertaG(a);
                	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} input[id="dni"]').val('');
                	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} input[id="numero"]').val('');
                	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} input[id="dni"]').focus();
                	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidopaterno"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidomaterno"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombres"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val('');
                }
            }
        });
    } else {
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidopaterno"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidomaterno"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombres"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val('');
    }
}
function validarDNI2(dni){
    dni = dni.replace("_","");
    cant_ = dni.split('_');
    for (var i = cant_.length - 1; i >= 0; i--) {
    	dni = dni.replace("_","");
    }
    var nacionalidad = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} input[id="nacionalidad2"]').val();
    var leng = (nacionalidad==="Peruano"?8:9);
    if(dni.length===leng&&dni!==$('#dni').val()){
        $.ajax({
            type: "POST",
            url: "historia/validarDNI2",
            data: "dni="+dni+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {            	
                data = JSON.parse(a);
                if(data[0].msg=="N"){
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidopaterno2"]').val(data[0].apellidopaterno);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidomaterno2"]').val(data[0].apellidomaterno);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombres2"]').val(data[0].nombres);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono2"]').val(data[0].telefono);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono22"]').val(data[0].telefono2);
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion2"]').val(data[0].direccion);
                } else {
                	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidopaterno2"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidomaterno2"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombres2"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono2"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono22"]').val('');
                    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion2"]').val('');
                }
            }
        });
    } else if(dni===$('#dni').val()&&dni!=='') {
    	alertaG('No puede ingresar el mismo DNI');
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni2"]').val('');
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidopaterno2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidomaterno2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombres2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono22"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion2"]').val('');
    } else if(dni.length!==leng){
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidopaterno2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="apellidomaterno2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombres2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono2"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="telefono22"]').val('');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion2"]').val('');
    }
}
function guardarHistoria (entidad, idboton) {
	if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val()=='Convenio' && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="carnet"]').val()==""){
		a = 'Ingresar Código de asegurado.';
		alertaG(a);
		return false;
	}/*
	if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val()=='Convenio' && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_susalud"]').val()==""){
		a = 'Ingresar número de Plan.';
		alertaG(a);
		return false;
	}*/
	var nacionalidad = $("#nacionalidad").val();
    var leng = (nacionalidad==="Peruano"?8:9);
    alert(nacionalidad);
	if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero"]').val().length !== leng){
		a = 'Ingresar número correcto de Documento.';
		alertaG(a);
		return false;
	}
	if ($('#emailh').val() !== '') {
		if(!(/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i).test($('#emailh').val())) {
			a = 'Ingresa un formato válido de Email.';
			alertaG(a);
			return false;
		}
	}
	a = 'Te falta ingresar información de Familiar.';	
	b = 'Te falta completar configuración de citas.';	
	
	nacionalidad = $("#nacionalidad2").val();
    leng = (nacionalidad==="Peruano"?8:9);
    alert(nacionalidad);
	if(($('#dni2').val()).length !== leng) {
		alertaG(a);
		return false;
	}	
	if($('#fechainicio').val() === '') {
		alertaG(b);
		return false;
	}
	if($('#horacita').val() === '') {
		alertaG(b);
		return false;
	}
	if($('#checktotal').val() === '') {
		alertaG(b);
		return false;
	}
	if($('#nombres2').val()==='') {
		alertaG(a);
		return false;
	}
	if($('#apellidopaterno2').val()==='') {
		alertaG(a);
		return false;
	}
	if($('#apellidomaterno2').val()==='') {
		alertaG(a);
		return false;	
	}
	if($('#telefono21').val()==='') {
		//alert($('#telefono21').val());
		alertaG(a);
		return false;
	}
	if($('#direccion2').val()==='') {
		//alert($('#direccion2').val());
		alertaG(a);
		return false;
	}
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
				a = 'Historia ';
				@if(!is_null($historia))
					a += 'Modificada';
				@else
					a += 'Generada';
					buscar('Historia');
				@endif   
				a += ' Correctamente';            
                alertaB(a);
                buscar('Historia');
                if(dat[0].id!==undefined){
                	window.open("historia/pdfhistoria?id="+dat[0].id,"_blank");
                }                
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}
setInterval(quitarPadding, 4000);
</script>