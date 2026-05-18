<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\HistoriaClinica;
use DateTime;
use App\Http\Requests;
use App\ConsultaNefrologica;
use App\Person;
use App\Cie;
use App\Historia;
use App\Librerias\Libreria;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConsultaNefrologica2Controller extends Controller
{

    protected $folderview      = 'app.consultanefrologica2';
    protected $tituloAdmin     = 'Consulta Nefrologica ESSALUD';
    protected $tituloRegistrar = 'Registrar consultanefrologica2';
    protected $tituloModificar = 'Modificar consultanefrologica2';
    protected $tituloEliminar  = 'Eliminar consultanefrologica2';
    protected $rutas           = array('create' => 'consultanefrologica2.create', 
            'edit'   => 'consultanefrologica2.edit', 
            'delete' => 'consultanefrologica2.eliminar',
            'search' => 'consultanefrologica2.buscar',
            'index'  => 'consultanefrologica2.index',
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
        $this->consultasMensuales();
        date_default_timezone_set('America/Lima');
        $mes              = date("m");
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'ConsultaNefrologica';
        $nombre           = Libreria::getParam($request->input('nombre'));
        $estado           = Libreria::getParam($request->input('estado'));
        $examenes         = Libreria::getParam($request->input('examenes'));
        $programacion     = Libreria::getParam($request->input('programacion'));
        $estado2          = Libreria::getParam($request->input('estado2'));
        $numero           = Libreria::getParam($request->input('numero'));
        $messs            = Libreria::getParam($request->input('messs'));
        $anooo            = Libreria::getParam($request->input('anooo'));
        $resultado        = Historia::join('person', 'person.id', '=', 'historia.person_id')
                            ->where(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%'.strtoupper($nombre).'%')
                            ->where('historia.convenio_id', '=', 2)
                            ->where('historia.baja', '!=', "S")
                            ->where('historia.numero', 'LIKE', '%'.$numero.'%')
                            ->select('person.nombres', 'person.apellidopaterno', 'person.apellidomaterno', 'historia.numero', 'historia.id as hid', 'person.dni', 'person.id as pid', 'c.id as cid')
                            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
                            ->where(DB::raw("MONTH(c.fecha)"), "=", $messs)
                            ->where(DB::raw("YEAR(c.fecha)"), "=", $anooo)
                            ->orderBy(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'));

        if($examenes=="1") {
            $resultado = $resultado->whereNotNull("c.estadoexamen");
        } elseif($examenes=="2") {
            $resultado = $resultado->whereNull("c.estadoexamen");
        }

        $lista            = $resultado->get();     

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Historia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI/CE', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Examenes', 'numero' => '1');
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'messs', 'anooo'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'messs', 'anooo'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'ConsultaNefrologica';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $messs = array(
            "1" => "ENERO",
            "2" => "FEBRERO",
            "3" => "MARZO",
            "4" => "ABRIL",
            "5" => "MAYO",
            "6" => "JUNIO",
            "7" => "JULIO",
            "8" => "AGOSTO",
            "9" => "SETIEMBRE",
            "10" => "OCTUBRE",
            "11" => "NOVIEMBRE",
            "12" => "DICIEMBRE"
        );
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', "messs"));
    }

    public function resultados(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes = date('m');
        $cid = $request->input("cid");
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $pid = $request->input('pid');
        $tipo = $request->input('situacion');
        $tipos = array('NUEVO'=>'NUEVO', 'MENSUAL'=>'MENSUAL', 'BIMENSUAL'=>'BIMENSUAL', 'TRIMESTRAL'=>'TRIMESTRAL', 'SEMESTRAL'=>'SEMESTRAL');
        $historia = Historia::where('person_id', '=', $pid)->first();
        $entidad  = 'HC';
        $c1 = ConsultaNefrologica::find($cid);
        //Analizo siguiente tio de consulta del siguiente mes:

        $hc = $c1;

        $fechasHD = "";
        $ppre = "";
        $ppos = "";
        $horas = "";
        $atencion_id = "";
        $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
            ->where("historia.person_id", "=", $pid)
            ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha)))
            ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha)))
            ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
            ->where("historiaclinica.estado", "!=", "C")
            ->select("historiaclinica.id", "historiaclinica.txtPesoInicial2", "historiaclinica.txtPesoFinal2", "historiaclinica.txtHorasHemodialisis")
            ->first();

        if($atencion!==null) {
            $ppre = $atencion->txtPesoInicial2;
            $ppos = $atencion->txtPesoFinal2;
            $horas = $atencion->txtHorasHemodialisis;
            $atencion_id = $atencion->id;
        }

        //dd($atencion_id . "--");

        $atencionesMensuales = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
            ->where("historia.person_id", "=", $pid)
            ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha)))
            ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha)))
            ->where("historiaclinica.estado", "!=", "C")
            ->select("historiaclinica.id", "historiaclinica.fecha_atencion")            
            ->get();
        foreach ($atencionesMensuales as $aM) {
            $fechasHD .= '<option value="' . $aM->id . '">' . date("d-m-Y", strtotime($aM->fecha_atencion)) . '</option>';
        }

        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';
        return view($this->folderview.'.resultados')->with(compact('tipo', 'tipos', 'hc', 'historia', 'formData', 'entidad', 'boton', 'listar', "fechasHD", "ppre", "ppos", "horas", "atencion_id"));
    }

    public function storeresultados(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id = $request->input('id1');
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $dat='';
        $error = DB::transaction(function() use($request,$id,&$dat){
            $c1 = ConsultaNefrologica::find($id);
            $c1->txtEli = ($request->input("txtEli")==""?NULL:$request->input("txtEli"));
            $c1->txtDet = ($request->input("txtDet")==""?NULL:$request->input("txtDet"));
            $c1->txtDet2 = ($request->input("txtDet2")==""?NULL:$request->input("txtDet2"));
            $c1->txtLg1 = ($request->input("txtLg1")==""?NULL:$request->input("txtLg1"));
            $c1->txtLg2 = ($request->input("txtLg2")==""?NULL:$request->input("txtLg2"));
            $c1->txtVacu1 = ($request->input("txtVacu1")==""?NULL:date("Y-m-d", strtotime($request->input("txtVacu1"))));
            $c1->txtVacu2 = ($request->input("txtVacu2")==""?NULL:date("Y-m-d", strtotime($request->input("txtVacu2"))));
            $c1->txtVacu3 = ($request->input("txtVacu3")==""?NULL:date("Y-m-d", strtotime($request->input("txtVacu3"))));
            $c1->txtVacu4 = ($request->input("txtVacu4")==""?NULL:date("Y-m-d", strtotime($request->input("txtVacu4"))));
            $c1->txtNeumo = ($request->input("txtNeumo")==""?NULL:date("Y-m-d", strtotime($request->input("txtNeumo"))));
            $c1->txtDet3 = ($request->input("txtDet3")==""?NULL:$request->input("txtDet3"));
            $c1->txtDet4 = ($request->input("txtDet4")==""?NULL:$request->input("txtDet4"));
            $c1->txtUre = ($request->input("txtUre")==""?NULL:$request->input("txtUre"));
            $c1->txtUre2 = ($request->input("txtUre2")==""?NULL:$request->input("txtUre2"));
            $c1->txtCre = ($request->input("txtCre")==""?NULL:$request->input("txtCre"));
            $c1->txtCre2 = ($request->input("txtCre2")==""?NULL:$request->input("txtCre2"));
            $c1->txtHem = ($request->input("txtHem")==""?NULL:$request->input("txtHem"));
            $c1->txtDos = ($request->input("txtDos")==""?NULL:$request->input("txtDos"));
            $c1->txtEle = ($request->input("txtEle")==""?NULL:$request->input("txtEle"));
            $c1->txtSodio = ($request->input("txtSodio")==""?NULL:$request->input("txtSodio"));
            $c1->txtPotasio = ($request->input("txtPotasio")==""?NULL:$request->input("txtPotasio"));
            $c1->txtCloro = ($request->input("txtCloro")==""?NULL:$request->input("txtCloro"));
            $c1->txtFos = ($request->input("txtFos")==""?NULL:$request->input("txtFos"));
            $c1->txtCal = ($request->input("txtCal")==""?NULL:$request->input("txtCal"));
            $c1->txtCal2 = ($request->input("txtCal2")==""?NULL:$request->input("txtCal2"));
            $c1->txtPro = ($request->input("txtPro")==""?NULL:$request->input("txtPro"));
            $c1->txtFos2 = ($request->input("txtFos2")==""?NULL:$request->input("txtFos2"));
            $c1->txtTgo = ($request->input("txtTgo")==""?NULL:$request->input("txtTgo"));
            $c1->txtTgp = ($request->input("txtTgp")==""?NULL:$request->input("txtTgp"));
            $c1->txtPru = ($request->input("txtPru")==""?NULL:$request->input("txtPru"));
            $c1->txtPar = ($request->input("txtPar")==""?NULL:$request->input("txtPar"));
            $c1->txtHie = ($request->input("txtHie")==""?NULL:$request->input("txtHie"));
            $c1->txtFer = ($request->input("txtFer")==""?NULL:$request->input("txtFer"));
            $c1->txtSat = ($request->input("txtSat")==""?NULL:$request->input("txtSat"));
            $c1->txtAlbu = ($request->input("txtAlbu")==""?NULL:$request->input("txtAlbu"));
            $c1->txtGlobu = ($request->input("txtGlobu")==""?NULL:$request->input("txtGlobu"));
            $c1->txtTransfe = ($request->input("txtTransfe")==""?NULL:$request->input("txtTransfe"));
            $c1->txtPcr = ($request->input("txtPcr")==""?NULL:$request->input("txtPcr"));
            $c1->estadoexamen = 1;
            $c1->save();

            //PARA LOS DATOS DE KTV
            $txtFechaKTV = $request->input("txtFechaKTV");

            if($txtFechaKTV!=="") {
                //PRIMERO CAPTURO LA DESCRIPCIÓN DE txtMuestraAnalisis SI HUBIERA UN HD CON MUESTRA DE ANALISIS
                $txtMuestraAnalisis0 = "SE TOMA MUESTRAS MENSUALES";
                $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                    ->where("historia.person_id", "=", $c1->persona->id)
                    ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha)))
                    ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha)))
                    ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
                    ->where("historiaclinica.estado", "!=", "C")
                    ->select("historiaclinica.id", "historiaclinica.txtPesoInicial2", "historiaclinica.txtPesoFinal2", "historiaclinica.txtHorasHemodialisis", "historiaclinica.txtMuestraAnalisis")
                    ->first();

                if($atencion!==null) {
                    $txtMuestraAnalisis0 = $atencion->txtMuestraAnalisis;
                }

                //SEGUNDO ELIMINO TODOS LOS txtMuestraAnalisis ASUMIENDO QUE NINGÚNA HD VA A TOMAR DATOS MENSUALES
                $atencionesMensuales = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                    ->where("historia.person_id", "=", $c1->persona->id)
                    ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha)))
                    ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha)))
                    ->select("historiaclinica.id")
                    ->get();
                foreach ($atencionesMensuales as $aM) {
                    $aM = HistoriaClinica::find($aM->id);
                    $aM->txtMuestraAnalisis = "";
                    $aM->txtHorasHemodialisis = "";
                    $aM->txtPesoFinal2 = "";
                    $aM->save();
                }

                $historita = HistoriaClinica::find($txtFechaKTV);
                $historita->txtHorasHemodialisis = $request->input("txtHorasHemodialisisKTV");
                //$historita->txtPesoInicial2 = $request->input("txtPesoInicial2KTV");
                $historita->txtPesoFinal2 = $request->input("txtPesoFinal2KTV");
                $historita->txtMuestraAnalisis = $txtMuestraAnalisis0;
                $historita->save(); 
            } else {
                //SEGUNDO ELIMINO TODOS LOS txtMuestraAnalisis ASUMIENDO QUE NINGÚNA HD VA A TOMAR DATOS MENSUALES
                $atencionesMensuales = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                    ->where("historia.person_id", "=", $c1->persona->id)
                    ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha)))
                    ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha)))
                    ->select("historiaclinica.id")
                    ->get();
                foreach ($atencionesMensuales as $aM) {
                    $aM = HistoriaClinica::find($aM->id);
                    $aM->txtMuestraAnalisis = "";
                    $aM->txtHorasHemodialisis = "";
                    $aM->txtPesoFinal2 = "";
                    $aM->save();
                }
            }
            
            $dat="OK";            
        });
        return is_null($error) ? $dat : $error;
    }

    public function consultasMensuales() {

        date_default_timezone_set('America/Lima');
        $mes = date('m');
        $ano = date('Y');

        $lista = Historia::where('historia.convenio_id', '=', 2)->where('historia.baja', '!=', "S")->get();

        //Comprobación de consultas

        foreach ($lista as $row) {
            $consultas = ConsultaNefrologica::where('persona_id', '=', $row->person_id)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->select("id")->get();
            if(count($consultas)===0) {
                //BAZAL
                $cons = new ConsultaNefrologica();
                $cons->fecha = date('Y-m-d');
                $cons->persona_id = $row->person_id;
                $cons->save();
            }
        }
    }
}
