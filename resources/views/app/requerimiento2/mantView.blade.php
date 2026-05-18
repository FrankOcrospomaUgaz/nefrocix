<?php
use App\Lote;
?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($requerimiento, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="col-lg-3 col-md-3 col-sm-3">
		<div class="form-group">
			{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('numerodocumento',str_pad($requerimiento->numero,8,'0',STR_PAD_LEFT), array('class' => 'form-control', 'id' => 'numerodocumento', 'readonly' => 'true')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3">
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				<div class='input-group' id='divfecha'>
					{!! Form::text('fecha', $requerimiento==NULL?date('d-m-Y'):date('d-m-Y', strtotime($requerimiento->fecha)), array('class' => 'form-control', 'id' => 'fecha', 'readonly' => 'true')) !!}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			<div class="col-lg-9 col-md-9 col-sm-9">
				{!! Form::textarea('comentario', null, array('style' => 'resize: none;', 'rows' => '3','class' => 'form-control', 'id' => 'comentario', 'readonly' => 'true')) !!}
			</div>
		</div>		
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center'>Producto</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Proveedor</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Tipo</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Cant. Solicitada</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Precio</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Subtotal</th>
		                </tr>
		            </thead>
		            <tbody>
		            @if(count($detalles) > 0)
			            @foreach($detalles as $key => $value)
						<tr>
							<td class="text-center">{!! $value->producto->nombre !!}</td>
							<td class="text-center">{!! $value->proveedor==NULL?"-":$value->proveedor->bussinesname !!}</td>
							<td class="text-center">{!! $value->tipo !!}</td>
							<td class="text-center">{!! $value->cantidad !!}</td>
							<td class="text-center">{!! $value->precio !!}</td>
							<td class="text-center">{!! $value->subtotal !!}</td>
						</tr>
						@endforeach
					@else
						<tr>
							<td class="text-center" colspan="6">Aún no se cargó el stock</td>
						</tr>
					@endif
		            </tbody>
		           
		        </table>
		    </div>
		</div>
	 </div>
    <br>
    <h4 style="color: red;">Datos de Carga a Stock</h4>
    @if($requerimiento!=NULL)
    	@if($requerimiento->movimiento!=NULL)
			<div class="col-lg-3 col-md-3 col-sm-3">
				<div class="form-group">
					{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
					<div class="col-lg-7 col-md-7 col-sm-7">
						{!! Form::text('numerodocumento',str_pad($requerimiento->movimiento->numero,8,'0',STR_PAD_LEFT), array('class' => 'form-control', 'id' => 'numerodocumento', 'readonly' => 'true')) !!}
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3">
				<div class="form-group">
					{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
					<div class="col-lg-7 col-md-7 col-sm-7">
						<div class='input-group' id='divfecha'>
							{!! Form::text('fecha', date("d-m-Y", strtotime($requerimiento->movimiento->fecha)), array('class' => 'form-control', 'id' => 'fecha', 'readonly' => 'true')) !!}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<div class="form-group">
					{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
					<div class="col-lg-9 col-md-9 col-sm-9">
						{!! Form::textarea('comentario', $requerimiento->movimiento->comentario, array('style' => 'resize: none;', 'rows' => '3','class' => 'form-control', 'id' => 'comentario', 'readonly' => 'true')) !!}
					</div>
				</div>		
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div id="divDetail" class="table-responsive" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
				        <table style="width: 100%;" class="table-condensed table-striped">
				            <thead>
				                <tr>
				                    <th bgcolor="#E0ECF8" class='text-center'>Producto</th>
				                    <th bgcolor="#E0ECF8" class='text-center'>Proveedor</th>
				                    <th bgcolor="#E0ECF8" class='text-center'>Tipo</th>
				                    <th bgcolor="#E0ECF8" class='text-center'>Cant. Adquirida</th>
				                    <th bgcolor="#E0ECF8" class='text-center'>Precio</th>
				                    <th bgcolor="#E0ECF8" class='text-center'>Subtotal</th>
				                </tr>
				            </thead>
				            <tbody>
				            @if(count($requerimiento->movimiento->detalles) > 0)
					            @foreach($requerimiento->movimiento->detalles as $key => $value2)
								<tr>
									<td class="text-center">{!! $value2->producto->nombre !!}</td>
									<td class="text-center">{!! $value2->proveedor==NULL?"-":$value2->proveedor->bussinesname !!}</td>
									<td class="text-center">{!! $value2->tipo !!}</td>
									<td class="text-center">{!! $value2->cantidad !!}</td>
									<td class="text-center">{!! $value2->precio !!}</td>
									<td class="text-center">{!! $value2->subtotal !!}</td>
								</tr>
								@endforeach
							@else
								<tr>
									<td class="text-center" colspan="6">Aún no se cargó el stock</td>
								</tr>
							@endif
				            </tbody>
				           
				        </table>
				    </div>
				</div>
			 </div>
		@else
			Aún no se cargó el stock
		@endif
	@else
	Aún no se cargó el stock
	@endif	
	<br>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1500');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
}); 

</script>