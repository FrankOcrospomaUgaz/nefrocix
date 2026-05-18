<?php
use App\User;
use App\Person;
use App\Usertype;
use App\Sucursal;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Session;

$user = Auth::user();

$sucursal_id = Session::get('sucursal_id');
$sucursal = null;
if($sucursal_id != null){
    $sucursal = Sucursal::find($sucursal_id);
}


Date::setLocale('es');
$user     = Auth::user();
$person   = Person::find($user->person_id);
$usertype = Usertype::find($user->usertype_id);
$date     = Date::instance($usertype->created_at)->format('l j F Y');
?>
<style>
.enlaces{   
    float: left;
    background-image: none;
    padding: 15px 15px;
    cursor: pointer;    color: #000;
    font-family: fontAwesome;
}
.modal {
    overflow-y:auto;
}
</style>
<header class="main-header">
    <!-- Logo -->
    <!--<a href="#" class="logo" onclick="window.open('{ url('/dashboard')}}','_blank')">-->
    <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>SIGHO</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>SIGHO</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <a href="#" onclick="cargarRuta('{{ url('/cita') }}', 'container');" title="Citas" class="enlaces"><i class="fa fa-calendar"></i></a>
        <!--<a href="#" onclick="cargarRuta('{{ url('/medico') }}', 'container');" title="Medicos" class="enlaces"><i class="fa fa-users"></i></a>
        <a href="#" onclick="cargarRuta('{{ url('/hospitalizacion') }}', 'container');" title="Hospitalizacion" class="enlaces"><i class="fa fa-ambulance"></i></a>
        <a href="#" onclick="cargarRuta('{{ url('/salaoperacion') }}', 'container');" title="Sala de Operacion" class="enlaces"><i class="fa fa-bed"></i></a>-->
        <a href="{{ url('/vistamedico') }}" target="_blank" title="Módulo para médicos" class="enlaces"><i class="fa fa-diamond"></i></a>
        <div id='divAlerta' class='enlaces' style='color:red;font-weight: bold;'></div>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li>
                @if($sucursal == null)
                    <div id="sucursalsession" style="margin-top: 15px; margin-right: 15px;"></div>
                @else
                    <div id="sucursalsession" style="margin-top: 15px; margin-right: 15px;">SUCURSAL: {{ $sucursal->razonsocial }}</div>
                @endif
                </li>
                <li>
                @if($user->sucursal_id == null)
                    <div style="margin-top: 10px; margin-right: 15px;">
                        <button class='btn btn-success btn-sm btnSucursal' id='btnSucursal' onclick='cargarRuta("usuario/escogerSucursal", "container");' type='button'>Escoger Sucursal</button> 
                    </div>
                @endif
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="dist/img/logo2.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ $person->apellidopaterno.' '.$person->apellidomaterno.' '.$person->nombres }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="dist/img/logo2.jpg" class="img-circle" alt="User Image">

                            <p>
                                {{ $person->apellidopaterno.' '.$person->apellidomaterno.' '.$person->nombres }} - {{ $usertype->name }}
                                <small>Miembro desde {{ $date }}</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a onclick="modal('usuario/cambiarPassword', '', this);" class="btn btn-default btn-flat">Perfil</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/auth/logout') }}" class="btn btn-default btn-flat">Cerrar Sesión</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<div id="modalAlertaG" class="modal fade" role="dialog" style="z-index: 1600;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="color:red;"><i class="fa fa-thumbs-o-down"></i> ¡Cuidado!</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img width="130px" height="150px" src="dist/img/rinon.gif" class="img-circle" alt="User Image">
                    </div>
                    <div class="col-md-8 text-center">
                        <h2 style="color:blue;" id="mensajeAlertaG"></h2>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>

    </div>
</div>

<div id="modalAlertaB" class="modal fade" role="dialog" style="z-index: 1600;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="color:green;"><i class="fa fa-thumbs-o-up"></i> ¡Correcto!</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img width="130px" height="150px" src="dist/img/rinon2.gif" class="img-circle" alt="User Image">
                    </div>
                    <div class="col-md-8 text-center">
                        <h2 style="color:blue;" id="mensajeAlertaB"></h2>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    function alertaCierre(){
        if ( '{{ date("H:i") }}' >= "23:50" ){
            if ( {{ $usertype->id }} == 11 || {{ $usertype->id }} == 5) {
                alert("\n \n \n \n        ¡RECUERDA QUE DEBES CERRAR TU CAJA HASTA LAS 11:55 P.M.!\n \n \n \n");
            }
        }
    }

    function alertaG(mensaje) {
        $('#mensajeAlertaG').html(mensaje);
        $('#modalAlertaG').modal('show');
    }

    function alertaB(mensaje) {
        $('#mensajeAlertaB').html(mensaje);
        $('#modalAlertaB').modal('show');
    }

    function quitarPadding() {
        $('.skin-blue').removeAttr('style');
    }    

    //setInterval(function(){ alertaCierre(); }, 60000);
    var alerta="";
    function alertaArchivo(){        
        $.ajax({
            type: "POST",
            url: "seguimiento/alerta",
            data: "_token="+$(' :input[name="_token"]').val(),
            success: function(a) {
                eval(a);
                if(vcantidad=='0'){
                    $("#divAlerta").html('');
                }else{
                    $("#divAlerta").html(vdatos);
                    if(alerta!=valerta){
                        alert(valerta);
                        alerta=valerta;
                    }
                }
            }
        });
    }
    @if($usertype->id==16)
        setInterval(function(){ alertaArchivo(); }, 4000);
    @endif
   
</script>