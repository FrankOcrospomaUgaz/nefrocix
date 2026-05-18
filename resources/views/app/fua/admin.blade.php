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

<?php 

date_default_timezone_set('America/Lima');
$fecha0 = date('Y-m-d');

?>

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
								{!! Form::label('fecha', 'Fecha:') !!}
								{!! Form::date('fecha', $fecha0, array('class' => 'form-control input-xs', 'id' => 'fecha__')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('paciente', 'Paciente:') !!}
								{!! Form::text('paciente', null, array('class' => 'form-control input-xs', 'id' => 'paciente', "onkeyup"=>"buscar('Fua')")) !!}
							</div>
							<div class="form-group">
								{!! Form::label('estado', 'Estado:') !!}
								{!! Form::select('estado', array(""=>"--- TODOS ---", "F"=>"FINALIZADO", "A"=>"ATENDIÉNDOSE", "C"=>"CANCELADO", "P"=>"PENDIENTE", "N"=>"AUSENTE"), null, array('class' => 'form-control input-xs', 'id' => 'estado', "onchange"=>"buscar('Fua')")) !!}
							</div>
							<div class="form-group">
								{!! Form::label('estado2', 'FUA:') !!}
								{!! Form::select('estado2', array(""=>"--- TODOS ---", "1"=>"SI TIENEN", "2"=>"NO TIENEN"), null, array('class' => 'form-control input-xs', 'id' => 'estado2', "onchange"=>"buscar('Fua')")) !!}
							</div>
							<div class="form-group">
								{!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo', array('class' => 'btn btn-info btn-xs', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-refresh"></i> Refrescar', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnBuscar2', 'onclick' => 'comprobarCitasDiaEspecifico()')) !!}
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
	});

	function comprobarCitasDiaEspecifico() {
        $.ajax({
            type: "GET",
            url: "fua/comprobarCitasDiaEspecifico",
            data: "fecha="+$("#fecha__").val(),
            beforeSend:function() {
            	$('#listadoFua').html('Cargando, por favor espere...');
            },
            success: function(a) {
                if(a == "OK"){
                    buscar('Fua');            
                }else{
                    alert("Error al refrescar");
                }
            }
        });
	}
</script>