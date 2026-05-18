<?php
	use Illuminate\Support\Facades\Auth;
	$usertype_id = Auth::user()->usertype_id;
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
		{{-- <small>Descripción</small> --}}
	</h1>
	{{--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Tables</a></li>
		<li class="active">Data tables</li>
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
								{!! Form::text('nombre', '', array('class' => 'form-control input-xs', 'id' => 'nombre', 'onkeyup' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('numero', 'Historia:') !!}
								{!! Form::text('numero', '', array('class' => 'form-control input-xs', 'id' => 'numero', 'onkeyup' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('baja', 'Dado de baja:') !!}
								{!! Form::select('baja', array("N" => "NO", "S" => "SI"), null, array('class' => 'form-control input-xs', 'id' => 'baja', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('estado', 'Estado:') !!}
								{!! Form::select('estado', array("" => "-- TODOS --", "1" => "ATENDIDOS", "2" => "NO ATENDIDOS"), null, array('class' => 'form-control input-xs', 'id' => 'estado', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('estado2', 'FUA:') !!}
								{!! Form::select('estado2', array("" => "-- TODOS --", "1" => "SI TIENE", "2" => "NO TIENEN"), null, array('class' => 'form-control input-xs', 'id' => 'estado2', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group" @if($usertype_id!==1&&$usertype_id!==2&&$usertype_id!==28&&$usertype_id!==34) style="display: none;" @endif>
								{!! Form::label('tipoconsulta', 'TIPO CONSULTA:') !!}
								{!! Form::select('tipoconsulta', array("" => "-- ELIGE UNA --", "1" => "NUTRICIONAL", "2" => "SALUD MENTAL", "3" => "SERVICIO SOCIAL"), $tipoconsulta, array('class' => 'form-control input-xs', 'id' => 'tipoconsulta', 'onchange' => 'buscar(\''.$entidad.'\'); tapar();')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('messs', 'Mes:') !!}
								{!! Form::select('messs', $messs, $mes, array('class' => 'form-control input-xs', 'id' => 'messs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('anooo', 'Año:') !!}
								{!! Form::selectRange('anooo', 2019, 2050, $year, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							{!! Form::button('<i class="fa fa-file"></i> Reporte Consolidado Excel', array('class' => 'btn btn-primary btn-xs', 'onclick' => 'consolidadoEvaluacionesExternasMensuales()')) !!}
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
		//buscar('{{ $entidad }}');
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
	});
	function consolidadoEvaluacionesExternasMensuales(){
        window.open("reporte/consolidadoEvaluacionesExternasMensuales?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="messs"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anooo"]').val()+"&formato="+$("#tipoconsulta").val(),"_blank");
    }
	setInterval(quitarPadding, 4000);	
</script>