<?php

namespace App\Http\Controllers;

use App\ConsultaNefrologica;
use App\HistoriaClinica;
use App\Historia;
use App\Http\Controllers\Controller;
use App\Librerias\Libreria;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultaNefrologicaController extends Controller
{

    protected $folderview      = 'app.consultanefrologica';
    protected $tituloAdmin     = 'Consulta Nefrologica SIS';
    protected $tituloRegistrar = 'Registrar consultanefrologica';
    protected $tituloModificar = 'Modificar consultanefrologica';
    protected $tituloEliminar  = 'Eliminar consultanefrologica';
    protected $rutas           = array(
        'create' => 'consultanefrologica.create',
        'edit'   => 'consultanefrologica.edit',
        'delete' => 'consultanefrologica.eliminar',
        'search' => 'consultanefrologica.buscar',
        'index'  => 'consultanefrologica.index',
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

        date_default_timezone_set('America/Lima');
        $mes          = date("m");
        $pagina       = $request->input('page');
        $filas        = $request->input('filas');
        $entidad      = 'ConsultaNefrologica';
        $nombre       = Libreria::getParam($request->input('nombre'));
        $baja         = Libreria::getParam($request->input('baja'));
        $estado       = Libreria::getParam($request->input('estado'));
        $examenes     = Libreria::getParam($request->input('examenes'));
        $programacion = Libreria::getParam($request->input('programacion'));
        $medico_id    = Libreria::getParam($request->input('medico_id2'));
        $estado2      = Libreria::getParam($request->input('estado2'));
        $numero       = Libreria::getParam($request->input('numero'));
        $messs        = Libreria::getParam($request->input('messs'));
        $anooo        = Libreria::getParam($request->input('anooo'));
        $this->consultasMensuales($messs, $anooo);
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%' . strtoupper($nombre) . '%')
            ->where('historia.convenio_id', '=', 1)
        //->where('historia.baja', '!=', "S")
            ->where('historia.numero', 'LIKE', '%' . $numero . '%')
            ->select('person.nombres', 'person.apellidopaterno', 'person.apellidomaterno', 'historia.numero', 'historia.id as hid', 'person.dni', 'person.id as pid', 'c.id as cid', "c.doctor_id", "historia.baja", "historia.fallecido", "historia.fechafallecido")
            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $messs)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anooo)
            ->orderBy(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'));

        if ($baja == "S") {
            $resultado = $resultado->where('historia.baja', '=', "S");
        } else {
            $resultado = $resultado->where('historia.baja', '!=', "S");
        }

        if ($estado == "1") {
            $resultado = $resultado->whereNotNull("c.doctor_id");
        } elseif ($estado == "2") {
            $resultado = $resultado->whereNull("c.doctor_id");
        }
        if ($estado2 == "1") {
            $resultado = $resultado->whereNotNull("c.numeroformato");
        } elseif ($estado2 == "2") {
            $resultado = $resultado->whereNull("c.numeroformato");
        }
        if ($examenes == "1") {
            $resultado = $resultado->whereNotNull("c.estadoexamen");
        } elseif ($examenes == "2") {
            $resultado = $resultado->whereNull("c.estadoexamen");
        }
        if ($programacion == "1") {
            $resultado = $resultado->whereNotNull("c.estadoprogramacion");
        } elseif ($programacion == "2") {
            $resultado = $resultado->whereNull("c.estadoprogramacion");
        }

        if ($medico_id != "") {
            $resultado = $resultado->where("c.doctor_id", "=", $medico_id);
        }
        $user = Auth::user();

        $lista = $resultado->get();

        $cabecera   = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Historia', 'numero' => '1');
        $cabecera[] = array('valor' => 'DNI/CE', 'numero' => '1');
        $cabecera[] = array('valor' => 'Nombre', 'numero' => '1');
        if ($user->usertype_id !== 31) {
            $cabecera[] = array('valor' => 'Médico', 'numero' => '1');
        }
        if ($user->usertype_id == 2 || $user->usertype_id == 1) {
            $cabecera[] = array('valor' => 'Examenes', 'numero' => '1');
            $cabecera[] = array('valor' => 'Mes actual', 'numero' => '1');
            $cabecera[] = array('valor' => 'Sgte. mes', 'numero' => '1');
        }
        if ($user->usertype_id == 28 || $user->usertype_id == 29) {
            $cabecera[] = array('valor' => 'Mes actual', 'numero' => '1');
            $cabecera[] = array('valor' => 'Sgte. mes', 'numero' => '1');
        }
        //$cabecera[]       = array('valor' => 'Consulta Nefrológica', 'numero' => '1');
        if ($user->usertype_id !== 2) {

            $cabecera[] = array('valor' => 'Reporte del Mes', 'numero' => '1');
            $cabecera[] = array('valor' => 'Progr. Medicam.', 'numero' => '1');
        }

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
            return view($this->folderview . '.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'messs', 'anooo'));
        }
        return view($this->folderview . '.list')->with(compact('lista', 'entidad', 'messs', 'anooo'));
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
        $messs            = array(
            "1"  => "ENERO",
            "2"  => "FEBRERO",
            "3"  => "MARZO",
            "4"  => "ABRIL",
            "5"  => "MAYO",
            "6"  => "JUNIO",
            "7"  => "JULIO",
            "8"  => "AGOSTO",
            "9"  => "SETIEMBRE",
            "10" => "OCTUBRE",
            "11" => "NOVIEMBRE",
            "12" => "DICIEMBRE",
        );

        $docts = Person::join('especialidad', 'especialidad.id', '=', 'person.especialidad_id')
            ->where('workertype_id', '=', 1)
            ->orderBy('apellidopaterno', 'ASC')
            ->select("person.id", "person.apellidopaterno", "person.apellidomaterno", "person.nombres")
            ->get();

        return view($this->folderview . '.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', "messs", "docts"));
    }

    public function cambiarDoctor(Request $request)
    {
        $id        = $request->input("id");
        $doctor_id = $request->input("doctor_id");
        return view($this->folderview . '.cambiarDoctor')->with(compact('id', 'doctor_id'));
    }

    public function resultados(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = date('m');
        $cid       = $request->input("cid");
        $listar    = Libreria::getParam($request->input('listar'), 'NO');
        $pid       = $request->input('pid');
        $tipo      = $request->input('situacion');
        $tipos     = array('NUEVO' => 'NUEVO', 'MENSUAL' => 'MENSUALES', 'BIMENSUAL' => 'MENSUALES + BIMENSUALES', 'TRIMESTRAL' => 'MENSUALES + TRIMESTRALES', 'SEMESTRAL' => 'MENS. + BIMENS. + TRIMES. + SEMES.');
        $historia  = Historia::where('person_id', '=', $pid)->first();
        $entidad   = 'HC';
        $c1        = ConsultaNefrologica::find($cid);
        $situacion = "NUEVO";
        switch ($c1->situacion) {
            case "M":
                $situacion = "MENSUAL";
                break;
            case "M-B":
                $situacion = "BIMENSUAL";
                break;
            case "M-T":
                $situacion = "TRIMESTRAL";
                break;
            case "M-B-T-S":
                $situacion = "SEMESTRAL";
                break;
        }
        $situacion2 = "NUEVO";
        switch ($c1->situacion2) {
            case "M":
                $situacion2 = "MENSUAL";
                break;
            case "M-B":
                $situacion2 = "BIMENSUAL";
                break;
            case "M-T":
                $situacion2 = "TRIMESTRAL";
                break;
            case "M-B-T-S":
                $situacion2 = "SEMESTRAL";
                break;
        }

        $hc       = $c1;

        $fechasHD = "";
        $ppre = "";
        $ppos = "";
        $horas = "";
        $atencion_id = "";
        $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
            ->where("historia.person_id", "=", $pid)
            ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha)))
            ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $request->input("anillo"))
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

        $atencionesMensuales = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
            ->where("historia.person_id", "=", $pid)
            ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha)))
            ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $request->input("anillo"))
            ->where("historiaclinica.estado", "!=", "C")
            ->select("historiaclinica.id", "historiaclinica.fecha_atencion")
            ->get();
        foreach ($atencionesMensuales as $aM) {
            $fechasHD .= '<option value="' . $aM->id . '">' . date("d-m-Y", strtotime($aM->fecha_atencion)) . '</option>';
        }

        //Analizo siguiente tio de consulta del siguiente mes:

        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';
        return view($this->folderview . '.resultados')->with(compact('tipo', 'tipos', 'hc', 'historia', 'formData', 'entidad', 'boton', 'listar', 'situacion', 'situacion2', "fechasHD", "ppre", "ppos", "horas", "atencion_id"));
    }

    public function storeresultados(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id      = $request->input('id1');
        $tipo    = $request->input('txtTipo');
        $tipo2   = $request->input('txtTipo2');
        $numcita = $request->input("txtNumCita");
        $dm      = $request->input('txtDatosMensuales');
        $listar  = Libreria::getParam($request->input('listar'), 'NO');
        $dat     = '';
        $error   = DB::transaction(function () use ($request, $id, $tipo, $tipo2, $dm, &$dat, $numcita) {
            $c1 = ConsultaNefrologica::find($id);
            //SETEO DATOS EN NULL
            $c1->txtEli     = null;
            $c1->txtDet     = null;
            $c1->txtDet2    = null;
            $c1->txtDet3    = null;
            $c1->txtDet4    = null;
            $c1->txtUre     = null;
            $c1->txtUre2    = null;
            $c1->txtCre     = null;
            $c1->txtHem     = null;
            $c1->txtDos     = null;
            $c1->txtEle     = null;
            $c1->txtSodio   = null;
            $c1->txtPotasio = null;
            $c1->txtCloro   = null;
            $c1->txtFos     = null;
            $c1->txtCal     = null;
            $c1->txtPro     = null;
            $c1->txtFos2    = null;
            $c1->txtTgo     = null;
            $c1->txtTgp     = null;
            $c1->txtPru     = null;
            $c1->txtPar     = null;
            $c1->txtHie     = null;
            $c1->txtFer     = null;
            $c1->txtSat     = null;
            $c1->txtAlbu    = null;
            $c1->txtGlobu   = null;
            $c1->txtTransfe = null;
            $c1->save();

            $c1                    = ConsultaNefrologica::find($id);
            $c1->txtDatosMensuales = $dm;

            $cadenaresultados = "";
            if ($tipo == 'NUEVO') {

                $c1->txtEli = $request->input('txtEliN');
                $cadenaresultados .= "1," . $request->input('txtEliN') . ";";
                $c1->txtDet = $request->input('txtDetN');
                $cadenaresultados .= "2," . $request->input('txtDetN') . ";";
                $c1->txtDet2 = $request->input('txtDet2N');
                $cadenaresultados .= "3," . $request->input('txtDet2N') . ";";
                $c1->txtDet3 = $request->input('txtDet3N');
                $cadenaresultados .= "4," . $request->input('txtDet3N') . ";";
                $c1->txtDet4 = $request->input('txtDet4N');
                $cadenaresultados .= "5," . $request->input('txtDet4N') . ";";
                $c1->txtPru = $request->input('txtPruN');
                $cadenaresultados .= "21," . $request->input('txtPruN') . ";";

                if ($dm == "SI") {
                    $c1->txtUre = $request->input('txtUre');
                    $cadenaresultados .= "6," . $request->input('txtUre') . ";";
                    $c1->txtUre2 = $request->input('txtUre2');
                    $cadenaresultados .= "7," . $request->input('txtUre2') . ";";
                    $c1->txtCre = $request->input('txtCre');
                    $cadenaresultados .= "8," . $request->input('txtCre') . ";";
                    $c1->txtHem = $request->input('txtHem');
                    $cadenaresultados .= "9," . $request->input('txtHem') . ";";
                    $c1->txtDos = $request->input('txtDos');
                    $cadenaresultados .= "10," . $request->input('txtDos') . ";";
                    $c1->txtEle = $request->input('txtEle');
                    $cadenaresultados .= "11," . $request->input('txtEle') . ";";
                    $c1->txtSodio = $request->input('txtSodio');
                    $cadenaresultados .= "12," . $request->input('txtSodio') . ";";
                    $c1->txtPotasio = $request->input('txtPotasio');
                    $cadenaresultados .= "13," . $request->input('txtPotasio') . ";";
                    $c1->txtCloro = $request->input('txtCloro');
                    $cadenaresultados .= "14," . $request->input('txtCloro') . ";";
                    $c1->txtFos = $request->input('txtFos');
                    $cadenaresultados .= "15," . $request->input('txtFos') . ";";
                    $c1->txtCal = $request->input('txtCal');
                    $cadenaresultados .= "16," . $request->input('txtCal') . ";";
                }

                /*$c1->txtUre = $request->input('txtUre');
            $cadenaresultados.="6,".$request->input('txtUre').";";
            $c1->txtUre2 = $request->input('txtUre2');
            $cadenaresultados.="7,".$request->input('txtUre2').";";
            $c1->txtCre = $request->input('txtCre');
            $cadenaresultados.="8,".$request->input('txtCre').";";
            $c1->txtHem = $request->input('txtHem');
            $cadenaresultados.="9,".$request->input('txtHem').";";
            $c1->txtDos = $request->input('txtDos');
            $cadenaresultados.="10,".$request->input('txtDos').";";
            $c1->txtEle = $request->input('txtEle');
            $cadenaresultados.="11,".$request->input('txtEle').";";
            $c1->txtSodio = $request->input('txtSodio');
            $cadenaresultados.="12,".$request->input('txtSodio').";";
            $c1->txtPotasio = $request->input('txtPotasio');
            $cadenaresultados.="13,".$request->input('txtPotasio').";";
            $c1->txtCloro = $request->input('txtCloro');
            $cadenaresultados.="14,".$request->input('txtCloro').";";
            $c1->txtFos = $request->input('txtFos');
            $cadenaresultados.="15,".$request->input('txtFos').";";
            $c1->txtCal = $request->input('txtCal');
            $cadenaresultados.="16,".$request->input('txtCal').";";
            $c1->txtPro = $request->input('txtPro');
            $cadenaresultados.="17,".$request->input('txtPro').";";
            $c1->txtFos2 = $request->input('txtFos2');
            $cadenaresultados.="18,".$request->input('txtFos2').";";
            $c1->txtTgo = $request->input('txtTgo');
            $cadenaresultados.="19,".$request->input('txtTgo').";";
            $c1->txtTgp = $request->input('txtTgp');
            $cadenaresultados.="20,".$request->input('txtTgp').";";
            $c1->txtPru = $request->input('txtPru');
            $cadenaresultados.="21,".$request->input('txtPru').";";
            $c1->txtPar = $request->input('txtPar');
            $cadenaresultados.="22,".$request->input('txtPar').";";
            $c1->txtHie = $request->input('txtHie');
            $cadenaresultados.="23,".$request->input('txtHie').";";
            $c1->txtFer = $request->input('txtFer');
            $cadenaresultados.="24,".$request->input('txtFer').";";
            $c1->txtSat = $request->input('txtSat');
            $cadenaresultados.="25,".$request->input('txtSat').";";
            $c1->txtAlbu = $request->input('txtAlbu');
            $cadenaresultados.="26,".$request->input('txtAlbu').";";
            $c1->txtGlobu = $request->input('txtGlobu');
            $cadenaresultados.="27,".$request->input('txtGlobu').";";
            $c1->txtTransfe = $request->input('txtTransfe');
            $cadenaresultados.="28,".$request->input('txtTransfe').";";*/

            } else {

                if ($tipo == 'SEMESTRAL') {

                    $c1->txtEli = $request->input('txtEli');
                    $cadenaresultados .= "1," . $request->input('txtEli') . ";";
                    $c1->txtDet = $request->input('txtDet');
                    $cadenaresultados .= "2," . $request->input('txtDet') . ";";
                    $c1->txtDet2 = $request->input('txtDet2');
                    $cadenaresultados .= "3," . $request->input('txtDet2') . ";";
                    $c1->txtDet3 = $request->input('txtDet3');
                    $cadenaresultados .= "4," . $request->input('txtDet3') . ";";
                    $c1->txtDet4 = $request->input('txtDet4');
                    $cadenaresultados .= "5," . $request->input('txtDet4') . ";";

                }

                $c1->txtUre = $request->input('txtUre');
                $cadenaresultados .= "6," . $request->input('txtUre') . ";";
                $c1->txtUre2 = $request->input('txtUre2');
                $cadenaresultados .= "7," . $request->input('txtUre2') . ";";
                $c1->txtCre = $request->input('txtCre');
                $cadenaresultados .= "8," . $request->input('txtCre') . ";";
                $c1->txtHem = $request->input('txtHem');
                $cadenaresultados .= "9," . $request->input('txtHem') . ";";
                $c1->txtDos = $request->input('txtDos');
                $cadenaresultados .= "10," . $request->input('txtDos') . ";";
                $c1->txtEle = $request->input('txtEle');
                $cadenaresultados .= "11," . $request->input('txtEle') . ";";
                $c1->txtSodio = $request->input('txtSodio');
                $cadenaresultados .= "12," . $request->input('txtSodio') . ";";
                $c1->txtPotasio = $request->input('txtPotasio');
                $cadenaresultados .= "13," . $request->input('txtPotasio') . ";";
                $c1->txtCloro = $request->input('txtCloro');
                $cadenaresultados .= "14," . $request->input('txtCloro') . ";";
                $c1->txtFos = $request->input('txtFos');
                $cadenaresultados .= "15," . $request->input('txtFos') . ";";
                $c1->txtCal = $request->input('txtCal');
                $cadenaresultados .= "16," . $request->input('txtCal') . ";";

                if ($tipo == 'TRIMESTRAL' || $tipo == 'SEMESTRAL') {

                    $c1->txtPro = $request->input('txtPro');
                    $cadenaresultados .= "17," . $request->input('txtPro') . ";";
                    $c1->txtFos2 = $request->input('txtFos2');
                    $cadenaresultados .= "18," . $request->input('txtFos2') . ";";

                }if ($tipo == 'BIMENSUAL' || $tipo == 'SEMESTRAL') {

                    $c1->txtTgo = $request->input('txtTgo');
                    $cadenaresultados .= "19," . $request->input('txtTgo') . ";";
                    $c1->txtTgp = $request->input('txtTgp');
                    $cadenaresultados .= "20," . $request->input('txtTgp') . ";";

                }if ($tipo == 'SEMESTRAL') {

                    $c1->txtPru = $request->input('txtPru');
                    $cadenaresultados .= "21," . $request->input('txtPru') . ";";
                    $c1->txtPar = $request->input('txtPar');
                    $cadenaresultados .= "22," . $request->input('txtPar') . ";";
                    $c1->txtHie = $request->input('txtHie');
                    $cadenaresultados .= "23," . $request->input('txtHie') . ";";
                    $c1->txtFer = $request->input('txtFer');
                    $cadenaresultados .= "24," . $request->input('txtFer') . ";";
                    $c1->txtSat = $request->input('txtSat');
                    $cadenaresultados .= "25," . $request->input('txtSat') . ";";
                    $c1->txtAlbu = $request->input('txtAlbu');
                    $cadenaresultados .= "26," . $request->input('txtAlbu') . ";";
                    $c1->txtGlobu = $request->input('txtGlobu');
                    $cadenaresultados .= "27," . $request->input('txtGlobu') . ";";
                    $c1->txtTransfe = $request->input('txtTransfe');
                    $cadenaresultados .= "28," . $request->input('txtTransfe') . ";";

                }if ($tipo == 'TRIMESTRAL') {
                    $c1->txtAlbu = $request->input('txtAlbu');
                    $cadenaresultados .= "26," . $request->input('txtAlbu') . ";";
                    $c1->txtGlobu = $request->input('txtGlobu');
                    $cadenaresultados .= "27," . $request->input('txtGlobu') . ";";
                }
            }

            $tipodatos = 0;
            $situacion = "N";
            switch ($tipo) {
                case "MENSUAL":
                    $situacion = "M";
                    $tipodatos = 1;
                    break;
                case "BIMENSUAL":
                    $situacion = "M-B";
                    $tipodatos = 2;
                    break;
                case "TRIMESTRAL":
                    $situacion = "M-T";
                    $tipodatos = 3;
                    break;
                case "SEMESTRAL":
                    $situacion = "M-B-T-S";
                    $tipodatos = 0;
                    break;
            }
            $situacion2 = "N";
            switch ($tipo2) {
                case "MENSUAL":
                    $situacion2 = "M";
                    break;
                case "BIMENSUAL":
                    $situacion2 = "M-B";
                    break;
                case "TRIMESTRAL":
                    $situacion2 = "M-T";
                    break;
                case "SEMESTRAL":
                    $situacion2 = "M-B-T-S";
                    break;
            }

            if ($tipo == "MENSUAL") {
                if ($tipo2 == "SEMESTRAL") {
                    $tipodatos = 5;
                }
            } else if ($tipo == "BIMENSUAL") {
                if ($tipo2 == "MENSUAL") {
                    $tipodatos = 4;
                }
            }

            $c1->txtTipoDatos     = $tipodatos;
            $c1->txtNumCita       = $numcita;
            $c1->cadenaresultados = $cadenaresultados;
            $c1->estadoexamen     = 1;
            $c1->situacion        = $situacion;
            $c1->situacion2       = $situacion2;
            $c1->save();

            //PARA LOS DATOS DE KTV
            $txtFechaKTV = $request->input("txtFechaKTV"); //CONTIENE EL ID DE LA HISTORIA A EDITAR
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
                    $aM->save();
                }
            }

            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function storeconfiguracionmensual(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id      = $request->input('id1');
        $tipo    = $request->input('txtTipo');
        $tipo2   = $request->input('txtTipo2');
        $numcita = $request->input("txtNumCita");
        $dm      = $request->input('txtDatosMensuales');
        $listar  = Libreria::getParam($request->input('listar'), 'NO');
        $dat     = '';
        $error   = DB::transaction(function () use ($request, $id, $tipo, $tipo2, $dm, &$dat, $numcita) {

            $c1                    = ConsultaNefrologica::find($id);
            $c1->txtDatosMensuales = $dm;

            $tipodatos = 0;
            $situacion = "N";
            switch ($tipo) {
                case "MENSUAL":
                    $situacion = "M";
                    $tipodatos = 1;
                    break;
                case "BIMENSUAL":
                    $situacion = "M-B";
                    $tipodatos = 2;
                    break;
                case "TRIMESTRAL":
                    $situacion = "M-T";
                    $tipodatos = 3;
                    break;
                case "SEMESTRAL":
                    $situacion = "M-B-T-S";
                    $tipodatos = 0;
                    break;
            }
            $situacion2 = "N";
            switch ($tipo2) {
                case "MENSUAL":
                    $situacion2 = "M";
                    break;
                case "BIMENSUAL":
                    $situacion2 = "M-B";
                    break;
                case "TRIMESTRAL":
                    $situacion2 = "M-T";
                    break;
                case "SEMESTRAL":
                    $situacion2 = "M-B-T-S";
                    break;
            }

            if ($tipo == "MENSUAL") {
                if ($tipo2 == "SEMESTRAL") {
                    $tipodatos = 5;
                }
            } else if ($tipo == "BIMENSUAL") {
                if ($tipo2 == "MENSUAL") {
                    $tipodatos = 4;
                }
            }

            $c1->txtTipoDatos = $tipodatos;
            $c1->situacion    = $situacion;
            $c1->situacion2   = $situacion2;
            $c1->save();

            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function programarmedicamentos(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $pid      = $request->input('pid');
        $tipo     = $request->input('situacion');
        $tipos    = array('NUEVO' => 'NUEVO', 'MENSUAL' => 'MENSUALES', 'BIMENSUAL' => 'MENSUALES + BIMENSUALES', 'TRIMESTRAL' => 'MENSUALES + TRIMESTRALES', 'SEMESTRAL' => 'MENS. + BIMENS. + TRIMES. + SEMES.');
        $historia = Historia::where('person_id', '=', $pid)->first();
        $entidad  = 'HC';
        $c1       = ConsultaNefrologica::find($request->input("cid"));
        $hc       = $c1;
        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';
        return view($this->folderview . '.programarmedicamentos')->with(compact('tipo', 'tipos', 'hc', 'historia', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function storeprogramarmedicamentos(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id     = $request->input('idcn');
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $dat    = '';
        $error  = DB::transaction(function () use ($request, $id, &$dat) {
            $c1                     = ConsultaNefrologica::find($id);
            $c1->cadenaepo          = $request->input('cadenaepo');
            $c1->cadenahierro       = $request->input('cadenahierro');
            $c1->cadenavita         = $request->input('cadenavita');
            $c1->estadoprogramacion = 1;
            $c1->save();
            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function consultasMensuales($mes, $ano)
    {

        date_default_timezone_set('America/Lima');

        $lista = Historia::select('id', 'person_id')->where('historia.convenio_id', '=', 1)->where('historia.baja', '!=', "S")->get();

        $dia = 1;

        //Comprobación de consultas

        foreach ($lista as $row) {
            $consultas = ConsultaNefrologica::select('id')->where('persona_id', '=', $row->person_id)->select("id")->get();
            if (count($consultas) === 0) {
                //BAZAL
                $cons               = new ConsultaNefrologica();
                $cons->fecha        = date('Y-m-d', strtotime($ano . "-" . $mes . "-" . $dia));
                $cons->situacion    = 'N';
                $cons->situacion2   = 'M';
                $cons->txtNumCita   = 0;
                $cons->txtTipoDatos = 0;
                $cons->persona_id   = $row->person_id;
                $cons->save();
            } else {
                //comruebo que no existe la consutla mensual
                $c1 = ConsultaNefrologica::where('persona_id', '=', $row->person_id)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $ano)->first();

                /*if($c1===NULL) {
                //TOMAMOS LA ÚLTIMA CONSULTA NEFROLÓGICA
                $cons = new ConsultaNefrologica();

                $c2 = ConsultaNefrologica::where('persona_id', '=', $row->person_id)->orderBy('fecha', 'DESC')->first();
                if($c2!==NULL) {
                $sitanterioractual = $c2->situacion2;
                $cons->fecha = date('Y-m-d');
                $cons->persona_id = $row->person_id;

                $cons->situacion = $c2->situacion2;

                //Busco la situacion del siguiente mes

                if($sitanterioractual == 'BIMENSUAL') {
                //comruebo la siguiente situacion trim o sem
                //Dos ultimas consultasnefrologicas
                $c3 = ConsultaNefrologica::where('persona_id', '=', $row->person_id)->orderBy('fecha', 'DESC')->limit(2)->get();
                $cons->situacion2 = 'TRIMESTRAL';
                if(!empty($c3[1])) {
                $cons->situacion2 = 'TRIMESTRAL';
                if($c3[1]->situacion=="TRIMESTRAL") {
                $cons->situacion2 = 'SEMESTRAL';
                }
                }
                } else if($sitanterioractual == 'MENSUAL') {
                $cons->situacion2 = 'BIMENSUAL';
                } else if($sitanterioractual == 'TRIMESTRAL' || $sitanterioractual == 'SEMESTRAL') {
                $cons->situacion2 = 'MENSUAL';
                }
                $cons->save();
                }
                }*/
                if ($c1 === null) {
                    $c2 = ConsultaNefrologica::where('persona_id', '=', $row->person_id)->orderBy('fecha', 'DESC')->first();
                    if ($c2 !== null) {
                        $numcita    = $c2->txtNumCita + 1;
                        $situacion  = "M-B-T-S";
                        $situacion2 = "M";
                        $tdato      = $c2->txtTipoDatos;
                        $tipodatos  = ($tdato < 5 ? ($tdato + 1) : 0);
                        switch ($tipodatos) {
                            case 1:
                                $situacion  = "M";
                                $situacion2 = "M-B";
                                break;
                            case 2:
                                $situacion  = "M-B";
                                $situacion2 = "M-T";
                                break;
                            case 3:
                                $situacion  = "M-T";
                                $situacion2 = "M-B";
                                break;
                            case 4:
                                $situacion  = "M-B";
                                $situacion2 = "M";
                                break;
                            case 5:
                                $situacion  = "M";
                                $situacion2 = "M-B-T-S";
                                break;
                        }
                        $cons               = new ConsultaNefrologica();
                        $cons->fecha        = date('Y-m-d', strtotime($ano . "-" . $mes . "-" . $dia));
                        $cons->situacion    = $situacion;
                        $cons->situacion2   = $situacion2;
                        $cons->persona_id   = $row->person_id;
                        $cons->txtNumCita   = $numcita;
                        $cons->txtTipoDatos = $tipodatos;
                        $cons->save();
                    }
                }
            }
        }
    }

    public function cambiarDoctor2(Request $request)
    {
        $consulta            = ConsultaNefrologica::find($request->input("id"));
        $consulta->doctor_id = $request->input("doctor_id");
        $consulta->save();
    }

    public function confTodasConsultas(Request $request)
    {
        $mes  = (int) $request->input("mes");
        $anno = (int) $request->input("anno");

        $mes2  = $mes - 1;
        $anno2 = $anno;

        if ($mes == 1) {
            $mes2  = 12;
            $anno2 = $anno - 1;
        }

        //CONSULTAS ESTE MES

        $lista = ConsultaNefrologica::select("id", "persona_id", "situacion2")->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $anno)->get();

        //Comprobación de consultas

        foreach ($lista as $r) {
            $r2 = ConsultaNefrologica::select("situacion2")->where(DB::raw('MONTH(fecha)'), '=', $mes2)->where(DB::raw('YEAR(fecha)'), '=', $anno2)->where("persona_id", "=", $r->persona_id)->first();
            if ($r2 !== null) {
                $row            = ConsultaNefrologica::find($r->id);
                $row->situacion = $r2->situacion2;
                $row->save();
            }
        }
    }

    public function storemismosresultadosmesanterior(Request $request)
    {
        /*$dat     = '';
        $error   = DB::transaction(function () use ($request, &$dat) {

            $consultas = ConsultaNefrologica::where(DB::raw('MONTH(fecha)'), '=', 2)->where(DB::raw('YEAR(fecha)'), '=', 2020)->get();

            foreach ($consultas as $cm) {
                $dm      = $cm->txtDatosMensuales;
                //BUSCO CONSULTA DEL MES PASADO
                $cons = ConsultaNefrologica::where('persona_id', '=', $cm->persona_id)->where(DB::raw('MONTH(fecha)'), '=', 3)->where(DB::raw('YEAR(fecha)'), '=', 2020)->first();
                //SETEO LOS DATOS DE LA CONSULTA DEL MES PASADO PARA LA ACTUAL
                if($cons !== NULL) {
                    $cons->txtEli            = $cm->txtEli;
                    $cons->txtDet            = $cm->txtDet;
                    $cons->txtDet2           = $cm->txtDet2;
                    $cons->txtDet3           = $cm->txtDet3;
                    $cons->txtDet4           = $cm->txtDet4;
                    $cons->txtUre            = $cm->txtUre;
                    $cons->txtUre2           = $cm->txtUre2;
                    $cons->txtCre            = $cm->txtCre;
                    $cons->txtHem            = $cm->txtHem;
                    $cons->txtDos            = $cm->txtDos;
                    $cons->txtEle            = $cm->txtEle;
                    $cons->txtSodio          = $cm->txtSodio;
                    $cons->txtPotasio        = $cm->txtPotasio;
                    $cons->txtCloro          = $cm->txtCloro;
                    $cons->txtFos            = $cm->txtFos;
                    $cons->txtCal            = $cm->txtCal;
                    $cons->txtPro            = $cm->txtPro;
                    $cons->txtFos2           = $cm->txtFos2;
                    $cons->txtTgo            = $cm->txtTgo;
                    $cons->txtTgp            = $cm->txtTgp;
                    $cons->txtPru            = $cm->txtPru;
                    $cons->txtPar            = $cm->txtPar;
                    $cons->txtHie            = $cm->txtHie;
                    $cons->txtFer            = $cm->txtFer;
                    $cons->txtSat            = $cm->txtSat;
                    $cons->txtAlbu           = $cm->txtAlbu;
                    $cons->txtGlobu          = $cm->txtGlobu;
                    $cons->txtTransfe        = $cm->txtTransfe;
                    $cons->txtDatosMensuales = $cm->txtDatosMensuales;
                    $cons->txtTipoDatos      = $cm->txtTipoDatos;
                    $cons->txtNumCita        = $cm->txtNumCita + 1;
                    $cons->cadenaresultados  = $cm->cadenaresultados;
                    $cons->estadoexamen      = 1;
                    $cons->situacion         = $cm->situacion;
                    $cons->situacion2        = $cm->situacion2;
                    $cons->save();

                    $cm->situacion2 = $cm->situacion;
                    $cm->save();
                }
            }

            $dat = "OK";
        });
        echo is_null($error) ? $dat : $error;*/
    }
}
