<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Turno;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{

    protected $folderview      = 'app.turno';
    protected $tituloAdmin     = 'Turno';
    protected $tituloRegistrar = 'Registrar turno';
    protected $tituloModificar = 'Modificar turno';
    protected $tituloEliminar  = 'Eliminar turno';
    protected $rutas           = array('create' => 'turno.create', 
            'edit'   => 'turno.edit', 
            'delete' => 'turno.eliminar',
            'search' => 'turno.buscar',
            'index'  => 'turno.index',
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
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Turno';
        $nombre             = Libreria::getParam($request->input('hora'));
        $resultado        = Turno::where('hora', 'LIKE', '%'.strtoupper($nombre).'%')->orderBy('hora', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Numero Romano', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
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
        $entidad          = 'Turno';
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
        $entidad  = 'Turno';
        $turno = null;
        $formData = array('turno.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('turno', 'formData', 'entidad', 'boton', 'listar'));
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
        $reglas     = array('hora' => 'required|max:100');
        $mensajes = array(
            'hora.required'         => 'Debe ingresar una hora'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $turno       = new Turno();
            $turno->hora = strtoupper($request->input('hora'));
            $turno->save();

            $turnos = Turno::orderBy("hora", "ASC")->get();

            if(count($turnos)>0) {

                for ($i=0; $i < count($turnos); $i++) { 
                    $turnos[$i]->romano=$this->convertirNumARom(($i+1));
                    $turnos[$i]->save();
                }
            }
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
        $existe = Libreria::verificarExistencia($id, 'turno');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $turno = Turno::find($id);
        $entidad  = 'Turno';
        $formData = array('turno.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('turno', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'turno');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('hora' => 'required|max:100');
        $mensajes = array(
            'hora.required'         => 'Debe ingresar una hora'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $turno       = Turno::find($id);
            $turno->hora = strtoupper($request->input('hora'));
            $turno->save();

            $turnos = Turno::orderBy("hora", "ASC")->get();

            if(count($turnos)>0) {

                for ($i=0; $i < count($turnos); $i++) { 
                    $turnos[$i]->romano=$this->convertirNumARom(($i+1));
                    $turnos[$i]->save();
                }
            }
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
        $existe = Libreria::verificarExistencia($id, 'turno');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $turno = Turno::find($id);
            $turno->delete();

            $turnos = Turno::orderBy("hora", "ASC")->get();

            if(count($turnos)>0) {

                for ($i=0; $i < count($turnos); $i++) { 
                    $turnos[$i]->romano=$this->convertirNumARom(($i+1));
                    $turnos[$i]->save();
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'turno');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Turno::find($id);
        $entidad  = 'Turno';
        $formData = array('route' => array('turno.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function convertirNumARom($num)
    {
        /*** intval(xxx) para que convierta explicitamente a int ***/
        $n = intval($num);
        $res = '';
       
        /*** Array con los numeros romanos  ***/
        $roman_numerals = array('M'  => 1000, 'CM' => 900, 'D'  => 500, 'CD' => 400, 'C'  => 100, 'XC' => 90, 'L'  => 50, 'XL' => 40, 'X'  => 10, 'IX' => 9, 'V'  => 5, 'IV' => 4, 'I'  => 1);
       
        foreach ($roman_numerals as $roman => $number) {
            /*** Dividir para encontrar resultados en array ***/
            $matches = intval($n / $number);
           
            /*** Asignar el numero romano al resultado ***/
            $res .= str_repeat($roman, $matches);
           
            /*** Descontar el numero romando al total ***/
            $n = $n % $number;
        }
       
        /*** Res = String ***/
        return $res;
    }
}
