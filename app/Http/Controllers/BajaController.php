<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\HistoriaClinica;
use App\Person;
use App\Area;
use App\Baja;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BajaController extends Controller
{

    protected $folderview      = 'app.baja';
    protected $tituloAdmin     = 'Baja';
    protected $tituloRegistrar = 'Registrar Baja/Alta';
    protected $tituloModificar = 'Modificar Baja';
    protected $tituloEliminar  = 'Eliminar Baja';
    protected $rutas           = array('create' => 'baja.create', 
            'edit'   => 'baja.edit', 
            'delete' => 'baja.eliminar',
            'search' => 'baja.buscar',
            'index'  => 'baja.index',
        );
    protected $meses      = array("" => "TODOS", "1" => "ENERO", "2" => "FEBRERO", "3" => "MARZO", "4" => "ABRIL", "5" => "MAYO", "6" => "JUNIO", "7" => "JULIO", "8" => "AGOSTO", "9" => "SETIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE");
    protected $anoos      = array("" => "TODOS", "2019" => "2019", "2020" => "2020", "2021" => "2021", "2022" => "2022", "2023" => "2023", "2024" => "2024", "2025" => "2025", "2026" => "2026", "2027" => "2027", "2028" => "2028", "2029" => "2029", "2030" => "2030", "2031" => "2031", "2032" => "2032", "2033" => "2033", "2034" => "2034", "2035" => "2035", "2036" => "2036", "2037" => "2037", "2038" => "2038", "2039" => "2039", "2040" => "2040", "2041" => "2041", "2042" => "2042", "2043" => "2043", "2045" => "2045", "2046" => "2046", "2047" => "2047", "2048" => "2048", "2049" => "2049", "2050" => "2050");

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
    	//-------------------------------------------
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Baja';
        $fecha            = $request->input("fecha");
        $estado           = $request->input("estado");
        $nombre           = $request->input("nombre");
        $mes              = $request->input("mess");
        $ano    		  = $request->input("anoo");
        $resultado  	  = Baja::join('historia', 'historia.id', '=', 'baja.historia_id')
                            ->join('person', 'person.id', '=', 'historia.person_id')
                            ->where("baja.estado","LIKE","%".$estado."%")
							->where(DB::raw('concat(person.apellidopaterno,\' \',person.apellidomaterno,\' \',person.nombres)'), 'LIKE', '%'.$nombre.'%')
                            ->select("person.nombres", "person.apellidopaterno", "person.apellidomaterno", "baja.estado", "baja.ipresshospitalizacion", "baja.motivo", "baja.fecha", "baja.id")
                            ->orderBy("baja.fecha", "DESC")
                            ->distinct();

        if($mes !== "") {
            $resultado = $resultado->where(DB::raw("MONTH(baja.fecha)"), "=", $mes);
        }

        if($ano !== "") {
            $resultado = $resultado->where(DB::raw("YEAR(baja.fecha)"), "=", $ano);
        }

        $lista            = $resultado->get(); 

        //ESTADO2 ES EL ESTADO DE LA ATENCION

        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha acontecimiento', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Motivo Alta/Baja', 'numero' => '1');
        $cabecera[]       = array('valor' => 'IPRESS Hospitalización', 'numero' => '1');
        
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

    public function index()
    {
        $entidad          = 'Baja';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $meses   = $this->meses;
        $anoos   = $this->anoos;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'meses', 'anoos'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Baja';
        $baja = null;
        $formData = array('baja.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('baja', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function store(Request $request)
    {
        $dat=array();
        $error = DB::transaction(function() use($request, &$dat){
            $user = Auth::user();
            $baja = new Baja();
            $baja->historia_id = $request->input("historia_id");
            $baja->fecha = $request->input("fecha");
            $baja->estado = strtoupper($request->input("estadoactual"));
            $baja->motivo = strtoupper($request->input("motivo"));
            $baja->ipresshospitalizacion = strtoupper($request->input("IPRESS"));
            $baja->usuario_id = $user->person->id;
            
            $historia = $baja->historia;
            $historia->baja = "S";
            $historia->fechafallecido = $request->input("fecha");
            $historia->motivobaja = strtoupper($request->input("motivo"));
            if(strtoupper($request->input("estadoactual"))=="A") {
                $historia->baja = "N";
                $historia->fechafallecido = NULL;
                $ultimabaja = Baja::orderBy("fecha", "DESC")->where("historia_id", "=", $request->input("historia_id"))->first();
                if($ultimabaja != null) {
                    $ultimabaja->baja_id = $baja->id;
                    $ultimabaja->save();
                }
            }
            if(strtoupper($request->input("estadoactual"))=="O") {
                $baja->motivo2 = $request->input("motivo2");
            }    
            if(strtoupper($request->input("estadoactual"))=="F") {
                $historia->fallecido = "S";
                $baja->motivo2 = "FALLECIMIENTO";
            }
            $historia->estadobaja = strtoupper($request->input("estadoactual"));
            $historia->save(); 
            $baja->save();

            $dat[0]=array("respuesta"=>"OK");
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function show($id)
    {
        //
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'baja');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $baja     = Baja::find($id);
        $entidad  = 'Baja';
        $formData = array('baja.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('baja', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $dat=array();
        $error = DB::transaction(function() use($request, $id, &$dat){
            $user = Auth::user();
            $baja = Baja::find($id);
            $baja->fecha = $request->input("fecha");
            $baja->motivo = strtoupper($request->input("motivo"));
            $baja->ipresshospitalizacion = strtoupper($request->input("IPRESS"));
            $baja->usuario_id = $user->person->id;
            if($baja->estado=="O") {
                $baja->motivo2 = $request->input("motivo2");
            }
            $baja->save();
            $dat[0]=array("respuesta"=>"OK");
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'baja');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $baja = Baja::find($id);

            $historia = $baja->historia;

            $ultimabaja = Baja::orderBy("fecha", "DESC")->where("historia_id", "=", $baja->historia->id)->first();

            if($ultimabaja !== null) {
                if($baja->id == $ultimabaja->id) {
                    $historia->fechafallecido = $ultimabaja->fecha;
                    //dd($baja->estado);
                    $historia->fallecido = "N";
                    if($baja->estado == "F") {
                        $historia->baja = "N";
                        $historia->fechafallecido = NULL;
                    }
                    $historia->motivobaja = $ultimabaja->motivo;
                    $historia->estadobaja = $ultimabaja->estado;
                    $historia->save();
                }
            }               

            $baja->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'baja');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Baja::find($id);
        $entidad  = 'Baja';
        $formData = array('route' => array('baja.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function personautocompletar($searching)
    {
        $resultado        = Person::join("historia", "historia.person_id", "=", "person.id")
                        ->where(DB::raw('CONCAT(apellidopaterno," ",apellidomaterno," ",nombres)'), 'LIKE', '%'.strtoupper($searching).'%')
                        ->where("historia.fallecido", "!=", "S")
                        ->orderBy('apellidopaterno', 'ASC')
                        ->select("historia.id", "person.nombres", "person.apellidopaterno", "person.apellidomaterno");
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $name = $value->apellidopaterno." ".$value->apellidomaterno." ".$value->nombres;
            $data[] = array(
                'label' => trim($name),
                'id'    => $value->id,
                'value' => trim($name),
            );
        }
        return json_encode($data);
    }

    public function evaluarPaciente(Request $request) {
        $id = $request->input("id");
        $estado = "";
        $tabla = "";
        $historia = Historia::find($id);
        $estado = $historia->estadobaja;
        $selectito = "";                
        switch ($estado) {
            case "H":
                $estado = "HOSPITALIZADO";
                $selectito = '<option value="F">FALLECIDO</option>
                <option value="A">ALTA</option>
                <option value="O">OTRO</option>';
                break;
            case "A":
                $estado = "ALTA";
                $selectito = '<option value="H">HOSPITALIZADO</option>
                <option value="F">FALLECIDO</option>
                <option value="O">OTRO</option>';
                break;
            case "O":
                $estado = "OTRO";
                $selectito = '<option value="H">HOSPITALIZADO</option>
                <option value="F">FALLECIDO</option>
                <option value="A">ALTA</option>';
                break;                
            default:
                $estado = "NORMAL";
                $selectito = '<option value="H">HOSPITALIZADO</option>
                <option value="F">FALLECIDO</option>
                <option value="O">OTRO</option>';
                break;
        }

        //HAGO LA TABLA
        $listahistorial = Baja::where("historia_id", "=", $historia->id)->orderBy("fecha", "DESC")->get();
        if(count($listahistorial)>0) {
            foreach ($listahistorial as $row) {
                $estado2 = $row->estado;
                switch ($estado2) {
                    case "H":
                        $estado2 = "HOSPITALIZADO";
                        break;
                    case "A":
                        $estado2 = "ALTA";
                        break;
                    case "O":
                        $estado2 = "OTRO";
                        break;
                }
                $tabla .= "<tr>
                    <td align='center'>" . date("d-m-Y", strtotime($row->fecha)) . "</td>
                    <td align='center'>" . $estado2 . "</td>
                    <td align='center'>" . strtoupper($row->motivo) . "</td>
                </tr>";
            }
        }
        $tabla .= "<tr>
                    <td align='center'>" . date("d-m-Y", strtotime($historia->created_at)) . "</td>
                    <td align='center'>INGRESO</td>
                    <td align='center'>REGISTRO DE PACIENTE</td>
                </tr>";
        $array = array(
            'estado' => $estado,
            'tabla' => $tabla,
            'selectito' => $selectito,
        );
        return json_encode($array);
    }
}
