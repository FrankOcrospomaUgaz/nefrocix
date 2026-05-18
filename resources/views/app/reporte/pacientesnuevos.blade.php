<?php 
	date_default_timezone_set('America/Lima');
?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		{{ $title }}
		{{-- <small>Descripción</small> --}}
	</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<div class="row">
						<div class="col-xs-12">
							{!! Form::open(['method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
						    
							<div class="form-group">
								{!! Form::label('mes', 'Mes:') !!}
								{!! Form::select('mes', $meses, date("m"), array('class' => 'form-control input-xs', 'id' => 'mes')) !!}
							</div>
                            <div class="form-group">
								{!! Form::label('anoo', 'Año:') !!}
								{!! Form::select('anoo', $anoos, date("Y"), array('class' => 'form-control input-xs', 'id' => 'anoo')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('tipo', 'Tipo:') !!}
								{!! Form::select('tipo', array('1'=>'SIS', '2'=>'ESSALUD'), null, array('class' => 'form-control input-xs', 'id' => 'tipo')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Reporte Excel', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar2', 'onclick' => 'registroMensualPacientesNuevos()')) !!}
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
	});
	function registroMensualPacientesNuevos(){
        window.open("reporte/registroMensualPacientesNuevos?mes="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="mes"]').val()+"&anno="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anoo"]').val()+"&tipo="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="tipo"]').val(),"_blank");
    }
</script>