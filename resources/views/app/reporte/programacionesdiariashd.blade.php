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
							<div class="form-group">
								<div class="col-lg-1 col-md-1 col-sm-1">
									{!! Form::label('de', 'Fecha:') !!}
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2">
									{!! Form::date('fechai', $hoy, array('class' => 'form-control input-sm', 'id' => 'fechai')) !!}
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2">
									{!! Form::date('fechaf', $hoy, array('class' => 'form-control input-sm', 'id' => 'fechaf')) !!}
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2">
									{!! Form::select('convenio_id', array("1"=>"SIS", "2"=>"ESSALUD"), null, array('class' => 'form-control input-sm', 'id' => 'convenio_id')) !!}
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3">
									{!! Form::button('<i class="glyphicon glyphicon-print"></i> Reporte Programaciones Diarias', array('class' => 'btn btn-sm btn-success', 'onclick' => 'reporteProgramacionesDiariasHD();')) !!}
								</div>
							</div>
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
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');	
		$('#prodcto').chosen({
			width: '100%'
		});
		$('#prodcto2').chosen({
			width: '100%'
		});	
	});

    function reporteProgramacionesDiariasHD(){
        window.open("reporte/reporteProgramacionesDiariasHD?fechai="+$("#fechai").val()+"&fechaf="+$("#fechaf").val()+"&convenio_id="+$("#convenio_id").val(),"_blank");
    }
</script>