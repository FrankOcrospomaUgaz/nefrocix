<?php

use App\ConsultaNefrologica;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

$user = Auth::user();
$usertype_id = $user->usertype_id;

date_default_timezone_set('America/Lima');
$paciente = '';
$pid = '';
$hid = '';
$cid = '0';
$direccion = '';
$sexo = '';
$dni = '';
$afiliacion = '';
$telefono = '';
$ipress = '';
$dpto = '';
$provincia = '';
$distrito = '';
$epsi = '';
$efam = '';
$evivi = '';
$talla = '';
$elab = '';
$eeco = '';
$diagnostico = '';
$mege = '';
$mees = '';
$fecha = date('Y-m-d');
$fecha_actual = date('Y-m-d');
$hora = date('H:i');
$dia = "";
$imc = 0;
$txtAlergia = "";
$cbxVacunacionHepatitisB = "";
$txtNumeroTransfusiones = "";
$selectepo = "AMPOLLAS/SESION";
$selectcalcit = "AMPOLLAS/SESION";

if($historia !== null) {
	$paciente = $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres;
	$pid = $historia->persona->id;
	$hid = $historia->id;
	$sexo = $historia->persona->sexo;
	$sexo=='M'?$sexo='MASCULINO':$sexo='FEMENINO';
	$direccion = $historia->persona->direccion;
	$talla = $historia->txtTalla;
	$dni = $historia->persona->dni;
	$afiliacion = $historia->carnet;
	$telefono = $historia->persona->telefono;
	$ipress = $historia->ipress;
	$dpto = $historia->departamento2->nombre;
	$provincia = $historia->provincia2->nombre;
	$txtAlergia = $historia->txtAlergia;
	$distrito = $historia->distrito2->nombre;
	$cbxVacunacionHepatitisB = $historia->cbxVacunacionHepatitisB;
	$txtNumeroTransfusiones = $historia->txtNumeroTransfusiones;
}

if($hc !== null) {
	$cid = $hc->id;
	$epsi = $hc->txtEpsi;
	$efam = $hc->txtEfam;
	$evivi = $hc->txtEvivi;
	$elab = $hc->txtElab;
	$eeco = $hc->txtEeco;
	$diagnostico = $hc->cadenacies;
	$mege = $hc->txtMege;
	$mees = $hc->txtMees;
	$txtVacunacion = $hc->txtVacunacion;
	$txtRevacunacion = $hc->txtRevacunacion;
	$txtTransfusiones = $hc->txtTransfusiones;
	$txtAlergia = $hc->txtAlergia;
	$imc = $hc->imc;
	$dia = date('d', strtotime($hc->fecha_atencion));
	$fecha = date('m/Y', strtotime($hc->fecha));
	$hora = date('H:i:s', strtotime($hc->fecha_atencion));
	$fecha_actual = date('Y-m-d', strtotime($hc->fecha_atencion));
	$selectepo = $hc->selectepo;
	$selectcalcit = $hc->selectcalcit==""?$selectcalcit:$hc->selectcalcit;
}

// Anteriores

$examenesGeneral_old = array(
    "86703" => "ELISA o prueba rápida para HIV-1 y HIV-2",
    "87340" => "Detección de antígeno de superficie de virus de Hepatitis B (HBsAg) por ELISA",
    "86706" => "Detección de anticuerpos para antígeno de superficie Hepatitis B (HBs-Ag)",
    "86704" => "Detección de anticuerpos totales para núcleo de virus de Hepatitis B (Total Anti-Hbcore)",
    "86803" => "Determinación de anticuerpos para Hepatitis C",
    "86592" => "Prueba de sífilis cualitativa (VDRL, RPR)",
    "84520" => "Úrea",        
    "82565" => "Creatinina en sangre",        
    "85014" => "Hematocrito",
    "80051" => "Electrolitos séricos",
    "85018" => "Dosaje de hemoglobina",        
    "84100" => "Fósforo en sangre",        
    "82310" => "Calcio sérico",
    "84450" => "TGO transaminasa glutámico oxalacética",
    "84460" => "TGP transaminasa glutámico pirúvica",
    "84075" => "Fosfatasa Alcalina",
    "83970" => "Paratohormona (PTH)",
    "83540" => "Hierro sérico",
    "82728" => "Ferritina",
    "84466" => "Saturación de transferrina",        
    "84165" => "Proteínas; fraccionamiento y determinación cuantitativa por electroforesis",
    "82040" => "Dosaje de Albúmina; suero, plasma o sangre total",
);

// Nuevos

$examenesGeneral_new = array(
    "84520" => "Nitrógeno ureico; cuantitativo",
    "82565" => "Dosaje de Creatinina en sangre",
    "85014" => "Hematocrito",
    "85018" => "Hemoglobina",
    "80051" => "Perfil de electrolito",
    "84100" => "Dosaje de Fósforo inorgánico (fosfato)",
    "82310" => "Dosaje de Calcio; total",
    "84075" => "Dosaje de Fosfatasa, alcalina",
    "84450" => "Aspartato amino transferasa (AST) (SGOT)",
    "84460" => "Transferasa; amino alanina (ALT) (SGPT)",
    "86703" => "Anticuerpo; HIV-1 y HIV-2, análisis único",
    "86592" => "Prueba de sífilis; anticuerpo no treponémico; cualitativo (p. ej. VDRL, RPR, ART)",
    "83970" => "Dosaje de Paratohormona (hormona paratiroidea)",
    "87340" => "Detección de antígenos de agentes infeccioso mediante técnica de inmunoensayo enzimático, cualitativo o semicuantitativo, método de varios pasos; hepatitis B antpigeno de superficie (HBsAg)",
    "86706" => "Anticuerpo contra el antígeno de superficie de la hepatitis B (HBsAb)",
    "86704" => "Anticuerpo contra el antígeno de la nucleocápside de la hepatitis B (HBcAb); total",
    "86803" => "Anticuerpo contra la hepatitis C",
    "83540" => "Dosaje de Hierro",
    "82728" => "Dosaje de Ferritina",
    "84466" => "Transferrina",
    "82040" => "Dosaje de Albúmina; suero, plasma o sangre total",
    "84165" => "Proteínas; fraccionamiento y determinación cuantitativa por electroforesis",
);

$examenesGeneral = null;
$oculto = '';

if(strtotime($fecha_actual) >= strtotime('2021-08-03')) {
    $examenesGeneral = $examenesGeneral_new;
    $oculto = 'hide';
} else {
    $examenesGeneral = $examenesGeneral_old;
}

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
	#cuerpoMedicam tr td {
		font-size: 11px;
	}
</style>
<div id="divMensajeError{!! $entidad !!}">
</div>
{!! Form::model($hc, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="panel-group">
    <div class="form-group">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    DATOS DE FILIACIÓN DEL PACIENTE
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('txtPaciente', 'Paciente', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-10 col-md-10 col-sm-10">
                            {!! Form::text('txtPaciente', $paciente, array('class' => 'form-control input-sm', 'id' => 'txtPaciente', 'readonly')) !!}
								{!! Form::hidden('id1', $cid, array('id' => 'id1')) !!}
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
                        {!! Form::label('txtEdad', 'Edad', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::text('txtEdad', $ed->y, array('class' => 'form-control input-sm', 'id' => 'txtEdad', 'readonly')) !!}
                        </div>
                        {!! Form::label('txtSexo', 'Sexo', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::text('txtSexo', $sexo, array('class' => 'form-control input-sm', 'id' => 'txtSexo', 'readonly')) !!}
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
                                <button class="btn btn-info btn-sm" onclick="modal('consultamensual/verconsolidadomedicamentos?id={{ $pid }}&anno={{$anillo}}', 'Consolidado Medicamentos<button type=\'button\' class=\'close closdat\' onclick=\'cerrarModal();\' title=\'Cerrar Ventana\'><i style=\'font-weight:bold;color:red; font-weight: bold;\' class=\'glyphicon glyphicon-remove-circle\'></i></button>', this);" title="Visualizar">
                                    <i class="fa fa-print">
                                    </i>
                                </button>
                                <button class="btn btn-success btn-sm" onclick="consolidadoMedicamentos('{{ $pid }}');" title="Descargar">
                                    <i class="fa fa-download">
                                    </i>
                                </button>
                            </div>
                            {!! Form::label('diaconsulta1', 'Consolidado Resultados', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
                            <div class="col-lg-3 col-md-3 col-sm-3">
                                <button class="btn btn-warning btn-sm" onclick="modal('consultamensual/verresultadosporpaciente?historia_id={{$hid}}&anno={{$anillo}}', 'Consolidado Resultados<button type=\'button\' class=\'close closdat\' onclick=\'cerrarModal();\' title=\'Cerrar Ventana\'><i style=\'font-weight:bold;color:red; font-weight: bold;\' class=\'glyphicon glyphicon-remove-circle\'></i></button>', this);" title="Visualizar">
                                    <i class="fa fa-print">
                                    </i>
                                </button>
                                <button class="btn btn-success btn-sm" onclick="historialResultadosPorPaciente({{$hid}}, {{$anillo}})" title="Descargar">
                                    <i class="fa fa-download">
                                    </i>
                                </button>
                            </div>
                        </div>
                    </hr>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-8">
            <div class="panel panel-success">
                <div class="panel-heading">
                    CONSULTA
                </div>
                <div class="panel-body">
                    <?php

						$sit = $hc->situacion;
						$tipodatos = $hc->txtTipoDatos;
			            $dosultimas = ConsultaNefrologica::where('persona_id', '=', $historia->person_id)
			            		->orderBy('fecha', 'DESC')
			            		->where("fecha", "<=", $hc->fecha)
			            		->limit(2)
			            		->get();

			            $penultima = null;
			            if(!empty($dosultimas[1])) {
			                $penultima = $dosultimas[1];
			            }

			            $resultadosmensualestexto = "";

			            if($penultima!==NULL) {
			                switch ($penultima->situacion) {
			                    case "N":
			                        $resultadosmensualestexto = " PARA PACIENTE NUEVO (PILA DE RESULTADOS COMPLETOS)";
			                    break;
			                    case "M":
			                        $resultadosmensualestexto = "MENSUALES";
			                    break;
			                    case "M-B":
			                        $resultadosmensualestexto = "MENSUALES + BIMENSUALES";
			                    break;
			                    case "M-T":
			                        $resultadosmensualestexto = "MENSUALES + TRIMESTRALES";
			                    break;
			                    case "M-B-T-S":
			                        $resultadosmensualestexto = "MENSUALES + BIMENSUALES + TRIMESTRALES + SEMESTRALES";
			                    break;
			                }
			            }

						?>
                    <div class="form-group">
                        {!! Form::label('txtMotivo', 'Motivo', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-7 col-md-7 col-sm-7">
                            {!! Form::text('txtMotivo', 'EVALUACIÓN NEFROLÓGICA CON RESULTADOS: '.$resultadosmensualestexto, array('class' => 'form-control input-sm', 'id' => 'txtMotivo', 'readonly')) !!}
                        </div>
                        {!! Form::label('tiempoenf', 'Tiempo enfermedad', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            {!! Form::text('tiempoenf', ($hc->tiempoenf==""||$hc->tiempoenf==NULL?($c2==NULL?"":$c2->tiempoenf):$hc->tiempoenf), array('class' => 'form-control input-sm requerido', 'id' => 'tiempoenf')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('anamnesis', 'Anamnesis', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-7 col-md-7 col-sm-7">
                            {!! Form::text('anamnesis', null, array('class' => 'form-control input-sm requerido', 'id' => 'anamnesis')) !!}
                        </div>
                        {!! Form::label('pesoseco', 'Peso Seco', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="input-group">
                                {!! Form::text('pesoseco', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'pesoseco', "onkeyup" => "calcularIMC();")) !!}
                                <span class="input-group-addon">
                                    Kg.
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('temperatura', 'T°', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="input-group">
                                {!! Form::text('temperatura', ($penultima!==NULL?$penultima->temperatura:""), array('class' => 'form-control input-sm requerido numerin', 'id' => 'temperatura')) !!}
                                <span class="input-group-addon">
                                    °C
                                </span>
                            </div>
                        </div>
                        {!! Form::label('pa', 'P.A.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            {!! Form::text('pa', null, array('class' => 'form-control input-sm requerido', 'id' => 'pa')) !!}
                        </div>
                        {!! Form::label('fc', 'F.C.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="input-group">
                                {!! Form::text('fc', null, array('class' => 'form-control input-sm requerido numerin', 'id' => 'fc')) !!}
                                <span class="input-group-addon">
                                    /min
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('fr', 'F.R.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="input-group">
                                {!! Form::text('fr', ($penultima!==NULL?$penultima->fr:""), array('class' => 'form-control input-sm requerido numerin', 'id' => 'fr')) !!}
                                <span class="input-group-addon">
                                    /min
                                </span>
                            </div>
                        </div>
                        {!! Form::label('talla', 'Talla', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="input-group">
                                {!! Form::text('talla', $talla, array('class' => 'form-control input-sm requerido numerin', 'id' => 'talla', 'onkeyup' => 'calcularIMC()')) !!}
                                <span class="input-group-addon">
                                    m.
                                </span>
                            </div>
                        </div>
                        {!! Form::label('imc', 'IMC', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            {!! Form::text('imc', $imc, array('class' => 'form-control input-sm requerido numerin', 'id' => 'imc', 'readonly')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('cav', 'CAV', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::text('cav', ($hc->cav==""||$hc->cav==NULL?($c2==NULL?"":$c2->cav):$hc->cav), array('class' => 'form-control input-sm requerido', 'id' => 'cav')) !!}
                        </div>
                        {!! Form::label('tcsc', 'TCSC', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::text('tcsc', null, array('class' => 'form-control input-sm requerido', 'id' => 'tcsc')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('pulmones', 'Pulmones', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::text('pulmones', ($hc->pulmones==""||$hc->pulmones==NULL?($c2==NULL?"":$c2->pulmones):$hc->pulmones), array('class' => 'form-control input-sm requerido', 'id' => 'pulmones')) !!}
                        </div>
                        {!! Form::label('sisnervioso', 'Sistema nervioso', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::text('sisnervioso', ($hc->sisnervioso==""||$hc->sisnervioso==NULL?($c2==NULL?"":$c2->sisnervioso):$hc->sisnervioso), array('class' => 'form-control input-sm requerido', 'id' => 'sisnervioso')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('txtAlergia', 'Alergia', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::textarea('txtAlergia', ($hc->txtAlergia==""||$hc->txtAlergia==NULL?($c2==NULL?"":$c2->txtAlergia):$hc->txtAlergia), array('class' => 'form-control input-sm', 'id' => 'txtAlergia', "rows"=>"2")) !!}
                        </div>
                        {!! Form::label('txtTransfusiones', 'Transfus.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::textarea('txtTransfusiones', ($hc->txtTransfusiones==""||$hc->txtTransfusiones==NULL?($c2==NULL?"":$c2->txtTransfusiones):$hc->txtTransfusiones), array('class' => 'form-control input-sm', 'id' => 'txtTransfusiones', "rows"=>"2")) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('txtVacunacion', 'Vacunación', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::textarea('txtVacunacion', ($hc->txtVacunacion==""||$hc->txtVacunacion==NULL?($c2==NULL?"":$c2->txtVacunacion):$hc->txtVacunacion), array('class' => 'form-control input-sm', 'id' => 'txtVacunacion', "rows"=>"2")) !!}
                        </div>
                        {!! Form::label('txtRevacunacion', 'Revacun.', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::textarea('txtRevacunacion', ($hc->txtRevacunacion==""||$hc->txtRevacunacion==NULL?($c2==NULL?"":$c2->txtRevacunacion):$hc->txtRevacunacion), array('class' => 'form-control input-sm', 'id' => 'txtRevacunacion', "rows"=>"2")) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('txtDiagnostico2', 'Diagóstico', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5">
                            {!! Form::text('txtDiagnostico2', '', array('class' => 'form-control input-sm', 'id' => 'txtDiagnostico2')) !!}
								{!! Form::hidden('cadenacies2', $diagnostico, array('id' => 'cadenacies2')) !!}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <table border="1" style="width:100%">
                                <thead id="cabeceracie2">
                                    <tr>
                                        <th style="font-size: 13px !important;" width="90%">
                                            Descripción
                                        </th>
                                        <th style="font-size: 13px !important;" width="10%">
                                            Eliminar
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="detallecie2">
                                </tbody>
                            </table>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                            <u style="color:blue;">
                                <b>
                                    Diagóstico de exámenes de laboratorio del mes pasado
                                </b>
                            </u>
                        </div>
                    </div>
                    <!-- Inicio escoger datos anteriores -->
                    <!--
						@if($sit !== "NUEVO")
						    @if($sit==="MENSUAL")
						        @if($penultima!==NULL)
						            @if($penultima->situacion=='NUEVO')
						                <div id="NUEVO" class="campito">
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12 text-center">
													<u><b style="color:red">DATOS PARA NUEVO PACIENTE</b></u>
												</div>
											</div>
								    		<div class="form-group">
												{!! Form::label('txtEli2', $examenesGeneral['86703'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtEli2', $penultima->txtEli, array('class' => 'form-control input-sm', 'id' => 'txtEli2', "disabled")) !!}								
												</div>
												{!! Form::label('txtDet222', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet222', $penultima->txtDet, array('class' => 'form-control input-sm', 'id' => 'txtDet222', "disabled")) !!}								
												</div>
												{!! Form::label('txtDet22', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet22', $penultima->txtDet2, array('class' => 'form-control input-sm', 'id' => 'txtDet22', "disabled")) !!}								
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtDet32', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet32', $penultima->txtDet3, array('class' => 'form-control input-sm', 'id' => 'txtDet32', "disabled")) !!}								
												</div>
												{!! Form::label('txtDet42', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet42', $penultima->txtDet4, array('class' => 'form-control input-sm', 'id' => 'txtDet42', "disabled")) !!}								
												</div>
											</div>
										</div>
						            @elseif($penultima->situacion=='TRIMESTRAL'||$penultima->situacion=='SEMESTRAL')
						                <div id="MENSUAL" class="campito">
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12 text-center">
													<u><b style="color:red">DATOS MENSUALES</b></u>
												</div>
											</div>
								    		<div class="form-group">
												{!! Form::label('txtUre', 'Úrea Pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtUre', $penultima->txtUre." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre')) !!}								
												</div>
												{!! Form::label('txtUre2', 'Úrea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtUre2', $penultima->txtUre2." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre2')) !!}								
												</div>
												{!! Form::label('txtCre', $examenesGeneral['82565'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtCre', $penultima->txtCre." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCre')) !!}								
												</div>								
											</div>
											<div class="form-group">
												{!! Form::label('txtHem', $examenesGeneral['85014'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtHem', $penultima->txtHem." %", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHem')) !!}								
												</div>
												{!! Form::label('txtDos', $examenesGeneral['85018'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDos', $penultima->txtDos." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDos')) !!}								
												</div>
												{!! Form::label('txtSodio', 'Sodio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtSodio', $penultima->txtSodio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSodio')) !!}								
												</div>								
											</div>
											<div class="form-group">
												{!! Form::label('txtFos', $examenesGeneral['84100'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtFos', $penultima->txtFos." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos')) !!}								
												</div>
												{!! Form::label('txtCal', $examenesGeneral['82310'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtCal', $penultima->txtCal." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCal')) !!}								
												</div>
												{!! Form::label('txtPotasio', 'Potasio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtPotasio', $penultima->txtPotasio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPotasio')) !!}								
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtCloro', 'Cloro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtCloro', $penultima->txtCloro." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCloro')) !!}								
												</div>
											</div>
										</div>
											@if($penultima->situacion=='SEMESTRAL')
								                <div id="BIMENSUAL" class="campito">
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12 text-center">
															<u><b style="color:red">DATOS BIMENSUALES</b></u>
														</div>
													</div>
										    		<div class="form-group">
														{!! Form::label('txtTgo', $examenesGeneral['84450'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-4 col-md-4 col-sm-4">
															{!! Form::text('txtTgo', $penultima->txtTgo, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgo')) !!}								
														</div>
														{!! Form::label('txtTgp', $examenesGeneral['84460'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-4 col-md-4 col-sm-4">
															{!! Form::text('txtTgp', $penultima->txtTgp, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgp')) !!}								
														</div>
													</div>
												</div>
											@endif
						                @if($penultima->situacion=='TRIMESTRAL'||$penultima->situacion=='SEMESTRAL')
						                    <div id="TRIMESTRAL" class="campito">
												<div class="form-group">
													<div class="col-lg-12 col-md-12 col-sm-12 text-center">
														<u><b style="color:red">DATOS TRIMESTRALES</b></u>
													</div>
												</div>
									    		<div class="form-group">
													{!! Form::label('txtPro', $examenesGeneral['84165'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-4 col-md-4 col-sm-4">
														{!! Form::text('txtPro', $penultima->txtPro, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPro')) !!}								
													</div>
													{!! Form::label('txtFos2', $examenesGeneral['84075'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-4 col-md-4 col-sm-4">
														{!! Form::text('txtFos2', $penultima->txtFos2, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos2')) !!}								
													</div>
												</div>
											</div>
											@if($penultima->situacion=='SEMESTRAL') 
												<div id="SEMESTRAL" class="campito">
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12 text-center">
															<u><b style="color:red">DATOS SEMESTRALES</b></u>
														</div>
													</div>
										    		<div class="form-group">
														{!! Form::label('txtEli', $examenesGeneral['86703'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtEli', $penultima->txtEli, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtEli')) !!}								
														</div>
														{!! Form::label('txtPru', $examenesGeneral['86592'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtPru', $penultima->txtPru, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPru')) !!}								
														</div>
														{!! Form::label('txtPar', $examenesGeneral['83970'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtPar', $penultima->txtPar, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPar')) !!}								
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtDet222', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtDet222', $penultima->txtDet, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet222')) !!}								
														</div>
														{!! Form::label('txtDet22', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtDet22', $penultima->txtDet2, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet22')) !!}								
														</div>
														{!! Form::label('txtDet32', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtDet32', $penultima->txtDet3, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet32')) !!}								
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtDet42', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtDet42', $penultima->txtDet4, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet42')) !!}								
														</div>
														{!! Form::label('txtHie', $examenesGeneral['83540'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtHie', $penultima->txtHie, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHie')) !!}								
														</div>
														{!! Form::label('txtFer', $examenesGeneral['82728'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtFer', $penultima->txtFer, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFer')) !!}								
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('txtSat', $examenesGeneral['84466'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtSat', $penultima->txtSat, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSat')) !!}								
														</div>
													</div>
												</div>
												<div id="OTROS" class="campito">
													<div class="form-group">
														<div class="col-lg-12 col-md-12 col-sm-12 text-center">
															<u><b style="color:red">OTROS DATOS</b></u>
														</div>
													</div>
										    		<div class="form-group">
														{!! Form::label('txtAlbu', $examenesGeneral['82040'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtAlbu', $penultima->txtAlbu." g/dl", array('class' => 'form-control input-sm', "readonly", 'id' => 'txtAlbu')) !!}								
														</div>
														{!! Form::label('txtGlobu', 'Globulina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtGlobu', $penultima->txtGlobu." g/dl", array('class' => 'form-control input-sm', "readonly", 'id' => 'txtGlobu')) !!}								
														</div>
														{!! Form::label('txtTransfe', 'Transferrina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
														<div class="col-lg-2 col-md-2 col-sm-2">
															{!! Form::text('txtTransfe', $penultima->txtTransfe, array('class' => 'form-control input-sm', "readonly", 'id' => 'txtTransfe')) !!}								
														</div>
													</div>
												</div>
											@endif
						                @elseif($penultima->situacion=='SEMESTRAL')
						                    <div id="SEMESTRAL" class="campito">
												<div class="form-group">
													<div class="col-lg-12 col-md-12 col-sm-12 text-center">
														<u><b style="color:red">DATOS SEMESTRALES</b></u>
													</div>
												</div>
									    		<div class="form-group">
													{!! Form::label('txtEli', $examenesGeneral['86703'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtEli', $penultima->txtEli, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtEli')) !!}								
													</div>
													{!! Form::label('txtPru', $examenesGeneral['86592'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtPru', $penultima->txtPru, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPru')) !!}								
													</div>
													{!! Form::label('txtPar', $examenesGeneral['83970'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtPar', $penultima->txtPar, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPar')) !!}								
													</div>
												</div>
												<div class="form-group">
													{!! Form::label('txtDet222', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtDet222', $penultima->txtDet, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet222')) !!}								
													</div>
													{!! Form::label('txtDet22', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtDet22', $penultima->txtDet2, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet22')) !!}								
													</div>
													{!! Form::label('txtDet32', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtDet32', $penultima->txtDet3, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet32')) !!}								
													</div>
												</div>
												<div class="form-group">
													{!! Form::label('txtDet42', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtDet42', $penultima->txtDet4, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet42')) !!}								
													</div>
													{!! Form::label('txtHie', $examenesGeneral['83540'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtHie', $penultima->txtHie, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHie')) !!}								
													</div>
													{!! Form::label('txtFer', $examenesGeneral['82728'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtFer', $penultima->txtFer, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFer')) !!}								
													</div>
												</div>
												<div class="form-group">
													{!! Form::label('txtSat', $examenesGeneral['84466'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
													<div class="col-lg-2 col-md-2 col-sm-2">
														{!! Form::text('txtSat', $penultima->txtSat, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSat')) !!}								
													</div>
												</div>
											</div>
										@endif
						            @endif
						        @endif
						    @elseif($sit==="BIMENSUAL")
						        @if($penultima!==NULL)
						            @if($penultima->situacion=='NUEVO')
						                <div id="NUEVO" class="campito">
											<div class="form-group">
												<div class="col-lg-12 col-md-12 col-sm-12 text-center">
													<u><b style="color:red">DATOS PARA NUEVO PACIENTE</b></u>
												</div>
											</div>
								    		<div class="form-group">
												{!! Form::label('txtEli2', $examenesGeneral['86703'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtEli2', $penultima->txtEli, array('class' => 'form-control input-sm', 'id' => 'txtEli2', "disabled")) !!}								
												</div>
												{!! Form::label('txtDet222', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet222', $penultima->txtDet, array('class' => 'form-control input-sm', 'id' => 'txtDet222', "disabled")) !!}								
												</div>
												{!! Form::label('txtDet22', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet22', $penultima->txtDet2, array('class' => 'form-control input-sm', 'id' => 'txtDet22', "disabled")) !!}								
												</div>
											</div>
											<div class="form-group">
												{!! Form::label('txtDet32', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet32', $penultima->txtDet3, array('class' => 'form-control input-sm', 'id' => 'txtDet32', "disabled")) !!}								
												</div>
												{!! Form::label('txtDet42', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
												<div class="col-lg-2 col-md-2 col-sm-2">
													{!! Form::text('txtDet42', $penultima->txtDet4, array('class' => 'form-control input-sm', 'id' => 'txtDet42', "disabled")) !!}								
												</div>
											</div>
										</div>                        
						            @endif
						            <div id="MENSUAL" class="campito">
										<div class="form-group">
											<div class="col-lg-12 col-md-12 col-sm-12 text-center">
												<u><b style="color:red">DATOS MENSUALES</b></u>
											</div>
										</div>
							    		<div class="form-group">
											{!! Form::label('txtUre', 'Úrea Pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtUre', $penultima->txtUre." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre')) !!}								
											</div>
											{!! Form::label('txtUre2', 'Úrea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtUre2', $penultima->txtUre2." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre2')) !!}								
											</div>
											{!! Form::label('txtCre', $examenesGeneral['82565'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtCre', $penultima->txtCre." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCre')) !!}								
											</div>								
										</div>
										<div class="form-group">
											{!! Form::label('txtHem', $examenesGeneral['85014'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtHem', $penultima->txtHem." %", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHem')) !!}								
											</div>
											{!! Form::label('txtDos', $examenesGeneral['85018'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtDos', $penultima->txtDos." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDos')) !!}								
											</div>
											{!! Form::label('txtSodio', 'Sodio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtSodio', $penultima->txtSodio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSodio')) !!}								
											</div>								
										</div>
										<div class="form-group">
											{!! Form::label('txtFos', $examenesGeneral['84100'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtFos', $penultima->txtFos." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos')) !!}								
											</div>
											{!! Form::label('txtCal', $examenesGeneral['82310'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtCal', $penultima->txtCal." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCal')) !!}								
											</div>
											{!! Form::label('txtPotasio', 'Potasio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtPotasio', $penultima->txtPotasio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPotasio')) !!}								
											</div>
										</div>
										<div class="form-group">
											{!! Form::label('txtCloro', 'Cloro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
											<div class="col-lg-2 col-md-2 col-sm-2">
												{!! Form::text('txtCloro', $penultima->txtCloro." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCloro')) !!}								
											</div>
										</div>
									</div>           
						        @endif
						    @elseif($sit==="TRIMESTRAL"||$sit==="SEMESTRAL")
						        <div id="MENSUAL" class="campito">
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12 text-center">
											<u><b style="color:red">DATOS MENSUALES</b></u>
										</div>
									</div>
						    		<div class="form-group">
										{!! Form::label('txtUre', 'Úrea Pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtUre', $penultima->txtUre." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre')) !!}								
										</div>
										{!! Form::label('txtUre2', 'Úrea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtUre2', $penultima->txtUre2." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre2')) !!}								
										</div>
										{!! Form::label('txtCre', $examenesGeneral['82565'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtCre', $penultima->txtCre." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCre')) !!}								
										</div>								
									</div>
									<div class="form-group">
										{!! Form::label('txtHem', $examenesGeneral['85014'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtHem', $penultima->txtHem." %", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHem')) !!}								
										</div>
										{!! Form::label('txtDos', $examenesGeneral['85018'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtDos', $penultima->txtDos." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDos')) !!}								
										</div>
										{!! Form::label('txtSodio', 'Sodio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtSodio', $penultima->txtSodio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSodio')) !!}								
										</div>								
									</div>
									<div class="form-group">
										{!! Form::label('txtFos', $examenesGeneral['84100'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtFos', $penultima->txtFos." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos')) !!}								
										</div>
										{!! Form::label('txtCal', $examenesGeneral['82310'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtCal', $penultima->txtCal." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCal')) !!}								
										</div>
										{!! Form::label('txtPotasio', 'Potasio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtPotasio', $penultima->txtPotasio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPotasio')) !!}								
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('txtCloro', 'Cloro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-2 col-md-2 col-sm-2">
											{!! Form::text('txtCloro', $penultima->txtCloro." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCloro')) !!}								
										</div>
									</div>
								</div>
						        <div id="BIMENSUAL" class="campito">
									<div class="form-group">
										<div class="col-lg-12 col-md-12 col-sm-12 text-center">
											<u><b style="color:red">DATOS BIMENSUALES</b></u>
										</div>
									</div>
						    		<div class="form-group">
										{!! Form::label('txtTgo', $examenesGeneral['84450'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtTgo', $penultima->txtTgo, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgo')) !!}								
										</div>
										{!! Form::label('txtTgp', $examenesGeneral['84460'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
										<div class="col-lg-4 col-md-4 col-sm-4">
											{!! Form::text('txtTgp', $penultima->txtTgp, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgp')) !!}								
										</div>
									</div>
								</div>
						    @endif
						@else
							<div class="form-group">
								<div class="col-lg-12 col-md-12 col-sm-12 text-center">
									<u style="color:red;"><b>ES UN PACIENTE NUEVO</b></u>
								</div>
							</div>
						@endif
						-->
                    @if($sit !== "N")
							@if($penultima!==NULL)
                    <div class="campito" id="MENSUAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS MENSUALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtUre', 'Úrea Pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtUre', $penultima->txtUre." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre')) !!}
                            </div>
                            {!! Form::label('txtUre2', 'Úrea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtUre2', $penultima->txtUre2." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre2')) !!}
                            </div>
                            {!! Form::label('txtCre', $examenesGeneral['82565'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtCre', $penultima->txtCre." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCre')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtHem', $examenesGeneral['85014'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtHem', $penultima->txtHem." %", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHem')) !!}
                            </div>
                            {!! Form::label('txtDos', $examenesGeneral['85018'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDos', $penultima->txtDos." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDos')) !!}
                            </div>
                            {!! Form::label('txtSodio', 'Sodio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtSodio', $penultima->txtSodio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSodio')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                        	{!! Form::label('txtPotasio', 'Potasio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPotasio', $penultima->txtPotasio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPotasio')) !!}
                            </div>
                            {!! Form::label('txtCloro', 'Cloro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtCloro', $penultima->txtCloro." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCloro')) !!}
                            </div>
                            {!! Form::label('txtFos', $examenesGeneral['84100'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtFos', $penultima->txtFos." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos')) !!}
                            </div>                                                       
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtCal', $examenesGeneral['82310'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtCal', $penultima->txtCal." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCal')) !!}
                            </div>
                        </div>
                    </div>
                    @if(
									($penultima->txtTipoDatos == 2||
										$penultima->txtTipoDatos == 4||
										$penultima->txtTipoDatos == 0)
									&&$penultima->situacion!=="N")
                    <div class="campito" id="BIMENSUAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS BIMENSUALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtTgo', $examenesGeneral['84450'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::text('txtTgo', $penultima->txtTgo, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgo')) !!}
                            </div>
                            {!! Form::label('txtTgp', $examenesGeneral['84460'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::text('txtTgp', $penultima->txtTgp, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgp')) !!}
                            </div>
                        </div>
                    </div>
                    @endif
								@if(
									($penultima->txtTipoDatos == 3||
										$penultima->txtTipoDatos == 0)
									&&$penultima->situacion!=="N")
                    <div class="campito" id="TRIMESTRAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS TRIMESTRALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtPro', $examenesGeneral['84165'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label '.$oculto)) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4 {{$oculto}}">
                                {!! Form::text('txtPro', $penultima->txtPro . " g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPro')) !!}
                            </div>
                            {!! Form::label('txtFos2', $examenesGeneral['84075'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::text('txtFos2', $penultima->txtFos2, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos2')) !!}
                            </div>
                        </div>
                    </div>
                    @endif
								@if($penultima->txtTipoDatos == 0&&$penultima->situacion!=="N")
                    <div class="campito" id="SEMESTRAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS SEMESTRALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtEli', $examenesGeneral['86703'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtEli', $penultima->txtEli, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtEli')) !!}
                            </div>
                            {!! Form::label('txtPru', $examenesGeneral['86592'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPru', $penultima->txtPru, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPru')) !!}
                            </div>
                            {!! Form::label('txtPar', $examenesGeneral['83970'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPar', $penultima->txtPar, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPar')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtDet222', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet222', $penultima->txtDet, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet222')) !!}
                            </div>
                            {!! Form::label('txtDet22', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet22', $penultima->txtDet2, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet22')) !!}
                            </div>
                            {!! Form::label('txtDet32', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet32', $penultima->txtDet3, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet32')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtDet42', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet42', $penultima->txtDet4, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet42')) !!}
                            </div>
                            {!! Form::label('txtHie', $examenesGeneral['83540'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtHie', $penultima->txtHie, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHie')) !!}
                            </div>
                            {!! Form::label('txtFer', $examenesGeneral['82728'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtFer', $penultima->txtFer, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFer')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtSat', $examenesGeneral['84466'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtSat', $penultima->txtSat, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSat')) !!}
                                    <span class="input-group-addon">
                                        %
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="campito" id="OTROS">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        OTROS DATOS
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtAlbu', $examenesGeneral['82040'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtAlbu', $penultima->txtAlbu." g/dl", array('class' => 'form-control input-sm', "readonly", 'id' => 'txtAlbu')) !!}
                            </div>
                            {!! Form::label('txtGlobu', 'Globulina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label '.$oculto)) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2 {{$oculto}}">
                                {!! Form::text('txtGlobu', $penultima->txtGlobu." g/dl", array('class' => 'form-control input-sm', "readonly", 'id' => 'txtGlobu')) !!}
                            </div>
                            {!! Form::label('txtTransfe', 'Transferrina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtTransfe', $penultima->txtTransfe, array('class' => 'form-control input-sm', "readonly", 'id' => 'txtTransfe')) !!}
                            </div>
                        </div>
                    </div>
                    @endif
							@endif
						@else
                    <div class="campito" id="MENSUAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS MENSUALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtUre', 'Úrea Pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtUre', $hc->txtUre." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre')) !!}
                            </div>
                            {!! Form::label('txtUre2', 'Úrea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtUre2', $hc->txtUre2." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtUre2')) !!}
                            </div>
                            {!! Form::label('txtCre', $examenesGeneral['82565'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtCre', $hc->txtCre." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCre')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtHem', $examenesGeneral['85014'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtHem', $hc->txtHem." %", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHem')) !!}
                            </div>
                            {!! Form::label('txtDos', $examenesGeneral['85018'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDos', $hc->txtDos." g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDos')) !!}
                            </div>
                            {!! Form::label('txtSodio', 'Sodio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtSodio', $hc->txtSodio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSodio')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtFos', $examenesGeneral['84100'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtFos', $hc->txtFos." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos')) !!}
                            </div>
                            {!! Form::label('txtCal', $examenesGeneral['82310'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtCal', $hc->txtCal." mg/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCal')) !!}
                            </div>
                            {!! Form::label('txtPotasio', 'Potasio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPotasio', $hc->txtPotasio." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPotasio')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtCloro', 'Cloro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtCloro', $hc->txtCloro." mmol/L", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtCloro')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="campito" id="BIMENSUAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS BIMENSUALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtTgo', $examenesGeneral['84450'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::text('txtTgo', $hc->txtTgo, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgo')) !!}
                            </div>
                            {!! Form::label('txtTgp', $examenesGeneral['84460'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::text('txtTgp', $hc->txtTgp, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtTgp')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="campito" id="TRIMESTRAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS TRIMESTRALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtPro', $examenesGeneral['84165'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label '.$oculto)) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4 {{$oculto}}">
                                {!! Form::text('txtPro', $hc->txtPro . " g/dl", array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPro')) !!}
                            </div>
                            {!! Form::label('txtFos2', $examenesGeneral['84075'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::text('txtFos2', $hc->txtFos2, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFos2')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="campito" id="SEMESTRAL">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        DATOS SEMESTRALES
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtEli', $examenesGeneral['86703'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtEli', $hc->txtEli, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtEli')) !!}
                            </div>
                            {!! Form::label('txtPru', $examenesGeneral['86592'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPru', $hc->txtPru, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPru')) !!}
                            </div>
                            {!! Form::label('txtPar', $examenesGeneral['83970'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPar', $hc->txtPar, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtPar')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtDet222', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet222', $hc->txtDet, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet222')) !!}
                            </div>
                            {!! Form::label('txtDet22', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet22', $hc->txtDet2, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet22')) !!}
                            </div>
                            {!! Form::label('txtDet32', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet32', $hc->txtDet3, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet32')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtDet42', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet42', $hc->txtDet4, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtDet42')) !!}
                            </div>
                            {!! Form::label('txtHie', $examenesGeneral['83540'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtHie', $hc->txtHie, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtHie')) !!}
                            </div>
                            {!! Form::label('txtFer', $examenesGeneral['82728'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtFer', $hc->txtFer, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtFer')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtSat', $examenesGeneral['84466'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtSat', $hc->txtSat, array('class' => 'form-control input-sm', 'readonly', 'id' => 'txtSat')) !!}
                                    <span class="input-group-addon">
                                        %
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="campito" id="OTROS">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <u>
                                    <b style="color:red">
                                        OTROS DATOS
                                    </b>
                                </u>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtAlbu', $examenesGeneral['82040'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtAlbu', $hc->txtAlbu." g/dl", array('class' => 'form-control input-sm', "readonly", 'id' => 'txtAlbu')) !!}
                            </div>
                            {!! Form::label('txtGlobu', 'Globulina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label '.$oculto)) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2 {{$oculto}}">
                                {!! Form::text('txtGlobu', $hc->txtGlobu." g/dl", array('class' => 'form-control input-sm', "readonly", 'id' => 'txtGlobu')) !!}
                            </div>
                            {!! Form::label('txtTransfe', 'Transferrina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtTransfe', $hc->txtTransfe, array('class' => 'form-control input-sm', "readonly", 'id' => 'txtTransfe')) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- Fin escoger datos anteriores -->
                    <div class="form-group">
                        {!! Form::label('observacion', 'Tratamiento y Observación', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-10 col-md-10 col-sm-10">
                            {!! Form::text('observacion', ($hc->observacion==""||$hc->observacion==NULL?"REQUIERE ERITROPOYETINA 2000 UI":$hc->observacion), array('class' => 'form-control input-sm', 'id' => 'observacion')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                            <u style="color:blue;">
                                <b>
                                    Medicamentos
                                </b>
                            </u>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <table border="1" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="font-size: 13px !important;" width="28%">
                                            DESCRIPCIÓN
                                        </th>
                                        <th style="font-size: 13px !important;" width="22%">
                                            FRECUENCIA
                                        </th>
                                        <th style="font-size: 13px !important;" width="7%">
                                            CANT/MES
                                        </th>
                                        <th style="font-size: 13px !important;" width="43%">
                                            OBSERVACIÓN
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpoMedicam">
                                    <tr>
                                        <td style="color:#600125; font-weight:bold;">
                                            N° SESIONES DE HEMODIÁLISIS
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f1', $frecuencia, array('class' => 'form-control input-sm numerin', 'id' => 'f1', "readonly")) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    V/SEMANA
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c1', ($hc->c1==NULL|| $hc->c1==""?$cantidadalmes:$hc->c1), array('class' => 'form-control input-sm numerin', 'id' => 'c1')) !!}
                                            <td>
                                                {!! Form::text('o1', ($hc->o1==""||$hc->o1==NULL?($c2==NULL?"":$c2->o1):$hc->o1), array('class' => 'form-control input-sm', 'id' => 'o1', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;", "style" => "color:blue; font-weight: bold;")) !!}
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#605901; font-weight:bold;">
                                            EPOETINA ALFA (ERITROPOYETINA) 2000 UI/ML INY 1 ML
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f2', null, array("onkeyup"=>"calcularCantidad(2, this.value, " . (int)($cantidadalmes/($frecuencia==0?1:$frecuencia)) . ");", 'class' => 'form-control input-sm numerin requerido', 'id' => 'f2')) !!}
                                                <select class="form-control input-sm" id="selectepo" name="selectepo" value="{{ $selectepo }}">
                                                    <option value="AMPOLLAS/SESION">
                                                        AMPOLLAS/SESION
                                                    </option>
                                                    <option value="AMPOLLAS/SEMANA">
                                                        AMPOLLAS/SEMANA
                                                    </option>
                                                    <option value="AMPOLLAS/MES">
                                                        AMPOLLAS/MES
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c2', null, array('class' => 'form-control input-sm numerin', 'id' => 'c2')) !!}
                                            <td>
                                                {!! Form::text('o2', ($hc->o2==""||$hc->o2==NULL?($c2==NULL?"":$c2->o2):$hc->o2), array('class' => 'form-control input-sm', 'id' => 'o2', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#605901; font-weight:bold;">
                                            HIERRO (COMO SACARATO) 20MG FE/ML INY 5 ML
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f3', null, array("onkeyup"=>"calcularCantidad(3, this.value, 1);", 'class' => 'form-control input-sm numerin requerido', 'id' => 'f3')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    AMPOLLAS/MES
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c3', null, array('class' => 'form-control input-sm numerin', 'id' => 'c3')) !!}
                                            <td>
                                                {!! Form::text('o3', ($hc->o3==""||$hc->o3==NULL?($c2==NULL?"":$c2->o3):$hc->o3), array('class' => 'form-control input-sm', 'id' => 'o3', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#605901; font-weight:bold;">
                                            VITAMINA B12 HIDROXICOBALAMINA 1MG/ML INY 1ML
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f4', null, array("onkeyup"=>"calcularCantidad(4, this.value, 1);", 'class' => 'form-control input-sm numerin requerido', 'id' => 'f4')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    AMPOLLAS/MES
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c4', null, array('class' => 'form-control input-sm numerin', 'id' => 'c4')) !!}
                                            <td>
                                                {!! Form::text('o4', ($hc->o4==""||$hc->o4==NULL?($c2==NULL?"":$c2->o4):$hc->o4), array('class' => 'form-control input-sm', 'id' => 'o4', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            CALCIO CARBONATO 500 MG (EQUIV.A 500 MG DE CALCIO) TAB
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f5', null, array("onkeyup"=>"calcularCantidad(5, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f5')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c5', null, array('class' => 'form-control input-sm numerin', 'id' => 'c5')) !!}
                                            <td>
                                                {!! Form::text('o5', ($hc->o5==""||$hc->o5==NULL?($c2==NULL?"":$c2->o5):$hc->o5), array('class' => 'form-control input-sm', 'id' => 'o5', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i5', ($hc->i5==""||$hc->i5==NULL?($c2==NULL?"":$c2->i5):$hc->i5), array('class' => 'form-control input-sm', 'id' => 'i5', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            PIRIDOXINA 50MG TAB
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f6', null, array("onkeyup"=>"calcularCantidad(6, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f6')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c6', null, array('class' => 'form-control input-sm numerin', 'id' => 'c6')) !!}
                                            <td>
                                                {!! Form::text('o6', ($hc->o6==""||$hc->o6==NULL?($c2==NULL?"":$c2->o6):$hc->o6), array('class' => 'form-control input-sm', 'id' => 'o6', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i6', ($hc->i6==""||$hc->i6==NULL?($c2==NULL?"":$c2->i6):$hc->i6), array('class' => 'form-control input-sm', 'id' => 'i6', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            TIAMINA 100MG TAB
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f7', null, array("onkeyup"=>"calcularCantidad(7, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f7')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c7', null, array('class' => 'form-control input-sm numerin', 'id' => 'c7')) !!}
                                            <td>
                                                {!! Form::text('o7', ($hc->o7==""||$hc->o7==NULL?($c2==NULL?"":$c2->o7):$hc->o7), array('class' => 'form-control input-sm', 'id' => 'o7', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i7', ($hc->i7==""||$hc->i7==NULL?($c2==NULL?"":$c2->i7):$hc->i7), array('class' => 'form-control input-sm', 'id' => 'i7', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            ÁCIDO FÓLICO 0.5 MG TAB
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f8', null, array("onkeyup"=>"calcularCantidad(8, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f8')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c8', null, array('class' => 'form-control input-sm numerin', 'id' => 'c8')) !!}
                                            <td>
                                                {!! Form::text('o8', ($hc->o8==""||$hc->o8==NULL?($c2==NULL?"":$c2->o8):$hc->o8), array('class' => 'form-control input-sm', 'id' => 'o8', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i8', ($hc->i8==""||$hc->i8==NULL?($c2==NULL?"":$c2->i8):$hc->i8), array('class' => 'form-control input-sm', 'id' => 'i8', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            CALCITRIOL 1 MCG/ML INY
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f9', null, array("onkeyup"=>"calcularCantidad(9, this.value, " . $cantidadalmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f9')) !!}
                                                <select class="form-control input-sm" id="selectcalcit" name="selectcalcit" oninvalid="{{ $selectcalcit }}">
                                                    <option value="AMPOLLAS/SESION">
                                                        AMPOLLAS/SESION
                                                    </option>
                                                    <option value="AMPOLLAS/SEMANA">
                                                        AMPOLLAS/SEMANA
                                                    </option>
                                                    <option value="AMPOLLAS/MES">
                                                        AMPOLLAS/MES
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c9', null, array('class' => 'form-control input-sm numerin', 'id' => 'c9')) !!}
                                            <td>
                                                {!! Form::text('o9', ($hc->o9==""||$hc->o9==NULL?($c2==NULL?"":$c2->o9):$hc->o9), array('class' => 'form-control input-sm', 'id' => 'o9', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i9', ($hc->i9==""||$hc->i9==NULL?($c2==NULL?"":$c2->i9):$hc->i9), array('class' => 'form-control input-sm', 'id' => 'i9', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            CALCITRIOL 0.25ug CAP (**)
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f91', null, array("onkeyup"=>"calcularCantidad(91, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f91')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c91', null, array('class' => 'form-control input-sm numerin', 'id' => 'c91')) !!}
                                            <td>
                                                {!! Form::text('o91', ($hc->o91==""||$hc->o91==NULL?($c2==NULL?"":$c2->o91):$hc->o91), array('class' => 'form-control input-sm', 'id' => 'o91', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i91', ($hc->i91==""||$hc->i91==NULL?($c2==NULL?"":$c2->i91):$hc->i91), array('class' => 'form-control input-sm', 'id' => 'i91', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            ENALAPRIL MALEATO 10 MG TAB
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f10', null, array("onkeyup"=>"calcularCantidad(10, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f10')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c10', null, array('class' => 'form-control input-sm numerin', 'id' => 'c10')) !!}
                                            <td>
                                                {!! Form::text('o10', ($hc->o10==""||$hc->o10==NULL?($c2==NULL?"":$c2->o10):$hc->o10), array('class' => 'form-control input-sm', 'id' => 'o10', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i10', ($hc->i10==""||$hc->i10==NULL?($c2==NULL?"":$c2->i10):$hc->i10), array('class' => 'form-control input-sm', 'id' => 'i10', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            CAPTOPRIL 25 MG TAB
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f11', null, array("onkeyup"=>"calcularCantidad(11, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f11')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c11', null, array('class' => 'form-control input-sm numerin', 'id' => 'c11')) !!}
                                            <td>
                                                {!! Form::text('o11', ($hc->o11==""||$hc->o11==NULL?($c2==NULL?"":$c2->o11):$hc->o11), array('class' => 'form-control input-sm', 'id' => 'o11', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i11', ($hc->i11==""||$hc->i11==NULL?($c2==NULL?"":$c2->i11):$hc->i11), array('class' => 'form-control input-sm', 'id' => 'i11', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            AMLODIPINO (COMO BESILATO) 10 MG TAB
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f12', null, array("onkeyup"=>"calcularCantidad(12, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f12')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c12', null, array('class' => 'form-control input-sm numerin', 'id' => 'c12')) !!}
                                            <td>
                                                {!! Form::text('o12', ($hc->o12==""||$hc->o12==NULL?($c2==NULL?"":$c2->o12):$hc->o12), array('class' => 'form-control input-sm', 'id' => 'o12', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i12', ($hc->i12==""||$hc->i12==NULL?($c2==NULL?"":$c2->i12):$hc->i12), array('class' => 'form-control input-sm', 'id' => 'i12', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            NIFEDIPINO 10 MG
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f13', null, array("onkeyup"=>"calcularCantidad(13, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f13')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c13', null, array('class' => 'form-control input-sm numerin', 'id' => 'c13')) !!}
                                            <td>
                                                {!! Form::text('o13', ($hc->o13==""||$hc->o13==NULL?($c2==NULL?"":$c2->o13):$hc->o13), array('class' => 'form-control input-sm', 'id' => 'o13', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i13', ($hc->i13==""||$hc->i13==NULL?($c2==NULL?"":$c2->i13):$hc->i13), array('class' => 'form-control input-sm', 'id' => 'i13', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            NIFEDIPINO DE 30 MG
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f14', null, array("onkeyup"=>"calcularCantidad(14, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f14')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c14', null, array('class' => 'form-control input-sm numerin', 'id' => 'c14')) !!}
                                            <td>
                                                {!! Form::text('o14', ($hc->o14==""||$hc->o14==NULL?($c2==NULL?"":$c2->o14):$hc->o14), array('class' => 'form-control input-sm', 'id' => 'o14', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i14', ($hc->i14==""||$hc->i14==NULL?($c2==NULL?"":$c2->i14):$hc->i14), array('class' => 'form-control input-sm', 'id' => 'i14', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            METILDOPA 250 MG
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f15', null, array("onkeyup"=>"calcularCantidad(15, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f15')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c15', null, array('class' => 'form-control input-sm numerin', 'id' => 'c15')) !!}
                                            <td>
                                                {!! Form::text('o15', ($hc->o15==""||$hc->o15==NULL?($c2==NULL?"":$c2->o15):$hc->o15), array('class' => 'form-control input-sm', 'id' => 'o15', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i15', ($hc->i15==""||$hc->i15==NULL?($c2==NULL?"":$c2->i15):$hc->i15), array('class' => 'form-control input-sm', 'id' => 'i15', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            ATENOLOL 100 MG
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f16', null, array("onkeyup"=>"calcularCantidad(16, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f16')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c16', null, array('class' => 'form-control input-sm numerin', 'id' => 'c16')) !!}
                                            <td>
                                                {!! Form::text('o16', ($hc->o16==""||$hc->o16==NULL?($c2==NULL?"":$c2->o16):$hc->o16), array('class' => 'form-control input-sm', 'id' => 'o16', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i16', ($hc->i16==""||$hc->i16==NULL?($c2==NULL?"":$c2->i16):$hc->i16), array('class' => 'form-control input-sm', 'id' => 'i16', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#015960; font-weight:bold;">
                                            LOSARTAN 50 MG
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::text('f17', null, array("onkeyup"=>"calcularCantidad(17, this.value, " . $diasenmes . ");", 'class' => 'form-control input-sm numerin', 'id' => 'f17')) !!}
                                                <span class="input-group-addon" style="font-size: 9px;">
                                                    TAB/DIA X {{ $diasenmes }} DÍAS
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('c17', null, array('class' => 'form-control input-sm numerin', 'id' => 'c17')) !!}
                                            <td>
                                                {!! Form::text('o17', ($hc->o17==""||$hc->o17==NULL?($c2==NULL?"":$c2->o17):$hc->o17), array('class' => 'form-control input-sm', 'id' => 'o17', "placeholder" => "INGRESA OBSERVACIÓN", "title" => "OBSERVACIÓN", "style" => "color:blue; font-weight: bold;")) !!}
                                                <br>
                                                    {!! Form::text('i17', ($hc->i17==""||$hc->i17==NULL?($c2==NULL?"":$c2->i17):$hc->i17), array('class' => 'form-control input-sm', 'id' => 'i17', "placeholder" => "INGRESA INDICACIÓN", "style" => "color:green; font-weight: bold;", "title" => "INDICACIÓN MÉDICA")) !!}
                                                </br>
                                            </td>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 text-right">
        @if($usertype_id!==28&&$usertype_id!==29)	
			{!! Form::button('
        <i class="fa fa-check fa-lg">
        </i>
        Registrar Medicamentos', array('class' => 'btn btn-primary btn-sm', 'id' => 'btnGuardar3', 'onclick' => 'registrarReporteMedicamentos();')) !!}
			@endif
			{!! Form::button('
        <i class="fa fa-check fa-lg">
        </i>
        '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarReporte2();')) !!}
			{!! Form::button('
        <i class="fa fa-exclamation fa-lg">
        </i>
        Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
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
	$("#hora2").val("{{$hora}}");
	$("#selectepo").val("{{$selectepo}}");
	$("#selectcalcit").val("{{$selectcalcit}}");
}); 

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
	}
});

var cie10s2 = new Bloodhound({
	datumTokenizer: function (d) {
		return Bloodhound.tokenizers.whitespace(d.value);
	},
	limit: 5,
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote: {
		url: 'historiaclinica/cie10autocompletar/%QUERY',
		filter: function (cie10s2) {
			return $.map(cie10s2, function (cie10) {
				return {
					value: cie10.value,
					id: cie10.id,
				};
			});
		}
	}
});

cie10s2.initialize();
$("#txtDiagnostico2").typeahead(null,{
	displayKey: 'value',
	source: cie10s2.ttAdapter()
}).on('typeahead:selected', function (object, datum) {
	$("#txtDiagnostico2").val("");
	$('#txtDiagnostico2').typeahead('val','');
	var cie_id = datum.id;
	var existe = false;
	$("#detallecie2 tr").each(function(){
		if(cie_id == this.id){
			existe = true;
		}
	});
	if(!existe){
		fila =  '<tr data-id="'+ datum.id +'" align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
		$("#detallecie2").append(fila);
		var cadenacies = '';
		$('#detallecie2 tr').each(function(index, el) {
			cadenacies += $(this).data('id') + ';';
		});
		$("#cadenacies2").val(cadenacies);
	}

});

function eliminarDetalleCie(comp,tipo){
	(($(comp).parent()).parent()).remove();
	var cadenacies = '';
	$('#detallecie2 tr').each(function(index, el) {
		cadenacies += $(this).data('id') + ';';
	});
	$("#cadenacies2").val(cadenacies);
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

function registrarReporte2() {
	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	if($("#cadenacies2").val() === '') {
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
	        url: "consultamensual/storereporte2",
	        data: $('#formMantenimiento{{ $entidad }}').serialize(),
	        beforeSend: function() {
	        	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	        },
	        success: function(a) {
	        	if(a == 'OK') {
	        		alertaB('ATENCIÓN NEFROLÓGICA MENSUAL FINALIZADA CORRECTAMENTE...');
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

function calcularIMC() {
	var pesoseco = $('#pesoseco').val();
	var talla = $('#talla').val();
	var imc = 0;

	if(pesoseco !== "" && talla !== "") {
		imc = pesoseco/(talla*talla);
	}	

	$('#imc').val(imc);	
}

function inicializarTablaCies(cies) {
	$.ajax({
		url: "historiaclinica/inicializarTablaCies",
		data: {cies: cies},
		beforeSend: function() {
			$("#detallecie2").html('<tr><td colspan="2">Cargando...</td></tr>');
		}, 
	})
	.done(function(a) {
		$("#detallecie2").html(a);
	});    	
}

$(document).on('change', '#selectepo', function(event) {
	event.preventDefault();
	calcularCantidad("2", $("#f2").val(), 1);
});

$(document).on('change', '#selectcalcit', function(event) {
	event.preventDefault();
	calcularCantidad("9", $("#f9").val(), 1);
});

function calcularCantidad(valid, value, multi) {
	if(value!=="") {		
		if(valid=="2") {
			var selectepo = $("#selectepo").val();
			if(selectepo=="AMPOLLAS/SESION") {
				value = ({{$cantidadalmes}}*value);
			} else if (selectepo=="AMPOLLAS/SEMANA") {
				value = ({{(int)($cantidadalmes/($frecuencia==0?1:$frecuencia))}}*value);
			} else {
				value = (1*value);
			}
		} else if(valid=="9") {
			var selectcalcit = $("#selectcalcit").val();
			if(selectcalcit=="AMPOLLAS/SESION") {
				value = ({{$cantidadalmes}}*value);
			} else if (selectcalcit=="AMPOLLAS/SEMANA") {
				value = ({{(int)($cantidadalmes/($frecuencia==0?1:$frecuencia))}}*value);
			} else {
				value = (1*value);
			}
		} else {
			value = (multi*value);
		}
	}	
	$("#c"+valid).val(value);
}

@if($hc !== NULL)
	@if($hc->cadenacies!==""&&$hc->cadenacies!==NULL)
		inicializarTablaCies('{{ $hc->cadenacies }}');
		$("#cadenacies2").val('{{ $hc->cadenacies }}');
	@else
		@if($penultima!==NULL)
			inicializarTablaCies('{{ $penultima->cadenacies }}');
			$("#cadenacies2").val('{{ $penultima->cadenacies }}');
		@endif
	@endif	
@endif

function registrarReporteMedicamentos() {
	$.ajax({
	        type: "POST",
	        url: "consultamensual/registrarReporteMedicamentos",
	        data: $('#formMantenimiento{{ $entidad }}').serialize(),
	        beforeSend: function() {
	        	$("#btnGuardar3").prop('disabled', true).html('Cargando...');
	        },
	        success: function(a) {
	        	if(a == 'OK') {
	        		alertaB('MEDICAMENTOS INGRESADOS CORRECTAMENTE...');
	        	}else{
	        		alertaG('OCURRIÓ UN ERROR AL GUARDAR, VUELVA A INTENTAR...');
	        	}
	        	$("#btnGuardar3").prop('disabled', false).html('<i class="fa fa-check fa-lg"></i> Registrar Medicamentos');
	        },
			error: function() {
				alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
		    }
	    });
}

function consolidadoMedicamentos(pid) {
	window.open("reporte/consolidadoMedicamentos?id="+pid,"_blank");
}
</script>