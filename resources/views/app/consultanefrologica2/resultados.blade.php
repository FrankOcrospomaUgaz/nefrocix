<?php

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

if($historia !== null) {
	$paciente = $historia->
persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres;
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
                    DATOS DE FILIACIÓN DEL PACIENTE
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('txtPaciente', 'Paciente', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                        <div class="col-lg-10 col-md-10 col-sm-10">
                            {!! Form::text('txtPaciente', $paciente, array('class' => 'form-control input-sm', 'id' => 'txtPaciente', 'readonly')) !!}
								{!! Form::hidden('id1', $hc->id, array('id' => 'id1')) !!}
								{!! Form::hidden('persona_id', $pid, array('id' => 'persona_id')) !!}
								{!! Form::hidden('tipo', $tipo, array('id' => 'tipo')) !!}
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
                    <div class="campito" id="MENSUAL">
                        <div class="form-group">
                            {!! Form::label('txtUre', 'Úrea Pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtUre', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtUre')) !!}
                                    <span class="input-group-addon">
                                        mg/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtUre2', 'Úrea Post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtUre2', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtUre2')) !!}
                                    <span class="input-group-addon">
                                        mg/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtCre', 'Creatinina pre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtCre', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtCre')) !!}
                                    <span class="input-group-addon">
                                        mg/dl
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtLg1', 'AcHBC - lg M', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtLg1', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtLg1')) !!}
                                    <!--<span class="input-group-addon">mg/dl</span>-->
                                </div>
                            </div>
                            {!! Form::label('txtLg2', 'AcHBC - lg O', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtLg2', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtLg2')) !!}
                                    <!--<span class="input-group-addon">mg/dl</span>-->
                                </div>
                            </div>
                            {!! Form::label('txtCre2', 'Creatinina post', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtCre2', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtCre2')) !!}
                                    <span class="input-group-addon">
                                        mg/dl
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtHem', 'Hematocrito', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtHem', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtHem')) !!}
                                    <span class="input-group-addon">
                                        %
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtDos', 'Dosaje de hemoglobina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtDos', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtDos')) !!}
                                    <span class="input-group-addon">
                                        g/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtSodio', 'Sodio', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtSodio', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtSodio')) !!}
                                    <span class="input-group-addon">
                                        mmol/L
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtFos', 'Fósforo en sangre', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtFos', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtFos')) !!}
                                    <span class="input-group-addon">
                                        mg/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtCal', 'Calcio sérico', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtCal', null, array('class' => 'form-control input-sm numerillo requerido', 'id' => 'txtCal')) !!}
                                    <span class="input-group-addon">
                                        mg/dl
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
                        </div>
                        <!-- -->
                        <div class="form-group">
                            {!! Form::label('txtCal2', 'Calcio corregido', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtCal2', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtCal2')) !!}
                                    <span class="input-group-addon">
                                        mg/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtTgo', 'TGO', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtTgo', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtTgo')) !!}
                            </div>
                            {!! Form::label('txtTgp', 'TGP', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtTgp', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtTgp')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtCloro', 'Cloro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtCloro', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtCloro')) !!}
                                    <span class="input-group-addon">
                                        mmol/L
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtPro', 'Proteínas Totales', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtPro', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtPro')) !!}
                                    <span class="input-group-addon">
                                        g/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtFos2', 'Fosfatasa Alcalina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtFos2', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtFos2')) !!}
                                    <span class="input-group-addon">
                                        U/L
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtVacu1', 'Vacunación para HVB 1', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::date('txtVacu1', null, array('class' => 'form-control input-sm', 'id' => 'txtVacu1')) !!}
                            </div>
                            {!! Form::label('txtVacu2', 'Vacunación para HVB 2', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::date('txtVacu2', null, array('class' => 'form-control input-sm', 'id' => 'txtVacu2')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtVacu3', 'Vacunación para HVB 3', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::date('txtVacu3', null, array('class' => 'form-control input-sm', 'id' => 'txtVacu3')) !!}
                            </div>
                            {!! Form::label('txtVacu4', 'Refuerzo para HVB', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::date('txtVacu4', null, array('class' => 'form-control input-sm', 'id' => 'txtVacu4')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtNeumo', 'Vacunación para Neumococo', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                {!! Form::date('txtNeumo', null, array('class' => 'form-control input-sm', 'id' => 'txtNeumo')) !!}
                            </div>
                        </div>
                        <!-- -->
                    </div>
                    <div class="campito" id="TRIMESTRAL">
                        <div class="form-group">
                        </div>
                    </div>
                    <div class="campito" id="SEMESTRAL">
                        <div class="form-group">
                            {!! Form::label('txtEli', 'ELISA o prueba rápida para HIV-1 y HIV-2', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtEli', null, array('class' => 'form-control input-sm', 'id' => 'txtEli')) !!}
                            </div>
                            {!! Form::label('txtPru', 'Prueba de sífilis cualitativa (VDRL, RPR)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPru', null, array('class' => 'form-control input-sm', 'id' => 'txtPru')) !!}
                            </div>
                            {!! Form::label('txtPar', 'Paratohormona (PTH)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPar', null, array('class' => 'form-control input-sm', 'id' => 'txtPar')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtDet', 'Detección de antígeno de superficie de virus de Hepatitis B (HBsAg) por ELISA', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet', $hc->txtDet, array('class' => 'form-control input-sm', 'id' => 'txtDet')) !!}
                            </div>
                            {!! Form::label('txtDet2', 'Detección de anticuerpos para antígeno de superficie Hepatitis B (HBs-Ag)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet2', $hc->txtDet2, array('class' => 'form-control input-sm', 'id' => 'txtDet2')) !!}
                            </div>
                            {!! Form::label('txtDet3', 'Detección de anticuerpos totales para núcleo de virus de Hepatitis B (Total Anti-Hbcore)', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet3', $hc->txtDet3, array('class' => 'form-control input-sm', 'id' => 'txtDet3')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtDet4', 'Determinación de anticuerpos para Hepatitis C', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtDet4', $hc->txtDet4, array('class' => 'form-control input-sm', 'id' => 'txtDet4')) !!}
                            </div>
                            {!! Form::label('txtHie', 'Hierro sérico', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtHie', null, array('class' => 'form-control input-sm', 'id' => 'txtHie')) !!}
                            </div>
                            {!! Form::label('txtFer', 'Ferritina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtFer', null, array('class' => 'form-control input-sm', 'id' => 'txtFer')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('txtSat', 'Saturación de transferrina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtSat', null, array('class' => 'form-control input-sm', 'id' => 'txtSat')) !!}
                                    <span class="input-group-addon">
                                        %
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtPcr', 'PCR', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtPcr', null, array('class' => 'form-control input-sm', 'id' => 'txtPcr')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="campito" id="OTROS">
                        <div class="form-group">
                            {!! Form::label('txtAlbu', 'Albúmina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtAlbu', null, array('class' => 'form-control input-sm numerillo2', 'id' => 'txtAlbu')) !!}
                                    <span class="input-group-addon">
                                        g/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtGlobu', 'Globulina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group">
                                    {!! Form::text('txtGlobu', null, array('class' => 'form-control input-sm numerillo2', 'id' => 'txtGlobu')) !!}
                                    <span class="input-group-addon">
                                        g/dl
                                    </span>
                                </div>
                            </div>
                            {!! Form::label('txtTransfe', 'Transferrina', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                {!! Form::text('txtTransfe', null, array('class' => 'form-control input-sm numerillo', 'id' => 'txtTransfe')) !!}
                            </div>
                        </div>
                    </div>
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
        '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarResultados2();')) !!}
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
	$('.numerillo').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 3 });	
	$('.numerillo2').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 3 });
    $("#txtFechaKTV").val("{{$atencion_id}}");
});

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
	}
});

function registrarResultados2() {
	$("#btnGuardar").prop('disabled', true).html('Cargando...');
	$.ajax({
        type: "POST",
        url: "consultanefrologica2/storeresultados",
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
</script>