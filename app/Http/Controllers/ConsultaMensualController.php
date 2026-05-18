<?php

namespace App\Http\Controllers;

use App\Caja;
use App\Cie;
use App\ConsultaNefrologica;
use App\ConsultaNutricion;
use App\ConsultaSaludMental;
use App\ConsultaServicioSocial;
use App\Detallemovimiento;
use App\Historia;
use App\HistoriaClinica;
use App\Http\Controllers\Controller;
use App\Librerias\Libreria;
use App\Movimiento;
use App\Person;
use App\Producto;
use DateTime;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ConsultaMensualController extends Controller
{

    protected $folderview      = 'app.consultamensual';
    protected $tituloAdmin     = 'ConsultaMensual';
    protected $tituloRegistrar = 'Registrar consultamensual';
    protected $tituloModificar = 'Modificar consultamensual';
    protected $tituloEliminar  = 'Eliminar consultamensual';
    protected $rutas           = array('create' => 'consultamensual.create',
        'edit'   => 'consultamensual.edit',
        'delete' => 'consultamensual.eliminar',
        'search' => 'consultamensual.buscar',
        'index'  => 'consultamensual.index',
    );
    protected $anoos = array("2019" => "2019", "2020" => "2020", "2021" => "2021", "2022" => "2022", "2023" => "2023", "2024" => "2024", "2025" => "2025", "2026" => "2026", "2027" => "2027", "2028" => "2028", "2029" => "2029", "2030" => "2030", "2031" => "2031", "2032" => "2032", "2033" => "2033", "2034" => "2034", "2035" => "2035", "2036" => "2036", "2037" => "2037", "2038" => "2038", "2039" => "2039", "2040" => "2040", "2041" => "2041", "2042" => "2042", "2043" => "2043", "2045" => "2045", "2046" => "2046", "2047" => "2047", "2048" => "2048", "2049" => "2049", "2050" => "2050");

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
        $mes         = date('m');
        $year        = date('Y');
        $usertype_id = Auth::user()->usertype_id;

        $pagina       = $request->input('page');
        $filas        = $request->input('filas');
        $entidad      = 'ConsultaMensual';
        $nombre       = Libreria::getParam($request->input('nombre'));
        $baja         = Libreria::getParam($request->input('baja'));
        $estado       = Libreria::getParam($request->input('estado'));
        $tipoconsulta = Libreria::getParam($request->input('tipoconsulta'));
        $this->crearConsultasMensuales($tipoconsulta);
        $estado2      = Libreria::getParam($request->input('estado2'));
        $numero       = Libreria::getParam($request->input('numero'));
        $messs        = Libreria::getParam($request->input('messs'));
        $anooo        = Libreria::getParam($request->input('anooo'));
        $resultado    = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%' . strtoupper($nombre) . '%')
            ->where('historia.numero', 'LIKE', '%' . $numero . '%')
            ->whereIn('historia.convenio_id',[1,2])
        //->where('historia.baja', '!=', "S")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $messs)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anooo)
            ->select("c.id as cid", 'person.nombres', 'person.apellidopaterno', 'person.apellidomaterno', 'historia.numero', 'historia.id as hid', 'person.dni', 'person.id as pid', "historia.baja")
            ->groupBy("historia.id")
            ->orderBy(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'));

        if ($baja == "S") {
            $resultado = $resultado->where('historia.baja', '=', "S");
        } else {
            $resultado = $resultado->where('historia.baja', '!=', "S");
        }

        if ($tipoconsulta !== "") {
            if ($tipoconsulta == "1") {
                $resultado = $resultado->join("consultanutricion as c", "c.persona_id", "=", "person.id");
            } else if ($tipoconsulta == "2") {
                $resultado = $resultado->join("consultasaludmental as c", "c.persona_id", "=", "person.id");
            } else if ($tipoconsulta == "3") {
                $resultado = $resultado->join("consultaserviciosocial as c", "c.persona_id", "=", "person.id");
            }

            if ($estado == "1" || $estado == "2") {
                $resultado = $resultado->where("c.estadoatencion", "=", $estado);
            }

            if ($estado2 == "1") {
                $resultado = $resultado->whereNotNull("c.numeroformato");
            } elseif ($estado2 == "2") {
                $resultado = $resultado->whereNull("c.numeroformato");
            }
        }

        $lista      = $resultado->get();
        $cabecera   = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'Historia', 'numero' => '1');
        $cabecera[] = array('valor' => 'DNI/CE', 'numero' => '1');
        $cabecera[] = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[] = array('valor' => 'Baja', 'numero' => '1');
        if ($tipoconsulta == "2") {
            $cabecera[] = array('valor' => 'Consulta Salud Mental', 'numero' => '1');
        }
        if ($tipoconsulta == "3") {
            $cabecera[] = array('valor' => 'Consuta Servicio Social', 'numero' => '1');
        }
        if ($tipoconsulta == "1") {
            $cabecera[] = array('valor' => 'Consuta Nutrición', 'numero' => '1');
        }
        //$cabecera[]       = array('valor' => 'Consulta Nefrológica', 'numero' => '1');

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
            return view($this->folderview . '.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'messs', 'anooo', 'tipoconsulta'));
        }
        return view($this->folderview . '.list')->with(compact('lista', 'entidad', 'messs', 'anooo'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function verconsolidadoMedicamentos(Request $request)
    {
        $id    = $request->input("id");
        $anno  = $request->input("anno");
        $years = $this->anoos;
        return view($this->folderview . '.verconsolidadomedicamentos')->with(compact('id', 'anno', 'years'));
    }

    public function verhistorialResultadosPorPaciente(Request $request)
    {
        $historia_id = $request->input("historia_id");
        $anno        = $request->input("anno");
        $years       = $this->anoos;
        $historia    = Historia::find($historia_id);

        return view($this->folderview . '.verresultadosporpaciente')->with(compact('historia', 'anno', 'years'));
    }

    public function index()
    {
        $entidad          = 'ConsultaMensual';
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
        return view($this->folderview . '.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'messs'));
    }

    public function reporte1(Request $request)
    {
        $mes      = $request->input("mes");
        $year     = $request->input("ano");
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $pid      = $request->input('pid');
        $historia = Historia::where('person_id', '=', $pid)->first();
        $entidad  = 'HC';
        $c1       = ConsultaSaludMental::where('persona_id', '=', $pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $year)->first();
        $hc       = $c1;
        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';
        return view($this->folderview . '.reporte1')->with(compact('hc', 'historia', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function reporte2(Request $request)
    {
        $mes             = $request->input("mes");
        $year            = $request->input("ano");
        $anillo          = $request->input("ano");
        $listar          = Libreria::getParam($request->input('listar'), 'NO');
        $pid             = $request->input('pid');
        $historia        = Historia::where('person_id', '=', $pid)->first();
        $fechanacimiento = new DateTime($historia->persona->fechanacimiento);
        $hoy             = new DateTime();
        $ed              = $hoy->diff($fechanacimiento);
        $entidad         = 'HC';
        $year2           = $year;
        $mes2            = (int) $mes - 1;
        if ($mes == "1") {
            $mes2  = "12";
            $year2 = (int) $year - 1;
        }
        $c1       = ConsultaNefrologica::where('persona_id', '=', $pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $year)->first();
        $c2       = ConsultaNefrologica::where('persona_id', '=', $pid)->where(DB::raw('MONTH(fecha)'), '=', $mes2)->where(DB::raw('YEAR(fecha)'), '=', $year2)->first();
        $hc       = $c1;
        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';

        //cantidad a la semana

        $ordencitas         = explode(';', $historia->ordencitas);
        $ordencitasopcional = explode(';', $historia->ordencitasopcional);

        $ordencitasNUEVO = "";
        for ($i = 0; $i < count($ordencitas); $i++) {
            $esta = false;
            for ($p = 0; $p < count($ordencitasopcional) - 1; $p++) {
                if ($ordencitasopcional[$p] == $ordencitas[$i]) {
                    $esta = true;
                }
            }
            if (!$esta) {
                $ordencitasNUEVO .= $ordencitas[$i] . ";";
            }
        }

        $ordencitasFIRME = explode(';', $ordencitasNUEVO);

        $frecuencia    = count($ordencitas) - count($ordencitasopcional);
        $cantidadalmes = 0;

        //Calculo proximo mes

        $mesactual     = $mes;
        $anito         = $year;
        $messiguienten = $mesactual;

        /*if ($mesactual == 12) {
        $anito++;
        $messiguienten = 1;
        }*/

        //cantidad de dias del mes

        $diasenmes = (cal_days_in_month(CAL_GREGORIAN, $messiguienten, $anito) <= 30 ? cal_days_in_month(CAL_GREGORIAN, $messiguienten, $anito) : 30);
        //$diasenmes = cal_days_in_month(CAL_GREGORIAN, $messiguienten, $anito);

        //cantidad al mes actual

        for ($i = 1; $i <= 31; $i++) {
            $fechadetratamiento = $anito . "-" . $messiguienten . "-" . $i;
            if (checkdate($messiguienten, $i, $anito)) {
                $var = (date("w", strtotime($fechadetratamiento)) == 0 ? 7 : date("w", strtotime($fechadetratamiento)));
                foreach ($ordencitasFIRME as $diacita) {
                    if ($var == ((int) $diacita)) {
                        $cantidadalmes++;
                    }
                }
            } else {
                break;
            }
        }

        return view($this->folderview . '.reporte2')->with(compact('hc', 'ed', 'historia', 'formData', 'entidad', 'boton', 'listar', "cantidadalmes", "frecuencia", "diasenmes", "ano", "anillo", "c2"));
    }

    public function reporte3(Request $request)
    {
        $mes      = $request->input("mes");
        $year     = $request->input("ano");
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $pid      = $request->input('pid');
        $historia = Historia::where('person_id', '=', $pid)->first();
        $entidad  = 'HC';
        $c1       = ConsultaServicioSocial::where('persona_id', '=', $pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $year)->first();
        $hc       = $c1;
        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';
        return view($this->folderview . '.reporte3')->with(compact('hc', 'historia', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function reporte4(Request $request)
    {
        $mes      = $request->input("mes");
        $year     = $request->input("ano");
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $pid      = $request->input('pid');
        $historia = Historia::where('person_id', '=', $pid)->first();
        $entidad  = 'HC';
        $c1       = ConsultaNutricion::where('persona_id', '=', $pid)->where(DB::raw('MONTH(fecha)'), '=', $mes)->where(DB::raw('YEAR(fecha)'), '=', $year)->first();
        $hc       = $c1;
        $hid      = $request->input("hid");
        $pid      = $request->input("pid");
        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento' . $entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar';
        return view($this->folderview . '.reporte4')->with(compact('hc', 'historia', 'formData', 'entidad', 'boton', 'listar', 'hid', 'pid', 'year'));
    }

    public function storereporte1(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id     = $request->input('id1');
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $dat    = '';
        $error  = DB::transaction(function () use ($request, $id, &$dat) {
            if ($id == '0') {
                $c1 = new ConsultaSaludMental();
            } else {
                $c1 = ConsultaSaludMental::find($id);
            }

            //COMPROBAR SI ES MEDICO, SETEO EL DOCTOR_ID

            $user = Auth::user();

            $usertype_id = $user->usertype_id;
            $person_id   = $user->person_id;

            if ($usertype_id == 36) {
                $c1->doctor_id = $person_id;
            }

            $mesatencion = date("Y-m", strtotime($c1->fecha));

            $c1->persona_id       = $request->input('persona_id');
            $c1->txtMotivo        = $request->input('txtMotivo');
            $c1->txtObservacion   = $request->input('txtObservacion');
            $c1->txtPrueba        = $request->input('txtPrueba');
            $c1->txtEuro          = $request->input('txtEuro');
            $c1->txtDiagnostico   = $request->input('cadenacies1');
            $c1->txtPlan          = $request->input('txtPlan');
            $c1->txtIntervencion  = $request->input('txtIntervencion');
            $c1->txtObservacion2  = $request->input('txtObservacion2');
            $c1->txtRecomendacion = $request->input('txtRecomendacion');
            $c1->fecha_atencion   = date('Y-m-d H:i:s', strtotime($mesatencion . "-" . $request->input('diaconsulta1') . ' ' . $request->input('hora1')));
            $c1->estadoatencion   = 1;
            $c1->save();

            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function storereporte2(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id     = $request->input('id1');
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $dat    = '';
        $error  = DB::transaction(function () use ($request, $id, &$dat) {
            $c1               = ConsultaNefrologica::find($id);
            $c1->tiempoenf    = $request->input('tiempoenf');
            $c1->anamnesis    = $request->input('anamnesis');
            $c1->temperatura  = $request->input('temperatura');
            $c1->pesoseco     = $request->input('pesoseco');
            $c1->pa           = $request->input('pa');
            $c1->fc           = $request->input('fc');
            $c1->fr           = $request->input('fr');
            $c1->talla        = $request->input('talla');
            $c1->selectepo    = $request->input('selectepo');
            $c1->selectcalcit = $request->input('selectcalcit');

            //MODIFICO LA TALLA
            $historita           = Historia::where("person_id", "=", $c1->persona_id)->first();
            $historita->txtTalla = $request->input('talla');
            $historita->save();

            $c1->imc            = $request->input('imc');
            $c1->cav            = $request->input('cav');
            $c1->tcsc           = $request->input('tcsc');
            $c1->pulmones       = $request->input('pulmones');
            $c1->sisnervioso    = $request->input('sisnervioso');
            $c1->cadenacies     = $request->input('cadenacies2');
            $c1->observacion    = $request->input('observacion');
            $mesatencion        = date("Y-m", strtotime($c1->fecha));
            $c1->fecha_atencion = date('Y-m-d H:i:s', strtotime($mesatencion . "-" . $request->input('diaconsulta1') . ' ' . $request->input('hora1')));
            $c1->estado         = 1;

            $c1->txtAlergia       = $request->input('txtAlergia');
            $c1->txtTransfusiones = $request->input('txtTransfusiones');
            $c1->txtVacunacion    = $request->input('txtVacunacion');
            $c1->txtRevacunacion  = $request->input('txtRevacunacion');
            //medicamento
            //Obtenemos id de producto nuevo:
            $pproducto = Producto::select('id')->where('nombre', '=', 'CALCITRIOL 0.25ug CAP (**)')->first();
            $c1->m1  = 0;
            $c1->m2  = 1;
            $c1->m3  = 2;
            $c1->m4  = 3;
            $c1->m5  = 7;
            $c1->m6  = 8;
            $c1->m7  = 9;
            $c1->m8  = 10;
            $c1->m9  = 11;
            $c1->m91 = $pproducto->id;//Aquí el id del calcitriol nuevo
            $c1->m10 = 12;
            $c1->m11 = 13;
            $c1->m12 = 14;
            $c1->m13 = 15;
            $c1->m14 = 16;
            $c1->m15 = 17;
            $c1->m16 = 18;
            $c1->m17 = 19;            
            //frecuencia
            $c1->f1  = ($request->input('f1') == "" ? null : $request->input("f1"));
            $c1->f2  = ($request->input('f2') == "" ? null : $request->input("f2"));
            $c1->f3  = ($request->input('f3') == "" ? null : $request->input("f3"));
            $c1->f4  = ($request->input('f4') == "" ? null : $request->input("f4"));
            $c1->f5  = ($request->input('f5') == "" ? null : $request->input("f5"));
            $c1->f6  = ($request->input('f6') == "" ? null : $request->input("f6"));
            $c1->f7  = ($request->input('f7') == "" ? null : $request->input("f7"));
            $c1->f8  = ($request->input('f8') == "" ? null : $request->input("f8"));
            $c1->f9  = ($request->input('f9') == "" ? null : $request->input("f9"));
            $c1->f91  = ($request->input('f91') == "" ? null : $request->input("f91"));
            $c1->f10 = ($request->input('f10') == "" ? null : $request->input("f10"));
            $c1->f11 = ($request->input('f11') == "" ? null : $request->input("f11"));
            $c1->f12 = ($request->input('f12') == "" ? null : $request->input("f12"));
            $c1->f13 = ($request->input('f13') == "" ? null : $request->input("f13"));
            $c1->f14 = ($request->input('f14') == "" ? null : $request->input("f14"));
            $c1->f15 = ($request->input('f15') == "" ? null : $request->input("f15"));
            $c1->f16 = ($request->input('f16') == "" ? null : $request->input("f16"));
            $c1->f17 = ($request->input('f17') == "" ? null : $request->input("f17"));
            //cantidad
            $c1->c1  = ($request->input('c1') == "" ? null : $request->input("c1"));
            $c1->c2  = ($request->input('c2') == "" ? null : $request->input("c2"));
            $c1->c3  = ($request->input('c3') == "" ? null : $request->input("c3"));
            $c1->c4  = ($request->input('c4') == "" ? null : $request->input("c4"));
            $c1->c5  = ($request->input('c5') == "" ? null : $request->input("c5"));
            $c1->c6  = ($request->input('c6') == "" ? null : $request->input("c6"));
            $c1->c7  = ($request->input('c7') == "" ? null : $request->input("c7"));
            $c1->c8  = ($request->input('c8') == "" ? null : $request->input("c8"));
            $c1->c9  = ($request->input('c9') == "" ? null : $request->input("c9"));
            $c1->c91 = ($request->input('c91') == "" ? null : $request->input("c91"));
            $c1->c10 = ($request->input('c10') == "" ? null : $request->input("c10"));
            $c1->c11 = ($request->input('c11') == "" ? null : $request->input("c11"));
            $c1->c12 = ($request->input('c12') == "" ? null : $request->input("c12"));
            $c1->c13 = ($request->input('c13') == "" ? null : $request->input("c13"));
            $c1->c14 = ($request->input('c14') == "" ? null : $request->input("c14"));
            $c1->c15 = ($request->input('c15') == "" ? null : $request->input("c15"));
            $c1->c16 = ($request->input('c16') == "" ? null : $request->input("c16"));
            $c1->c17 = ($request->input('c17') == "" ? null : $request->input("c17"));
            //observacion
            $c1->o1  = ($request->input('o1') == "" ? null : $request->input("o1"));
            $c1->o2  = ($request->input('o2') == "" ? null : $request->input("o2"));
            $c1->o3  = ($request->input('o3') == "" ? null : $request->input("o3"));
            $c1->o4  = ($request->input('o4') == "" ? null : $request->input("o4"));
            $c1->o5  = ($request->input('o5') == "" ? null : $request->input("o5"));
            $c1->o6  = ($request->input('o6') == "" ? null : $request->input("o6"));
            $c1->o7  = ($request->input('o7') == "" ? null : $request->input("o7"));
            $c1->o8  = ($request->input('o8') == "" ? null : $request->input("o8"));
            $c1->o9  = ($request->input('o9') == "" ? null : $request->input("o9"));
            $c1->o91 = ($request->input('o91') == "" ? null : $request->input("o91"));
            $c1->o10 = ($request->input('o10') == "" ? null : $request->input("o10"));
            $c1->o11 = ($request->input('o11') == "" ? null : $request->input("o11"));
            $c1->o12 = ($request->input('o12') == "" ? null : $request->input("o12"));
            $c1->o13 = ($request->input('o13') == "" ? null : $request->input("o13"));
            $c1->o14 = ($request->input('o14') == "" ? null : $request->input("o14"));
            $c1->o15 = ($request->input('o15') == "" ? null : $request->input("o15"));
            $c1->o16 = ($request->input('o16') == "" ? null : $request->input("o16"));
            $c1->o17 = ($request->input('o17') == "" ? null : $request->input("o17"));

            //indicacion
            //$c1->i1 = ($request->input('i1')==""?NULL:$request->input("i1"));
            //$c1->i2 = ($request->input('i2')==""?NULL:$request->input("i2"));
            //$c1->i3 = ($request->input('i3')==""?NULL:$request->input("i3"));
            //$c1->i4 = ($request->input('i4')==""?NULL:$request->input("i4"));
            $c1->i5  = ($request->input('i5') == "" ? null : $request->input("i5"));
            $c1->i6  = ($request->input('i6') == "" ? null : $request->input("i6"));
            $c1->i7  = ($request->input('i7') == "" ? null : $request->input("i7"));
            $c1->i8  = ($request->input('i8') == "" ? null : $request->input("i8"));
            $c1->i9  = ($request->input('i9') == "" ? null : $request->input("i9"));
            $c1->i91 = ($request->input('i91') == "" ? null : $request->input("i91"));
            $c1->i10 = ($request->input('i10') == "" ? null : $request->input("i10"));
            $c1->i11 = ($request->input('i11') == "" ? null : $request->input("i11"));
            $c1->i12 = ($request->input('i12') == "" ? null : $request->input("i12"));
            $c1->i13 = ($request->input('i13') == "" ? null : $request->input("i13"));
            $c1->i14 = ($request->input('i14') == "" ? null : $request->input("i14"));
            $c1->i15 = ($request->input('i15') == "" ? null : $request->input("i15"));
            $c1->i16 = ($request->input('i16') == "" ? null : $request->input("i16"));
            $c1->i17 = ($request->input('i17') == "" ? null : $request->input("i17"));
            //COMPROBAR SI ES MEDICO, SETEO EL DOCTOR_ID

            $user = Auth::user();

            $usertype_id = $user->usertype_id;
            $person_id   = $user->person_id;

            if ($usertype_id == 28 || $usertype_id == 29) {
                $c1->doctor_id = $person_id;
            }
            $c1->estadoatencion = 1;
            $c1->save();

            //sucursal_id
            $sucursal_id = Session::get('sucursal_id');
            $mesactual   = date("m");
            $caja        = Caja::where("sucursal_id", "=", $sucursal_id)->first();

            //Creo el requerimiento de los medicamentos x 30 o 31 dias

            //cancelo todos los requerimientos asignados a esta atencion si es que existen
            $movimientoalmacenanterior = Movimiento::where("caja_id", "=", $caja->id)->where("sucursal_id", "=", $sucursal_id)->where(DB::raw('MONTH(fecha)'), '=', date("m", strtotime($c1->fecha_atencion)))->where(DB::raw('YEAR(fecha)'), '=', date("Y", strtotime($c1->fecha_atencion)))->where("persona_id", "=", $c1->persona->id)->where("situacion", "=", "P")->where("tipodocumento_id", "=", 23)->where("tipomovimiento_id", "=", 15)->get();

            $movimientoalmacenanterior2 = Movimiento::where("caja_id", "=", $caja->id)->where("sucursal_id", "=", $sucursal_id)->where(DB::raw('MONTH(fecha)'), '=', date("m", strtotime($c1->fecha_atencion)))->where(DB::raw('YEAR(fecha)'), '=', date("Y", strtotime($c1->fecha_atencion)))->where("persona_id", "=", $c1->persona->id)->where("situacion", "!=", "P")->where("tipodocumento_id", "=", 23)->where("tipomovimiento_id", "=", 15)->get();

            $crear = false;

            if (count($movimientoalmacenanterior2) > 0) {
                $crear = false;
            } else {
                foreach ($movimientoalmacenanterior as $ma) {
                    $ma->delete();
                }
                for ($c = 5; $c <= 18; $c++) {
                    ##Compruebo si no es null o vacia la cantidad
                    //Le quitamos el inyectable calcitriol && $c1["c" . $c] != $pproducto->id
                    $aa = $c;
                    if($c == 9) {
                        $aa = 91;
                    } else if($c > 9) {
                        $aa = $c + 1;
                    }
                    if ($c1["c" . $aa] !== null && $c1["c" . $aa] !== "" && $c1["c" . $aa] != 11) {
                        $crear = true;
                        break;
                    }
                }
            }

            if ($crear) {

                $movimientoalmacen                    = new Movimiento();
                $movimientoalmacen->movimiento_id     = $c1->id;
                $movimientoalmacen->sucursal_id       = $sucursal_id;
                $movimientoalmacen->caja_id           = $caja->id;
                $movimientoalmacen->tipodocumento_id  = 23;
                $movimientoalmacen->almacen_id        = 1;
                $movimientoalmacen->tipomovimiento_id = 15;
                $movimientoalmacen->comentario        = "RECETA MENSUAL DE CONSULTA NEFROLOGICA PARA EL PACIENTE " . $c1->persona->nombres . " " . $c1->persona->apellidopaterno . " " . $c1->persona->apellidomaterno;
                $movimientoalmacen->numero            = Movimiento::NumeroSigue2($caja->id, $sucursal_id, 15, 23);
                $movimientoalmacen->fecha             = date("Y-m-d", strtotime($c1->fecha_atencion));
                $movimientoalmacen->total             = 0;
                $user                                 = Auth::user();
                $movimientoalmacen->responsable_id    = $user->person_id;
                $movimientoalmacen->persona_id        = $c1->persona->id;
                $movimientoalmacen->situacion         = 'P'; //PENDIENTE
                $movimientoalmacen->save();
                $movimiento_id = $movimientoalmacen->id;

                for ($c = 5; $c <= 17; $c++) {
                    //Compruebo si no es null o vacia la cantidad
                    //Le quitamos el inyectable calcitriol && $c1["c" . $c] != 11
                    $aa = $c;
                    if($c == 9) {
                        $aa = 91;
                    } else if($c > 9) {
                        $aa = $c + 1;
                    }
                    if ($c1["c" . $aa] !== null && $c1["c" . $aa] !== "" && $c1["c" . $aa] != 11) {
                        $cantidad                    = $c1["c" . $aa];
                        $detalleVenta                = new Detallemovimiento();
                        $detalleVenta->cantidad      = $cantidad;
                        $detalleVenta->precio        = 0;
                        $detalleVenta->subtotal      = 0;
                        $detalleVenta->movimiento_id = $movimiento_id;
                        $detalleVenta->producto_id   = $c1["m" . $aa];
                        $detalleVenta->save();
                    }
                }
            }

            /*//PARA LOS DATOS DE KTV
            $txtFechaKTV = $request->input("txtFechaKTV");
            if($txtFechaKTV!=="") {

                //PRIMERO CAPTURO LA DESCRIPCIÓN DE txtMuestraAnalisis SI HUBIERA UN HD CON MUESTRA DE ANALISIS
                $txtMuestraAnalisis0 = "SE TOMA MUESTRAS MENSUALES";
                $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                    ->where("historia.person_id", "=", $c1->persona->id)
                    ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha_atencion)))
                    ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha_atencion)))
                    ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
                    ->select("historiaclinica.id", "historiaclinica.txtPesoInicial2", "historiaclinica.txtPesoFinal2", "historiaclinica.txtHorasHemodialisis")
                    ->first();

                if($atencion!==null) {
                    $txtMuestraAnalisis0 = $atencion->txtMuestraAnalisis;
                }

                //SEGUNDO ELIMINO TODOS LOS txtMuestraAnalisis ASUMIENDO QUE NINGÚNA HD VA A TOMAR DATOS MENSUALES
                $atencionesMensuales = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                    ->where("historia.person_id", "=", $c1->persona->id)
                    ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha_atencion)))
                    ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha_atencion)))
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
                    ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", date("m", strtotime($c1->fecha_atencion)))
                    ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", date("Y", strtotime($c1->fecha_atencion)))
                    ->select("historiaclinica.id")
                    ->get();
                foreach ($atencionesMensuales as $aM) {
                    $aM = HistoriaClinica::find($aM->id);
                    $aM->txtMuestraAnalisis = "";
                    $aM->save();
                }
            }*/

            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function registrarReporteMedicamentos(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id     = $request->input('id1');
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $dat    = '';
        $error  = DB::transaction(function () use ($request, $id, &$dat) {
            $c1 = ConsultaNefrologica::find($id);
            $c1->selectepo    = $request->input('selectepo');
            $c1->selectcalcit = $request->input('selectcalcit');
            //medicamento
            //Obtenemos id de producto nuevo:
            $pproducto = Producto::select('id')->where('nombre', '=', 'CALCITRIOL 0.25ug CAP (**)')->first();
            $c1->m1  = 0;
            $c1->m2  = 1;
            $c1->m3  = 2;
            $c1->m4  = 3;
            $c1->m5  = 7;
            $c1->m6  = 8;
            $c1->m7  = 9;
            $c1->m8  = 10;
            $c1->m9  = 11;
            $c1->m91 = $pproducto->id;
            $c1->m10 = 12;
            $c1->m11 = 13;
            $c1->m12 = 14;
            $c1->m13 = 15;
            $c1->m14 = 16;
            $c1->m15 = 17;
            $c1->m16 = 18;
            $c1->m17 = 19;
            //frecuencia
            $c1->f1  = ($request->input('f1') == "" ? null : $request->input("f1"));
            $c1->f2  = ($request->input('f2') == "" ? null : $request->input("f2"));
            $c1->f3  = ($request->input('f3') == "" ? null : $request->input("f3"));
            $c1->f4  = ($request->input('f4') == "" ? null : $request->input("f4"));
            $c1->f5  = ($request->input('f5') == "" ? null : $request->input("f5"));
            $c1->f6  = ($request->input('f6') == "" ? null : $request->input("f6"));
            $c1->f7  = ($request->input('f7') == "" ? null : $request->input("f7"));
            $c1->f8  = ($request->input('f8') == "" ? null : $request->input("f8"));
            $c1->f9  = ($request->input('f9') == "" ? null : $request->input("f9"));
            $c1->f91 = ($request->input('f91') == "" ? null : $request->input("f91"));
            $c1->f10 = ($request->input('f10') == "" ? null : $request->input("f10"));
            $c1->f11 = ($request->input('f11') == "" ? null : $request->input("f11"));
            $c1->f12 = ($request->input('f12') == "" ? null : $request->input("f12"));
            $c1->f13 = ($request->input('f13') == "" ? null : $request->input("f13"));
            $c1->f14 = ($request->input('f14') == "" ? null : $request->input("f14"));
            $c1->f15 = ($request->input('f15') == "" ? null : $request->input("f15"));
            $c1->f16 = ($request->input('f16') == "" ? null : $request->input("f16"));
            $c1->f17 = ($request->input('f17') == "" ? null : $request->input("f17"));
            //cantidad
            $c1->c1  = ($request->input('c1') == "" ? null : $request->input("c1"));
            $c1->c2  = ($request->input('c2') == "" ? null : $request->input("c2"));
            $c1->c3  = ($request->input('c3') == "" ? null : $request->input("c3"));
            $c1->c4  = ($request->input('c4') == "" ? null : $request->input("c4"));
            $c1->c5  = ($request->input('c5') == "" ? null : $request->input("c5"));
            $c1->c6  = ($request->input('c6') == "" ? null : $request->input("c6"));
            $c1->c7  = ($request->input('c7') == "" ? null : $request->input("c7"));
            $c1->c8  = ($request->input('c8') == "" ? null : $request->input("c8"));
            $c1->c9  = ($request->input('c9') == "" ? null : $request->input("c9"));
            $c1->c91 = ($request->input('c91') == "" ? null : $request->input("c91"));
            $c1->c10 = ($request->input('c10') == "" ? null : $request->input("c10"));
            $c1->c11 = ($request->input('c11') == "" ? null : $request->input("c11"));
            $c1->c12 = ($request->input('c12') == "" ? null : $request->input("c12"));
            $c1->c13 = ($request->input('c13') == "" ? null : $request->input("c13"));
            $c1->c14 = ($request->input('c14') == "" ? null : $request->input("c14"));
            $c1->c15 = ($request->input('c15') == "" ? null : $request->input("c15"));
            $c1->c16 = ($request->input('c16') == "" ? null : $request->input("c16"));
            $c1->c17 = ($request->input('c17') == "" ? null : $request->input("c17"));
            //observacion
            $c1->o1  = ($request->input('o1') == "" ? null : $request->input("o1"));
            $c1->o2  = ($request->input('o2') == "" ? null : $request->input("o2"));
            $c1->o3  = ($request->input('o3') == "" ? null : $request->input("o3"));
            $c1->o4  = ($request->input('o4') == "" ? null : $request->input("o4"));
            $c1->o5  = ($request->input('o5') == "" ? null : $request->input("o5"));
            $c1->o6  = ($request->input('o6') == "" ? null : $request->input("o6"));
            $c1->o7  = ($request->input('o7') == "" ? null : $request->input("o7"));
            $c1->o8  = ($request->input('o8') == "" ? null : $request->input("o8"));
            $c1->o9  = ($request->input('o9') == "" ? null : $request->input("o9"));
            $c1->o91 = ($request->input('o91') == "" ? null : $request->input("o91"));
            $c1->o10 = ($request->input('o10') == "" ? null : $request->input("o10"));
            $c1->o11 = ($request->input('o11') == "" ? null : $request->input("o11"));
            $c1->o12 = ($request->input('o12') == "" ? null : $request->input("o12"));
            $c1->o13 = ($request->input('o13') == "" ? null : $request->input("o13"));
            $c1->o14 = ($request->input('o14') == "" ? null : $request->input("o14"));
            $c1->o15 = ($request->input('o15') == "" ? null : $request->input("o15"));
            $c1->o16 = ($request->input('o16') == "" ? null : $request->input("o16"));
            $c1->o17 = ($request->input('o17') == "" ? null : $request->input("o17"));
            //indicacion
            //$c1->i1 = ($request->input('i1')==""?NULL:$request->input("i1"));
            //$c1->i2 = ($request->input('i2')==""?NULL:$request->input("i2"));
            //$c1->i3 = ($request->input('i3')==""?NULL:$request->input("i3"));
            //$c1->i4 = ($request->input('i4')==""?NULL:$request->input("i4"));
            $c1->i5  = ($request->input('i5') == "" ? null : $request->input("i5"));
            $c1->i6  = ($request->input('i6') == "" ? null : $request->input("i6"));
            $c1->i7  = ($request->input('i7') == "" ? null : $request->input("i7"));
            $c1->i8  = ($request->input('i8') == "" ? null : $request->input("i8"));
            $c1->i9  = ($request->input('i9') == "" ? null : $request->input("i9"));
            $c1->i91 = ($request->input('i91') == "" ? null : $request->input("i91"));
            $c1->i10 = ($request->input('i10') == "" ? null : $request->input("i10"));
            $c1->i11 = ($request->input('i11') == "" ? null : $request->input("i11"));
            $c1->i12 = ($request->input('i12') == "" ? null : $request->input("i12"));
            $c1->i13 = ($request->input('i13') == "" ? null : $request->input("i13"));
            $c1->i14 = ($request->input('i14') == "" ? null : $request->input("i14"));
            $c1->i15 = ($request->input('i15') == "" ? null : $request->input("i15"));
            $c1->i16 = ($request->input('i16') == "" ? null : $request->input("i16"));
            $c1->i17 = ($request->input('i17') == "" ? null : $request->input("i17"));
            $c1->save();

            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function storereporte3(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id     = $request->input('id3');
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $dat    = '';
        $error  = DB::transaction(function () use ($request, $id, &$dat) {
            if ($id == '0') {
                $c1 = new ConsultaServicioSocial();
            } else {
                $c1 = ConsultaServicioSocial::find($id);
            }

            //COMPROBAR SI ES MEDICO, SETEO EL DOCTOR_ID

            $user = Auth::user();

            $usertype_id = $user->usertype_id;
            $person_id   = $user->person_id;

            if ($usertype_id == 37) {
                $c1->doctor_id = $person_id;
            }

            $mesatencion = date("Y-m", strtotime($c1->fecha));

            $c1->persona_id      = $request->input('persona_id');
            $c1->txtEpsi         = $request->input('txtEpsi');
            $c1->txtEfam         = $request->input('txtEfam');
            $c1->txtEvivi        = $request->input('txtEvivi');
            $c1->txtElab         = $request->input('txtElab');
            $c1->txtEeco         = $request->input('txtEeco');
            $c1->txtDiagnostico  = $request->input('txtDiagnostico3');
            $c1->txtDiagnostico2 = $request->input('cadenacies3');
            $c1->txtMege         = $request->input('txtMege');
            $c1->txtIntervencion = $request->input('txtIntervencion');
            $c1->txtObservacion2 = $request->input('txtObservacion2');
            $c1->fecha_atencion  = date('Y-m-d H:i:s', strtotime($mesatencion . "-" . $request->input('diaconsulta1') . ' ' . $request->input('hora1')));
            $c1->txtMees         = $request->input('txtMees');
            $c1->estadoatencion  = 1;
            $c1->save();

            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function storereporte4(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $id     = $request->input('id4');
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $dat    = '';
        $error  = DB::transaction(function () use ($request, $id, &$dat) {
            if ($id == '0') {
                $c1 = new ConsultaNutricion();
            } else {
                $c1 = ConsultaNutricion::find($id);
            }

            //COMPROBAR SI ES MEDICO, SETEO EL DOCTOR_ID

            $user = Auth::user();

            $usertype_id = $user->usertype_id;
            $person_id   = $user->person_id;

            if ($usertype_id == 35) {
                $c1->doctor_id = $person_id;
            }

            $mesatencion = date("Y-m", strtotime($c1->fecha));

            $c1->persona_id             = $request->input('persona_id');
            $c1->txtHistoriaNutricional = $request->input('txtHistoriaNutricional');
            $c1->txtPesoseco            = $request->input('txtPesoseco');
            $c1->txtPesoactual          = $request->input('txtPesoactual');

            $c1->txtPesousual    = $request->input('txtPesousual');
            $c1->txtPesoideal    = $request->input('txtPesoideal');
            $c1->txtTalla        = $request->input('txtTalla');
            $c1->txtIMC          = $request->input('txtIMC');
            $c1->txtHemoglobina  = $request->input('txtHemoglobina');
            $c1->txtHematocrito  = $request->input('txtHematocrito');
            $c1->txtUreapost     = $request->input('txtUreapost');
            $c1->txtUreapre      = $request->input('txtUreapre');
            $c1->txtCreatinina   = $request->input('txtCreatinina');
            $c1->txtCalcio       = $request->input('txtCalcio');
            $c1->txtFosforo      = $request->input('txtFosforo');
            $c1->txtIntervencion = $request->input('txtIntervencion');
            $c1->txtObservacion2 = $request->input('txtObservacion2');
            $c1->txtReultimo     = $request->input('txtReultimo');
            $c1->txtDiagnostico  = $request->input('txtDiagnostico4');
            $c1->txtDiagnostico2 = $request->input('cadenacies4');
            $c1->txtRecoge       = $request->input('txtRecoge');
            $c1->fecha_atencion  = date('Y-m-d H:i:s', strtotime($mesatencion . "-" . $request->input('diaconsulta1') . ' ' . $request->input('hora1')));
            $c1->txtRedie        = $request->input('txtRedie');
            $c1->estadoatencion  = 1;
            $c1->save();

            $dat = "OK";
        });
        return is_null($error) ? $dat : $error;
    }

    public function pdfReporte1(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $hc = ConsultaSaludMental::find($request->input('id'));

        if ($hc == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $historia = Historia::where('person_id', '=', $hc->persona_id)->first();
            $pdf      = new TCPDF();
            // set margins
            $pdf::SetMargins(20, 20, 30, 20);

            $pdf::SetTitle('AtencionSaludMetal');
            $pdf::AddPage();

            $pdf::SetFont('', '', 8);

            $cies = '';
            $cs   = explode(';', $hc->txtDiagnostico);
            foreach ($cs as $ca) {
                $cc = Cie::find($ca);
                if ($cc !== null) {
                    $cies .= $cc->codigo . ' - ' . $cc->descripcion . '<br>';
                }
            }

            $cies = substr($cies, 0, strlen($cies) - 4);

            $tbl = '
                <table width="100%" height="100%" cellpadding="1">
                    <tr>
                        <td>
                            <table width="100%" height="100%" cellpadding="2">
                                <tr align="left">
                                    <td width="40%">
                                        <img src="dist/img/logo2-nefrocix.jpg" width="150px" height="65px">
                                    </td>
                                    <td width="50%" align="center">
                                        <h2>ANEXO N° 17-B </h2>
                                        <h2>ATENCIÓN EN SALUD MENTAL </h2>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td colspan="4"><h4>DATOS DE FILIACIÓN DEL PACIENTE </h4></td>
                                </tr>
                                <tr><td></td></tr>
                                <tr align="left">
                                    <td width="20%"><h4>Apellidos y nombres: </h4></td>
                                    <td width="80%">' . htmlentities($historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="20%"><h4>N° de afiliación: </h4></td>
                                    <td width="43%">' . htmlentities($historia->carnet) . '</td>
                                    <td width="8%"><h4>DNI/CE: </h4></td>
                                    <td width="30%">' . htmlentities($historia->persona->dni) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="9%"><h4>Distrito: </h4></td>
                                    <td width="22%">' . htmlentities($historia->distrito2->nombre) . '</td>
                                    <td width="10%"><h4>Provincia: </h4></td>
                                    <td width="22%">' . htmlentities($historia->provincia2->nombre) . '</td>
                                    <td width="15%"><h4>Departamento: </h4></td>
                                    <td width="22%">' . htmlentities($historia->departamento2->nombre) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="11%"><h4>Dirección: </h4></td>
                                    <td width="89%">' . htmlentities(strtoupper($historia->persona->direccion)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="10%"><h4>Teléfono: </h4></td>
                                    <td width="53%">' . htmlentities($historia->persona->telefono) . '</td>
                                    <td width="11%"><h4>IPRESS: </h4></td>
                                    <td width="27%">NEFROCIX SAC</td>
                                </tr>

                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%" align="left">
                                        <table width="100%" height="100%" cellpadding="4" border="1">
                                            <tr>
                                                <td width="21%"><h4>Motivo de Consulta </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtMotivo)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Observación de conducta </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtObservacion)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Pruebas psicológicas realizadas  y resultados </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtPrueba)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Último resultado de UuroQoI-5D </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtEuro)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Diagnóstico psicológico </h4></td>
                                                <td width="79%">' . strtoupper($cies) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Plan de tratamiento </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtPlan)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Recomendaciones </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtRecomendacion)) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr align="left">
                                    <td width="8%"><h4>Fecha: </h4></td>
                                    <td width="24%">' . date('d-m-Y', strtotime($hc->fecha_atencion)) . '</td>
                                    <td width="8%"><h4>Hora: </h4></td>
                                    <td width="15%">' . date('H:i', strtotime($hc->fecha_atencion)) . ' hrs.</td>
                                </tr>
                                <tr align="left">
                                    <td width="15%"><h4>Profesional: </h4></td>
                                    <td width="85%">' . htmlentities(($hc->doctor !== null ? ($hc->doctor->apellidopaterno . " " . $hc->doctor->apellidomaterno . " " . $hc->doctor->nombres) : "")) . '</td>
                                </tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" cellpadding="1">
                                            <tr>
                                                <td width="50%" style="font-size:8px;">_____________________________</td>
                                                <td width="50%" style="font-size:8px;">___________________________________________</td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:8px;">Firma del paciente y huella</td>
                                                <td width="50%" style="font-size:8px;">Firma y Sello del Responsable de la Atención</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>';

            $pdf::writeHTML($tbl, true, false, true, false, 'C');

            $pdf::Output('Historia.pdf');
        }
    }

    public function pdfReporte3(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $hc = ConsultaServicioSocial::find($request->input('id'));

        if ($hc == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $historia = Historia::where('person_id', '=', $hc->persona_id)->first();
            $pdf      = new TCPDF();
            // set margins
            $pdf::SetMargins(20, 20, 30, 20);

            $pdf::SetTitle('AtencionServicioSocial');
            $pdf::AddPage();

            $pdf::SetFont('', '', 8);

            $cies = '';
            $cs   = explode(';', $hc->txtDiagnostico2);
            foreach ($cs as $ca) {
                $cc = Cie::find($ca);
                if ($cc !== null) {
                    $cies .= $cc->codigo . ' - ' . $cc->descripcion . '<br>';
                }
            }

            $cies = substr($cies, 0, strlen($cies) - 4);

            $tbl = '
                <table width="100%" height="100%" cellpadding="1">
                    <tr>
                        <td>
                            <table width="100%" height="100%" cellpadding="2">
                                <tr align="left">
                                    <td width="40%">
                                        <img src="dist/img/logo2-nefrocix.jpg" width="150px" height="65px">
                                    </td>
                                    <td width="50%" align="center">
                                        <br><br>
                                        <h2>ATENCIÓN EN SERVICIO SOCIAL </h2>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td></td>
                                </tr>
                                <tr align="left">
                                    <td colspan="4"><h4>DATOS DE FILIACIÓN DEL PACIENTE </h4></td>
                                </tr>
                                <tr><td></td></tr>
                                <tr align="left">
                                    <td width="20%"><h4>Apellidos y nombres: </h4></td>
                                    <td width="80%">' . htmlentities($historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="20%"><h4>N° de afiliación: </h4></td>
                                    <td width="43%">' . htmlentities($historia->carnet) . '</td>
                                    <td width="8%"><h4>DNI/CE: </h4></td>
                                    <td width="30%">' . htmlentities($historia->persona->dni) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="9%"><h4>Distrito: </h4></td>
                                    <td width="22%">' . htmlentities($historia->distrito2->nombre) . '</td>
                                    <td width="10%"><h4>Provincia: </h4></td>
                                    <td width="22%">' . htmlentities($historia->provincia2->nombre) . '</td>
                                    <td width="15%"><h4>Departamento: </h4></td>
                                    <td width="22%">' . htmlentities($historia->departamento2->nombre) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="11%"><h4>Dirección: </h4></td>
                                    <td width="89%">' . htmlentities(strtoupper($historia->persona->direccion)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="10%"><h4>Teléfono: </h4></td>
                                    <td width="53%">' . htmlentities($historia->persona->telefono) . '</td>
                                    <td width="11%"><h4>IPRESS: </h4></td>
                                    <td width="27%">NEFROCIX SAC</td>
                                </tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%" align="left">
                                        <table width="100%" height="100%" cellpadding="4" border="1">
                                            <tr>
                                                <td width="21%"><h4>Evaluación psicosocial </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtEpsi)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Evaluación familiar </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtEfam)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Evaluación de vivienda </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtEvivi)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Evaluación laboral </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtElab)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Evaluación económica </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtEeco)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Diagnóstico social </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtDiagnostico)) . '<br>' . strtoupper($cies) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Medidas generales </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtMege)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="21%"><h4>Medidas específicas </h4></td>
                                                <td width="79%">' . htmlentities(strtoupper($hc->txtMees)) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr align="left">
                                    <td width="8%"><h4>Fecha: </h4></td>
                                    <td width="24%">' . date('d-m-Y', strtotime($hc->fecha_atencion)) . '</td>
                                    <td width="8%"><h4>Hora: </h4></td>
                                    <td width="15%">' . date('H:i', strtotime($hc->fecha_atencion)) . ' hrs.</td>
                                </tr>
                                <tr align="left">
                                    <td width="15%"><h4>Profesional: </h4></td>
                                    <td width="85%">' . htmlentities(($hc->doctor !== null ? ($hc->doctor->apellidopaterno . " " . $hc->doctor->apellidomaterno . " " . $hc->doctor->nombres) : "")) . '</td>
                                </tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" cellpadding="1">
                                            <tr>
                                                <td width="50%" style="font-size:8px;">_____________________________</td>
                                                <td width="50%" style="font-size:8px;">___________________________________________</td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:8px;">Firma del paciente y huella</td>
                                                <td width="50%" style="font-size:8px;">Firma y Sello del Responsable de la Atención</td>
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

    public function pdfReporte4(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $hc = ConsultaNutricion::find($request->input('id'));

        if ($hc == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $historia = Historia::where('person_id', '=', $hc->persona_id)->first();
            $pdf      = new TCPDF();
            // set margins
            $pdf::SetMargins(20, 20, 30, 20);

            $pdf::SetTitle('AtencionNutricionista');
            $pdf::AddPage();

            $pdf::SetFont('', '', 8);

            $cies = '';
            $cs   = explode(';', $hc->txtDiagnostico2);
            foreach ($cs as $ca) {
                $cc = Cie::find($ca);
                if ($cc !== null) {
                    $cies .= $cc->codigo . ' - ' . $cc->descripcion . '<br>';
                }
            }

            $cies = substr($cies, 0, strlen($cies) - 4);

            $tbl = '
                <table width="100%" height="100%" cellpadding="1">
                    <tr>
                        <td>
                            <table width="100%" height="100%" cellpadding="2">
                                <tr align="left">
                                    <td width="40%">
                                        <img src="dist/img/logo2-nefrocix.jpg" width="150px" height="65px">
                                    </td>
                                    <td width="50%" align="center">
                                        <h2>ANEXO N° 17-A </h2>
                                        <h2>ATENCIÓN EN NUTRICIÓN </h2>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td colspan="4"><h4>DATOS DE FILIACIÓN DEL PACIENTE </h4></td>
                                </tr>
                                <tr><td></td></tr>
                                <tr align="left">
                                    <td width="20%"><h4>Apellidos y nombres: </h4></td>
                                    <td width="80%">' . htmlentities($historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="20%"><h4>N° de afiliación: </h4></td>
                                    <td width="43%">' . htmlentities(strtoupper($historia->carnet)) . '</td>
                                    <td width="8%"><h4>DNI/CE: </h4></td>
                                    <td width="30%">' . htmlentities(strtoupper($historia->persona->dni)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="9%"><h4>Distrito: </h4></td>
                                    <td width="22%">' . htmlentities(strtoupper($historia->distrito2->nombre)) . '</td>
                                    <td width="11%"><h4>Provincia: </h4></td>
                                    <td width="21%">' . htmlentities(strtoupper($historia->provincia2->nombre)) . '</td>
                                    <td width="15%"><h4>Departamento: </h4></td>
                                    <td width="22%">' . htmlentities(strtoupper($historia->departamento2->nombre)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="11%"><h4>Dirección: </h4></td>
                                    <td width="89%">' . htmlentities(strtoupper($historia->persona->direccion)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="10%"><h4>Teléfono: </h4></td>
                                    <td width="53%">' . htmlentities(strtoupper($historia->persona->telefono)) . '</td>
                                    <td width="9%"><h4>IPRESS: </h4></td>
                                    <td width="28%">NEFROCIX SAC</td>
                                </tr>

                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%" align="left">
                                        <table width="100%" height="100%" cellpadding="4" border="1">
                                            <tr>
                                                <td width="20%"><h4>Historia clínica </h4></td>
                                                <td width="80%">' . htmlentities(strtoupper($historia->numero)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="20%"><h4>Historia nutricional </h4></td>
                                                <td width="80%">' . htmlentities(strtoupper($hc->txtHistoriaNutricional)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="100%"><h4>Medidas antropométricas </h4></td>
                                            </tr>
                                            <tr>
                                                <td width="20%"><h4>PESO SECO: </h4></td>
                                                <td width="14%">' . htmlentities($hc->txtPesoseco) . ' Kg.</td>
                                                <td width="20%"><h4>PESO ACTUAL: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtPesoseco) . ' Kg.</td>
                                                <td width="20%"><h4>PESO USUAL: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtPesousual) . ' Kg.</td>
                                            </tr>
                                            <tr>
                                                <td width="20%"><h4>PESO IDEAL: </h4></td>
                                                <td width="14%">' . htmlentities($hc->txtPesoideal) . ' Kg.</td>
                                                <td width="20%"><h4>TALLA: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtTalla) . ' m.</td>
                                                <td width="20%"><h4>IMC: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtIMC) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="100%"><h4>Resultados bioquímicos </h4></td>
                                            </tr>
                                            <tr>
                                                <td width="20%"><h4>HEMOGLOBINA: </h4></td>
                                                <td width="14%">' . htmlentities($hc->txtHemoglobina) . ' g/dl</td>
                                                <td width="20%"><h4>HEMATOCRITO: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtHematocrito) . ' %</td>
                                                <td width="20%"><h4>UREA POST: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtUreapost) . ' mg/dl</td>
                                            </tr>
                                            <tr>
                                                <td width="15%"><h4>CREATININA: </h4></td>
                                                <td width="14%">' . htmlentities($hc->txtCreatinina) . ' mg/dl</td>
                                                <td width="7%"><h4>CA: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtCalcio) . ' mg/dl</td>
                                                <td width="7%"><h4>P: </h4></td>
                                                <td width="13%">' . htmlentities($hc->txtFosforo) . ' mg/dl</td>
                                                <td width="15%"><h4>UREA PRE: </h4></td>
                                                <td width="16%">' . htmlentities($hc->txtUreapre) . ' mg/dl</td>
                                            </tr>
                                            <tr>
                                                <td width="25%"><h4>Resultado del último Malnutrition Inflammation Score </h4></td>
                                                <td width="75%">' . htmlentities(strtoupper($hc->txtReultimo)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="25%"><h4>Diagnóstico nutricional </h4></td>
                                                <td width="75%">' . htmlentities(strtoupper($hc->txtDiagnostico)) . '<br>' . strtoupper($cies) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="25%"><h4>Recomendaciones generales </h4></td>
                                                <td width="75%">' . htmlentities(strtoupper($hc->txtRecoge)) . '</td>
                                            </tr>
                                            <tr>
                                                <td width="25%"><h4>Recomendaciones dietéticas </h4></td>
                                                <td width="75%">' . htmlentities(strtoupper($hc->txtRedie)) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <tr align="left">
                                    <td width="8%"><h4>Fecha: </h4></td>
                                    <td width="24%">' . date('d-m-Y', strtotime($hc->fecha_atencion)) . '</td>
                                    <td width="8%"><h4>Hora: </h4></td>
                                    <td width="15%">' . date('H:i', strtotime($hc->fecha_atencion)) . ' hrs.</td>
                                </tr>
                                <tr align="left">
                                    <td width="15%"><h4>Profesional: </h4></td>
                                    <td width="85%">' . htmlentities(($hc->doctor !== null ? ($hc->doctor->apellidopaterno . " " . $hc->doctor->apellidomaterno . " " . $hc->doctor->nombres) : "")) . '</td>
                                </tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr>
                                    <td width="100%">
                                        <table width="100%" height="100%" cellpadding="1">
                                            <tr>
                                                <td width="50%" style="font-size:8px;">_____________________________</td>
                                                <td width="50%" style="font-size:8px;">___________________________________________</td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-size:8px;">Firma del paciente y huella</td>
                                                <td width="50%" style="font-size:8px;">Firma y Sello del Responsable de la Atención</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>';

            $pdf::writeHTML($tbl, true, false, true, false, 'C');

            $pdf::Output('Historia.pdf');
        }
    }

    public function pdfReporte2(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $hc = ConsultaNefrologica::find($request->input('id'));

        // Anteriores

        $examenesGeneral_old = array(
            "86703" => "ELISA o prueba rápida para HIV-1 y HIV-2",
            "87340" => "Detección de antígeno de superficie de virus de Hepatitis B (HBsAg) por ELISA",
            "86706" => "Detección de anticuerpos para antígeno de superficie Hepatitis B (HBs-Ag)",
            "86704" => "Detección de anticuerpos totales para núcleo de virus de Hepatitis B (Total Anti-Hbcore)",
            "86803" => "Determinación de anticuerpos para Hepatitis C",
            "86592" => "Prueba de sífilis cualitativa (VDRL, RPR)",
            "84520" => "Úrea",        
            "82565" => "Creatinina en sangre",        
            "85014" => "Hematocrito",
            "80051" => "Electrolitos séricos",
            "85018" => "Dosaje de hemoglobina",        
            "84100" => "Fósforo en sangre",        
            "82310" => "Calcio sérico",
            "84450" => "TGO transaminasa glutámico oxalacética",
            "84460" => "TGP transaminasa glutámico pirúvica",
            "84075" => "Fosfatasa Alcalina",
            "83970" => "Paratohormona (PTH)",
            "83540" => "Hierro sérico",
            "82728" => "Ferritina",
            "84466" => "Saturación de transferrina",        
            "84165" => "Proteínas; fraccionamiento y determinación cuantitativa por electroforesis",
            "82040" => "Dosaje de Albúmina; suero, plasma o sangre total",
        );

        // Nuevos

        $examenesGeneral_new = array(
            "84520" => "Nitrógeno ureico; cuantitativo",
            "82565" => "Dosaje de Creatinina en sangre",
            "85014" => "Hematocrito",
            "85018" => "Hemoglobina",
            "80051" => "Perfil de electrolito",
            "84100" => "Dosaje de Fósforo inorgánico (fosfato)",
            "82310" => "Dosaje de Calcio; total",
            "84075" => "Dosaje de Fosfatasa, alcalina",
            "84450" => "Aspartato amino transferasa (AST) (SGOT)",
            "84460" => "Transferasa; amino alanina (ALT) (SGPT)",
            "86703" => "Anticuerpo; HIV-1 y HIV-2, análisis único",
            "86592" => "Prueba de sífilis; anticuerpo no treponémico; cualitativo (p. ej. VDRL, RPR, ART)",
            "83970" => "Dosaje de Paratohormona (hormona paratiroidea)",
            "87340" => "Detección de antígenos de agentes infeccioso mediante técnica de inmunoensayo enzimático, cualitativo o semicuantitativo, método de varios pasos; hepatitis B antpigeno de superficie (HBsAg)",
            "86706" => "Anticuerpo contra el antígeno de superficie de la hepatitis B (HBsAb)",
            "86704" => "Anticuerpo contra el antígeno de la nucleocápside de la hepatitis B (HBcAb); total",
            "86803" => "Anticuerpo contra la hepatitis C",
            "83540" => "Dosaje de Hierro",
            "82728" => "Dosaje de Ferritina",
            "84466" => "Transferrina",
            "82040" => "Dosaje de Albúmina; suero, plasma o sangre total",
            "84165" => "Proteínas; fraccionamiento y determinación cuantitativa por electroforesis",
        );

        $examenesGeneral = null;        

        if ($hc == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $historia = Historia::where('person_id', '=', $hc->persona_id)->first();
            $pdf      = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set margins
            $pdf::SetMargins(13, 3, 10);

            $pdf::SetTitle('AtencionHemoMensual');
            $pdf::AddPage();

            $pdf::SetFont('', '', 8);

            //cantidad a la semana

            $ordencitas    = explode(';', $historia->ordencitas);
            $frecuencia    = count($ordencitas) - 1;
            $cantidadalmes = 0;

            //Calculo proximo mes

            $mesactual     = (int) date("m", strtotime($hc->fecha));
            $anito         = (int) date("Y", strtotime($hc->fecha));
            $messiguiente  = "";
            $messiguienten = $mesactual + 1;

            switch ($mesactual) {
                case 1:$messiguiente = "FEBRERO";
                    break;
                case 2:$messiguiente = "MARZO";
                    break;
                case 3:$messiguiente = "ABRIL";
                    break;
                case 4:$messiguiente = "MAYO";
                    break;
                case 5:$messiguiente = "JUNIO";
                    break;
                case 6:$messiguiente = "JULIO";
                    break;
                case 7:$messiguiente = "AGOSTO";
                    break;
                case 8:$messiguiente = "SETIEMBRE";
                    break;
                case 9:$messiguiente = "OCTUBRE";
                    break;
                case 10:$messiguiente = "NOVIEMBRE";
                    break;
                case 11:$messiguiente = "DICIEMBRE";
                    break;
                case 12:$messiguiente = "ENERO";
                    break;
            }

            if ($mesactual == 12) {
                $anito++;
                $messiguienten = 1;
            }

            $messiguiente .= " " . $anito;

            //cantidad al mes actual

            //$diasenmes = cal_days_in_month(CAL_GREGORIAN, $messiguienten, $anito);

            $diasenmes = (cal_days_in_month(CAL_GREGORIAN, $messiguienten, $anito) <= 30 ? cal_days_in_month(CAL_GREGORIAN, $messiguienten, $anito) : 30);

            for ($i = 1; $i <= 31; $i++) {
                $fechadetratamiento = $anito . "-" . $messiguienten . "-" . $i;
                if (checkdate($mesactual, $i, $anito)) {
                    $var = (date("w", strtotime($fechadetratamiento)) == 0 ? 7 : date("w", strtotime($fechadetratamiento)));
                    foreach ($ordencitas as $diacita) {
                        if ($var == ((int) $diacita)) {
                            $cantidadalmes++;
                        }
                    }
                } else {
                    break;
                }
            }

            $cies = '';
            $cs   = explode(';', $hc->txtDiagnostico2);
            foreach ($cs as $ca) {
                $cc = Cie::find($ca);
                if ($cc !== null) {
                    $cies .= $cc->codigo . ' - ' . $cc->descripcion . '<br>';
                }
            }

            $cies = substr($cies, 0, strlen($cies) - 4);

            if ($historia->persona->fechanacimiento != '') {
                $fechanacimiento = new DateTime($historia->persona->fechanacimiento);
                $hoy             = new DateTime();
                $annos           = $hoy->diff($fechanacimiento);
                $edadpaciente    = $annos->y;
            } else {
                $edadpaciente = '-';
            }

            $cicies = '';

            $chies = explode(";", $hc->cadenacies);
            $da    = 1;
            foreach ($chies as $ches) {
                $ciecito = Cie::find($ches);
                if ($ciecito !== null) {
                    $cicies .= $ciecito->codigo . ".- " . $ciecito->descripcion . "<br>";
                    $da++;
                }
            }

            $cicies = substr($cicies, 0, strlen($cicies) - 4);

            $sit        = $hc->situacion;
            $dosultimas = ConsultaNefrologica::where('persona_id', '=', $historia->person_id)
                ->orderBy('fecha', 'DESC')
                ->where("fecha", "<=", $hc->fecha)
                ->limit(2)
                ->get();

            $penultima = null;
            if (!empty($dosultimas[1])) {
                $penultima = $dosultimas[1];
            }

            $analisis = "";

            if ($penultima !== null) {
                $analisis = $penultima->situacion;
            }

            $cadenaderesultados = '';

            /*if($sit !== "NUEVO") {
            if($sit==="MENSUAL") {
            if($penultima!==NULL) {
            if($penultima->situacion=='TRIMESTRAL'||$penultima->situacion=='SEMESTRAL') {
            $cadenaderesultados .= '<tr align="left">
            <td width="13%"><h4>Úrea Pre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtUre . ' mg/dl</td>
            <td width="13%"><h4>Úrea Post </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtUre2 . ' mg/dl</td>
            <td width="21%"><h4>Creatinina en sangre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtCre . ' mg/dl</td>
            <td width="13%"><h4>Hematocrito </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtHem . ' %</td>
            </tr>
            <tr align="left">
            <td width="24%"><h4>Dosaje de hemoglobina </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtDos . ' g/dl</td>

            <td width="4%"><h4>Na</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtSodio . ' mmol/L</td>
            <td width="4%"><h4>K</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtPotasio . ' mmol/L</td>
            <td width="4%"><h4>Cl</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtCloro . ' mmol/L</td>

            <td width="23%"><h4>Fósforo en sangre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtFos . ' mg/dl</td>
            </tr>
            <tr align="left">
            <td width="12%"><h4>Calcio sérico </h4></td>
            <td width="8%" style="font-size:7px;">' . $penultima->txtCal . ' mg/dl</td>
            <td width="32%"><h4>TGO transaminasa glutámico oxalacética </h4></td>
            <td width="9%" style="font-size:7px;">' . $penultima->txtTgo . '</td>
            <td width="30%"><h4>TGP transaminasa glutámico pirúvica </h4></td>
            <td width="9%" style="font-size:7px;">' . $penultima->txtTgp . '</td>
            </tr>';
            if($penultima->situacion=='TRIMESTRAL') {
            $cadenaderesultados .= '<tr align="left">
            <td width="80%"><h4>Proteínas; fraccionamiento y determinación cuantitativa por electroforesis </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtPro . '</td>
            </tr>
            <tr align="left">
            <td width="80%"><h4>Fosfatasa Alcalina </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtFos2 . '</td>
            </tr>';
            }
            if($penultima->situacion=='SEMESTRAL') {
            $cadenaderesultados .= '<tr align="left">
            <td width="80%"><h4>Proteínas; fraccionamiento y determinación cuantitativa por electroforesis </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtPro . '</td>
            </tr>
            <tr align="left">
            <td width="30%"><h4>Fosfatasa Alcalina </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtFos2 . '</td>
            <td width="30%"><h4>ELISA o prueba rápida para HIV-1 y HIV-2 </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtEli . '</td>
            </tr>';
            $cadenaderesultados .= '
            <tr align="left">
            <td width="30%"><h4>Prueba de sífilis cualitativa (VDRL, RPR) </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtPru . '</td>
            <td width="30%"><h4>Paratohormona (PTH) </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtPar . '</td>
            </tr>
            <tr align="left">
            <td width="80%"><h4>Detección de antígeno de superficie de virus de Hepatitis B (HBsAg) por ELISA </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtDet . '</td>
            </tr>
            <tr align="left">
            <td width="80%"><h4>Detección de anticuerpos para antígeno de superficie Hepatitis B (HBs-Ag) </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtDet2 . '</td>
            </tr>
            <tr align="left">
            <td width="80%"><h4>Detección de anticuerpos para antígeno de superficie Hepatitis B (HBs-Ag) </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtDet2 . '</td>
            </tr>
            <tr align="left">
            <td width="80%"><h4>Detección de anticuerpos totales para núcleo de virus de Hepatitis B (Total Anti-Hbcore) </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtDet3 . '</td>
            </tr>
            <tr align="left">
            <td width="80%"><h4>Determinación de anticuerpos para Hepatitis C </h4></td>
            <td width="20%" style="font-size:7px;">' . $penultima->txtDet4 . '</td>
            </tr>
            <tr align="left">
            <td width="18%"><h4>Hierro sérico </h4></td>
            <td width="12%" style="font-size:7px;">' . $penultima->txtHie . '</td>
            <td width="18%"><h4>Ferritina </h4></td>
            <td width="12%" style="font-size:7px;">' . $penultima->txtFer . '</td>
            <td width="28%"><h4>Saturación de transferrina </h4></td>
            <td width="12%" style="font-size:7px;">' . $penultima->txtSat . '</td>
            </tr>';
            }
            }
            }
            }
            //analizo si el bimensual fue nuevo
            if($sit==="BIMENSUAL") {
            if($penultima!==NULL) {
            //Datos mensuales
            $cadenaderesultados .= '<tr align="left">
            <td width="13%"><h4>Úrea Pre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtUre . ' mg/dl</td>
            <td width="13%"><h4>Úrea Post </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtUre2 . ' mg/dl</td>
            <td width="21%"><h4>Creatinina en sangre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtCre . ' mg/dl</td>
            <td width="13%"><h4>Hematocrito </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtHem . ' %</td>
            </tr>
            <tr align="left">
            <td width="24%"><h4>Dosaje de hemoglobina </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtDos . ' g/dl</td>

            <td width="4%"><h4>Na</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtSodio . ' mmol/L</td>
            <td width="4%"><h4>K</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtPotasio . ' mmol/L</td>
            <td width="4%"><h4>Cl</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtCloro . ' mmol/L</td>

            <td width="23%"><h4>Fósforo en sangre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtFos . ' mg/dl</td>
            </tr>
            <tr align="left">
            <td width="12%"><h4>Calcio sérico </h4></td>
            <td width="8%" style="font-size:7px;">' . $penultima->txtCal . ' mg/dl</td>
            </tr>';
            }
            }
            if($sit==="TRIMESTRAL"||$sit==="SEMESTRAL") {
            $cadenaderesultados .= '<tr align="left">
            <td width="13%"><h4>Úrea Pre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtUre . ' mg/dl</td>
            <td width="13%"><h4>Úrea Post </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtUre2 . ' mg/dl</td>
            <td width="21%"><h4>Creatinina en sangre </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtCre . ' mg/dl</td>
            <td width="13%"><h4>Hematocrito </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtHem . ' /</td>
            </tr>
            <tr align="left">
            <td width="24%"><h4>Dosaje de hemoglobina </h4></td>
            <td width="10%" style="font-size:7px;">' . $penultima->txtDos . ' g/dl</td>

            <td width="4%"><h4>Na</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtSodio . ' mmol/L</td>
            <td width="4%"><h4>K</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtPotasio . ' mmol/L</td>
            <td width="4%"><h4>Cl</h4></td>
            <td width="7%" style="font-size:7px;">' . $penultima->txtCloro . ' mmol/L</td>

            <td width="23%"><h4>Fósforo en sangre </h4></td>
            <td width="10%">' . $penultima->txtFos . ' mg/dl</td>
            </tr>
            <tr align="left">
            <td width="12%"><h4>Calcio sérico </h4></td>
            <td width="8%" style="font-size:7px;">' . $penultima->txtCal . ' mg/dl</td>
            <td width="32%"><h4>TGO transaminasa glutámico oxalacética </h4></td>
            <td width="9%" style="font-size:7px;">' . $penultima->txtTgo . '</td>
            <td width="30%"><h4>TGP transaminasa glutámico pirúvica </h4></td>
            <td width="9%" style="font-size:7px;">' . $penultima->txtTgp . '</td>
            </tr>';
            }
            } else {
            $cadenaderesultados .= '<tr align="center">
            <td width="100%"><h4>Es un paciente nuevo.</h4></td>
            </tr>';
            }*/

            //////////////////////////////////////////////////////////////////////////////////////

            if ($sit !== "N") {
                if ($penultima !== null) {

                    if(date("Y-m-d", strtotime($hc->fecha_atencion)) >= date("Y-m-d", strtotime('2021-08-03'))) {
                        $examenesGeneral = $examenesGeneral_new;
                    } else {
                        $examenesGeneral = $examenesGeneral_old;
                    }

                    // LA ANTERIOR NO FUE NUEVA

                    if ($penultima->situacion !== "N") {

                        // MENSUALES EN TODOS LOS MESES

                        $cadenaderesultados .= '<tr align="left">
                            <td width="12.5%"><h4>Úrea Pre</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtUre) . ' mg/dl</td>
                            <td width="12.5%"><h4>Úrea Post</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtUre2) . ' mg/dl</td>
                            <td width="12.5%"><h4>' . htmlentities($examenesGeneral['82565']) . '</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtCre) . ' mg/dl</td>
                            <td width="12.5%"><h4>' . htmlentities($examenesGeneral['85014']) . '</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtHem) . '%</td>
                        </tr>
                        <tr align="left">
                            <td width="12.5%"><h4>' . htmlentities($examenesGeneral['85018']) .'</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtDos) . ' g/dl</td>
                            <td width="12.5%"><h4>Sodio</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtSodio) . ' mmol/L</td>
                            <td width="12.5%"><h4>Potasio</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtPotasio) . ' mmol/L</td>
                            <td width="12.5%"><h4>Cloro</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtCloro) . ' mmol/L</td>
                        </tr>
                        <tr align="left">
                            <td width="12.5%"><h4>' . htmlentities($examenesGeneral['84100']) .'</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtFos) . ' mg/dl</td>
                            <td width="12.5%"><h4>' . htmlentities($examenesGeneral['82310']) .'</h4></td>
                            <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtCal) . ' mg/dl</td>';

                        // 5TO MES -> SEMESTRALES

                        if ($penultima->txtTipoDatos == 0) {
                            $cadenaderesultados .= '
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['84450']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtTgo) . '</td>
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['84460']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtTgp) . '</td>
                            </tr>
                            <tr align="left">
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['82040']) .'</h4></td>
                                <td width="9%" style="font-size:7px;">' . htmlentities($penultima->txtAlbu) . ' (g/dl)</td>
                                <td width="14%"><h4>' . htmlentities($examenesGeneral['84075']) .'</h4></td>
                                <td width="9%" style="font-size:7px;">' . htmlentities($penultima->txtFos2) . '</td>
                                <td width="21%"><h4>' . htmlentities($examenesGeneral['86592']) .'</h4></td>
                                <td width="9%" style="font-size:7px;">' . htmlentities($penultima->txtPru) . '</td>
                                <td width="16.5%"><h4>' . htmlentities($examenesGeneral['83970']) .'</h4></td>
                                <td width="9%" style="font-size:7px;">' . htmlentities($penultima->txtPar) . '</td>
                            </tr>
                            <tr align="left">
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['87340']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet) . '</td>
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['86706']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet2) . '</td>
                            </tr>
                            <tr align="left">
                                <td width="80%"><h4>' . htmlentities($examenesGeneral['86703']) .'</h4></td>
                                <td width="20%" style="font-size:7px;">' . htmlentities($penultima->txtEli) . '</td>
                            </tr>
                            <tr align="left">
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['86704']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet3) . '</td>
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['86803']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet4) . '</td>
                            </tr>
                            <tr align="left">
                                <td width="18%"><h4>' . htmlentities($examenesGeneral['83540']) .'</h4></td>
                                <td width="12%" style="font-size:7px;">' . htmlentities($penultima->txtHie) . '</td>
                                <td width="18%"><h4>' . htmlentities($examenesGeneral['82728']) .'</h4></td>
                                <td width="12%" style="font-size:7px;">' . htmlentities($penultima->txtFer) . '</td>
                                <td width="28%"><h4>' . htmlentities($examenesGeneral['84466']) .'</h4></td>
                                <td width="12%" style="font-size:7px;">' . htmlentities($penultima->txtTransfe) . '%</td>
                            </tr>';
                        }

                        // NUEVO o 4TO MES -> MENSUALES

                        if ($penultima->txtTipoDatos == 1 || $penultima->txtTipoDatos == 5) {
                            $cadenaderesultados .= '</tr>';
                        }

                        // 1ER MES o 3TO MES -> BIMENSUALES

                        if ($penultima->txtTipoDatos == 2 || $penultima->txtTipoDatos == 4) {
                            $cadenaderesultados .= '
                            <td width="12.5%"><h4>' . htmlentities($examenesGeneral['84450']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtTgo) . '</td>
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['84460']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtTgp) . '</td>
                            </tr>';
                        }

                        // 2DO MES -> TRIMESTRALES

                        if ($penultima->txtTipoDatos == 3) {
                            $cadenaderesultados .= '
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['82040']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtPro) . ' (g/dl)</td>
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['84075']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtFos2) . '</td>
                            </tr>';
                        }
                    } else {
                        $cadenaderesultados .= '
                        <tr align="left">
                            <td width="40%"><h4>' . htmlentities($examenesGeneral['87340']) .'</h4></td>
                            <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet) . '</td>
                            <td width="40%"><h4>' . htmlentities($examenesGeneral['86706']) .'</h4></td>
                            <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet2) . '</td>
                        </tr>
                        <tr align="left">
                            <td width="40%"><h4>' . htmlentities($examenesGeneral['86704']) .'</h4></td>
                            <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet3) . '</td>
                            <td width="40%"><h4>' . htmlentities($examenesGeneral['86803']) .'</h4></td>
                            <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtDet4) . '</td>
                        </tr>';

                        if ($penultima->txtDatosMensuales == "SI") {
                            $cadenaderesultados .= '<tr align="left">
                                <td width="12.5%"><h4>Úrea Pre</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtUre) . ' mg/dl</td>
                                <td width="12.5%"><h4>Úrea Post</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtUre2) . ' mg/dl</td>
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['82565']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtCre) . ' mg/dl</td>
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['85014']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtHem) . '%</td>
                            </tr>
                            <tr align="left">
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['85018']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtDos) . ' g/dl</td>
                                <td width="12.5%"><h4>Sodio</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtSodio) . ' mmol/L</td>
                                <td width="12.5%"><h4>Potasio</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtPotasio) . ' mmol/L</td>
                                <td width="12.5%"><h4>Cloro</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtCloro) . ' mmol/L</td>
                            </tr>
                            <tr align="left">
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['84100']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtFos) . ' mg/dl</td>
                                <td width="12.5%"><h4>' . htmlentities($examenesGeneral['82310']) .'</h4></td>
                                <td width="12.5%" style="font-size:7px;">' . htmlentities($penultima->txtCal) . ' mg/dl</td>
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['86703']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtEli) . '</td>
                            </tr>
                            <tr align="left">
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['86592']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtPru) . '</td>
                                <td width="50%"></td>
                            </tr>';
                        } else {
                            $cadenaderesultados .= '<tr align="left">
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['86703']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtEli) . '</td>
                                <td width="40%"><h4>' . htmlentities($examenesGeneral['86592']) .'</h4></td>
                                <td width="10%" style="font-size:7px;">' . htmlentities($penultima->txtPru) . '</td>
                            </tr>';
                        }
                    }
                }
            } else {
                /*
            $cadenaderesultados .= '<tr align="left">
            <td width="12.5%"><h4>Úrea Pre</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtUre) . ' mg/dl</td>
            <td width="12.5%"><h4>Úrea Post</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtUre2) . ' mg/dl</td>
            <td width="12.5%"><h4>Creatinina</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtCre) . ' mg/dl</td>
            <td width="12.5%"><h4>Hematocrito</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtHem) . ' %</td>
            </tr>
            <tr align="left">
            <td width="12.5%"><h4>' . htmlentities($examenesGeneral['85018']) .'</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtDos) . ' g/dl</td>
            <td width="12.5%"><h4>Sodio</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtSodio) . ' mmol/L</td>
            <td width="12.5%"><h4>Potasio</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtPotasio) . ' mmol/L</td>
            <td width="12.5%"><h4>Cloro</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtCloro) . ' mmol/L</td>
            </tr>
            <tr align="left">
            <td width="12.5%"><h4>Fósforo en sangre </h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtFos) . ' mg/dl</td>
            <td width="12.5%"><h4>Calcio sérico </h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtCal) . ' mg/dl</td>
            <td width="12.5%"><h4>TGO</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtTgo) . '</td>
            <td width="12.5%"><h4>TGP</h4></td>
            <td width="12.5%" style="font-size:7px;">' . htmlentities($hc->txtTgp) . '</td>
            </tr>
            <tr align="left">
            <td width="12.5%"><h4>Proteínas Totales</h4></td>
            <td width="9%" style="font-size:7px;">' . htmlentities($hc->txtPro) . ' (g/dl)</td>
            <td width="14%"><h4>Fosfatasa Alcalina</h4></td>
            <td width="9%" style="font-size:7px;">' . htmlentities($hc->txtFos2) . '</td>
            <td width="21%"><h4>Prueba de sífilis (VDRL, RPR)</h4></td>
            <td width="9%" style="font-size:7px;">' . htmlentities($hc->txtPru) . '</td>
            <td width="16.5%"><h4>Paratohormona (PTH) </h4></td>
            <td width="9%" style="font-size:7px;">' . htmlentities($hc->txtPar) . '</td>
            </tr>
            <tr align="left">
            <td width="40%"><h4>Ant. de superficie virus Hep. B (HBsAg) por ELISA </h4></td>
            <td width="10%" style="font-size:7px;">' . htmlentities($hc->txtDet) . '</td>
            <td width="40%"><h4>Det. de ant. para antígeno superficie Hep. B (HBs-Ag) </h4></td>
            <td width="10%" style="font-size:7px;">' . htmlentities($hc->txtDet2) . '</td>
            </tr>
            <tr align="left">
            <td width="40%"><h4>Antic. tot. para núcleo de virus Hep. B (Total Anti-Hbcore) </h4></td>
            <td width="10%" style="font-size:7px;">' . htmlentities($hc->txtDet3) . '</td>
            <td width="40%"><h4>Determinación de anticuerpos para Hep. C </h4></td>
            <td width="10%" style="font-size:7px;">' . htmlentities($hc->txtDet4) . '</td>
            </tr>
            <tr align="left">
            <td width="18%"><h4>Hierro sérico</h4></td>
            <td width="12%" style="font-size:7px;">' . htmlentities($hc->txtHie) . '</td>
            <td width="18%"><h4>Ferritina</h4></td>
            <td width="12%" style="font-size:7px;">' . htmlentities($hc->txtFer) . '</td>
            <td width="28%"><h4>Saturación de transferrina</h4></td>
            <td width="12%" style="font-size:7px;">' . htmlentities($hc->txtSat) . '%</td>
            </tr>';
             */
            }

            //////////////////////////////////////////////////////////////////////////////////////

            $resultadosmensualestexto = "EVALUCIÓN NEFROLÓGICA CON RESULTADOS";
            if ($penultima !== null) {
                switch ($penultima->situacion) {
                    case "N":
                        if ($penultima->txtDatosMensuales == "SI") {
                            $resultadosmensualestexto .= " MENSUALES";
                        } else {
                            $resultadosmensualestexto .= " DE MARCADORES VIRALES";
                        }
                        break;
                    case "M":
                        $resultadosmensualestexto .= " MENSUALES";
                        break;
                    case "M-B":
                        //$resultadosmensualestexto = " MENSUALES + BIMENSUALES";
                        $resultadosmensualestexto .= " BIMENSUALES";
                        break;
                    case "M-T":
                        //$resultadosmensualestexto = " MENSUALES + TRIMESTRALES";
                        $resultadosmensualestexto .= " TRIMESTRALES";
                        break;
                    case "M-B-T-S":
                        //$resultadosmensualestexto = " MENSUALES + BIMENSUALES + TRIMESTRALES + SEMESTRALES";
                        $resultadosmensualestexto .= " SEMESTRALES";
                        break;
                }
            }
            if ($hc->situacion == "N") {
                $resultadosmensualestexto = "PACIENTE ES NUEVO";
            }

            //VER SI ES DATOS MENSUALES

            $esdatoemensual = "";
            $estadomarcadores = "";
            if ($hc->txtDatosMensuales == "SI" && $hc->situacion == "N") {
                $esdatoemensual = "X";
                $estadomarcadores = "X";
            }
            if ($penultima !== null && $hc->situacion !== "N") {
                if ($penultima->situacion2 == "M") {
                    $esdatoemensual = "X";
                }
            }

            ///////////////////////////

            $tbl = '
                <table width="100%" height="100%" cellpadding="2">
                    <tr>
                        <td>
                            <table width="100%" height="100%" cellpadding="2" border="1" style="font-size:7x;">
                                <tr align="left">
                                    <td width="33%">
                                        <img src="dist/img/logo2-nefrocix.jpg" width="150px" height="50px">
                                    </td>
                                    <td width="67%" align="center">
                                        <h1>FORMATO DE CONSULTA NEFROLÓGICA </h1>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td width="15%"><h4>Apellidos y nombres </h4></td>
                                    <td width="48%">' . htmlentities($historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres) . '</td>
                                    <td width="7%"><h4>Fecha</h4></td>
                                    <td width="15%">' . date("d/m/Y", strtotime($hc->fecha_atencion)) . '</td>
                                    <td width="7%"><h4>Hora </h4></td>
                                    <td width="8%">' . date("H:i:s", strtotime($hc->fecha_atencion)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="8%"><h4>Edad </h4></td>
                                    <td width="15%">' . htmlentities($edadpaciente) . ' años</td>
                                    <td width="8%"><h4>Sexo </h4></td>
                                    <td width="15%">' . htmlentities(($historia->persona->sexo == "M" ? "MASCULINO" : "FEMENINO")) . '</td>
                                    <td width="16%"><h4>Historia Clínica </h4></td>
                                    <td width="15%">' . htmlentities($historia->numero) . '</td>
                                    <td width="8%"><h4>DNI/CE </h4></td>
                                    <td width="15%">' . htmlentities($historia->persona->dni) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="20%"><h4>Motivo de Consulta </h4></td>
                                    <td width="80%">' . htmlentities($resultadosmensualestexto) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="20%"><h4>Tiempo de enfermedad </h4></td>
                                    <td width="80%">' . htmlentities(strtoupper($hc->tiempoenf)) . ' EN HEMODIÁLISIS</td>
                                </tr>
                                <tr align="left">
                                    <td width="20%"><h4>Anamnesis </h4></td>
                                    <td width="80%">' . htmlentities(strtoupper($hc->anamnesis)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="100%"><h4>EXAMEN FÍSICO </h4></td>
                                </tr>
                                <tr align="left">
                                    <td width="10%"><h4>Peso seco</h4></td>
                                    <td width="8%">' . htmlentities($hc->pesoseco) . ' Kg.</td>
                                    <td width="11%"><h4>Temperatura</h4></td>
                                    <td width="8%">' . htmlentities($hc->temperatura) . ' °C</td>
                                    <td width="4%"><h4>PA.</h4></td>
                                    <td width="8%">' . htmlentities($hc->pa) . '</td>
                                    <td width="4%"><h4>FC.</h4></td>
                                    <td width="8%">' . htmlentities($hc->fc) . '</td>
                                    <td width="4%"><h4>FR.</h4></td>
                                    <td width="8%">' . htmlentities($hc->fr) . '</td>
                                    <td width="6%"><h4>Talla</h4></td>
                                    <td width="7%">' . htmlentities($hc->talla) . ' m.</td>
                                    <td width="5%"><h4>IMC</h4></td>
                                    <td width="9%">' . htmlentities($hc->imc) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="6%"><h4>CAV </h4></td>
                                    <td width="43%">' . htmlentities(strtoupper($hc->cav)) . '</td>
                                    <td width="8%"><h4>TCSC </h4></td>
                                    <td width="43%">' . htmlentities(strtoupper($hc->tcsc)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="10%"><h4>Pulmones </h4></td>
                                    <td width="90%">' . htmlentities(strtoupper($hc->pulmones)) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="15%"><h4>Sistema nervioso </h4></td>
                                    <td width="85%">' . htmlentities(strtoupper(str_replace('<', '<< >>', $hc->sisnervioso))) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="25%"><h4>ALERGIA A MEDICAMENTOS </h4></td>
                                    <td width="25%">' . htmlentities(($hc->txtAlergia == "" || $hc->txtAlergia == null ? "NO" : strtoupper($hc->txtAlergia))) . '</td>
                                    <td width="25%"><h4>VACUNACIÓN </h4></td>
                                    <td width="25%">' . htmlentities(($hc->txtVacunacion == "" || $hc->txtVacunacion == null ? "NO" : strtoupper($hc->txtVacunacion))) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="25%"><h4>REVACUNACIÓN </h4></td>
                                    <td width="25%">' . htmlentities(($hc->txtRevacunacion == "" || $hc->txtRevacunacion == null ? "NO" : strtoupper($hc->txtRevacunacion))) . '</td>
                                    <td width="25%"><h4>TRANSFUSIONES </h4></td>
                                    <td width="25%">' . htmlentities(($hc->txtTransfusiones == "" || $hc->txtTransfusiones == null ? 0 : strtoupper($hc->txtTransfusiones))) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="25%"><h4>DIAGNÓSTICO </h4></td>
                                    <td width="75%">' . strtoupper($cicies) . '</td>
                                </tr>
                                <tr align="left">
                                    <td width="100%"><h4>RESULTADOS DE EXÁMENES DE LABORATORIO </h4></td>
                                </tr>'
            . $cadenaderesultados .

            '<tr align="left">
                                    <td width="30%"><h4>TRATAMIENTO Y OBSERVACION </h4></td>
                                    <td width="70%">' . htmlentities(strtoupper($hc->observacion)) . '</td>
                                </tr>
                                <tr align="center">
                                    <td width="38%"><h4>DESCRIPCION </h4></td>
                                    <td width="16%"><h4>FRECUENCIA </h4></td>
                                    <td width="16%"><h4>CANTIDAD AL MES </h4></td>
                                    <td width="30%"><h4>OBSERVACION </h4></td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">N° SESIONES DE HEMODIALISIS </td>
                                    <td width="16%" style="text-align:center;">' . $hc->f1 . 'V/SEMANA</td>
                                    <td width="16%" style="text-align:center;">' . $hc->c1 . ' SESIONES</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o1)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">EPOETINA ALFA (ERITROPOYETINA) 2000 UI/ML INY 1 ML</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f2 == null || $hc->f2 == "" ? 0 : $this->prueba($hc->f2))) . ' ' . $hc->selectepo . '</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c2 == null || $hc->c2 == "" || $hc->c2 == "0" ? "" : $this->prueba($hc->c2))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o2)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">HIERRO (COMO SACARATO) 20MG FE/ML INY 5 ML</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f3 == null || $hc->f3 == "" ? 0 : $this->prueba($hc->f3))) . ' AMPOLLAS/MES</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c3 == null || $hc->c3 == "" || $hc->c3 == "0" ? "" : $this->prueba($hc->c3))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o3)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">VITAMINA B12 HIDROXICOBALAMINA 1MG/ML INY 1ML</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f4 == null || $hc->f4 == "" ? 0 : $this->prueba($hc->f4))) . ' AMPOLLAS/MES</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c4 == null || $hc->c4 == "" || $hc->c4 == "0" ? "" : $this->prueba($hc->c4))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o4)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">CALCIO CARBONATO 500 MG (EQUIV.A 500 MG DE CALCIO) TAB</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f5 == null || $hc->f5 == "" ? 0 : $this->prueba($hc->f5))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c5 == null || $hc->c5 == "" || $hc->c5 == "0" ? "" : $this->prueba($hc->c5))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o5)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">PIRIDOXINA 50MG TAB</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f6 == null || $hc->f6 == "" ? 0 : $this->prueba($hc->f6))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c6 == null || $hc->c6 == "" || $hc->c6 == "0" ? "" : $this->prueba($hc->c6))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o6)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">TIAMINA 100MG TAB</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f7 == null || $hc->f7 == "" ? 0 : $this->prueba($hc->f7))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c7 == null || $hc->c7 == "" || $hc->c7 == "0" ? "" : $this->prueba($hc->c7))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o7)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">ÁCIDO FÓLICO 0.5 MG TAB</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f8 == null || $hc->f8 == "" ? 0 : $this->prueba($hc->f8))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c8 == null || $hc->c8 == "" || $hc->c8 == "0" ? "" : $this->prueba($hc->c8))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o8)) . '</td>
                                </tr>

                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">CALCITRIOL 1 MCG/ML INY</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f9 == null || $hc->f9 == "" ? 0 : $this->prueba($hc->f9))) . ' ' . $hc->selectcalcit . '</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c9 == null || $hc->c9 == "" || $hc->c9 == "0" ? "" : $this->prueba($hc->c9))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o9)) . '</td>
                                </tr>

                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">CALCITRIOL 0.25ug CAP (**)</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f91 == null || $hc->f91 == "" ? 0 : $this->prueba($hc->f91))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c91 == null || $hc->c91 == "" || $hc->c91 == "0" ? "" : $this->prueba($hc->c91))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o91)) . '</td>
                                </tr>

                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">ENALAPRIL MALEATO 10 MG TAB</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f10 == null || $hc->f10 == "" ? 0 : $this->prueba($hc->f10))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c10 == null || $hc->c10 == "" || $hc->c10 == "0" ? "" : $this->prueba($hc->c10))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o10)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">CAPTOPRIL 25 MG TAB</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f11 == null || $hc->f11 == "" ? 0 : $this->prueba($hc->f11))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c11 == null || $hc->c11 == "" || $hc->c11 == "0" ? "" : $this->prueba($hc->c11))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o11)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">AMLODIPINO (COMO BESILATO) 10 MG TAB</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f12 == null || $hc->f12 == "" ? 0 : $this->prueba($hc->f12))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c12 == null || $hc->c12 == "" || $hc->c12 == "0" ? "" : $this->prueba($hc->c12))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o12)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">NIFEDIPINO 10 MG</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f13 == null || $hc->f13 == "" ? 0 : $this->prueba($hc->f13))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c13 == null || $hc->c13 == "" || $hc->c13 == "0" ? "" : $this->prueba($hc->c13))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o13)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">NIFEDIPINO DE 30 MG</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f14 == null || $hc->f14 == "" ? 0 : $this->prueba($hc->f14))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c14 == null || $hc->c14 == "" || $hc->c14 == "0" ? "" : $this->prueba($hc->c14))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o14)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">METILDOPA 250 MG</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f15 == null || $hc->f15 == "" ? 0 : $this->prueba($hc->f15))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c15 == null || $hc->c15 == "" || $hc->c15 == "0" ? "" : $this->prueba($hc->c15))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o15)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">ATENOLOL 100 MG</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f16 == null || $hc->f16 == "" ? 0 : $this->prueba($hc->f16))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c16 == null || $hc->c16 == "" || $hc->c16 == "0" ? "" : $this->prueba($hc->c16))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o16)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="38%">LOSARTAN 50 MG</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->f17 == null || $hc->f17 == "" ? 0 : $this->prueba($hc->f17))) . ' TAB/DIA X ' . htmlentities($diasenmes) . ' DÍAS</td>
                                    <td width="16%" style="text-align:center;">' . htmlentities(($hc->c17 == null || $hc->c17 == "" || $hc->c17 == "0" ? "" : $this->prueba($hc->c17))) . '</td>
                                    <td width="30%">' . htmlentities(strtoupper($hc->o17)) . '</td>
                                </tr>
                                <tr align="left" style="font-size:6px;">
                                    <td width="8%"><h4>ANÁLISIS </h4></td>
                                    <td width="20%"><h4>Marcadores Virales </h4></td>
                                    <td width="8%"><h4 style="text-align:center;">' . htmlentities($estadomarcadores) . '</h4></td>
                                    <td width="8%"><h4>Mensual </h4></td>
                                    <td width="8%"><h4 style="text-align:center;">' . htmlentities($esdatoemensual) . '</h4></td>
                                    <td width="8%"><h4>Bimensual</h4></td>
                                    <td width="8%"><h4 style="text-align:center;">' . htmlentities(($penultima !== null ? ($penultima->situacion2 == "M-B" ? "X" : "") : "")) . '</h4></td>
                                    <td width="8%"><h4>Trimestral</h4></td>
                                    <td width="8%"><h4 style="text-align:center;">' . htmlentities(($penultima !== null ? ($penultima->situacion2 == "M-T" ? "X" : "") : "")) . '</h4></td>
                                    <td width="8%"><h4>Semestral</h4></td>
                                    <td width="8%"><h4 style="text-align:center;">' . htmlentities(($penultima !== null ? ($penultima->situacion2 == "M-B-T-S" ? "X" : "") : "")) . '</h4></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table width="100%" height="100%" cellpadding="1" style="font-size:7x;">
                    <tr align="left">
                        <td width="1%"></td>
                        <td width="99%"><h4>Proxima cita: ' . htmlentities($messiguiente) . '</h4></td>
                    </tr>
                    <tr align="left">
                        <td width="1%"></td>
                        <td width="99%"><h4>Atendido por: ' . htmlentities($hc->doctor == null ? "" : ($hc->doctor->apellidopaterno . " " . $hc->doctor->apellidomaterno . " " . $hc->doctor->nombres)) . '</h4></td>
                    </tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr align="left">
                        <td width="1%"></td>
                        <td width="31%"></td>
                        <td width="34%">____________________________________</td>
                        <td width="34%">____________________________________</td>
                    </tr>
                    <tr align="left">
                        <td width="32%"></td>
                        <td width="34%">Firma y Sello</td>
                        <td width="34%">Firma del Paciente</td>
                    </tr>
                    <tr align="left">
                        <td width="32%"></td>
                        <td width="34%">(Colegio prof.)</td>
                        <td width="34%"></td>
                    </tr>
                </table>';

            /*<tr align="left" style="font-size:6px;">
            <td width="28%"><h4>ANÁLISIS </h4></td>
            <td width="10%"><h4>Mensual </h4></td>
            <td width="8%"><h4 style="text-align:center;">' . htmlentities(($hc!==NULL?($hc->situacion!=="N"?"X":""):"")) . '</h4></td>
            <td width="8%"><h4>Bimensual</h4></td>
            <td width="8%"><h4 style="text-align:center;">' . htmlentities(($hc!==NULL?($hc->situacion=="M-B"||$hc->situacion=="M-B-T-S"?"X":""):"")) . '</h4></td>
            <td width="8%"><h4>Trimestral</h4></td>
            <td width="8%"><h4 style="text-align:center;">' . htmlentities(($hc!==NULL?($hc->situacion=="M-T"||$hc->situacion=="M-B-T-S"?"X":""):"")) . '</h4></td>
            <td width="8%"><h4>Semestral</h4></td>
            <td width="14%"><h4 style="text-align:center;">' . htmlentities(($hc!==NULL?($hc->situacion=="M-B-T-S"?"X":""):"")) . '</h4></td>
            </tr>*/

            $pdf::writeHTML($tbl, true, false, true, false, '');

            $pdf::Output('Historia.pdf');
        }
    }

    public function crearConsultasMensuales($tipoconsulta)
    {
        date_default_timezone_set('America/Lima');
        $mes       = date('m');
        $year      = date('Y');
        $fecha     = date("Y-m-d");
        $resultado = Historia::select('historia.id', 'historia.person_id')
            ->join('person', 'person.id', '=', 'historia.person_id')
            ->whereIn('historia.convenio_id',[1,2])
            ->where('historia.baja', '!=', "S")
            ->get();

        foreach ($resultado as $r) {

        	switch ($tipoconsulta) {
        		case "1":
        			$c3 = ConsultaNutricion::select('id')
                        ->where("persona_id", "=", $r->person_id)
		                ->where(DB::raw("MONTH(fecha)"), "=", $mes)
		                ->where(DB::raw("YEAR(fecha)"), "=", $year)
		                ->orderBy("id", "ASC")
		                ->get();

		            if (count($c3) == 0) {
		                $c33                 = new ConsultaNutricion();
		                $c33->persona_id     = $r->person_id;
		                $c33->fecha          = $fecha;
		                $c33->estadoatencion = 2;
		                $c33->save();
		            } else {
		                $i = 1;
		                foreach ($c3 as $k2) {
		                    if($i !== 1) {
		                        $k2->delete();
		                    }
		                    $i++;
		                }
		            }
        			break;
        		
        		case "2":
        			$c2 = ConsultaSaludMental::select('id')
                        ->where("persona_id", "=", $r->person_id)
		                ->where(DB::raw("MONTH(fecha)"), "=", $mes)
		                ->where(DB::raw("YEAR(fecha)"), "=", $year)
		                ->orderBy("id", "ASC")
		                ->get();

		            if (count($c2) == 0) {
		                $c22                 = new ConsultaSaludMental();
		                $c22->persona_id     = $r->person_id;
		                $c22->fecha          = $fecha;
		                $c22->estadoatencion = 2;
		                $c22->save();
		            } else {
		                $i = 1;
		                foreach ($c2 as $k2) {
		                    if($i !== 1) {
		                        $k2->delete();
		                    }
		                    $i++;
		                }
		            }
        			break;

        		case "3":
        			$c1 = ConsultaServicioSocial::select('id')
                        ->where("persona_id", "=", $r->person_id)
		                ->where(DB::raw("MONTH(fecha)"), "=", $mes)
		                ->where(DB::raw("YEAR(fecha)"), "=", $year)
		                ->orderBy("id", "ASC")
		                ->get();

		            if (count($c1) == 0) {
		                $c11                 = new ConsultaServicioSocial();
		                $c11->persona_id     = $r->person_id;
		                $c11->fecha          = $fecha;
		                $c11->estadoatencion = 2;
		                $c11->save();
		            } else {
		                $i = 1;
		                foreach ($c1 as $k1) {
		                    if($i !== 1) {
		                        $k1->delete();
		                    }
		                    $i++;
		                }
		            }
        			break;
        	}
        }
    }

    public function ImprimirReceta(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $hc = ConsultaNefrologica::find($request->input('id'));

        if ($hc == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $historia = Historia::where('person_id', '=', $hc->persona_id)->first();
            $pdf      = new TCPDF();
            // set margins
            $pdf::SetMargins(3, 3, 3);

            $pdf::SetTitle('RecetaHemoMensual');
            $pdf::SetTextColor(34, 68, 136);
            $pdf::AddPage("P");

            $pdf::SetFont('', '', 8);

            //Numero de historia

            $nhis        = $historia->numero;
            $histonumero = '<table cellpadding="0" border="1"><tr align="center">';

            for ($ww = 0; $ww < strlen($historia->numero); $ww++) {
                $histonumero .= "<td>" . $nhis[$ww] . "</td>";
            }

            $histonumero .= '</tr></table>';

            //Edad de paciente

            $anitos = "-";

            if ($historia->persona->fechanacimiento != '') {
                $fechanacimiento = new DateTime($historia->persona->fechanacimiento);
                $hoy             = new DateTime();
                $annos           = $hoy->diff($fechanacimiento);
                $anitos          = $annos->y;
            }

            $cicies  = "";
            $ncicies = "";

            $chies = explode(";", $hc->cadenacies);
            foreach ($chies as $ches) {
                $ciecito = Cie::find($ches);
                if ($ciecito !== null) {
                    $cicies .= htmlentities(strtoupper($ciecito->codigo));
                    $ncicies .= htmlentities(strtoupper($ciecito->descripcion));
                    break;
                }
            }

            $mesactual     = (int) date("m", strtotime($hc->fecha));
            $anito         = (int) date("Y", strtotime($hc->fecha));
            $messiguienten = $mesactual + 1;

            if ($mesactual == 12) {
                $anito++;
                $messiguienten = 1;
            }

            $finicio = "01/" . str_pad($mesactual, 2, "0", STR_PAD_LEFT) . "/" . $anito;

            $ffin = cal_days_in_month(CAL_GREGORIAN, $mesactual, $anito) . "/" . str_pad($mesactual, 2, "0", STR_PAD_LEFT) . "/" . $anito;

            //cadena de medicamentos

            $cadenamedicamentos = "";
            $ol                 = 1;

            for ($i = 2; $i <= 18; $i++) {
                $numM = $i;
                if($i == 10) {
                    $numM = 91;
                }
                if($i > 10) {
                    $numM--;
                }
                if ($hc["c" . $numM] !== 0 && $hc["c" . $numM] !== "" && $hc["c" . $numM] !== null) {
                    $medica = Producto::find($hc["m" . $numM]);
                    if ($medica !== null) {
                        $cadenamedicamentos .= '<tr>
                            <td width="5%" align="left">' . htmlentities($ol) . '. </td>
                            <td width="80%" align="left">' . htmlentities($medica->nombre) . '</td>
                            <td width="15%" align="center"><h4>(' . htmlentities($hc["c" . $numM]) . ')</h4></td>
                        </tr>';
                        $ol++;
                    }
                }
            }
            if ($ol > 18) {
                for ($i = $ol; $i <= 18; $i++) {
                    $cadenamedicamentos .= '<tr><td></td></tr>';
                }
            }

            //cadena de indicaciones

            $cadenaindicaciones = "";
            $ol                 = 1;

            for ($i = 5; $i <= 18; $i++) {
                $numM = $i;
                if($i == 10) {
                    $numM = 91;
                }
                if($i > 10) {
                    $numM--;
                }
                if ($hc["i" . $numM] !== 0 && $hc["i" . $numM] !== "" && $hc["i" . $numM] !== null) {
                    $medica = Producto::find($hc["m" . $numM]);
                    if ($medica !== null) {
                        $nombre_medica = $medica->nombre;
                        $cadenaindicaciones .= '<tr>
                            <td width="5%" align="left" border=".5">' . htmlentities($ol) . '. </td>
                            <td width="40%" align="left" border=".5">' . htmlentities($nombre_medica) . '</td>
                            <td width="55%" align="left" border=".5"><h4>' . htmlentities($hc["i" . $numM]) . '</h4></td>
                        </tr>';
                        $ol++;
                    }
                }
            }
            if ($ol > 18) {
                for ($i = $ol; $i <= 18; $i++) {
                    $cadenaindicaciones .= '<tr><td></td></tr>';
                }
            }

            $tbl = '
            <table cellpadding="1" with="100%" height="100%">
                <tr>
                    <td width="1%"></td>
                    <td width="96%" border="2">
                        <table width="100%" height="100%" cellpadding="1">
                            <tr align="center">
                                <td width="100%">
                                    <img src="dist/img/logo2-nefrocix.jpg" width="100px" height="50px"><br>
                                    <font style="font-weight:bold; font-size:11px;">RECETA </font>
                                </td>
                            </tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr align="left">
                                <td width="35%"><h4>Apellidos y Nombres: </h4></td>
                                <td width="65%">' . htmlentities($historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres) . '</td>
                            </tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr align="left">
                                <td width="12%"><h4>Edad: </h4></td>
                                <td width="18%" align="left">' . htmlentities($anitos) . ' Años</td>
                                <td width="12%"><h4>DNI/CE: </h4></td>
                                <td width="20%" align="left">' . htmlentities($historia->persona->dni) . '</td>
                                <td width="12%"><h4>HC: </h4></td>
                                <td width="18%" align="left">' . htmlentities($historia->numero) . '</td>
                            </tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr align="left">
                                <td width="15%"><h4>Usuario: </h4></td>
                                <td width="18%"><h4>ESSALUD </h4></td>
                                <td width="10%"><h4>' . ($historia->convenio->nombre == 'ESSALUD' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</h4></td>
                                <td width="15%"><h4>SIS </h4></td>
                                <td width="10%"><h4>' . ($historia->convenio->nombre == 'SIS' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</h4></td>
                                <td width="15%"><h4>OTROS </h4></td>
                                <td width="10%"><h4>' . ($historia->convenio->nombre == 'OTROS' ? '<img src="dist/img/check.png" alt="" width="10" height="10">' : '<img src="dist/img/uncheck.png" alt="" width="10" height="10">') . '</h4></td>
                            </tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr align="left">
                                <td width="100%"><h4>Diagnostico (Definitivo Presuntivo) ' . $ncicies . ' (CIE-10): ' . $cicies . '</h4></td>
                            </tr>
                        </table>
                        <table width="100%" height="100%" cellpadding="1" style="font-size:7x;">
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr align="left">
                                <td width="100%" style="font-size:2px;"><hr></td>
                            </tr>
                            <tr align="left">
                                <td width="6%"><h4>Rp. </h4></td>
                                <td width="42%"><h4>Medicamentos e Insumos. </h4></td>
                                <td width="34%"><h4>Concentracion Forma </h4></td>
                                <td width="18%"><h4>Cantidad </h4></td>
                            </tr>
                            <tr align="left">
                                <td width="10%"><h4></h4></td>
                                <td width="35%"><h4>(Obligatorio DCI) </h4></td>
                                <td width="20%"><h4></h4></td>
                                <td width="30%"><h4>Farmaceutica </h4></td>
                            </tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            ' . $cadenamedicamentos . '
                            <tr><td></td></tr>
                            <tr>
                                <td width="100%" align="center"><h4>NÚMERO DE SESIONES DE HEMODIÁLISIS (' . htmlentities($hc->c1) . ')</h4></td>
                            </tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr align="center">
                                <td width="1%"></td>
                                <td width="39%"></td>
                                <td width="3%"></td>
                                <td width="28%">' . date("d/m/Y", strtotime($hc->fecha_atencion)) . '</td>
                                <td width="1%"></td>
                                <td width="30%"></td>
                            </tr>
                            <tr align="center">
                                <td width="1%"></td>
                                <td width="39%">________________________</td>
                                <td width="3%"></td>
                                <td width="28%">_________________</td>
                                <td width="1%"></td>
                                <td width="30%"></td>
                            </tr>
                            <tr align="center">
                                <td width="1%"></td>
                                <td width="39%">Sello/Firma/Col/Profesional</td>
                                <td width="3%"></td>
                                <td width="28%">Fecha de Atención</td>
                                <td width="1%"></td>
                                <td width="30%"></td>
                            </tr>
                        </table>
                    </td>
                    <td width="1%"></td>
                </tr>
            </table>';

            $tbl3 = '
            <table cellpadding="1" with="100%" height="100%">
                <tr>
                    <td width="1%"></td>
                    <td width="96%" border="2">
                        <table width="100%" height="100%" cellpadding="1">
                            <tr align="center">
                                <td width="100%">
                                    <img src="dist/img/logo2-nefrocix.jpg" width="90px" height="40px"><br>
                                    <font style="font-weight:bold; font-size:11px;">INDICACIÓN MÉDICA </font>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" height="100%" cellpadding="1" style="font-size:7x;">
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr><td style="font-size:2px;"></td></tr>
                            <tr align="left">
                                <td width="45%" border=".5"><h4>Medicamento </h4></td>
                                <td width="55%" border=".5"><h4>Indicación </h4></td>
                            </tr>
                            ' . $cadenaindicaciones . '
                            <tr><td></td></tr>
                            <tr>
                                <td width="100%" align="center"><h4>Av. Manuel Pardo N° 620 Urb. San Luis - Chiclayo - Telf. 074-267142</h4></td>
                            </tr>
                            <tr>
                                <td width="100%" align="center"><h4>Email: nefro.cix.sac@hotmail.com</h4></td>
                            </tr>
                        </table>
                    </td>
                    <td width="1%"></td>
                </tr>
            </table>';

            $tbl2 = '<table border="0" with="100%" height="100%">
                <tr with="100%" height="100%">
                    <td width="50%">
                        ' . $tbl . '
                    </td>
                    <td width="50%">
                        ' . $tbl . '
                    </td>
                </tr>
                <tr><td></td></tr>
                <tr with="100%" height="100%">
                    <td width="50%">
                        ' . $tbl3 . '
                    </td>
                    <td width="50%">
                    </td>
                </tr>
            </table>';

            $pdf::writeHTML($tbl2, true, false, true, false, 'C');

            $pdf::Output('Historia.pdf');
        }
    }

    public function ImprimirReciboProgramacionDiaria(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $requerimiento = Movimiento::find($request->input('id'));

        if ($requerimiento == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $persona = $requerimiento->persona->nombres . " " . $requerimiento->persona->apellidopaterno . " " . $requerimiento->persona->apellidomaterno;
            $pdf     = new TCPDF();
            // set margins
            $pdf::SetMargins(3, 3, 3, 3);

            $pdf::SetTitle('ReciboProgramacionDiaria');
            $pdf::SetTextColor(34, 68, 136);
            $pdf::AddPage();

            $pdf::SetFont('', '', 8);

            //cadena de medicamentos

            $cadenamedicamentos = "";
            $ol                 = 1;

            $detallesmovimiento = Detallemovimiento::where("movimiento_id", "=", $requerimiento->id)->get();

            foreach ($detallesmovimiento as $dm) {
                $cadenamedicamentos .= '<tr>
                    <td width="12%" align="center">' . $dm->cantidad . ' </td>
                    <td width="73%" align="left">' . htmlentities($dm->producto->nombre) . '</td>
                    <td width="15%" align="center"><h4>' . ($dm->despachado == null ? "0.00" : $dm->despachado) . '</h4></td>
                </tr>';
                $ol++;
            }

            $tbl = '
            <table cellpadding="8" with="100%" height="100%">
                <tr>
                    <td width="8%"></td>
                    <td width="84%" border="2">
                        <table width="100%" height="100%" cellpadding="1">
                            <tr align="center">
                                <br>
                                <td width="40%">
                                    <img src="dist/img/logo2-nefrocix.jpg" width="100px" height="50px"><br>
                                </td>
                                <td width="60%" style="font-size:6px">
                                    <font>Centro de Diálisis</font><br>
                                    <font>NEFRO CIX SAC</font><br>
                                    <font>Dirección Administrativa</font><br>
                                    <font>Departamento de Logística</font><br>
                                    <font>VALE DE SALIDA DE ALMACÉN</font>
                                </td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>FECHA DE SOLICITUD: </h4></td>
                                <td width="63%" style="font-size:6px">' . date("d-m-Y", strtotime($requerimiento->fecha)) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>SOLICITANTE: </h4></td>
                                <td width="63%" style="font-size:6px">' . htmlentities($persona) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>DEPARTAMENTO: </h4></td>
                                <td width="63%" style="font-size:6px">LAMBAYEQUE</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>PARA UTILIZARSE: </h4></td>
                                <td width="63%" style="font-size:6px">' . htmlentities($requerimiento->comentario) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>ESTADO: </h4></td>
                                <td width="63%" style="font-size:6px">' . ($requerimiento->situacion == "P" ? "PENDIENTE" : "DESPACHADO") . '</td>
                            </tr>
                            <tr align="left">
                                <td width="100%" style="font-size:0.2px;"></td>
                            </tr>
                            <tr width="100%">
                                <td width="2%" style="font-size:5px"></td>
                                <td width="100%" style="font-size:5px">
                                    <table border="1" width="100%" height="100%" cellpadding="1" style="font-size:5x;">
                                        <tr align="left">
                                            <td width="12%"><h4>CANT. SOLIC. </h4></td>
                                            <td width="73%"><h4>PRODUCTO </h4></td>
                                            <td width="15%"><h4>CANT. ENTREGA. </h4></td>
                                        </tr>
                                        ' . $cadenamedicamentos . '
                                    </table>
                                </td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="10%" style="font-size:6px"></td>
                                <td width="40%">________________</td>
                                <td width="10%"></td>
                                <td width="40%">________________</td>
                            </tr>
                            <tr align="center">
                                <td width="10%" style="font-size:6px"></td>
                                <td width="40%">ENTREGA</td>
                                <td width="10%"></td>
                                <td width="40%">RECIBE</td>
                            </tr>
                        </table>
                    </td>
                    <td width="8%"></td>
                </tr>
            </table>';

            $tbl2 = '<table cellpadding="8" border="0" with="100%" height="100%">
                <tr with="100%" height="100%" cellpadding="4">
                    <td width="50%" cellpadding="4">
                        ' . $tbl . '
                    </td>
                    <td width="50%">
                        ' . $tbl . '
                    </td>
                </tr>
            </table>';

            $pdf::writeHTML($tbl2, true, true, true, true, 'C');

            $pdf::Output('Historia.pdf');
        }
    }

    public function ImprimirReciboRequerimientoNormal(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $requerimiento = Movimiento::find($request->input('id'));

        if ($requerimiento == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $persona = $requerimiento->persona->nombres . " " . $requerimiento->persona->apellidopaterno . " " . $requerimiento->persona->apellidomaterno;
            $pdf     = new TCPDF();
            // set margins
            $pdf::SetMargins(3, 3, 3, 3);

            $pdf::SetTitle('ReciboRequerimiento');
            $pdf::SetTextColor(34, 68, 136);
            $pdf::AddPage();

            $pdf::SetFont('', '', 8);

            //cadena de medicamentos

            $cadenamedicamentos = "";
            $ol                 = 1;

            $detallesmovimiento = Detallemovimiento::where("movimiento_id", "=", $requerimiento->id)->get();

            foreach ($detallesmovimiento as $dm) {
                $cadenamedicamentos .= '<tr>
                    <td width="12%" align="center">' . $dm->cantidad . ' </td>
                    <td width="73%" align="left">' . htmlentities($dm->producto->nombre) . '</td>
                    <td width="15%" align="center"><h4>' . ($dm->despachado == null ? "0.00" : $dm->despachado) . '</h4></td>
                </tr>';
                $ol++;
            }

            $tbl = '
            <table cellpadding="8" with="100%" height="100%">
                <tr>
                    <td width="8%"></td>
                    <td width="84%" border="2">
                        <table width="100%" height="100%" cellpadding="1">
                            <tr align="center">
                                <br>
                                <td width="40%">
                                    <img src="dist/img/logo2-nefrocix.jpg" width="100px" height="50px"><br>
                                </td>
                                <td width="60%" style="font-size:6px">
                                    <font>Centro de Diálisis</font><br>
                                    <font>NEFRO CIX SAC</font><br>
                                    <font>Dirección Administrativa</font><br>
                                    <font>Departamento de Logística</font><br>
                                    <font>VALE DE SALIDA DE ALMACÉN</font>
                                </td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>FECHA DE SOLICITUD: </h4></td>
                                <td width="63%" style="font-size:6px">' . date("d-m-Y", strtotime($requerimiento->fecha)) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>SOLICITANTE: </h4></td>
                                <td width="63%" style="font-size:6px">' . htmlentities($persona) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>DEPARTAMENTO: </h4></td>
                                <td width="63%" style="font-size:6px">LAMBAYEQUE</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>PARA UTILIZARSE: </h4></td>
                                <td width="63%" style="font-size:6px">' . htmlentities($requerimiento->comentario) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:6px"></td>
                                <td width="35%" style="font-size:6px"><h4>ESTADO: </h4></td>
                                <td width="63%" style="font-size:6px">' . ($requerimiento->situacion == "P" ? "PENDIENTE" : "DESPACHADO") . '</td>
                            </tr>
                            <tr align="left">
                                <td width="100%" style="font-size:0.2px;"></td>
                            </tr>
                            <tr width="100%">
                                <td width="2%" style="font-size:5px"></td>
                                <td width="100%" style="font-size:5px">
                                    <table border="1" width="100%" height="100%" cellpadding="1" style="font-size:5x;">
                                        <tr align="left">
                                            <td width="12%"><h4>CANT. SOLIC. </h4></td>
                                            <td width="73%"><h4>PRODUCTO </h4></td>
                                            <td width="15%"><h4>CANT. ENTREGA. </h4></td>
                                        </tr>
                                        ' . $cadenamedicamentos . '
                                    </table>
                                </td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                            <tr align="center">
                                <td width="10%" style="font-size:6px"></td>
                                <td width="40%">________________</td>
                                <td width="10%"></td>
                                <td width="40%">________________</td>
                            </tr>
                            <tr align="center">
                                <td width="10%" style="font-size:6px"></td>
                                <td width="40%">ENTREGA</td>
                                <td width="10%"></td>
                                <td width="40%">RECIBE</td>
                            </tr>
                        </table>
                    </td>
                    <td width="8%"></td>
                </tr>
            </table>';

            $tbl2 = '<table cellpadding="8" border="0" with="100%" height="100%">
                <tr with="100%" height="100%" cellpadding="4">
                    <td width="50%" cellpadding="4">
                        ' . $tbl . '
                    </td>
                    <td width="50%">
                        ' . $tbl . '
                    </td>
                </tr>
            </table>';

            $pdf::writeHTML($tbl2, true, true, true, true, 'C');

            $pdf::Output('Historia.pdf');
        }
    }

    //CARGO LA TABLA

    public function cargaTablaverconsolidadoMedicamentos(Request $request)
    {
        $id   = $request->input("id");
        $anno = $request->input("anno");
        date_default_timezone_set('America/Lima');
        $paciente = Person::find($id);

        $nombreMedicamento = array(
            "1"  => "EPOETINA ALFA (ERITROPOYETINA) 2000 UI/ML INY 1 ML",
            "2"  => "HIERRO (COMO SACARATO) 20MG FE/ML INY 5 ML",
            "3"  => "VITAMINA B12 HIDROXICOBALAMINA 1MG/ML INY 1ML",
            "4"  => "CALCIO CARBONATO 500 MG (EQUIV.A 500 MG DE CALCIO) TAB",
            "5"  => "PIRIDOXINA 50MG TAB",
            "6"  => "TIAMINA 100MG TAB",
            "7"  => "ÁCIDO FÓLICO 0.5 MG TAB",
            "8"  => "CALCITRIOL 1 MCG/ML INY",
            "9"  => "CALCITRIOL 0.25ug CAP (**)",
            "10" => "ENALAPRIL MALEATO 10 MG TAB",
            "11" => "CAPTOPRIL 25 MG TAB",
            "12" => "AMLODIPINO (COMO BESILATO) 10 MG TAB",
            "13" => "NIFEDIPINO 10 MG",
            "14" => "NIFEDIPINO DE 30 MG",
            "15" => "METILDOPA 250 MG",
            "16" => "ATENOLOL 100 MG",
            "17" => "LOSARTAN 50 MG",
        );

        //ARMO ARRAY DE CONTENIDO

        $retorno = "";

        $arrayContenido = array();

        for ($as = 1; $as <= 12; $as++) {
            $atencion = ConsultaNefrologica::join("person as p", "p.id", "=", "consultanefrologica.persona_id")
                ->join("historia as h", "h.person_id", "=", "p.id")
                ->where("p.id", "=", $paciente->id)
                ->where(DB::raw("MONTH(consultanefrologica.fecha)"), "=", $as)
                ->where(DB::raw("YEAR(consultanefrologica.fecha)"), "=", $anno)
                ->first();
            if ($atencion !== null) {
                $arrayContenido[$as] = array(
                    "1"  => $atencion->c2,
                    "2"  => $atencion->c3,
                    "3"  => $atencion->c4,
                    "4"  => $atencion->c5,
                    "5"  => $atencion->c6,
                    "6"  => $atencion->c7,
                    "7"  => $atencion->c8,
                    "8"  => $atencion->c9,
                    "9"  => $atencion->c91,
                    "10"  => $atencion->c10,
                    "11" => $atencion->c11,
                    "12" => $atencion->c12,
                    "13" => $atencion->c13,
                    "14" => $atencion->c14,
                    "15" => $atencion->c15,
                    "16" => $atencion->c16,
                    "17" => $atencion->c17,
                );
            } else {
                $arrayContenido[$as] = array("1" => "", "2" => "", "3" => "", "4" => "", "5" => "", "6" => "", "7" => "", "8" => "", "9" => "", "10" => "", "11" => "", "12" => "", "13" => "", "14" => "", "15" => "", "16" => "", "17" => "", "18" => "",
                );
            }
        }

        for ($i = 1; $i <= 17; $i++) {
            $retorno .= '<tr>
                <td>' . $nombreMedicamento[$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[1][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[2][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[3][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[4][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[5][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[6][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[7][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[8][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[9][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[10][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[11][$i] . '</td>
                <td style="text-align: center; vertical-align: middle; width: 7.6%;">' . $arrayContenido[12][$i] . '</td>
            </tr>';
        }

        $jsonarray = array("ret" => $retorno);
        return json_encode($jsonarray);
    }

    public function cargaTablaverhistorialResultadosPorPaciente(Request $request)
    {
        $historia_id     = $request->input("historia_id");
        $anno            = $request->input("anno");
        $years           = $this->anoos;
        $historia        = Historia::find($historia_id);
        $nombreResultado = array(
            "1"  => 'Hemoglobina (g/dl)',
            "2"  => 'Hematocrito (%)',
            "3"  => 'Urea pre (mg/dl)',
            "4"  => 'Urea post (mg/dl)',
            "5"  => 'TGO (U/l)',
            "6"  => 'TGP (U/l)',
            "7"  => 'Creatinina Pre (mg/dl)',
            "8"  => 'Creatinina Post (mg/dl)',
            "9"  => 'Fosfatasa Alcalina (U/L)',
            "10" => 'Hierro (ug/dl)',
            "11" => 'Transferrina',
            "11" => '% de Saturación de transferrina',
            "12" => 'Proteínas Totales (g/dl)',
            "13" => 'Albumina (g/dl)',
            "14" => 'Globulina (g/dl)',
            "15" => 'Fósforo (mg/dl)',

            "16" => 'Sodio (mmol/L)',
            "17" => 'Potasio (mmol/L)',
            "18" => 'Cloro (mmol/L)',

            "19" => 'Calcio (mg/dl)',
            "20" => 'Calcio corregido (mg/dl)',            
            "21" => 'Ferritina (ng/ml)',
            "22" => 'Parathormona (pg/ml)',
            "23" => 'Antígeno de superficie Hepatitis B',
            "24" => 'Anticuerpos antígeno de superficie Hepatitis B',
            "25" => 'Anticuerpos para Hepatitis C',
            "26" => 'ANTI HBcAg CORE TOTAL',
            "27" => 'AcHBC - lg M',
            "28" => 'AcHBC - lg O',
            "29" => "VACUNACIÓN PARA HVB",
            "30" => "",
            "31" => "",
            "32" => "",
            "33" => "VACUNACIÓN PARA NEUMOCOCO",
            "34" => "VDRL",
            "35" => "HIV",
            "36" => "KTV",
            "37" => "TRU",
            "38" => "Peso Seco",
            "39" => "Acceso Vascular",
            "40" => "Área del dializador",
        );

        //ARMO ARRAY DE CONTENIDO

        $arrayContenido = array();

        for ($as = 1; $as <= 12; $as++) {
            $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
                ->where('historia.convenio_id', '=', 1)
                ->where('historia.baja', '!=', "S")
                ->where('historia.id', '=', $historia->id)
                ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
                ->where(DB::raw("MONTH(c.fecha)"), "=", $as)
                ->where(DB::raw("YEAR(c.fecha)"), "=", $anno)
                ->first();
            $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                ->where("historia.id", "=", $historia->id)
                ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $as)
                ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anno)
                ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
                ->where("historiaclinica.estado", "!=", "C")
                //->where("mensuales2", "=", 1)
                ->first();
            if ($resultado !== null) {
                $time = 0;
                $ppre = 0;
                $ppos = 0;
                if ($atencion !== null) {
                    $time = $atencion->txtHorasHemodialisis;
                    $ppre = $atencion->txtPesoInicial2;
                    $ppos = $atencion->txtPesoFinal2;
                }
                //ARMO VALOR DE KTV
                $ktv = "";
                $tru = "";
                if ($resultado->txtUre !== null && $resultado->txtUre !== "" && $ppos !== null && $ppos !== "" && $time != 0) {
                    $ktv = -log($resultado->txtUre2 / $resultado->txtUre - 0.008 * $time) + (4 - 3.5 * $resultado->txtUre2 / $resultado->txtUre) * (($ppre - $ppos) / $ppos);
                }

                //ARMO VALOR DE TRU
                if ($resultado->txtUre !== null && $resultado->txtUre !== "") {
                    $tru = 100 - ($resultado->txtUre2 * 100 / $resultado->txtUre);
                }
                $accesin = "";
                if ($atencion !== null) {
                    switch ($atencion->txtAccesoVascularArterial) {
                        case '1':
                            $accesin = "FAV";
                            break;
                        case '2':
                            $accesin = "Autoinjerto";
                            break;
                        case '3':
                            $accesin = "Injerto";
                            break;
                        case '4':
                            $accesin = "CVCP";
                            break;
                        case '5':
                            $accesin = "CVCT";
                            break;
                        case '6':
                            $accesin = "VP";
                            break;
                    }
                }
                $arrayContenido[$as] = array(
                    "1"  => $resultado->txtDos,
                    "2"  => $resultado->txtHem,
                    "3"  => $resultado->txtUre,
                    "4"  => $resultado->txtUre2,
                    "5"  => $resultado->txtTgo,
                    "6"  => $resultado->txtTgp,
                    "7"  => $resultado->txtCre,
                    "8"  => "",
                    "9"  => $resultado->txtFos2,
                    "10"  => $resultado->txtHie,
                    "11" => $resultado->txtTransfe,
                    "12" => $resultado->txtSat,
                    "13" => $resultado->txtPro,
                    "14" => $resultado->txtAlbu,
                    "15" => $resultado->txtGlobu,
                    "16" => $resultado->txtFos,

                    "17" => $resultado->txtSodio,
                    "18" => $resultado->txtPotasio,
                    "19" => $resultado->txtCloro,

                    "20" => $resultado->txtCal,
                    "21" => "",
                    "22" => $resultado->txtFer,
                    "23" => $resultado->txtPar,
                    "24" => $resultado->txtDet,
                    "25" => $resultado->txtDet2,
                    "26" => $resultado->txtDet4,
                    "27" => $resultado->txtDet3,
                    "28" => "",
                    "29" => "",
                    "30" => "",
                    "31" => "",
                    "32" => "",
                    "33" => "",
                    "34" => $resultado->txtPru,
                    "35" => $resultado->txtEli,
                    "36" => round($ktv, 2),
                    "37" => round($tru, 2),
                    "38" => ($atencion == null ? "" : $atencion->txtPesoSeco),
                    "39" => ($accesin),
                    "40" => ($atencion == null ? "" : $atencion->txtAreaDializador),
                );
            } else {
                $arrayContenido[$as] = array("1" => "", "2" => "", "3" => "", "4" => "", "5" => "", "6" => "", "7" => "", "8" => "", "9" => "", "10" => "", "11" => "", "12" => "", "13" => "", "14" => "", "15" => "", "16" => "", "17" => "", "18" => "", "19" => "", "20" => "", "21" => "", "22" => "", "23" => "", "24" => "", "25" => "", "26" => "", "27" => "", "28" => "", "29" => "", "30" => "", "31" => "", "32" => "", "33" => "", "34" => "", "35" => "", "36" => "", "37" => "", "38" => "", "39" => "", "40" => "",
                );
            }
        }

        $retorno = "";

        for ($i = 1; $i < 41; $i++) {
            $retorno .= '<tr>';
            if ($i == 29) {
                $retorno .= '<td rowspan="4" style="text-align:center; vertical-align: middle;">' . $nombreResultado[$i] . '</td>';
                $retorno .= '<td>1° Dosis</td>';
            } else if ($i == 30) {
                $retorno .= '<td>2° Dosis</td>';
            } else if ($i == 31) {
                $retorno .= '<td>3° Dosis</td>';
            } else if ($i == 32) {
                $retorno .= '<td>4° Dosis</td>';
            } else {
                $retorno .= '<td colspan="2">' . $nombreResultado[$i] . '</td>';
            }
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[1][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[2][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[3][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[4][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[5][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[6][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[7][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . ($arrayContenido[8][$i] == "0" ? "" : $arrayContenido[8][$i]) . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . ($arrayContenido[9][$i] == "0" ? "" : $arrayContenido[9][$i]) . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[10][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[11][$i] . '</td>';
            $retorno .= '<td style="text-align: right; width: 8.3%;">' . $arrayContenido[12][$i] . '</td>';
            $retorno .= '</tr>';
        }

        $jsonarray = array("ret" => $retorno);
        return json_encode($jsonarray);
    }

    public function ImprimirReciboRequerimientoAdministrativo(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $requerimiento = Movimiento::find($request->input('id'));

        if ($requerimiento == null) {
            echo 'AÚN NO SE FINALIZA EL PROCESO, REGRESE...';
        } else {
            $persona = $requerimiento->persona->nombres . " " . $requerimiento->persona->apellidopaterno . " " . $requerimiento->persona->apellidomaterno;
            $pdf     = new TCPDF();
            // set margins
            $pdf::SetMargins(2, 2, 2, 2);

            $pdf::SetTitle('ReciboRequerimientoAdmin');
            $pdf::SetTextColor(34, 68, 136);
            $pdf::AddPage();

            $pdf::SetFont('', '', 8);

            //cadena de medicamentos

            $cadenamedicamentos = "";
            $cadenamedicamentos2 = "";
            $ol = 1;

            $detallesmovimiento = Detallemovimiento::where("movimiento_id", "=", $requerimiento->id)->get();

            foreach ($detallesmovimiento as $dm) {
                $cadenamedicamentos .= '<tr>
                    <td width="8%" style="font-size:8px" align="center">' . $ol . ' </td>
                    <td width="30%" style="font-size:8px" align="center">' . htmlentities($dm->producto->nombre) . ' </td>
                    <td width="30%" style="font-size:8px" align="center">' . ($dm->proveedor==NULL?"-":$dm->proveedor->bussinesname) . ' </td>
                    <td width="8%" style="font-size:8px" align="center">' . substr($dm->tipo, 0, 5) . "." . ' </td>
                    <td width="8%" style="font-size:8px" align="center">' . $dm->cantidad . ' </td>
                    <td width="8%" style="font-size:8px" align="center">' . ($dm->producto==NULL?"0.00":$dm->producto->preciocompra) . '</td>
                    <td width="8%" style="font-size:8px" align="center"><h4>' . ($dm->subtotal) . '</h4></td>
                </tr>';
                $ol++;
            }

            $cadenamedicamentos .= '<tr>
                    <td width="92%" style="font-size:8px" align="center">TOTAL</td>
                    <td width="8%" style="font-size:8px" align="center"><h4>' . ($requerimiento->total) . '</h4></td>
                </tr>';

            $ol = 1;

            if($requerimiento->movimiento !== NULL) {
                foreach ($requerimiento->movimiento->detalles as $dm) {
                    $cadenamedicamentos2 .= '<tr>
                        <td width="8%" style="font-size:8px" align="center">' . $ol . ' </td>
                        <td width="30%" style="font-size:8px" align="center">' . htmlentities($dm->producto->nombre) . ' </td>
                        <td width="30%" style="font-size:8px" align="center">' . ($dm->proveedor==NULL?"-":$dm->proveedor->bussinesname) . ' </td>
                        <td width="8%" style="font-size:8px" align="center">' . substr($dm->tipo, 0, 5) . "." . ' </td>
                        <td width="8%" style="font-size:8px" align="center">' . $dm->cantidad . ' </td>
                        <td width="8%" style="font-size:8px" align="center">' . ($dm->producto==NULL?"0.00":$dm->producto->preciocompra) . '</td>
                        <td width="8%" style="font-size:8px" align="center"><h4>' . ($dm->subtotal) . '</h4></td>
                    </tr>';
                    $ol++;
                }
                $cadenamedicamentos2 .= '<tr>
                    <td width="92%" style="font-size:8px" align="center">TOTAL</td>
                    <td width="8%" style="font-size:8px" align="center"><h4>' . ($requerimiento->movimiento->total) . '</h4></td>
                </tr>';
            } else {
                $cadenamedicamentos2 .= '<tr>
                        <td width="100%" style="font-size:8px" align="center">AÚN NO SE HA CARGADO EL STOCK</td>
                    </tr>';
            }           

            $tbl = '
            <table cellpadding="8" with="100%" height="100%">
                <tr>
                    <td width="100%" border="2">
                        <table width="100%" height="100%" cellpadding="1">
                            <tr align="center">
                                <br>
                                <td width="20%">
                                    <img src="dist/img/logo2-nefrocix.jpg" width="100px" height="50px"><br>
                                </td>
                                <td width="80%" style="font-size:9px">
                                    <font>Centro de Diálisis</font><br>
                                    <font>NEFRO CIX SAC</font><br>
                                    <font>Departamento de Logística</font><br>
                                    <font>REQUERIMIENTO DE ADQUISICIÓN DE PRODUCTOS - ADMINISTRACIÓN</font>
                                </td>
                            </tr>
                            <tr><td style="font-size:1px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:8px"></td>
                                <td width="35%" style="font-size:8px"><h4>FECHA DE SOLICITUD: </h4></td>
                                <td width="63%" style="font-size:8px">' . date("d-m-Y", strtotime($requerimiento->fecha)) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:8px"></td>
                                <td width="35%" style="font-size:8px"><h4>SOLICITANTE: </h4></td>
                                <td width="63%" style="font-size:8px">' . htmlentities($persona) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:8px"></td>
                                <td width="35%" style="font-size:8px"><h4>DEPARTAMENTO: </h4></td>
                                <td width="63%" style="font-size:8px">LAMBAYEQUE</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:8px"></td>
                                <td width="35%" style="font-size:8px"><h4>PARA UTILIZARSE: </h4></td>
                                <td width="63%" style="font-size:8px">' . htmlentities($requerimiento->comentario) . '</td>
                            </tr>
                            <tr><td style="font-size:0.2px;"></td></tr>
                            <tr align="left">
                                <td width="2%" style="font-size:8px"></td>
                                <td width="35%" style="font-size:8px"><h4>ESTADO: </h4></td>
                                <td width="63%" style="font-size:8px">' . ($requerimiento->situacion == "P" ? "PENDIENTE" : "DESPACHADO") . '</td>
                            </tr>
                            <tr align="left">
                                <td width="100%" style="font-size:2px;"></td>
                            </tr>
                            <tr align="left">
                                <td width="100%" style="font-size:2px;"></td>
                            </tr>
                            <tr align="left">
                                <td width="2%" style="font-size:8px"></td>
                                <td width="98%" style="font-size:10px"><h4>DETALLES DE REQUERIMIENTO DE ADMINISTRACIÓN </h4></td>
                            </tr>
                            <tr align="left">
                                <td width="100%" style="font-size:2px;"></td>
                            </tr>
                            <tr width="100%">
                                <td width="1.5%"></td>
                                <td width="100%" style="font-size:5px">
                                    <table border="1" width="100%" height="100%" cellpadding="1" style="font-size:7x;">
                                        <tr align="left">
                                            <td width="8%" style="text-align:center;"><h4>ITEM </h4></td>
                                            <td width="30%" style="text-align:center;"><h4>PRODUCTO </h4></td>
                                            <td width="30%" style="text-align:center;"><h4>PROVEEDOR </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>TIPO </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>CANTIDAD </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>P.U </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>SUBTOTAL </h4></td>
                                        </tr>
                                        ' . $cadenamedicamentos . '
                                    </table>
                                </td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>

                            <tr align="left">
                                <td width="2%" style="font-size:8px"></td>
                                <td width="98%" style="font-size:10px"><h4>DETALLES DE CARGA DE STOCK </h4></td>
                            </tr>
                            <tr align="left">
                                <td width="100%" style="font-size:2px;"></td>
                            </tr>
                            <tr width="100%">
                                <td width="1.5%"></td>
                                <td width="100%" style="font-size:5px">
                                    <table border="1" width="100%" height="100%" cellpadding="1" style="font-size:7x;">
                                        <tr align="left">
                                            <td width="8%" style="text-align:center;"><h4>ITEM </h4></td>
                                            <td width="30%" style="text-align:center;"><h4>PRODUCTO </h4></td>
                                            <td width="30%" style="text-align:center;"><h4>PROVEEDOR </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>TIPO </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>CANTIDAD </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>P.U </h4></td>
                                            <td width="8%" style="text-align:center;"><h4>SUBTOTAL </h4></td>
                                        </tr>
                                        ' . $cadenamedicamentos2 . '
                                    </table>
                                </td>
                            </tr>
                            <tr align="center">
                                <td width="100%" style="font-size:7px"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>';

            $tbl2 = '<table cellpadding="8" border="0" with="100%" height="100%">
                <tr with="100%" height="100%" cellpadding="4">
                    <td width="100%" cellpadding="4">
                        ' . $tbl . '
                    </td>
                </tr>
            </table>';

            $pdf::writeHTML($tbl2, true, true, true, true, 'C');

            $pdf::Output('Historia.pdf');
        }
    }

    public function prueba($f)
    {
        $entero  = (int) $f;
        $decimal = $f - $entero;
        if ($decimal == 0.5) {
            return $entero . " ½";
        } else if ($decimal == 0.25) {
            return $entero . " ¼";
        } else {
            return $f;
        }
    }
}
