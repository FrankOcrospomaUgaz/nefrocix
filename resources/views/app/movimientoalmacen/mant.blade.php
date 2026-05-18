<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($movimientoalmacen, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('total', '0', array( 'id' => 'total')) !!}
	{!! Form::hidden('producto_id', null, array( 'id' => 'producto_id')) !!}
	{!! Form::hidden('tienelote', null, array( 'id' => 'tienelote')) !!}
	{!! Form::hidden('pfraccion', null, array( 'id' => 'pfraccion')) !!}
	{!! Form::hidden('stock', null, array('id' => 'stock')) !!}
	<input type="hidden" name="cantproductos" id="cantproductos" value="0">
	<div class="col-lg-4 col-md-4 col-sm-4">
		<div class="form-group" style="display: none;">
			{!! Form::label('almacen_id', 'Almacen :', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::select('almacen_id', $cboAlmacen, null, array('style' => 'background-color: #D4F0FF;' ,'class' => 'form-control input-sm', 'id' => 'almacen_id', 'onclick' => 'generarNumero(this.value);')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::select('tipo', $cboTipo, 8, array('style' => 'background-color: #D4F0FF;' ,'class' => 'form-control input-sm', 'id' => 'tipo', 'onclick' => 'generarNumero(this.value);')) !!}
			</div>
		</div>
		<div class="form-group" id="divDescuentokayros">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				<div id='divfecha'>
					{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control input-sm', 'id' => 'fecha', 'placeholder' => 'Ingrese fecha')) !!}						
				</div>
			</div>
		</div>
		
		<div class="form-group">
			{!! Form::label('numerodocumento', 'Nro.:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('numerodocumento', "-", array('class' => 'form-control input-sm', 'id' => 'numerodocumento', 'placeholder' => 'numerodocumento', 'readonly' => 'readonly')) !!}
			</div>

		</div>		
		<div id="zona_ingreso">
			<div class="form-group">
				{!! Form::label('documento_id', 'Documento:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-4 col-md-4 col-sm-4">
					{!! Form::select('documento_id', array(""=>"SIN DOCUMENTO", "FACTURA"=>"FACTURA", "BOLETA"=>"BOLETA", "GUÍA DE REMISIÓN" => "GUÍA DE REMISIÓN"), null, array('style' => 'background-color: #D4F0FF;' ,'class' => 'form-control input-sm', 'id' => 'documento_id')) !!}
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4">
					{!! Form::text('documento_num', null, array('class' => 'form-control input-sm', 'id' => 'documento_num', 'placeholder' => 'Nro Doc.')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('documento_rs', 'Nro RS:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-8 col-md-8 col-sm-8">
					{!! Form::text('documento_rs', null, array('class' => 'form-control input-sm', 'id' => 'documento_rs', 'placeholder' => 'Nro Registro Sanitario')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('documento_fecha_rs', 'Fecha Venc RS:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-8 col-md-8 col-sm-8">
					{!! Form::date('documento_fecha_rs', null, array('class' => 'form-control input-sm', 'id' => 'documento_fecha_rs', 'placeholder' => 'Fecha Reg San')) !!}
				</div>
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::textarea('comentario', null, array('class' => 'form-control input-sm', 'id' => 'comentario', 'placeholder' => 'comentario', "rows"=>"3")) !!}
			</div>
		</div>
	</div>


	<div class="col-lg-8 col-md-8 col-sm-8">
		<div class="form-group">
			<div class="col-lg-2 col-md-2 col-sm-2"></div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				<div class="form-group">
					{!! Form::label('nombreproducto', 'Producto:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
					<div class="col-lg-9 col-md-9 col-sm-9">
						{!! Form::text('nombreproducto', null, array('class' => 'form-control input-sm', 'id' => 'nombreproducto', 'placeholder' => 'Ingrese nombre')) !!}
					</div>
					<div class="col-lg-1 col-md-1 col-sm-1">
		                {!! Form::button('<i class="glyphicon glyphicon-plus"></i>', array('class' => 'btn btn-info btn-sm', 'onclick' => 'modal (\''.URL::route('producto.create', array('listar'=>'SI','modo'=>'popup')).'\', \'Nuevo Producto\', this);', 'title' => 'Nuevo Producto')) !!}
		    		</div>			
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2"></div>
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div id="divProductos" style="overflow:auto; height:160px; border:1px outset">
					<table class='table-condensed' border='1' id="tablaProductos" width="100%">
						<thead>
							<tr>
								<th class='text-center' style='width:60%;'><span style='display: block; font-size:.9em'>Nombre</span></th>
								<th class='text-center' style='width:30%;'><span style='display: block; font-size:.9em'>Presentación</span></th>
								<th class='text-center' style='width:10%;'><span style='display: block; font-size:.9em'>Stock</span></th>
							</tr>
						</thead>
						<tbody id='tablaProducto'>
							<tr><td align='center' colspan='3' style="color:blue;">Digite 3 caracteres o más para buscar.</td></tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<h6 style="color: blue;" id="mensajeproducto">Elige un Medicamento</h6>
				</div>
			</div>
			<div class="form-group" id="datosproducto">				
				<div class="col-lg-3 col-md-3 col-sm-3">
					<div class="form-group">
						{!! Form::label('cantidad', 'Cantidad:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
						<div class="col-lg-8 col-md-8 col-sm-8">
							{!! Form::text('cantidad', null, array('class' => 'form-control input-sm numero', 'id' => 'cantidad', 'placeholder' => 'Ingrese cantidad', 'onkeyup' => "javascript:this.value=this.value.toUpperCase();")) !!}
						</div>
					</div>				
				</div>
				<div class="col-lg-5 col-md-5 col-sm-5" id="divfechavencimiento">
					<div class="form-group">
						{!! Form::label('fechavencimiento', 'F. Vencimiento:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
						<div class="col-lg-7 col-md-7 col-sm-7">
							{!! Form::date('fechavencimiento', null, array('class' => 'form-control input-sm', 'id' => 'fechavencimiento', 'placeholder' => 'Ingrese fechavencimiento')) !!}
						</div>
					</div>				
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3">
					{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarMovimiento(\''.$entidad.'\', this)')) !!}
					{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8">

		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				<!--<div align="center" class="col-lg-3 ">
		       {-- Form::button('<i class="glyphicon glyphicon-plus"></i> Agregar', array('class' => 'btn btn-info btn-xs', 'id' => 'btnAgregar', 'onclick' => 'ventanaproductos();')) --}   
		    	
		    	</div>-->
				
			</div>
		</div>
		
	</div>
	<div class="form-group" style="display: none;">
		<div class="col-lg-12 col-md-12 col-sm-12" >
			{!! Form::label('codigo', 'Comprobar Productos:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-5 col-md-5 col-sm-5">
				{!! Form::text('codigo', null, array('class' => 'form-control input-sm', 'id' => 'codigo', 'placeholder' => 'Ingrese codigo')) !!}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:220px; width: 100%; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped" border="1">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center'>N°</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:580px;">Producto</th>
		                    <th bgcolor="#E0ECF8" class='text-center fvencimientos' style="width:95px;">F. Vencim.</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:95px;">Lote</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:95px;">Cantidad</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:95px;">Precio Unit</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:90px;">Subtotal</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Quitar</th>
		                </tr>
		            </thead>
		            <tbody id="detallesMovimiento">
		            </tbody>
		            <tbody border="1">
		            	<tr>
		            		<th id="colspantotaltotal" colspan="6" style="text-align: right;">TOTAL</th>
		            		<td class="text-center">
		            			<center id="totalmovimiento2">0.00</center><input type="hidden" id="totalmovimiento" readonly="" name="totalmovimiento" value="0.00">
		            		</td>
		            	</tr>
		            </tbody>
		        </table>
		    </div>
		</div>
	
{!! Form::close() !!}
<style type="text/css">
tr.resaltar {
    background-color: #D4F0FF;
    cursor: pointer;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1300');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');

	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });

	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cantidad"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="codigo"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				comprobarproducto ();
			}
		});

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="cantidad"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				e.preventDefault();
				if($('#tipo').val() == '9') {
					addpurchasecart();
				} else {
					var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
					inputs.eq( inputs.index(this)+ 1 ).focus();
				}					
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="fechavencimiento"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				if($(this).val() == '') {
					return false;
					$(this).focus();
				}
				addpurchasecart();				
				indice = -1;				
			}
		});

	var indice=-1;

	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').on( 'keyup', function () {
        var e = window.event; 
        var keyc = e.keyCode || e.which;        
        if(this.value.length>2 && keyc == 13){
            buscarProducto(this.value);
            $(this).val("");
        }
        if(keyc == 38 || keyc == 40 || keyc == 13) {
            var tabladiv='tablaProducto';
			var child = document.getElementById(tabladiv).rows;			
			var cantfilas = child.length;
			if(cantfilas>0) {
				// abajo
				if(keyc == 40) {					
					if (indice<(cantfilas-1)) {
						indice++;
						pintarfila(child[indice].id);
					}
				// arriba
				} else if(keyc == 38) {					
					if (indice>0) {
						indice--;
						pintarfila(child[indice].id);
					}
				} else if(keyc == 13) {
					if (indice!=-1) {						
						seleccionarProducto(child[indice].id);
						indice=-1;						
					}					
				}
			}						
        } if(keyc == 27) {
        	$(".escogerFila").removeClass("resaltar");
        	indice=-1;
        }
    });

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').focus();

	generarNumero(8);
}); 

function pintarfila(elemento) {
	$(".escogerFila").removeClass("resaltar");
	$('#'+elemento).addClass("resaltar");
}

function buscarProducto(valor){
    if(valor.length >= 3){
        $.ajax({
            type: "POST",
            url: "venta/buscandoproducto",
            data: "nombre="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                datos=JSON.parse(a);
                $("#divProductos").css("overflow-x",'hidden');
                var pag=parseInt($("#pag").val());
                var d=0;
                var a = '';
                if(datos.length > 0) {
	                for(c=0; c < datos.length; c++){
	                	//Algoritmo para stock
	                	var stock = datos[c].stock;
	                	if(datos[c].fraccion != 1) {
	                		var pres1 = 1;
	                		pres1 = Math.trunc(parseFloat(datos[c].stock)/parseFloat(datos[c].fraccion));
	                		entero = parseFloat(pres1);
	                		pres2 = parseFloat(datos[c].stock) - entero*parseFloat(datos[c].fraccion);
	                		stock = pres1.toString() + 'F' + pres2.toString();
	                	}
	                    a+="<tr class='escogerFila' style='cursor:pointer;' id='"+datos[c].idproducto+"' onclick=\"seleccionarProducto('"+datos[c].idproducto+"')\"><td><span style='display: block; font-size:.9em'>"+datos[c].nombre+"</span></td><td align='right'><span style='display: block; font-size:.9em'>"+datos[c].presentacion+"</span></td><td align='right'><span style='display: block; font-size:.9em'>"+stock+"</span></td></tr>";	                               
	                }
	            } else {
	            	a +="<tr><td align='center' colspan='3' style='color:red'>Medicamento no encontrado.</td></tr>";
	            }
	            $("#tablaProducto").html(a);
    	    }
        });
    } else {
    	$("#tablaProducto").html("<tr><td align='center' colspan='3' style='color:blue'>Digite 3 caracteres o más para buscar.</td></tr>");
    }
}

function seleccionarProducto(idproducto){
	var _token =$('input[name=_token]').val();
	$.post('{{ URL::route("venta.consultaproducto")}}', {idproducto: idproducto,_token: _token} , function(data){
		var datos = data.split('@');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="producto_id"]').val(datos[0]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="stock"]').val(datos[3]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cantidad"]').focus();
		$("#mensajeproducto").html("Elegiste el medicamento: "+datos[7]);
		pintarfila(datos[0]);
	});	
}


function abrirconvenios() {
	modal('{{URL::route('venta.buscarconvenio')}}', '');
}


function generarNumero(valor){
    $.ajax({
        type: "POST",
        url: "venta/generarNumeroDocMovAlmacen",
        data: "tipodocumento_id="+valor+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val()+"&almacen_id="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="almacen_id"]').val(),
        success: function(a) {
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numerodocumento"]').val(a);
            $(".escogerFila").removeClass('resaltar');
            if($("#tipo").val()=="8") {
            	$("#divfechavencimiento").show();
            	$(".fvencimientos").show();
            	$("#zona_ingreso").show();
            	$("#colspantotaltotal").removeAttr('colspan').attr("colspan", "6");
            } else {
            	$("#divfechavencimiento").hide();
            	$(".fvencimientos").hide();
            	$("#zona_ingreso").hide();
            	$("#divfechavencimiento").val("");
            	$("#colspantotaltotal").removeAttr('colspan').attr("colspan", "5");
            }
        }
    });
}

function retornarFloat (value) {
	var retorno = 0.00;
	value       = value.replace(',','');
	if(value.trim() === ''){
		retorno = 0.00; 
	}else{
		retorno = parseFloat(value)
	}
	return retorno;
}

$(document).on('click', '.quitarFila', function(event) {
	event.preventDefault();
	$(this).parent('span').parent('td').parent('tr').remove();
	calculatetotal();
});

function calculatetotal () {
	var i = 1;
	var total = 0;
	$('#detallesMovimiento tr .numeration3').each(function() {
		$(this).html(i);
		i++;
	});
	i = 1;

	$('#detallesMovimiento tr .infoProducto').each(function() {
		$(this).find('.producto_id').attr('name', '').attr('name', 'producto_id' + i);
		$(this).find('.productonombre').attr('name', '').attr('name', 'productonombre' + i);
		$(this).find('.fechavencimiento').attr('name', '').attr('name', 'fechavencimiento' + i);
		$(this).find('.cantidad').attr('name', '').attr('name', 'cantidad' + i);
		$(this).find('.precio').attr('name', '').attr('name', 'precio' + i);
		$(this).find('.precioventa').attr('name', '').attr('name', 'precioventa' + i);
		$(this).find('.subtotal').attr('name', '').attr('name', 'subtotal' + i);
		total += parseFloat($(this).find('.subtotal').val());
		i++;
	});
	$('#cantproductos').val(i-1);
	$('#totalmovimiento2').html(parseFloat(total).toFixed(2));
	$('#totalmovimiento').val(parseFloat(total).toFixed(2));
}

function comprobarproducto () {
	var _token =$('input[name=_token]').val();
	var valor =$('input[name=codigo]').val();
	$.post('{{ URL::route("venta.comprobarproducto")}}', {valor: valor,_token: _token} , function(data){
		
		if (data.trim() == 'NO') {
			$('input[name=codigo]').val('');
			bootbox.alert("Este Producto no esta en lista de venta");
            setTimeout(function () {
                $('#codigo').focus();
            },2000) 
		}else{
			$('input[name=codigo]').val('');
			$('#codigo').focus();
		}
	});
}

function guardarMovimiento (entidad, idboton) {
	if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="fecha"]').val()==""){
		alert("Debe ingresar una fecha");
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="fecha"]').focus();
		return false;
	} else {
		var total = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalmovimiento"]').val();
		var mensaje = '<h3 align = "center">Total = '+total+'</h3>';
		bootbox.confirm({
			message : mensaje,
			buttons: {
				'cancel': {
					label: 'Cancelar',
					className: 'btn btn-default btn-sm'
				},
				'confirm':{
					label: 'Aceptar',
					className: 'btn btn-success btn-sm'
				}
			}, 
			callback: function(result) {
				if (result) {
					var idformulario = IDFORMMANTENIMIENTO + entidad;
					var data         = submitForm(idformulario);
					var respuesta    = '';
					var listar       = 'NO';
					
					var btn = $(idboton);
					btn.button('loading');
					data.done(function(msg) {
						respuesta = msg;
					}).fail(function(xhr, textStatus, errorThrown) {
						respuesta = 'ERROR';
					}).always(function() {
						btn.button('reset');
						if(respuesta === 'ERROR'){
						}else{
							var dat = JSON.parse(respuesta);
				            if(dat[0]!==undefined){
				                resp=dat[0].respuesta;    
				            }else{
				                resp='VALIDACION';
				            }
				            
							if (resp === 'OK') {
								cerrarModal();
				                buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');				                
							} else if(resp === 'ERROR') {
								alert(dat[0].msg);
							} else {
								mostrarErrores(respuesta, idformulario, entidad);
							}
						}
					});
				};
			}            
		}).find("div.modal-content").addClass("bootboxConfirmWidth");
		setTimeout(function () {
			if (contadorModal !== 0) {
				$('.modal' + (contadorModal-1)).css('pointer-events','auto');
				$('body').addClass('modal-open');
			}
		},2000);
	}
}

$(document).on('click', '.escogerFila', function(){
	$('.escogerFila').removeClass("resaltar");
	$(this).addClass("resaltar");
});

function addpurchasecart(elemento = 'N') {
	var cantidad = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cantidad"]').val();
	cantidad = cantidad.replace(",", "");
	var product_id = $('#producto_id').val();
	var fechavencimiento = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="fechavencimiento"]').val();
	var stock = $('#stock').val();
	var tipo = $('#tipo').val();

	var _token =$('input[name=_token]').val();
	if(parseFloat(stock) == 0 && tipo == "9"){
		bootbox.alert("<center><h3 style='color:red'>No tienes stock</h3></center>");
            setTimeout(function () {
                $('#cantidad').focus();
            },5000) 
	} else if(parseFloat(cantidad.trim()) > parseFloat(stock) && tipo == "9"){
		bootbox.alert("<center><h3 style='color:red'>No puedes sacar más de lo que tienes en stock</h3></center>");
            setTimeout(function () {
                $('#cantidad').focus();
            },5000) 
	} else if(cantidad.trim() === ''||cantidad.trim() === 0){
		bootbox.alert("<center><h3 style='color:red'>Ingrese Cantidad</h3></center>");
            setTimeout(function () {
                $('#cantidad').focus();
            },5000) 
	} else if(fechavencimiento.trim() === '' && tipo == "8"){
		bootbox.alert("<center><h3 style='color:red'>Ingrese Fecha Vencimiento</h3></center>");
        setTimeout(function () {
            $('#fechavencimiento').focus();
        },5000) 
	} else{
		$.post('{{ URL::route("movimientoalmacen.agregarcarritomovimientoalmacen")}}', {cantidad: cantidad, producto_id: product_id, tipo: tipo, fechavencimiento: fechavencimiento, _token: _token,tipo:tipo,stock:stock, elemento: elemento} , function(data) {
			if(data === '0-0') {
				bootbox.alert("<center><h3 style='color:red'>No es un formato válido de cantidad</h3></center>");
				$('#cantidad').val('').focus();
				return false;
			} else if(data === '0-1') {
				bootbox.alert("<center><h3 style='color:red'>No puedes sacar más de lo que tienes en stock</h3></center>");
				$('#cantidad').val('').focus();
				return false;
			} else {
				var producto_id = $('#producto_id').val();
				if ($("#Product" + producto_id)[0]) {
					$("#Product" + producto_id).html(data);
				} else {
					$('#detallesMovimiento').append('<tr id="Product' + producto_id + '">' + data + '</tr>');
				}	
				$("#Product" + producto_id).css('display', 'none').fadeIn(1000);		
				calculatetotal();
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').val('');
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="cantidad"]').val('');
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="fechavencimiento"]').val('');
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').focus();
				$('.escogerFila').css('background-color', 'white');
				$("#mensajeproducto").html("Elige un Medicamento");
			}
		});
	}
}
</script>