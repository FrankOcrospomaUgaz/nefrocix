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

class Requerimiento2Controller extends Controller
{

    protected $folderview      = 'app.requerimiento2';
    protected $tituloAdmin     = 'Requerimiento de Administración';
    protected $tituloRegistrar = 'Registrar Requerimiento de Administración';
    protected $tituloModificar = 'Despachar Requerimiento de Administración';
    protected $tituloVer       = 'Ver Requerimiento de Administración';
    protected $tituloEliminar  = 'Eliminar Requerimiento de Administración';
    protected $rutas           = array(
        'create' => 'requerimiento2.create',
        'edit'   => 'requerimiento2.edit',
        'show'   => 'requerimiento2.show',
        'delete' => 'requerimiento2.eliminar',
        'search' => 'requerimiento2.buscar',
        'index'  => 'requerimiento2.index',
    );

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $entidad          = 'Requerimiento2';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user             = Auth::user();
        return view($this->folderview . '.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'user'));
    }

    public function buscar(Request $request) {
        $pagina      = $request->input('page');
        $filas       = $request->input('filas');
        $entidad     = 'Requerimiento2';
        $fechainicio = Libreria::getParam($request->input('fechainicio'));
        $fechafin    = Libreria::getParam($request->input('fechafin'));
        $comentary   = Libreria::getParam($request->input('comentary'));

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $user = Auth::user();

        $resultado = Movimientoalmacen::where('tipomovimiento_id', '=', '15')
            ->where('sucursal_id', '=', $sucursal_id)
            ->where("radmin", "=", 1)
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
        $resultado  = $resultado->select('movimiento.*');
        $lista      = $resultado->get();
        $cabecera   = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[] = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[] = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[] = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[] = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[] = array('valor' => 'Total Aprox', 'numero' => '1');
        $cabecera[] = array('valor' => 'Total Real', 'numero' => '1');
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

    public function create(Request $request) {
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $entidad       = 'Requerimiento2';
        $requerimiento = null;
        $formData      = array('requerimiento2.store');
        $formData      = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton         = 'Registrar';
        $radmin        = 1;
        return view($this->folderview . '.mant')->with(compact('requerimiento', 'formData', 'entidad', 'boton', 'listar', 'radmin'));
    }

    public function store(Request $request) {
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
            $total                                = $request->input("total");
            $movimientoalmacen                    = new Movimiento();
            $movimientoalmacen->radmin            = 1;
            $movimientoalmacen->sucursal_id       = $sucursal_id;
            //$movimientoalmacen->caja_id           = $caja->id;
            //$movimientoalmacen->tipodocumento_id  = 24;
            //$movimientoalmacen->almacen_id        = 1;
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
            $rucs          = explode(";", $arr[2]);
            $tipos         = explode(";", $arr[3]);
            $subtotales    = explode(";", $arr[4]);
            $precios       = explode(";", $arr[5]);
            $nombres       = explode(";", $arr[6]);
            $direcciones   = explode(";", $arr[7]);

            for ($c = 0; $c < count($ides); $c++) {
                $cantidad                    = str_replace(',', '', $cantidades[$c]);
                $ruc                         = str_replace(',', '', $rucs[$c]);
                $tipo                        = str_replace(',', '', $tipos[$c]);
                $subtotal                    = str_replace(',', '', $subtotales[$c]);
                $precio                      = str_replace(',', '', $precios[$c]);
                $nombre                      = str_replace(',', '', $nombres[$c]);
                $direccion                   = str_replace(',', '', $direcciones[$c]);
                $proveedor_id                = NULL;

                $producto                    = Producto::find($ides[$c]);
                $producto->preciocompra      = $precio;
                $producto->save();

                if($ruc !== "") {
                    $proveedor               = Person::select("id")->where("ruc", "=", $ruc)->first();
                    if($proveedor !== NULL) {
                        $proveedor_id = $proveedor->id;
                    } else {
                        $empr = new Person();
                        $empr->ruc          = $ruc;
                        $empr->direccion    = $direccion;
                        $empr->bussinesname = $nombre;
                        $empr->save();
                        $proveedor_id = $empr->id;
                    }
                }

                $detalleVenta                = new Detallemovimiento();
                $detalleVenta->cantidad      = $cantidad;
                $detalleVenta->precio        = $precio;
                $detalleVenta->subtotal      = $subtotal;
                $detalleVenta->proveedor_id  = $proveedor_id;
                $detalleVenta->movimiento_id = $movimiento_id;
                $detalleVenta->tipo          = $tipo;
                $detalleVenta->producto_id   = $ides[$c];
                $detalleVenta->save();
            }
            $dat[0] = array(
                "respuesta"        => "OK", 
                "requerimiento_id" => $movimientoalmacen->id, 
                "ind"              => 0, 
                "second_id"        => 0
            );
        });
        return is_null($error) ? json_encode($dat) : $error;

    }

    public function show(Request $request, $id) {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        $tipo   = $request->input("tipo");
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $requerimiento = Movimientoalmacen::find($id);
        $entidad       = 'Requerimiento2';
        $formData      = array('requerimiento2.update', $id);
        $formData      = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton         = 'Modificar';
        if($tipo == 1) {
            $detalles      = Detallemovimiento::where('movimiento_id', '=', $requerimiento->id)->whereNull("tipo2")->get();
        } else {
            $detalles      = Detallemovimiento::where('movimiento_id', '=', $requerimiento->id)->whereNotNull("tipo2")->get();
        }    

        return view($this->folderview . '.mantView')->with(compact('requerimiento', 'formData', 'entidad', 'boton', 'listar', 'detalles', 'tipo'));
    }

    public function edit($id, Request $request) {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $requerimiento = Movimiento::find($id);
        $entidad       = 'Requerimiento2';
        $formData      = array('requerimiento2.update', $id);
        $formData      = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton         = 'Despachar';
        $detalles      = Detallemovimiento::where('movimiento_id', '=', $requerimiento->id)->get();
        $radmin        = 2;
        return view($this->folderview . '.mant')->with(compact('requerimiento', 'formData', 'entidad', 'boton', 'listar', 'detalles', 'radmin'));
    }

    public function generarNumeroDocMovAlmacen() {
        $sucursal_id       = 1;
        $tipodoc           = 9;
        $almacen_id        = 1;
        $tipomovimiento_id = 5;
        $numero            = Movimiento::NumeroSigueDocMovAlmacen($sucursal_id, $tipomovimiento_id, $tipodoc, $almacen_id);
        return $numero;
    }

    public function update(Request $request, $id) {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
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

        $error = DB::transaction(function () use ($request, $sucursal_id, &$dat, $id) {
            $total                                = $request->input("total");
            $movimientoalmacen                    = new Movimiento();
            $movimientoalmacen->radmin            = 1;
            $movimientoalmacen->sucursal_id       = $sucursal_id;
            //$movimientoalmacen->caja_id           = $caja->id;
            $movimientoalmacen->tipodocumento_id  = 24;
            $movimientoalmacen->almacen_id        = 1;
            $movimientoalmacen->tipomovimiento_id = 5;
            $movimientoalmacen->movimiento_id     = $id;
            $movimientoalmacen->comentario        = Libreria::obtenerParametro($request->input('comentario'));
            $movimientoalmacen->numero            = $request->input('numerodocumento');
            $movimientoalmacen->fecha             = $request->input('fecha');
            $movimientoalmacen->total             = $total;
            $user                                 = Auth::user();
            $movimientoalmacen->responsable_id    = $user->person_id;
            $movimientoalmacen->persona_id        = $user->person_id;
            $movimientoalmacen->situacion         = 'D'; //DESPACHADO
            $movimientoalmacen->save();
            $movimiento_id = $movimientoalmacen->id;
            $arr           = explode("@", $request->input('listaProducto'));
            $ides          = explode(";", $arr[0]);
            $cantidades    = explode(";", $arr[1]);
            $rucs          = explode(";", $arr[2]);
            $tipos         = explode(";", $arr[3]);
            $subtotales    = explode(";", $arr[4]);
            $precios       = explode(";", $arr[5]);
            $nombres       = explode(";", $arr[6]);
            $direcciones   = explode(";", $arr[7]);

            for ($c = 0; $c < count($ides); $c++) {
                $cantidad                    = str_replace(',', '', $cantidades[$c]);
                $ruc                         = str_replace(',', '', $rucs[$c]);
                $tipo                        = str_replace(',', '', $tipos[$c]);
                $subtotal                    = str_replace(',', '', $subtotales[$c]);
                $precio                      = str_replace(',', '', $precios[$c]);
                $nombre                      = str_replace(',', '', $nombres[$c]);
                $direccion                   = str_replace(',', '', $direcciones[$c]);
                $proveedor_id                = NULL;

                $producto                    = Producto::find($ides[$c]);
                $producto->preciocompra      = $precio;
                $producto->save();

                if($ruc !== "") {
                    $proveedor               = Person::select("id")->where("ruc", "=", $ruc)->first();
                    if($proveedor !== NULL) {
                        $proveedor_id = $proveedor->id;
                    } else {
                        $empr = new Person();
                        $empr->ruc          = $ruc;
                        $empr->direccion    = $direccion;
                        $empr->bussinesname = $nombre;
                        $empr->save();
                        $proveedor_id = $empr->id;
                    }
                }

                $detalleVenta                = new Detallemovimiento();
                $detalleVenta->cantidad      = $cantidad;
                $detalleVenta->precio        = $precio;
                $detalleVenta->subtotal      = $subtotal;
                $detalleVenta->proveedor_id  = $proveedor_id;
                $detalleVenta->movimiento_id = $movimiento_id;
                $detalleVenta->tipo          = $tipo;
                $detalleVenta->producto_id   = $ides[$c];
                $detalleVenta->save();

                $almacen_id = 1;

                //ABASTECIENDO STOCK

                $ultimokardex = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('producto_id', '=', $producto->id)->where('movimiento.almacen_id', '=', $almacen_id)->orderBy('kardex.id', 'DESC')->first();
                // Creamos el lote para el producto
                $lote = new Lote();
                //$lote->nombre  = "ABASTECIMIENTO-REQUERIMIENTO-ADMINISTRACIÓN";
                //$lote->fechavencimiento  = date('Y-m-d', strtotime($request->input('fechavencimiento'.$i)));
                $lote->cantidad = $cantidad;
                $lote->queda = $cantidad;
                $lote->producto_id = $producto->id;
                $lote->almacen_id = $almacen_id;
                $lote->save();

                $stock = Stock::where('producto_id', $producto->id)->where('almacen_id', $almacen_id)->first();
                if (is_null($stock)) {
                    $stock = new Stock();
                    $stock->producto_id = $producto->id;
                    $stock->almacen_id = $almacen_id;
                }
                $stock->cantidad += $cantidad;
                $stock->save();

                $detalleVenta->lote = $lote->id . ';' . $cantidad . '@';

                $stockanterior = 0;
                $stockactual = 0;

                if ($ultimokardex === NULL) {
                    $stockactual = $cantidad;
                    $kardex = new Kardex();
                    $kardex->tipo = 'I';
                    $kardex->fecha = date('d/m/Y', strtotime($request->input('fecha')));
                    $kardex->stockanterior = $stockanterior;
                    $kardex->stockactual = $stockactual;
                    $kardex->cantidad = $cantidad;
                    $kardex->almacen_id = $almacen_id;
                    $kardex->detallemovimiento_id = $detalleVenta->id;
                    $kardex->lote_id = $lote->id;
                    $kardex->save();
                    
                } else {
                    $stockanterior = $ultimokardex->stockactual;
                    $stockactual = $ultimokardex->stockactual+$cantidad;
                    $kardex = new Kardex();
                    $kardex->tipo = 'I';
                    $kardex->fecha = date('d/m/Y', strtotime($request->input('fecha')));
                    $kardex->stockanterior = $stockanterior;
                    $kardex->stockactual = $stockactual;
                    $kardex->cantidad = $cantidad;
                    $kardex->almacen_id = $almacen_id;
                    $kardex->detallemovimiento_id = $detalleVenta->id;
                    $kardex->lote_id = $lote->id;
                    $kardex->save();
                }
            }

            $movprincipal = Movimiento::find($id);
            $movprincipal->situacion = "D";
            $movprincipal->movimiento_id = $movimiento_id;
            $movprincipal->save();

            $dat[0] = array(
                "respuesta"        => "OK", 
                "requerimiento_id" => $movimientoalmacen->id, 
                "ind"              => 0, 
                "second_id"        => 0
            );
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function eliminar($id, $listarLuego) {
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
        $entidad  = 'Requerimiento2';
        $formData = array('route' => array('requerimiento2.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function destroy($id) {
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

    public function pdf($id) {
        $entidad = 'Requerimiento2';
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

    public function show2(Request $request) {
        $id = $request->input("id");
        echo $id;
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $requerimiento = Movimientoalmacen::find($id);
        $entidad       = 'Requerimiento2';
        $formData      = array('requerimiento2.update', $id);
        $formData      = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton         = 'Modificar';
        $detalles      = Detallemovimiento::where('movimiento_id', '=', $requerimiento->id)->whereNull("tipo2")->get();

        return view($this->folderview . '.mantView')->with(compact('requerimiento', 'formData', 'entidad', 'boton', 'listar', 'detalles'));
    }
}
