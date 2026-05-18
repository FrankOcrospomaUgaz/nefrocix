@if($historia!==NULL)
<form method="#" accept-charset="UTF-8" class="form-horizontal">
	<div class="panel-group">
		<!--<button type="button" class="close closdat" onclick="cerrarModal();" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>-->
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-warning">
			  		<div class="panel-heading" style="height: 45px;">
			  			<div class="row">
							<div class="col-lg-10 col-md-10 col-sm-10">
								<div class="form-group">
									&nbsp;&nbsp;RESULTADOS DEL PACIENTE {{strtoupper($historia->persona->apellidopaterno." ".$historia->persona->apellidomaterno." ".$historia->persona->nombres)}} - <font style="color: red; font-weight: bold;" id="anillo2">{{$anno}}</font>
								</div>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2">
								<div class="form-group text-right">
									{!! Form::label('txtYear2', 'AÑO', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
									<div class="col-lg-10 col-md-10 col-sm-10">
										{!! Form::select('txtYear2', $years, $anno, array('class' => 'form-control input-sm', 'id' => 'txtYear2', 'onchange' => 'cargaTablaverhistorialResultadosPorPaciente();')) !!}								
									</div>
								</div>
							</div>
						</div>
			  		</div>
			    	<div class="panel-body">
			    		<table width="100%" height="100%">
			    			<thead>
			    				<tr>
			    					<th width="25%" style="text-align:center; vertical-align: middle;" rowspan="2" colspan="2">EXÁMENES DE LABORATORIO</th>
			    					<th width="75%" style="text-align:center;" colspan="13">RESULTADOS</th>
			    				</tr>
			    				<tr>
			    					<th>ENERO</th>
			    					<th>FEBRERO</th>
			    					<th>MARZO</th>
			    					<th>ABRIL</th>
			    					<th>MAYO</th>
			    					<th>JUNIO</th>
			    					<th>JULIO</th>
			    					<th>AGOSTO</th>
			    					<th>SETIEMBRE</th>
			    					<th>OCTUBRE</th>
			    					<th>NOVIEMBRE</th>
			    					<th>DICIEMBRE</th>
			    				</tr>
			    			</thead>
			    			<tbody id="tablaverconsolidadoResultados"></tbody>
			    		</table>
			    	</div>
			  	</div>
			</div>
		</div>		  	
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>	
</form>
<script>
	$(document).ready(function() {
		configurarAnchoModal('1300');
	});
</script>
@else
    <h3>HA SELECCIONADO UNA HISTORIA QUE NO EXISTE O ESTÁ DE BAJA</h3>
@endif

<script>
    
    $(document).ready(function() {
        configurarAnchoModal('1300');
        cargaTablaverhistorialResultadosPorPaciente();     
    });
    function cargaTablaverhistorialResultadosPorPaciente() {
        $.ajax({
            url: 'consultamensual/cargaTablaverhistorialResultadosPorPaciente?historia_id={{$historia->id}}&anno='+$("#txtYear2").val(),
            type: 'GET',
            dataType: "JSON",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            beforeSend: function() {
                $("#tablaverconsolidadoResultados").html("<td colspan='14'>Cargando datos...</td>");
            },
            success: function(a) {
                $("#tablaverconsolidadoResultados").html(a["ret"]);
                $("#anillo2").html($("#txtYear2").val());
            },
        })
    }
    
</script>