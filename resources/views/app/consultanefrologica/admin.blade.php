<?php
	use Illuminate\Support\Facades\Auth;
	$usertype_id = Auth::user()->
usertype_id;
	$user_person_id = Auth::user()->person_id;
	$tipoconsulta = "";
	if($usertype_id==35) {
		$tipoconsulta = "1";
	} else if($usertype_id==36) {
		$tipoconsulta = "2";
	} else if($usertype_id==37) {
		$tipoconsulta = "3";
	}
	date_default_timezone_set('America/Lima');
    $mes = date('m');
    $year = date('Y');
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $title }}
		{{--
        <small>
            Descripción
        </small>
        --}}
    </h1>
    {{--
    <ol class="breadcrumb">
        <li>
            <a href="#">
                <i class="fa fa-dashboard">
                </i>
                Home
            </a>
        </li>
        <li>
            <a href="#">
                Tables
            </a>
        </li>
        <li class="active">
            Data tables
        </li>
    </ol>
    --}}
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-xs-12">
                            {!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
                            <div class="form-group">
                                {!! Form::label('nombre', 'Nombre:') !!}
								{!! Form::text('nombre', '', array('class' => 'form-control input-xs', 'id' => 'nombre')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('numero', 'Historia:') !!}
								{!! Form::text('numero', '', array('class' => 'form-control input-xs', 'id' => 'numero')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('baja', 'Dado de baja:') !!}
								{!! Form::select('baja', array("N" => "NO", "S" => "SI"), null, array('class' => 'form-control input-xs', 'id' => 'baja', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('examenes', 'Examenes:') !!}
								{!! Form::select('examenes', array("" => "-- TODOS --", "1" => "SI", "2" => "NO"), null, array('class' => 'form-control input-xs', 'id' => 'examenes', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('estado', 'Estado:') !!}
								{!! Form::select('estado', array("" => "-- TODOS --", "1" => "ATENDIDOS", "2" => "NO ATENDIDOS"), null, array('class' => 'form-control input-xs', 'id' => 'estado', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('estado2', 'FUA:') !!}
								{!! Form::select('estado2', array("" => "-- TODOS --", "1" => "SI TIENE", "2" => "NO TIENEN"), null, array('class' => 'form-control input-xs', 'id' => 'estado2', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('programacion', 'Programación mensual:') !!}
								{!! Form::select('programacion', array("" => "-- TODOS --", "1" => "PROGRAMADO", "2" => "NO PROGRAMADO"), null, array('class' => 'form-control input-xs', 'id' => 'programacion', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('messs', 'Mes:') !!}
								{!! Form::select('messs', $messs, $mes, array('class' => 'form-control input-xs', 'id' => 'messs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('medico_id2', 'Médico:') !!}
                                <select class="form-control input-xs" id="medico_id2" name="medico_id2" onchange="buscar('{{$entidad}}');">
                                    <option value="">
                                        --TODOS--
                                    </option>
                                    @if(count($docts))
										@foreach($docts as $dt)
                                    <option value="{{$dt->id}}">
                                        {{ $dt->apellidopaterno." ".$dt->apellidomaterno." ".$dt->nombres }}
                                    </option>
                                    @endforeach
									@endif
                                </select>
                            </div>
                            <div class="form-group">
                                {!! Form::label('anooo', 'Año:') !!}
								{!! Form::selectRange('anooo', 2019, 2050, $year, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                            </div>
                            {!! Form::button('
                            <i class="glyphicon glyphicon-search">
                            </i>
                            Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							{!! Form::button('
                            <i class="glyphicon glyphicon-print">
                            </i>
                            Examenes Mensuales', array('class' => 'btn btn-warning btn-xs', 'onclick' => 'analisisMensualesKtvTru();')) !!}
							{!! Form::button('
                            <i class="glyphicon glyphicon-print">
                            </i>
                            Programación Medicamentos', array('class' => 'btn btn-danger btn-xs', 'onclick' => 'programacionMensualMedicamentos();')) !!}
							{!! Form::button('
                            <i class="glyphicon glyphicon-print">
                            </i>
                            Consolidado Mensual de Medicamentos', array('class' => 'btn btn-info btn-xs', 'onclick' => 'consolidadoMedicamentosTodosPacientes();')) !!}
							{!! Form::button('
                            <i class="glyphicon glyphicon-print">
                            </i>
                            Consolidado Mensual de Datos Clínicos - Facturación', array('class' => 'btn btn-success btn-xs', 'onclick' => 'consolidadoDatosClinicosCalculoKTVHBPorPaciente();')) !!}
							{!! Form::button('
                            <i class="glyphicon glyphicon-print">
                            </i>
                            Consolidado Mensual de Duración de Sesiones HD - Facturación', array('class' => 'btn btn-primary btn-xs', 'onclick' => 'consolidadoDuracionSesiones();')) !!}
							{!! Form::button('
                            <i class="glyphicon glyphicon-cog">
                            </i>
                            Configurar Tipo Resultados de este Mes', array('class' => 'btn btn-success btn-xs', "id" => "btnConfFF", 'onclick' => 'confTodasConsultas();')) !!}
							{!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" id="listado{{ $entidad }}">
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
<script>
    $(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombre"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="numero"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		@if($usertype_id==28||$usertype_id==29)
			$("#medico_id2").val("{{$user_person_id}}");
		@endif
	});

	setInterval(quitarPadding, 4000);

	function confTodasConsultas() {
		$.ajax({
			url: "consultanefrologica/confTodasConsultas?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="messs"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anooo"]').val(),
			beforeSend: function() {
				$("#btnConfFF").val('Cargando...').attr("disabled", true);
			},
		})
		.done(function() {
			buscar("{{ $entidad }}");
			$("#btnConfFF").val("<i class='glyphicon glyphicon-cog'></i> Configurar Consultas de este Mes").attr("disabled", false);
		});
		
	}

	function analisisMensualesKtvTru(){
        window.open("reporte/analisisMensualesKtvTru?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="messs"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anooo"]').val(),"_blank");
    }

    function programacionMensualMedicamentos(){
        window.open("reporte/programacionMensualMedicamentos?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="messs"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anooo"]').val(),"_blank");
    }

    function consolidadoMedicamentosTodosPacientes() {
    	window.open("reporte/consolidadoMedicamentosTodosPacientes?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="messs"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anooo"]').val(),"_blank");
    }

    function consolidadoDatosClinicosCalculoKTVHBPorPaciente() {
    	window.open("reporte/consolidadoDatosClinicosCalculoKTVHBPorPaciente?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="messs"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anooo"]').val(),"_blank");
    }
    function consolidadoDuracionSesiones() {
    	window.open("reporte/consolidadoDuracionSesiones?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="messs"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anooo"]').val(),"_blank");
    }
</script>