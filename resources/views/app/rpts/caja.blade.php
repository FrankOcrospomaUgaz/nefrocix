<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		{{ $title }}
		{{-- <small>Descripci�n</small> --}}
	</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					{!! Form::open(['method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					<div class="row">						
						<div class="col-xs-12">
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
						    
							<div class="form-group">
								{!! Form::label('fechainicial', 'Fecha Inicial:') !!}
								{!! Form::date('fechainicial', date('Y-m-d',strtotime("now",strtotime("-1 week"))), array('class' => 'form-control input-sm', 'id' => 'fechainicial')) !!}
							</div>
                            <div class="form-group">
								{!! Form::label('fechafinal', 'Fecha Final:') !!}
								{!! Form::date('fechafinal', date('Y-m-d'), array('class' => 'form-control input-sm', 'id' => 'fechafinal')) !!}
							</div>

							<div class="form-group" @if(Auth::user()->usertype_id != 1) style="display: none;" @endif id="cajas"></div>
						</div>
					</div>	
					<hr>					
					<div class="com-xs-12">
						<div class="form-group">
							{!! Form::button('<i class="glyphicon glyphicon-export"></i> Consolidado PDF', array('class' => 'btn btn-sm btn-danger', 'onclick' => 'imprimirDetalleF(\'\')')) !!}						

							{!! Form::button('<i class="glyphicon glyphicon-export"></i> Por cajas PDF', array('class' => 'btn btn-sm btn-danger', 'onclick' => 'imprimirDetalleF(\'2\')')) !!}
							<!--{!! Form::button('<i class="glyphicon glyphicon-export"></i> Consolidado Excel', array('class' => 'btn btn-sm btn-success','onclick' => 'pdfDetalleCierreExcelF(\'\')')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-export"></i> Por cajas Excel', array('class' => 'btn btn-sm btn-success','onclick' => 'pdfDetalleCierreExcelF(\'2\')')) !!}		-->					
							@if($user->usertype_id==1 || $user->usertype_id==14 || $user->usertype_id==8)
								<!--{!! Form::button('<i class="glyphicon glyphicon-print"></i> Movilidad', array('class' => 'btn btn-sm btn-warning', 'onclick' => 'imprimirMovilidadF()')) !!}-->
								<!--{! Form::button('<i class="glyphicon glyphicon-export"></i> Excel', array('class' => 'btn btn-sm btn-success', 'onclick' => 'imprimirExcelF()')) !!}-->
								{!! Form::button('<i class="glyphicon glyphicon-arrow-up"></i> Egresos', array('class' => 'btn btn-sm btn-info', 'onclick' => 'egresosExcel()')) !!}
							@endif
							@if($user->usertype_id==1 || $user->usertype_id==23)
								<!--{!! Form::button('<i class="glyphicon glyphicon-arrow-up"></i> Detalle de Egresos', array('class' => 'btn btn-sm btn-info','onclick' => 'pdfDetalleEgresos()')) !!}-->
							@endif
							<!--@if($user->usertype_id==1 || $user->usertype_id==11 )
								{!! Form::button('<i class="glyphicon glyphicon-print"></i> Ventas Por Producto Individual', array('class' => 'btn btn-sm btn-primary', 'id' => 'btnBuscar', 'onclick' => 'detallePorProducto();')) !!}
								{!! Form::button('<i class="glyphicon glyphicon-print"></i> Ventas Por Producto Agrupado, Convenio y Particular', array('class' => 'btn btn-sm btn-info', 'id' => 'btnBuscar', 'onclick' => 'detallePorProductoAgrupado();')) !!}				
							@endif-->
							<!--
							{! Form::button('<i class="glyphicon glyphicon-export"></i> Exportar Excel', array('class' => 'btn btn-sm btn-success','onclick' => 'pdfDetalleCierreExcelF()')) !!}
							-->							
					</div>

					<hr>
					<!--<div class="col-xs-12">		
						<div class="form-group">
							{!! Form::label('prodcto', 'Producto:') !!}
						</div>	
						<div class="form-group">
							<select name="prodcto" id="prodcto" class='form-control input-sm'>
								<option value="">&nbsp;&nbsp;&nbsp;&nbsp;------------Todos------------&nbsp;&nbsp;&nbsp;&nbsp;</option>
								@if(count($productos)>0)
									@foreach($productos as $key => $producto)
										<option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
									@endforeach
								@endif
							</select>
						</div>					
						{!! Form::button('<i class="glyphicon glyphicon-print"></i> Por Lote, Stock, F. Vencimiento', array('class' => 'btn btn-sm btn-warning', 'id' => 'btnBuscar', 'onclick' => 'pdfDetallePorLoteStockFV();')) !!}
					</div>-->
					
					{!! Form::close() !!}
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
	$(document).ready(function($) {
		$('#prodcto').chosen({
			width: '100%'
		});
	});
	function cajas(){
		$.ajax({
			type:'GET',
			url:"rpts/cajas",
			data:'',
			success: function(a) {
				$('#cajas').html(a);
			}
		});
	}

	cajas();

	function imprimirDetalleF(tipo){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        if ($('#Medico').val() != 6 && $('#Medico').val() != 7) {
        	window.open('caja/pdfDetalleCierreF' + tipo + '?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        } else {
        	@if($user->usertype_id==1 || $user->usertype_id==14 || $user->usertype_id==8)
        		window.open('cajatesoreria/pdfDetalleCierreF' + tipo + '?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        	@endif
        }
    }

	@if($user->usertype_id==1 || $user->usertype_id==11 )
		
	function detallePorProducto(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		window.open('caja/pdfDetallePorProducto?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
	}

	function detallePorProductoAgrupado(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		window.open('caja/pdfDetallePorProductoAgrupado?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
	}
	
	function pdfDetallePorLoteStockFV(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		var producto_id = $('#prodcto').val();
		window.open('caja/pdfDetallePorLoteStockFV?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff+'&producto_id='+producto_id,"_blank");
	}

	@endif

    function imprimirMovilidadF(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        if ($('#Medico').val() != 6) {
        	//window.open('caja/pdfDetalleCierreF?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        } else {
        	window.open('cajatesoreria/pdfMovilidadF?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        }
    }

	function pdfDetalleCierreExcelF(tipo){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        //if ($('#Medico').val() != 6) {
        	//window.open('caja/pdfDetalleCierreF?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        //} else {
        	window.open('caja/pdfDetalleCierreExcelF' + tipo + '?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        //}
    }

    function pdfDetalleEgresos(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        window.open('caja/pdfDetalleEgresos?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
    }

    function egresosExcel(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
      	window.open('cajatesoreria/egresosExcel?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
    }

	function Genera(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		if (ff != "") {
			var med = '';
			if ($('#Medico').val() != null) {
				med = '&med='+$('#Medico').val();
			}
			var link = 'reporte.php?rep=6&fi='+fi+'&ff='+ff+''+med;
			var link2 = 'reporte.php?rep=61&fi='+fi+'&ff='+ff+'';
			if($('#Medico').val() != 4){
				window.open(link,'_blank');
			} else {
				window.open(link2,'_blank');
			}
		}
	}
</script>