<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($requerimiento, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('listProducto', null, array('id' => 'listProducto')) !!}
	{!! Form::hidden('listaProducto', null, array('id' => 'listaProducto')) !!}
	{!! Form::hidden('total', '0', array( 'id' => 'total')) !!}
	<div class="col-lg-4 col-md-4 col-sm-4">
		<div class="form-group">
			<div class="form-group">
				{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control', 'id' => 'fecha', 'placeholder' => 'Ingrese fecha', 'readonly' => 'true')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::text('numerodocumento', "-", array('class' => 'form-control', 'id' => 'numerodocumento', 'placeholder' => 'numerodocumento', "readonly")) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::textarea('comentario', null, array('class' => 'form-control', 'rows' => '6', 'id' => 'comentario', 'placeholder' => 'comentario')) !!}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8">
		<div class="form-group">
			{!! Form::label('nombreproducto', 'Producto:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-5 col-md-5 col-sm-5">
				{!! Form::text('nombreproducto', null, array('class' => 'form-control', 'id' => 'nombreproducto', 'placeholder' => 'Ingrese nombre','onkeyup' => 'buscarProducto3(this.value);')) !!}
			</div>
			{!! Form::hidden('producto_id', null, array( 'id' => 'producto_id')) !!}
			{!! Form::hidden('precioventa', null, array('id' => 'precioventa')) !!}
			{!! Form::hidden('stock', null, array('id' => 'stock')) !!}
		</div>

		<div class="form-group" id="divProductos" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
			<table class='table-condensed table-hover' width="100%" border='1'>
				<thead>
					<tr>
						<th class='text-center' style='width:630px;'><span style='display: block;'>Nombre</span></th>
						<th class='text-center' style='width:300px;'><span style='display: block;'>Presentacion</span></th>
					</tr>
				</thead>
				<tbody id='tablaProducto'>
					<tr><td align='center' colspan='8'>Digite más de 3 caracteres.</td></tr>
				</tbody>
			</table>
		</div>

		<div class="form-group">
			<div class="col-lg-8 col-md-8 col-sm-8 text-left">
				<font class="btn btn-default text-right" style="color: red; font-size: 20px;">
					TOTAL: <b id="totalGeneral">0.00</b>
				</font>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 text-right">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success', 'id' => 'btnGuardar', 'onclick' => '$(\'#listProducto\').val(carro);guardarRequerimiento2(\''.$entidad.'\', this)')) !!}
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
		
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:100%; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped" border="1" id="tbDetalle">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:5%;">Item</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:30%;">Producto</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:30%;">Proveedor</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:10%;">Tipo</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:7%;">Cantidad</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:7%;">PrecUnit</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:7%;">Subtot.</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:4%;">Quitar</th>
		                </tr>
		            </thead>
		           	<tbody id="detallesCompra">
		            </tbody>
		            <tr id="ceromedicamentos">
		            	<td colspan="8"><center style="color:red;">Escoja al menos un medicamento.</center></td>
		            </tr>
		        </table>
		    </div>
		</div>
	 </div>
    <br>

{!! Form::close() !!}
<style type="text/css">
	tr.resaltar {
	    background-color: #A9F5F2;
	    cursor: pointer;
	}
	.requerido2 { 
		border: 1px solid #f00; 
		background-color: #FFD6CE;
		color: red;
	}
</style>
<script type="text/javascript">
var valorbusqueda="";
var indice = -1;
var anterior = -1;
var carro = new Array();
$(document).ready(function() {
	configurarAnchoModal('1500');
	$(".closdat").remove();
    $(".modal-title").append('<button type="button" class="close closdat" onclick="$(this).parent().parent().parent().parent().parent().modal(\'hide\')" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');		
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="codigo"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				comprobarproducto ();
			}
		});

	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').on( 'keydown', function () {
        var e = window.event; 
        var keyc = e.keyCode || e.which;
        if(this.value.length>2 && keyc == 13 && valorbusqueda!=this.value){
            buscarProducto3(this.value);
            valorbusqueda=this.value;
            this.focus();
            return false;
        }
        if(keyc == 38 || keyc == 40 || keyc == 13 || keyc == 27) {
            var tabladiv='tablaProducto';
			var child = document.getElementById(tabladiv).rows;
			var i=0;
			if(keyc == 27) {			
			     if(indice != -1){
					var seleccionado = '';			 
					if(child[indice].id) {
					   seleccionado = child[indice].id;
					} else {
					   seleccionado = child[indice].id;
					}		 		
					seleccionarProducto(seleccionado);
				}
			} else {
				// abajo
				if(keyc == 40) {
					if(indice == (child.length - 1) ) {
					   indice = 1;
					} else {
					   if(indice==-1) indice=0;
	                   indice=indice+1;
					} 
				// arriba
				} else if(keyc == 38) {
					indice = indice - 1;
					if(indice==0) indice=-1;
					if(indice < 0) {
						indice = (child.length - 1);
					}
				}	
				
				child[indice].className = child[indice].className+' tr_hover';

				if (indice != -1) {
					var element = '#'+child[indice].id;
					$(element).addClass("resaltar");
					if (anterior  != -1) {
						element = '#'+anterior;
						$(element).removeClass("resaltar");
					}
					anterior = child[indice].id;
				}
			}
        }
    });


	//cambiotipoventa();
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').focus();

	$("#ceromedicamentos").show();

	@if($requerimiento != NULL)

	@foreach($requerimiento->detalles as $detallito)

	seleccionarProducto("{{$detallito->producto_id}}", "{{$detallito->producto->nombre}}", "{{$detallito->producto->presentacion_id}}", "{{$detallito->producto->presentacion->nombre}}", "{{$detallito->producto->preciocompra==NULL||$detallito->producto->preciocompra==''?0.00:$detallito->producto->preciocompra}}");

	@endforeach

	@endif

});

var valorinicial="";
function buscarProducto3(valor){
    if(valor.length >= 3){
        $.ajax({
            type: "POST",
            url: "requerimiento/buscarproducto",
            data: "nombre="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                datos=JSON.parse(a);
                //$("#divProductos").html("<table class='table-condensed table-hover' border='1' id='tablaProducto'><thead><tr><th class='text-center' style='width:220px;'><span style='display: block; font-size:.9em'>Nombre</span></th><th class='text-center' style='width:70px;'><span style='display: block; font-size:.9em'>Presentacion</span></th></tr></thead></table>");
                $("#divProductos").css("overflow-x",'hidden');
                var pag=parseInt($("#pag").val());
                var d=0;
                var a = '';
                if(datos.length > 0) {
	                for(c=0; c < datos.length; c++){
	                	var nombre = datos[c].nombre;
	                	nombre = nombre.replace('"','@');
	                    a += "<tr style='cursor:pointer' class='escogerFila' id='"+datos[c].idproducto+"' onclick=\"seleccionarProducto('"+datos[c].idproducto+"','"+nombre+"','"+datos[c].presentacion_id+"','"+datos[c].presentacion+"','"+datos[c].preciocompra+"')\"><td><span style='display: block;'>"+datos[c].nombre+"</span></td><td align='center'><span style='display: block;'>"+datos[c].presentacion+"</span></td></tr>";          
	                }	                
	            } else {
	            	a +="<tr><td align='center' colspan='2' style='color:red'>Medicamentos no encontrados.</td></tr>";
	            }
	            $("#tablaProducto").html(a);
                $('#tablaProducto').DataTable({
                    "paging":         false,
                    "ordering"        :false
                });
                $('#tablaProducto_filter').css('display','none');
                $("#tablaProducto_info").css("display","none");
    	    }
        });
    } else {
    	$("#tablaProducto").html("<tr><td align='center' colspan='8'>Digite más de 3 caracteres.</td></tr>");
    }
}

function seleccionarProducto(idproducto,producto,presentacion_id,presentacion,preciocompra){
	var _token =$('input[name=_token]').val();
	var band=true;
	producto = producto.replace("@",'"');
    for(c=0; c < carro.length; c++){
        if(carro[c]==idproducto){
            band=false;
        }      
    }
    if(band){
		$("#tbDetalle").append("<tr id='tr"+idproducto+"'>" + 
			"<td class='itemcillo' style='text-align: center; color:red; font-weight:bold;'></td>" + 
			"<td align='left'>"+producto+"</td>"+
			"<td align='center'>" + 
				"<input type='hidden' class='rucs' id='ccruc"+idproducto+"' name='ccruc"+idproducto+"'/>" + 
				"<input type='hidden' class='direcciones' id='ccdireccion"+idproducto+"' name='ccdireccion"+idproducto+"'/>" + 
				"<input type='hidden' class='nombres' id='ccrazon"+idproducto+"' name='ccrazon"+idproducto+"'/>" + 
				"<input type='text' placeholder='Digita el RUC' class='form-control' id='txtRazon"+idproducto+"' name='txtRazon"+idproducto+"' value='"+producto+"' onkeyup=\"buscarEmpresa('"+idproducto+"')\"/></td>"+
			"<td align='center'>" + 
				"<select class='tipos requerido form-control' id='txtTipo"+idproducto+"' name='txtTipo"+idproducto+"' value='"+producto+"'><option value='EFECTIVO'>EFECTIVO</option><option value='CREDITO'>CREDITO</option><option value='TRANSFERENCIA'>TRANSFERENCIA</option></select></td>"+
			"<td align='center'>" + 
				"<input type='hidden' class='ides' id='txtProducto"+idproducto+"' name='txtProducto"+idproducto+"' value='"+idproducto+"'/>" + 
				"<input type='text' data='numero' class='cantidades requerido form-control' id='txtCantidad"+idproducto+"' name='txtCantidad"+idproducto+"' value='1' size='3' onkeyup=\"generarSubtotal('"+idproducto+"')\"/></td>"+
			"<td align='center'>" + 
				"<input type='text' data='numero' class='precios requerido form-control' id='txtPrecio"+idproducto+"' name='txtPrecio"+idproducto+"' value='"+preciocompra+"' size='3' onkeyup=\"generarSubtotal('"+idproducto+"')\"/></td>"+
			"<td align='center'>" + 
				"<input type='text' data='numero' class='subtotales requerido form-control' id='txtSubtotal"+idproducto+"' name='txtSubtotal"+idproducto+"' value='"+preciocompra+"' size='3' onkeyup=\"generarTotal('"+idproducto+"')\"/></td>"+
	        "<td align='center'><a href='#' onclick=\"quitar('"+idproducto+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
	    carro.push(idproducto);
	    $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	    itemcillo();
	    buscarProveedor(idproducto);
	    $("#nombreproducto").val("");
	    generarSubtotal(""+idproducto);
	}else{
		itemcillo();
		$('#txtCantidad'+idproducto).focus();
	    $("#nombreproducto").val("");
	}	
}

function generarSubtotal(id) {
	var cantidad = $("#txtCantidad" + id).val()==""?0.00:parseFloat($("#txtCantidad" + id).val()).toFixed(2);
	var precio = $("#txtPrecio" + id).val()==""?0.00:parseFloat($("#txtPrecio" + id).val()).toFixed(2);
	var txtSubtotal = (cantidad * precio).toFixed(2);
	$("#txtSubtotal" + id).val(txtSubtotal);
	generarTotal();
}

function generarTotal() {
	var total = 0.00;
	var stot = 0.00;
	$(".subtotales").each(function() {
		stot = $(this).val()==""?0.00:parseFloat($(this).val());
		total += stot;
	});
	$("#totalGeneral").html(total.toFixed(2));
	$("#total").val(total.toFixed(2));
}

function generarNumero2(){
    $.ajax({
        type: "POST",
        url: "requerimiento/generarNumeroRadmin",
        data: "_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val() + "&radmin={{$radmin}}",
        success: function(a) {
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numerodocumento"]').val(a);
        }
    });
}

generarNumero2();

function quitar (id) {
	$("#tr"+id).remove();
    for(c=0; c < carro.length; c++){
        if(carro[c] == id) {
            carro.splice(c,1);
        }
    }
    itemcillo();
}

function guardarRequerimiento2 (entidad, idboton, entidad2) {
	//armar medicamentos y cantidades
	var ides = "";
	var cantidades = "";
	var rucs = "";
	var tipos = "";
	var subtotales = "";
	var precios = "";
	var nombres = "";
	var direcciones = "";
	$(".ides").each(function(index, el) {
		ides += $(this).val() + ";";
	});
	$(".cantidades").each(function(index, el) {
		cantidades += $(this).val() + ";";
	});
	$(".rucs").each(function(index, el) {
		rucs += $(this).val() + ";";
	});
	$(".tipos").each(function(index, el) {
		tipos += $(this).val() + ";";
	});
	$(".subtotales").each(function(index, el) {
		subtotales += $(this).val() + ";";
	});
	$(".precios").each(function(index, el) {
		precios += $(this).val() + ";";
	});
	$(".nombres").each(function(index, el) {
		nombres += $(this).val() + ";";
	});
	$(".direcciones").each(function(index, el) {
		direcciones += $(this).val() + ";";
	});
	//fin
	$("#listaProducto").val(
		ides.substring(0, ides.length-1) + 
		"@" + cantidades.substring(0, cantidades.length-1) + 
		"@" + rucs.substring(0, rucs.length-1) + 
		"@" + tipos.substring(0, tipos.length-1) + 
		"@" + subtotales.substring(0, subtotales.length-1) + 
		"@" + precios.substring(0, precios.length-1) +
		"@" + nombres.substring(0, nombres.length-1) + 
		"@" + direcciones.substring(0, direcciones.length-1)
	);
	var idformulario = IDFORMMANTENIMIENTO + entidad;
	var data         = submitForm(idformulario);
	var respuesta    = '';
	var listar       = 'NO';
	if (cantitemcillo()==0) {
		alertaG("Selecciona al menos un medicamento.");
	} else if (!validarInputs()) {
		alertaG("Verifica las cantidades.");
	} else {
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
	                alertaB("Requerimiento registrado correctamente.");
				} else if(resp === 'ERROR') {
					alert(dat[0].msg);
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	}		
}

$(document).on('click', '.escogerFila', function(){
	$('.escogerFila').css('background-color', 'white');
	$(this).css('background-color', 'yellow');
});

function itemcillo() {
	var a = 0;
	$("#detallesCompra tr .itemcillo").each(function(index, el) {
		$(this).html((a+1));
		a++;
	});
	$("#ceromedicamentos").hide();
	if(a == 0) {
		$("#ceromedicamentos").show();
	}
}

function cantitemcillo() {
	var a = 0;
	$("#detallesCompra tr .itemcillo").each(function(index, el) {
		a++;
	});
	return a;
}

function validarInputs() {
	var a = true;
	$('.requerido').each(function(index, el) {
		if($(this).val().length==0||$(this).val()==0||$(this).val()<0) {
        	a = false;
        	$(this).addClass('requerido2');
		} else {
			$(this).removeClass('requerido2');
		}
	});
	return a;
}

$(document).on('keyup', '.requerido2', function(event) {
	event.preventDefault();
	var palabra = $(this).val();
	if(palabra !== '') {
        $(this).removeClass('requerido2');
	}
});

function buscarEmpresa(id) {
	ruc = $("#txtRazon" + id).val();
	if(ruc.length == 11) {  
	    $.ajax({
	        type: 'GET',
	        url: "ticket/buscarEmpresa",
	        data: "ruc="+ruc,
	        beforeSend(){
	            $("#txtRazon" + id).val('Comprobando...');
	        },
	        success: function (a) {
	            if(a == '')  {
	        		buscarEmpresa2(ruc, id);
	        	} else {
	        		var e = a.split(';;');
	        		$("#ccruc" + id).val(ruc);
	        		$('#ccrazon' + id).val(e[0]);
	        		$('#txtRazon' + id).val(e[0]);
	        		$('#ccdireccion' + id).val(e[1]);
	        	}
	        }
	    });
	} else {
		$("#ccruc" + id).val("");
		$('#ccrazon' + id).val("");
		$('#ccdireccion' + id).val("");
	}
}

function buscarEmpresa2(ruc, id){  
    $.ajax({
        type: 'GET',
        url: "SunatPHP/demo.php",
        data: "ruc="+ruc,
        beforeSend(){
            $("#txtRazon" + id).val('Comprobando...');
        },
        success: function (data, textStatus, jqXHR) {
            if(data.RazonSocial == null) {
                alertaG('El RUC ingresado no existe... Digite uno válido.');
        		$("#ccruc" + id).val('').focus();
                $("#ccrazon" + id).val('');
                $("#txtRazon" + id).val('');
                $("#ccdireccion" + id).val('');
            } else {
                $("#ccruc" + id).val(ruc);
                $("#ccrazon" + id).val(data.RazonSocial);
                $("#txtRazon" + id).val(data.RazonSocial);
                $("#ccdireccion" + id).val('-');
            }
        }
    });
}

function buscarProveedor(idproducto) {
	$.ajax({
        type: 'POST',
        url: "requerimiento/buscarProveedor",
        data: "idproducto="+idproducto+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        beforeSend(){
            $("#txtRazon" + idproducto).val('Comprobando...');
        },
        success: function (a) {
        	var datos = JSON.parse(a);
			if(datos.length == 0) {
        		$("#ccruc" + idproducto).val('');
                $("#ccrazon" + idproducto).val('');
                $("#txtRazon" + idproducto).val('');
                $("#ccdireccion" + idproducto).val('');
            } else {
                $("#ccruc" + idproducto).val(datos[0].ruc);
                $("#ccrazon" + idproducto).val(datos[0].razon);
                $("#txtRazon" + idproducto).val(datos[0].razon);
                $("#ccdireccion" + idproducto).val(datos[0].direccion);
            }
        }
    });
}

</script>