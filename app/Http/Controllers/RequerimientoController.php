<?php

namespace App\Http\Controllers;

use App\Caja;
use App\ConsultaNefrologica;
use App\Detallemovimiento;
use App\Http\Controllers\Controller;
use App\Kardex;
use App\Librerias\Libreria;
use App\Lote;
use App\Movimiento;
use App\Movimientoalmacen;
use App\Producto;
use App\Person;
use App\Stock;
use App\Tipodocumento;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;
use Validator;

ini_set('memory_limit', '512M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB

class RequerimientoController extends Controller
{

    protected $folderview      = 'app.requerimiento';
    protected $tituloAdmin     = 'Requerimientos';
    protected $tituloRegistrar = 'Registrar Requerimiento';
    protected $tituloModificar = 'Despachar Requerimiento';
    protected $tituloVer       = 'Ver Requerimiento';
    protected $tituloEliminar  = 'Eliminar Requerimiento';
    protected $rutas           = array(
        'create'           => 'requerimiento.create',
        'edit'             => 'requerimiento.edit',
        'show'             => 'requerimiento.show',
        'delete'           => 'requerimiento.eliminar',
        'search'           => 'requerimiento.buscar',
        'index'            => 'requerimiento.index'
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Requerimiento';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user             = Auth::user();
        return view($this->folderview . '.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'user'));
    }

    /**
     * Mostrar el resultado de búsquedas
     *
     * @return Response
     */
    public function buscar(Request $request)
    {
        $this->comprobarRequerimientos();
        $pagina      = $request->input('page');
        $filas       = $request->input('filas');
        $entidad     = 'Requerimiento';
        $fechainicio = Libreria::getParam($request->input('fechainicio'));
        $fechafin    = Libreria::getParam($request->input('fechafin'));
        $comentary   = Libreria::getParam($request->input('comentary'));

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $user = Auth::user();
        /*if($request->input('usuario')=="Todos"){
        $responsable_id=0;
        }else{
        $responsable_id=$user->person_id;
        }*/

        $resultado = Movimientoalmacen::where('tipomovimiento_id', '=', '15')
            ->where('sucursal_id', '=', $sucursal_id)
            ->whereNull("radmin")
            ->where(function ($query) use ($fechainicio, $fechafin, $comentary) {
            if (!is_null($fechainicio) && $fechainicio !== '') {
                $query->where('fecha', '>=', $fechainicio);
            }
            if (!is_null($fechafin) && $fechafin !== '') {
                $query->where('fecha', '<=', $fechafin);
            }
            if (!is_null($comentary) && $comentary !== '') {
                $query->where("comentario", "LIKE", "%" . $comentary . "%");
            }
        });
        /*if($responsable_id>0){
        $resultado = $resultado->where('movimiento.responsable_id', '=', $responsable_id);
        }*/
        $resultado  = $resultado->select('movimiento.*');
        $lista      = $resultado->get();
        $cabecera   = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[] = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[] = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[] = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[] = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '4');

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_ver       = $this->tituloVer;
        $ruta             = $this->rutas;
        $user             = Auth::user();
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview . '.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_ver', 'ruta', 'user'));
        }
        return view($this->folderview . '.list')->with(compact('lista', 'entidad'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $entidad       = 'Requerimiento';
        $requerimiento = null;
        $formData      = array('requerimiento.store');
        $formData      = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton         = 'Registrar';
        return view($this->folderview . '.mant')->with(compact('requerimiento', 'formData', 'entidad', 'boton', 'listar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $reglas = array(
            'numerodocumento' => 'required',
            'fecha'           => 'required',
        );
        $mensajes = array(
            'numerodocumento.required' => 'Debe ingresar un numero de documento',
            'fecha.required'           => 'Debe ingresar fecha',
        );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $dat = array();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $error = DB::transaction(function () use ($request, $sucursal_id, &$dat) {
            $total                                = 0;
            $caja                                 = Caja::where("sucursal_id", "=", $sucursal_id)->first();
            $movimientoalmacen                    = new Movimiento();
            $movimientoalmacen->sucursal_id       = $sucursal_id;
            $movimientoalmacen->caja_id           = $caja->id;
            $movimientoalmacen->tipodocumento_id  = 24;
            $movimientoalmacen->almacen_id        = 1;
            $movimientoalmacen->tipomovimiento_id = 15;
            $movimientoalmacen->comentario        = Libreria::obtenerParametro($request->input('comentario'));
            $movimientoalmacen->numero            = $request->input('numerodocumento');
            $movimientoalmacen->fecha             = $request->input('fecha');
            $movimientoalmacen->total             = $total;
            $user                                 = Auth::user();
            $movimientoalmacen->responsable_id    = $user->person_id;
            $movimientoalmacen->persona_id        = $user->person_id;
            $movimientoalmacen->situacion         = 'P'; //PENDIENTE
            $movimientoalmacen->save();
            $movimiento_id = $movimientoalmacen->id;
            $arr           = explode("@", $request->input('listaProducto'));
            $ides          = explode(";", $arr[0]);
            $cantidades    = explode(";", $arr[1]);
            for ($c = 0; $c < count($ides); $c++) {
                $cantidad                    = str_replace(',', '', $cantidades[$c]);
                $detalleVenta                = new Detallemovimiento();
                $detalleVenta->cantidad      = $cantidad;
                $detalleVenta->precio        = 0;
                $detalleVenta->subtotal      = 0;
                $detalleVenta->movimiento_id = $movimiento_id;
                $detalleVenta->producto_id   = $ides[$c];
                $detalleVenta->save();
            }
            $dat[0] = array("respuesta" => "OK", "requerimiento_id" => $movimientoalmacen->id, "ind" => 0, "second_id" => 0);
        });
        return is_null($error) ? json_encode($dat) : $error;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $requerimiento = Movimientoalmacen::find($id);
        $entidad       = 'Requerimiento';
        $formData      = array('requerimiento.update', $id);
        $formData      = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton         = 'Modificar';
        $detalles      = Detallemovimiento::where('movimiento_id', '=', $requerimiento->id)->get();

        return view($this->folderview . '.mantView')->with(compact('requerimiento', 'formData', 'entidad', 'boton', 'listar', 'detalles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $requerimiento = Movimiento::find($id);
        $entidad       = 'Requerimiento';
        $formData      = array('requerimiento.update', $id);
        $formData      = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton         = 'Despachar';
        $detalles      = Detallemovimiento::where('movimiento_id', '=', $requerimiento->id)->get();
        return view($this->folderview . '.despachar')->with(compact('requerimiento', 'formData', 'entidad', 'boton', 'listar', 'detalles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generarNumeroDocMovAlmacen()
    {
        $sucursal_id       = 1;
        $tipodoc           = 9;
        $almacen_id        = 1;
        $tipomovimiento_id = 5;
        $numero            = Movimiento::NumeroSigueDocMovAlmacen($sucursal_id, $tipomovimiento_id, $tipodoc, $almacen_id);
        return $numero;
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function () use ($request, $id) {
            $movimiento            = Movimiento::find($id);
            $movimiento->situacion = 'D';
            $movimiento->save();
            $detalles = Detallemovimiento::where('movimiento_id', '=', $id)->get();
            foreach ($detalles as $key => $value) {
                if ($value->producto->lote != "SI") {
                    $lotcant = '';
                    //STOCK
                    $stock           = Stock::where('producto_id', '=', $value->producto_id)->where('almacen_id', '=', 1)->first();
                    $stock->cantidad = $stock->cantidad - $request->input('txtCantidad' . $value->producto_id);
                    $stock->save();

                    //DESPACHO
                    $value->despachado = $request->input('txtCantidad' . $value->producto_id);
                    $value->save();
                    //KARDEX
                    $ultimokardex  = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('producto_id', '=', $value->producto_id)->where('movimiento.almacen_id', '=', 1)->orderBy('kardex.id', 'DESC')->first();
                    $stockanterior = 0;
                    $stockactual   = 0;

                    $lotes = Lote::where('producto_id', '=', $value->producto_id)->where('almacen_id', 1)->where('queda', '>', '0')->orderBy('fechavencimiento', 'ASC')->get();

                    $aux = $request->input('txtCantidad' . $value->producto_id);
                    foreach ($lotes as $key => $value2) {
                        if ($value2->queda >= $aux) {
                            $queda         = $value2->queda - $aux;
                            $value2->queda = $queda;
                            $value2->save();
                            $ultimokardex  = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('producto_id', '=', $value->producto_id)->where('movimiento.almacen_id', '=', 1)->orderBy('kardex.id', 'DESC')->first();
                            $stockanterior = 0;
                            $stockactual   = 0;
                            // ingresamos nuevo kardex
                            if ($ultimokardex === null) {
                            } else {
                                $stockanterior                = $ultimokardex->stockactual;
                                $stockactual                  = $ultimokardex->stockactual - $aux;
                                $kardex                       = new Kardex();
                                $kardex->tipo                 = 'S';
                                $kardex->fecha                = date('Y-m-d', strtotime($request->input('fecha')));
                                $kardex->stockanterior        = $stockanterior;
                                $kardex->stockactual          = $stockactual;
                                $kardex->cantidad             = $aux;
                                $kardex->almacen_id           = 1;
                                $kardex->detallemovimiento_id = $value->id;
                                $kardex->lote_id              = $value2->id;
                                $kardex->save();

                                $lotcant .= $value2->id . ';' . $aux . '@';

                            }
                            break;
                        } else {
                            $aux           = $aux - $value2->queda;
                            $aux2          = $value2->queda;
                            $value2->queda = 0;
                            $value2->save();

                            $ultimokardex  = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('producto_id', '=', $value->producto_id)->where('movimiento.almacen_id', '=', 1)->orderBy('kardex.id', 'DESC')->first();
                            $stockanterior = 0;
                            $stockactual   = 0;
                            // ingresamos nuevo kardex
                            if ($ultimokardex === null) {
                            } else {
                                $stockanterior                = $ultimokardex->stockactual;
                                $stockactual                  = $ultimokardex->stockactual - $aux2;
                                $kardex                       = new Kardex();
                                $kardex->tipo                 = 'S';
                                $kardex->fecha                = date('Y-m-d', strtotime($request->input('fecha')));
                                $kardex->stockanterior        = $stockanterior;
                                $kardex->stockactual          = $stockactual;
                                $kardex->cantidad             = $aux2;
                                $kardex->almacen_id           = 1;
                                $kardex->detallemovimiento_id = $value->id;
                                $kardex->lote_id              = $value2->id;
                                $kardex->save();

                                $lotcant .= $value2->id . ';' . $aux2 . '@';

                            }
                        }
                    }

                    $value->lote = $lotcant;
                    $value->save();

                    /*if ($ultimokardex === NULL) {
                $stockactual = $cantidad;
                $kardex = new Kardex();
                $kardex->tipo = 'S';
                $kardex->fecha = date("Y-m-d");
                $kardex->stockanterior = $stockanterior;
                $kardex->stockactual = (-1)*$request->input('txtCantidad'.$value->producto_id);
                $kardex->cantidad = $request->input('txtCantidad'.$value->producto_id);
                $kardex->preciocompra = $value->precio;
                //$kardex->almacen_id = 2;
                $kardex->detallemovimiento_id = $value->id;
                //$kardex->lote_id = $lote->id;
                $kardex->save();
                }else{
                $stockanterior = $ultimokardex->stockactual;
                $stockactual = $ultimokardex->stockactual-$request->input('txtCantidad'.$value->producto_id);
                $kardex = new Kardex();
                $kardex->tipo = 'S';
                $kardex->fecha = date('Y-m-d');
                $kardex->stockanterior = $stockanterior;
                $kardex->stockactual = $stockactual;
                $kardex->cantidad = $request->input('txtCantidad'.$value->producto_id);
                $kardex->preciocompra = $value->precio;
                //$kardex->almacen_id = 2;
                $kardex->detallemovimiento_id = $value->id;
                //$kardex->lote_id = $lote->id;
                $kardex->save();

                }   */
                } /*else{
            $lote = Lote::where('producto_id','=',$value->producto_id)->where('almacen_id','=',1)->where('queda','>',0)->get();
            $st = 0; $lo = "";
            if(count($lote)>0){
            foreach ($lote as $k => $v) {
            if(!is_null($request->input('txtCantidad'.$value->producto_id.'-'.$v->id)) && ($request->input('txtCantidad'.$value->producto_id.'-'.$v->id) + 0)>0){
            $v->queda = $v->queda - $request->input('txtCantidad'.$value->producto_id.'-'.$v->id);
            $v->save();

            $st = $st + $request->input('txtCantidad'.$value->producto_id.'-'.$v->id);
            $lo.=$v->id."@".$request->input('txtCantidad'.$value->producto_id.'-'.$v->id)."|";

            //KARDEX
            $ultimokardex = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('producto_id', '=', $value->producto_id)->where('movimiento.almacen_id', '=',1)->orderBy('kardex.id', 'DESC')->first();
            $stockanterior = 0;
            $stockactual = 0;

            if ($ultimokardex === NULL) {
            $stockactual = $cantidad;
            $kardex = new Kardex();
            $kardex->tipo = 'S';
            $kardex->fecha = date("Y-m-d");
            $kardex->stockanterior = $stockanterior;
            $kardex->stockactual = (-1)*$request->input('txtCantidad'.$value->producto_id.'-'.$v->id);
            $kardex->cantidad = $request->input('txtCantidad'.$value->producto_id.'-'.$v->id);
            $kardex->preciocompra = $value->precio;
            //$kardex->almacen_id = 2;
            $kardex->detallemovimiento_id = $value->id;
            $kardex->lote_id = $v->id;
            $kardex->save();
            }else{
            $stockanterior = $ultimokardex->stockactual;
            $stockactual = $ultimokardex->stockactual-$request->input('txtCantidad'.$value->producto_id.'-'.$v->id);
            $kardex = new Kardex();
            $kardex->tipo = 'S';
            $kardex->fecha = date('Y-m-d');
            $kardex->stockanterior = $stockanterior;
            $kardex->stockactual = $stockactual;
            $kardex->cantidad = $request->input('txtCantidad'.$value->producto_id.'-'.$v->id);
            $kardex->preciocompra = $value->precio;
            //$kardex->almacen_id = 2;
            $kardex->detallemovimiento_id = $value->id;
            $kardex->lote_id = $v->id;
            $kardex->save();

            }
            }
            }
            //DESPACHO DE Q LOTE
            if($lo!=""){
            $lo = substr($lo, 0, strlen($lo)-1);
            }
            $value->despachado = $st;
            $value->lote = $lo;
            $value->save();

            //STOCK
            $stock = Stock::where('producto_id','=',$value->producto_id)->where('almacen_id','=',1)->first();
            $stock->cantidad = $stock->cantidad - $st;
            $stock->save();
            }
            }*/
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

    public function eliminar($id, $listarLuego)
    {
        //
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Movimiento::find($id);
        $entidad  = 'Requerimiento';
        $formData = array('route' => array('requerimiento.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function destroy($id)
    {
        $error = DB::transaction(function () use ($id) {
            $movimiento = Movimiento::find($id);
            $movimiento->delete();
            $detalles = Detallemovimiento::where('movimiento_id', '=', $movimiento->id)->get();
            foreach ($detalles as $key => $value) {
                if ($value->producto->tipo != "SI") {

                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function pdf($id)
    {
        $entidad = 'Requerimiento';
        $dato    = Movimientoalmacen::find($id);

        $pdf = new TCPDF();
        $pdf::SetTitle('Requerimiento');
        $pdf::AddPage('');
        $pdf::Image("http://localhost:81/clinica/dist/img/logo2-ojos.jpg", 20, 7, 50, 15);
        $pdf::SetFont('helvetica', 'B', 12);
        $pdf::Cell(0, 7, $dato->tipodocumento->nombre . ' ' . str_pad($dato->numero, 8, '0', STR_PAD_LEFT) . '-' . date("Y", strtotime($dato->fecha)), 0, 0, 'C');
        $pdf::Ln();
        //$pdf::Image("http://localhost:81/clinica/dist/img/logo2-ojos.jpg", 20, 7, 50, 15);
        $pdf::Ln();
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(15, 7, "Fecha: ", 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 10);
        $pdf::Cell(80, 7, date("d/m/Y H:i:s", strtotime($dato->updated_at)), 0, 0, 'L');
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(25, 7, "Responsable: ", 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 10);
        $pdf::Cell(40, 7, $dato->responsable->nombres, 0, 0, 'L');
        $pdf::Ln();
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(25, 7, "Comentario: ", 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 10);
        $pdf::Cell(80, 7, $dato->comentario, 0, 0, 'L');
        $pdf::Ln();
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(15, 7, "Salida: ", 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 10);
        $pdf::Cell(80, 7, 'LOGISTICA', 0, 0, 'L');
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(15, 7, "Destino: ", 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 10);
        if (!is_null($dato->responsable->workertype)) {
            $pdf::Cell(40, 7, $dato->responsable->workertype->name, 0, 0, 'L');
        }
        $pdf::Ln();
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(8, 6, "Nro.", 1, 0, 'C');
        $pdf::Cell(15, 6, "Cant.", 1, 0, 'C');
        $pdf::Cell(90, 6, "Producto", 1, 0, 'C');
        $pdf::Cell(23, 6, "Presentacion", 1, 0, 'C');
        $pdf::Cell(15, 6, "Desp.", 1, 0, 'C');
        $pdf::Cell(40, 6, "Lote | Fecha Venc.", 1, 0, 'C');
        $pdf::Ln();
        $detalles = Detallemovimiento::where('movimiento_id', '=', $dato->id)->get();
        $c        = 0;
        foreach ($detalles as $key => $value) {
            $c = $c + 1;
            if (!is_null($value->lote) && trim($value->lote) != "") {
                $ls = explode("|", $value->lote);
                for ($i = 0; $i < count($ls); $i++) {
                    $datos = "";
                    $pdf::SetFont('helvetica', '', 8);
                    $pdf::Cell(8, 6, $c, 1, 0, 'R');
                    $pdf::Cell(15, 6, $value->cantidad, 1, 0, 'C');
                    $pdf::Cell(90, 6, $value->producto->nombre, 1, 0, 'L');
                    $pdf::Cell(23, 6, $value->producto->presentacion->nombre, 1, 0, 'C');
                    $list = explode("@", $ls[$i]);
                    $lote = Lote::find($list[0]);
                    $pdf::Cell(15, 6, $list[1], 1, 0, 'C');
                    $datos .= $lote->nombre . " | " . date("d/m/Y", strtotime($lote->fechavencimiento));
                    $pdf::Cell(40, 6, $datos, 1, 0, 'C');
                    $pdf::Ln();
                }
            } else {
                $pdf::SetFont('helvetica', '', 8);
                $pdf::Cell(8, 6, $c, 1, 0, 'R');
                $pdf::Cell(15, 6, $value->cantidad, 1, 0, 'C');
                $pdf::Cell(90, 6, $value->producto->nombre, 1, 0, 'L');
                $pdf::Cell(23, 6, $value->producto->presentacion->nombre, 1, 0, 'C');
                $pdf::Cell(15, 6, $value->despachado, 1, 0, 'C');
                $pdf::Cell(40, 6, '-', 1, 0, 'C');
                $pdf::Ln();
            }
        }
        $pdf::Ln();
        $pdf::Ln();
        $pdf::Ln();
        $pdf::Cell(25, 6, "", 0, 0, 'C');
        $pdf::Cell(50, 6, "_______________________________", 0, 0, 'C');
        $pdf::Cell(35, 6, "", 0, 0, 'C');
        $pdf::Cell(50, 6, "_______________________________", 0, 0, 'C');
        $pdf::Ln();
        $pdf::Cell(25, 6, "", 0, 0, 'C');
        $pdf::Cell(50, 6, "Usuario", 0, 0, 'C');
        $pdf::Cell(35, 6, "", 0, 0, 'C');
        $pdf::Cell(50, 6, "Entregado", 0, 0, 'C');
        $pdf::Ln();
        $pdf::Output('DocAlmacen.pdf');
    }

    public function generarNumero(Request $request)
    {
        $sucursal_id = Session::get('sucursal_id');
        //tipo movimiento 15 = requerimiento
        //tipo documento 24 = requerimiento
        $caja = Caja::where("sucursal_id", "=", $sucursal_id)->first();
        echo Movimiento::NumeroSigue2($caja->id, $sucursal_id, 15, 24);
    }

    public function generarNumeroRadmin(Request $request)
    {
        $sucursal_id = Session::get('sucursal_id');
        $radmin      = $request->input('radmin');
        echo Movimiento::NumeroSigueRadmin($sucursal_id, $radmin);
    }

    public function buscarproducto(Request $request)
    {
        $nombre = $request->input("nombre");

        $resultado = Producto::where('nombre', 'LIKE', '%' . strtoupper($nombre) . '%')->orderBy('nombre', 'ASC')->get();

        if (count($resultado) > 0) {
            $c = 0;
            foreach ($resultado as $key => $value) {
                $nombrepresentacion = '';
                if ($value->presentacion != null) {
                    $nombrepresentacion = $value->presentacion->nombre;
                }
                $data[$c] = array(
                    'nombre'          => $value->nombre,
                    'presentacion'    => $nombrepresentacion,
                    'presentacion_id' => $value->presentacion_id,
                    'precioventa'     => number_format($value->precioventa, 2, '.', ''),
                    'idproducto'      => $value->id,
                    'preciocompra'    => number_format($value->preciocompra, 2, '.', '')
                );
                $c++;
                if ($c == 5) {
                    break;
                }
            }
        } else {
            $data = array();
        }
        return json_encode($data);
    }

    public function comprobarRequerimientos() {

        date_default_timezone_set('America/Lima');
        $fecha       = date("Y-m-d");
        $dia         = date("d");
        $sucursal_id = 1;
        $user        = Auth::user();
        $caja        = Caja::where("sucursal_id", "=", $sucursal_id)->first();

        $requerimientodiario = Movimiento::where("sucursal_id", "=", $sucursal_id)
            ->where("caja_id", "=", $caja->id)
            ->where("tipodocumento_id", "=", 15)
            ->where("tipomovimiento_id", "=", 15)
            ->where("created_at", "LIKE", $fecha . "%")
            ->where("total", "=", 0)
            ->whereNull("deleted_at")
            ->first();

        if ($requerimientodiario == null) {

            $consultasmensuales = ConsultaNefrologica::join("person as p", "p.id", "=", "consultanefrologica.persona_id")
                ->join("historia as h", "h.person_id", "=", "p.id")
                ->where("estadoprogramacion", "=", 1)
                ->where(DB::raw("MONTH(consultanefrologica.fecha)"), "=", date("m"))
                ->where(DB::raw("YEAR(consultanefrologica.fecha)"), "=", date("Y"))
                ->where("h.baja", "!=", "S")
                ->get();
            $cantepo    = 0;
            $canthierro = 0;
            $cantvita   = 0;

            if (count($consultasmensuales) > 0) {
                foreach ($consultasmensuales as $c) {
                    $cadenaepo    = $c->cadenaepo;
                    $cadenahierro = $c->cadenahierro;
                    $cadenavita   = $c->cadenavita;

                    if ($c->cadenaepo !== null && $c->cadenaepo !== "") {
                        $cadenaepo = explode("**", $c->cadenaepo);
                        foreach ($cadenaepo as $epo) {
                            $diap = explode(";", $epo);
                            if ($diap[0] == $dia && $diap[1] !== "") {
                                $cantepo += $diap[1];
                                break;
                            }
                        }
                    }
                    if ($c->cadenahierro !== null && $c->cadenahierro !== "") {
                        $cadenahierro = explode("**", $c->cadenahierro);
                        foreach ($cadenahierro as $hierro) {
                            $diap = explode(";", $hierro);
                            if ($diap[0] == $dia && $diap[1] !== "") {
                                $canthierro += $diap[1];
                                break;
                            }
                        }
                    }
                    if ($c->cadenavita !== null && $c->cadenavita !== "") {
                        $cadenavita = explode("**", $c->cadenavita);
                        foreach ($cadenavita as $vita) {
                            $diap = explode(";", $vita);
                            if ($diap[0] == $dia && $diap[1] !== "") {
                                $cantvita += $diap[1];
                                break;
                            }
                        }
                    }
                }

                /////////////////////////////

                if ($cantepo !== 0 || $canthierro !== 0 || $cantvita !== 0) {

                    $total                                = 0;
                    $movimientoalmacen                    = new Movimiento();
                    $movimientoalmacen->sucursal_id       = 1;
                    $movimientoalmacen->caja_id           = $caja->id;
                    $movimientoalmacen->tipodocumento_id  = 15;
                    $movimientoalmacen->tipomovimiento_id = 15;
                    $movimientoalmacen->comentario        = "REQUERIMIENTO DE MEDICAMENTOS PARA LAS HEMODIÁLISIS PROGRAMADAS PARA EL DÍA " . date("d-m-Y");
                    $movimientoalmacen->numero            = Movimiento::NumeroSigueDocMovAlmacen($sucursal_id, 15, 15, 1);
                    $movimientoalmacen->fecha             = $fecha;
                    $movimientoalmacen->total             = $total;
                    $movimientoalmacen->responsable_id    = $user->person_id;
                    $movimientoalmacen->persona_id        = $user->person_id;
                    $movimientoalmacen->situacion         = 'P'; //PENDIENTE
                    $movimientoalmacen->save();
                    $movimiento_id = $movimientoalmacen->id;

                    if ($cantepo !== 0) {
                        $cantidad                    = $cantepo;
                        $detalleVenta                = new Detallemovimiento();
                        $detalleVenta->cantidad      = $cantidad;
                        $detalleVenta->precio        = 0;
                        $detalleVenta->subtotal      = 0;
                        $detalleVenta->movimiento_id = $movimiento_id;
                        $detalleVenta->producto_id   = 1;
                        $detalleVenta->save();
                    }

                    if ($canthierro !== 0) {
                        $cantidad                    = $canthierro;
                        $detalleVenta                = new Detallemovimiento();
                        $detalleVenta->cantidad      = $cantidad;
                        $detalleVenta->precio        = 0;
                        $detalleVenta->subtotal      = 0;
                        $detalleVenta->movimiento_id = $movimiento_id;
                        $detalleVenta->producto_id   = 2;
                        $detalleVenta->save();
                    }

                    if ($cantvita !== 0) {
                        $cantidad                    = $cantvita;
                        $detalleVenta                = new Detallemovimiento();
                        $detalleVenta->cantidad      = $cantidad;
                        $detalleVenta->precio        = 0;
                        $detalleVenta->subtotal      = 0;
                        $detalleVenta->movimiento_id = $movimiento_id;
                        $detalleVenta->producto_id   = 3;
                        $detalleVenta->save();
                    }
                }
            }

            /////////////////////////////

        }
    }

    public function buscarProveedor(Request $request) {
        $dat = array();
        $error = DB::transaction(function () use ($request, &$dat) {
            $idproducto = $request->input("idproducto");
            $detalle = Detallemovimiento::where("producto_id", "=", $idproducto)->whereNotNull("proveedor_id")->first();

            if($detalle !== NULL) {
                $emp = Person::find($detalle->proveedor_id);
                if($emp !== NULL) {
                    $dat[0]     = array(
                        "ruc"       => $emp->ruc, 
                        "razon"     => $emp->bussinesname, 
                        "direccion" => $emp->direccion
                    );
                }                   
            }                
        });
        return is_null($error) ? json_encode($dat) : $error;
    }
}
