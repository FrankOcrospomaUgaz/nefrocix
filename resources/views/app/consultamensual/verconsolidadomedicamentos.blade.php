<?php 
use App\Person;
$paciente = Person::find($id);
?>

@if($paciente!==NULL)
<form method="#" accept-charset="UTF-8" class="form-horizontal">
    <div class="panel-group">
        <!--<button type="button" class="close closdat" onclick="cerrarModal();" title="Cerrar Ventana"><i style="font-weight:bold;color:red; font-weight: bold;" class="glyphicon glyphicon-remove-circle"></i></button>-->
        <div class="form-group">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading" style="height: 45px;">
                        <div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-10">
                                <div class="form-group">
                                    &nbsp;&nbsp;CONSOLIDADO DE MEDICAMENTOS EN <font style="color: red; font-weight: bold;" id="anillo">{{$anno}}</font>  RECETADOS AL PACIENTE: {{$paciente->apellidopaterno . " " . $paciente->apellidomaterno . " " . $paciente->nombres}}
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="form-group text-right">
                                    {!! Form::label('txtYear', 'AÑO', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                                    <div class="col-lg-10 col-md-10 col-sm-10">
                                        {!! Form::select('txtYear', $years, $anno, array('class' => 'form-control input-sm', 'id' => 'txtYear', 'onchange' => 'cargaTablaverconsolidadoMedicamentos();')) !!}                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table width="100%" height="100%">
                            <thead>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;" width="30%">DESCRIPCIÓN</th>
                                    <th style="text-align: center; vertical-align: middle;">ENERO</th>
                                    <th style="text-align: center; vertical-align: middle;">FEBRERO</th>
                                    <th style="text-align: center; vertical-align: middle;">MARZO</th>
                                    <th style="text-align: center; vertical-align: middle;">ABRIL</th>
                                    <th style="text-align: center; vertical-align: middle;">MAYO</th>
                                    <th style="text-align: center; vertical-align: middle;">JUNIO</th>
                                    <th style="text-align: center; vertical-align: middle;">JULIO</th>
                                    <th style="text-align: center; vertical-align: middle;">AGOSTO</th>
                                    <th style="text-align: center; vertical-align: middle;">SETIEMBRE</th>
                                    <th style="text-align: center; vertical-align: middle;">OCTUBRE</th>
                                    <th style="text-align: center; vertical-align: middle;">NOVIEMBRE</th>
                                    <th style="text-align: center; vertical-align: middle;">DICIEMBRE</th>
                                </tr>
                            </thead>
                            <tbody id="tablaverconsolidadoMedicamentos"></tbody>
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

@else
    <h3>HA SELECCIONADO UNA HISTORIA QUE NO EXISTE O ESTÁ DE BAJA</h3>
@endif
<script>
    
    $(document).ready(function() {
        configurarAnchoModal('1300');
        cargaTablaverconsolidadoMedicamentos();
    });
    function cargaTablaverconsolidadoMedicamentos() {
        $.ajax({
            url: 'consultamensual/cargaTablaverconsolidadoMedicamentos?id={{$id}}&anno='+$("#txtYear").val(),
            type: 'GET',
            dataType: "JSON",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            beforeSend: function() {
                $("#tablaverconsolidadoMedicamentos").html("<td colspan='13'>Cargando datos...</td>");
            },
            success: function(a) {
                $("#tablaverconsolidadoMedicamentos").html(a["ret"]);
                $("#anillo").html($("#txtYear").val());
            },
        })
    }
    
</script>