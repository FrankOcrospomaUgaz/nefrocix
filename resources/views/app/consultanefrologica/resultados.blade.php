<?php

date_default_timezone_set('America/Lima');
$paciente = '';
$pid = '';
$direccion = '';
$sexo = '';
$numcita = 0;
$dni = '';
$afiliacion = '';
$telefono = '';
$ipress = '';
$dpto = '';
$provincia = '';
$distrito = '';

if($historia !== null) {
	$paciente = $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres;
	$pid = $historia->persona->id;
	$numcita = $historia->txtNumCita;
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

if(date("Y-m-d") >= date("Y-m-d", strtotime('2021-08-03'))) {
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
	.input-group-addon {
		font-size: 11px;
		padding: 2px;
		margin: 0;
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
                    DATOS DE FILIACIÓN DEL PACIENTE {{$hc->fecha}}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('txtPaciente', 'Paciente', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-10 col-md-10 col-sm-10">
                            {!! Form::text('txtPaciente', $paciente, array('class' => 'form-control input-sm', 'id' => 'txtPaciente', 'readonly')) !!}
								{!! Form::hidden('id1', $hc->id, array('id' => 'id1')) !!}
								{!! Form::hidden('persona_id', $pid, array('id' => 'persona_id')) !!}
								{!! Form::hidden('tipo', $situacion, array('id' => 'tipo')) !!}
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
                        {!! Form::label('txtSexo', 'Sexo', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-10 col-md-10 col-sm-10">
                            {!! Form::text('txtSexo', $sexo, array('class' => 'form-control input-sm', 'id' => 'txtSexo', 'readonly')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('txtNumCita', 'Número de Cita', array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::text('txtNumCita', $numcita, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtNumCita')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-8">
            <div class="panel panel-success">
                <div class="panel-heading">
                    REGISTRO DE RESULTADOS
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('txtTipo', 'Tipo de consulta de este mes:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::select('txtTipo', $tipos, $situacion, array('class' => 'form-control input-sm', 'id' => 'txtTipo', 'onchange'=>'CambiarTipoCampo(this.value);')) !!}
                        </div>
                        <div class="switch2">
                            {!! Form::label('txtDatosMensuales', 'Datos Mensuales', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <label class="switch">
                                    <input data-aa="0" id="cbxDatosMensuales" type="checkbox">
                                        <span class="slider round">
                                        </span>
                                    </input>
                                </label>
                            </div>
                        </div>
                        {!! Form::label('txtTipo2', 'Tipo de consulta del siguiente mes:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::select('txtTipo2', $tipos, $situacion2, array('class' => 'form-control input-sm', 'id' => 'txtTipo2')) !!}
                        </div>
                        {!! Form::hidden('txtDatosMensuales', 'NO', array('id' => 'txtDatosMensuales')) !!}
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12 col-md-12 col-sm-12 text-left">
                            <u style="color:blue;">
                                <b>
                                    Datos para cálculo de KTV
                                </b>
                            </u>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('txtFechaKTV', 'Fecha de Hemodiálisis (Toma de datos)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                        	<select name="txtFechaKTV" id="txtFechaKTV" class="form-control input-sm">
                        		<option value="">-- NO SELECCIONADO --</option>
                        		<?php echo $fechasHD; ?>
                        	</select>
                        </div>
                        {!! Form::label('txtHorasHemodialisisKTV', 'Tiempo HD. (Horas)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::text('txtHorasHemodialisisKTV', $horas, array('class' => 'form-control input-sm numerin', 'id' => 'txtHorasHemodialisisKTV')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <!--{!! Form::label('txtPesoInicial2KTV', 'Peso inicial (Kg.)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', "style" => "display:none;")) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::text('txtPesoInicial2KTV', $ppre, array('class' => 'form-control input-sm numerin', 'id' => 'txtPesoInicial2KTV', "style" => "display:none;")) !!}
                        </div>-->
                        {!! Form::label('txtPesoFinal2KTV', 'Peso final (Kg.)', array('class' => 'col-offset-6 col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            {!! Form::text('txtPesoFinal2KTV', $ppos, array('class' => 'form-control input-sm numerin', 'id' => 'txtPesoFinal2KTV')) !!}
                        </div>
                    </div>
                    <hr>
                        <div class="campito" id="NUEVO">
                            <div class="form-group">
                                <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                    <u>
                                        <b style="color:red">
                                            DATOS PARA NUEVO PACIENTE
                                        </b>
                                    </u>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('txtEliN', $examenesGeneral['86703'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtEliN', $hc->txtEli, array('class' => 'form-control input-sm requerido', 'id' => 'txtEliN')) !!}
                                </div>
                                {!! Form::label('txtDetN', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDetN', $hc->txtDet, array('class' => 'form-control input-sm requerido', 'id' => 'txtDetN')) !!}
                                </div>
                                {!! Form::label('txtDet2N', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDet2N', $hc->txtDet2, array('class' => 'form-control input-sm requerido', 'id' => 'txtDet2N')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('txtDet3N', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDet3N', $hc->txtDet3, array('class' => 'form-control input-sm requerido', 'id' => 'txtDet3N')) !!}
                                </div>
                                {!! Form::label('txtDet4N', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDet4N', $hc->txtDet4, array('class' => 'form-control input-sm requerido', 'id' => 'txtDet4N')) !!}
                                </div>
                                {!! Form::label('txtPruN', $examenesGeneral['86592'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtPruN', $hc->txtPru, array('class' => 'form-control input-sm requerido', 'id' => 'txtPruN')) !!}
                                </div>
                            </div>
                        </div>
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
                                    <div class="input-group">
                                        {!! Form::text('txtUre', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtUre')) !!}
                                        <span class="input-group-addon">
                                            mg/dl
                                        </span>
                                    </div>
                                </div>
                                {!! Form::label('txtUre2', 'Úrea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtUre2', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtUre2')) !!}
                                        <span class="input-group-addon">
                                            mg/dl
                                        </span>
                                    </div>
                                </div>
                                {!! Form::label('txtCre', $examenesGeneral['82565'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtCre', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtCre')) !!}
                                        <span class="input-group-addon">
                                            mg/dl
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('txtHem', $examenesGeneral['85014'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtHem', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtHem')) !!}
                                        <span class="input-group-addon">
                                            %
                                        </span>
                                    </div>
                                </div>
                                {!! Form::label('txtDos', $examenesGeneral['85018'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtDos', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtDos')) !!}
                                        <span class="input-group-addon">
                                            g/dl
                                        </span>
                                    </div>
                                </div>                                
                            </div>
                            <div class="form-group">
                            	{!! Form::label('txtSodio', 'Sodio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtSodio', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtSodio')) !!}
                                        <span class="input-group-addon">
                                            mmol/L
                                        </span>
                                    </div>
                                </div>
                            	{!! Form::label('txtPotasio', 'Potasio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtPotasio', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtPotasio')) !!}
                                        <span class="input-group-addon">
                                            mmol/L
                                        </span>
                                    </div>
                                </div>
                                {!! Form::label('txtCloro', 'Cloro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtCloro', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtCloro')) !!}
                                        <span class="input-group-addon">
                                            mmol/L
                                        </span>
                                    </div>
                                </div>                                
                            </div>
                            <div class="form-group">
                            	{!! Form::label('txtFos', $examenesGeneral['84100'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtFos', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtFos')) !!}
                                        <span class="input-group-addon">
                                            mg/dl
                                        </span>
                                    </div>
                                </div>
                                {!! Form::label('txtCal', $examenesGeneral['82310'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtCal', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtCal')) !!}
                                        <span class="input-group-addon">
                                            mg/dl
                                        </span>
                                    </div>
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
                                    {!! Form::text('txtTgo', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtTgo')) !!}
                                </div>
                                {!! Form::label('txtTgp', $examenesGeneral['84460'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    {!! Form::text('txtTgp', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtTgp')) !!}
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
                                {!! Form::label('txtPro', $examenesGeneral['84165'], array('class' => "col-lg-2 col-md-2 col-sm-2 control-label ". $oculto)) !!}
                                <div class="col-lg-4 col-md-4 col-sm-4 {{$oculto}}">
                                    <div class="input-group">
                                        {!! Form::text('txtPro', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtPro')) !!}
                                        <span class="input-group-addon">
                                            g/dl
                                        </span>
                                    </div>
                                </div>
                                {!! Form::label('txtFos2', $examenesGeneral['84075'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="input-group">
                                        {!! Form::text('txtFos2', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtFos2')) !!}
                                        <span class="input-group-addon">
                                            U/L
                                        </span>
                                    </div>
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
                                    {!! Form::text('txtEli', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtEli')) !!}
                                </div>
                                {!! Form::label('txtPru', $examenesGeneral['86592'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtPru', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtPru')) !!}
                                </div>
                                {!! Form::label('txtPar', $examenesGeneral['83970'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtPar', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtPar')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('txtDet', $examenesGeneral['87340'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDet', $hc->txtDet, array('class' => 'form-control input-sm requerido', 'id' => 'txtDet')) !!}
                                </div>
                                {!! Form::label('txtDet2', $examenesGeneral['86706'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDet2', $hc->txtDet2, array('class' => 'form-control input-sm requerido', 'id' => 'txtDet2')) !!}
                                </div>
                                {!! Form::label('txtDet3', $examenesGeneral['86704'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDet3', $hc->txtDet3, array('class' => 'form-control input-sm requerido', 'id' => 'txtDet3')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('txtDet4', $examenesGeneral['86803'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtDet4', $hc->txtDet4, array('class' => 'form-control input-sm requerido', 'id' => 'txtDet4')) !!}
                                </div>
                                {!! Form::label('txtHie', $examenesGeneral['83540'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtHie', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtHie')) !!}
                                </div>
                                {!! Form::label('txtFer', $examenesGeneral['82728'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    {!! Form::text('txtFer', null, array('class' => 'form-control input-sm requerido', 'id' => 'txtFer')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('txtSat', '% de Saturación de transferrina (opcional)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="input-group">
                                        {!! Form::text('txtSat', null, array('class' => 'form-control input-sm', 'id' => 'txtSat')) !!}
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
                                    <div class="input-group">
                                        {!! Form::text('txtAlbu', null, array('class' => 'form-control input-sm numerillo2 requerido', 'id' => 'txtAlbu')) !!}
                                        <span class="input-group-addon">
                                            g/dl
                                        </span>
                                    </div>
                                </div>
                                {!! Form::label('txtGlobu', 'Globulina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label '.$oculto)) !!}
                                <div class="col-lg-2 col-md-2 col-sm-2 {{$oculto}}">
                                    <div class="input-group">
                                        {!! Form::text('txtGlobu', null, array('class' => 'form-control input-sm numerillo2', 'id' => 'txtGlobu')) !!}
                                        <span class="input-group-addon">
                                            g/dl
                                        </span>
                                    </div>
                                </div>
                                <div id="ddatos">
                                    {!! Form::label('txtTransfe', $examenesGeneral['84466'], array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                        {!! Form::text('txtTransfe', "hola", array('class' => 'form-control input-sm numerillo', 'id' => 'txtTransfe')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </hr>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 text-right">
        {!! Form::button('
        <i class="fa fa-check fa-lg">
        </i>
        Guardar Configuración', array('class' => 'btn btn-info btn-sm', 'onclick' => 'configuracionmensual();')) !!}
			{!! Form::button('
        <i class="fa fa-check fa-lg">
        </i>
        Guardar Resultados', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarResultados();')) !!}
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
	CambiarTipoCampo('{{ $situacion }}');
	$('.numerillo').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 3 });	
	$('.numerillo2').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 3 });
	$("#txtTipo2").val("{{ $situacion2 }}");
	//alert("{{ $situacion2 }}");
	$("#txtFechaKTV").val("{{$atencion_id}}");
    $('#txtTransfe').val("{{($hc!==null)?$hc->txtTransfe:''}}");
});

$(document).on('click', '.switch', function(event) {
	event.preventDefault();
	event.stopImmediatePropagation();
	var a = false;
	var t = '0';
	var r = 'NO';
	var opt = '';
	var rep = "";
	$('#MENSUAL').addClass('hide');
	if($('#cbxDatosMensuales').attr('data-aa') == '0') {
		a = true;
		t = '1';
		r = 'SI';
		//muestro mensual
		$('#MENSUAL').removeClass('hide');
		opt += '<option value="BIMENSUAL" selected="selected">BIMENSUAL</option>';
		rep = "BIMENSUAL";
		
	} else {
		opt += '<option value="MENSUAL" selected="selected">MENSUAL</option>';
		rep = "MENSUAL";
	}
	//$('#txtTipo2').html(opt);
	$('#txtTipo2').val(rep);
	$('#cbxDatosMensuales').prop('checked', a);
	$('#cbxDatosMensuales').attr('data-aa', t);
	$('#txtDatosMensuales').val(r);
});

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
	}
});

function CambiarOpcionesCampo2(val) {
	/*var opt = '';
	if(val=='NUEVO' || val=='SEMESTRAL') {		
		//if($('#cbxDatosMensuales').attr('data-aa') == '1') {
		//	opt += '<option value="MENSUAL" selected="selected">BIMENSUAL</option>';
		//} else {
			opt += '<option value="MENSUAL" selected="selected">MENSUALES</option>';
		//}
	}
	if(val=='BIMENSUAL') {
		opt += '<option value="TRIMESTRAL">MENSUALES + TRIMESTRALES</option><option value="MENSUAL" selected="selected">MENSUALES</option>';
	}
	if(val=='MENSUAL') {
		opt += '<option value="BIMENSUAL">MENSUALES + BIMENSUALES</option><option value="SEMESTRAL">MENS. + BIMENS. + TRIMES. + SEMES.</option>';
	}
	if(val=='TRIMESTRAL') {
		opt += '<option value="BIMENSUAL">MENSUALES + BIMENSUALES</option>';
	}
	$('#txtTipo2').html(opt);*/
};

function CambiarTipoCampo(valor) {
	$('.campito').addClass("hide");
	$('#NUEVO').addClass('hide');
	$('#MENSUAL').removeClass('hide');
	$('#'+valor).removeClass('hide');
	$('.switch2').addClass('hide');
	$('#cbxDatosMensuales').prop('checked', false);
	$('#cbxDatosMensuales').attr('data-aa', '0');
	$('#txtDatosMensuales').val('NO');
	//$('#txtTransfe').val('');
	$('#ddatos').removeClass('hide');
	if(valor=='NUEVO') {
		$('#MENSUAL').addClass('hide');
		$('.switch2').removeClass('hide');
	}
	//if(valor=='SEMESTRAL'||valor=='NUEVO') {
	if(valor=='SEMESTRAL') {
		$('#BIMENSUAL').removeClass('hide');
		$('#TRIMESTRAL').removeClass('hide');
		$('#SEMESTRAL').removeClass('hide');
	}
	//if(valor=='TRIMESTRAL'||valor=='SEMESTRAL'||valor=='NUEVO') {
	if(valor=='TRIMESTRAL'||valor=='SEMESTRAL') {
		$('#OTROS').removeClass('hide');
	}
	if(valor=='TRIMESTRAL') {
		//$('#txtTransfe').val('-');
		$('#ddatos').addClass('hide');
	}
	$('.requerido').removeClass('requerido2');
	CambiarOpcionesCampo2(valor);
}

function validarInputs() {
	var a = true;
	var txtTipo = $('#txtTipo').val();
	var txtDatosMensuales = $('#txtDatosMensuales').val();
	if(txtTipo!=="NUEVO") {
		$('#MENSUAL .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
	} if(txtTipo=="BIMENSUAL"||txtTipo=="SEMESTRAL") {
		$('#BIMENSUAL .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
	} if(txtTipo=="TRIMESTRAL"||txtTipo=="SEMESTRAL") {
		$('#TRIMESTRAL .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
	//} if(txtTipo=="SEMESTRAL"||txtTipo=="NUEVO") {
	} if(txtTipo=="SEMESTRAL") {
		$('#BIMENSUAL .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
		$('#TRIMESTRAL .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
		$('#SEMESTRAL .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});		
	//} if(txtTipo=="SEMESTRAL"||txtTipo=="TRIMESTRAL"||txtTipo=="NUEVO") {
	} if(txtTipo=="SEMESTRAL"||txtTipo=="TRIMESTRAL") {
		$('#OTROS .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
	} if(txtTipo=="NUEVO") {
		$('#NUEVO .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
	} if(txtTipo=="NUEVO"&&txtDatosMensuales=="SI") {
		$('#MENSUAL .requerido').each(function(index, el) {
			if($(this).val().length==0) {
	        	a = false;
	        	$(this).addClass('requerido2');
			} else {
				$(this).removeClass('requerido2');
			}
		});
	}
	return a;
}

function registrarResultados() {
	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	if($("#txtNumCita").val()=="") {
		a = 'Corrige los campos en rojo y vuelve a enviar.';
		alertaG(a);
		$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Registrar');
		$("#txtNumCita").addClass('requerido2');
		return false;
	} else if(!validarInputs()) {
		a = 'Corrige los campos en rojo y vuelve a enviar.';
		alertaG(a);
		$("#btnGuardar").prop('disabled', false).html('<i class="glyphicon glyphicon-check"></i> Registrar');
		$("#txtNumCita").removeClass('requerido2');
		return false;
	} else {
		$.ajax({
	        type: "POST",
	        url: "consultanefrologica/storeresultados",
	        data: $('#formMantenimiento{{ $entidad }}').serialize(),
	        beforeSend: function() {
	        	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	        },
	        success: function(a) {
	        	if(a == 'OK') {
	        		alertaB('RESULTADOS REGISTRADOS CORRECTAMENTE...');
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

function configuracionmensual() {
	$.ajax({
        type: "POST",
        url: "consultanefrologica/storeconfiguracionmensual",
        data: $('#formMantenimiento{{ $entidad }}').serialize(),
        success: function(a) {
        	if(a == 'OK') {
        		alertaB('CONFIGURACIÓN CORRECTA...');
        		buscar('ConsultaNefrologica');
        	}else{
        		alertaG('OCURRIÓ UN ERROR AL CONFIGURAR, VUELVA A INTENTAR...');
        	}
        },
		error: function() {
			alertaG('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
	    }
    });
}

@if($hc->txtDatosMensuales == 'SI')

	$('#cbxDatosMensuales').prop('checked', true);
	$('#cbxDatosMensuales').attr('data-aa', '1');
	$('#txtDatosMensuales').val('SI');
	$('#MENSUAL').removeClass('hide');

@endif


@if($hc !== null)
	//CambiarOpcionesCampo2('{{ $hc->situacion }}');
	$("#tipo2").val("{{ $situacion2 }}");
@endif
</script>