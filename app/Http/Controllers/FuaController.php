<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\HistoriaClinica;
use App\Area;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FuaController extends Controller
{

    protected $folderview      = 'app.fua';
    protected $tituloAdmin     = 'Fua';
    protected $tituloRegistrar = 'Registrar Fua';
    protected $tituloModificar = 'Modificar Fua';
    protected $tituloEliminar  = 'Eliminar Fua';
    protected $rutas           = array('create' => 'fua.create', 
            'edit'   => 'fua.edit', 
            'delete' => 'fua.eliminar',
            'search' => 'fua.buscar',
            'index'  => 'fua.index',
        );


     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscar(Request $request)
    {
    	//COMPRUEBO LAS CITAS DE TODAY
    	$this->comprobarCitasHoy();
    	//-------------------------------------------
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $estado2          = $request->input('estado2');
        $entidad          = 'Fua';
        $fecha 			  = $request->input("fecha");
        $nombre 		  = $request->input("paciente");
        $estado 		  = $request->input("estado");
        $resultado  	  = HistoriaClinica::leftjoin('historia as h', 'h.id', '=', 'historiaclinica.historia_id')
        					->where("fecha_atencion", "LIKE","%".$fecha."%")
        					->join('person as paciente', 'paciente.id', '=', 'h.person_id')
							->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.$nombre.'%')
        					->where("historiaclinica.estado", "LIKE", "%".$estado."%")
                            ->select('historiaclinica.estado', 'historiaclinica.doctor_id', 'historiaclinica.historia_id', 'historiaclinica.numeroformato', 'historiaclinica.id', 'historiaclinica.fecha_atencion', 'h.person_id')
                            ->orderBy(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'));
        					
        if($estado2 == "1") {
            $resultado = $resultado->whereNotNull("numeroformato");
        } else if($estado2 == "2") {
            $resultado = $resultado->whereNull('numeroformato');
        }

        $lista            = $resultado->get(); 

        //ESTADO2 ES EL ESTADO DE LA ATENCION

        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de Atención', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Médico', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Acciones', 'numero' => '1');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Fua';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Fua';
        $fua = null;
        $formData = array('fua.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('fua', 'formData', 'entidad', 'boton', 'listar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array('nombre' => 'required|max:100');
        $mensajes = array(
            'nombre.required'         => 'Debe ingresar un nombre'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $area       = new Area();
            $area->nombre = strtoupper($request->input('nombre'));
            $area->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $area = Area::find($id);
        $entidad  = 'Area';
        $formData = array('area.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('area', 'formData', 'entidad', 'boton', 'listar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('nombre' => 'required|max:100');
        $mensajes = array(
            'nombre.required'         => 'Debe ingresar un nombre'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $area       = Area::find($id);
            $area->nombre = strtoupper($request->input('nombre'));
            $area->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $area = Area::find($id);
            $area->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Area::find($id);
        $entidad  = 'Area';
        $formData = array('route' => array('area.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    /*public function colamedico(Request $request) {
        date_default_timezone_set('America/Lima');
        $fecha0 = date('Y-m-d');

        $fecha = strtotime($fecha0);
        $diasemana = date('w', $fecha);
        if($diasemana==0) {
            $diasemana=7;
        }        

        $turnos = Turno::orderBy('hora', 'ASC')->get();
        $sconsultas = "<table style='width:100%' border='2'>";

        if(count($turnos)>0) {
            $a = 1;
            foreach ($turnos as $turno) {
                $sconsultas.="
                    <thead>
                        <tr>
                            <th class='text-center' width='100%' colspan='5' style='color:blue;font-size:18px;'>TURNO ".$a.": ".$turno->hora."</th>
                        </tr>
                        <tr>
                            <th class='text-center' width='5%'>Nro</th>
                            <th class='text-center' width='55%'>Paciente</th>
                            <th class='text-center' width='10%'>Turno</th>
                            <th class='text-center' width='20%'>Estado</th>
                            <th class='text-center' width='10%'>Llamar</th>
                        </tr>
                    </thead>
                    <tbody>";

                $resultado = Historia::select('historia.id as hid', 'historia.horacita', 'historia.person_id')->join('person', 'person.id', '=','historia.person_id');

                //TODOS MENOS LOS FINALIZADOS
                if($fecha0 !== NULL) {
                    $resultado = $resultado->where('ordencitas', 'LIKE', '%'.$diasemana.'%')->where('horacita', '=', $turno->id)->where('estado2', '!=', 'F');
                    //->where('horacita', 'LIKE', '%'.$horacita.'%');
                }
                $i=1;
                $lista = $resultado->orderBy('horacita', 'ASC')->get();  
                if(count($lista)>0) {
                    foreach ($lista as $row) {
                        $sconsultas.='<tr style="background-color:';

                        $color = 'white';
                        $color2 = 'black';
                        $estado = 'PENDIENTE';

                        $historiaclinica = HistoriaClinica::where('historia_id', '=', $row->hid)->where("estado", "!=", "C")->where('fecha_atencion', '>=', date('Y-m-d'))->first();

                        if($historiaclinica!==NULL) {

                            if($historiaclinica->estado=='L') {
                                $color = '#FFEAB5';
                                $color2 = 'blue';
                                $estado = 'LLAMADO';
                            }

                            if($historiaclinica->estado=='A') {
                                $color = '#8EFFC0';
                                $color2 = 'blue';
                                $estado = 'ATENDIÉNDOSE';
                            }

                            if($historiaclinica->estado=='N') {
                                $color = 'yellow';
                                $color2 = 'blue';
                                $estado = 'AUSENTE';
                            }

                            $sconsultas.=$color.';"><td>'.$i.'</td>';
                            $sconsultas.='<td>'.$row->persona->apellidopaterno.' '.$row->persona->apellidomaterno.' '.$row->persona->nombres.'</td>';
                            $sconsultas.='<td>'.$turno->hora.'</td>';
                            $sconsultas.='<td style="font-weight:bold; color:'.$color2.';">'.$estado.'</td>';
                            if($historiaclinica->estado=='P'||$historiaclinica->estado=='N') {
                                $sconsultas.='<td><a data-id="'.$historiaclinica->historia_id.'" class="btn btn-success btn-xs btnLlamarPaciente" href="#"><i class="fa fa-diamond"></i> Llamar</a></td></tr>';
                            } else {
                                $sconsultas.='<td><a class="btn btn-warning btn-xs" href="#"><i class="fa fa-diamond"></i> En Proceso</a></td></tr>';
                            }
                        
                            $i++;
                        }
                    }
                } else {
                    $sconsultas.="<tr><td colspan='5' style='color:red;font-weight:bold;text-align:right;'>No hay citas en este turno</td></tr>";
                }
                $a++;
                $sconsultas.="<tr><td colspan='5'></td></tr>";
                $sconsultas.="<tr><td colspan='5'></td></tr>";
            }
        }

        $this->comprobarCitasHoy();            

        $dat=array("rpta"=>$sconsultas);
        return json_encode($dat);
    }*/

    public function comprobarCitasHoy() {
        date_default_timezone_set('America/Lima');

        $fecha0 = date('Y-m-d');
        $fecha1 = date('Y-m-d H:i:s');

        $fecha = strtotime($fecha0);
        $diasemana = date('w', $fecha);
        if($diasemana==0) {
            $diasemana=7;
        }

        $resultado = Historia::select('historia.id as hid', 'historia.horacita')
            ->join('person', 'person.id', '=','historia.person_id')
            ->where('fechainicio', '<=', $fecha0)
            ->where('ordencitas', 'LIKE', '%'.$diasemana.'%');

        $lista = $resultado->orderBy('horacita', 'DESC')->get();

        foreach ($lista as $value) {
            $cita = HistoriaClinica::select('id')
                ->where('historia_id', '=', $value->hid)
                ->where('estado', '!=', 'C')
                ->where('turno', '=', $value->horacita)                
                ->where('fecha_atencion', '>=', $fecha0)
                ->get();
            if(count($cita)>1) {
                $i = 1;
                foreach ($cita as $c) {
                    if($i!==1) {
                        $c->delete();
                    }
                    $i++;
                }
            } else if(count($cita)==0) {
                $hc = new HistoriaClinica();
                $hc->tipo = 'V';
                $hc->numero = HistoriaClinica::numeroSigue();
                $hc->estado = 'P';
                $hc->fecha_atencion = $fecha1;
                $hc->turno = $value->horacita;
                $hc->historia_id = $value->hid;
                $hc->save();
                $historia = Historia::find($value->hid);
                $historia->estado2 = 'P';
                $historia->save();
            }
        }
    }

    public function comprobarCitasDiaEspecifico(Request $request) {
        date_default_timezone_set('America/Lima');

        $fecha0 = date('Y-m-d 00:00:00', strtotime($request->fecha));
        $fecha01 = date('Y-m-d 23:59:59', strtotime($request->fecha));
        $fecha1 = date('Y-m-d H:i:s', strtotime($request->fecha . " " . date('H:i')));

        $fecha = strtotime($fecha0);
        $diasemana = date('w', $fecha);
        if($diasemana==0) {
            $diasemana=7;
        }

        $resultado = Historia::select('historia.id as hid', 'historia.horacita')
            ->join('person', 'person.id', '=','historia.person_id')
            ->where('fechainicio', '<=', $fecha0)
            ->where('ordencitas', 'LIKE', '%'.$diasemana.'%');
        $lista = $resultado->orderBy('horacita', 'DESC')->get();

        foreach ($lista as $value) {
            $cita = HistoriaClinica::select('id')
                ->where('historia_id', '=', $value->hid)
                ->where('estado', '!=', 'C')
                ->where('turno', '=', $value->horacita)
                ->whereBetween('fecha_atencion', [$fecha0, $fecha01])                
                //->where('fecha_atencion', '=', $fecha0)
                ->get();
            if(count($cita)>1) {
                $i = 1;
                foreach ($cita as $c) {
                    if($i > 1) {
                        $c->delete();
                    }
                    $i++;
                }
            } else if(count($cita)==0) {
                $hc = new HistoriaClinica();
                $hc->tipo = 'V';
                $hc->numero = HistoriaClinica::numeroSigue();
                $hc->estado = 'P';
                $hc->fecha_atencion = $fecha1;
                $hc->turno = $value->horacita;
                $hc->historia_id = $value->hid;
                $hc->save();
                $historia = Historia::find($value->hid);
                $historia->estado2 = 'P';
                $historia->save();
            }
        }

        echo "OK";
    }
}
