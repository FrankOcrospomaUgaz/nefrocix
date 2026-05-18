<?php
use App\Lote;
use App\Stock;
use App\Kardex;
$js="";
?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($requerimiento, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('numerodocumento',str_pad($requerimiento->numero,8,'0',STR_PAD_LEFT), array('class' => 'form-control', 'id' => 'numerodocumento', 'readonly' => 'true')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::textarea('comentario', null, array('style' => 'resize: none;', 'rows' => '4','class' => 'form-control', 'id' => 'comentario', 'placeholder' => 'Ingrese comentario')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('fecha', date('d/m/Y'), array('class' => 'form-control', 'id' => 'fecha', 'readonly' => 'true')) !!}
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
		                    <th bgcolor="#E0ECF8" class='text-center'>Cantidad</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Presentacion</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Stock</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Despacho</th>
		                </tr>
		            </thead>
		            <tbody>
		            @foreach($detalles as $key => $value)
					<tr>
						<td class="text-center"><input type='hidden' value='<?=$value->producto->lote?>' id='txtTipo<?=$value->producto_id?>' />{!! $value->producto->nombre !!}</td>
						<td class="text-center">{!! $value->cantidad !!}</td>
						<td class="text-center">{!! $value->producto->presentacion->nombre !!}</td>
						<?php
						//$stock = Stock::where('producto_id','=',$value->producto_id)->where('almacen_id','=',1)->first();

						$ultimokardex = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('movimiento.almacen_id', '=',1)->orderBy('kardex.id', 'DESC')->where("producto_id", "=", $value->producto_id)->first();

						//ALMACEN 2 LOGISTICA
						if($value->producto->lote!="SI"){
							if(!is_null($ultimokardex)){
								$st = $ultimokardex->stockactual;
							}else{
								$st = 0;
							}
							echo "<td class='text-center'><input type='hidden' id='txtStock".$value->producto_id."' name='txtStock".$value->producto_id."' value='".$st."' />$st</td>";
							echo "<td align='center'><input type='text' data='numero' id='txtCantidad".$value->producto_id."' name='txtCantidad".$value->producto_id."' value='0' style='width: 100px;' class='form-control' /><td>";
							$js.="carro.push(".$value->producto_id.");";
						}else{
							$js.="carro.push(".$value->producto_id.");";
							$lote = Lote::where('producto_id','=',$value->producto_id)->where('almacen_id','=',1)->where('queda','>',0)->get();
							if(count($lote)>0){
								echo "<td class='text-center'>";
								$ls = "";
								foreach ($lote as $k => $v) {
									echo "<input type='hidden' id='txtStock".$value->producto_id."-".$v->id."' name='txtStock".$value->producto_id."-".$v->id."' value='".$v->queda."' />".date("d/m/Y",strtotime($v->fechavencimiento))." => ".$v->queda."<br />";
									$ls.="<div style='display:inline-flex'>".date("d/m/Y",strtotime($v->fechavencimiento))." => <input type='text' data='numero' id='txtCantidad".$value->producto_id."-".$v->id."' name='txtCantidad".$value->producto_id."-".$v->id."' value='0' style='width: 100px;' class='form-control' /></div><br />";
									$js.="carro2.push('".$v->id."');";
								}
								echo "</td><td align='center'>".$ls."</td>";
							}
						}
						?>
					</tr>
					@endforeach
		            </tbody>
		           
		        </table>
		    </div>
		</div>
	 </div>
    <br>
	
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'despachar(\''.$entidad.'\', this);')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('880');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
	$(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
});

function validar() {
	a = true;
	@for($i = 0; $i < count($detalles); $i++) 
		if(parseFloat($("#txtCantidad{{ $detalles[$i]->producto_id }}").val())!=""&&parseFloat($("#txtCantidad{{ $detalles[$i]->producto_id }}").val())!==0) {
			if({{ $detalles[$i]->cantidad }} < parseFloat($("#txtCantidad{{ $detalles[$i]->producto_id }}").val())) {
				a = false;
				//alert("CANTIDAD MENOR A PIDES");
			}
			if(parseFloat($("#txtCantidad{{ $detalles[$i]->producto_id }}").val()) > parseFloat($("#txtStock{{ $detalles[$i]->producto_id }}").val())) {
				a = false;
				//alert("CANTIDAD MAYOR A STOCK");
			}			
		} else {
			a = false;
			//alert("CERO O VACIO");
		}			
	@endfor
    return a;
}
function despachar() {
	if(!validar()) {
		alertaG("Corrige, no puedes dejar espacios en blanco, no puedes despachar más de lo que te han pedido y no puedes despachar más de lo que tienes en tu stock");
		return false;
	} else {		
		guardar("{{$entidad}}", $("#btnGuardar"));
	}	
}
<?php 
echo $js;
?>
</script>