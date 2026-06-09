<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\HistoriaClinica;
use DateTime;
use App\Http\Requests;
use App\ConsultaNefrologica;
use App\ConsultaNefrologicaAnalisisAdicional;
use App\LaboratorioRangoReferencial;
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

    private $rangosConfig = null;


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
        $tipos = array('NUEVO'=>'NUEVO', 'MENSUAL'=>'MENSUAL', 'BIMENSUAL'=>'BIMENSUAL', 'TRIMESTRAL'=>'TRIMESTRAL', 'SEMESTRAL'=>'SEMESTRAL', 'ANUAL'=>'ANUAL');
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
        $periodicidad = $this->obtenerPeriodicidad($c1);
        $analisisAdicionales = ConsultaNefrologicaAnalisisAdicional::where('consultanefrologica_id', '=', $c1->id)->orderBy('orden', 'asc')->get();
        return view($this->folderview.'.resultados')->with(compact('tipo', 'tipos', 'hc', 'historia', 'formData', 'entidad', 'boton', 'listar', "fechasHD", "ppre", "ppos", "horas", "atencion_id", 'periodicidad', 'analisisAdicionales'));
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
            $c1->txtLeucocitos = ($request->input("txtLeucocitos")==""?NULL:$request->input("txtLeucocitos"));
            $c1->txtHematies = ($request->input("txtHematies")==""?NULL:$request->input("txtHematies"));
            $c1->txtPlaquetas = ($request->input("txtPlaquetas")==""?NULL:$request->input("txtPlaquetas"));
            $c1->txtVcm = ($request->input("txtVcm")==""?NULL:$request->input("txtVcm"));
            $c1->txtHcm = ($request->input("txtHcm")==""?NULL:$request->input("txtHcm"));
            $c1->txtCcmh = ($request->input("txtCcmh")==""?NULL:$request->input("txtCcmh"));
            $c1->txtRdw = ($request->input("txtRdw")==""?NULL:$request->input("txtRdw"));
            $c1->txtRdwSd = ($request->input("txtRdwSd")==""?NULL:$request->input("txtRdwSd"));
            $c1->txtVpm = ($request->input("txtVpm")==""?NULL:$request->input("txtVpm"));
            $c1->txtAbastonados = ($request->input("txtAbastonados")==""?NULL:$request->input("txtAbastonados"));
            $c1->txtSegmentados = ($request->input("txtSegmentados")==""?NULL:$request->input("txtSegmentados"));
            $c1->txtEosinofilos = ($request->input("txtEosinofilos")==""?NULL:$request->input("txtEosinofilos"));
            $c1->txtBasofilos = ($request->input("txtBasofilos")==""?NULL:$request->input("txtBasofilos"));
            $c1->txtMonocitos = ($request->input("txtMonocitos")==""?NULL:$request->input("txtMonocitos"));
            $c1->txtLinfocitos = ($request->input("txtLinfocitos")==""?NULL:$request->input("txtLinfocitos"));
            $c1->txtColesterol = ($request->input("txtColesterol")==""?NULL:$request->input("txtColesterol"));
            $c1->txtTrigliceridos = ($request->input("txtTrigliceridos")==""?NULL:$request->input("txtTrigliceridos"));
            $c1->txtHdl = ($request->input("txtHdl")==""?NULL:$request->input("txtHdl"));
            $c1->txtLdl = ($request->input("txtLdl")==""?NULL:$request->input("txtLdl"));
            $c1->txtVitaminaB12 = ($request->input("txtVitaminaB12")==""?NULL:$request->input("txtVitaminaB12"));
            $c1->txtAcidoFolico = ($request->input("txtAcidoFolico")==""?NULL:$request->input("txtAcidoFolico"));
            $c1->txtAcidoUrico = ($request->input("txtAcidoUrico")==""?NULL:$request->input("txtAcidoUrico"));
            $periodicidadExamen = $this->normalizarPeriodicidad($request->input("periodicidad_examen"), $this->obtenerPeriodicidad($c1));
            $c1->situacion = $this->periodicidadCodigo($periodicidadExamen);
            $c1->txtTipoDatos = $this->periodicidadTipoDatos($periodicidadExamen);
            $c1->estadoexamen = 1;
            $c1->save();

            ConsultaNefrologicaAnalisisAdicional::where('consultanefrologica_id', '=', $c1->id)->delete();
            $nombresAdicionales = $request->input('adicional_nombre', array());
            $resultadosAdicionales = $request->input('adicional_resultado', array());
            $unidadesAdicionales = $request->input('adicional_unidad', array());
            $referenciasAdicionales = $request->input('adicional_referencia', array());
            if (is_array($nombresAdicionales)) {
                foreach ($nombresAdicionales as $index => $nombreAdicional) {
                    $nombreAdicional = trim($nombreAdicional);
                    if ($nombreAdicional === '') {
                        continue;
                    }
                    $adicional = new ConsultaNefrologicaAnalisisAdicional();
                    $adicional->consultanefrologica_id = $c1->id;
                    $adicional->nombre = $nombreAdicional;
                    $adicional->resultado = (isset($resultadosAdicionales[$index]) && $resultadosAdicionales[$index] != "" ? $resultadosAdicionales[$index] : NULL);
                    $adicional->unidad = (isset($unidadesAdicionales[$index]) && $unidadesAdicionales[$index] != "" ? $unidadesAdicionales[$index] : NULL);
                    $adicional->rango_referencial = (isset($referenciasAdicionales[$index]) && $referenciasAdicionales[$index] != "" ? $referenciasAdicionales[$index] : NULL);
                    $adicional->orden = $index;
                    $adicional->save();
                }
            }

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

    private function obtenerPeriodicidad($hc)
    {
        if ($hc === null) {
            return 'MENSUAL';
        }

        if ($hc->situacion === 'N') {
            return 'NUEVO';
        }

        $periodicidadSituacion = $this->periodicidadDesdeCodigo($hc->situacion);
        if ($periodicidadSituacion === 'ANUAL' || $periodicidadSituacion === 'NUEVO') {
            return $periodicidadSituacion;
        }

        switch ((int) $hc->txtTipoDatos) {
            case 2:
            case 4:
                return 'BIMENSUAL';
            case 3:
                return 'TRIMESTRAL';
            case 0:
                $annualFields = array($hc->txtEli, $hc->txtPru, $hc->txtVitaminaB12, $hc->txtAcidoFolico, $hc->txtAcidoUrico);
                if (count(array_filter($annualFields, function ($value) {
                    return $value !== null && $value !== '';
                })) > 0) {
                    return 'ANUAL';
                }
                return 'SEMESTRAL';
            case 5:
            case 1:
            default:
                return $periodicidadSituacion !== null ? $periodicidadSituacion : 'MENSUAL';
        }
    }

    private function normalizarPeriodicidad($periodicidad, $default = 'MENSUAL')
    {
        $periodicidad = strtoupper(trim((string) $periodicidad));
        $permitidas = array('MENSUAL', 'BIMENSUAL', 'TRIMESTRAL', 'SEMESTRAL', 'ANUAL', 'NUEVO');

        return in_array($periodicidad, $permitidas) ? $periodicidad : $default;
    }

    private function periodicidadDesdeCodigo($codigo)
    {
        $mapa = array(
            'M' => 'MENSUAL',
            'M-B' => 'BIMENSUAL',
            'M-T' => 'TRIMESTRAL',
            'M-B-T-S' => 'SEMESTRAL',
            'ANUAL' => 'ANUAL',
            'N' => 'NUEVO',
            'MENSUAL' => 'MENSUAL',
            'BIMENSUAL' => 'BIMENSUAL',
            'TRIMESTRAL' => 'TRIMESTRAL',
            'SEMESTRAL' => 'SEMESTRAL',
            'NUEVO' => 'NUEVO',
        );

        return isset($mapa[$codigo]) ? $mapa[$codigo] : null;
    }

    private function periodicidadCodigo($periodicidad)
    {
        $mapa = array(
            'MENSUAL' => 'M',
            'BIMENSUAL' => 'M-B',
            'TRIMESTRAL' => 'M-T',
            'SEMESTRAL' => 'M-B-T-S',
            'ANUAL' => 'ANUAL',
            'NUEVO' => 'N',
        );

        return isset($mapa[$periodicidad]) ? $mapa[$periodicidad] : 'M';
    }

    private function periodicidadTipoDatos($periodicidad)
    {
        $mapa = array(
            'MENSUAL' => 1,
            'BIMENSUAL' => 2,
            'TRIMESTRAL' => 3,
            'SEMESTRAL' => 0,
            'ANUAL' => 0,
            'NUEVO' => 0,
        );

        return isset($mapa[$periodicidad]) ? $mapa[$periodicidad] : 1;
    }

    private function periodicidadEtiqueta($periodicidad)
    {
        $etiquetas = array(
            'MENSUAL' => 'MENSUAL',
            'BIMENSUAL' => 'BIMENSUAL',
            'TRIMESTRAL' => 'TRIMESTRAL',
            'SEMESTRAL' => 'SEMESTRAL',
            'ANUAL' => 'ANUAL',
            'NUEVO' => 'NUEVO',
        );

        return isset($etiquetas[$periodicidad]) ? $etiquetas[$periodicidad] : 'MENSUAL';
    }

    private function analisisPermitidosPorPeriodicidad($periodicidad)
    {
        $mensual = array('hemograma', 'urea_pre', 'urea_post', 'tgo', 'tgp', 'formula_rel', 'formula_abs');
        $bimensual = array('calcio', 'fosforo', 'albumina', 'hbsag', 'anti_hbs', 'anti_hbc', 'hcv');
        $trimestral = array('crea_pre', 'crea_post', 'proteinas_totales', 'fal', 'ferritina', 'hierro', 'transferrina', 'sat_transferrina', 'parathormona', 'pcr');
        $semestralExtra = array('colesterol', 'trigliceridos', 'hdl', 'ldl');
        $anualExtra = array('vih', 'vdrl', 'vitamina_b12', 'ac_folico', 'ac_urico');

        switch ($periodicidad) {
            case 'BIMENSUAL':
                return array_merge($mensual, $bimensual);
            case 'TRIMESTRAL':
                return array_merge($mensual, $trimestral);
            case 'SEMESTRAL':
                return array_merge($mensual, $bimensual, $trimestral, $semestralExtra);
            case 'ANUAL':
            case 'NUEVO':
                return array_merge($mensual, $bimensual, $trimestral, $semestralExtra, $anualExtra);
            case 'MENSUAL':
            default:
                return $mensual;
        }
    }

    private function analisisPermitido($clave, $permitidos)
    {
        return in_array($clave, $permitidos);
    }

    private function valorLab($value, $decimals = null)
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($decimals !== null && is_numeric($value)) {
            return number_format((float) $value, $decimals, '.', '');
        }

        return $value;
    }

    private function valorLabSerologia($value, $decimals = 2)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $valor = trim($value);
        if ($valor === '') {
            return '';
        }

        if (is_numeric(str_replace(',', '.', $valor))) {
            return number_format((float) str_replace(',', '.', $valor), $decimals, '.', '');
        }

        return $valor;
    }

    private function mapaAnalisisClaves()
    {
        return array(
            'Recuento de Leucocitos|10^9/L' => 'leucocitos',
            'Recuento de Hematies|10^12/L' => 'hematies',
            'Recuento de Plaquetas|10^9/L' => 'plaquetas',
            'Hemoglobina|g/dL' => 'hemoglobina',
            'Hematocrito|%' => 'hematocrito',
            'VCM|fL' => 'vcm',
            'HCM|pg' => 'hcm',
            'CCMH|g/dL' => 'ccmh',
            'RDW - Indice de Anisocitosis (%)|%' => 'rdw',
            'RDW - Indice de Anisocitosis (SD)|fL' => 'rdw_sd',
            'VPM|fL' => 'vpm',
            'Abastonados|%' => 'abastonados_rel',
            'Segmentados|%' => 'segmentados_rel',
            'Eosinofilos|%' => 'eosinofilos_rel',
            'Basofilos|%' => 'basofilos_rel',
            'Monocitos|%' => 'monocitos_rel',
            'Linfocitos|%' => 'linfocitos_rel',
            'Abastonados|10^9/L' => 'abastonados_abs',
            'Segmentados|10^9/L' => 'segmentados_abs',
            'Eosinofilos|10^9/L' => 'eosinofilos_abs',
            'Basofilos|10^9/L' => 'basofilos_abs',
            'Monocitos|10^9/L' => 'monocitos_abs',
            'Linfocitos|10^9/L' => 'linfocitos_abs',
            'Urea Pre|mg/dL' => 'urea_pre',
            'Urea Post|mg/dL' => 'urea_post',
            'Creatinina Pre|mg/dL' => 'creatinina_pre',
            'Creatinina Post|mg/dL' => 'creatinina_post',
            'TGO|U/L' => 'tgo',
            'TGP|U/L' => 'tgp',
            'Proteinas Totales|g/dL' => 'proteinas_totales',
            'Albumina|g/dL' => 'albumina',
            'Fosfatasa alcalina|U/L' => 'fal',
            'Ferritina|ng/mL' => 'ferritina',
            'Hierro|ug/dL' => 'hierro',
            'Transferrina|ug/dL' => 'transferrina',
            'Saturacion de Transferrina|%' => 'sat_transferrina',
            'Parathormona|pg/mL' => 'parathormona',
            'Proteina C Reactiva|mg/L' => 'pcr',
            'Colesterol|mg/dL' => 'colesterol',
            'Trigliceridos|mg/dL' => 'trigliceridos',
            'HDL Colesterol|mg/dL' => 'hdl',
            'LDL Colesterol|mg/dL' => 'ldl',
            'Calcio|mg/dL' => 'calcio',
            'Fosforo|mg/dL' => 'fosforo',
            'HBsAg (Antigeno de superficie)|COI' => 'hbsag',
            'Anti HCV (Hepatitis C anticuerpos)|COI' => 'hcv',
            'Anti HBc Core Total|COI' => 'anti_hbc',
            'Anti HBsAg|mIU/mL' => 'anti_hbs',
            'Acido urico|mg/dL' => 'ac_urico',
            'Acido folico|ng/mL' => 'ac_folico',
            'Vitamina B12|pg/mL' => 'vitamina_b12',
            'VIH|COI' => 'vih',
            'VDRL|DILS' => 'vdrl',
        );
    }

    private function cargarRangosReferenciales()
    {
        if ($this->rangosConfig !== null) {
            return $this->rangosConfig;
        }
        $this->rangosConfig = array();
        try {
            $rangos = LaboratorioRangoReferencial::all();
            foreach ($rangos as $r) {
                $this->rangosConfig[$r->clave] = $r;
            }
        } catch (\Exception $e) {
        }
        return $this->rangosConfig;
    }

    private function filaLab($nombre, $resultado, $unidad, $referencia)
    {
        if ($resultado === null || $resultado === '') {
            return '';
        }

        $rangos = $this->cargarRangosReferenciales();
        $mapa = $this->mapaAnalisisClaves();
        $clave = $nombre . '|' . $unidad;
        if (isset($mapa[$clave]) && isset($rangos[$mapa[$clave]])) {
            $r = $rangos[$mapa[$clave]];
            if ($r->nombre !== null && $r->nombre !== '') $nombre = $r->nombre;
            if ($r->unidad !== null && $r->unidad !== '') $unidad = $r->unidad;
            if ($r->rango_referencial !== null) $referencia = $r->rango_referencial;
        }

        return '<tr>
            <td width="38%">' . htmlentities($nombre) . '</td>
            <td width="15%" align="center">' . htmlentities($resultado) . '</td>
            <td width="12%" align="center">' . htmlentities($unidad) . '</td>
            <td width="35%">' . htmlentities($referencia) . '</td>
        </tr>';
    }

    private function seccionLab($titulo)
    {
        return '<tr><td colspan="4" style="border-top:1px solid #000;border-bottom:1px solid #000;"><b>' . htmlentities($titulo) . '</b></td></tr>';
    }

    private function tipoImagenPdf($ruta)
    {
        $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

        if ($ext === 'png') {
            return 'PNG';
        }
        if ($ext === 'gif') {
            return 'GIF';
        }

        return 'JPEG';
    }

    private function altoCabeceraPdfLaboratorio()
    {
        return 36;
    }

    private function altoFooterPdfLaboratorio()
    {
        $footer = base_path('nefrocix/excel/PHPExcel/Shared/PDF/images/footer.png');
        $anchoPagina = 210;

        if (file_exists($footer)) {
            $size = @getimagesize($footer);
            if ($size !== false && $size[0] > 0) {
                return ($size[1] / $size[0]) * $anchoPagina;
            }
        }

        return 18;
    }

    private function dibujarFooterPdfLaboratorio($instancia)
    {
        $footer = base_path('nefrocix/excel/PHPExcel/Shared/PDF/images/footer.png');

        if (!file_exists($footer)) {
            return;
        }

        $anchoPagina = 210;
        $altoFooter = $this->altoFooterPdfLaboratorio();
        $y = $instancia->getPageHeight() - $altoFooter;

        $instancia->Image($footer, 0, $y, $anchoPagina, $altoFooter, 'PNG');
    }

    private function dibujarFranjaAzulCabecera($instancia)
    {
        $anchoPagina = 210;
        $ySuperior = 4;
        $yLados = 11;
        $yCentro = 5;
        $segmentos = 30;

        $instancia->SetFillColor(0, 113, 188);
        $instancia->Rect(0, 0, $anchoPagina, $ySuperior, 'F');

        $puntos = array(0, $ySuperior, $anchoPagina, $ySuperior);
        for ($i = $segmentos; $i >= 0; $i--) {
            $t = $i / $segmentos;
            $x = $t * $anchoPagina;
            $y = $yLados - ($yLados - $yCentro) * sin(M_PI * $t);
            $puntos[] = $x;
            $puntos[] = $y;
        }

        $instancia->Polygon($puntos, 'F');
    }

    private function dibujarCabeceraPdfLaboratorio($instancia)
    {
        $this->dibujarFranjaAzulCabecera($instancia);

        $imgDir = base_path('nefrocix/excel/PHPExcel/Shared/PDF/images');
        $logoIzq = $imgDir . '/logoizquierdo.jpeg';
        $logoDer = $imgDir . '/logoderecho.png';
        if (!file_exists($logoDer)) {
            $logoDer = $imgDir . '/logoderecho.jpeg';
        }
        $margenIzq = 10;
        $anchoUtil = 190;
        $yLogos = 12;
        $altoLogos = 18;
        $altoLogoDer = 22;

        if (file_exists($logoIzq)) {
            $instancia->Image($logoIzq, $margenIzq, $yLogos, 0, $altoLogos, $this->tipoImagenPdf($logoIzq));
        }
        if (file_exists($logoDer)) {
            $anchoLogoDer = $altoLogoDer;
            $size = @getimagesize($logoDer);
            if ($size !== false && $size[1] > 0) {
                $anchoLogoDer = ($size[0] / $size[1]) * $altoLogoDer;
            }
            $xDer = $margenIzq + $anchoUtil - $anchoLogoDer;
            $instancia->Image($logoDer, $xDer, $yLogos, 0, $altoLogoDer, $this->tipoImagenPdf($logoDer));
        }

        if ($instancia->getPage() === 1) {
            $altoCabecera = max($altoLogos, file_exists($logoDer) ? $altoLogoDer : 0);
            $instancia->SetFont('helvetica', 'B', 13);
            $instancia->SetXY($margenIzq, $yLogos + ($altoCabecera / 2) - 3);
            $instancia->Cell($anchoUtil, 7, 'LABORATORIO CLÍNICO', 0, 0, 'C');
            $tituloY = $instancia->GetY() + 7;
            $instancia->Line(78, $tituloY, 132, $tituloY);
        }
    }

    private function directorioFirmaLaboratorio()
    {
        $dir = storage_path('firmas');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private function rutaFirmaLaboratorioPdf()
    {
        $dir = $this->directorioFirmaLaboratorio();
        $extensiones = array('png', 'jpg', 'jpeg', 'gif');
        foreach ($extensiones as $ext) {
            $ruta = $dir . '/firma_laboratorio.' . $ext;
            if (file_exists($ruta)) {
                return $ruta;
            }
        }
        return null;
    }

    private function eliminarArchivosFirmaLaboratorio()
    {
        $dir = $this->directorioFirmaLaboratorio();
        $archivos = glob($dir . '/firma_laboratorio.*');
        if (is_array($archivos)) {
            foreach ($archivos as $archivo) {
                if (is_file($archivo)) {
                    @unlink($archivo);
                }
            }
        }
    }

    private function altoFirmaLaboratorioPdf($ruta, $anchoFirma = 65)
    {
        $altoFirma = 25;
        $size = @getimagesize($ruta);
        if ($size !== false && $size[0] > 0) {
            $altoFirma = ($size[1] / $size[0]) * $anchoFirma;
        }
        return $altoFirma;
    }

    private function dibujarFirmaLaboratorioPdfDespuesContenido()
    {
        $firma = $this->rutaFirmaLaboratorioPdf();
        if ($firma === null) {
            return;
        }
        $anchoFirma = 65;
        $altoFirma = $this->altoFirmaLaboratorioPdf($firma, $anchoFirma);
        $altoFooter = $this->altoFooterPdfLaboratorio();
        $margenSuperior = 8;
        $margenInferiorFooter = $altoFooter + 5;
        $y = TCPDF::GetY() + $margenSuperior;
        $maxY = TCPDF::getPageHeight() - $margenInferiorFooter - $altoFirma;
        if ($y + $altoFirma > $maxY) {
            TCPDF::AddPage();
            $y = TCPDF::GetY() + $margenSuperior;
        }
        $x = 210 - $anchoFirma - 10;
        TCPDF::Image($firma, $x, $y, $anchoFirma, $altoFirma, $this->tipoImagenPdf($firma));
    }

    public function configuracionFirmaLaboratorio(Request $request)
    {
        $entidad = 'FirmaLaboratorio';
        $tieneFirma = $this->rutaFirmaLaboratorioPdf() !== null;
        return view($this->folderview . '.configuracionfirma')->with(compact('entidad', 'tieneFirma'));
    }

    public function guardarFirmaLaboratorio(Request $request)
    {
        if (!$request->hasFile('archivo')) {
            $error = array('archivo' => array('Debe seleccionar una imagen.'));
            return json_encode($error);
        }
        $archivo = $request->file('archivo');
        if (!$archivo->isValid()) {
            $error = array('archivo' => array('No se pudo cargar el archivo.'));
            return json_encode($error);
        }
        $ext = strtolower($archivo->getClientOriginalExtension());
        if (!in_array($ext, array('png', 'jpg', 'jpeg', 'gif'))) {
            $error = array('archivo' => array('Formato no permitido. Use PNG, JPG o GIF.'));
            return json_encode($error);
        }
        $this->eliminarArchivosFirmaLaboratorio();
        $nombre = 'firma_laboratorio.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        $archivo->move($this->directorioFirmaLaboratorio(), $nombre);
        return 'OK';
    }

    public function eliminarFirmaLaboratorio(Request $request)
    {
        $this->eliminarArchivosFirmaLaboratorio();
        return 'OK';
    }

    public function firmaLaboratorioImagen()
    {
        $ruta = $this->rutaFirmaLaboratorioPdf();
        if ($ruta === null) {
            abort(404);
        }
        $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
        $mime = 'image/png';
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $mime = 'image/jpeg';
        } elseif ($ext === 'gif') {
            $mime = 'image/gif';
        }
        return response()->make(file_get_contents($ruta), 200, array('Content-Type' => $mime));
    }

    public function configuracionRangosReferenciales(Request $request)
    {
        $entidad = 'RangosReferenciales';
        $secciones = array();
        try {
            $rangos = LaboratorioRangoReferencial::orderBy('orden', 'asc')->get();
            foreach ($rangos as $r) {
                $secciones[$r->seccion][] = $r;
            }
        } catch (\Exception $e) {
        }
        return view($this->folderview . '.configuracionrangos')->with(compact('entidad', 'secciones'));
    }

    public function guardarRangosReferenciales(Request $request)
    {
        $nombres = $request->input('nombre', array());
        $unidades = $request->input('unidad', array());
        $rangos = $request->input('rango', array());

        foreach ($nombres as $clave => $nombre) {
            $r = LaboratorioRangoReferencial::where('clave', '=', $clave)->first();
            if ($r !== null) {
                $r->nombre = $nombre;
                $r->unidad = isset($unidades[$clave]) ? $unidades[$clave] : $r->unidad;
                $r->rango_referencial = isset($rangos[$clave]) ? $rangos[$clave] : $r->rango_referencial;
                $r->save();
            }
        }

        return 'OK';
    }

    private function absolutoLeucocitario($leucocitos, $porcentaje)
    {
        if (!is_numeric($leucocitos) || !is_numeric($porcentaje)) {
            return '';
        }

        return number_format((((float) $porcentaje) / 100) * ((float) $leucocitos), 2, '.', '');
    }

    private function seccionLabSiHayFilas($titulo, $filas)
    {
        if (trim($filas) === '') {
            return '';
        }

        return $this->seccionLab($titulo) . $filas;
    }

    private function generarFilasHemograma($hc, $permitidos)
    {
        if (!$this->analisisPermitido('hemograma', $permitidos)) {
            return '';
        }

        $rows = '';
        $hemogramaFields = array(
            $hc->txtLeucocitos, $hc->txtHematies, $hc->txtPlaquetas, $hc->txtDos, $hc->txtHem,
            $hc->txtVcm, $hc->txtHcm, $hc->txtCcmh, $hc->txtRdw, $hc->txtRdwSd, $hc->txtVpm,
            $hc->txtAbastonados, $hc->txtSegmentados, $hc->txtEosinofilos, $hc->txtBasofilos,
            $hc->txtMonocitos, $hc->txtLinfocitos
        );

        if (count(array_filter($hemogramaFields, function ($value) {
            return $value !== null && $value !== '';
        })) === 0) {
            return '';
        }

        $rows .= $this->seccionLabSiHayFilas('HEMOGRAMA COMPLETO',
            $this->filaLab('Recuento de Leucocitos', $this->valorLab($hc->txtLeucocitos, 2), '10^9/L', '4.00 - 10.00') .
            $this->filaLab('Recuento de Hematies', $this->valorLab($hc->txtHematies, 2), '10^12/L', '3.50 - 5.50') .
            $this->filaLab('Recuento de Plaquetas', $this->valorLab($hc->txtPlaquetas, 0), '10^9/L', '140 - 440') .
            $this->filaLab('Hemoglobina', $this->valorLab($hc->txtDos, 1), 'g/dL', 'M: (12.5 - 15.1), H: (12.5 - 15.8)') .
            $this->filaLab('Hematocrito', $this->valorLab($hc->txtHem, 1), '%', 'M: (36 - 47), H: (38 - 48)')
        );

        $rows .= $this->seccionLabSiHayFilas('CONSTANTES CORPUSCULARES',
            $this->filaLab('VCM', $this->valorLab($hc->txtVcm, 1), 'fL', '80 - 100') .
            $this->filaLab('HCM', $this->valorLab($hc->txtHcm, 1), 'pg', '27 - 34') .
            $this->filaLab('CCMH', $this->valorLab($hc->txtCcmh, 1), 'g/dL', '32 - 36') .
            $this->filaLab('RDW - Indice de Anisocitosis (%)', $this->valorLab($hc->txtRdw, 1), '%', '11 - 16') .
            $this->filaLab('RDW - Indice de Anisocitosis (SD)', $this->valorLab($hc->txtRdwSd, 1), 'fL', '35 - 56') .
            $this->filaLab('VPM', $this->valorLab($hc->txtVpm, 1), 'fL', '7 - 11')
        );

        if ($this->analisisPermitido('formula_rel', $permitidos)) {
            $rows .= $this->seccionLabSiHayFilas('FORMULA LEUCOCITARIA (REL)',
                $this->filaLab('Abastonados', $this->valorLab($hc->txtAbastonados, 0), '%', '0.0 - 5.0') .
                $this->filaLab('Segmentados', $this->valorLab($hc->txtSegmentados, 0), '%', '45.0 - 74.0') .
                $this->filaLab('Eosinofilos', $this->valorLab($hc->txtEosinofilos, 0), '%', '0.0 - 4.4') .
                $this->filaLab('Basofilos', $this->valorLab($hc->txtBasofilos, 0), '%', '1.0 - 1.2') .
                $this->filaLab('Monocitos', $this->valorLab($hc->txtMonocitos, 0), '%', '0.7 - 7.5') .
                $this->filaLab('Linfocitos', $this->valorLab($hc->txtLinfocitos, 0), '%', '22.3 - 49.9')
            );
        }

        if ($this->analisisPermitido('formula_abs', $permitidos)) {
            $rows .= $this->seccionLabSiHayFilas('FORMULA LEUCOCITARIA (ABS)',
                $this->filaLab('Abastonados', $this->absolutoLeucocitario($hc->txtLeucocitos, $hc->txtAbastonados), '10^9/L', '0.15 - 0.40') .
                $this->filaLab('Segmentados', $this->absolutoLeucocitario($hc->txtLeucocitos, $hc->txtSegmentados), '10^9/L', '2.00 - 7.80') .
                $this->filaLab('Eosinofilos', $this->absolutoLeucocitario($hc->txtLeucocitos, $hc->txtEosinofilos), '10^9/L', '0.02 - 0.35') .
                $this->filaLab('Basofilos', $this->absolutoLeucocitario($hc->txtLeucocitos, $hc->txtBasofilos), '10^9/L', '0.01 - 0.05') .
                $this->filaLab('Monocitos', $this->absolutoLeucocitario($hc->txtLeucocitos, $hc->txtMonocitos), '10^9/L', '0.1 - 0.5') .
                $this->filaLab('Linfocitos', $this->absolutoLeucocitario($hc->txtLeucocitos, $hc->txtLinfocitos), '10^9/L', '0.80 - 4.00')
            );
        }

        return $rows;
    }

    private function generarFilasPdf($hc, $permitidos)
    {
        $rows = $this->generarFilasHemograma($hc, $permitidos);
        $filas = '';

        if ($this->analisisPermitido('urea_pre', $permitidos)) {
            $filas .= $this->filaLab('Urea Pre', $this->valorLab($hc->txtUre, 2), 'mg/dL', '10 - 50');
        }
        if ($this->analisisPermitido('urea_post', $permitidos)) {
            $filas .= $this->filaLab('Urea Post', $this->valorLab($hc->txtUre2, 2), 'mg/dL', '10 - 50');
        }
        if ($this->analisisPermitido('crea_pre', $permitidos)) {
            $filas .= $this->filaLab('Creatinina Pre', $this->valorLab($hc->txtCre, 2), 'mg/dL', 'M: (0.5-0.9), H: (0.6-1.1)');
        }
        if ($this->analisisPermitido('crea_post', $permitidos)) {
            $filas .= $this->filaLab('Creatinina Post', $this->valorLab($hc->txtCre2, 2), 'mg/dL', 'M: (0.5-0.9), H: (0.6-1.1)');
        }
        if ($this->analisisPermitido('tgo', $permitidos)) {
            $filas .= $this->filaLab('TGO', $this->valorLab($hc->txtTgo, 2), 'U/L', 'M: Hasta 31, H: Hasta 37');
        }
        if ($this->analisisPermitido('tgp', $permitidos)) {
            $filas .= $this->filaLab('TGP', $this->valorLab($hc->txtTgp, 2), 'U/L', 'M: Hasta 34, H: Hasta 45');
        }
        if ($this->analisisPermitido('proteinas_totales', $permitidos)) {
            $filas .= $this->filaLab('Proteinas Totales', $this->valorLab($hc->txtPro, 2), 'g/dL', '6.4 - 8.3');
        }
        if ($this->analisisPermitido('albumina', $permitidos)) {
            $filas .= $this->filaLab('Albumina', $this->valorLab($hc->txtAlbu, 2), 'g/dL', '3.5 - 5.2');
        }
        if ($this->analisisPermitido('fal', $permitidos)) {
            $filas .= $this->filaLab('Fosfatasa alcalina', $this->valorLab($hc->txtFos2, 2), 'U/L', 'M: (42-141), H: (53-128)');
        }
        if ($this->analisisPermitido('ferritina', $permitidos)) {
            $filas .= $this->filaLab('Ferritina', $this->valorLab($hc->txtFer, 2), 'ng/mL', 'M: (10-124), H: (16-220)');
        }
        if ($this->analisisPermitido('hierro', $permitidos)) {
            $filas .= $this->filaLab('Hierro', $this->valorLab($hc->txtHie, 2), 'ug/dL', 'M: (50-170), H: (65-175)');
        }
        if ($this->analisisPermitido('transferrina', $permitidos)) {
            $filas .= $this->filaLab('Transferrina', $this->valorLab($hc->txtTransfe, 2), 'ug/dL', '250.00 - 400.00');
        }
        if ($this->analisisPermitido('sat_transferrina', $permitidos)) {
            $filas .= $this->filaLab('Saturacion de Transferrina', $this->valorLab($hc->txtSat, 2), '%', '20.0 - 55.0');
        }
        if ($this->analisisPermitido('parathormona', $permitidos)) {
            $filas .= $this->filaLab('Parathormona', $this->valorLab($hc->txtPar, 2), 'pg/mL', '15.00 - 65.00');
        }
        if ($this->analisisPermitido('pcr', $permitidos)) {
            $filas .= $this->filaLab('Proteina C Reactiva', $this->valorLab($hc->txtPcr, 2), 'mg/L', 'Positivo > 5.0, Negativo < 5.0');
        }
        if ($this->analisisPermitido('colesterol', $permitidos)) {
            $filas .= $this->filaLab('Colesterol', $this->valorLab($hc->txtColesterol, 2), 'mg/dL', 'Optimo: <200, Moderado: 200-239, Alto: >239');
        }
        if ($this->analisisPermitido('trigliceridos', $permitidos)) {
            $filas .= $this->filaLab('Trigliceridos', $this->valorLab($hc->txtTrigliceridos, 2), 'mg/dL', 'Normal: <150, Alto: 200-499, Muy alto: >500');
        }
        if ($this->analisisPermitido('hdl', $permitidos)) {
            $filas .= $this->filaLab('HDL Colesterol', $this->valorLab($hc->txtHdl, 2), 'mg/dL', 'Bajo: < 40, Alto: >= 60');
        }
        if ($this->analisisPermitido('ldl', $permitidos)) {
            $filas .= $this->filaLab('LDL Colesterol', $this->valorLab($hc->txtLdl, 2), 'mg/dL', 'Optimo: <100, Moderado: 130-159, Alto: 160-189, Muy Alto: >189');
        }
        if ($this->analisisPermitido('calcio', $permitidos)) {
            $filas .= $this->filaLab('Calcio', $this->valorLab($hc->txtCal, 2), 'mg/dL', '8.5 - 10.5');
        }
        if ($this->analisisPermitido('fosforo', $permitidos)) {
            $filas .= $this->filaLab('Fosforo', $this->valorLab($hc->txtFos, 2), 'mg/dL', '2.5 - 5.5');
        }
        if ($this->analisisPermitido('hbsag', $permitidos)) {
            $filas .= $this->filaLab('HBsAg (Antigeno de superficie)', $this->valorLabSerologia($hc->txtDet), 'COI', '(No reactivo < .09) (Indeterminado 0.9-1.1) (Reactivo > 1.0)');
        }
        if ($this->analisisPermitido('hcv', $permitidos)) {
            $filas .= $this->filaLab('Anti HCV (Hepatitis C anticuerpos)', $this->valorLabSerologia($hc->txtDet4), 'COI', '(No reactivo < .09) (Indeterminado 0.9-1.1) (Reactivo > 1.0)');
        }
        if ($this->analisisPermitido('anti_hbc', $permitidos)) {
            $filas .= $this->filaLab('Anti HBc Core Total', $this->valorLabSerologia($hc->txtDet3), 'COI', 'Reactivo <= 1.0, No Reactivo > 1.0');
        }
        if ($this->analisisPermitido('anti_hbs', $permitidos)) {
            $filas .= $this->filaLab('Anti HBsAg', $this->valorLabSerologia($hc->txtDet2), 'mIU/mL', 'Estado inmune: > 10');
        }
        if ($this->analisisPermitido('ac_urico', $permitidos)) {
            $filas .= $this->filaLab('Acido urico', $this->valorLab($hc->txtAcidoUrico, 2), 'mg/dL', 'Mujeres: 2.6 - 6.0, Hombres: 3.4 - 7.2');
        }
        if ($this->analisisPermitido('ac_folico', $permitidos)) {
            $filas .= $this->filaLab('Acido folico', $this->valorLab($hc->txtAcidoFolico, 2), 'ng/mL', 'Mujeres: 4.8 - 37.3, Hombres: 4.5 - 32.2');
        }
        if ($this->analisisPermitido('vitamina_b12', $permitidos)) {
            $filas .= $this->filaLab('Vitamina B12', $this->valorLab($hc->txtVitaminaB12, 2), 'pg/mL', '211-946');
        }
        if ($this->analisisPermitido('vih', $permitidos)) {
            $filas .= $this->filaLab('VIH', $this->valorLabSerologia($hc->txtEli), 'COI', 'Negativo: 0 - 0.9, Indeterminado 0.9 - 1.1, Positivo: > 1.1');
        }
        if ($this->analisisPermitido('vdrl', $permitidos)) {
            $filas .= $this->filaLab('VDRL', $this->valorLabSerologia($hc->txtPru), 'DILS', 'Reactivo >= 2');
        }

        $rows .= $this->seccionLabSiHayFilas('OTROS ANALISIS', $filas);

        return $rows;
    }

    private function generarFilasAdicionalesPdf($hc)
    {
        $rows = '';
        $adicionales = ConsultaNefrologicaAnalisisAdicional::where('consultanefrologica_id', '=', $hc->id)->orderBy('orden', 'asc')->get();

        if (count($adicionales) === 0) {
            return '';
        }

        $filas = '';
        foreach ($adicionales as $adicional) {
            $filas .= $this->filaLab($adicional->nombre, $this->valorLab($adicional->resultado), $adicional->unidad, $adicional->rango_referencial);
        }

        return $this->seccionLabSiHayFilas('ANALISIS ADICIONALES', $filas);
    }

    public function pdfLaboratorio(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $hc = ConsultaNefrologica::find($request->input('id'));

        if ($hc === null) {
            echo 'AUN NO SE FINALIZA EL PROCESO, REGRESE...';
            return;
        }

        $historia = Historia::where('person_id', '=', $hc->persona_id)->first();
        if ($historia === null) {
            echo 'NO SE ENCONTRO LA HISTORIA DEL PACIENTE';
            return;
        }

        $edad = '-';
        if ($historia->persona->fechanacimiento !== null && $historia->persona->fechanacimiento !== '') {
            $edad = (new DateTime())->diff(new DateTime($historia->persona->fechanacimiento))->y . ' anos';
        }

        $periodicidad = $this->obtenerPeriodicidad($hc);
        $tipoExamen = $this->periodicidadEtiqueta($periodicidad);
        $permitidos = $this->analisisPermitidosPorPeriodicidad($periodicidad);
        $rows = $this->generarFilasPdf($hc, $permitidos);
        $rows .= $this->generarFilasAdicionalesPdf($hc);

        if ($rows === '') {
            $rows = '<tr><td colspan="4" align="center">No hay resultados registrados.</td></tr>';
        }

        $html = '<style>
            table { font-size: 8px; }
            .box td { border: 1px solid #000; }
            .result th { border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; }
            .result td { border-bottom: 1px solid #000; }
        </style>
        <table class="box" cellpadding="3">
            <tr>
                <td width="13%">Paciente:</td>
                <td width="42%">' . htmlentities($historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres) . '</td>
                <td width="15%">Sexo:</td>
                <td width="30%">' . htmlentities($historia->persona->sexo == 'M' ? 'MASCULINO' : 'FEMENINO') . '</td>
            </tr>
            <tr>
                <td>Edad:</td>
                <td>' . htmlentities($edad) . '</td>
                <td>Fecha:</td>
                <td>' . date('d/m/Y', strtotime($hc->fecha)) . '</td>
            </tr>
            <tr>
                <td>DNI:</td>
                <td>' . htmlentities($historia->persona->dni) . '</td>
                <td>Tipo de examen:</td>
                <td>' . htmlentities($tipoExamen) . '</td>
            </tr>
        </table>
        <br>
        <table class="result" cellpadding="2">
            <tr>
                <th width="38%">EXAMEN</th>
                <th width="15%" align="center">RESULTADO</th>
                <th width="12%" align="center">UNIDAD</th>
                <th width="35%">RANGO REFERENCIAL</th>
            </tr>
            ' . $rows . '
        </table>';

        $altoCabecera = $this->altoCabeceraPdfLaboratorio();
        $altoFooter = $this->altoFooterPdfLaboratorio();
        $controlador = $this;

        app('tcpdf')->reset();
        app('tcpdf')->setHeaderCallback(function ($instancia) use ($controlador) {
            $controlador->dibujarCabeceraPdfLaboratorio($instancia);
        });
        app('tcpdf')->setFooterCallback(function ($instancia) use ($controlador) {
            $controlador->dibujarFooterPdfLaboratorio($instancia);
        });

        $pdf = new TCPDF();
        $pdf::setPrintHeader(true);
        $pdf::setPrintFooter(true);
        $pdf::setHeaderMargin(0);
        $pdf::setFooterMargin(0);
        $pdf::SetMargins(10, $altoCabecera, 10);
        $pdf::SetAutoPageBreak(true, $altoFooter + 2);
        $pdf::SetTitle('LaboratorioClinico');
        $pdf::AddPage();
        $pdf::SetFont('helvetica', '', 8);
        $pdf::writeHTML($html, true, false, true, false, '');
        $this->dibujarFirmaLaboratorioPdfDespuesContenido();
        $pdf::Output('LaboratorioClinico.pdf');
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
