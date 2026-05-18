<?php

namespace App\Http\Controllers;

use App\Cie;
use App\Cita;
use App\Configuracion;
use App\ConsultaNefrologica;
use App\ConsultaNutricion;
use App\ConsultaSaludMental;
use App\ConsultaServicioSocial;
use App\Convenio;
use App\Departamento;
use App\Detallehistoriacie;
use App\Distrito;
use App\Etiologia;
use App\Examenhistoriaclinica;
use App\Historia;
use App\HistoriaClinica;
use App\Http\Controllers\Controller;
use App\Librerias\Libreria;
use App\Person;
use App\Plan;
use App\Producto;
use App\Provincia;
use App\Rolpersona;
use App\Seguimiento;
use App\Turno;
use DateTime;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Validator;

ini_set('memory_limit', '512M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB

class HistoriaController extends Controller
{
    protected $folderview      = 'app.historia';
    protected $tituloAdmin     = 'Historia';
    protected $tituloRegistrar = 'Registrar historia';
    protected $tituloModificar = 'Modificar historia';
    protected $tituloEliminar  = 'Eliminar historia';
    protected $rutas           = array('create' => 'historia.create',
        'edit'                 => 'historia.edit',
        'delete'               => 'historia.eliminar',
        'search'               => 'historia.buscar',
        'buscaProv'            => 'historia.buscaProv',
        'buscaDist'            => 'historia.buscaDist',
        'index'                => 'historia.index',
        'fallecido'            => 'historia.fallecido',
        'createhcinicial'      => 'historia.createhcinicial',
        'createhenfermeria'    => 'historia.createhenfermeria',
        'observaciones'        => 'historia.observaciones',
        'guardarobservaciones' => 'historia.guardarobservaciones',
        'activar'              => 'historia.activar',
    );

    // Anteriores

    protected $examenesGeneral_old = array(
        '86703' => 'ELISA o prueba rápida para HIV-1 y HIV-2',
        '87340' => 'Detección de antígeno de superficie de virus de Hepatitis B (HBsAg) por ELISA',
        '86706' => 'Detección de anticuerpos para antígeno de superficie Hepatitis B (HBs-Ag)',
        '86704' => 'Detección de anticuerpos totales para núcleo de virus de Hepatitis B (Total Anti-Hbcore)',
        '86803' => 'Determinación de anticuerpos para Hepatitis C',
        '86592' => 'Prueba de sífilis cualitativa (VDRL, RPR)',
        '84520' => 'Úrea',        
        '82565' => 'Creatinina en sangre',        
        '85014' => 'Hematocrito',
        '80051' => 'Electrolitos séricos',
        '85018' => 'Dosaje de hemoglobina',        
        '84100' => 'Fósforo en sangre',        
        '82310' => 'Calcio sérico',
        '84450' => 'TGO transaminasa glutámico oxalacética',
        '84460' => 'TGP transaminasa glutámico pirúvica',
        '84075' => 'Fosfatasa Alcalina',
        '83970' => 'Paratohormona (PTH)',
        '83540' => 'Hierro sérico',
        '82728' => 'Ferritina',
        '84466' => 'Saturación de transferrina',        
        '84165' => 'Proteínas; fraccionamiento y determinación cuantitativa por electroforesis',
        '82040' => 'Dosaje de Albúmina; suero, plasma o sangre total',
    );

    // Nuevos

    protected $examenesGeneral_new = array(
        '84520' => 'Nitrógeno ureico; cuantitativo',
        '82565' => 'Dosaje de Creatinina en sangre',
        '85014' => 'Hematocrito',
        '85018' => 'Hemoglobina',
        '80051' => 'Perfil de electrolito',
        '84100' => 'Dosaje de Fósforo inorgánico (fosfato)',
        '82310' => 'Dosaje de Calcio; total',
        '84075' => 'Dosaje de Fosfatasa, alcalina',
        '84450' => 'Aspartato amino transferasa (AST) (SGOT)',
        '84460' => 'Transferasa; amino alanina (ALT) (SGPT)',
        '86703' => 'Anticuerpo; HIV-1 y HIV-2, análisis único',
        '86592' => 'Prueba de sífilis; anticuerpo no treponémico; cualitativo (p. ej. VDRL, RPR, ART)',
        '83970' => 'Dosaje de Paratohormona (hormona paratiroidea)',
        '87340' => 'Detección de antígenos de agentes infeccioso mediante técnica de inmunoensayo enzimático, cualitativo o semicuantitativo, método de varios pasos; hepatitis B antpigeno de superficie (HBsAg)',
        '86706' => 'Anticuerpo contra el antígeno de superficie de la hepatitis B (HBsAb)',
        '86704' => 'Anticuerpo contra el antígeno de la nucleocápside de la hepatitis B (HBcAb); total',
        '86803' => 'Anticuerpo contra la hepatitis C',
        '83540' => 'Dosaje de Hierro',
        '82728' => 'Dosaje de Ferritina',
        '84466' => 'Transferrina',
        '84165' => 'Proteínas; fraccionamiento y determinación cuantitativa por electroforesis',
        '82040' => 'Dosaje de Albúmina; suero, plasma o sangre total',
    );


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $sucursal_id = Session::get('sucursal_id');
        $pagina      = $request->input('page');
        $filas       = $request->input('filas');
        $entidad     = 'Historia';
        $nombre      = Libreria::getParam($request->input('nombre'), '');
        $estado      = Libreria::getParam($request->input('estado'), '');
        $estado3     = Libreria::getParam($request->input('estado3'), '');
        //$dni              = Libreria::getParam($request->input('dni'));
        $numero = Libreria::getParam($request->input('numero'));
        //$numero2          = Libreria::getParam($request->input('numero2'));
        $tipopaciente = Libreria::getParam($request->input('tipopaciente'));
        $resultado    = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->leftjoin('convenio', 'convenio.id', '=', 'historia.convenio_id')
        //->where('historia.sucursal_id', '=', $sucursal_id)
            ->where(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%' . strtoupper($nombre) . '%')
            ->orderBy(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'));
        //->where('person.dni', 'LIKE', '%'.strtoupper($dni).'%');
        if ($tipopaciente != "") {
            $resultado = $resultado->where('historia.tipopaciente', 'LIKE', '' . strtoupper($tipopaciente) . '');
        }
        if ($numero != "") {
            $resultado = $resultado->where('historia.numero', 'LIKE', '%' . strtoupper($numero) . '%');
        }
        if ($estado != "") {
            if ($estado == "S") {
                $resultado = $resultado->where('historia.estado', '=', 'S');
            } else {
                $resultado = $resultado->where('historia.estado', '!=', 'S');
            }
        }
        if ($estado3 != "") {
            if ($estado3 == "S") {
                $resultado = $resultado->where('historia.estado3', '=', 'S');
            } else {
                $resultado = $resultado->where('historia.estado3', '!=', 'S');
            }
        }
        /*if($numero2!=""){
        $resultado = $resultado->where('historia.numero2', 'LIKE', '%'.strtoupper($numero2).'%');
        }*/
        $resultado   = $resultado->select('historia.*', 'person.nacionalidad')->orderBy('historia.numero', 'ASC');
        $vistamedico = $request->input('vistamedico');
        if ($vistamedico != "SI") {
            $resultado = $resultado->limit(100);
        }
        $lista      = $resultado->get();
        $cabecera   = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[] = array('valor' => 'Historia/DNI/CE', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Nro Historia2', 'numero' => '1');
        $cabecera[] = array('valor' => 'Paciente', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[] = array('valor' => 'Tipo Paciente', 'numero' => '1');
        $cabecera[] = array('valor' => 'Telefono', 'numero' => '1');
        $cabecera[] = array('valor' => 'Fecha Nacimiento', 'numero' => '1');
        $cabecera[] = array('valor' => 'Direccion', 'numero' => '1');
        $cabecera[] = array('valor' => 'Hist. Inicial', 'numero' => '3');
        $cabecera[] = array('valor' => 'Hist. Enfermería', 'numero' => '3');

        if ($vistamedico != "SI") {
            $cabecera[] = array('valor' => 'Operaciones', 'numero' => '4');
        } else {
            $cabecera[] = array('valor' => 'Ver Citas', 'numero' => '1');
        }
        $cabecera[] = array('valor' => 'Obs.', 'numero' => '1');

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
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
            return view($this->folderview . '.list')->with(compact('lista', 'paginacion', 'inicio', 'vistamedico', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'user'));
        }
        return view($this->folderview . '.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Historia';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboTipoPaciente  = array("" => "Todos", "Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");
        return view($this->folderview . '.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboTipoPaciente'));
    }

    public function create(Request $request)
    {
        $listar      = Libreria::getParam($request->input('listar'), 'NO');
        $modo        = $request->input('modo', '');
        $entidad     = 'Historia';
        $historia    = null;
        $cboConvenio = array();
        $cboDepa     = array('' => '---- Elija uno ----');
        $convenios   = Convenio::where(DB::raw('1'), '=', '1')->orderBy('nombre', 'ASC')->get();
        foreach ($convenios as $key => $value) {
            $cboConvenio = $cboConvenio + array($value->id => $value->nombre);
        }
        $departamentos = Departamento::orderBy('nombre', 'ASC')->get();
        foreach ($departamentos as $key => $value) {
            $cboDepa = $cboDepa + array($value->id => $value->nombre);
        }
        $cboEstadoCivil = array("SOLTERO(A)" => "SOLTERO(A)", "CASADO(A)" => "CASADO(A)", "VIUDO(A)" => "VIUDO(A)", "DIVORCIADO(A)" => "DIVORCIADO(A)", "CONVIVIENTE" => "CONVIVIENTE");
        $cboSexo        = array("M" => "Masculino", "F" => "Femenino");
        //$cboCategoria = array("Normal"=>"Normal","Religioso"=>"Religioso","Doctor"=>"Doctor","Familiar Trabajador"=>"Familiar Trabajador","Aldeas Infantiles"=>"Aldeas Infantiles");
        $cboCategoria    = array("Normal" => "Normal", "Religioso" => "Religioso");
        $cboRegimen      = array("Semicontributivo" => "Semicontributivo", "Subsidiado" => "Subsidiado", "Ninguno" => "Ninguno");
        $cboGrupo        = array("AB+" => "AB+", "AB-" => "AB-", "A+" => "A+", "A-" => "A-", "B+" => "B+", "B-" => "B-", "O+" => "O+", "O-" => "O-");
        $cboGrado        = array("Inicial" => "Inicial", "Analfabeto" => "Analfabeto", "Primaria" => "Primaria", "Secundaria" => "Secundaria", "Superior" => "Superior");
        $formData        = array('historia.store');
        $cboTipoPaciente = array("Convenio" => "Convenio", "Particular" => "Particular");
        $cboModo         = array("F" => "Fisico", "V" => "Registro Virtual");
        $cboAccesoV      = array("1" => "FAV", "2" => "Autoinjerto", "3" => "Injerto", "4" => "CVCP", "5" => "CVCT", "6" => "Cperitoneal");

        $turnos = Turno::orderBy('hora', 'ASC')->get();

        $cboTurno = array();
        $i        = 1;
        foreach ($turnos as $turno) {
            $cboTurno = $cboTurno + array($turno->id => 'TURNO ' . $i . ': ' . $turno->hora);
            $i++;
        }

        $sucursal_id = Session::get('sucursal_id');
        $num         = Historia::NumeroSigue($sucursal_id);
        $user        = Auth::user();
        $formData    = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton       = 'Registrar';
        return view($this->folderview . '.mant')->with(compact('historia', 'formData', 'entidad', 'boton', 'listar', 'cboTipoPaciente', 'cboConvenio', 'cboEstadoCivil', 'modo', 'cboSexo', 'cboDepa', 'cboModo', 'num', 'user', 'cboCategoria', 'cboRegimen', 'cboGrupo', 'cboGrado', 'cboAccesoV', 'cboTurno'));
    }

    public function buscaProv($departamento)
    {
        $provincias = Provincia::where('departamento_id', '=', $departamento)->orderBy('nombre', 'ASC')->get();
        $cboProv    = '<option value="">---- Elija uno ----</option>';
        foreach ($provincias as $key => $value) {
            $cboProv = $cboProv . '<option value="' . $value->id . '">' . $value->nombre . '</option>';
        }
        echo $cboProv;
    }

    public function buscaDist($provincia)
    {
        $distritos = Distrito::where('provincia_id', '=', $provincia)->orderBy('nombre', 'ASC')->get();
        $cboDist   = '<option value="">---- Elija uno ----</option>';
        foreach ($distritos as $key => $value) {
            $cboDist = $cboDist . '<option value="' . $value->id . '">' . $value->nombre . '</option>';
        }
        echo $cboDist;
    }

    public function store(Request $request)
    {
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $modo   = $request->input('modo', '');
        $reglas = array(
            'nombres'              => 'required',
            'dni'                  => 'required',
            'dni2'                 => 'required',
            'apellidopaterno'      => 'required',
            'nacionalidad'         => 'required',
            'nacionalidad2'        => 'required',
            'apellidomaterno'      => 'required',
            'telefono'             => 'required',
            'departamento'         => 'required',
            'provincia'            => 'required',
            'distrito'             => 'required',
            'direccion'            => 'required',
            'fechanacimiento'      => 'required',
            'antecedentesclinicos' => 'required',
            'fechainicio'          => 'required',
            'horacita'             => 'required',
        );
        $mensajes = array(
            'apellidopaterno.required'      => 'Debe ingresar un apellido paterno',
            'apellidomaterno.required'      => 'Debe ingresar un apellido materno',
            'nombres.required'              => 'Debe ingresar un nombre',
            'nacionalidad.required'         => 'Debe ingresar una nacionalidad',
            'nacionalidad2.required'        => 'Debe ingresar una nacionalidad',
            'nombres.required'              => 'Debe ingresar un nombre',
            'dni.required'                  => 'Debe ingresar un N° Documento de Paciente',
            'dni2.required'                 => 'Debe ingresar un N° Documento de Familiar',
            'departamento.required'         => 'Debe ingresar un Departamento',
            'provincia.required'            => 'Debe ingresar un Provincia',
            'distrito.required'             => 'Debe ingresar un Distrito',
            'direccion.required'            => 'Debe ingresar una Dirección',
            'fechanacimiento.required'      => 'Debe ingresar una fecha de nacimiento',
            'antecedentesclinicos.required' => 'Debe ingresar los antecedentes clínicos',
            'fechainicio.required'          => 'Debe ingresar una fecha de inicio de las Citas',
            'horacita.required'             => 'Debe ingresar una hora para las Citas',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $dni         = $request->input('dni');
        $mdlPerson   = new Person();
        $resultado   = Person::where('dni', 'LIKE', $dni);
        $value       = $resultado->first();
        $sucursal_id = Session::get('sucursal_id');
        if (count($value) > 0 && strlen(trim($dni)) > 0) {
            $objHistoria = new Historia();
            //$list2       = Historia::where('person_id','=',$value->id)->where('historia.sucursal_id','=',$sucursal_id)->first();
            $list2 = Historia::where('person_id', '=', $value->id)->first();
            //SI TIENE HISTORIA
            if (count($list2) > 0) { 
                return $dat[0] = array("respuesta" => "Ya tiene historia");
            //NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
            } else { 
                $idpersona = $value->id;
            }
        } else {
            $idpersona = 0;
        }
        $dat   = array();
        $user  = Auth::user();
        $error = DB::transaction(function () use ($request, $idpersona, $user, &$dat) {
            $sucursal_id = Session::get('sucursal_id');
            $Historia    = new Historia();
            if ($idpersona == 0) {
                $person                  = new Person();
                $person->dni             = $request->input('dni');
                $person->apellidopaterno = trim(strtoupper($request->input('apellidopaterno')));
                $person->apellidomaterno = trim(strtoupper($request->input('apellidomaterno')));
                $person->nombres         = trim(strtoupper($request->input('nombres')));
                $person->telefono        = $request->input('telefono');
                $person->nacionalidad    = $request->input('nacionalidad');
                $person->direccion       = trim($request->input('direccion'));
                $person->telefono2       = $request->input('telefono2');
                $person->sexo            = $request->input('sexo');
                $person->raza            = $request->input('raza');
                //$person->email=$request->input('email');
                if ($request->input('fechanacimiento') != "") {
                    $person->fechanacimiento = $request->input('fechanacimiento');
                }
                $person->save();
                $idpersona = $person->id;
            } else {
                $person = Person::find($idpersona);
            }
            //VALIDAMOS EL FAMILIAR
            $familiar = Person::where('dni', '=', $request->input('dni2'))->first();
            if ($familiar === null) {
                $familiar = new Person();
            }
            $familiar->dni             = $request->input('dni2');
            $familiar->apellidopaterno = trim(strtoupper($request->input('apellidopaterno2')));
            $familiar->apellidomaterno = trim(strtoupper($request->input('apellidomaterno2')));
            $familiar->nombres         = trim(strtoupper($request->input('nombres2')));
            $familiar->telefono        = $request->input('telefono21');
            $familiar->direccion       = trim($request->input('direccion2'));
            $familiar->telefono2       = $request->input('telefono22');
            $familiar->nacionalidad    = $request->input('nacionalidad2');
            $familiar->save();
            /////////////////////////////////////////
            $Historia->person_id           = $idpersona;
            $Historia->person2_id          = $familiar->id;
            $Historia->estado2             = 'P';
            $Historia->baja                = 'N';
            $Historia->horacita            = $request->input('horacita');
            $Historia->fechafallecido      = null;
            $Historia->txtTipoAccesoInicio = $request->input('accesovascular');
            $Historia->ordencitas          = $request->input('checktotal');
            $Historia->ordencitasopcional  = $request->input('checktotal2');
            $Historia->fechainicio         = $request->input('fechainicio');
            $Historia->tipopaciente        = $request->input('tipopaciente');
            $Historia->familiar            = $request->input('familiar');
            $Historia->ipress              = $request->input('ipress');
            $Historia->fecha               = date("Y-m-d");
            $Historia->gradoinstruccion    = $request->input('gradoinstruccion');
            $Historia->gruposanguineo      = $request->input('grupos');
            $Historia->antecedentes2       = strtoupper($request->input('antecedentesclinicos'));
            //$Historia->enviadopor=$request->input('enviadopor');
            //$Historia->familiar=$request->input('familiar');
            $Historia->modo        = $request->input('modo');
            $Historia->estadocivil = $request->input('estadocivil');
            //$Historia->ocupacion=$request->input('ocupacion');
            $Historia->departamento     = $request->input('departamento');
            $Historia->provincia        = $request->input('provincia');
            $Historia->distrito         = $request->input('distrito');
            $Historia->categoria        = $request->input('categoria');
            $Historia->detallecategoria = $request->input('detallecategoria');
            $Historia->usuario_id       = $user->person_id;
            if ($request->input('tipopaciente') == "Convenio") {
                $Historia->convenio_id = $request->input('convenio');
                //$Historia->empresa=$request->input('empresa');
                $Historia->carnet       = $request->input('carnet');
                $Historia->plan_susalud = $request->input('plan_susalud');
                $Historia->regimen      = $request->input('regimen');
                //$Historia->poliza=$request->input('poliza');
                //$Historia->soat=$request->input('soat');
                //$Historia->titular=$request->input('titular');
            }
            $Historia->numero  = $request->input('numero');
            $Historia->email   = $request->input('emailh');
            $Historia->estado  = 'N'; //HCINICIAL DESACTIVADA
            $Historia->estado3 = 'N'; //HCINICIAL DESACTIVADA
            //$Historia->sucursal_id = $sucursal_id;
            $Historia->save();
            $RolPersona            = new RolPersona();
            $RolPersona->rol_id    = 3;
            $RolPersona->person_id = $idpersona;
            $RolPersona->save();

            $dat[0] = array("respuesta" => "OK", "id" => $Historia->id, "paciente" => $person->apellidopaterno . ' ' . $person->apellidomaterno . ' ' . $person->nombres, "historia" => $Historia->numero, "person_id" => $Historia->person_id, "tipopaciente" => $Historia->tipopaciente);
        });
        if ($modo == "popup") {
            return is_null($error) ? json_encode($dat) : $error;
        } else {
            return is_null($error) ? json_encode($dat) : $error;
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar      = Libreria::getParam($request->input('listar'), 'NO');
        $modo        = $request->input('modo', '');
        $historia    = Historia::join('person', 'person.id', '=', 'historia.person_id')->where('historia.id', '=', $id)->select('historia.*')->select('person.*', 'historia.*')->first();
        $entidad     = 'Historia';
        $cboConvenio = array();
        $convenios   = Convenio::where(DB::raw('1'), '=', '1')->orderBy('nombre', 'ASC')->get();
        foreach ($convenios as $key => $value) {
            $cboConvenio = $cboConvenio + array($value->id => $value->nombre);
        }
        $cboEstadoCivil = array("SOLTERO(A)" => "SOLTERO(A)", "CASADO(A)" => "CASADO(A)", "VIUDO(A)" => "VIUDO(A)", "DIVORCIADO(A)" => "DIVORCIADO(A)", "CONVIVIENTE" => "CONVIVIENTE");
        //$cboCategoria = array("Normal"=>"Normal","Religioso"=>"Religioso","Doctor"=>"Doctor","Familiar Trabajador"=>"Familiar Trabajador","Aldeas Infantiles"=>"Aldeas Infantiles");
        $cboCategoria    = array("Normal" => "Normal", "Religioso" => "Religioso");
        $cboModo         = array("F" => "Fisico", "V" => "Registro Virtual");
        $cboTipoPaciente = array("Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");

        $turnos   = Turno::orderBy('hora', 'ASC')->get();
        $cboTurno = array();
        $i        = 1;
        foreach ($turnos as $turno) {
            $cboTurno = $cboTurno + array($turno->id => 'TURNO ' . $i . ': ' . $turno->hora);
            $i++;
        }

        $cboSexo    = array("M" => "Masculino", "F" => "Femenino");
        $cboRegimen = array("Semicontributivo" => "Semicontributivo", "Subsidiado" => "Subsidiado", "Ninguno" => "Ninguno");
        $cboGrupo   = array("AB+" => "AB+", "AB-" => "AB-", "A+" => "A+", "A-" => "A-", "B+" => "B+", "B-" => "B-", "O+" => "O+", "O-" => "O-");
        $cboGrado   = array("Inicial" => "Inicial", "Analfabeto" => "Analfabeto", "Primaria" => "Primaria", "Secundaria" => "Secundaria", "Superior" => "Superior");
        $cboAccesoV = array("1" => "FAV", "2" => "Autoinjerto", "3" => "Injerto", "4" => "CVCP", "5" => "CVCT", "6" => "Cperitoneal");

        $user     = Auth::user();
        $formData = array('historia.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';

        $cboDepa       = array('' => '---- Elija uno ----');
        $departamentos = Departamento::orderBy('nombre', 'ASC')->get();
        foreach ($departamentos as $key => $value) {
            $cboDepa = $cboDepa + array($value->id => $value->nombre);
        }
        return view($this->folderview . '.mant')->with(compact('historia', 'formData', 'entidad', 'boton', 'listar', 'cboConvenio', 'cboTipoPaciente', 'cboEstadoCivil', 'modo', 'cboSexo', 'cboDepa', 'cboModo', 'user', 'cboCategoria', 'cboRegimen', 'cboGrupo', 'cboGrado', 'cboAccesoV', 'cboTurno'));
    }

    public function update(Request $request, $id)
    {
        $sucursal_id = Session::get('sucursal_id');
        $existe      = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'nombres'                => 'required',
            'dni'                    => 'required',
            'apellidopaterno'        => 'required',
            'apellidomaterno'        => 'required',
            'nacionalidad'           => 'required',
            'nacionalidad2'          => 'required',
            'nacionalidad.required'  => 'Debe ingresar una nacionalidad',
            'telefono'               => 'required',
            'departamento'           => 'required',
            'provincia'              => 'required',
            'distrito'               => 'required',
            'direccion'              => 'required',
            'fechanacimiento'        => 'required',
            'antecedentesclinicos'   => 'required',
            'fechainicio'            => 'required',
            'horacita'               => 'required',
        );
        $mensajes = array(
            'apellidopaterno.required'      => 'Debe ingresar un apellido paterno',
            'apellidomaterno.required'      => 'Debe ingresar un apellido materno',
            'nacionalidad.required'         => 'Debe ingresar una nacionalidad',
            'nacionalidad2.required'        => 'Debe ingresar una nacionalidad',
            'nacionalidad.required'         => 'Debe ingresar una nacionalidad',
            'nacionalidad2.required'        => 'Debe ingresar una nacionalidad',
            'nombres.required'              => 'Debe ingresar un nombre',
            'dni.required'                  => 'Debe ingresar un N° Documento',
            'departamento.required'         => 'Debe ingresar un Departamento',
            'provincia.required'            => 'Debe ingresar un Provincia',
            'distrito.required'             => 'Debe ingresar un Distrito',
            'direccion.required'            => 'Debe ingresar una Dirección',
            'fechanacimiento.required'      => 'Debe ingresar una fecha de nacimiento',
            'antecedentesclinicos.required' => 'Debe ingresar los antecedentes clínicos',
            'fechainicio.required'          => 'Debe ingresar una fecha de inicio de las Citas',
            'horacita.required'             => 'Debe ingresar una hora para las Citas',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $dni       = $request->input('dni');
        $id_       = $request->input('id');
        $mdlPerson = new Person();
        $resultado = Person::where('dni', 'LIKE', $dni);
        $value     = $resultado->first();
        if (count($value) > 0 && strlen(trim($dni)) > 0) {
            $objHistoria = new Historia();
            $list2       = Historia::where('person_id', '=', $value->id)->where('historia.sucursal_id', $sucursal_id)->where('id', '<>', $id)->first();
            if (count($list2) > 0) {
//SI TIENE HISTORIA
                return "Ya tiene otra historia";
            } else {
//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                $idperson = $value->id;
            }
        } else {
            $idperson = 0;
        }
        $error = DB::transaction(function () use ($request, $id_, $idperson) {
            $sucursal_id = Session::get('sucursal_id');
            $Historia    = Historia::find($id_);
            $idpersona   = $Historia->person_id;
            if ($Historia->modo == "V" && $request->input('modo') == "F") {
                $Historia->fechamodo = date("Y-m-d");
            }
            /*if($idpersona==0){
            $person = new Person();
            $person->dni=$request->input('dni');
            $person->apellidopaterno=trim(strtoupper($request->input('apellidopaterno')));
            $person->apellidomaterno=trim(strtoupper($request->input('apellidomaterno')));
            $person->nombres=trim(strtoupper($request->input('nombres')));
            $person->telefono=$request->input('telefono');
            $person->direccion=trim($request->input('direccion'));
            $person->nombres=$request->input('nombres');
            $person->telefono2=$request->input('telefono2');
            $person->sexo=$request->input('sexo');
            $person->raza=$request->input('raza');
            //$person->email=$request->input('email');
            if($request->input('fechanacimiento')!=""){
            $person->fechanacimiento=$request->input('fechanacimiento');
            }
            $person->save();
            $idpersona=$person->id;
            $list = Movimiento::where('persona_id','=',$Historia->person_id)->where('tipomovimiento_id','=',1)->get();
            foreach ($list as $key => $value) {
            $value->persona_id=$idpersona;
            $value->save();
            }
            }else{*/
            $person                  = Person::find($idpersona);
            $person->dni             = $request->input('dni');
            $person->apellidopaterno = trim($request->input('apellidopaterno'));
            $person->apellidomaterno = trim($request->input('apellidomaterno'));
            $person->nombres         = trim($request->input('nombres'));
            $person->nacionalidad    = trim($request->input('nacionalidad'));
            $person->telefono        = $request->input('telefono');
            $person->direccion       = trim($request->input('direccion'));
            $person->telefono2       = $request->input('telefono2');
            $person->sexo            = $request->input('sexo');
            $person->raza            = $request->input('raza');
            //$person->email=$request->input('email');
            if ($request->input('fechanacimiento') != "") {
                $person->fechanacimiento = $request->input('fechanacimiento');
            }
            $person->save();
            $idpersona = $person->id;
            //}
            //VALIDAMOS EL FAMILIAR
            $familiar = Person::where('dni', '=', $request->input('dni2'))->first();
            if ($familiar === null) {
                $familiar = new Person();
            }
            $familiar->dni             = $request->input('dni2');
            $familiar->apellidopaterno = trim(strtoupper($request->input('apellidopaterno2')));
            $familiar->apellidomaterno = trim(strtoupper($request->input('apellidomaterno2')));
            $familiar->nombres         = trim(strtoupper($request->input('nombres2')));
            $familiar->telefono        = $request->input('telefono21');
            $familiar->direccion       = trim($request->input('direccion2'));
            $familiar->nacionalidad    = trim($request->input('nacionalidad2'));
            $familiar->telefono2       = $request->input('telefono22');
            $familiar->save();
            /////////////////////////////////////////
            $Historia->person_id           = $idpersona;
            $Historia->person2_id          = $familiar->id;
            $Historia->numero              = $request->input('numero');
            $Historia->txtTipoAccesoInicio = $request->input('accesovascular');
            $Historia->ordencitas          = $request->input('checktotal');
            $Historia->ordencitasopcional  = $request->input('checktotal2');
            $Historia->fechainicio         = $request->input('fechainicio');
            $Historia->horacita            = $request->input('horacita');
            $Historia->fechafallecido      = null;
            $Historia->tipopaciente        = $request->input('tipopaciente');
            $Historia->familiar            = $request->input('familiar');
            $Historia->ipress              = $request->input('ipress');
            $Historia->gradoinstruccion    = $request->input('gradoinstruccion');
            $Historia->gruposanguineo      = $request->input('grupos');
            $Historia->antecedentes2       = strtoupper($request->input('antecedentesclinicos'));
            //$Historia->fecha=date("Y-m-d");
            //$Historia->enviadopor=$request->input('enviadopor');
            //$Historia->familiar=$request->input('familiar');
            $Historia->estadocivil      = $request->input('estadocivil');
            $Historia->modo             = $request->input('modo');
            $Historia->departamento     = $request->input('departamento');
            $Historia->provincia        = $request->input('provincia');
            $Historia->distrito         = $request->input('distrito');
            $Historia->categoria        = $request->input('categoria');
            $Historia->detallecategoria = $request->input('detallecategoria');
            if ($request->input('tipopaciente') == "Convenio") {
                $Historia->convenio_id = $request->input('convenio');
                //$Historia->empresa=$request->input('empresa');
                $Historia->carnet       = $request->input('carnet');
                $Historia->plan_susalud = $request->input('plan_susalud');
                $Historia->regimen      = $request->input('regimen');
                //$Historia->poliza=$request->input('poliza');
                //$Historia->soat=$request->input('soat');
                //$Historia->titular=$request->input('titular');
            }
            $Historia->email = $request->input('emailh');
            //$Historia->sucursal_id = $sucursal_id;
            $Historia->save();

            //Elimino los tratamientos invalidos del dia de hoy en caso que la configuraion sea un dia distinto de hoy
            $consultas = HistoriaClinica::where("historia_id", "=", $Historia->id)->where("fecha_atencion", ">=", date("Y-m-d"))->get();
            if (count($consultas) > 0) {
                foreach ($consultas as $const) {
                    $diasemana = date("w", strtotime($const->fecha_atencion));
                    $diasemana = ($diasemana == 0 ? 7 : $diasemana);
                    if (strpos($Historia->ordendecitas, $diasemana) !== false) {
                        $const->delete();
                    }
                }
            }
        });
        $dat    = array();
        $dat[0] = array("respuesta" => "OK");
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function () use ($id) {
            $Historia = Historia::find($id);
            $Historia->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Historia::find($id);
        $entidad  = 'Historia';
        $formData = array('route' => array('historia.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function validarDNI(Request $request)
    {
        $dni         = $request->input("dni");
        $entidad     = 'Person';
        $mdlPerson   = new Person();
        $resultado   = Person::where('dni', 'LIKE', $dni);
        $value       = $resultado->first();
        $sucursal_id = Session::get('sucursal_id');
        if (count($value) > 0) {
            $objHistoria = new Historia();
            //$list2       = Historia::where('person_id','=',$value->id)->where('historia.sucursal_id','=',$sucursal_id)->first();
            $list2 = Historia::where('person_id', '=', $value->id)->first();
            if (count($list2) > 0) {
//SI TIENE HISTORIA
                $data[] = array(
                    'apellidopaterno' => $value->apellidopaterno,
                    'apellidomaterno' => $value->apellidomaterno,
                    'nombres'         => $value->nombres,
                    'telefono'        => $value->telefono,
                    'direccion'       => $value->direccion,
                    'id'              => $value->id,
                    'msg'             => 'N',
                );
            } else {
//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                $data[] = array(
                    'apellidopaterno' => $value->apellidopaterno,
                    'apellidomaterno' => $value->apellidomaterno,
                    'nombres'         => $value->nombres,
                    'telefono'        => $value->telefono,
                    'direccion'       => $value->direccion,
                    'id'              => $value->id,
                    'msg'             => 'S',
                    'modo'            => 'Registrado',
                );
            }
        } else {
            $data[] = array('msg' => 'S', 'modo' => 'Nada');
        }
        return json_encode($data);
    }

    public function validarDNI2(Request $request)
    {
        $dni         = $request->input("dni");
        $entidad     = 'Person';
        $mdlPerson   = new Person();
        $resultado   = Person::where('dni', 'LIKE', $dni);
        $value       = $resultado->first();
        $sucursal_id = Session::get('sucursal_id');
        if (count($value) > 0) {
            $data[] = array(
                'apellidopaterno' => $value->apellidopaterno,
                'apellidomaterno' => $value->apellidomaterno,
                'nombres'         => $value->nombres,
                'telefono'        => $value->telefono,
                'telefono2'       => $value->telefono2,
                'direccion'       => $value->direccion,
                'msg'             => 'N',
            );
        } else {
            $data[] = array('msg' => 'S');
        }
        return json_encode($data);
    }

    public function personautocompletar($searching)
    {
        $entidad     = 'Historia';
        $sucursal_id = Session::get('sucursal_id');
        $resultado   = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->leftjoin('convenio', 'convenio.id', '=', 'historia.convenio_id')
            ->where(DB::raw('concat(person.dni,\' \',apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%' . strtoupper($searching) . '%')
        //->where('historia.sucursal_id', '=', $sucursal_id)
            ->select('historia.*', 'convenio.nombre as convenio2', 'convenio.plan_id');
        $list = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            if ($value->plan_id) {
                $pl          = Plan::find($value->plan_id);
                $plan        = $pl->nombre;
                $coa         = $pl->coaseguro;
                $deducible   = $pl->deducible;
                $ruc         = $pl->ruc;
                $direccion   = $pl->direccion;
                $razonsocial = $pl->razonsocial;
                $tipo        = $pl->tipo;
            } else {
                $pl          = Plan::find(6);
                $plan        = $pl->nombre;
                $coa         = $pl->coaseguro;
                $deducible   = $pl->deducible;
                $ruc         = $pl->ruc;
                $direccion   = $pl->direccion;
                $razonsocial = $pl->razonsocial;
                $tipo        = $pl->tipo;
            }
            $data[] = array(
                'label'        => $value->persona->dni . ' ' . $value->persona->apellidopaterno . ' ' . $value->persona->apellidomaterno . ' ' . $value->persona->nombres,
                'id'           => $value->id,
                'value'        => $value->persona->dni . ' ' . $value->persona->apellidopaterno . ' ' . $value->persona->apellidomaterno . ' ' . $value->persona->nombres,
                'value2'       => $value->persona->apellidopaterno . ' ' . $value->persona->apellidomaterno . ' ' . $value->persona->nombres,
                'numero'       => $value->numero,
                'person_id'    => $value->persona->id,
                'dni'          => $value->persona->dni,
                'tipopaciente' => $value->tipopaciente,
                'telefono'     => $value->persona->telefono,
                'fallecido'    => $value->fallecido,
                'placa'        => $value->poliza,
                'convenio'     => $value->convenio2,
                'plan_id'      => $pl->id,
                'plan'         => $plan,
                'coa'          => $coa,
                'deducible'    => $deducible,
                'ruc'          => $ruc,
                'direccion'    => $direccion,
                'razonsocial'  => $razonsocial,
                'tipo'         => $tipo,
                'direccion2'   => $value->persona->direccion,
                'edad'         => ($value->persona->fechanacimiento == "" ? '0' : $value->persona->fechanacimiento),
                'fecha'        => date('Y-m-d'),
            );
        }
        return json_encode($data);
    }

    public function historiaautocompletar($searching)
    {
        $entidad     = 'Historia';
        $sucursal_id = Session::get('sucursal_id');
        $resultado   = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->leftjoin('convenio', 'convenio.id', '=', 'historia.convenio_id')
            ->where('historia.numero', 'LIKE', '%' . strtoupper($searching) . '%')
            ->whereNull('person.deleted_at')
        //->where('historia.sucursal_id', '=', $sucursal_id)
            ->select('historia.*', 'convenio.nombre as convenio2', 'convenio.plan_id');
        $list = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            if ($value->plan_id) {
                $pl          = Plan::find($value->plan_id);
                $plan        = $pl->nombre;
                $coa         = $pl->coaseguro;
                $deducible   = $pl->deducible;
                $ruc         = $pl->ruc;
                $direccion   = $pl->direccion;
                $razonsocial = $pl->razonsocial;
                $tipo        = $pl->tipo;
            } else {
                $plan        = '';
                $coa         = 0;
                $deducible   = 0;
                $ruc         = '';
                $direccion   = '';
                $razonsocial = '';
                $tipo        = '';
            }
            $data[] = array(
                'label'        => $value->persona->dni . ' ' . $value->persona->apellidopaterno . ' ' . $value->persona->apellidomaterno . ' ' . $value->persona->nombres,
                'id'           => $value->id,
                'value'        => $value->persona->dni . ' ' . $value->persona->apellidopaterno . ' ' . $value->persona->apellidomaterno . ' ' . $value->persona->nombres,
                'value2'       => $value->persona->apellidopaterno . ' ' . $value->persona->apellidomaterno . ' ' . $value->persona->nombres,
                'numero'       => $value->numero,
                'person_id'    => $value->persona->id,
                'dni'          => $value->persona->dni,
                'tipopaciente' => $value->tipopaciente,
                'telefono'     => $value->persona->telefono,
                'fallecido'    => $value->fallecido,
                'placa'        => $value->poliza,
                'convenio'     => $value->convenio2,
                'plan_id'      => $value->plan_id,
                'plan'         => $plan,
                'coa'          => $coa,
                'deducible'    => $deducible,
                'ruc'          => $ruc,
                'direccion'    => $direccion,
                'razonsocial'  => $razonsocial,
                'tipo'         => $tipo,
                'direccion2'   => $value->persona->direccion,
                'edad'         => ($value->persona->fechanacimiento == "" ? '0' : $value->persona->fechanacimiento),
                'fecha'        => date('Y-m-d'),
            );
        }
        return json_encode($data);
    }

    public function pdfSeguimiento(Request $request)
    {
        $resultado = Seguimiento::where('historia_id', '=', $request->id)->orderBy('fechaenvio', 'ASC');
        $lista     = $resultado->get();
        if (count($lista) > 0) {
            $historia = Historia::find($request->id);
            $pdf      = new TCPDF();
            $pdf::SetTitle('Seguimiento de Historia');
            $pdf::AddPage();
            $pdf::SetFont('helvetica', 'B', 12);
            $pdf::Cell(0, 10, utf8_decode("SEGUIMIENTO DE HISTORIA " . $historia->numero), 0, 0, 'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica', 'B', 10);
            $pdf::Cell(20, 9, utf8_decode("PACIENTE: "), 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 10);
            $pdf::Cell(0, 9, utf8_decode($historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres), 0, 0, 'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(20, 6, utf8_decode("TIPO"), 1, 0, 'C');
            $pdf::Cell(30, 6, utf8_decode("FECHA"), 1, 0, 'C');
            $pdf::Cell(40, 6, utf8_decode("AREA"), 1, 0, 'C');
            $pdf::Cell(40, 6, utf8_decode("USUARIO"), 1, 0, 'C');
            $pdf::Cell(60, 6, utf8_decode("COMENTARIO"), 1, 0, 'C');
            $pdf::Ln();

            foreach ($lista as $key => $value) {
                $pdf::SetFont('helvetica', '', 8);
                $pdf::Cell(20, 5, utf8_decode("ENVIADO"), 1, 0, 'C');
                $pdf::Cell(30, 5, utf8_decode($value->fechaenvio), 1, 0, 'L');
                $pdf::Cell(40, 5, utf8_decode($value->areaenvio->nombre), 1, 0, 'C');
                $pdf::Cell(40, 5, utf8_decode($value->personaenvio->apellidopaterno . " " . $value->personaenvio->apellidomaterno . " " . $value->personaenvio->nombres), 1, 0, 'C');
                $pdf::Cell(60, 5, utf8_decode($value->comentario), 1, 0, 'C');
                $pdf::Ln();
                if ($value->fecharecepcion != "") {
                    if ($value->situacion == "A") {
                        $pdf::Cell(20, 5, utf8_decode("RECIBIDO"), 1, 0, 'C');
                    } else {
                        $pdf::Cell(20, 5, utf8_decode("RECHAZADO"), 1, 0, 'C');
                    }
                    $pdf::Cell(30, 5, utf8_decode($value->fecharecepcion), 1, 0, 'L');
                    $pdf::Cell(40, 5, utf8_decode($value->areadestino->nombre), 1, 0, 'C');
                    $pdf::Cell(40, 5, utf8_decode($value->personarecepcion->apellidopaterno . " " . $value->personarecepcion->apellidomaterno . " " . $value->personarecepcion->nombres), 1, 0, 'C');
                    $pdf::Cell(60, 5, "", 1, 0, 'C');
                    $pdf::Ln();
                }
            }
            $pdf::Output('ListaCita.pdf');
        }
    }

    public function pdfHistoria(Request $request)
    {

        $historia = Historia::find($request->id);
        if ($historia->departamento != 0) {
            $departamento = Departamento::find($historia->departamento);
            $provincia    = Provincia::find($historia->provincia);
            $distrito     = Distrito::find($historia->distrito);
        } else {
            $departamento = (object) array('nombre' => '-');
            $provincia    = (object) array('nombre' => '-');
            $distrito     = (object) array('nombre' => '-');
        }
        $pdf = new TCPDF();
        $pdf::SetTitle('Historia');
        $pdf::AddPage();
        $pdf::Image("dist/img/logo2-nefrocix.jpg", 20, 7, 50, 15);
        $pdf::SetFont('helvetica', 'B', 15);
        $pdf::Cell(60, 10, "", 0, 0, 'C');
        $pdf::Cell(75, 10, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 18);
        $pdf::Cell(50, 10, utf8_decode($historia->numero), 1, 0, 'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica', 'B', 15);
        $pdf::Cell(60, 10, strtoupper(""), 0, 0, 'C');
        $pdf::Cell(70, 10, "", 0, 0, 'C');
        $pdf::Ln(3);
        $pdf::Cell(60, 6, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 14);
        $pdf::Cell(70, 4, "**************************************************", 0, 0, 'C');
        $pdf::Ln(3);
        $pdf::SetFont('helvetica', 'B', 14);
        $pdf::Cell(48, 10, "", 0, 0, 'C');
        $pdf::Cell(95, 10, utf8_decode(utf8_encode("HISTORIA CLÍNICA")), 'B', 0, 'C');
        $pdf::Ln(12);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(65, 8, utf8_decode("DATOS DEL PACIENTE:"), 'B', 0, 'L');
        $pdf::Ln(8);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("FECHA: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, date("d/m/Y", strtotime($historia->fecha)), 0, 0, 'C');
        $pdf::Cell(10, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("HORA: "), 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, date("H:i:s", strtotime($historia->created_at)), 0, 0, 'C');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("PACIENTE: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 8, ($historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(30, 8, utf8_decode("NACIONALIDAD: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 8, ($historia->persona->nacionalidad), 0, 0, 'L');
        $pdf::Ln(6);
        /*$pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(28,8,utf8_decode("TIPO PACIENTE: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,strtoupper($historia->tipopaciente),0,0,'L');
        $pdf::Ln(6);*/
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("G. INSTR.: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode($historia->gradoinstruccion), 0, 0, 'L');
        $pdf::Cell(20, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(29, 8, utf8_decode("GRUPO SANG.: "), 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(29, 8, utf8_decode(utf8_encode($historia->gruposanguineo)), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(45, 8, utf8_decode("IPRESS DE PROCEDENCIA.: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode($historia->ipress), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(45, 8, utf8_decode("ACCESO VASCULAR.: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $tav = '';
        if ($historia->txtTipoAccesoInicio == '1') {
            $tav = 'FAV';
        }
        if ($historia->txtTipoAccesoInicio == '2') {
            $tav = 'Autoinjerto';
        }
        if ($historia->txtTipoAccesoInicio == '3') {
            $tav = 'Injerto';
        }
        if ($historia->txtTipoAccesoInicio == '4') {
            $tav = 'CVCP';
        }
        if ($historia->txtTipoAccesoInicio == '5') {
            $tav = 'CVCT';
        }
        if ($historia->txtTipoAccesoInicio == '6') {
            $tav = 'Cperitoneal';
        }
        $pdf::Cell(29, 8, strtoupper(utf8_decode(utf8_encode($tav))), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("DNI/CE: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode($historia->persona->dni), 0, 0, 'L');
        $pdf::Cell(10, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("SEXO: "), 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode($historia->persona->sexo), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("FECHA NAC:"), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, date("d/m/Y", strtotime($historia->persona->fechanacimiento)), 0, 0, 'L');
        $pdf::Cell(10, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("EDAD: "), 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 9);
        if ($historia->persona->fechanacimiento != '') {
            $fechanacimiento = new DateTime($historia->persona->fechanacimiento);
            $hoy             = new DateTime();
            $annos           = $hoy->diff($fechanacimiento);
            $pdf::Cell(20, 8, $annos->y, 0, 0, 'L');
        } else {
            $pdf::Cell(20, 8, '-', 0, 0, 'L');
        }
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("DOMICILIO:"), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->persona->direccion)), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(29, 8, utf8_decode("DEPARTAMENTO:"), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode(utf8_encode($departamento->nombre)), 0, 0, 'L');
        $pdf::Cell(15, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("PROVINCIA:"), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        if (!is_null($provincia)) {
            $pdf::Cell(20, 8, utf8_decode(utf8_encode($provincia->nombre)), 0, 0, 'L');
        } else {
            $pdf::Cell(20, 8, utf8_decode(""), 0, 0, 'L');
        }
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("DISTRITO: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        if (!is_null($distrito)) {
            $pdf::Cell(20, 8, utf8_decode(utf8_encode($distrito->nombre)), 0, 0, 'L');
        } else {
            $pdf::Cell(20, 8, '', 0, 0, 'L');
        }
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("CATEGORIA: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(25, 8, utf8_decode(utf8_encode(' ' . $historia->categoria)), 0, 0, 'L');
        $pdf::Cell(15, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("DETALLE: "), 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->detallecategoria)), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("TELEFONO: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->persona->telefono . ' - ' . $historia->persona->telefono2)), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(28, 8, utf8_decode("ESTADO CIVIL: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->estadocivil)), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(28, 8, utf8_decode("CORREO: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode(utf8_encode(($historia->email == '' || $historia->email == null ? '-' : $historia->email))), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        /*$pdf::Cell(20,8,utf8_decode("FAM. RESP: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode(utf8_encode($historia->familiar)),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("ENV. POR: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode(utf8_encode($historia->enviadopor)),0,0,'L');*/
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(65, 8, utf8_decode("DATOS DEL FAMILIAR:"), 'B', 0, 'L');
        $pdf::Ln(8);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("DNI/CE: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode($historia->persona2->dni), 0, 0, 'L');
        $pdf::Cell(10, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("TELEFONO: "), 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode($historia->persona2->telefono . ($historia->persona2->telefono2 == null ? '' : (' - ' . $historia->persona2->telefono2))), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("NOMBRE: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 8, ($historia->persona2->apellidopaterno . " " . $historia->persona2->apellidomaterno . " " . $historia->persona2->nombres), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("RELACION: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 8, ($historia->txtRelacion == null ? '-' : $historia->txtRelacion), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("DOMICILIO:"), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->persona2->direccion)), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(29, 8, utf8_decode("DEPARTAMENTO:"), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, ($historia->persona2->distrito2 == null ? '-' : utf8_decode(utf8_encode($historia->persona2->distrito2->provincia->departamento->nombre))), 0, 0, 'L');
        $pdf::Cell(15, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("PROVINCIA:"), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, ($historia->persona2->distrito2 == null ? '-' : utf8_decode(utf8_encode($historia->persona2->distrito2->provincia->nombre))), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("DISTRITO: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, ($historia->persona2->distrito2 == null ? '-' : utf8_decode(utf8_encode($historia->persona2->distrito2->nombre))), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(65, 8, utf8_decode("DATOS DEL CONVENIO:"), 'B', 0, 'L');
        $pdf::Ln(8);
        /*$pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("SOAT "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode(utf8_encode($historia->soat)),0,0,'L');
        $pdf::Ln();*
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("TITULAR: "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode(utf8_encode($historia->titular)),0,0,'L');
        $pdf::Ln();*/
        if ($historia->tipopaciente == 'Convenio') {
            $pdf::Cell(40, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(20, 8, utf8_decode("EMPRESA: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);
            if ($historia->convenio_id == null) {
                $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->empresa)), 0, 0, 'L');
            } else {
                $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->convenio->nombre)), 0, 0, 'L');
                //.' - '.$historia->empresa
            }
            $pdf::Ln(6);
            $pdf::Cell(40, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(20, 8, utf8_decode("REGIMEN: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);
            $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->regimen)), 0, 0, 'L');
            $pdf::Ln(6);
            $pdf::Cell(40, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(20, 8, utf8_decode("CARNET: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);
            $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->carnet)), 0, 0, 'L');
            $pdf::Ln(6);
            $pdf::Cell(40, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(20, 8, utf8_decode("N. PLAN: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);
            $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->plan_susalud)), 0, 0, 'L');
            $pdf::Ln(6);
        } else {
            $pdf::Cell(40, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(20, 8, utf8_decode("EMPRESA: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);
            $pdf::Cell(20, 8, utf8_decode(utf8_encode($historia->empresa)), 0, 0, 'L');
            $pdf::Ln(6);
        }
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(65, 8, utf8_decode(utf8_encode("CONFIGURACIÓN DE CITAS:")), 'B', 0, 'L');
        $pdf::Ln(8);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(20, 8, utf8_decode("TURNO: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(20, 8, utf8_decode($historia->horacita), 0, 0, 'L');
        $pdf::Ln(6);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 9);
        $pdf::Cell(30, 8, utf8_decode("CONF. SEMANAL: "), 0, 0, 'L');
        $pdf::SetFont('helvetica', '', 9);

        $dias  = explode(';', $historia->ordencitas);
        $sdias = '';

        foreach ($dias as $dia) {
            if ($dia == '1') {$sdias .= ' LUNES ';} else if ($dia == '2') {$sdias .= ' MARTES ';} else if ($dia == '3') {$sdias .= ' MIERCOLES ';} else if ($dia == '4') {$sdias .= ' JUEVES ';} else if ($dia == '5') {$sdias .= ' VIERNES ';} else if ($dia == '6') {$sdias .= ' SABADO ';} else if ($dia == '7') {$sdias .= ' DOMINGO ';}
        }
        $pdf::Cell(80, 8, utf8_decode($sdias), 0, 0, 'L');
        /*$pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("POLIZA: "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode(utf8_encode($historia->poliza)),0,0,'L');
        $pdf::Ln();*/
        /*$pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("CARNET: "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode(utf8_encode($historia->carnet)),0,0,'L');
        $pdf::Ln();*/

        $pdf::Ln(6);
        $pdf::Ln(6);
        $pdf::Ln(6);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::Cell(50, 8, "IDENTIDAD MEDICINA - HISTORIAS CLINICA", 0, 0, 'C');
        $pdf::Cell(40, 8, "", 0, 0, 'C');
        $pdf::Cell(15, 8, "USUARIO:", 0, 0, 'C');
        $pdf::SetFont('helvetica', '', 8);
        if ($historia->usuario_id > 0) {
            $pdf::Cell(50, 8, utf8_decode(utf8_encode($historia->usuario->nombres)), 0, 0, 'L');
        } else {
            $pdf::Cell(50, 8, "", 0, 0, 'C');
        }
        $pdf::Output('Historia.pdf');
    }

    public function guardarfallecido(Request $request)
    {
        $error = DB::transaction(function () use ($request) {
            $Historia                 = Historia::find($request->input('historia_id'));
            $Historia->fechafallecido = $request->input('fecha');
            $Historia->fallecido      = $request->input("fallecido");
            $Historia->motivobaja     = $request->input("motivo");
            $Historia->baja           = 'S';
            $Historia->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function fallecido($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Historia::find($id);
        $entidad  = 'Historia';
        $paciente = $modelo->persona->apellidopaterno . " " . $modelo->persona->apellidomaterno . " " . $modelo->persona->nombres;
        $numero   = $modelo->numero;
        $formData = array('route' => array('historia.guardarfallecido', $id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Dar de baja';
        return view($this->folderview . '.fallecido')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'numero', 'paciente'));
    }

    public function guardarActivar($id,Request $request)
    {
        $error = DB::transaction(function () use ($request,$id) {
            $Historia                 = Historia::find($id);
            $Historia->fechafallecido = null;
            $Historia->fallecido      = 'N';
            $Historia->motivobaja     = null;
            $Historia->baja           = 'N';
            $Historia->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function activar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Historia::find($id);
        $entidad  = 'Historia';
        $paciente = $modelo->persona->apellidopaterno . " " . $modelo->persona->apellidomaterno . " " . $modelo->persona->nombres;
        $numero   = $modelo->numero;
        $formData = array('route' => array('historia.guardarActivar', $id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Activar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'numero', 'paciente'));
    }

    public function pdfHistoria2(Request $request)
    {
        $citas    = HistoriaClinica::where('historia_id', $request->input('id'))->where('estado', '!=', 'C')->get();
        $historia = Historia::find($request->id);
        $pdf      = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf::SetTitle('Historial de Citas');
        // remove default header/footer
        $pdf::setPrintHeader(false);
        $pdf::setPrintFooter(false);

        // set default monospaced font
        $pdf::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // set auto page breaks
        $pdf::SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf::AddPage();
        $pdf::Image("dist/img/logo2-nefrocix.jpg", 20, 26, 50, 15);
        $pdf::SetFont('helvetica', 'B', 15);
        $pdf::Cell(60, 10, "", 0, 0, 'C');
        $pdf::Cell(75, 10, "", 0, 0, 'C');
        $pdf::SetFont('helvetica', 'B', 10);
        $pdf::Cell(40, 10, utf8_decode('Historia ' . $historia->numero), 1, 0, 'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica', 'B', 15);
        $pdf::Cell(60, 10, strtoupper(""), 0, 0, 'C');
        $pdf::Cell(70, 10, "", 0, 0, 'C');
        $pdf::Cell(60, 6, "", 0, 0, 'C');

        $o = 0;
        foreach ($citas as $cita) {

            //Solo los antecedentes anteriores del paciente :v

            if ($o == 0) {

                $pdf::Ln(4);
                $pdf::SetFont('helvetica', 'B', 14);
                $pdf::Cell(48, 10, "", 0, 0, 'C');
                $pdf::Cell(95, 10, 'ANTECEDENTES ANTERIORES', 'B', 0, 'C');
                $pdf::Ln(4);
                $pdf::Ln(4);
                $pdf::Ln(4);
                $pdf::Ln(4);
                $pdf::Cell(8, 8, "", 0, 0, 'C');
                $pdf::SetFont('helvetica', 'B', 9);
                $pdf::Cell(35, 8, utf8_decode("PACIENTE: "), 0, 0, 'L');
                $pdf::SetFont('helvetica', '', 9);
                $pdf::Multicell(120, 8, ($historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres), 0, 'L');

                $pdf::Ln(2);
                $pdf::Ln(3);
                $pdf::Ln(3);
                $pdf::SetFont('helvetica', '', 9);
                $pdf::Cell(8, 8, "", 0, 0, 'C');
                $pdf::Multicell(165, 8, ($historia->antecedentes2), 0, 'L');

                $pdf::Ln(2);
                $pdf::Ln(3);
                $pdf::Ln(3);

            }

            //

            $pdf::Ln(4);
            $pdf::SetFont('helvetica', 'B', 14);
            $pdf::Cell(48, 10, "", 0, 0, 'C');
            $pdf::Cell(95, 10, 'Cita N° ' . $cita->numero . ' / ' . date('d-m-Y', strtotime($cita->fecha_atencion)), 'B', 0, 'C');
            $pdf::Ln(4);
            $pdf::Ln(4);
            $pdf::Ln(4);
            $pdf::Ln(4);
            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, utf8_decode("PACIENTE: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);
            $pdf::Multicell(120, 8, ($historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres), 0, 'L');

            $pdf::Ln(2);
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, utf8_decode("CIE 10: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);

            $cadenacies = '';

            if ($cita->cie_id != 0 || $cita->cie_id != '') {
                $cadenacies .= $cita->cie->codigo . ' ' . $cita->cie->descripcion . '<br>';
            }

            $cies = Detallehistoriacie::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

            if (count($cies) != 0) {
                foreach ($cies as $value) {
                    $cadenacies .= $value->cie->codigo . ' ' . $value->cie->descripcion . '<br>';
                }
            }
            $cies2 = explode('<BR>', strtoupper($cadenacies));
            if ($cies2[0] == '') {
                $cies2[] = '-';
            }
            $i = 0;
            foreach ($cies2 as $c) {
                if ($c != '') {
                    if ($i != 0) {
                        $pdf::Cell(8, 8, "", 0, 0, 'C');
                        $pdf::Cell(35, 8, "", 0, 0, 'L');
                    }
                    $pdf::Multicell(120, 8, $c == '' ? '-' : $c, 0, 'L');
                    $pdf::Ln(3);
                    $i++;
                }
            }

            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, utf8_decode("MOTIVO: "), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);

            $mot = explode('<BR>', strtoupper($cita->motivo));
            if ($mot[0] == '') {
                $mot[] = '-';
            }
            $i = 0;
            foreach ($mot as $m) {
                if ($m != '') {
                    if ($i != 0) {
                        $pdf::Cell(8, 8, "", 0, 0, 'C');
                        $pdf::Cell(35, 8, "", 0, 0, 'L');
                    }
                    $pdf::Multicell(120, 8, $m == '' ? '-' : $m, 0, 'L');
                    $pdf::Ln(3);
                    $i++;
                }
            }
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, "DIAGNÓSTICO: ", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);

            $diag = explode('<BR>', strtoupper($cita->diagnostico));
            if ($diag[0] == '') {
                $diag[] = '-';
            }
            $i = 0;
            foreach ($diag as $d) {
                if ($d != '') {
                    if ($i != 0) {
                        $pdf::Cell(8, 8, "", 0, 0, 'C');
                        $pdf::Cell(35, 8, "", 0, 0, 'L');
                    }
                    $pdf::Multicell(120, 8, $d, 0, 'L');
                    $pdf::Ln(3);
                    $i++;
                }
            }
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, "TRATAMIENTO: ", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);

            $trat = explode('<BR>', strtoupper($cita->tratamiento));
            if ($trat[0] == '') {
                $trat[] = '-';
            }
            $i = 0;
            foreach ($trat as $t) {
                if ($t != '') {
                    if ($i != 0) {
                        $pdf::Cell(8, 8, "", 0, 0, 'C');
                        $pdf::Cell(35, 8, "", 0, 0, 'L');
                    }
                    $pdf::Multicell(120, 8, $t, 0, 'L');
                    $pdf::Ln(3);
                    $i++;
                }
            }
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, "EXÁMENES: ", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);

            $cadenaexamenes = '';

            $examenes = Examenhistoriaclinica::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

            if (count($examenes) != 0) {
                foreach ($examenes as $value) {
                    $cadenaexamenes .= $value->servicio->nombre . '<br>';
                }
            }

            $examenes2 = explode('<BR>', strtoupper($cadenaexamenes));

            if ($examenes2[0] == '') {
                $examenes2[] = '-';
            }

            $i = 0;
            foreach ($examenes2 as $e) {
                if ($e != '') {
                    if ($i != 0) {
                        $pdf::Cell(8, 8, "", 0, 0, 'C');
                        $pdf::Cell(35, 8, "", 0, 0, 'L');
                    }
                    $pdf::Multicell(120, 8, $e, 0, 'L');
                    $pdf::Ln(3);
                    $i++;
                }
            }
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, "EXPLOR. FÍSICA: ", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);

            $exf = explode('<BR>', strtoupper($cita->exploracion_fisica));

            if ($exf[0] == '') {
                $exf[] = '-';
            }

            $i = 0;
            foreach ($exf as $ef) {
                if ($ef != '') {
                    if ($i != 0) {
                        $pdf::Cell(8, 8, "", 0, 0, 'C');
                        $pdf::Cell(35, 8, "", 0, 0, 'L');
                    }
                    $pdf::Multicell(120, 8, $ef, 0, 'L');
                    $pdf::Ln(3);
                    $i++;
                }
            }
            $pdf::Ln(3);
            $pdf::Ln(3);
            $pdf::Cell(8, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 9);
            $pdf::Cell(35, 8, "COMENTARIO: ", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 9);
            $pdf::Multicell(120, 8, $cita->comentario == null ? '-' : $cita->comentario, 0, 'L');
            $o++;
        }
        $pdf::Output('HistorialCitas.pdf');
    }

    public function unirHistorias(Request $request)
    {
        /*$historias = Person::select('h1.id as i1', 'h2.id as i2', 'h1.numero as n1', 'h1.sucursal_id as s1', 'h2.numero as n2', 'h2.sucursal_id as s2', 'h1.person_id as p1', 'h2.person_id as p2', 'person.id')
        ->leftjoin('historia as h1', 'h1.person_id', '=', 'person.id')
        ->leftjoin('historia as h2', 'h2.person_id', '=', 'person.id')
        ->where('h1.sucursal_id', '=', 1)
        ->where('h2.sucursal_id', '=', 2)
        ->whereRaw('h1.person_id = h2.person_id')
        ->orderBy('h1.numero')
        ->orderBy('h2.numero')
        ->get();*/

        /*select `h1`.`numero` as `n1`, `h1`.`sucursal_id` as `s1`, `h2`.`numero` as `n2`, `h2`.`sucursal_id` as `s2`, `h1`.`person_id` as `p1`, `h2`.`person_id` as `p2`, `person`.`id`
        from `person`
        inner join `historia` as `h1` on `h1`.`person_id` = `person`.`id`
        inner join `historia` as `h2` on `h2`.`person_id` = `person`.`id`
        where `person`.`deleted_at` is null
        and `h1`.`person_id` = h2.person_id
        and h1.sucursal_id = 1
        and h2.sucursal_id = 2
        order by `h1`.`numero` asc, `h2`.`numero` asc*/

        /*$mensaje = '';

        foreach ($historias as $value) {
        //Eliminar la Historia con sucursal_id = 1, rescatar su numero de historia

        //Pasar ese id a id_alternativo y buscar historiasclinicas, actualizar la referencia con el nuevo id
        }

        echo $mensaje;*/

        $personas = Person::select('id')->get();
        foreach ($personas as $value) {
            $historia1 = Historia::where('person_id', '=', $value->id)->where('sucursal_id', '=', 1)->first();
            $historia2 = Historia::where('person_id', '=', $value->id)->where('sucursal_id', '=', 2)->first();
            //Solo si hay dos historias (ojos y esp)
            if ($historia1 !== null && $historia2 !== null) {
                //busco las historiasclinicas que tienen id de historia de ojos
                $historiasclinicas = HistoriaClinica::where('historia_id', '=', $historia1->id)->get();
                if (count($historiasclinicas) > 0) {
                    foreach ($historiasclinicas as $hc) {
                        //Actualizo la historia_id a la de la sucursal 2 (esp)
                        $hc->historia_id = $historia2->id;
                        $hc->save();
                    }
                }
                //busco las citas que tienen id de historia de ojos
                $citas = Cita::where('historia_id', '=', $historia1->id)->get();
                if (count($citas) > 0) {
                    foreach ($citas as $cita) {
                        //Actualizo la historia_id a la de la sucursal 2 (esp)
                        $cita->historia_id = $historia2->id;
                        $cita->save();
                    }
                }
                //Elimino sucursal en Historia2
                //$historia2->sucursal_id=null;
                //$historia2->save();
                //Elimino historia con sucursal 1
                $historia1->delete();
            }
        }
        //Reestructurar números de historia
        $historias = Historia::select('historia.id')->orderBy(DB::raw('CONCAT(apellidopaterno, " ", apellidomaterno, " ", nombres)'), 'ASC')
            ->join('person as p', 'p.id', '=', 'historia.person_id')
            ->get();
        $i = 1;
        foreach ($historias as $history) {
            $historia = Historia::find($history->id);
            $numero2  = $historia->numero;
            $numero1  = str_pad($i, 8, '0', STR_PAD_LEFT);
            if ($historia->sucursal_id == 1) {
                $historia->numero2 = null;
            } else {
                $historia->numero2 = $numero2;
            }
            $historia->sucursal_id = null;
            $historia->numero      = $numero1;
            $historia->save();
            echo $historia->sucursal_id;
            $i++;
        }
    }

    //Eliminar Historias Clinicas con número = 0

    public function unirHistorias2(Request $request)
    {
        $historiasclinicas = HistoriaClinica::where('numero', '=', '0')->get();
        foreach ($historiasclinicas as $hc) {
            $hc->delete();
        }
        echo count($historiasclinicas) . ' HISTORIAS CLÍNICAS ELIMINADAS';
    }

    public function createhcinicial(Request $request)
    {
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $id            = $request->input('id');
        $modo          = $request->input('modo', '');
        $entidad       = 'Historia22';
        $historia      = Historia::find($id);
        $cboDepa       = array('' => '---- Elija uno ----');
        $departamentos = Departamento::orderBy('nombre', 'ASC')->get();
        foreach ($departamentos as $key => $value) {
            $cboDepa = $cboDepa + array($value->id => $value->nombre);
        }
        $cboEtiologia                = array('' => '---Selecciona una Etiología---');
        $cboModalidadInicioTRR       = array('1' => 'Hemodiálisis', '2' => 'Diálisis Peritoneal', '3' => 'Transplante Renal');
        $cboSubsistemaSaludInicioTRR = array('1' => 'Essalud', '2' => 'Minsa', '3' => 'EPS', '4' => 'FFAA', '5' => 'FFPP', '6' => 'Otros');
        $cboTipoAccesoInicio         = array("1" => "FAV", "2" => "Autoinjerto", "3" => "Injerto", "4" => "CVCP", "5" => "CVCT", "6" => "Cperitoneal");
        $etiologias                  = Etiologia::where('etiologia_id', '=', null)->orderBy('codigo', 'ASC')->get();
        foreach ($etiologias as $key => $value) {
            $cboEtiologia = $cboEtiologia + array($value->id => $value->nombre);
        }
        $sucursal_id = Session::get('sucursal_id');
        $user        = Auth::user();
        $formData    = array('historia.storehcinicial');
        $formData    = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton       = 'Registrar';
        return view($this->folderview . '.hcinicial')->with(compact('historia', 'formData', 'entidad', 'boton', 'listar', 'modo', 'cboDepa', 'user', 'id', 'cboEtiologia', 'cboModalidadInicioTRR', 'cboSubsistemaSaludInicioTRR', 'cboTipoAccesoInicio'));
    }

    public function storehcinicial(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $Historia = Historia::find($request->input('historia_id'));
        $dat      = array();
        $error    = DB::transaction(function () use ($request, $Historia, &$dat) {
            $txtEnfermedad                           = $request->input('txtEnfermedad') == null || $request->input('txtEnfermedad') == "" ? null : $request->input('txtEnfermedad');
            $txtEtiologia1_id                        = $request->input('txtEtiologia1_id') == null || $request->input('txtEtiologia1_id') == "" ? null : $request->input('txtEtiologia1_id');
            $txtEtiologia2_id                        = $request->input('txtEtiologia2_id') == null || $request->input('txtEtiologia2_id') == "" ? null : $request->input('txtEtiologia2_id');
            $txtComorbilidades                       = $request->input('txtComorbilidades') == null || $request->input('txtComorbilidades') == "" ? null : $request->input('txtComorbilidades');
            $txtFechaPrimeraHemodialisis             = $request->input('txtFechaPrimeraHemodialisis') == null || $request->input('txtFechaPrimeraHemodialisis') == "" ? null : $request->input('txtFechaPrimeraHemodialisis');
            $txtNumeroTransfusiones                  = $request->input('txtNumeroTransfusiones') == null || $request->input('txtNumeroTransfusiones') == "" ? null : $request->input('txtNumeroTransfusiones');
            $txtIntervencionesQuirurgicas            = $request->input('txtIntervencionesQuirurgicas') == null || $request->input('txtIntervencionesQuirurgicas') == "" ? null : $request->input('txtIntervencionesQuirurgicas');
            $txtMedicacion                           = $request->input('txtMedicacion') == null || $request->input('txtMedicacion') == "" ? null : $request->input('txtMedicacion');
            $txtDiuresis1                            = $request->input('txtDiuresis1') == null || $request->input('txtDiuresis1') == "" ? null : $request->input('txtDiuresis1');
            $txtDiuresis2                            = $request->input('txtDiuresis2') == null || $request->input('txtDiuresis2') == "" ? null : $request->input('txtDiuresis2');
            $txtAlergia                              = $request->input('txtAlergia') == null || $request->input('txtAlergia') == "" ? null : $request->input('txtAlergia');
            $txtCantDosis                            = $request->input('txtCantDosis') == null || $request->input('txtCantDosis') == "" ? null : $request->input('txtCantDosis');
            $txtFechaCantDosis1                      = $request->input('txtFechaCantDosis1') == null || $request->input('txtFechaCantDosis1') == "" ? null : $request->input('txtFechaCantDosis1');
            $txtFechaCantDosis2                      = $request->input('txtFechaCantDosis2') == null || $request->input('txtFechaCantDosis2') == "" ? null : $request->input('txtFechaCantDosis2');
            $txtFechaCantDosis3                      = $request->input('txtFechaCantDosis3') == null || $request->input('txtFechaCantDosis3') == "" ? null : $request->input('txtFechaCantDosis3');
            $txtFechaCantDosis4                      = $request->input('txtFechaCantDosis4') == null || $request->input('txtFechaCantDosis4') == "" ? null : $request->input('txtFechaCantDosis4');
            $txtFechaCantDosis5                      = $request->input('txtFechaCantDosis5') == null || $request->input('txtFechaCantDosis5') == "" ? null : $request->input('txtFechaCantDosis5');
            $txtFechaCantDosis6                      = $request->input('txtFechaCantDosis6') == null || $request->input('txtFechaCantDosis6') == "" ? null : $request->input('txtFechaCantDosis6');
            $txtFechaCantDosis7                      = $request->input('txtFechaCantDosis7') == null || $request->input('txtFechaCantDosis7') == "" ? null : $request->input('txtFechaCantDosis7');
            $txtFechaCantDosis8                      = $request->input('txtFechaCantDosis8') == null || $request->input('txtFechaCantDosis8') == "" ? null : $request->input('txtFechaCantDosis8');
            $txtFechaCantDosis9                      = $request->input('txtFechaCantDosis9') == null || $request->input('txtFechaCantDosis9') == "" ? null : $request->input('txtFechaCantDosis9');
            $txtFechaCantDosis10                     = $request->input('txtFechaCantDosis10') == null || $request->input('txtFechaCantDosis10') == "" ? null : $request->input('txtFechaCantDosis10');
            $txtFechaCantDosis11                     = $request->input('txtFechaCantDosis11') == null || $request->input('txtFechaCantDosis11') == "" ? null : $request->input('txtFechaCantDosis11');
            $txtFechaCantDosis12                     = $request->input('txtFechaCantDosis12') == null || $request->input('txtFechaCantDosis12') == "" ? null : $request->input('txtFechaCantDosis12');
            $txtFechaDialisisPeritoneal1             = $request->input('txtFechaDialisisPeritoneal1') == null || $request->input('txtFechaDialisisPeritoneal1') == "" ? null : $request->input('txtFechaDialisisPeritoneal1');
            $txtFechaDialisisPeritoneal2             = $request->input('txtFechaDialisisPeritoneal2') == null || $request->input('txtFechaDialisisPeritoneal2') == "" ? null : $request->input('txtFechaDialisisPeritoneal2');
            $txtTransplanteRenal1                    = $request->input('txtTransplanteRenal1') == null || $request->input('txtTransplanteRenal1') == "" ? null : $request->input('txtTransplanteRenal1');
            $txtTransplanteRenal2                    = $request->input('txtTransplanteRenal2') == null || $request->input('txtTransplanteRenal2') == "" ? null : $request->input('txtTransplanteRenal2');
            $txtAntecedentesPMedicos                 = $request->input('txtAntecedentesPMedicos') == null || $request->input('txtAntecedentesPMedicos') == "" ? null : $request->input('txtAntecedentesPMedicos');
            $txtAntecedentesPQuirurgicos             = $request->input('txtAntecedentesPQuirurgicos') == null || $request->input('txtAntecedentesPQuirurgicos') == "" ? null : $request->input('txtAntecedentesPQuirurgicos');
            $txtSintomasEnfermedadActual             = $request->input('txtSintomasEnfermedadActual') == null || $request->input('txtSintomasEnfermedadActual') == "" ? null : $request->input('txtSintomasEnfermedadActual');
            $txtPresionArterial1                     = $request->input('txtPresionArterial1') == null || $request->input('txtPresionArterial1') == "" ? null : $request->input('txtPresionArterial1');
            $txtPresionArterial2                     = $request->input('txtPresionArterial2') == null || $request->input('txtPresionArterial2') == "" ? null : $request->input('txtPresionArterial2');
            $txtFC                                   = $request->input('txtFC') == null || $request->input('txtFC') == "" ? null : $request->input('txtFC');
            $txtFR                                   = $request->input('txtFR') == null || $request->input('txtFR') == "" ? null : $request->input('txtFR');
            $txtPeso                                 = $request->input('txtPeso') == null || $request->input('txtPeso') == "" ? null : $request->input('txtPeso');
            $txtTalla                                = $request->input('txtTalla') == null || $request->input('txtTalla') == "" ? null : $request->input('txtTalla');
            $txtPiel                                 = $request->input('txtPiel') == null || $request->input('txtPiel') == "" ? null : $request->input('txtPiel');
            $txtNumeroAccesoVascular                 = $request->input('txtNumeroAccesoVascular') == null || $request->input('txtNumeroAccesoVascular') == "" ? null : $request->input('txtNumeroAccesoVascular');
            $txtTiempoPermanenciaAccesosVasculares   = $request->input('txtTiempoPermanenciaAccesosVasculares') == null || $request->input('txtTiempoPermanenciaAccesosVasculares') == "" ? null : $request->input('txtTiempoPermanenciaAccesosVasculares');
            $txtCambioPerdida                        = $request->input('txtCambioPerdida') == null || $request->input('txtCambioPerdida') == "" ? null : $request->input('txtCambioPerdida');
            $cbxDescripcionResponsableRealizacion2   = $request->input('cbxDescripcionResponsableRealizacion2') == null || $request->input('cbxDescripcionResponsableRealizacion2') == "" ? null : $request->input('cbxDescripcionResponsableRealizacion2');
            $txtDescripcionResponsableRealizacion    = $request->input('txtDescripcionResponsableRealizacion') == null || $request->input('txtDescripcionResponsableRealizacion') == "" ? null : $request->input('txtDescripcionResponsableRealizacion');
            $txtFechaAccesoVascularActual            = $request->input('txtFechaAccesoVascularActual') == null || $request->input('txtFechaAccesoVascularActual') == "" ? null : $request->input('txtFechaAccesoVascularActual');
            $cbxUbicacionVascularActual2             = $request->input('cbxUbicacionVascularActual2') == null || $request->input('cbxUbicacionVascularActual2') == "" ? null : $request->input('cbxUbicacionVascularActual2');
            $txtUbicacionVascularActual              = $request->input('txtUbicacionVascularActual') == null || $request->input('txtUbicacionVascularActual') == "" ? null : $request->input('txtUbicacionVascularActual');
            $cbxTipoDescripcionAccesoVascularActual2 = $request->input('cbxTipoDescripcionAccesoVascularActual2') == null || $request->input('cbxTipoDescripcionAccesoVascularActual2') == "" ? null : $request->input('cbxTipoDescripcionAccesoVascularActual2');
            $txtTipoDescripcionAccesoVascularActual  = $request->input('txtTipoDescripcionAccesoVascularActual') == null || $request->input('txtTipoDescripcionAccesoVascularActual') == "" ? null : $request->input('txtTipoDescripcionAccesoVascularActual');
            $cbxThill2                               = $request->input('cbxThill2') == null || $request->input('cbxThill2') == "" ? null : $request->input('cbxThill2');
            $txtCorazon                              = $request->input('txtCorazon') == null || $request->input('txtCorazon') == "" ? null : $request->input('txtCorazon');
            $txtPulsosPerifericos                    = $request->input('txtPulsosPerifericos') == null || $request->input('txtPulsosPerifericos') == "" ? null : $request->input('txtPulsosPerifericos');
            $txtAparatoRespiratorio                  = $request->input('txtAparatoRespiratorio') == null || $request->input('txtAparatoRespiratorio') == "" ? null : $request->input('txtAparatoRespiratorio');
            $txtAbdomen                              = $request->input('txtAbdomen') == null || $request->input('txtAbdomen') == "" ? null : $request->input('txtAbdomen');
            $txtNeurologicos                         = $request->input('txtNeurologicos') == null || $request->input('txtNeurologicos') == "" ? null : $request->input('txtNeurologicos');
            $txtOsteomuscular                        = $request->input('txtOsteomuscular') == null || $request->input('txtOsteomuscular') == "" ? null : $request->input('txtOsteomuscular');
            $txtEstadoNutricional                    = $request->input('txtEstadoNutricional') == null || $request->input('txtEstadoNutricional') == "" ? null : $request->input('txtEstadoNutricional');
            $txtKarnofski                            = $request->input('txtKarnofski') == null || $request->input('txtKarnofski') == "" ? null : $request->input('txtKarnofski');
            $txtFechaGrupoSanguineoLetra             = $request->input('txtFechaGrupoSanguineoLetra') == null || $request->input('txtFechaGrupoSanguineoLetra') == "" ? null : $request->input('txtFechaGrupoSanguineoLetra');
            $txtFechaGrupoSanguineoSigno             = $request->input('txtFechaGrupoSanguineoSigno') == null || $request->input('txtFechaGrupoSanguineoSigno') == "" ? null : $request->input('txtFechaGrupoSanguineoSigno');

            $txtHbHto = ($request->input('txtHbHto1') == null || $request->input('txtHbHto1') == "" ? null : $request->input('txtHbHto1')) . "/" . ($request->input('txtHbHto2') == null || $request->input('txtHbHto2') == "" ? null : $request->input('txtHbHto2'));

            $txtFechaHbHto                = $request->input('txtFechaHbHto') == null || $request->input('txtFechaHbHto') == "" ? null : $request->input('txtFechaHbHto');
            $txtTiempoHemodialisis        = $request->input('txtTiempoHemodialisis') == null || $request->input('txtTiempoHemodialisis') == "" ? null : $request->input('txtTiempoHemodialisis');
            $txtTransfusionesPrevias      = $request->input('txtTransfusionesPrevias') == null || $request->input('txtTransfusionesPrevias') == "" ? null : $request->input('txtTransfusionesPrevias');
            $txtGlicemia                  = $request->input('txtGlicemia') == null || $request->input('txtGlicemia') == "" ? null : $request->input('txtGlicemia');
            $txtFechaGlicemia             = $request->input('txtFechaGlicemia') == null || $request->input('txtFechaGlicemia') == "" ? null : $request->input('txtFechaGlicemia');
            $txtDepuracionCreatina        = $request->input('txtDepuracionCreatina') == null || $request->input('txtDepuracionCreatina') == "" ? null : $request->input('txtDepuracionCreatina');
            $txtFechaDepuracionCreatina   = $request->input('txtFechaDepuracionCreatina') == null || $request->input('txtFechaDepuracionCreatina') == "" ? null : $request->input('txtFechaDepuracionCreatina');
            $txtEndogena                  = $request->input('txtEndogena') == null || $request->input('txtEndogena') == "" ? null : $request->input('txtEndogena');
            $txtFechaEndogena             = $request->input('txtFechaEndogena') == null || $request->input('txtFechaEndogena') == "" ? null : $request->input('txtFechaEndogena');
            $txtUremia                    = $request->input('txtUremia') == null || $request->input('txtUremia') == "" ? null : $request->input('txtUremia');
            $txtFechaUremia               = $request->input('txtFechaUremia') == null || $request->input('txtFechaUremia') == "" ? null : $request->input('txtFechaUremia');
            $txtCreatinina                = $request->input('txtCreatinina') == null || $request->input('txtCreatinina') == "" ? null : $request->input('txtCreatinina');
            $txtFechaCreatinina           = $request->input('txtFechaCreatinina') == null || $request->input('txtFechaCreatinina') == "" ? null : $request->input('txtFechaCreatinina');
            $txtAcidoUrico                = $request->input('txtAcidoUrico') == null || $request->input('txtAcidoUrico') == "" ? null : $request->input('txtAcidoUrico');
            $txtFechaAcidoUrico           = $request->input('txtFechaAcidoUrico') == null || $request->input('txtFechaAcidoUrico') == "" ? null : $request->input('txtFechaAcidoUrico');
            $txtProteinas                 = $request->input('txtProteinas') == null || $request->input('txtProteinas') == "" ? null : $request->input('txtProteinas');
            $txtFechaProteinas            = $request->input('txtFechaProteinas') == null || $request->input('txtFechaProteinas') == "" ? null : $request->input('txtFechaProteinas');
            $txtAlbumina                  = $request->input('txtAlbumina') == null || $request->input('txtAlbumina') == "" ? null : $request->input('txtAlbumina');
            $txtFechaAlbumina             = $request->input('txtFechaAlbumina') == null || $request->input('txtFechaAlbumina') == "" ? null : $request->input('txtFechaAlbumina');
            $txtCalcio                    = $request->input('txtCalcio') == null || $request->input('txtCalcio') == "" ? null : $request->input('txtCalcio');
            $txtFechaCalcio               = $request->input('txtFechaCalcio') == null || $request->input('txtFechaCalcio') == "" ? null : $request->input('txtFechaCalcio');
            $txtFosforo                   = $request->input('txtFosforo') == null || $request->input('txtFosforo') == "" ? null : $request->input('txtFosforo');
            $txtFechaFosforo              = $request->input('txtFechaFosforo') == null || $request->input('txtFechaFosforo') == "" ? null : $request->input('txtFechaFosforo');
            $txtTGO                       = $request->input('txtTGO') == null || $request->input('txtTGO') == "" ? null : $request->input('txtTGO');
            $txtFechaTGO                  = $request->input('txtFechaTGO') == null || $request->input('txtFechaTGO') == "" ? null : $request->input('txtFechaTGO');
            $txtTGP                       = $request->input('txtTGP') == null || $request->input('txtTGP') == "" ? null : $request->input('txtTGP');
            $txtFechaTGP                  = $request->input('txtFechaTGP') == null || $request->input('txtFechaTGP') == "" ? null : $request->input('txtFechaTGP');
            $txtBilirrubina               = $request->input('txtBilirrubina') == null || $request->input('txtBilirrubina') == "" ? null : $request->input('txtBilirrubina');
            $txtFechaBilirrubina          = $request->input('txtFechaBilirrubina') == null || $request->input('txtFechaBilirrubina') == "" ? null : $request->input('txtFechaBilirrubina');
            $txtHierroSerico              = $request->input('txtHierroSerico') == null || $request->input('txtHierroSerico') == "" ? null : $request->input('txtHierroSerico');
            $txtFechaHierroSerico         = $request->input('txtFechaHierroSerico') == null || $request->input('txtFechaHierroSerico') == "" ? null : $request->input('txtFechaHierroSerico');
            $txtTransferrina              = $request->input('txtTransferrina') == null || $request->input('txtTransferrina') == "" ? null : $request->input('txtTransferrina');
            $txtFechaTransferrina         = $request->input('txtFechaTransferrina') == null || $request->input('txtFechaTransferrina') == "" ? null : $request->input('txtFechaTransferrina');
            $txtParatohormona             = $request->input('txtParatohormona') == null || $request->input('txtParatohormona') == "" ? null : $request->input('txtParatohormona');
            $txtFechaParatohormona        = $request->input('txtFechaParatohormona') == null || $request->input('txtFechaParatohormona') == "" ? null : $request->input('txtFechaParatohormona');
            $cbxSerologicasLues2          = $request->input('cbxSerologicasLues2') == null || $request->input('cbxSerologicasLues2') == "" ? null : $request->input('cbxSerologicasLues2');
            $txtFechaSerologicasLues      = $request->input('txtFechaSerologicasLues') == null || $request->input('txtFechaSerologicasLues') == "" ? null : $request->input('txtFechaSerologicasLues');
            $cbxAgHbs2                    = $request->input('cbxAgHbs2') == null || $request->input('cbxAgHbs2') == "" ? null : $request->input('cbxAgHbs2');
            $txtFechaAgHbs                = $request->input('txtFechaAgHbs') == null || $request->input('txtFechaAgHbs') == "" ? null : $request->input('txtFechaAgHbs');
            $cbxAcHbs2                    = $request->input('cbxAcHbs2') == null || $request->input('cbxAcHbs2') == "" ? null : $request->input('cbxAcHbs2');
            $txtFechaAcHbs                = $request->input('txtFechaAcHbs') == null || $request->input('txtFechaAcHbs') == "" ? null : $request->input('txtFechaAcHbs');
            $cbxAcHbc2                    = $request->input('cbxAcHbc2') == null || $request->input('cbxAcHbc2') == "" ? null : $request->input('cbxAcHbc2');
            $txtFechaAcHbc                = $request->input('txtFechaAcHbc') == null || $request->input('txtFechaAcHbc') == "" ? null : $request->input('txtFechaAcHbc');
            $cbxAcHVC2                    = $request->input('cbxAcHVC2') == null || $request->input('cbxAcHVC2') == "" ? null : $request->input('cbxAcHVC2');
            $txtFechaAcHVC                = $request->input('txtFechaAcHVC') == null || $request->input('txtFechaAcHVC') == "" ? null : $request->input('txtFechaAcHVC');
            $cbxHIV2                      = $request->input('cbxHIV2') == null || $request->input('cbxHIV2') == "" ? null : $request->input('cbxHIV2');
            $txtFechaHIV                  = $request->input('txtFechaHIV') == null || $request->input('txtFechaHIV') == "" ? null : $request->input('txtFechaHIV');
            $cbxVacunacionHepatitisB2     = $request->input('cbxVacunacionHepatitisB2') == null || $request->input('cbxVacunacionHepatitisB2') == "" ? null : $request->input('cbxVacunacionHepatitisB2');
            $cbxEcografiaRenal            = $request->input('cbxEcografiaRenal') == null || $request->input('cbxEcografiaRenal') == "" ? null : $request->input('cbxEcografiaRenal');
            $txtFechaEcografiaRenal       = $request->input('txtFechaEcografiaRenal') == null || $request->input('txtFechaEcografiaRenal') == "" ? null : $request->input('txtFechaEcografiaRenal');
            $txtObservacionEcografiaRenal = $request->input('txtObservacionEcografiaRenal') == null || $request->input('txtObservacionEcografiaRenal') == "" ? null : $request->input('txtObservacionEcografiaRenal');
            $cbxRXTorax                   = $request->input('cbxRXTorax') == null || $request->input('cbxRXTorax') == "" ? null : $request->input('cbxRXTorax');
            $txtFechaRXTorax              = $request->input('txtFechaRXTorax') == null || $request->input('txtFechaRXTorax') == "" ? null : $request->input('txtFechaRXTorax');
            $txtObservacionRXTorax        = $request->input('txtObservacionRXTorax') == null || $request->input('txtObservacionRXTorax') == "" ? null : $request->input('txtObservacionRXTorax');
            $txtIdDoctor                  = $request->input('txtIdDoctor') == null || $request->input('txtIdDoctor') == "" ? null : $request->input('txtIdDoctor');
            $txtRelacion                  = $request->input('txtRelacion') == null || $request->input('txtRelacion') == "" ? null : $request->input('txtRelacion');
            $estado                       = 'S'; //HCINICIAL ACTIVADA

            //
            $txtSubsistemaSaludInicioTRR = $request->input('txtSubsistemaSaludInicioTRR') == null || $request->input('txtSubsistemaSaludInicioTRR') == "" ? null : $request->input('txtSubsistemaSaludInicioTRR');
            $txtModalidadInicioTRR       = $request->input('txtModalidadInicioTRR') == null || $request->input('txtModalidadInicioTRR') == "" ? null : $request->input('txtModalidadInicioTRR');
            $txtFechaModalidadInicioTRR  = $request->input('txtFechaModalidadInicioTRR') == null || $request->input('txtFechaModalidadInicioTRR') == "" ? null : $request->input('txtFechaModalidadInicioTRR');
            $txtTipoAccesoInicio         = $request->input('txtTipoAccesoInicio') == null || $request->input('txtTipoAccesoInicio') == "" ? null : $request->input('txtTipoAccesoInicio');
            $txtFechaTipoAccesoInicio    = $request->input('txtFechaTipoAccesoInicio') == null || $request->input('txtFechaTipoAccesoInicio') == "" ? null : $request->input('txtFechaTipoAccesoInicio');

            //////////////////////////////////

            $Historia->txtEnfermedad                          = $txtEnfermedad;
            $Historia->txtEtiologia1_id                       = $txtEtiologia1_id;
            $Historia->txtEtiologia2_id                       = $txtEtiologia2_id;
            $Historia->txtComorbilidades                      = $txtComorbilidades;
            $Historia->txtFechaPrimeraHemodialisis            = $txtFechaPrimeraHemodialisis;
            $Historia->txtNumeroTransfusiones                 = $txtNumeroTransfusiones;
            $Historia->txtIntervencionesQuirurgicas           = $txtIntervencionesQuirurgicas;
            $Historia->txtMedicacion                          = $txtMedicacion;
            $Historia->txtDiuresis1                           = $txtDiuresis1;
            $Historia->txtDiuresis2                           = $txtDiuresis2;
            $Historia->txtAlergia                             = $txtAlergia;
            $Historia->txtCantDosis                           = $txtCantDosis;
            $Historia->txtFechaCantDosis1                     = $txtFechaCantDosis1;
            $Historia->txtFechaCantDosis2                     = $txtFechaCantDosis2;
            $Historia->txtFechaCantDosis3                     = $txtFechaCantDosis3;
            $Historia->txtFechaCantDosis4                     = $txtFechaCantDosis4;
            $Historia->txtFechaCantDosis5                     = $txtFechaCantDosis5;
            $Historia->txtFechaCantDosis6                     = $txtFechaCantDosis6;
            $Historia->txtFechaCantDosis7                     = $txtFechaCantDosis7;
            $Historia->txtFechaCantDosis8                     = $txtFechaCantDosis8;
            $Historia->txtFechaCantDosis9                     = $txtFechaCantDosis9;
            $Historia->txtFechaCantDosis10                    = $txtFechaCantDosis10;
            $Historia->txtFechaCantDosis11                    = $txtFechaCantDosis11;
            $Historia->txtFechaCantDosis12                    = $txtFechaCantDosis12;
            $Historia->txtFechaDialisisPeritoneal1            = $txtFechaDialisisPeritoneal1;
            $Historia->txtFechaDialisisPeritoneal2            = $txtFechaDialisisPeritoneal2;
            $Historia->txtTransplanteRenal1                   = $txtTransplanteRenal1;
            $Historia->txtTransplanteRenal2                   = $txtTransplanteRenal2;
            $Historia->txtAntecedentesPMedicos                = $txtAntecedentesPMedicos;
            $Historia->txtAntecedentesPQuirurgicos            = $txtAntecedentesPQuirurgicos;
            $Historia->txtSintomasEnfermedadActual            = $txtSintomasEnfermedadActual;
            $Historia->txtPresionArterial1                    = $txtPresionArterial1;
            $Historia->txtPresionArterial2                    = $txtPresionArterial2;
            $Historia->txtFC                                  = $txtFC;
            $Historia->txtFR                                  = $txtFR;
            $Historia->txtPeso                                = $txtPeso;
            $Historia->txtTalla                               = $txtTalla;
            $Historia->txtPiel                                = $txtPiel;
            $Historia->txtNumeroAccesoVascular                = $txtNumeroAccesoVascular;
            $Historia->txtTiempoPermanenciaAccesosVasculares  = $txtTiempoPermanenciaAccesosVasculares;
            $Historia->txtCambioPerdida                       = $txtCambioPerdida;
            $Historia->cbxDescripcionResponsableRealizacion   = $cbxDescripcionResponsableRealizacion2;
            $Historia->txtDescripcionResponsableRealizacion   = $txtDescripcionResponsableRealizacion;
            $Historia->txtFechaAccesoVascularActual           = $txtFechaAccesoVascularActual;
            $Historia->cbxUbicacionVascularActual             = $cbxUbicacionVascularActual2;
            $Historia->txtUbicacionVascularActual             = $txtUbicacionVascularActual;
            $Historia->cbxTipoDescripcionAccesoVascularActual = $cbxTipoDescripcionAccesoVascularActual2;
            $Historia->txtTipoDescripcionAccesoVascularActual = $txtTipoDescripcionAccesoVascularActual;
            $Historia->cbxThill                               = $cbxThill2;
            $Historia->txtCorazon                             = $txtCorazon;
            $Historia->txtPulsosPerifericos                   = $txtPulsosPerifericos;
            $Historia->txtAparatoRespiratorio                 = $txtAparatoRespiratorio;
            $Historia->txtAbdomen                             = $txtAbdomen;
            $Historia->txtNeurologicos                        = $txtNeurologicos;
            $Historia->txtOsteomuscular                       = $txtOsteomuscular;
            $Historia->txtEstadoNutricional                   = $txtEstadoNutricional;
            $Historia->txtKarnofski                           = $txtKarnofski;
            $Historia->txtFechaGrupoSanguineoLetra            = $txtFechaGrupoSanguineoLetra;
            $Historia->txtFechaGrupoSanguineoSigno            = $txtFechaGrupoSanguineoSigno;
            $Historia->txtHbHto                               = $txtHbHto;
            $Historia->txtFechaHbHto                          = $txtFechaHbHto;
            $Historia->txtTiempoHemodialisis                  = $txtTiempoHemodialisis;
            $Historia->txtTransfusionesPrevias                = $txtTransfusionesPrevias;
            $Historia->txtGlicemia                            = $txtGlicemia;
            $Historia->txtFechaGlicemia                       = $txtFechaGlicemia;
            $Historia->txtDepuracionCreatina                  = $txtDepuracionCreatina;
            $Historia->txtFechaDepuracionCreatina             = $txtFechaDepuracionCreatina;
            $Historia->txtEndogena                            = $txtEndogena;
            $Historia->txtFechaEndogena                       = $txtFechaEndogena;
            $Historia->txtUremia                              = $txtUremia;
            $Historia->txtFechaUremia                         = $txtFechaUremia;
            $Historia->txtCreatinina                          = $txtCreatinina;
            $Historia->txtFechaCreatinina                     = $txtFechaCreatinina;
            $Historia->txtAcidoUrico                          = $txtAcidoUrico;
            $Historia->txtFechaAcidoUrico                     = $txtFechaAcidoUrico;
            $Historia->txtProteinas                           = $txtProteinas;
            $Historia->txtFechaProteinas                      = $txtFechaProteinas;
            $Historia->txtAlbumina                            = $txtAlbumina;
            $Historia->txtFechaAlbumina                       = $txtFechaAlbumina;
            $Historia->txtCalcio                              = $txtCalcio;
            $Historia->txtFechaCalcio                         = $txtFechaCalcio;
            $Historia->txtFosforo                             = $txtFosforo;
            $Historia->txtFechaFosforo                        = $txtFechaFosforo;
            $Historia->txtTGO                                 = $txtTGO;
            $Historia->txtFechaTGO                            = $txtFechaTGO;
            $Historia->txtTGP                                 = $txtTGP;
            $Historia->txtFechaTGP                            = $txtFechaTGP;
            $Historia->txtBilirrubina                         = $txtBilirrubina;
            $Historia->txtFechaBilirrubina                    = $txtFechaBilirrubina;
            $Historia->txtHierroSerico                        = $txtHierroSerico;
            $Historia->txtFechaHierroSerico                   = $txtFechaHierroSerico;
            $Historia->txtTransferrina                        = $txtTransferrina;
            $Historia->txtFechaTransferrina                   = $txtFechaTransferrina;
            $Historia->txtParatohormona                       = $txtParatohormona;
            $Historia->txtFechaParatohormona                  = $txtFechaParatohormona;
            $Historia->cbxSerologicasLues                     = $cbxSerologicasLues2;
            $Historia->txtFechaSerologicasLues                = $txtFechaSerologicasLues;
            $Historia->cbxAgHbs                               = $cbxAgHbs2;
            $Historia->txtFechaAgHbs                          = $txtFechaAgHbs;
            $Historia->cbxAcHbs                               = $cbxAcHbs2;
            $Historia->txtFechaAcHbs                          = $txtFechaAcHbs;
            $Historia->cbxAcHbc                               = $cbxAcHbc2;
            $Historia->txtFechaAcHbc                          = $txtFechaAcHbc;
            $Historia->cbxAcHVC                               = $cbxAcHVC2;
            $Historia->txtFechaAcHVC                          = $txtFechaAcHVC;
            $Historia->cbxHIV                                 = $cbxHIV2;
            $Historia->txtFechaHIV                            = $txtFechaHIV;
            $Historia->cbxVacunacionHepatitisB                = $cbxVacunacionHepatitisB2;
            $Historia->cbxEcografiaRenal                      = $cbxEcografiaRenal;
            $Historia->txtFechaEcografiaRenal                 = $txtFechaEcografiaRenal;
            $Historia->txtObservacionEcografiaRenal           = $txtObservacionEcografiaRenal;
            $Historia->cbxRXTorax                             = $cbxRXTorax;
            $Historia->txtFechaRXTorax                        = $txtFechaRXTorax;
            $Historia->txtObservacionRXTorax                  = $txtObservacionRXTorax;
            $Historia->txtIdDoctor                            = $txtIdDoctor;
            $Historia->txtRelacion                            = $txtRelacion;
            $Historia->estado                                 = 'S'; //HCINICIAL ACTIVADA

            $Historia->txtSubsistemaSaludInicioTRR = $txtSubsistemaSaludInicioTRR;
            $Historia->txtModalidadInicioTRR       = $txtModalidadInicioTRR;
            $Historia->txtFechaModalidadInicioTRR  = $txtFechaModalidadInicioTRR;
            $Historia->txtTipoAccesoInicio         = $txtTipoAccesoInicio;
            $Historia->txtFechaTipoAccesoInicio    = $txtFechaTipoAccesoInicio;

            $Historia->save();

            $familiar = Person::where('dni', '=', $request->input('txtDNI2'))->first();
            if ($familiar === null) {
                $familiar = new Person();
            }
            $familiar->direccion = $request->input('txtDireccion2');
            $familiar->telefono  = $request->input('txtTelefono2');
            $familiar->telefono2 = $request->input('txtTelefono22');
            $familiar->distrito  = $request->input('txtDistrito2');
            $familiar->save();

            $dat[0] = array("respuesta" => "OK", "id" => $Historia->id, "paciente" => $Historia->persona->apellidopaterno . ' ' . $Historia->persona->apellidomaterno . ' ' . $Historia->persona->nombres, "historia" => $Historia->numero, "person_id" => $Historia->person_id, "tipopaciente" => $Historia->tipopaciente);
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function cargarCantidadEquipos(Request $request)
    {
        $fechainicio = $request->input('fechainicio');
        $horacitaid  = $request->input('horacita');
        $turno       = Turno::find($horacitaid);

        $horacita = $turno->hora;

        //CANTIDAD DE EQUIPOS

        $cantidadequ = Configuracion::where('nombre', '=', 'CANTIDAD DE EQUIPOS')->first();

        if ($cantidadequ !== null) {

            //CANTIDAD DE EQUIPOS
            $cantidadequipos = $cantidadequ->cantidad;

            //LUNES
            $citaslunes         = Historia::where('ordencitas', 'LIKE', '%1%')->where('horacita', '=', $horacitaid)->where('fechainicio', '<=', $fechainicio)->get();
            $cantidadcitaslunes = count($citaslunes);

            //MARTES
            $citasmartes         = Historia::where('ordencitas', 'LIKE', '%2%')->where('horacita', '=', $horacitaid)->where('fechainicio', '<=', $fechainicio)->get();
            $cantidadcitasmartes = count($citasmartes);

            //MIERCOLES
            $citasmiercoles         = Historia::where('ordencitas', 'LIKE', '%3%')->where('horacita', '=', $horacitaid)->where('fechainicio', '<=', $fechainicio)->get();
            $cantidadcitasmiercoles = count($citasmiercoles);

            //JUEVES
            $citasjueves         = Historia::where('ordencitas', 'LIKE', '%4%')->where('horacita', '=', $horacitaid)->where('fechainicio', '<=', $fechainicio)->get();
            $cantidadcitasjueves = count($citasjueves);

            //VIERNES
            $citasviernes         = Historia::where('ordencitas', 'LIKE', '%5%')->where('horacita', '=', $horacitaid)->where('fechainicio', '<=', $fechainicio)->get();
            $cantidadcitasviernes = count($citasviernes);

            //SABADO
            $citassabado         = Historia::where('ordencitas', 'LIKE', '%6%')->where('horacita', '=', $horacitaid)->where('fechainicio', '<=', $fechainicio)->get();
            $cantidadcitassabado = count($citassabado);

            //DOMINGO
            $citasdomingo         = Historia::where('ordencitas', 'LIKE', '%7%')->where('horacita', '=', $horacitaid)->where('fechainicio', '<=', $fechainicio)->get();
            $cantidadcitasdomingo = count($citasdomingo);

            $dat = array("cantidadcitaslunes" => $cantidadcitaslunes, "cantidadcitasmartes" => $cantidadcitasmartes, "cantidadcitasmiercoles" => $cantidadcitasmiercoles, "cantidadcitasjueves" => $cantidadcitasjueves, "cantidadcitasviernes" => $cantidadcitasviernes, "cantidadcitassabado" => $cantidadcitassabado, 'cantidadcitasdomingo' => $cantidadcitasdomingo, "cantidadcitaslunes2" => ($cantidadequipos - $cantidadcitaslunes), "cantidadcitasmartes2" => ($cantidadequipos - $cantidadcitasmartes), "cantidadcitasmiercoles2" => ($cantidadequipos - $cantidadcitasmiercoles), "cantidadcitasjueves2" => ($cantidadequipos - $cantidadcitasjueves), "cantidadcitasviernes2" => ($cantidadequipos - $cantidadcitasviernes), "cantidadcitassabado2" => ($cantidadequipos - $cantidadcitassabado), 'cantidadcitasdomingo2' => ($cantidadequipos - $cantidadcitasdomingo));
            return json_encode($dat);
        } else {
            $dat = array("cantidadcitaslunes" => "error");
            return json_encode($dat);
        }
    }

    public function buscaEtiologia(Request $request)
    {
        $id         = $request->input('id');
        $etiologia  = Etiologia::find($id);
        $etiologias = Etiologia::where('etiologia_id', '=', $id)->get();
        $text       = '<option value="">---Seleccionar una Etiología---</option>';
        foreach ($etiologias as $eti) {
            $text .= '<option value="' . $eti->id . '">' . $eti->nombre . '</option>';
        }

        echo $text;
    }

    public function pdfHistoriaClinica(Request $request)
    {

        date_default_timezone_set('America/Lima');

        $hc = HistoriaClinica::where('id', '=', $request->id)->where('fecha_atencion', '>=', date('Y-m-d'))
        ->where('estado', '=', 'F')->orderBy('fecha_atencion', 'ASC')->first();

        if ($hc === null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $pdf = new TCPDF();
            // set margins
            $pdf::SetMargins(8, 8, 8);

            //color blue
            $pdf::SetTextColor(34, 68, 136);

            // set auto page breaks
            $pdf::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

            $Medicacion = $hc->txtMedicacion;

            $pdf::SetTitle('HistoriaClinica');
            $pdf::AddPage();
            $pdf::Image("dist/img/logo2-nefrocix.jpg", 10, 7, 50, 25);
            $pdf::SetFont('helvetica', 'B', 15);
            $pdf::Cell(60, 3, "", 0, 0, 'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica', 'B', 14);
            $pdf::Cell(48, 10, "", 0, 0, 'C');
            $pdf::Cell(145, 0, utf8_decode(utf8_encode("FORMATO DE PROCEDIMIENTO DE HEMODIÁLISIS")), '', 0, 'C');
            $pdf::Ln();
            $pdf::Ln();
            $pdf::Ln(3);
            $pdf::Cell(1, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 5, utf8_decode(utf8_encode("Apellidos y Nombres: " . $hc->historia->persona->apellidopaterno . ' ' . $hc->historia->persona->apellidomaterno . ' ' . $hc->historia->persona->nombres)), '', 0, 'L');
            $pdf::Cell(70, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Fecha: " . date('d/m/Y', strtotime($hc->fecha_atencion)))), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(1, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 5, utf8_decode(utf8_encode("N° de SIS: " . $hc->historia->carnet)), '', 0, 'L');
            $pdf::Cell(36, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("N° de historia clínica: " . $hc->historia->numero)), 0, 0, 'C');

            //Calculo veces por semana (frecuencia)
            $ordencitas         = explode(';', $hc->historia->ordencitas);
            $ordencitasopcional = explode(';', $hc->historia->ordencitasopcional);
            $frecuencia         = count($ordencitas) - count($ordencitasopcional);
            //Calculo N Hemodialisis mes
            $fecha  = date('Y-m-d');
            $fecha0 = explode('-', $fecha);
            $fechai = $fecha0[0] . '-' . $fecha0[1] . '-';
            $a      = 0;
            for ($i = 1; $i <= $fecha0[2]; $i++) {
                $fechaf = $fechai . str_pad($i, 2, "0", STR_PAD_LEFT);
                $hc2    = HistoriaClinica::where('historia_id', '=', $hc->historia->id)->where('fecha_atencion', 'LIKE', $fechaf . '%')->where('estado', '=', 'F')->first();
                if ($hc2 !== null) {
                    $a++;
                }
            }
            $numsesion = $a;
            $pdf::Ln();
            $pdf::Cell(1, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 5, utf8_decode(utf8_encode("N° de sesión de hemodiálisis del mes: " . $numsesion)), '', 0, 'L');
            $pdf::Cell(36, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Frecuencia (veces/semana): " . $frecuencia)), 0, 0, 'C');
            $pdf::Cell(25, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Turno: " . $hc->historia->turno->romano)), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(3, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("I. Parte de atención médica")), '', 0, 'L');
            $pdf::Cell(61, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 8, utf8_decode(utf8_encode("Evaluación Previa: " . date('H:i', strtotime($hc->fecha_atencion)))), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(18, 5, utf8_decode(utf8_encode("Evolución: ")), '', 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            //$pdf::Cell(52,5,utf8_decode(utf8_encode("Signos y Síntomas: ")),'',0,'L');

            //cadenacies
            $cies       = explode(';', $hc->txtCies);
            $cadenacies = '';
            for ($ss = 0; $ss < count($cies) - 1; $ss++) {
                $cie = Cie::find($cies[$ss]);
                $cadenacies .= "* " . $cie->codigo . ': ' . $cie->descripcion . "\n";
            }

            $pdf::MultiCell(120, 5, utf8_decode(utf8_encode($cadenacies)) . utf8_decode(utf8_encode($hc->txtEvoSigSin)), 0, '', 0, 0, '', '', false);
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Funciones vitales:")), 0, 0, 'C');

            /*$pdf::Ln();
            $pdf::Cell(5,5,"",0,0,'C');
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(18,5,utf8_decode(utf8_encode("Signos y Síntomas: ")),'',0,'L');
            $pdf::SetFont('helvetica','',8);
            $pdf::Cell(5,5,"",0,0,'C');
            $pdf::SetFont('helvetica','',8);
            $pdf::Cell(20,5,utf8_decode(utf8_encode($hc->txtEvoSigSin)),0,0,'C');*/

            $pdf::Ln();
            $pdf::Cell(6, 5, "", 0, 0, 'C');
            $pdf::Cell(73, 5, utf8_decode(utf8_encode("")), '', 0, 'L');
            $pdf::Cell(61, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("PA:")), 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPA)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(6, 5, "", 0, 0, 'C');
            $pdf::Cell(73, 5, utf8_decode(utf8_encode("")), '', 0, 'L');
            $pdf::Cell(61, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("FC:")), 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtFC)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(6, 5, "", 0, 0, 'C');
            $pdf::Cell(73, 5, utf8_decode(utf8_encode("")), '', 0, 'L');
            $pdf::Cell(61, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("FR:")), 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtFR)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Ln();
            $pdf::Cell(5, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("Prescripción para máquina de Hemodiálisis:")), '', 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Horas de hemodiálisis: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtHorasHemodialisis)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso Inicial: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPesoInicial)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Qb: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtQb)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Na inicial: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtNaInicial)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Dosis de heparina: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtDosisHepa)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso Final: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPesoFinal)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Qd: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtQd)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Na final: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtNaFinal)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso seco: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtPesoSeco)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Perfil de UF: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPerfilUF)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Buffer: Bicarbonato")), 0, 0, 'L');
            $pdf::Cell(35, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Perfin de Na.: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtBufer)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Ultrafiltrado a programar: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtUltrafiltrado)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Conductividad: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtConductividad)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Temperatura:       " . $hc->txtTemperatura . " °C")), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Medicación: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode("SE INDICA EN LA TABLA DEBAJO.")), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("Prescripción para dializador:")), '', 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Área de dializador: " . $hc->txtAreaDializador)), 0, 0, 'L');
            $pdf::Cell(73, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Membrana de dializador: " . $hc->txtMembranaDializador)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Condición clínica del paciente al finalizar hemodiálisis: ")), 0, 0, 'L');
            $pdf::Cell(73, 5, "", 0, 0, 'L');
            $pdf::MultiCell(92, 5, utf8_decode(utf8_encode($hc->txtCondicionClinicaFinal)), 0, '', 0, 0, '', '', false);

            $pdf::Ln();
            $pdf::Cell(30, 4, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("...................................")), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(30, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Firma y sello")), 0, 0, 'C');

            $pdf::Ln(5);
            $pdf::Cell(3, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("II. Parte de atención de enfermería")), '', 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("P.A. inicial: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtPAInicial)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("N° de puesto: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtNPuesto)), 0, 0, 'L');

            //txtAdmiMedic

            $medicamentos = explode('&iliu&', $hc->txtAdmiMedic);

            $pdf::Cell(30, 5, "", 0, 0, 'L');
            $tbl = '
                <table width="100%" style="font-size:7px;" cellpadding="1" border="0">
                    <tr>
                        <td width="100%" rowspan="2">
                            <table width="100%" cellpadding="2" border="1">
                                <tr>
                                    <td colspan="2">Administración de medicamentos endovenosos</td>
                                </tr>
                                <tr>
                                    <td width="70%" style="font-weight:bold;">Presentación</td>
                                    <td width="30%" style="font-weight:bold;">Cantidad</td>
                                </tr>';

            foreach ($medicamentos as $medicamento) {
                $cadenamedicamento = explode('&ilid&', $medicamento);
                $nommedicamento    = '';
                $prod              = Producto::find($cadenamedicamento[0]);
                if ($prod !== null) {
                    $nommedicamento = $prod->nombre;
                }
                $tbl .= '<tr>
                            <td align="left" style="font-size:6px;">' . $nommedicamento . '</td>
                            <td align="right" style="font-size:6px;">' . $cadenamedicamento[1] . '</td>
                        </tr>';
            }

            $tbl .= '</table>
                        </td>
                        <td width="50%"></td>
                    </tr>
                </table>';

            $pdf::writeHTML($tbl, false, false, false, false, 'C');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("P.A. final: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtNPuesto)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("N° de máquina: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtNMAquina)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso inicial: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtPesoInicial2)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Marca/modelo de máquina: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtMarcaModeloMaquina)) . '/' . utf8_decode(utf8_encode($hc->txtMarcaModeloMaquina2)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso final: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtPesoFinal2)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Área/membrana de filtro: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtAreaMembranaFiltro)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Ultrafiltrado programado: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtUltrafiltadoProgramado)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Lote y serie de filtro: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtLoteSerieFiltro)) . '/' . utf8_decode(utf8_encode($hc->txtLoteSerieFiltro2)), 0, 0, 'L');

            //txtAccesoVascularArterial
            //txtAccesoVascularVenoso

            $pdf::Ln();
            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Acceso Vascular:")), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Arterial:")), 0, 0, 'L');
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("FAV.")), ($hc->txtAccesoVascularArterial == 1 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("AUTINJ.")), ($hc->txtAccesoVascularArterial == 2 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("INJ.")), ($hc->txtAccesoVascularArterial == 3 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCP")), ($hc->txtAccesoVascularArterial == 4 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCT")), ($hc->txtAccesoVascularArterial == 5 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("VP.")), ($hc->txtAccesoVascularArterial == 6 ? 1 : 0), 0, 'L');

            $pdf::Ln();
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Venoso:")), 0, 0, 'L');
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("FAV.")), ($hc->txtAccesoVascularVenoso == 1 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("AUTINJ.")), ($hc->txtAccesoVascularVenoso == 2 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("INJ.")), ($hc->txtAccesoVascularVenoso == 3 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCP.")), ($hc->txtAccesoVascularVenoso == 4 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCT.")), ($hc->txtAccesoVascularVenoso == 5 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("VP.")), ($hc->txtAccesoVascularVenoso == 6 ? 1 : 0), 0, 'L');

            $pdf::Ln();
            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Valoración de enfermería: ")), 0, 0, 'L');
            $pdf::Cell(32, 5, "", 0, 0, 'L');
            $pdf::MultiCell(159, 5, utf8_decode(utf8_encode($hc->txtValoracionEnfermeria)), 0, '', 0, 0, '', '', false);

            $pdf::Ln(5);
            $pdf::Ln(5);
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Evolución del tratamiento de hemodiálisis")), 0, 0, 'L');

            //txtEvalHemodialisis

            $evaluacion = explode('&iliu&', $hc->txtEvalHemodialisis);

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $tbl = '
                <table width="100%" style="font-size:7px;" cellpadding="2" border="1">
                    <thead>
                        <tr>
                            <td width="7%">HORA</td>
                            <td width="7%">P.A.</td>
                            <td width="7%">PULSO</td>
                            <td width="7%">Qb</td>
                            <td width="7%">CND</td>
                            <td width="7%">R.A.</td>
                            <td width="7%">R.V.</td>
                            <td width="7%">PTM</td>
                            <td width="22%">SOL./HEMODERIVADOS</td>
                            <td width="22%">OBSERVACIONES</td>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($evaluacion as $fila) {
                $cadenaevaluacion = explode('&ilid&', $fila);
                $tbl .= '<tr>
                            <td width="7%">' . htmlentities($cadenaevaluacion[0]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[1]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[2]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[3]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[4]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[5]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[6]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[7]) . '</td>
                            <td width="22%">' . htmlentities($cadenaevaluacion[8]) . '</td>
                            <td width="22%">' . htmlentities($cadenaevaluacion[9]) . '</td>
                        </tr>';
            }

            $tbl .= '</tbody>
                </table>';
            $pdf::writeHTML($tbl, false, false, false, false, 'C');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Observación final: ")), 0, 0, 'L');
            $pdf::Cell(20, 5, "", 0, 0, 'L');
            $pdf::MultiCell(159, 4, utf8_decode(utf8_encode($hc->txtObservacionFinal)), 0, '', 0, 0, '', '', false);

            $pdf::Ln(5);
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Aspecto de filtro: ")), 0, 0, 'L');
            $pdf::Cell(19, 5, "", 0, 0, 'L');
            $pdf::MultiCell(159, 4, utf8_decode(utf8_encode($hc->txtAspectoFiltro)), 0, '', 0, 0, '', '', false);

            $pdf::Ln();
            $pdf::Ln(5);
            $pdf::Cell(30, 4, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("__________________")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("__________________")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("__________________")), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(30, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Lic. En enfermería")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Paciente")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Lic. En enfermería")), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(30, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Inicia tratamiento")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Finaliza tratamiento")), 0, 0, 'C');

            $pdf::SetAutoPageBreak(true, 0);
            $pdf::Output('Historia.pdf');
        }
    }

    public function pdfHistoriaClinica2(Request $request)
    {
        date_default_timezone_set('America/Lima');

        //$hc = HistoriaClinica::where('id', '=', $request->id)->where('fecha_atencion', '>=', date('Y-m-d'))->where('estado', '=', 'F')->orderBy('fecha_atencion', 'ASC')->first();

        if ($hc === null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $pdf = new TCPDF();
            // set margins
            $pdf::SetMargins(10, 10, 10);

            // set auto page breaks
            $pdf::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

            $pdf::SetTitle('HistoriaClinica');
            $pdf::AddPage();
            $pdf::Image("dist/img/logo2-nefrocix.jpg", 10, 7, 50, 25);
            $pdf::SetFont('helvetica', 'B', 15);
            $pdf::Cell(60, 3, "", 0, 0, 'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica', 'B', 14);
            $pdf::Cell(48, 10, "", 0, 0, 'C');
            $pdf::Cell(145, 0, utf8_decode(utf8_encode("FORMATO DE PROCEDIMIENTO DE HEMODIÁLISIS")), '', 0, 'C');
            $pdf::Ln();
            $pdf::Ln();
            $pdf::Ln(3);
            $pdf::Cell(1, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 5, utf8_decode(utf8_encode("Apellidos y Nombres: " . $hc->historia->persona->apellidopaterno . ' ' . $hc->historia->persona->apellidomaterno . ' ' . $hc->historia->persona->nombres)), '', 0, 'L');
            $pdf::Cell(70, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Fecha: " . date('d/m/Y H:i', strtotime($hc->fecha_atencion)))), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(1, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 5, utf8_decode(utf8_encode("N° de SIS: " . $hc->historia->carnet)), '', 0, 'L');
            $pdf::Cell(36, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("N° de historia clínica: " . $hc->historia->numero)), 0, 0, 'C');

            //Calculo veces por semana (frecuencia)
            $ordencitas         = explode(';', $hc->historia->ordencitas);
            $ordencitasopcional = explode(';', $hc->historia->ordencitasopcional);
            $frecuencia         = count($ordencitas) - count($ordencitasopcional);
            //Calculo N Hemodialisis mes
            $fecha  = date('Y-m-d');
            $fecha0 = explode('-', $fecha);
            $fechai = $fecha0[0] . '-' . $fecha0[1] . '-';
            $a      = 0;
            for ($i = 1; $i <= $fecha0[2]; $i++) {
                $fechaf = $fechai . str_pad($i, 2, "0", STR_PAD_LEFT);
                $hc2    = HistoriaClinica::where('historia_id', '=', $hc->historia->id)->where('fecha_atencion', 'LIKE', $fechaf . '%')->where('estado', '=', 'F')->first();
                if ($hc2 !== null) {
                    $a++;
                }
            }
            $numsesion = $a;
            $pdf::Ln();
            $pdf::Cell(1, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 5, utf8_decode(utf8_encode("N° de sesión de hemodiálisis del mes: " . $numsesion)), '', 0, 'L');
            $pdf::Cell(36, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Frecuencia (veces/semana): " . $frecuencia)), 0, 0, 'C');
            $pdf::Cell(25, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Turno: " . $hc->turno)), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(3, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("I. Parte de atención médica")), '', 0, 'L');
            $pdf::Cell(61, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 8, utf8_decode(utf8_encode("Evaluación Previa: ")), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(18, 5, utf8_decode(utf8_encode("Evolución: ")), '', 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            //$pdf::Cell(52,5,utf8_decode(utf8_encode("Signos y Síntomas: ")),'',0,'L');

            //cadenacies
            $cies       = explode(';', $hc->txtCies);
            $cadenacies = '';
            for ($ss = 0; $ss < count($cies) - 1; $ss++) {
                $cie = Cie::find($cies[$ss]);
                $cadenacies .= $cie->codigo . ': ' . $cie->descripcion . "\n";
            }

            $pdf::MultiCell(120, 5, utf8_decode(utf8_encode($cadenacies)), 0, '', 0, 0, '', '', false);
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("Funciones vitales:")), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(18, 5, utf8_decode(utf8_encode("Signos y Síntomas: ")), '', 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtEvoSigSin)), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(6, 5, "", 0, 0, 'C');
            $pdf::Cell(73, 5, utf8_decode(utf8_encode("")), '', 0, 'L');
            $pdf::Cell(61, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("PA:")), 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPA)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(6, 5, "", 0, 0, 'C');
            $pdf::Cell(73, 5, utf8_decode(utf8_encode("")), '', 0, 'L');
            $pdf::Cell(61, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("FC:")), 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtFC)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(6, 5, "", 0, 0, 'C');
            $pdf::Cell(73, 5, utf8_decode(utf8_encode("")), '', 0, 'L');
            $pdf::Cell(61, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode("FR:")), 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtFR)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Ln();
            $pdf::Cell(5, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("Prescripción para máquina de Hemodiálisis:")), '', 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Horas de hemodiálisis: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtHorasHemodialisis)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso Inicial: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPesoInicial)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Qb: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtQb)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Na inicial: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtNaInicial)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Dosis de heparina: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtDosisHepa)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso Final: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPesoFinal)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Qd: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtQd)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Na final: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtNaFinal)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso seco: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtPesoSeco)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Perfil de UF: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtPerfilUF)), 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Buffer: Bicarbonato")), 0, 0, 'L');
            $pdf::Cell(35, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Perfin de Na.: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtBufer)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Ultrafiltrado a programar: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(28, 5, utf8_decode(utf8_encode($hc->txtUltrafiltrado)), 0, 0, 'L');
            $pdf::Cell(42, 5, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Conductividad: ")), 0, 0, 'L');
            $pdf::Cell(15, 5, "", 0, 0, 'L');
            $pdf::Cell(20, 5, utf8_decode(utf8_encode($hc->txtConductividad)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("Prescripción para dializador:")), '', 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Área de dializador: " . $hc->txtAreaDializador)), 0, 0, 'L');
            $pdf::Cell(73, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Membrana de dializador: " . $hc->txtMembranaDializador)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Condición clínica del paciente al finalizar hemodiálisis: ")), 0, 0, 'L');
            $pdf::Cell(73, 5, "", 0, 0, 'L');
            $pdf::MultiCell(92, 5, utf8_decode(utf8_encode($hc->txtCondicionClinicaFinal)), 0, '', 0, 0, '', '', false);

            $pdf::Ln(5);
            $pdf::Ln(5);
            $pdf::Cell(3, 8, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', 'B', 8);
            $pdf::Cell(65, 8, utf8_decode(utf8_encode("II. Parte de atención de enfermería")), '', 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("P.A. inicial: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtPAInicial)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("N° de puesto: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtNPuesto)), 0, 0, 'L');

            //txtAdmiMedic

            $medicamentos = explode('&iliu&', $hc->txtAdmiMedic);

            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $tbl = '
                <table width="100%" style="font-size:7px;" cellpadding="1" border="0">
                    <tr>
                        <td width="100%" rowspan="2">
                            <table width="100%" cellpadding="2" border="1">
                                <tr>
                                    <td colspan="2">Administración de medicamentos endovenosos</td>
                                </tr>
                                <tr>
                                    <td width="70%" style="font-weight:bold;">Presentación</td>
                                    <td width="30%" style="font-weight:bold;">Cantidad</td>
                                </tr>';

            foreach ($medicamentos as $medicamento) {
                $cadenamedicamento = explode('&ilid&', $medicamento);
                $tbl .= '<tr>
                            <td align="left">' . htmlentities($cadenamedicamento[0]) . '</td>
                            <td align="right">' . htmlentities($cadenamedicamento[1]) . '</td>
                        </tr>';
            }

            $tbl .= '</table>
                        </td>
                        <td width="50%"></td>
                    </tr>
                </table>';

            $pdf::writeHTML($tbl, false, false, false, false, 'C');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("P.A. final: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtNPuesto)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("N° de máquina: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtNMAquina)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso inicial: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtPesoInicial2)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Marca/modelo de máquina: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtMarcaModeloMaquina)) . '/' . utf8_decode(utf8_encode($hc->txtMarcaModeloMaquina2)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Peso final: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtPesoFinal2)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Área/membrana de filtro: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtAreaMembranaFiltro)), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Ultrafiltrado programado: ")), 0, 0, 'L');
            $pdf::Cell(27, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtUltrafiltadoProgramado)), 0, 0, 'L');
            $pdf::Cell(13, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Lote y serie de filtro: ")), 0, 0, 'L');
            $pdf::Cell(29, 5, "", 0, 0, 'L');
            $pdf::Cell(5, 5, utf8_decode(utf8_encode($hc->txtLoteSerieFiltro)), 0, 0, 'L');

            //txtAccesoVascularArterial
            //txtAccesoVascularVenoso

            $pdf::Ln();
            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Acceso Vascular:")), 0, 0, 'L');

            $pdf::Ln();
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Arterial:")), 0, 0, 'L');
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("FAV.")), ($hc->txtAccesoVascularArterial == 1 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("INJ.")), ($hc->txtAccesoVascularArterial == 2 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCT.")), ($hc->txtAccesoVascularArterial == 3 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCP.")), ($hc->txtAccesoVascularArterial == 4 ? 1 : 0), 0, 'L');

            $pdf::Ln();
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Venoso:")), 0, 0, 'L');
            $pdf::Cell(10, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("FAV.")), ($hc->txtAccesoVascularVenoso == 1 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("INJ.")), ($hc->txtAccesoVascularVenoso == 2 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCT.")), ($hc->txtAccesoVascularVenoso == 3 ? 1 : 0), 0, 'L');
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(12, 5, utf8_decode(utf8_encode("CVCP.")), ($hc->txtAccesoVascularVenoso == 4 ? 1 : 0), 0, 'L');

            $pdf::Ln();
            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Valoración de enfermería: ")), 0, 0, 'L');
            $pdf::Cell(32, 5, "", 0, 0, 'L');
            $pdf::MultiCell(159, 5, utf8_decode(utf8_encode($hc->txtValoracionEnfermeria)), 0, '', 0, 0, '', '', false);

            $pdf::Ln(5);
            $pdf::Ln(5);
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Evolución del tratamiento de hemodiálisis")), 0, 0, 'L');

            //txtEvalHemodialisis

            $evaluacion = explode('&iliu&', $hc->txtEvalHemodialisis);

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $tbl = '
                <table width="100%" style="font-size:7px;" cellpadding="2" border="1">
                    <thead>
                        <tr>
                            <td width="7%">HORA</td>
                            <td width="7%">P.A.</td>
                            <td width="7%">PULSO</td>
                            <td width="7%">Qb</td>
                            <td width="7%">CND</td>
                            <td width="7%">R.A.</td>
                            <td width="7%">R.V.</td>
                            <td width="7%">PTM</td>
                            <td width="22%">SOL./HEMODERIVADOS</td>
                            <td width="22%">OBSERVACIONES</td>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($evaluacion as $fila) {
                $cadenaevaluacion = explode('&ilid&', $fila);
                $tbl .= '<tr>
                            <td width="7%">' . htmlentities($cadenaevaluacion[0]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[1]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[2]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[3]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[4]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[5]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[6]) . '</td>
                            <td width="7%">' . htmlentities($cadenaevaluacion[7]) . '</td>
                            <td width="22%">' . htmlentities($cadenaevaluacion[8]) . '</td>
                            <td width="22%">' . htmlentities($cadenaevaluacion[9]) . '</td>
                        </tr>';
            }

            $tbl .= '</tbody>
                </table>';
            $pdf::writeHTML($tbl, false, false, false, false, 'C');

            $pdf::Ln();
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Observación final: ")), 0, 0, 'L');
            $pdf::Cell(20, 5, "", 0, 0, 'L');
            $pdf::MultiCell(159, 4, utf8_decode(utf8_encode($hc->txtObservacionFinal)), 0, '', 0, 0, '', '', false);

            $pdf::Ln(5);
            $pdf::Ln(5);
            $pdf::Cell(5, 5, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 5, utf8_decode(utf8_encode("Aspecto de filtro: ")), 0, 0, 'L');
            $pdf::Cell(19, 5, "", 0, 0, 'L');
            $pdf::MultiCell(159, 4, utf8_decode(utf8_encode($hc->txtAspectoFiltro)), 0, '', 0, 0, '', '', false);

            $pdf::Ln();
            $pdf::Ln(5);
            $pdf::Ln(5);
            $pdf::Cell(30, 4, "", 0, 0, 'L');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("__________________")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("__________________")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("__________________")), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(30, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Lic. En enfermería")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Paciente")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Lic. En enfermería")), 0, 0, 'C');

            $pdf::Ln();
            $pdf::Cell(30, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Inicia tratamiento")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("")), 0, 0, 'C');
            $pdf::Cell(55, 4, "", 0, 0, 'C');
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(7, 4, utf8_decode(utf8_encode("Finaliza tratamiento")), 0, 0, 'C');

            $pdf::SetAutoPageBreak(true, 0);
            $pdf::Output('Historia.pdf');
        }
    }

    public function reporteformato(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $formatomensual = $request->input('formatomensual');
        $formatotipo    = $request->input('formatotipo');

        $id = $request->input('id');

        $cadenaresultados          = "";
        $especialidadformato       = "";
        $numeroespecialidadformato = "";

        $codi                 = '';
        $descrip              = '';
        $history              = null;
        $medicamentos         = '';
        $filasmedicamentillos = '';
        $fechona              = "..-..-..";

        for ($i = 1; $i <= 21; $i++) {
            $cadenaresultados .= '<tr>
                                    <td width="8%" style="font-size:6px"></td>
                                    <td width="26%" style="font-size:6px"></td>
                                    <td width="8%" style="font-size:6px"></td>
                                    <td width="8%" style="font-size:6px"></td>
                                    <td width="8%" style="font-size:6px"></td>
                                    <td width="26%" style="font-size:6px"></td>
                                    <td width="8%" style="font-size:6px"></td>
                                    <td width="8%" style="font-size:6px"></td>
                                </tr>';
        }

        if ($formatomensual === '2') {
            $hc                        = HistoriaClinica::find($id);
            $fechona                   = $hc->fecha_atencion;

            // Configurando nuevos parámetos para examenesGenerales

            $examenesGeneral = null;
            $extra_code = '';
            $extra_name = '';

            if(strtotime($fechona) >= strtotime('2021-08-03')) {
                $examenesGeneral = $this->examenesGeneral_new;
                // El parámetro de albulina
                $extra_code = '82040';
                $extra_name = $examenesGeneral['82040'];
            } else {
                $examenesGeneral = $this->examenesGeneral_old;
                // El parámetro de proteina
                $extra_code = '84165';
                $extra_name = $examenesGeneral['84165'];
            }

            $codi                      = '90937';
            $especialidadformato       = "NEFROLOGIA";
            $numeroespecialidadformato = "1";
            $descrip                   = 'Procedimiento de hemodiálisis que requiere repetida(s) evaluación(es) con o sin una revisión médica substancial de la prescripción de la diálisis';
            $history                   = $hc->historia;

            //Creo los medicamentos

            $medicamentillos = explode('&iliu&', $hc->txtAdmiMedic);

            $rs = 0;

            foreach ($medicamentillos as $medicamentillo) {

                $codmedicamentoo = "";
                $nommedicamentoo = "";

                $medicamentillo2         = explode('&ilid&', $medicamentillo);
                $cantidadmedicamentillo2 = "";

                if (count($medicamentillo2) == 2) {
                    $prod = Producto::find($medicamentillo2[0]);
                    if ($prod !== null && $medicamentillo2[1] !== "0") {
                        $codmedicamentoo         = $prod->codigobarra;
                        $nommedicamentoo         = $prod->nombre;
                        $cantidadmedicamentillo2 = $medicamentillo2[1];
                    }
                    $filasmedicamentillos .= '<tr>
                                            <td width="8%" style="font-size:6px">' . htmlentities($codmedicamentoo) . '</td>
                                            <td width="26%" style="font-size:6px; text-align:left;">' . htmlentities($nommedicamentoo) . '</td>
                                            <td width="8%" style="font-size:6px">' . htmlentities($cantidadmedicamentillo2) . '</td>
                                            <td width="8%" style="font-size:6px">' . htmlentities($cantidadmedicamentillo2) . '</td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="26%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                        </tr>';
                }

                $rs++;
            }

            for ($i = $rs; $i <= 21; $i++) {
                $filasmedicamentillos .= '<tr>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="26%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="26%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                        </tr>';
            }

            $cadenaresultados = "";
            $trcodigo         = "";
            $cantresultados   = 1;

            if ($hc->mensuales2 !== "2") {
                switch ($hc->mensuales) {
                    // NUEVO
                    case "1":

                        $cantresultados = 7;

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86703</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86703'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">87340</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['87340'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86706</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86706'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86704</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86704'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86803</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86803'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86592</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86592'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        break;

                    // NUEVO + MENSUALES
                    case "2":

                        $cantresultados = 12;

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84520</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84520'] . '</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82565</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82565'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85014</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85014'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85018</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85018'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">80051</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['80051'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';                        

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84100</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84100'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82310</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82310'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86703</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86703'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">87340</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['87340'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86706</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86706'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86704</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86704'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86803</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86803'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        break;

                    // MENSUALES
                    case "3":

                        $cantresultados = 7;

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84520</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84520'] . '</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82565</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82565'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85014</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85014'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85018</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85018'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">80051</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['80051'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84100</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84100'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82310</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82310'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        break;

                    // MENSUALES + BIMENSUALES
                    case "4":

                        $cantresultados = 9;

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84520</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84520'] . '</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82565</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82565'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85014</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85014'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85018</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85018'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">80051</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['80051'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84100</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84100'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82310</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82310'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84450</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84450'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84460</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84460'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        break;

                    // MENSUALES + TRIMESTRALES
                    case "5":

                        //$cantresultados = 11;
                        $cantresultados = 9;

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84520</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84520'] . '</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82565</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82565'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85014</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85014'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85018</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85018'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">80051</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['80051'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84100</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84100'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82310</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82310'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        /*$cadenaresultados.='<tr>
                        <td width="8%" style="font-size:6px">84450</td>
                        <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84450'] . '</td>
                        <td width="8%" style="font-size:6px">1</td>
                        <td width="8%" style="font-size:6px">1</td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="26%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados.='<tr>
                        <td width="8%" style="font-size:6px">84460</td>
                        <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84460'] . '</td>
                        <td width="8%" style="font-size:6px">1</td>
                        <td width="8%" style="font-size:6px">1</td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="26%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        </tr>';*/

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">' . $extra_code . '</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $extra_name . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84075</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84075'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        break;

                    // MENSUALES + TRIMESTRALES + BIMENSUALES + SEMESTRAL
                    case "6":

                        $cantresultados = 21;
                        //$cantresultados = 17;

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84520</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84520'] . '</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px">2</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82565</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82565'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85014</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85014'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">85018</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85018'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">80051</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['80051'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84100</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84100'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82310</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82310'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">' . $extra_code . '</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $extra_name . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84075</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84075'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84450</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84450'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84460</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84460'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';                        

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86703</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86703'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86592</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86592'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">83970</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['83970'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">87340</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['87340'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86706</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86706'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86704</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86704'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">86803</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86803'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">83540</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['83540'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">82728</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82728'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';
                        $cadenaresultados .= '<tr>
                            <td width="8%" style="font-size:6px">84466</td>
                            <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84466'] . '</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px">1</td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="26%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                            <td width="8%" style="font-size:6px"></td>
                        </tr>';

                        break;

                        /*case "1":

                $cantresultados = 21;
                //$cantresultados = 17;

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">84520</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84520'] . '</td>
                <td width="8%" style="font-size:6px">2</td>
                <td width="8%" style="font-size:6px">2</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">82565</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82565'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">85014</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85014'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">85018</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['85018'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">80051</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['80051'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">84100</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84100'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">82310</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82310'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">' . $extra_code . '</td>
                <td width="26%" style="font-size:6px;" align="left">' . $extra_name . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">84075</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84075'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">84450</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84450'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">84460</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84460'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">86703</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86703'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">86592</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86592'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">83970</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['83970'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">87340</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['87340'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">86706</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86706'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">86704</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86704'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">86803</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['86803'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">83540</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['83540'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">82728</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['82728'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';
                $cadenaresultados.='<tr>
                <td width="8%" style="font-size:6px">84466</td>
                <td width="26%" style="font-size:6px;" align="left">' . $examenesGeneral['84466'] . '</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px">1</td>
                <td width="8%" style="font-size:6px"></td>
                <td width="26%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                <td width="8%" style="font-size:6px"></td>
                </tr>';

                break;
                 */
                }
            }

            for ($i = $cantresultados; $i <= 21; $i++) {
                $cadenaresultados .= '<tr>
                    <td width="8%" style="font-size:6px"></td>
                    <td width="26%" style="font-size:6px"></td>
                    <td width="8%" style="font-size:6px"></td>
                    <td width="8%" style="font-size:6px"></td>
                    <td width="8%" style="font-size:6px"></td>
                    <td width="26%" style="font-size:6px"></td>
                    <td width="8%" style="font-size:6px"></td>
                    <td width="8%" style="font-size:6px"></td>
                </tr>';
            }

        } else if ($formatomensual === '1') {
            $formato = $formatotipo;
            if ($formato == '1') {
                $especialidadformato       = "PSICÓLOGO";
                $numeroespecialidadformato = "8";
                $hc                        = ConsultaSaludMental::find($id);
                $codi                      = '99207';
                $descrip                   = 'Atención en salud mental';

                //Creo los medicamentos

                for ($i = 1; $i <= 21; $i++) {
                    $filasmedicamentillos .= '<tr>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="26%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="26%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                            <td width="8%" style="font-size:6px"></td>
                                        </tr>';
                }

            } else if ($formato == '2') {
                $hc                        = ConsultaNefrologica::find($id);
                $especialidadformato       = "NEFROLOGIA";
                $numeroespecialidadformato = "1";
                $codi                      = '99215';
                $descrip                   = 'Consulta ambulatoria especializada para la evaluación y manejo de un paciente continuador';
                $cadenaresultados          = "";

                //Creo los medicamentos

                $cantmendicamentos = 0;

                for ($ii = 2; $ii <= 18; $ii++) {
                    $numM = $ii;
                    if($ii == 10) {
                        $numM = 91;
                    }
                    if($ii > 10) {
                        $numM--;
                    }
                    // Quitamos CALCITRIOL 1 MCG/ML INY id = 11
                    if ($hc["c" . $numM] !== null && $hc["c" . $numM] !== "" && $hc["c" . $numM] !== "0" && $hc["m" . $numM] !== 1 && $hc["m" . $numM] !== 2 && $hc["m" . $numM] !== 3 && $hc["m" . $numM] !== 11) {
                        $prod = Producto::find($hc["m" . $numM]);
                        if ($prod !== null && $hc["f" . $numM] !== "0") {
                            $codmedicamentoo         = $prod->codigobarra;
                            $nommedicamentoo         = $prod->nombre;
                            $cantidadmedicamentillo2 = $hc["c" . $numM];
                        }
                        $filasmedicamentillos .= '<tr>
                            <td width="8%" style="font-size:6px;">' . htmlentities($codmedicamentoo) . '</td>
                            <td width="26%" style="font-size:6px;" align="left">' . htmlentities($nommedicamentoo) . '</td>
                            <td width="8%" style="font-size:6px;">' . htmlentities($cantidadmedicamentillo2) . '</td>
                            <td width="8%" style="font-size:6px;">' . htmlentities($cantidadmedicamentillo2) . '</td>
                            <td width="8%" style="font-size:6px;"></td>
                            <td width="26%" style="font-size:6px;"></td>
                            <td width="8%" style="font-size:6px;"></td>
                            <td width="8%" style="font-size:6px;"></td>
                        </tr>';
                        $cantmendicamentos++;
                    }
                }

                for ($i = $cantmendicamentos; $i <= 21; $i++) {
                    $filasmedicamentillos .= '<tr>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="26%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="26%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                            </tr>';
                }

                //ARMANDO LISTA DE EXAMENES

                $todosresultados = explode(";", $hc->cadenaresultados);
                $cantresultados  = 0;

                if (count($todosresultados) > 0) {
                    foreach ($todosresultados as $tresul) {
                        $todosresultadosi = explode(",", $tresul);

                        $trid = $todosresultadosi[0];
                        //$trvalor = $todosresultadosi[1];
                        $nnumero = "1";

                        $trcodigo = "";
                        $trnombre = "";

                        /*switch ($trid) {
                            case '1':
                                $trcodigo = "86703";
                                $trnombre = "ELISA o prueba rápida para HIV-1 y HIV-2";
                                $cantresultados++;
                                break;
                            case '2':
                                $trcodigo = "87340";
                                $trnombre = "Detección de antígeno de superficie de virus de Hepatitis B (HBsAg) por ELISA";
                                $cantresultados++;
                                break;
                            case '3':
                                $trcodigo = "86706";
                                $trnombre = "Detección de anticuerpos para antígeno de superficie Hepatitis B (HBs-Ag)";
                                $cantresultados++;
                                break;
                            case '4':
                                $trcodigo = "86704";
                                $trnombre = "Detección de anticuerpos totales para núcleo de virus de Hepatitis B (Total Anti-Hbcore)";
                                $cantresultados++;
                                break;
                            case '5':
                                $trcodigo = "86803";
                                $trnombre = "Determinación de anticuerpos para Hepatitis C";
                                $cantresultados++;
                                break;
                            case '6':
                                $trcodigo = "84520";
                                $trnombre = "Úrea";
                                $nnumero = "1";
                                $cantresultados++;
                                break;
                            case '8':
                                $trcodigo = "82565";
                                $trnombre = "Creatinina en sangre";
                                $cantresultados++;
                                break;
                            case '9':
                                $trcodigo = "85014";
                                $trnombre = "Hematocrito";
                                $cantresultados++;
                                break;
                            case '10':
                                $trcodigo = "85018";
                                $trnombre = "Dosaje de Hemoglobina";
                                $cantresultados++;
                                break;
                            case '12':
                                $trcodigo = "80051";
                                $trnombre = "Electrolitos séricos";
                                $cantresultados++;
                                break;
                            case '15':
                                $trcodigo = "84100";
                                $trnombre = "Fósforo en sangre";
                                $cantresultados++;
                                break;
                            case '16':
                                $trcodigo = "82310";
                                $trnombre = "Calcio sérico";
                                $cantresultados++;
                                break;
                            case '17':
                                $trcodigo = "84165";
                                $trnombre = "Proteínas; fraccionamiento y determinación cuantitativa por electroforesis";
                                $cantresultados++;
                                break;
                            case '18':
                                $trcodigo = "84075";
                                $trnombre = "Fosfatasa Alcalina";
                                $cantresultados++;
                                break;
                            case '19':
                                $trcodigo = "84450";
                                $trnombre = "TGO transaminasa glutámico oxalacética";
                                $cantresultados++;
                                break;
                            case '20':
                                $trcodigo = "84460";
                                $trnombre = "TGP transaminasa glutámico pirúvica";
                                $cantresultados++;
                                break;
                            case '21':
                                $trcodigo = "86592";
                                $trnombre = "Prueba de sífilis cualitativa (VDRL, RPR)";
                                $cantresultados++;
                                break;
                            case '22':
                                $trcodigo = "83970";
                                $trnombre = "Paratohormona (PTH)";
                                $cantresultados++;
                                break;
                            case '23':
                                $trcodigo = "83540";
                                $trnombre = "Hierro sérico";
                                $cantresultados++;
                                break;
                            case '24':
                                $trcodigo = "82728";
                                $trnombre = "Ferritina";
                                $cantresultados++;
                                break;
                            case '25':
                                $trcodigo = "84466";
                                $trnombre = "Saturación de transferrina";
                                $cantresultados++;
                                break;
                        }*/

                        if ($trcodigo !== "") {
                            $cadenaresultados .= '<tr>
                                <td width="8%" style="font-size:6px">' . htmlentities($trcodigo) . '</td>
                                <td width="26%" style="font-size:6px;" align="left">' . htmlentities($trnombre) . '</td>
                                <td width="8%" style="font-size:6px">' . htmlentities($nnumero) . '</td>
                                <td width="8%" style="font-size:6px">' . htmlentities($nnumero) . '</td>
                                <td width="8%" style="font-size:6px"></td>
                                <td width="26%" style="font-size:6px"></td>
                                <td width="8%" style="font-size:6px"></td>
                                <td width="8%" style="font-size:6px"></td>
                            </tr>';
                            $cantresultados++;
                        }
                    }
                }

                for ($i = $cantresultados; $i <= 21; $i++) {
                    $cadenaresultados .= '<tr>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="26%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="26%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                        <td width="8%" style="font-size:6px"></td>
                    </tr>';
                }

            } else if ($formato == '3') {
                $especialidadformato       = "TRABAJADORA SOCIAL";
                $numeroespecialidadformato = "7";
                $hc                        = ConsultaServicioSocial::find($id);
                $codi                      = '99210';
                $descrip                   = 'Atención en servicio social';

                //Creo los medicamentos

                for ($i = 1; $i <= 21; $i++) {
                    $filasmedicamentillos .= '<tr>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="26%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="26%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                            </tr>';
                }

            } else if ($formato == '4') {
                $especialidadformato       = "NUTRICIONISTA";
                $numeroespecialidadformato = "10";
                $hc                        = ConsultaNutricion::find($id);
                $codi                      = '99209';
                $descrip                   = 'Atención en nutrición';

                //Creo los medicamentos

                for ($i = 1; $i <= 21; $i++) {
                    $filasmedicamentillos .= '<tr>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="26%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="26%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                            </tr>';
                }
            }
            $fechona = $hc->fecha_atencion;
            $history = Historia::where('person_id', '=', $hc->persona_id)->first();
        }

        if ($hc === null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {

            $pdf = new TCPDF();
            // set margins
            $pdf::SetMargins(6, 6, 6);

            $pdf::SetTitle('FormatoAtencion');
            $pdf::AddPage();

            $pdf::SetFont('times', '', 7);

            $cicies = '';

            $chies = explode(";", $hc->ciesformato);
            $da    = 1;

            $img1 = '<img src="dist/img/pmarcada.png" width="10px" height="10px">';
            $img2 = '<img src="dist/img/dmarcada.png" width="10px" height="10px">';
            $img3 = '<img src="dist/img/rmarcada.png" width="10px" height="10px">';

            foreach ($chies as $ches) {
                $chies2  = explode(',', $ches);
                $ciecito = Cie::find($chies2[0]);
                if ($ciecito !== null) {
                    $cicies .= '<tr>
                        <td style="font-size:7px">' . $da . '</td>
                        <td style="font-size:11px">' . strtoupper($ciecito->descripcion) . '</td>
                        <td style="font-size:11px;font-weight:bold;">' . strtoupper($ciecito->codigo) . '</td>
                        <td style="font-size:7px;">' . ($chies2[1] === 'P' ? $img1 : 'P') . '</td>
                        <td style="font-size:7px;">' . ($chies2[1] === 'D' ? $img2 : 'D') . '</td>
                        <td style="font-size:7px;">' . ($chies2[1] === 'R' ? $img3 : 'R') . '</td>
                    </tr>';
                    $da++;
                }
                if ($da === 7) {
                    break;
                }
            }

            if ($da < 7) {
                for ($i = $da; $i <= 5; $i++) {
                    $cicies .= '<tr>
                        <td style="font-size:7px">' . $i . '</td>
                        <td style="font-size:11px"></td>
                        <td style="font-size:11px;font-weight:bold;"></td>
                        <td style="font-size:7px;">P</td>
                        <td style="font-size:7px;">D</td>
                        <td style="font-size:7px;">R</td>
                    </tr>';
                }
            }

            $tbl_hora_nuevo_formato = '';

            if(strtotime($fechona) >= strtotime('2021-08-03')) {
                $tbl_hora_nuevo_formato .= '<td width="10%">
                                        <table width="100%" height="200%" border="1" cellpadding="3">
                                            <tr>
                                                <td colspan="3" style="background-color:#C3BFBD">HORA</td>
                                            </tr>
                                            <tr>
                                                <td width="41%" style="font-size:11px" height="32">' . $fechona[11] .  $fechona[12] . '</td>
                                                <td width="18%" align="center" style="font-size:10px" height="32">' . $fechona[13]. '</td>
                                                <td width="41%" style="font-size:11px" height="32">' . $fechona[14] .  $fechona[15] . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="16%">
                                        <table width="100%" height="200%" border="1" cellpadding="3">
                                            <tr>
                                                <td style="background-color:#C3BFBD">N° HISTORIA CLÍNICA</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px" rowspan="2" height="32">' . $history->numero . '</td>
                                            </tr>
                                        </table>
                                    </td>';
            } else {
                $tbl_hora_nuevo_formato .= '<td width="19%">
                                        <table width="100%" height="200%" border="1" cellpadding="3">
                                            <tr>
                                                <td style="background-color:#C3BFBD">N° HISTORIA CLÍNICA</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px" rowspan="2" height="32">' . $history->numero . '</td>
                                            </tr>
                                        </table>
                                    </td>';
            }

            if(strtotime($fechona) >= strtotime('2021-08-03')) {
                $tbl_hora_nuevo_formato .= '<td width="51%">
                                                <table width="100%" height="100%" border="1" cellpadding="3">
                                                    <tr>
                                                        <td width="55%" colspan="4" style="background-color:#C3BFBD">IDENTIFICACIÓN</td>
                                                        <td width="45%" colspan="2" style="background-color:#C3BFBD">RÉGIMEN</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="22" valign="center" width="8%" style="background-color:#DFDAD9;font-size:5px" rowspan="2">TD</td>
                                                        <td width="8%" style="font-size:11px" rowspan="2">' . $hc->td . '</td>
                                                        <td width="19%" style="background-color:#DFDAD9;font-size:5px" rowspan="2">N° DOCUMENTO</td>
                                                        <td width="20%" style="font-size:' . (strlen($history->persona->dni)<=8?"11":"8") . 'px;vertical-align:baseline" rowspan="2">' . $history->persona->dni . '</td>
                                                        <td align="left" style="background-color:#DFDAD9;font-size:5px">SUBSIDIADO</td>
                                                        <td style="font-size:8px">' . ($history->regimen === 'Subsidiado' ? 'X' : '') . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="background-color:#DFDAD9;font-size:5px">SEMICONTRIBUTIVO</td>
                                                        <td style="font-size:8px">' . ($history->regimen === 'Semicontributivo' ? 'X' : '') . '</td>
                                                    </tr>
                                                </table>
                                            </td>';
            } else {
                $tbl_hora_nuevo_formato .= '<td width="58%">
                                                <table width="100%" height="100%" border="1" cellpadding="3">
                                                    <tr>
                                                        <td width="55%" colspan="4" style="background-color:#C3BFBD">IDENTIFICACIÓN</td>
                                                        <td width="45%" colspan="2" style="background-color:#C3BFBD">RÉGIMEN</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="22" valign="center" width="8%" style="background-color:#DFDAD9;font-size:5px" rowspan="2">TD</td>
                                                        <td width="8%" style="font-size:11px" rowspan="2">' . $hc->td . '</td>
                                                        <td width="19%" style="background-color:#DFDAD9;font-size:5px" rowspan="2">N° DOCUMENTO</td>
                                                        <td width="20%" style="font-size:11px" rowspan="2">' . $history->persona->dni . '</td>
                                                        <td align="left" style="background-color:#DFDAD9;font-size:5px">SUBSIDIADO</td>
                                                        <td style="font-size:8px">' . ($history->regimen === 'Subsidiado' ? 'X' : '') . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="background-color:#DFDAD9;font-size:5px">SEMICONTRIBUTIVO</td>
                                                        <td style="font-size:8px">' . ($history->regimen === 'Semicontributivo' ? 'X' : '') . '</td>
                                                    </tr>
                                                </table>
                                            </td>';
            }

            $tbl = '
                <table width="100%" height="100%" border="1" cellpadding="6">
                    <tr>
                        <td>
                            <table width="100%" height="100%">
                                <tr align="left" cellpadding="4">
                                    <td>
                                        <img src="dist/img/logominsa.png" width="400px" height="45px">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="34%"></td>
                                    <td width="31%">
                                        <br>
                                        <h3>FORMATO DE ATENCIÓN </h3>
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr style="background-color:#C3BFBD">
                                                <td colspan="5">NÚMERO DE FORMATO</td>
                                            </tr>
                                            <tr>
                                                <td width="35%" style="font-weight:bold;font-size:11px">00025388</td>
                                                <td width="8%" style="font-weight:bold;font-size:11px">-</td>
                                                <td width="14%" style="font-weight:bold;font-size:11px">' . $fechona[2] . $fechona[3] . '</td>
                                                <td width="8%" style="font-weight:bold;font-size:11px">-</td>
                                                <td width="35%" style="font-weight:bold;font-size:11px;color:red">' . str_pad($hc->numeroformato, 8, '0', STR_PAD_LEFT) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="23%" style="background-color:#C3BFBD">CÓDIGO IPRESS</td>
                                                <td width="51%" style="background-color:#C3BFBD">NOMBRE DE IPRESS QUE REALIZA LA ATENCIÓN</td>
                                                <td width="23%" style="background-color:#C3BFBD">RECONSIDERACIÓN (*)</td>
                                                <td width="3%">' . ($hc->numeroconsideracion !== '' ? 'X' : '') . '</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px" rowspan="2">00025388</td>
                                                <td style="font-size:11px" rowspan="2">NEFRO CIX SAC</td>
                                                <td colspan="2" style="background-color:#DFDAD9;font-size:5px;">N° FORMATO ATENCIÓN PARA RECONSIDERACIÓN</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">' . ($hc->numeroconsideracion == null || $hc->numeroconsideracion == "" ? '' : str_pad($hc->numeroconsideracion, 8, '0', STR_PAD_LEFT)) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="23%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td style="background-color:#C3BFBD">FECHA DE ATENCIÓN</td>
                                            </tr>
                                            <tr>
                                                <td width="25%" colspan="2" style="background-color:#DFDAD9;font-size:5px">DÍA</td>
                                                <td width="25%" colspan="2" style="background-color:#DFDAD9;font-size:5px">MES</td>
                                                <td width="50%" colspan="4" style="background-color:#DFDAD9;font-size:5px">AÑO</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px" height="18">' . $fechona[8] . '</td>
                                                <td style="font-size:11px">' . $fechona[9] . '</td>
                                                <td style="font-size:11px">' . $fechona[5] . '</td>
                                                <td style="font-size:11px">' . $fechona[6] . '</td>
                                                <td style="font-size:11px">' . $fechona[0] . '</td>
                                                <td style="font-size:11px">' . $fechona[1] . '</td>
                                                <td style="font-size:11px">' . $fechona[2] . '</td>
                                                <td style="font-size:11px">' . $fechona[3] . '</td>
                                            </tr>
                                        </table>
                                    </td>' . $tbl_hora_nuevo_formato . '                                    
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="50%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="100%" style="background-color:#C3BFBD">APELLIDO PATERNO</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px">' . htmlentities($history->persona->apellidopaterno) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="100%" style="background-color:#C3BFBD">APELLIDO MATERNO</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px">' . htmlentities($history->persona->apellidomaterno) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="50%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="100%" style="background-color:#C3BFBD">PRIMER NOMBRE</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px">' . htmlentities(explode(" ", $history->persona->nombres)[0]) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="100%" style="background-color:#C3BFBD">OTROS NOMBRES</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:11px">' . htmlentities(!isset(explode(" ", $history->persona->nombres)[1]) ? '----------------------------------' : explode(" ", $history->persona->nombres)[1]) . htmlentities(!isset(explode(" ", $history->persona->nombres)[2]) ? '' : " " .explode(" ", $history->persona->nombres)[2]) . htmlentities(!isset(explode(" ", $history->persona->nombres)[3]) ? '' : " " .explode(" ", $history->persona->nombres)[3]) . htmlentities(!isset(explode(" ", $history->persona->nombres)[4]) ? '' : " " .explode(" ", $history->persona->nombres)[4]) . htmlentities(!isset(explode(" ", $history->persona->nombres)[5]) ? '' : " " .explode(" ", $history->persona->nombres)[5]) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td colspan="4" width="100%" style="background-color:#C3BFBD">TIPO DE PRESTACIÓN</td>
                                            </tr>
                                            <tr>
                                                <td width="45%" align="left" style="background-color:#DFDAD9;font-size:7px;">Consulta externa</td>
                                                <td width="5%" style="font-size:8px;">' . ($formatomensual === "1" ? 'X' : '') . '</td>
                                                <td width="45%" align="left" style="background-color:#DFDAD9;font-size:7px;">Atención de procedimientos ambulatorios</td>
                                                <td width="5%" style="font-size:8px;">' . ($formatomensual === "2" ? 'X' : '') . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="3%" rowspan="2" style="background-color:#C3BFBD">N°</td>
                                                <td width="97%" colspan="5" style="background-color:#C3BFBD">DIAGNÓSTICOS</td>
                                            </tr>
                                            <tr>
                                                <td width="74%" style="background-color:#DFDAD9;">DESCRIPCIÓN</td>
                                                <td width="15%" style="background-color:#DFDAD9;">CIE - 10</td>
                                                <td width="8%" colspan="3" style="background-color:#DFDAD9;">TIPO Dx</td>
                                            </tr>
                                            ' . $cicies . '
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="25%" style="background-color:#C3BFBD">N° DNI</td>
                                                <td width="52%" style="background-color:#C3BFBD">NOMBRE DEL RESPONSABLE DE LA ATENCIÓN</td>
                                                <td width="23%" style="background-color:#C3BFBD">N° COLEGIATURA</td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="font-size:10px;">' . htmlentities($hc->doctor->dni) . '</td>
                                                <td align="center" style="font-size:10px;">' . htmlentities($hc->doctor->apellidopaterno . ' ' . $hc->doctor->apellidomaterno . ' ' . $hc->doctor->nombres) . '</td>
                                                <td align="center" style="font-size:10px;">' . htmlentities($hc->doctor->cmp) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="30%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="88%" style="background-color:#C3BFBD">RESPONSABLE DE LA ATENCIÓN</td>
                                                <td width="12%" style="font-size:11px;font-weight:bold;">' . $numeroespecialidadformato . '</td>
                                            </tr>
                                            <tr>
                                                <td width="271%" style="font-size:4.5px;background-color:white">1=MÉDICO; 2=FARMACÉUTICO; 3=ODONTÓLOGO; 4=BIÓLOGO; 5=OBSTETRIZ; 6=ENFERMERA; 7=TRABAJADORA SOCIAL; 8=PSICÓLOGO; 9=TÉCNÓLOGO MÉDICO; 10=NUTRICIONISTA; 11=TÉCNICO</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td width="30%" style="background-color:#C3BFBD">ESPECIALIDAD</td>
                                                <td width="70%" style="font-size:11px;font-weight:bold;">' . $especialidadformato . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="20%">
                                        <table width="100%" height="100%" border="1" cellpadding="3">
                                            <tr>
                                                <td height="130"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="80%">
                                        <table width="100%" height="100%" cellpadding="1">
                                            <tr>
                                                <td width="50%" style="font-size:8px;">____________________________________________</td>
                                                <td width="50%" style="font-size:8px;">____________________________________</td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:8px;">Firma y Sello del Responsable de la Atención</td>
                                                <td width="50%" style="font-size:8px;">Firma del asegurado o apoderado</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>';

            $pdf::writeHTML($tbl, true, true, true, true, 'C');

            $pdf::AddPage();

            $tbl = '
                <table width="100%" height="100%" border="1" cellpadding="3">
                    <tr>
                        <td>
                            <table width="100%" height="100%">
                                <tr>
                                    <td width="100%">
                                        <h3>FORMATO DE ATENCIÓN</h3>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" border="1" cellpadding="1">
                                            <tr>
                                                <td colspan="8" style="font-weight:bold;background-color:#6C6A69;color:white;font-size:8px">MEDICAMENTOS</td>
                                            </tr>
                                            <tr>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">CÓDIGO</td>
                                                <td width="26%" style="background-color:#DFDAD9;font-size:5.5px">DESCRIPCIÓN</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C.PRESCRITA</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C.ENTREGADA</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">CÓDIGO</td>
                                                <td width="26%" style="background-color:#DFDAD9;font-size:5.5px">DESCRIPCIÓN</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C.PRESCRITA</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C.ENTREGADA</td>
                                            </tr>' .
            $filasmedicamentillos
            . '</table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" border="1" cellpadding="1">
                                            <tr>
                                                <td colspan="8" style="font-weight:bold;background-color:#6C6A69;color:white;font-size:8px">PROCEDIMIENTOS SESIONES DE HEMODIÁLISIS/EXÁMENES AUXILIARES/CONSULTAS</td>
                                            </tr>
                                            <tr>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">CÓDIGO</td>
                                                <td width="26%" style="background-color:#DFDAD9;font-size:5.5px">DESCRIPCIÓN</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C. INDICADA</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C. EJECUTADA</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">CÓDIGO</td>
                                                <td width="26%" style="background-color:#DFDAD9;font-size:5.5px">DESCRIPCIÓN</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C. INDICADA</td>
                                                <td width="8%" style="background-color:#DFDAD9;font-size:5.5px">C.EJECUTADA</td>
                                            </tr>
                                            <tr>
                                                <td width="8%" style="font-size:6.5px">' . htmlentities($codi) . '</td>
                                                <td width="26%" style="font-size:6.5px;" align="left">' . htmlentities($descrip) . '</td>
                                                <td width="8%" style="font-size:6.5px">1</td>
                                                <td width="8%" style="font-size:6.5px">1</td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="26%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                                <td width="8%" style="font-size:6px"></td>
                                            </tr>'
            . $cadenaresultados . '</table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" border="1" cellpadding="2">
                                            <tr>
                                                <td style="font-weight:bold;background-color:#6C6A69;color:white;font-size:8px">OBSERVACIONES</td>
                                            </tr>
                                            <tr>
                                                <td height="21" align="left" style="font-size:7px">' . htmlentities(strtoupper($hc->observacionformato1)) . '</td>
                                            </tr>
                                            <tr>
                                                <td height="21" align="left" style="font-size:7px">' . htmlentities(strtoupper($hc->observacionformato2)) . '</td>
                                            </tr>
                                            <tr>
                                                <td height="21" align="left" style="font-size:7px">' . htmlentities(strtoupper($hc->observacionformato3)) . '</td>
                                            </tr>
                                            <tr>
                                                <td height="21" align="left" style="font-size:7px">' . htmlentities(strtoupper($hc->observacionformato4)) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="80%">
                                        <table width="100%" height="100%" cellpadding="1">
                                            <tr>
                                                <td width="50%" style="font-size:10px;"></td>
                                                <td width="50%" style="font-size:10px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:10px;"></td>
                                                <td width="50%" style="font-size:10px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:10px;"></td>
                                                <td width="50%" style="font-size:10px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:10px;"></td>
                                                <td width="50%" style="font-size:10px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:10px;"></td>
                                                <td width="50%" style="font-size:10px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:10px;"></td>
                                                <td width="50%" style="font-size:10px;"></td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:8px;">____________________________________________</td>
                                                <td width="50%" style="font-size:8px;">____________________________________</td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:8px;">Firma y Sello del Responsable de la Atención</td>
                                                <td width="50%" style="font-size:8px;">Firma del Asegurado o Apoderado</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="20%">
                                        <table border="1">
                                            <tr>
                                                <td height="100" width="100%" style="font-size:8px;">Huella digital del <br> Asegurado o Apoderado </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>';

            $pdf::writeHTML($tbl, true, true, true, true, 'C');

            $pdf::Output('Historia.pdf');
        }
    }

    public function reporteHistoriaInicial(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $id       = $request->input('id');
        $historia = Historia::find($id);

        if ($historia === null) {
            echo 'NO EXISTE ESTA HISTORIA';
        } else {
            if ($historia->estado !== 'S') {
                echo 'AÚN NO SE COMPLETA LA HISTORIA CLÍNICA INICIAL';
            } else {
                $pdf = new TCPDF();
                // set margins
                $pdf::SetMargins(10, 10, 10);

                // set auto page breaks
                $pdf::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
                $pdf::SetTextColor(34, 68, 136);

                $pdf::SetTitle('HistCliniIni');
                $pdf::AddPage();
                $pdf::Image("dist/img/logo2-nefrocix.jpg", 10, 7, 50, 25);
                $pdf::SetFont('helvetica', 'B', 15);
                $pdf::Cell(60, 3, "", 0, 0, 'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica', 'B', 14);
                $pdf::Cell(48, 10, "", 0, 0, 'C');
                $pdf::Cell(145, 0, utf8_decode(utf8_encode("HISTORIA CLÍNICA INICIAL")), '', 0, 'C');
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);

                $comorb  = "";
                $ccomorb = explode(";", $historia->txtComorbilidades);

                foreach ($ccomorb as $comm) {
                    if ($comm == "1") {$comorb .= "Enfermedades Ateroscleróticas";}
                    if ($comm == "2") {$comorb .= "Insuficiencia cardíaca congestiva";}
                    if ($comm == "3") {$comorb .= "Enfermedad vascular periférica";}
                    if ($comm == "4") {$comorb .= "Accidente cerebrovascular/isquémico";}
                    if ($comm == "5") {$comorb .= "Cáncer";}
                    if ($comm == "6") {$comorb .= "Diabetes";}
                    if ($comm == "7") {$comorb .= "Hipertensión";}
                    if ($comm == "8") {$comorb .= "Tuberculosis";}
                    if ($comm == "9") {$comorb .= "Otra";}
                    $comorb .= "; ";
                }

                $dia    = date("d");
                $mes    = date("m");
                $ano    = date("Y");
                $dianaz = date("d", strtotime($historia->persona->fechanacimiento));
                $mesnaz = date("m", strtotime($historia->persona->fechanacimiento));
                $anonaz = date("Y", strtotime($historia->persona->fechanacimiento));
                //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
                if (($mesnaz == $mes) && ($dianaz > $dia)) {
                    $ano = ($ano - 1);}
                //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
                if ($mesnaz > $mes) {
                    $ano = ($ano - 1);}
                //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
                $edad = ($ano - $anonaz);
                if ($historia->persona->fechanacimiento == null) {
                    $edad = "-";
                }

                $comorb = substr($comorb, 0, -4);

                $tbl = '
                <font style="font-size:10px; text-align:left;">I. <u>DATOS GENERALES:</u><font>
                <br>
                <br>
                <font style="font-size:10px; text-align:left;">1.1 <u>DATOS DEL PACIENTE:</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Apellidos y nombres: ' . htmlentities($historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Dirección del Domicilio: ' . htmlentities($historia->persona->direccion) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td width="60%"><ul><li>N° de Afiliación: ' . htmlentities($historia->carnet) . '</li></ul></td>
                            <td width="40%"><ul><li>N° de DNI/CE: ' . htmlentities($historia->persona->dni) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td width="60%"><ul><li>Departamento: ' . htmlentities($historia->departamento2->nombre) . '</li></ul></td>
                            <td width="40%"><ul><li>Provincia: ' . htmlentities($historia->provincia2->nombre) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td width="60%"><ul><li>Distrito: ' . htmlentities($historia->distrito2->nombre) . '</li></ul></td>
                            <td width="40%"><ul><li>Teléfono: ' . htmlentities($historia->persona->telefono) . ' ' . htmlentities($historia->persona->telefono2 !== "" && $historia->persona->telefono2 !== null ? " / " . $historia->persona->telefono2 : "") . '</li></ul></td>
                        </tr>
                        <tr>
                            <td width="60%"><ul><li>IPRESS pública de procedencia: ' . htmlentities($historia->ipress) . '</li></ul></td>
                            <td width="40%"><ul><li>Sexo: ' . ($historia->persona->sexo == "M" ? "MASCULINO" : "FEMENINO") . '</li></ul></td>
                        </tr>
                        <tr>
                            <td width="60%"><ul><li>Edad: ' . htmlentities($edad) . '</li></ul></td>
                            <td width="40%"><ul><li>Raza: ' . htmlentities($historia->persona->raza) . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <font style="font-size:10px; text-align:left;">1.2 <u>DIRECCIONES DE EMERGENCIA:</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Apellidos y nombres: ' . htmlentities($historia->persona2->apellidopaterno . ' ' . $historia->persona2->apellidomaterno . ' ' . $historia->persona2->nombres) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Relación con el paciente: ' . htmlentities($historia->txtRelacion) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Dirección: ' . htmlentities($historia->persona2->direccion) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td width="60%"><ul><li>Departamento: ' . htmlentities($historia->persona2->distrito2 == null ? "-" : $historia->persona2->distrito2->provincia->departamento->nombre) . '</li></ul></td>
                            <td width="40%"><ul><li>Provincia: ' . htmlentities($historia->persona2->distrito2 == null ? "-" : $historia->persona2->distrito2->provincia->nombre) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td width="60%"><ul><li>Distrito: ' . htmlentities($historia->persona2->distrito2 == null ? "-" : $historia->persona2->distrito2->nombre) . '</li></ul></td>
                            <td width="40%"><ul><li>Teléfono: ' . htmlentities($historia->persona2->telefono) . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <font style="font-size:10px; text-align:left;">II. <u>EVALUACIÓN MÉDICA</u><font>
                <br>
                <br>
                <font style="font-size:10px; text-align:left;">2.1 <u>ANTECEDENTES PATOLÓGICOS:</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Enfermedad o condición clínica que produjo la insuficiencia renal: ' . htmlentities(strtoupper(($historia->etiologia !== null ? $historia->etiologia->nombre : "") . ' - ' . ($historia->etiologia2 !== null ? $historia->etiologia2->nombre : ""))) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Fecha de primera hemodiálisis: ' . ($historia->txtFechaPrimeraHemodialisis == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaPrimeraHemodialisis))) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Comorbilidades: ' . ($comorb == "" ? "NO" : $comorb) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Grupo de sangre: ' . ($historia->gruposanguineo == null ? "-" : $historia->gruposanguineo) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Intervenciones quirúrgicas: ' . htmlentities($historia->txtIntervencionesQuirurgicas == null ? "-" : strtoupper($historia->txtIntervencionesQuirurgicas)) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Número de transfusiones: ' . htmlentities($historia->txtNumeroTransfusiones == null ? "0" : $historia->txtNumeroTransfusiones) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Diuresis residual en 24 horas: ' . htmlentities($historia->txtDiuresis1 . 'cc / ' . $historia->txtDiuresis1) . 'h</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Medicación que recibe: ' . htmlentities($historia->txtMedicacion == null ? "-" : strtoupper($historia->txtMedicacion)) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Alergia a Medicamentos: ' . htmlentities($historia->txtAlergia == null ? "NO" : strtoupper($historia->txtAlergia)) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Inmunización contra hepatitis B: ' . ($historia->txtCantDosis == null || $historia->txtCantDosis == "" || $historia->txtCantDosis == "0" ? "NO" : "SI") . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="20%"> - N° Dosis</td>
                                        <td width="2%">:</td>
                                        <td width="72%">' . htmlentities($historia->txtCantDosis == null ? "-" : strtoupper($historia->txtCantDosis)) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="20%"> - Fecha 1ra dosis</td>
                                        <td width="2%">:</td>
                                        <td width="72%">' . htmlentities($historia->txtFechaCantDosis1 == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaCantDosis1))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="20%"> - Fecha 2da dosis</td>
                                        <td width="2%">:</td>
                                        <td width="72%">' . htmlentities($historia->txtFechaCantDosis2 == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaCantDosis2))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="20%"> - Fecha 3ra dosis</td>
                                        <td width="2%">:</td>
                                        <td width="72%">' . ($historia->txtFechaCantDosis3 == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaCantDosis3)) . " ") . ($historia->txtFechaCantDosis4 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis4)) . " ") . ($historia->txtFechaCantDosis5 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis5)) . " ") . ($historia->txtFechaCantDosis6 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis6)) . " ") . ($historia->txtFechaCantDosis7 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis7))) . ' ' . ($historia->txtFechaCantDosis8 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis8)) . " ") . ($historia->txtFechaCantDosis9 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis9)) . " ") . ($historia->txtFechaCantDosis10 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis10)) . " ") . ($historia->txtFechaCantDosis11 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis11)) . " ") . ($historia->txtFechaCantDosis12 == null ? "" : date("d/m/Y", strtotime($historia->txtFechaCantDosis12)) . " ") . '</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Otras terapias previas de reemplazo renal: ' . htmlentities($historia->txtTransplanteRenal1 !== null || $historia->txtTransplanteRenal2 !== null || $historia->txtFechaDialisisPeritoneal1 !== null || $historia->txtFechaDialisisPeritoneal2 !== null ? "SI" : "NO") . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="45%"> - Diálisis Peritoneal Continua Ambulatoria</td>
                                        <td width="49%"> Desde ' . ($historia->txtFechaDialisisPeritoneal1 == null || $historia->txtFechaDialisisPeritoneal1 == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaDialisisPeritoneal1))) . ' Hasta ' . ($historia->txtFechaDialisisPeritoneal2 == null || $historia->txtFechaDialisisPeritoneal2 == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaDialisisPeritoneal2))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="45%"> - Transplante Renal</td>
                                        <td width="49%"> Desde ' . ($historia->txtTransplanteRenal1 == null || $historia->txtTransplanteRenal1 == null ? "-" : date("d/m/Y", strtotime($historia->txtTransplanteRenal1))) . ' Hasta ' . ($historia->txtTransplanteRenal2 == null || $historia->txtTransplanteRenal2 == null ? "-" : date("d/m/Y", strtotime($historia->txtTransplanteRenal2))) . '</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <font style="font-size:10px; text-align:left;">2.2 <u>OTROS ANTECEDENTES PATOLÓGICOS DE IMPORTANCIA:</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Médicos: ' . htmlentities($historia->txtAntecedentesPMedicos == null ? "-" : $historia->txtAntecedentesPMedicos) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Quirúrgicos: ' . htmlentities($historia->txtAntecedentesPQuirurgicos == null ? "-" : $historia->txtAntecedentesPQuirurgicos) . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                ';
                $pdf::writeHTML($tbl, false, false, false, false, 'C');

                $pdf::AddPage();
                $pdf::Image("dist/img/logo2-nefrocix.jpg", 10, 7, 50, 25);
                $pdf::SetFont('helvetica', 'B', 15);
                $pdf::Cell(60, 3, "", 0, 0, 'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica', 'B', 14);
                $pdf::Cell(48, 10, "", 0, 0, 'C');
                $pdf::Cell(145, 0, utf8_decode(utf8_encode("")), '', 0, 'C');
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);

                $tbl = '
                <font style="font-size:10px; text-align:left;">2.3 <u>ENFERMEDAD ACTUAL:</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Síntomas: ' . htmlentities($historia->txtSintomasEnfermedadActual == null ? "-" : $historia->txtSintomasEnfermedadActual) . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <font style="font-size:10px; text-align:left;">2.4 <u>EXAMEN CLÍNICO:</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Funciones vitales: </li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="13%"></td>
                                        <td width="29%"> - Presión arterial: ' . $historia->txtPresionArterial1 . '/' . $historia->txtPresionArterial2 . '</td>
                                        <td width="29%"> - F.C.: ' . ((int) $historia->txtFC) . ' lat. x min.</td>
                                        <td width="29%"> - F.R.: ' . ((int) $historia->txtFR) . ' res. x min.</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%"><ul><li>Peso: ' . $historia->txtPeso . ' Kg.</li></ul></td>
                            <td width="50%"><ul><li>Talla: ' . $historia->txtTalla . ' m.</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Piel: ' . htmlentities($historia->txtPiel == null ? "-" : $historia->txtPiel) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Acceso Vascular: </li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - N° de accesos vasculares previos: ' . htmlentities($historia->txtNumeroAccesoVascular == null ? "-" : $historia->txtNumeroAccesoVascular) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Tiempo promedio de permanencia en los accesos vasculares: ' . htmlentities($historia->txtTiempoPermanenciaAccesosVasculares == null ? "-" : $historia->txtTiempoPermanenciaAccesosVasculares) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Causa de camino y/o pérdida: ' . htmlentities($historia->txtCambioPerdida == null ? "-" : $historia->txtCambioPerdida) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Responsable de la realización:</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <table width="100%" style="font-size:10px;">
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Cirujano cardiovascular</td>
                                                    <td width="5%">' . ($historia->cbxDescripcionResponsableRealizacion == 'Cirujano Cardiovascular' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                    <td width="10%"></td>
                                                    <td width="15%"> Nefrólogo</td>
                                                    <td width="5%">' . ($historia->cbxDescripcionResponsableRealizacion == 'Nefrologo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Cirujano general</td>
                                                    <td width="5%">' . ($historia->cbxDescripcionResponsableRealizacion == 'Cirujano general' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                    <td width="10%"></td>
                                                    <td width="15%"> Otro</td>
                                                    <td width="5%">' . ($historia->cbxDescripcionResponsableRealizacion == 'Otro' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                    <td width="28%">' . ($historia->txtDescripcionResponsableRealizacion == null ? "-" : $historia->txtDescripcionResponsableRealizacion) . '</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Fecha de realización de acceso vascular actual: ' . ($historia->txtFechaAccesoVascularActual == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaAccesoVascularActual))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Ubicación:</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <table width="100%" style="font-size:10px;">
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Radial</td>
                                                    <td width="5%">' . ($historia->cbxUbicacionVascularActual == 'Radial' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                    <td width="10%"></td>
                                                    <td width="15%"> Humeral</td>
                                                    <td width="5%">' . ($historia->cbxUbicacionVascularActual == 'Humeral' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Otros</td>
                                                    <td width="5%">' . ($historia->cbxUbicacionVascularActual == 'Otros' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                    <td width="15%">(especificar)</td>
                                                    <td width="43%">' . ($historia->txtUbicacionVascularActual == null ? "-" : $historia->txtUbicacionVascularActual) . '</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Tipo:</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <table width="100%" style="font-size:10px;">
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Fístula</td>
                                                    <td width="5%">' . ($historia->cbxTipoDescripcionAccesoVascularActual == 'Fístula' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                    <td width="2%"></td>
                                                    <td width="23%"> Catéter temporal</td>
                                                    <td width="5%">' . ($historia->cbxTipoDescripcionAccesoVascularActual == 'Cateter temporal' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Injerto</td>
                                                    <td width="5%">' . ($historia->cbxTipoDescripcionAccesoVascularActual == 'Injerto' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                    <td width="2%"></td>
                                                    <td width="23%"> Catéter permanente</td>
                                                    <td width="5%">' . ($historia->cbxTipoDescripcionAccesoVascularActual == 'Cateter permanente' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="88%">' . htmlentities($historia->txtTipoDescripcionAccesoVascularActual == null ? "-" : $historia->txtTipoDescripcionAccesoVascularActual) . '</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Thrill:</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <table width="100%" style="font-size:10px;">
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Bueno</td>
                                                    <td width="5%">' . ($historia->cbxThill == 'Bueno' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Regular</td>
                                                    <td width="5%">' . ($historia->cbxThill == 'Regular' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> Malo</td>
                                                    <td width="5%">' . ($historia->cbxThill == 'Malo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="25%"> No aplica</td>
                                                    <td width="5%">' . ($historia->cbxThill == 'No aplica' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>A. Cardiovascular: </li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Corazón: ' . htmlentities($historia->txtCorazon == null ? "-" : $historia->txtCorazon) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Pulsos Periféricos: ' . htmlentities($historia->txtPulsosPerifericos == null ? "-" : $historia->txtPulsosPerifericos) . '</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Aparato Respiratorio: ' . htmlentities($historia->txtAparatoRespiratorio == null ? "-" : $historia->txtAparatoRespiratorio) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Abdomen: ' . htmlentities($historia->txtAbdomen == null ? "-" : $historia->txtAbdomen) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Neurológico: ' . htmlentities($historia->txtNeurologicos == null ? "-" : $historia->txtNeurologicos) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Osteomuscular: ' . htmlentities($historia->txtOsteomuscular == null ? "-" : $historia->txtOsteomuscular) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Estado Nutricional: ' . htmlentities($historia->txtEstadoNutricional == null ? "-" : $historia->txtEstadoNutricional) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td colspan="2"><ul><li>Indice de Karnofski: ' . htmlentities($historia->txtKarnofski == null ? "-" : $historia->txtKarnofski) . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                ';
                $pdf::writeHTML($tbl, false, false, false, false, 'C');

                $pdf::AddPage();
                $pdf::Image("dist/img/logo2-nefrocix.jpg", 10, 7, 50, 25);
                $pdf::SetFont('helvetica', 'B', 15);
                $pdf::Cell(60, 3, "", 0, 0, 'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica', 'B', 14);
                $pdf::Cell(48, 10, "", 0, 0, 'C');
                $pdf::Cell(145, 0, utf8_decode(utf8_encode("")), '', 0, 'C');
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);

                $txtHbHto1 = null;
                $txtHbHto2 = null;

                if ($historia->txtHbHto !== null && $historia->txtHbHto !== "") {
                    $txtHbHto1 = (!isset(explode("/", $historia->txtHbHto)[0]) ? "-" : explode("/", $historia->txtHbHto)[0]) . "gr/L ";
                    $txtHbHto2 = (!isset(explode("/", $historia->txtHbHto)[1]) ? "-" : explode("/", $historia->txtHbHto)[1]) . "%";
                }

                $tbl = '
                <font style="font-size:10px; text-align:left;">2.5 <u>EVALUACIÓN PREVIA:</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Hematología: </li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Grupo Sanguíneo: ' . substr($historia->gruposanguineo, 0, strlen($historia->gruposanguineo) - 1) . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaGrupoSanguineoLetra == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaGrupoSanguineoLetra))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Factor Rh: ' . substr($historia->gruposanguineo, strlen($historia->gruposanguineo) - 1, strlen($historia->gruposanguineo)) . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaGrupoSanguineoSigno == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaGrupoSanguineoSigno))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Hb/Hto: ' . $txtHbHto1 . $txtHbHto2 . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaHbHto == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaHbHto))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td colspan="2"> - Tiempo de Hemodiálisis: ' . ($historia->txtTiempoHemodialisis == null ? "-" : $historia->txtTiempoHemodialisis) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="30%"> - Transfusiones previas:</td>
                                        <td width="5%"> Si</td>
                                        <td width="5%">' . ($historia->txtTransfusionesPrevias == '' ? '<img src="dist/img/uncheck.png" alt="" width="10" height="10">' : '<img src="dist/img/check.png" alt="" width="10" height="10">') . '</td>
                                        <td width="5%"> No</td>
                                        <td width="5%">' . ($historia->txtTransfusionesPrevias == '' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="40%"> Número: ' . $historia->txtTransfusionesPrevias . '</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Bioquímica: </li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Glicemia: ' . htmlentities($historia->txtGlicemia == null ? "-" : $historia->txtGlicemia . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaGlicemia == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaGlicemia))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Depuración de Creatinina: ' . ($historia->txtDepuracionCreatina == null ? "-" : $historia->txtDepuracionCreatina . " mil/min x 1.73 m²") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaDepuracionCreatina == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaDepuracionCreatina))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Endogena: ' . htmlentities($historia->txtEndogena == null ? "-" : $historia->txtEndogena . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaEndogena == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaEndogena))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Uremia: ' . ($historia->txtUremia == null ? "-" : $historia->txtUremia . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaUremia == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaUremia))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Creatinina: ' . ($historia->txtCreatinina == null ? "-" : $historia->txtCreatinina . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaCreatinina == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaCreatinina))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Ácido Úrico: ' . ($historia->txtAcidoUrico == null ? "-" : $historia->txtAcidoUrico . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaAcidoUrico == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaAcidoUrico))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Proteínas Totales: ' . ($historia->txtProteinas == null ? "-" : $historia->txtProteinas . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaProteinas == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaProteinas))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Albúmina: ' . ($historia->txtAlbumina == null ? "-" : $historia->txtAlbumina . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaAlbumina == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaAlbumina))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Calcio: ' . ($historia->txtCalcio == null ? "-" : $historia->txtCalcio . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaCalcio == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaCalcio))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Fosforo: ' . ($historia->txtFosforo == null ? "-" : $historia->txtFosforo . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaFosforo == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaFosforo))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - TGO: ' . ($historia->txtTGO == null ? "-" : $historia->txtTGO . " UI/L") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaTGO == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaTGO))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - TGP: ' . ($historia->txtTGP == null ? "-" : $historia->txtTGP . " UI/L") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaTGP == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaTGP))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Bilirrubina Total: ' . ($historia->txtBilirrubina == null ? "-" : $historia->txtBilirrubina . " MG/DL") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaBilirrubina == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaBilirrubina))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Hierro sérico: ' . ($historia->txtHierroSerico == null ? "-" : $historia->txtHierroSerico . " µg/dl") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaHierroSerico == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaHierroSerico))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Saturación de transferrina: ' . ($historia->txtTransferrina == null ? "-" : $historia->txtTransferrina . " %") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaTransferrina == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaTransferrina))) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="54%"> - Dosaje de paratohormona: ' . ($historia->txtParatohormona == null ? "-" : $historia->txtParatohormona . " PG/ML") . '</td>
                                        <td width="40%"> Fecha: ' . ($historia->txtFechaParatohormona == null ? "-" : date("d/m/Y", strtotime($historia->txtFechaParatohormona))) . '</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2"><ul><li>Serología: </li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="font-size:10px;">
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="22%"> - Serología para Lúes:</td>
                                        <td width="10%"> Positivo</td>
                                        <td width="4%">' . ($historia->cbxSerologicasLues == 'Positivo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="10%"> Negativo</td>
                                        <td width="4%">' . ($historia->cbxSerologicasLues == 'Negativo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="14%"> Desconocido</td>
                                        <td width="4%">' . ($historia->cbxSerologicasLues == 'Desconocido' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="21%"> Fecha: ' . date("d/m/Y", strtotime($historia->txtFechaSerologicasLues)) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="22%"> - AgHbs:</td>
                                        <td width="10%"> Positivo</td>
                                        <td width="4%">' . ($historia->cbxAgHbs == 'Positivo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="10%"> Negativo</td>
                                        <td width="4%">' . ($historia->cbxAgHbs == 'Negativo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="14%"> Desconocido</td>
                                        <td width="4%">' . ($historia->cbxAgHbs == 'Desconocido' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="21%"> Fecha: ' . date("d/m/Y", strtotime($historia->txtFechaAgHbs)) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="22%"> - AcHbs:</td>
                                        <td width="10%"> Positivo</td>
                                        <td width="4%">' . ($historia->cbxAcHbs == 'Positivo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="10%"> Negativo</td>
                                        <td width="4%">' . ($historia->cbxAcHbs == 'Negativo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="14%"> Desconocido</td>
                                        <td width="4%">' . ($historia->cbxAcHbs == 'Desconocido' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="21%"> Fecha: ' . date("d/m/Y", strtotime($historia->txtFechaAcHbs)) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="22%"> - AcHbc:</td>
                                        <td width="10%"> Positivo</td>
                                        <td width="4%">' . ($historia->cbxAcHbc == 'Positivo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="10%"> Negativo</td>
                                        <td width="4%">' . ($historia->cbxAcHbc == 'Negativo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="14%"> Desconocido</td>
                                        <td width="4%">' . ($historia->cbxAcHbc == 'Desconocido' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="21%"> Fecha: ' . date("d/m/Y", strtotime($historia->txtFechaAcHbc)) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="22%"> - AcHVC:</td>
                                        <td width="10%"> Positivo</td>
                                        <td width="4%">' . ($historia->cbxAcHVC == 'Positivo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="10%"> Negativo</td>
                                        <td width="4%">' . ($historia->cbxAcHVC == 'Negativo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="14%"> Desconocido</td>
                                        <td width="4%">' . ($historia->cbxAcHVC == 'Desconocido' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="21%"> Fecha: ' . date("d/m/Y", strtotime($historia->txtFechaAcHVC)) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="22%"> - HIV:</td>
                                        <td width="10%"> Positivo</td>
                                        <td width="4%">' . ($historia->cbxHIV == 'Positivo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="10%"> Negativo</td>
                                        <td width="4%">' . ($historia->cbxHIV == 'Negativo' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="14%"> Desconocido</td>
                                        <td width="4%">' . ($historia->cbxHIV == 'Desconocido' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                        <td width="21%"> Fecha: ' . date("d/m/Y", strtotime($historia->txtFechaHIV)) . '</td>
                                    </tr>
                                    <tr>
                                        <td width="6%"></td>
                                        <td width="94%"> - Vacunación contra Hepatitis B:</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <table width="100%" style="font-size:10px;">
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="20%"> Completa</td>
                                                    <td width="5%">' . ($historia->cbxVacunacionHepatitisB == 'Completa' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="20%"> Incompleta</td>
                                                    <td width="5%">' . ($historia->cbxVacunacionHepatitisB == 'Incompleta' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="20%"> En Proceso</td>
                                                    <td width="5%">' . ($historia->cbxVacunacionHepatitisB == 'En Proceso' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                                <tr>
                                                    <td width="12%"></td>
                                                    <td width="20%"> No inició Esquema</td>
                                                    <td width="5%">' . ($historia->cbxVacunacionHepatitisB == 'No inició Esquema' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td width="36%"><ul><li>Ecografia Renal: </li></ul></td>
                            <td width="5%"> Si</td>
                            <td width="5%">' . ($historia->txtFechaEcografiaRenal == '' ? '<img src="dist/img/uncheck.png" alt="" width="10" height="10">' : '<img src="dist/img/check.png" alt="" width="10" height="10">') . '</td>
                            <td width="5%"> No</td>
                            <td width="5%">' . ($historia->txtFechaEcografiaRenal == '' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                            <td width="40%">Fecha: ' . ($historia->txtFechaEcografiaRenal !== null && $historia->txtFechaEcografiaRenal !== "" ? date("d/m/Y", strtotime($historia->txtFechaEcografiaRenal)) : "-") . '</td>
                        </tr>
                        <tr>
                            <td width="6%"></td>
                            <td width="94%">' . $historia->txtObservacionEcografiaRenal . '</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td width="36%"><ul><li>RX de Torax: </li></ul></td>
                            <td width="5%"> Si</td>
                            <td width="5%">' . ($historia->txtFechaRXTorax == '' ? '<img src="dist/img/uncheck.png" alt="" width="10" height="10">' : '<img src="dist/img/check.png" alt="" width="10" height="10">') . '</td>
                            <td width="5%"> No</td>
                            <td width="5%">' . ($historia->txtFechaRXTorax == '' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                            <td width="40%">Fecha: ' . ($historia->txtFechaRXTorax !== null && $historia->txtFechaRXTorax !== "" ? date("d/m/Y", strtotime($historia->txtFechaRXTorax)) : "-") . '</td>
                        </tr>
                        <tr>
                            <td width="6%"></td>
                            <td width="94%">' . htmlentities($historia->txtObservacionRXTorax) . '</td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <font style="font-size:10px; text-align:left;">III. <u>DATOS DEL MEDICO</u><font>
                <br>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="2">
                    <tbody>
                        <tr>
                            <td width="28%"><ul><li>Apellidos y Nombres</li></ul></td>
                            <td width="2%">:</td>
                            <td width="70%">' . htmlentities($historia->doctor !== null ? $historia->doctor->apellidopaterno . ' ' . $historia->doctor->apellidomaterno . ' ' . $historia->doctor->nombres : "-") . '</td>
                        </tr>
                        <tr>
                            <td width="28%"><ul><li>N° CMP</li></ul></td>
                            <td width="2%">:</td>
                            <td width="70%">' . htmlentities($historia->doctor == null || $historia->doctor == "" ? "-" : $historia->doctor->cmp) . '</td>
                        </tr>
                        <tr>
                            <td width="28%"><ul><li>Especialidad </li></ul></td>
                            <td width="2%">:</td>
                            <td width="70%">' . htmlentities($historia->doctor == null || $historia->doctor == "" ? "-" : $historia->doctor->especialidad->nombre) . '</td>
                        </tr>
                        <tr>
                            <td width="28%"><ul><li>N° RNE</li></ul></td>
                            <td width="2%">:</td>
                            <td width="70%">' . htmlentities($historia->doctor || $historia->doctor == "" ? "-" : $historia->doctor->rne) . '</td>
                        </tr>
                        <tr>
                            <td width="28%"><ul><li>Firma y sello</li></ul></td>
                            <td width="2%">:</td>
                            <td width="70%"></td>
                        </tr>
                    </tbody>
                </table>
                ';
                $pdf::writeHTML($tbl, false, false, false, false, 'C');

                $pdf::SetAutoPageBreak(true, 0);
                $pdf::Output('Historia.pdf');
            }
        }
    }

    public function reporteHistoriaEnfermeria(Request $request)
    {

        date_default_timezone_set('America/Lima');

        $id       = $request->input('id');
        $historia = Historia::find($id);

        if ($historia === null) {
            echo 'NO EXISTE ESTA HISTORIA';
        } else {
            if ($historia->estado3 !== 'S') {
                echo 'AÚN NO SE COMPLETA EL FORMATO DE ENFERMERÍA';
            } else {
                $pdf = new TCPDF();
                // set margins
                $pdf::SetMargins(40, 20, 30);

                // set auto page breaks
                $pdf::SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
                $pdf::SetTextColor(34, 68, 136);

                $pdf::SetTitle('HistEnfermeria');
                $pdf::AddPage();
                $pdf::Image("dist/img/logo2-nefrocix.jpg", 10, 7, 50, 25);
                $pdf::SetFont('helvetica', 'B', 13);
                $pdf::Cell(48, 10, "", 0, 0, 'C');
                $pdf::Cell(95, 0, utf8_decode(utf8_encode("HISTORIA DE ENFERMERÍA EN LA ADMISIÓN DEL PACIENTE")), '', 0, 'C');
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);
                $pdf::Ln(5);

                $dia    = date("d");
                $mes    = date("m");
                $ano    = date("Y");
                $dianaz = date("d", strtotime($historia->persona->fechanacimiento));
                $mesnaz = date("m", strtotime($historia->persona->fechanacimiento));
                $anonaz = date("Y", strtotime($historia->persona->fechanacimiento));
                //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
                if (($mesnaz == $mes) && ($dianaz > $dia)) {
                    $ano = ($ano - 1);}
                //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
                if ($mesnaz > $mes) {
                    $ano = ($ano - 1);}
                //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
                $edad = ($ano - $anonaz);
                if ($historia->persona->fechanacimiento == null) {
                    $edad = "-";
                }

                $tbl = '
                <table width="100%" style="font-size:10px;" cellpadding="3">
                    <tr>
                        <td align="left" width="70%"><b>Fecha: </b>' . date("d-m-Y", strtotime($historia->fechaformatoenfermeria)) . '</td>
                        <td align="left" width="30%"><b>Hora: </b>' . date("H:i", strtotime($historia->fechaformatoenfermeria)) . '</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <font style="font-size:11px; text-align:left;">Datos Generales:<font>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="3">
                    <tbody>
                        <tr>
                            <td><ul><li>Apellidos y Nombres: ' . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Edad: ' . $edad . ' AÑOS</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Dirección: ' . strtoupper($historia->persona->direccion) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Número de teléfono fijo o celular: ' . $historia->persona->telefono . ' ' . ($historia->persona->telefono2 !== "" && $historia->persona->telefono2 !== null ? " / " . $historia->persona->telefono2 : "") . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Estado civil: ' . $historia->estadocivil . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Prefesión u ocupación: ' . strtoupper($historia->ocupacion) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Grado de Instrucción: ' . $historia->gradoinstruccion . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Familiar responsable: ' . $historia->persona2->apellidopaterno . ' ' . $historia->persona2->apellidomaterno . ' ' . $historia->persona2->nombres . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Dirección: ' . strtoupper($historia->persona2->direccion) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Teléfono: ' . $historia->persona2->telefono . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <font style="font-size:11px; text-align:left;">Antecedentes:<font>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="3">
                    <tbody>
                        <tr>
                            <td><ul><li>FAMILIARES:</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul style="list-style-type: none;"><li>' . $historia->antecedentesfamiliares . '</li></ul></td>
                        </tr>
                        <tr>
                            <td style="font-size:4px;"></td>
                        </tr>
                        <tr>
                            <td><ul><li>MEDICAMENTOS:</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul style="list-style-type: none;"><li>' . $historia->antecedentesmedicamentos . '</li></ul></td>
                        </tr>
                        <tr>
                            <td style="font-size:4px;"></td>
                        </tr>
                        <tr>
                            <td><ul><li>TRATAMIENTO FARMACOLÓGICO ACTUAL:</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul style="list-style-type: none;"><li>' . $historia->antecedentesfarma . '</li></ul></td>
                        </tr>
                        <tr>
                            <td style="font-size:4px;"></td>
                        </tr>
                        <tr>
                            <td><ul><li>TRANSFUSIONES SANGUÍNEAS (fechas actuales, en un periodo no mayor a 6 meses):</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul style="list-style-type: none;"><li>' . $historia->antecedentestransfusiones . '</li></ul></td>
                        </tr>
                        <tr>
                            <td style="font-size:4px;"></td>
                        </tr>
                        <tr>
                            <td><ul><li>HOSPITALIZACIONES Y OPERACIONES (fechass actuales, en un periodo no mayor a 6 meses y establecimiento de salud donde se hospitalizó)</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul style="list-style-type: none;"><li>' . $historia->antecedenteshospital . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                ';

                $pdf::writeHTML($tbl, true, true, true, true, 'C');

                $pdf::AddPage();

                $tbl = '
                <font style="font-size:11px; text-align:left;">Estado de Salud actual:<font>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="3">
                    <tbody>
                        <tr>
                            <td><ul><li>Funciones vitales:</li></ul></td>
                        </tr>
                        <tr>
                            <td>
                                <ul style="list-style-type: none;">
                                    <li>&nbsp;&nbsp;&nbsp;Presión Arterial: ' . $historia->txtPresionArterial1 . "/" . $historia->txtPresionArterial2 . '</li>
                                    <li>&nbsp;&nbsp;&nbsp;Frecuencia Cardiaca: ' . $historia->txtFC . '</li>
                                    <li>&nbsp;&nbsp;&nbsp;Temperatura: ' . $historia->txtTemperatura . '</li>
                                    <li>&nbsp;&nbsp;&nbsp;Frecuencia Respiratoria: ' . $historia->txtFR . '</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><ul><li>Serología:</li></ul></td>
                        </tr>
                        <tr>
                            <td>
                                <ul style="list-style-type: none;">
                                    <li>&nbsp;&nbsp;&nbsp;AcHBe: ' . $historia->txtacbe2 . '</li>
                                    <li>&nbsp;&nbsp;&nbsp;AgHBs: ' . $historia->txtaghbs2 . '</li>
                                    <li>&nbsp;&nbsp;&nbsp;AvHCV: ' . $historia->txtachcv . '</li>
                                    <li>&nbsp;&nbsp;&nbsp;VIH: ' . $historia->txtvih . '</li>
                                    <li>&nbsp;&nbsp;&nbsp;VDRL: ' . $historia->txtvdrl . '</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><ul><li>Inmunizaciones:</li></ul></td>
                        </tr>
                        <tr>
                            <td>
                                <ul>
                                    <li>Hepatitis B:</li>
                                    <li>
                                        <ul>
                                            <li>&nbsp;&nbsp;&nbsp;1° Dosis: ' . date("d-m-Y", strtotime($historia->txtFechaCantDosis1)) . '</li>
                                            <li>&nbsp;&nbsp;&nbsp;2° Dosis: ' . date("d-m-Y", strtotime($historia->txtFechaCantDosis2)) . '</li>
                                            <li>&nbsp;&nbsp;&nbsp;3° Dosis: ' . date("d-m-Y", strtotime($historia->txtFechaCantDosis3)) . '</li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <font style="font-size:11px; text-align:left;">ACCESO VASCULAR:<font>
                <br>
                <table width="100%" style="font-size:10px;" cellpadding="3">
                    <tbody>
                        <tr>
                            <td><ul><li>Tipo de acceso vascular:</li></ul></td>
                        </tr>
                        <tr>
                            <td>
                                <ul style="list-style-type: none;">
                                    <li>a) Fistula arteriovenosa ' . ($historia->txttipoaccesovascular2 == 'FÍSTULA ARTERIOVENOSA' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '') . '</li>
                                    <li>b) Injerto ' . ($historia->txttipoaccesovascular2 == 'INJERTO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '') . '</li>
                                    <li>c) Catéter venoso central temporal ' . ($historia->txttipoaccesovascular2 == 'CATÉTER VENOSO CENTRAL TEMPORAL' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '') . '</li>
                                    <li>d) Catéter venoso central permanente ' . ($historia->txttipoaccesovascular2 == 'CATÉTER VENOSO CENTRAL PERMANENTE' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '') . '</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><ul><li>Ubicación: ' . $historia->txtubicacionaccesovascular2 . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Fecha de inicio de acceso vascular: ' . date("d-m-Y", strtotime($historia->txtfechainicioaccesovascular2)) . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Presencia de thrill: ' . $historia->txtpresenciathrill2 . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Presencia de soplo: ' . $historia->txtpresenciasoplo2 . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Presencia de pseudoneurisma: ' . $historia->txtpresenciapseudo2 . '</li></ul></td>
                        </tr>
                        <tr>
                            <td><ul><li>Condiciones de higiene: ' . $historia->txtcondicioneshigiene2 . '</li></ul></td>
                        </tr>
                    </tbody>
                </table>
                <font style="font-size:10px; text-align:left;"><font>
                <br>
                <table cellpadding="3">
                    <tr>
                        <td>Conocimiento de signos y síntomas de riesgo a pérdida y/o infección de acceso vascular:</td>
                    </tr>
                    <tr>
                        <td>
                            <ul style="list-style-type: none;">
                                <li>
                                    a) Identificar disminución o ausencia de thrill en fístula arteriovenosa:
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width="50%">SI ' . ($historia->txtidentifica2 == 'SI' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="50%">NO ' . ($historia->txtidentifica2 == 'NO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                    <tr>
                        <td width="100%">
                            <ul style="list-style-type: none;">
                                <li>
                                    b) Identificar presencia de prurito y dolor en zona de acceso vascular:
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width="50%">SI ' . ($historia->txtidentifica22 == 'SI' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="50%">NO ' . ($historia->txtidentifica22 == 'NO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                </table>
                <font style="font-size:10px; text-align:left;"><font>
                <br>
                <table cellpadding="3">
                    <tr>
                        <td>Conocimiento de acciones ante una ruptura accidental de fístula arteriovenosas o una migración o ruptura de catéter venoso central:</td>
                    </tr>
                    <tr>
                        <td align="center" width="50%">Conoce ' . ($historia->txtconocimiento2 == 'CONOCE' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="50%">Desconoce ' . ($historia->txtconocimiento2 == 'DESCONOCE' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                </table>
                ';

                $pdf::writeHTML($tbl, true, true, true, true, 'C');

                $pdf::AddPage();

                $tbl = '
                <font style="font-size:10px; text-align:left;"><font>
                <br>
                <table cellpadding="3">
                    <tr>
                        <td>Conocimiento de signos y síntomas ante una emergencia dialítica:</td>
                    </tr>
                    <tr>
                        <td align="left" width="40%">&nbsp;&nbsp;&nbsp;a) Hiperkalemia:</td>
                        <td align="center" width="30%">SI ' . ($historia->txtconocimiento22 == 'SI' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="30%">NO ' . ($historia->txtconocimiento22 == 'NO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                    <tr>
                        <td align="left" width="40%">&nbsp;&nbsp;&nbsp;b) Edema adugo de pulmón:</td>
                        <td align="center" width="30%">SI ' . ($historia->txtedema2 == 'SI' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="30%">NO ' . ($historia->txtedema2 == 'NO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                </table>
                <font style="font-size:10px; text-align:left;"><font>
                <br>
                <table cellpadding="3">
                    <tr>
                        <td>Conocimiento y cumplimiento de la medicación:</td>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li>
                                    Menciona horarios de administración de medicamentos:
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width="50%">SI ' . ($historia->txtconocimiento23 == 'SI' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="50%">NO ' . ($historia->txtconocimiento23 == 'NO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                    <tr>
                        <td width="100%">
                            <ul>
                                <li>
                                    Menciona medicamentos de mayor consumo:
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width="50%">SI ' . ($historia->txtmedicamentos2 == 'SI' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="50%">NO ' . ($historia->txtmedicamentos2 == 'NO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                </table>
                <font style="font-size:10px; text-align:left;"><font>
                <br>
                <table cellpadding="3">
                    <tr>
                        <td>Conocimiento de los cuidados del acceso vascular que presenta en ese momento:</td>
                    </tr>
                    <tr>
                        <td align="center" width="50%">SI ' . ($historia->txtconocimiento24 == 'SI' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                        <td align="center" width="50%">NO ' . ($historia->txtconocimiento24 == 'NO' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</td>
                    </tr>
                </table>
                <font style="font-size:10px; text-align:left;"><font>
                <br>
                <table cellpadding="3">
                    <tr>
                        <td>Formulación de los cuidados del acceso vascular que presenta en ese momento:</td>
                    </tr>
                    <tr>
                        <td>
                            <ul style="list-style-type: none;">
                                <li>
                                    1) ' . $historia->txtformulacion2 . '
                                </li>
                                <li>
                                    2) ' . $historia->txtformulacion22 . '
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <font style="font-size:10px; text-align:left;"><font>
                <br>
                <table cellpadding="3">
                    <tr>
                        <td>Planeación de acciones de enfermería:</td>
                    </tr>
                    <tr>
                        <td>
                            <ul style="list-style-type: none;">
                                <li>
                                    1) ' . $historia->txtplaneacion2 . '
                                </li>
                                <li>
                                    2) ' . $historia->txtplaneacion22 . '
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>';
                $pdf::writeHTML($tbl, false, false, false, false, 'C');

                $pdf::SetAutoPageBreak(true, 0);
                $pdf::Output('HistoriaEnfermeria.pdf');
            }
        }
    }

    public function createhenfermeria(Request $request)
    {
        $listar      = Libreria::getParam($request->input('listar'), 'NO');
        $id          = $request->input('id');
        $modo        = $request->input('modo', '');
        $entidad     = 'Historia222';
        $historia    = Historia::find($id);
        $sucursal_id = Session::get('sucursal_id');
        $user        = Auth::user();
        $formData    = array('historia.storehenfermeria');
        $formData    = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton       = 'Registrar';
        $dia         = date("d");
        $mes         = date("m");
        $ano         = date("Y");
        $dianaz      = date("d", strtotime($historia->persona->fechanacimiento));
        $mesnaz      = date("m", strtotime($historia->persona->fechanacimiento));
        $anonaz      = date("Y", strtotime($historia->persona->fechanacimiento));
        //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
        if (($mesnaz == $mes) && ($dianaz > $dia)) {
            $ano = ($ano - 1);}
        //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
        if ($mesnaz > $mes) {
            $ano = ($ano - 1);}
        //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
        $edad = ($ano - $anonaz);
        if ($historia->persona->fechanacimiento == null) {
            $edad = "-";
        }
        return view($this->folderview . '.henfermeria')->with(compact('historia', 'edad', 'formData', 'entidad', 'boton', 'listar', 'modo', 'user', 'id'));
    }

    public function storehenfermeria(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $Historia = Historia::find($request->input('id'));
        $dat      = array();
        $error    = DB::transaction(function () use ($request, $Historia, &$dat) {

            $ocupacion                     = $request->input('ocupacion') == null || $request->input('ocupacion') == "" ? null : $request->input('ocupacion');
            $ocupacion                     = $request->input("ocupacion") == null || $request->input("ocupacion") == "" ? null : $request->input("ocupacion");
            $antecedentesfamiliares        = $request->input("antecedentesfamiliares") == null || $request->input("antecedentesfamiliares") == "" ? null : $request->input("antecedentesfamiliares");
            $antecedentesmedicamentos      = $request->input("antecedentesmedicamentos") == null || $request->input("antecedentesmedicamentos") == "" ? null : $request->input("antecedentesmedicamentos");
            $antecedentesfarma             = $request->input("antecedentesfarma") == null || $request->input("antecedentesfarma") == "" ? null : $request->input("antecedentesfarma");
            $antecedentestransfusiones     = $request->input("antecedentestransfusiones") == null || $request->input("antecedentestransfusiones") == "" ? null : $request->input("antecedentestransfusiones");
            $antecedenteshospital          = $request->input("antecedenteshospital") == null || $request->input("antecedenteshospital") == "" ? null : $request->input("antecedenteshospital");
            $txtPresionArterial1           = $request->input("txtPresionArterial1") == null || $request->input("txtPresionArterial1") == "" ? null : $request->input("txtPresionArterial1");
            $txtPresionArterial2           = $request->input("txtPresionArterial2") == null || $request->input("txtPresionArterial2") == "" ? null : $request->input("txtPresionArterial2");
            $txtFC                         = $request->input("txtFC") == null || $request->input("txtFC") == "" ? null : $request->input("txtFC");
            $txtFR                         = $request->input("txtFR") == null || $request->input("txtFR") == "" ? null : $request->input("txtFR");
            $txtTemperatura                = $request->input("txtTemperatura") == null || $request->input("txtTemperatura") == "" ? null : $request->input("txtTemperatura");
            $txtacbe2                      = $request->input("txtacbe2") == null || $request->input("txtacbe2") == "" ? null : $request->input("txtacbe2");
            $txtaghbs2                     = $request->input("txtaghbs2") == null || $request->input("txtaghbs2") == "" ? null : $request->input("txtaghbs2");
            $txtachcv                      = $request->input("txtachcv") == null || $request->input("txtachcv") == "" ? null : $request->input("txtachcv");
            $txtvih                        = $request->input("txtvih") == null || $request->input("txtvih") == "" ? null : $request->input("txtvih");
            $txtvdrl                       = $request->input("txtvdrl") == null || $request->input("txtvdrl") == "" ? null : $request->input("txtvdrl");
            $txtFechaCantDosis1            = $request->input("txtFechaCantDosis1") == null || $request->input("txtFechaCantDosis1") == "" ? null : $request->input("txtFechaCantDosis1");
            $txtFechaCantDosis2            = $request->input("txtFechaCantDosis2") == null || $request->input("txtFechaCantDosis2") == "" ? null : $request->input("txtFechaCantDosis2");
            $txtFechaCantDosis3            = $request->input("txtFechaCantDosis3") == null || $request->input("txtFechaCantDosis3") == "" ? null : $request->input("txtFechaCantDosis3");
            $txtFechaCantDosis4            = $request->input("txtFechaCantDosis4") == null || $request->input("txtFechaCantDosis4") == "" ? null : $request->input("txtFechaCantDosis4");
            $txttipoaccesovascular2        = $request->input("txttipoaccesovascular2") == null || $request->input("txttipoaccesovascular2") == "" ? null : $request->input("txttipoaccesovascular2");
            $txtubicacionaccesovascular2   = $request->input("txtubicacionaccesovascular2") == null || $request->input("txtubicacionaccesovascular2") == "" ? null : $request->input("txtubicacionaccesovascular2");
            $txtfechainicioaccesovascular2 = $request->input("txtfechainicioaccesovascular2") == null || $request->input("txtfechainicioaccesovascular2") == "" ? null : $request->input("txtfechainicioaccesovascular2");
            $txtpresenciathrill2           = $request->input("txtpresenciathrill2") == null || $request->input("txtpresenciathrill2") == "" ? null : $request->input("txtpresenciathrill2");
            $txtpresenciasoplo2            = $request->input("txtpresenciasoplo2") == null || $request->input("txtpresenciasoplo2") == "" ? null : $request->input("txtpresenciasoplo2");
            $txtpresenciapseudo2           = $request->input("txtpresenciapseudo2") == null || $request->input("txtpresenciapseudo2") == "" ? null : $request->input("txtpresenciapseudo2");
            $txtcondicioneshigiene2        = $request->input("txtcondicioneshigiene2") == null || $request->input("txtcondicioneshigiene2") == "" ? null : $request->input("txtcondicioneshigiene2");
            $txtformulacion2               = $request->input("txtformulacion2") == null || $request->input("txtformulacion2") == "" ? null : $request->input("txtformulacion2");
            $txtformulacion22              = $request->input("txtformulacion22") == null || $request->input("txtformulacion22") == "" ? null : $request->input("txtformulacion22");
            $txtplaneacion2                = $request->input("txtplaneacion2") == null || $request->input("txtplaneacion2") == "" ? null : $request->input("txtplaneacion2");
            $txtidentifica2                = $request->input("txtidentifica2") == null || $request->input("txtidentifica2") == "" ? null : $request->input("txtidentifica2");
            $txtidentifica22               = $request->input("txtidentifica22") == null || $request->input("txtidentifica22") == "" ? null : $request->input("txtidentifica22");
            $txtconocimiento2              = $request->input("txtconocimiento2") == null || $request->input("txtconocimiento2") == "" ? null : $request->input("txtconocimiento2");
            $txtconocimiento22             = $request->input("txtconocimiento22") == null || $request->input("txtconocimiento22") == "" ? null : $request->input("txtconocimiento22");
            $txtedema2                     = $request->input("txtedema2") == null || $request->input("txtedema2") == "" ? null : $request->input("txtedema2");
            $txtconocimiento23             = $request->input("txtconocimiento23") == null || $request->input("txtconocimiento23") == "" ? null : $request->input("txtconocimiento23");
            $txtmedicamentos2              = $request->input("txtmedicamentos2") == null || $request->input("txtmedicamentos2") == "" ? null : $request->input("txtmedicamentos2");
            $txtconocimiento24             = $request->input("txtconocimiento24") == null || $request->input("txtconocimiento24") == "" ? null : $request->input("txtconocimiento24");
            $txtplaneacion22               = $request->input("txtplaneacion22") == null || $request->input("txtplaneacion22") == "" ? null : $request->input("txtplaneacion22");

            $fechaformatoenfermeria = $request->input("fechaformatoenfermeria") == null || $request->input("fechaformatoenfermeria") == "" ? null : $request->input("fechaformatoenfermeria");

            $horaformatoenfermeria = $request->input("horaformatoenfermeria") == null || $request->input("horaformatoenfermeria") == "" ? null : $request->input("horaformatoenfermeria");

            $date = date("Y-m-d H:i:s", strtotime($fechaformatoenfermeria . " " . $horaformatoenfermeria));

            //////////////////////////////////

            $Historia->ocupacion                     = $ocupacion;
            $Historia->antecedentesfamiliares        = $antecedentesfamiliares;
            $Historia->antecedentesmedicamentos      = $antecedentesmedicamentos;
            $Historia->antecedentesfarma             = $antecedentesfarma;
            $Historia->antecedentestransfusiones     = $antecedentestransfusiones;
            $Historia->antecedenteshospital          = $antecedenteshospital;
            $Historia->txtPresionArterial1           = $txtPresionArterial1;
            $Historia->txtPresionArterial2           = $txtPresionArterial2;
            $Historia->txtFC                         = $txtFC;
            $Historia->txtFR                         = $txtFR;
            $Historia->txtTemperatura                = $txtTemperatura;
            $Historia->txtacbe2                      = $txtacbe2;
            $Historia->txtaghbs2                     = $txtaghbs2;
            $Historia->txtachcv                      = $txtachcv;
            $Historia->txtvih                        = $txtvih;
            $Historia->txtvdrl                       = $txtvdrl;
            $Historia->txtFechaCantDosis1            = $txtFechaCantDosis1;
            $Historia->txtFechaCantDosis2            = $txtFechaCantDosis2;
            $Historia->txtFechaCantDosis3            = $txtFechaCantDosis3;
            $Historia->txtFechaCantDosis4            = $txtFechaCantDosis4;
            $Historia->txttipoaccesovascular2        = $txttipoaccesovascular2;
            $Historia->txtubicacionaccesovascular2   = $txtubicacionaccesovascular2;
            $Historia->txtfechainicioaccesovascular2 = $txtfechainicioaccesovascular2;
            $Historia->txtpresenciathrill2           = $txtpresenciathrill2;
            $Historia->txtpresenciasoplo2            = $txtpresenciasoplo2;
            $Historia->txtpresenciapseudo2           = $txtpresenciapseudo2;
            $Historia->txtcondicioneshigiene2        = $txtcondicioneshigiene2;
            $Historia->txtformulacion2               = $txtformulacion2;
            $Historia->txtformulacion22              = $txtformulacion22;
            $Historia->txtplaneacion2                = $txtplaneacion2;
            $Historia->txtidentifica2                = $txtidentifica2;
            $Historia->txtidentifica22               = $txtidentifica22;
            $Historia->txtconocimiento2              = $txtconocimiento2;
            $Historia->txtconocimiento22             = $txtconocimiento22;
            $Historia->txtedema2                     = $txtedema2;
            $Historia->txtconocimiento23             = $txtconocimiento23;
            $Historia->txtmedicamentos2              = $txtmedicamentos2;
            $Historia->txtconocimiento24             = $txtconocimiento24;
            $Historia->estado3                       = 'S'; //HENFERMERIA ACTIVADA
            $Historia->fechaformatoenfermeria        = $date;
            $Historia->txtplaneacion22               = $txtplaneacion22;
            $Historia->save();

            $dat[0] = array("respuesta" => "OK", "id" => $Historia->id, "paciente" => $Historia->persona->apellidopaterno . ' ' . $Historia->persona->apellidomaterno . ' ' . $Historia->persona->nombres, "historia" => $Historia->numero, "person_id" => $Historia->person_id, "tipopaciente" => $Historia->tipopaciente);
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function observaciones($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $modelo   = Historia::find($id);
        $entidad  = 'Historia';
        $paciente = $modelo->persona->apellidopaterno . " " . $modelo->persona->apellidomaterno . " " . $modelo->persona->nombres;
        $numero   = $modelo->numero;
        $formData = array('route' => array('historia.guardarobservaciones', "id=".$id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Dar de baja';
        return view($this->folderview . '.observaciones')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'numero', 'paciente'));
    }

    public function guardarobservaciones(Request $request) {
        $existe = Libreria::verificarExistencia($request->id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $error    = DB::transaction(function () use ($request) {
            $historia = Historia::find($request->id);
            $historia->observaciones = $request->observaciones;
            $historia->save();
        });
        return is_null($error) ? "OK" : $error;
    }
}
