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
					{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control input-sm', 'id' => 'fecha', 'placeholder' => 'Ingrese fecha', 'readonly' => 'true')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::text('numerodocumento', "-", array('class' => 'form-control input-sm', 'id' => 'numerodocumento', 'placeholder' => 'numerodocumento', "readonly")) !!}
				</div>

			</div>
			<div class="form-group">
				{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::textarea('comentario', null, array('class' => 'form-control input-sm', 'rows' => '6', 'id' => 'comentario', 'placeholder' => 'comentario')) !!}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8">
		<div class="form-group">
			{!! Form::label('nombreproducto', 'Producto:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-5 col-md-5 col-sm-5">
				{!! Form::text('nombreproducto', null, array('class' => 'form-control input-sm', 'id' => 'nombreproducto', 'placeholder' => 'Ingrese nombre','onkeyup' => 'buscarProducto(this.value);')) !!}
			</div>
			{!! Form::hidden('producto_id', null, array( 'id' => 'producto_id')) !!}
			{!! Form::hidden('precioventa', null, array('id' => 'precioventa')) !!}
			{!! Form::hidden('stock', null, array('id' => 'stock')) !!}
		</div>

		<div class="form-group" id="divProductos" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
			<table class='table-condensed table-hover' border='1'>
				<thead>
					<tr>
						<th class='text-center' style='width:630px;'><span style='display: block; font-size:.7em'>Nombre</span></th>
						<th class='text-center' style='width:300px;'><span style='display: block; font-size:.7em'>Presentacion</span></th>
					</tr>
				</thead>
				<tbody id='tablaProducto'>
					<tr><td align='center' colspan='8'>Digite más de 3 caracteres.</td></tr>
				</tbody>
			</table>
		</div>

		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-list fa-lg"></i> Precargar Productos de enfermería', array('class' => 'btn btn-default btn-sm', 'id' => 'btnPrecargar'.$entidad, 'onclick' => 'precargar();')) !!}
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => '$(\'#listProducto\').val(carro);guardarRequerimiento(\''.$entidad.'\', this)')) !!}
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
		
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:100%; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped" border="1" id="tbDetalle">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:50;">Item</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:100px;">Cantidad</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:700px;">Producto</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:250px;">Presentacion</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Quitar</th>                          
		                </tr>
		            </thead>
		           	<tbody id="detallesCompra">
		            </tbody>
		            <tr id="ceromedicamentos">
		            	<td colspan="5"><center style="color:red;">Escoja al menos un medicamento.</center></td>
		            </tr>
		            <!--<tbody border="1">
		            	<tr>
		            		<th colspan="3" style="text-align: right;">TOTAL</th>
		            		<td class="text-center">
		            			<center id="totalcompra2">0.00</center><input type="hidden" id="totalcompra" readonly="" name="totalcompra" value="0.00">
		            		</td>
		            	</tr>
		            </tbody>-->
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
$(document).ready(function() {
	configurarAnchoModal('1300');
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
            buscarProducto(this.value);
            valorbusqueda=this.value;
            this.focus();
            return false;
        }
        if(keyc == 38 || keyc == 40 || keyc == 13 || keyc == 27) {
            var tabladiv='tablaProducto';
			var child = document.getElementById(tabladiv).rows;
			//var indice = -1;
			var i=0;
            /*$('#tablaProducto tr').each(function(index, elemento) {
                if($(elemento).hasClass("tr_hover")) {
    			    $(elemento).removeClass("par");
    				$(elemento).removeClass("impar");								
    				indice = i;
                }
                if(i % 2==0){
    			    $(elemento).removeClass("tr_hover");
    			    $(elemento).addClass("impar");
                }else{
    				$(elemento).removeClass("tr_hover");								
    				$(elemento).addClass('par');
    			}
    			i++;
    		});*/		 
			// return
			//if(keyc == 13) { // enter
			if(keyc == 27) { // esc  				
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

}); 

function precargar() {
	$("#detallesCompra").html(`<tr id="tr21"><td class="itemcillo">1</td><td align="center"><input type="hidden" class="ides" id="txtProducto21" name="txtProducto21" value="21"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad21" name="txtCantidad21" value="1" size="3" style="text-align: right;"></td><td align="left">ELISIO 1.7</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion21" id="txtPresentacion21" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('21')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr22"><td class="itemcillo">2</td><td align="center"><input type="hidden" class="ides" id="txtProducto22" name="txtProducto22" value="22"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad22" name="txtCantidad22" value="1" size="3" style="text-align: right;"></td><td align="left">ELISIO 1.9</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion22" id="txtPresentacion22" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('22')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr23"><td class="itemcillo">3</td><td align="center"><input type="hidden" class="ides" id="txtProducto23" name="txtProducto23" value="23"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad23" name="txtCantidad23" value="1" size="3" style="text-align: right;"></td><td align="left">ELISIO 2.1</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion23" id="txtPresentacion23" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('23')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr55"><td class="itemcillo">4</td><td align="center"><input type="hidden" class="ides" id="txtProducto55" name="txtProducto55" value="55"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad55" name="txtCantidad55" value="1" size="3" style="text-align: right;"></td><td align="left">ELISIO 1.5</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion55" id="txtPresentacion55" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('55')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr24"><td class="itemcillo">5</td><td align="center"><input type="hidden" class="ides" id="txtProducto24" name="txtProducto24" value="24"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad24" name="txtCantidad24" value="1" size="3" style="text-align: right;"></td><td align="left">LINEAS ARTERIOVENOSAS</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion24" id="txtPresentacion24" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('24')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr51"><td class="itemcillo">6</td><td align="center"><input type="hidden" class="ides" id="txtProducto51" name="txtProducto51" value="51"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad51" name="txtCantidad51" value="1" size="3" style="text-align: right;"></td><td align="left">LINEAS PEDIATRICAS</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion51" id="txtPresentacion51" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('51')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr105"><td class="itemcillo">7</td><td align="center"><input type="hidden" class="ides" id="txtProducto105" name="txtProducto105" value="105"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad105" name="txtCantidad105" value="1" size="3" style="text-align: right;"></td><td align="left">CLORURO DE SODIO</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion105" id="txtPresentacion105" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('105')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr28"><td class="itemcillo">8</td><td align="center"><input type="hidden" class="ides" id="txtProducto28" name="txtProducto28" value="28"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad28" name="txtCantidad28" value="1" size="3" style="text-align: right;"></td><td align="left">EQUIPO VENOCLISIS</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion28" id="txtPresentacion28" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('28')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr26"><td class="itemcillo">9</td><td align="center"><input type="hidden" class="ides" id="txtProducto26" name="txtProducto26" value="26"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad26" name="txtCantidad26" value="1" size="3" style="text-align: right;"></td><td align="left">FISTULA 16</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion26" id="txtPresentacion26" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('26')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr27"><td class="itemcillo">10</td><td align="center"><input type="hidden" class="ides" id="txtProducto27" name="txtProducto27" value="27"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad27" name="txtCantidad27" value="1" size="3" style="text-align: right;"></td><td align="left">FISTULA 17</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion27" id="txtPresentacion27" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('27')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr29"><td class="itemcillo">11</td><td align="center"><input type="hidden" class="ides" id="txtProducto29" name="txtProducto29" value="29"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad29" name="txtCantidad29" value="1" size="3" style="text-align: right;"></td><td align="left">HEPARINA </td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion29" id="txtPresentacion29" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('29')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr30"><td class="itemcillo">12</td><td align="center"><input type="hidden" class="ides" id="txtProducto30" name="txtProducto30" value="30"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad30" name="txtCantidad30" value="1" size="3" style="text-align: right;"></td><td align="left">JERINGA 5ML</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion30" id="txtPresentacion30" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('30')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr31"><td class="itemcillo">13</td><td align="center"><input type="hidden" class="ides" id="txtProducto31" name="txtProducto31" value="31"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad31" name="txtCantidad31" value="1" size="3" style="text-align: right;"></td><td align="left">JERINGA 20ML</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion31" id="txtPresentacion31" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('31')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr32"><td class="itemcillo">14</td><td align="center"><input type="hidden" class="ides" id="txtProducto32" name="txtProducto32" value="32"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad32" name="txtCantidad32" value="1" size="3" style="text-align: right;"></td><td align="left">JERINGA 1ML</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion32" id="txtPresentacion32" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('32')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr119"><td class="itemcillo">15</td><td align="center"><input type="hidden" class="ides" id="txtProducto119" name="txtProducto119" value="119"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad119" name="txtCantidad119" value="1" size="3" style="text-align: right;"></td><td align="left">JERINGA 3CC</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion119" id="txtPresentacion119" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('119')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr33"><td class="itemcillo">16</td><td align="center"><input type="hidden" class="ides" id="txtProducto33" name="txtProducto33" value="33"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad33" name="txtCantidad33" value="1" size="3" style="text-align: right;"></td><td align="left">GORROS ENF.</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion33" id="txtPresentacion33" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('33')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr34"><td class="itemcillo">17</td><td align="center"><input type="hidden" class="ides" id="txtProducto34" name="txtProducto34" value="34"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad34" name="txtCantidad34" value="1" size="3" style="text-align: right;"></td><td align="left">MASCARILLAS</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion34" id="txtPresentacion34" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('34')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr35"><td class="itemcillo">18</td><td align="center"><input type="hidden" class="ides" id="txtProducto35" name="txtProducto35" value="35"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad35" name="txtCantidad35" value="1" size="3" style="text-align: right;"></td><td align="left">ESPARADRAPO PLASTICO</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion35" id="txtPresentacion35" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('35')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr36"><td class="itemcillo">19</td><td align="center"><input type="hidden" class="ides" id="txtProducto36" name="txtProducto36" value="36"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad36" name="txtCantidad36" value="1" size="3" style="text-align: right;"></td><td align="left">ESPARADRAPO PAPEL</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion36" id="txtPresentacion36" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('36')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr37"><td class="itemcillo">20</td><td align="center"><input type="hidden" class="ides" id="txtProducto37" name="txtProducto37" value="37"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad37" name="txtCantidad37" value="1" size="3" style="text-align: right;"></td><td align="left">GUANTES QUIRURGICOS 6.5</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion37" id="txtPresentacion37" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('37')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr150"><td class="itemcillo">21</td><td align="center"><input type="hidden" class="ides" id="txtProducto150" name="txtProducto150" value="150"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad150" name="txtCantidad150" value="1" size="3" style="text-align: right;"></td><td align="left">GUANTES QUIRURGICOS 7.0</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion150" id="txtPresentacion150" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('150')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr40"><td class="itemcillo">22</td><td align="center"><input type="hidden" class="ides" id="txtProducto40" name="txtProducto40" value="40"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad40" name="txtCantidad40" value="1" size="3" style="text-align: right;"></td><td align="left">ALGODON</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion40" id="txtPresentacion40" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('40')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr41"><td class="itemcillo">23</td><td align="center"><input type="hidden" class="ides" id="txtProducto41" name="txtProducto41" value="41"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad41" name="txtCantidad41" value="1" size="3" style="text-align: right;"></td><td align="left">ALCOHOL</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion41" id="txtPresentacion41" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('41')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr42"><td class="itemcillo">24</td><td align="center"><input type="hidden" class="ides" id="txtProducto42" name="txtProducto42" value="42"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad42" name="txtCantidad42" value="1" size="3" style="text-align: right;"></td><td align="left">BENCINA</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion42" id="txtPresentacion42" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('42')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr118"><td class="itemcillo">25</td><td align="center"><input type="hidden" class="ides" id="txtProducto118" name="txtProducto118" value="118"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad118" name="txtCantidad118" value="1" size="3" style="text-align: right;"></td><td align="left">AGUA OXIGENADA </td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion118" id="txtPresentacion118" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('118')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr44"><td class="itemcillo">26</td><td align="center"><input type="hidden" class="ides" id="txtProducto44" name="txtProducto44" value="44"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad44" name="txtCantidad44" value="1" size="3" style="text-align: right;"></td><td align="left">APOSITOS (TEGADERM)</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion44" id="txtPresentacion44" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('44')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr45"><td class="itemcillo">27</td><td align="center"><input type="hidden" class="ides" id="txtProducto45" name="txtProducto45" value="45"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad45" name="txtCantidad45" value="1" size="3" style="text-align: right;"></td><td align="left">GASA</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion45" id="txtPresentacion45" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('45')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr112"><td class="itemcillo">28</td><td align="center"><input type="hidden" class="ides" id="txtProducto112" name="txtProducto112" value="112"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad112" name="txtCantidad112" value="1" size="3" style="text-align: right;"></td><td align="left">GASA ABSORBENTE</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion112" id="txtPresentacion112" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('112')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr46"><td class="itemcillo">29</td><td align="center"><input type="hidden" class="ides" id="txtProducto46" name="txtProducto46" value="46"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad46" name="txtCantidad46" value="1" size="3" style="text-align: right;"></td><td align="left">PAPEL CREPADO</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion46" id="txtPresentacion46" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('46')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr66"><td class="itemcillo">30</td><td align="center"><input type="hidden" class="ides" id="txtProducto66" name="txtProducto66" value="66"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad66" name="txtCantidad66" value="1" size="3" style="text-align: right;"></td><td align="left">PAPEL DE MANO</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion66" id="txtPresentacion66" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('66')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr143"><td class="itemcillo">31</td><td align="center"><input type="hidden" class="ides" id="txtProducto143" name="txtProducto143" value="143"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad143" name="txtCantidad143" value="1" size="3" style="text-align: right;"></td><td align="left">PAÑOS DE LIMPIEZA WYPALL</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion143" id="txtPresentacion143" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('143')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr88"><td class="itemcillo">32</td><td align="center"><input type="hidden" class="ides" id="txtProducto88" name="txtProducto88" value="88"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad88" name="txtCantidad88" value="1" size="3" style="text-align: right;"></td><td align="left">BOLSA AMARILLA 20X30</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion88" id="txtPresentacion88" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('88')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr47"><td class="itemcillo">33</td><td align="center"><input type="hidden" class="ides" id="txtProducto47" name="txtProducto47" value="47"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad47" name="txtCantidad47" value="1" size="3" style="text-align: right;"></td><td align="left">BOLSA BRILLITO </td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion47" id="txtPresentacion47" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('47')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr113"><td class="itemcillo">34</td><td align="center"><input type="hidden" class="ides" id="txtProducto113" name="txtProducto113" value="113"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad113" name="txtCantidad113" value="1" size="3" style="text-align: right;"></td><td align="left">BOLSA BRILLITO 7X100</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion113" id="txtPresentacion113" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('113')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr87"><td class="itemcillo">35</td><td align="center"><input type="hidden" class="ides" id="txtProducto87" name="txtProducto87" value="87"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad87" name="txtCantidad87" value="1" size="3" style="text-align: right;"></td><td align="left">BOLSA NEGRA 140 L</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion87" id="txtPresentacion87" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('87')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr84"><td class="itemcillo">36</td><td align="center"><input type="hidden" class="ides" id="txtProducto84" name="txtProducto84" value="84"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad84" name="txtCantidad84" value="1" size="3" style="text-align: right;"></td><td align="left">BOLSA NEGRA 20X30</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion84" id="txtPresentacion84" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('84')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr69"><td class="itemcillo">37</td><td align="center"><input type="hidden" class="ides" id="txtProducto69" name="txtProducto69" value="69"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad69" name="txtCantidad69" value="1" size="3" style="text-align: right;"></td><td align="left">MANDILONES QUIRURGICOS</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion69" id="txtPresentacion69" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('69')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr68"><td class="itemcillo">38</td><td align="center"><input type="hidden" class="ides" id="txtProducto68" name="txtProducto68" value="68"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad68" name="txtCantidad68" value="1" size="3" style="text-align: right;"></td><td align="left">MANDILONES SIMPLES</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion68" id="txtPresentacion68" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('68')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr70"><td class="itemcillo">39</td><td align="center"><input type="hidden" class="ides" id="txtProducto70" name="txtProducto70" value="70"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad70" name="txtCantidad70" value="1" size="3" style="text-align: right;"></td><td align="left">SABANAS QUIRURGICAS</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion70" id="txtPresentacion70" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('70')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr49"><td class="itemcillo">40</td><td align="center"><input type="hidden" class="ides" id="txtProducto49" name="txtProducto49" value="49"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad49" name="txtCantidad49" value="1" size="3" style="text-align: right;"></td><td align="left">CARTUCHO DE BICARBONATO</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion49" id="txtPresentacion49" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('49')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr52"><td class="itemcillo">41</td><td align="center"><input type="hidden" class="ides" id="txtProducto52" name="txtProducto52" value="52"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad52" name="txtCantidad52" value="1" size="3" style="text-align: right;"></td><td align="left">HIBICLEN 2% (CATETER)</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion52" id="txtPresentacion52" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('52')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr><tr id="tr53"><td class="itemcillo">42</td><td align="center"><input type="hidden" class="ides" id="txtProducto53" name="txtProducto53" value="53"><input type="text" data="numero" class="requerido form-control input-sm cantidades" id="txtCantidad53" name="txtCantidad53" value="1" size="3" style="text-align: right;"></td><td align="left">HIBICLEN 2% (PARA MANOS)</td><td align="center"><input type="hidden" class="form-control input-sm" name="txtPresentacion53" id="txtPresentacion53" value="1">UNIDAD</td><td align="center"><a href="#" onclick="quitar('53')"><i class="fa fa-minus-circle" title="Quitar" width="20px" height="20px"></i></a></td></tr>`);
	$("#ceromedicamentos").hide();
	carro.push(21);
	carro.push(22);
	carro.push(23);
	carro.push(55);
	carro.push(24);
	carro.push(51);
	carro.push(105);
	carro.push(28);
	carro.push(26);
	carro.push(27);
	carro.push(29);
	carro.push(30);
	carro.push(31);
	carro.push(32);
	carro.push(119);
	carro.push(33);
	carro.push(34);
	carro.push(35);
	carro.push(36);
	carro.push(37);
	carro.push(150);
	carro.push(40);
	carro.push(41);
	carro.push(42);
	carro.push(118);
	carro.push(44);
	carro.push(45);
	carro.push(112);
	carro.push(46);
	carro.push(66);
	carro.push(143);
	carro.push(88);
	carro.push(47);
	carro.push(113);
	carro.push(87);
	carro.push(84);
	carro.push(69);
	carro.push(68);
	carro.push(70);
	carro.push(49);
	carro.push(52);
	carro.push(53);
}

var valorinicial="";
var carro = new Array();
function buscarProducto(valor){
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
	                    a += "<tr style='cursor:pointer' class='escogerFila' id='"+datos[c].idproducto+"' onclick=\"seleccionarProducto('"+datos[c].idproducto+"','"+nombre+"','"+datos[c].presentacion_id+"','"+datos[c].presentacion+"')\"><td><span style='display: block;'>"+datos[c].nombre+"</span></td><td align='center'><span style='display: block;'>"+datos[c].presentacion+"</span></td></tr>";          
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

function seleccionarProducto(idproducto,producto,presentacion_id,presentacion){
	var _token =$('input[name=_token]').val();
	var band=true;
	producto = producto.replace("@",'"');
    for(c=0; c < carro.length; c++){
        if(carro[c]==idproducto){
            band=false;
        }      
    }
    if(band){
		$("#tbDetalle").append("<tr id='tr"+idproducto+"'><td class='itemcillo'></td><td align='center'><input type='hidden' class='ides' id='txtProducto"+idproducto+"' name='txtProducto"+idproducto+"' value='"+idproducto+"'/><input type='text' data='numero' class='requerido form-control input-sm cantidades' id='txtCantidad"+idproducto+"' name='txtCantidad"+idproducto+"' value='1' size='3'  /></td>"+
	        "<td align='left'>"+producto+"</td>"+
	        "<td align='center'><input type='hidden' class='form-control input-sm'  name='txtPresentacion"+idproducto+"' id='txtPresentacion"+idproducto+"' value='"+presentacion_id+"' />"+presentacion+"</td>"+
	        "<td align='center'><a href='#' onclick=\"quitar('"+idproducto+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
	    carro.push(idproducto);
	    $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	    itemcillo();
	    $("#nombreproducto").val("");
	}else{
		itemcillo();
		$('#txtCantidad'+idproducto).focus();
	    $("#nombreproducto").val("");
	}	
}

function generarNumero(){
    $.ajax({
        type: "POST",
        url: "requerimiento/generarNumero",
        data: "_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numerodocumento"]').val(a);
        }
    });
}

generarNumero();

function quitar (id) {
	$("#tr"+id).remove();
    for(c=0; c < carro.length; c++){
        if(carro[c] == id) {
            carro.splice(c,1);
        }
    }
    itemcillo();
}

function guardarRequerimiento (entidad, idboton, entidad2) {
	//armar medicamentos y cantidades
	var ides = "";
	var cantidades = "";
	$(".ides").each(function(index, el) {
		ides += $(this).val() + ";";
	});
	$(".cantidades").each(function(index, el) {
		cantidades += $(this).val() + ";";
	});
	//fin
	$("#listaProducto").val(ides.substring(0, ides.length-1) + "@" + cantidades.substring(0, cantidades.length-1));
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

</script>