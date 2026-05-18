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
							<div class="form-group">
							{!! Form::label('prodcto', 'Stock de Productos:') !!}
							</div>	
							<div class="form-group">
								<div class="col-lg-6 col-md-6 col-sm-6">
									<select name="prodcto" id="prodcto" class='form-control input-sm' onchange="consultarStock2();">
										<option value="">------------ Todos los productos ------------</option>
										@if(count($productos)>0)
											@foreach($productos as $key => $producto)
												<option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
											@endforeach
										@endif
									</select>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2">
									<b style="color: blue; font-size: 18px;" id="stockin">-</b>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3">
									{!! Form::button('<i class="glyphicon glyphicon-print"></i> Reporte STOCK', array('class' => 'btn btn-sm btn-success', 'onclick' => 'consultarStock();')) !!}
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
							{!! Form::label('prodcto2', 'Kardex de Productos:') !!}
							</div>	
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
								<div class="col-lg-4 col-md-4 col-sm-4">
									<select name="prodcto2" id="prodcto2" class='form-control input-sm'>
										<option value="">------------ Todos los productos ------------</option>
										@if(count($productos)>0)
											@foreach($productos as $key => $producto)
												<option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
											@endforeach
										@endif
									</select>
								</div>	
								<div class="col-lg-3 col-md-3 col-sm-3">
									{!! Form::button('<i class="glyphicon glyphicon-print"></i> Reporte KARDEX por movimiento', array('class' => 'btn btn-sm btn-warning', 'onclick' => 'consultarKardex();')) !!}
								</div>							
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-xs-12">
							<div class="form-group">
							{!! Form::label('prodcto2', 'Resumen de Kardex de Productos por año:') !!}
							</div>	
							<div class="form-group">
								<div class="col-lg-1 col-md-1 col-sm-1">
									{!! Form::label('de', 'Año:') !!}
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2">
									{!! Form::selectRange('anual', 2019, 2060, date("Y"), array('class' => 'form-control input-sm', 'id' => 'anual')) !!}
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3">
									{!! Form::button('<i class="glyphicon glyphicon-print"></i> Reporte KARDEX consolidado Producto', array('class' => 'btn btn-sm btn-danger', 'onclick' => 'consultarKardexConsolidado();')) !!}
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

	function consultarStock2() {
		$("#stockin").html("-");
		if($("#prodcto").val() !== "") {
        	$.ajax({
				url: 'reporte/consultarStockNumero?id='+$("#prodcto").val(),
				type: 'GET',
				dataType: 'JSON',
				beforeSend: function() {
					$("#stockin").html("Cargando...");
				},
				success: function(e) {
					$("#stockin").html(e.cantidad + " UNIDADES");
				}
			});
        }
	}

    function consultarStock(){
        window.open("reporte/consultarStock?id="+$("#prodcto").val(),"_blank");
    }

    function consultarKardex(){
        window.open("reporte/consultarKardex?id="+$("#prodcto2").val()+"&fechai="+$("#fechai").val()+"&fechaf="+$("#fechaf").val(),"_blank");
    }

    function consultarKardexConsolidado(){
        window.open("reporte/consultarKardexConsolidado?id="+$("#prodcto2").val()+"&anual="+$("#anual").val(),"_blank");
    }
</script>