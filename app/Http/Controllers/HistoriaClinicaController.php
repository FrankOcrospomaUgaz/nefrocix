<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Cie;
use App\User;
use App\Cita;
use App\Servicio;
use App\ConsultaSaludMental;
use App\ConsultaNefrologica;
use App\Turno;
use App\ConsultaServicioSocial;
use App\ConsultaNutricion;
use App\Detallehistoriacie;
use App\Producto;
use App\Examenhistoriaclinica;
use App\Person;
use App\BitacoraTratamiento;
use App\Detallemovcaja;
use App\Movimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class HistoriaClinicaController extends Controller
{
    protected $folderview      = 'app.producto';
    protected $rutas           = array('create' => 'historiaclinica.create', 
            'buscar' => 'historiaclinica.buscar',
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
    public function nuevaHistoriaClinica(Request $request)
    {
        date_default_timezone_set('America/Lima');

        $historia = Historia::find($request->input('historia_id'));
        if($request->input('cid')!==NULL&&$request->input('cid')!=="") {
            $historiaclinica = HistoriaClinica::find($request->input('cid'));
        } else {
            $historiaclinica = HistoriaClinica::where('historia_id', $historia->id)->where('estado', '!=', 'C')->where('fecha_atencion', '>=', date('Y-m-d'))->first(); 
        }        
               
        if($historia !== null){
            $persona = $historia->persona->apellidopaterno .' '.$historia->persona->apellidomaterno.' '.$historia->persona->nombres;
            //Calculo veces por semana (frecuencia)
            $ordencitas = explode(';', $historia->ordencitas);
            $frecuencia = count($ordencitas)-1;
            //Calculo N Hemodialisis mes
            if($request->input('cid')!==NULL&&$request->input('cid')!=="") {
                $fecha = date("Y-m-d", strtotime($historiaclinica->fecha_atencion));
                $diaa = date("d", strtotime($historiaclinica->fecha_atencion));
                $mess = date("m", strtotime($historiaclinica->fecha_atencion));
                $anoo = date("Y", strtotime($historiaclinica->fecha_atencion));
            } else {
                $fecha = date('Y-m-d');
                $diaa = date("d", strtotime($fecha));
                $mess = date("m", strtotime($fecha));
                $anoo = date("Y", strtotime($fecha));
            }
            $a = 0;
            for ($i=1; $i <= $diaa; $i++) {
                $fechina = $anoo . "-" . $mess . "-" . $i;
                $hcc = HistoriaClinica::where('historia_id', '=', $historia->id)
                    ->where('fecha_atencion', 'LIKE', $fechina."%")->first();
                if($hcc!==NULL) {
                    $a++;
                }
            }
            $numsesion = $a+1;            
            //Calculo prox cita
            $citaproxima = '';
            if($request->input('cid')!==NULL&&$request->input('cid')!=="") {
                $fechaa = date("Y-m-d", strtotime($historiaclinica->fecha_atencion));
            } else {
                $fechaa = date('Y-m-d');
            }
            $fecha = strtotime($fecha);
            $diasemana = date('w', $fecha);
            if($diasemana==0) {
                $diasemana=7;
            }
            $fecha0 = explode('-', $fechaa);
            $fechai = $fecha0[0].'-'.$fecha0[1].'-';
            for ($i=($fecha0[2]+1); $i <= 31; $i++) {   
                $fechaf = strtotime($fechai.$i);
                $diasemana = date('w', $fechaf);
                if($diasemana==0) {
                    $diasemana=7;
                }      
                foreach ($ordencitas as $f0) {
                    if($f0==$diasemana) {
                        $citaproxima=$fechai.$i;
                        $i=31;
                        break;
                    }
                }           
            }

            //penultima hc
            $phc = HistoriaClinica::where('historia_id', '=', $historia->id)->where('fecha_atencion', '<', $fechaa)->where('estado', '=', 'F')->where('estado', '!=', 'C')->orderBy('fecha_atencion', 'DESC')->first();
            $PesoSeco = "";
            if($historiaclinica->txtPesoSeco!==NULL&&$historiaclinica->txtPesoSeco!=="") {
                $PesoSeco = $historiaclinica->txtPesoSeco;
            } else {
                if($phc!==NULL) {
                    $PesoSeco = $phc->txtPesoSeco;
                }
            }  

            //txtMembranaDializador
            $MembranaDializador = "POLISULFONA";
            if($historiaclinica->txtMembranaDializador!==NULL&&$historiaclinica->txtMembranaDializador!=="") {
                $MembranaDializador = $historiaclinica->txtMembranaDializador;
            }

            //txtBufer   
            $Bufer = "BICARBONATO";
            if($historiaclinica->txtBufer!==NULL&&$historiaclinica->txtBufer!=="") {
                $Bufer = $historiaclinica->txtBufer;
            }

            //txtMedicacion
            $Medicacion = "* EPOETINA ALFA 2000 UI/ML. INY 1 ML: \n* HIERRO 20MG FE/ML. INY 5 ML: \n* VITAMINA B12 HIDROXICOBALAMINA 1MG7ML INY 1 ML: ";
            if($historiaclinica->txtMedicacion!==NULL&&$historiaclinica->txtMedicacion!=="") {
                $Medicacion = $historiaclinica->txtMedicacion;
            }

            //txtMarcaModeloMaquina

            $MarcaModeloMaquina = "NIPRO";
            if($historiaclinica->txtMarcaModeloMaquina!==NULL&&$historiaclinica->txtMarcaModeloMaquina!=="") {
                $MarcaModeloMaquina = $historiaclinica->txtMarcaModeloMaquina;
            }

            //txtMarcaModeloMaquina2

            $MarcaModeloMaquina2 = "DIAMAX";
            if($historiaclinica->txtMarcaModeloMaquina2!==NULL&&$historiaclinica->txtMarcaModeloMaquina2!=="") {
                $MarcaModeloMaquina2 = $historiaclinica->txtMarcaModeloMaquina2;
            }

            if($request->input("inicial")!==NULL&&$request->input("inicial")!=="") {
                //$txtAdmiMedic;
                //ARMAMOS EL TXTADMIMEDIC SI ES QUE SE HA PROGRAMADO PESH
                $historiamensual = ConsultaNefrologica::where("persona_id", "=", $historiaclinica->historia->persona->id)
                                ->where(DB::raw("MONTH(fecha)"), "=", date("m", strtotime($historiaclinica->fecha_atencion)))
                                ->where(DB::raw("YEAR(fecha)"), "=", date("Y", strtotime($historiaclinica->fecha_atencion)))
                                ->first();
                $diahemodialisis = date("d", strtotime($historiaclinica->fecha_atencion));
                $cantepo = "";
                $canthierro = "";
                $cantvita = "";
                //SOLO SI EXISTE Y SE HA PROGRAMADO
                if($historiamensual !== NULL) {
                    if($historiamensual->cadenaepo!==NULL&&$historiamensual->cadenaepo!=="") {
                        $cadenaepo = explode("**", $historiamensual->cadenaepo);
                        foreach ($cadenaepo as $epo) {
                            $diap = explode(";", $epo);
                            if($diap[0]==$diahemodialisis) {
                                $cantepo = $diap[1];
                                break;
                            }
                        }
                    }
                    if($historiamensual->cadenahierro!==NULL&&$historiamensual->cadenahierro!=="") {
                        $cadenahierro = explode("**", $historiamensual->cadenahierro);
                        foreach ($cadenahierro as $hierro) {
                            $diap = explode(";", $hierro);
                            if($diap[0]==$diahemodialisis) {
                                $canthierro = $diap[1];
                                break;
                            }
                        }
                    }
                    if($historiamensual->cadenavita!==NULL&&$historiamensual->cadenavita!=="") {
                        $cadenavita = explode("**", $historiamensual->cadenavita);
                        foreach ($cadenavita as $vita) {
                            $diap = explode(";", $vita);
                            if($diap[0]==$diahemodialisis) {
                                $cantvita = $diap[1];
                                break;
                            }
                        }
                    }
                }
                //ARMANDO TXTADMIMEDICA PARA SETEAR
                $txtAdmiMedic = "1&ilid&" . $cantepo . "&iliu&2&ilid&" . $canthierro . "&iliu&3&ilid&" . $cantvita . "&iliu&&ilid&&iliu&&ilid&&iliu&&ilid&";
                $seteohistoriaclinica = HistoriaClinica::find($historiaclinica->id);
                $seteohistoriaclinica->txtAdmiMedic = $txtAdmiMedic;
                $seteohistoriaclinica->save();
            } else {
                $txtAdmiMedic = $historiaclinica->txtAdmiMedic;
            }

            $jsondata = array(
                'historia_id' => $historia->id,
                'numhistoria' => $historia->numero,
                'paciente' => $persona,
                'turno' => $historia->turno->hora,
                'romano' => $historia->turno->romano,
                'plan_susalud' => $historia->carnet,
                'convenio' => $historia->convenio->nombre,
                'frecuencia' => $frecuencia,
                'numsesion' => $numsesion,
                'citaproxima' => date('d-m-Y', strtotime($citaproxima)),
                'txtEvoSigSin' => $historiaclinica->txtEvoSigSin,
                'txtPA' => $historiaclinica->txtPA,
                'txtFC' => $historiaclinica->txtFC,
                'fecha_atencion'=> date("Y-m-d", strtotime($historiaclinica->fecha_atencion)),
                'txtFR' => $historiaclinica->txtFR,
                'txtHorasHemodialisis' => $historiaclinica->txtHorasHemodialisis,
                'txtPesoInicial' => $historiaclinica->txtPesoInicial,
                'txtQb' => $historiaclinica->txtQb,
                'txtNaInicial' => $historiaclinica->txtNaInicial,
                'txtDosisHepa' => $historiaclinica->txtDosisHepa,
                'txtPesoFinal' => $historiaclinica->txtPesoFinal,
                'txtQd' => $historiaclinica->txtQd,
                'txtNaFinal' => $historiaclinica->txtNaFinal,
                'txtPesoSeco' => $PesoSeco,
                'txtPerfilUF' => $historiaclinica->txtPerfilUF,
                'txtBufer' => $Bufer,
                'txtPerfilNa' => $historiaclinica->txtPerfilNa,
                'txtMedicacion' => $Medicacion,
                'txtUltrafiltrado' => $historiaclinica->txtUltrafiltrado,
                'txtConductividad' => $historiaclinica->txtConductividad,
                'txtAreaDializador' => $historiaclinica->txtAreaDializador,
                'txtMembranaDializador' => $MembranaDializador,
                'txtCondicionClinicaFinal' => $historiaclinica->txtCondicionClinicaFinal,
                'txtPAInicial' => $historiaclinica->txtPAInicial,
                'txtNPuesto' => $historiaclinica->txtNPuesto,
                'txtPesoInicial2' => $historiaclinica->txtPesoInicial2,
                'txtMarcaModeloMaquina' => $MarcaModeloMaquina,
                'txtMarcaModeloMaquina2' => $MarcaModeloMaquina2,                
                'txtUltrafiltadoProgramado' => $historiaclinica->txtUltrafiltadoProgramado,
                'txtUltrafiltadoProgramado2' => $historiaclinica->txtUltrafiltadoProgramado2,
                'txtUltrafiltadoProgramado3' => $historiaclinica->txtUltrafiltadoProgramado3,
                'txtLoteSerieFiltro' => $historiaclinica->txtLoteSerieFiltro,
                'txtLoteSerieFiltro2' => $historiaclinica->txtLoteSerieFiltro2,
                'txtAccesoVascularArterial' => $historiaclinica->txtAccesoVascularArterial,
                'txtAccesoVascularVenoso' => $historiaclinica->txtAccesoVascularVenoso,
                'txtPAFinal' => $historiaclinica->txtPAFinal,
                'txtTemperatura' => $historiaclinica->txtTemperatura,
                'txtNMAquina' => $historiaclinica->txtNMAquina,
                'txtPesoFinal2' => $historiaclinica->txtPesoFinal2,
                'txtAreaMembranaFiltro' => $historiaclinica->txtAreaMembranaFiltro,
                'txtValoracionEnfermeria' => $historiaclinica->txtValoracionEnfermeria,
                'txtEvalHemodialisis' => $historiaclinica->txtEvalHemodialisis,
                'txtObservacionFinal' => $historiaclinica->txtObservacionFinal,
                'txtAspectoFiltro' => $historiaclinica->txtAspectoFiltro,
                'txtAdmiMedic' => $txtAdmiMedic,
                'txtMuestraAnalisis' => $historiaclinica->txtMuestraAnalisis,
                'txtCies' => $historiaclinica->txtCies,
                'txtHoraEvaluacionPrevia' => date('H:i:s'),
            );
        }
        return json_encode($jsondata);
    }


    public function cie10autocompletar($searching)
    {
        $resultado        = Cie::where(DB::raw('CONCAT(codigo," ",descripcion)'), 'LIKE', '%'.strtoupper($searching).'%')->whereNull('deleted_at')->orderBy('descripcion', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $name = $value->codigo." - ".$value->descripcion;
            $data[] = array(
                            'label' => trim($name),
                            'id'    => $value->id,
                            'value' => trim($name),
                        );
        }
        return json_encode($data);
    }

    public function examenesAutocompletar($searching)
    {
        $resultado        = Servicio::where('nombre', 'LIKE', '%'.strtoupper($searching).'%')->where('tiposervicio_id','!=', 1)->whereNull('deleted_at')->orderBy('nombre', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $name = $value->nombre;
            $data[] = array(
                            'label' => trim($name),
                            'id'    => $value->id,
                            'value' => trim($name),
                        );
        }
        return json_encode($data);
    }

    public function registrarHistoriaClinica(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $error = DB::transaction(function() use($request){
            //Recepción de datos:
            $historia_id = $request->input('historia_id');
            $nsesion = $request->input('nsesion');
            $frecuencia = $request->input('frecuencia');
            $turno = Turno::where("hora", "=", $request->input('turno'))->first();
            $turno = $turno->id;
            $txtEvoSigSin = $request->input('txtEvoSigSin');
            $txtPA = $request->input('txtPA');
            $txtFC = $request->input('txtFC');
            $txtFR = $request->input('txtFR');
            $txtHorasHemodialisis = $request->input('txtHorasHemodialisis');
            $txtPesoInicial = $request->input('txtPesoInicial');
            $txtQb = $request->input('txtQb');
            $txtNaInicial = $request->input('txtNaInicial');
            $txtDosisHepa = $request->input('txtDosisHepa');
            $txtPesoFinal = $request->input('txtPesoFinal');
            $txtQd = $request->input('txtQd');
            $txtNaFinal = $request->input('txtNaFinal');
            $txtPesoSeco = $request->input('txtPesoSeco');
            $txtPerfilUF = $request->input('txtPerfilUF');
            $txtBufer = $request->input('txtBufer');
            $txtPerfilNa = $request->input('txtPerfilNa');
            $txtMedicacion = $request->input('txtMedicacion');
            $txtUltrafiltrado = $request->input('txtUltrafiltrado');
            $txtConductividad = $request->input('txtConductividad');
            $txtAreaDializador = $request->input('txtAreaDializador');
            $txtMembranaDializador = $request->input('txtMembranaDializador');
            $txtCondicionClinicaFinal = $request->input('txtCondicionClinicaFinal');
            $txtPAInicial = $request->input('txtPAInicial');
            $txtNPuesto = $request->input('txtNPuesto');
            $txtPesoInicial2 = $request->input('txtPesoInicial2');
            $txtMarcaModeloMaquina = $request->input('txtMarcaModeloMaquina');
            $txtMarcaModeloMaquina2 = $request->input('txtMarcaModeloMaquina2');
            $txtUltrafiltadoProgramado = $request->input('txtUltrafiltadoProgramado');
            $txtUltrafiltadoProgramado2 = $request->input('txtUltrafiltadoProgramado2');
            $txtUltrafiltadoProgramado3 = $request->input('txtUltrafiltadoProgramado3');
            $txtLoteSerieFiltro = $request->input('txtLoteSerieFiltro');
            $txtLoteSerieFiltro2 = $request->input('txtLoteSerieFiltro2');
            $txtAccesoVascularArterial = $request->input('txtAccesoVascularArterial');
            $txtAccesoVascularVenoso = $request->input('txtAccesoVascularVenoso');
            $txtPAFinal = $request->input('txtPAFinal');
            $txtNMAquina = $request->input('txtNMAquina');
            $txtPesoFinal2 = $request->input('txtPesoFinal2');
            $txtAreaMembranaFiltro = $request->input('txtAreaMembranaFiltro');
            $txtValoracionEnfermeria = $request->input('txtValoracionEnfermeria');            
            $txtObservacionFinal = $request->input('txtObservacionFinal');
            $txtAspectoFiltro = $request->input('txtAspectoFiltro');
            $txtHoraEvaluacionPrevia = $request->input('txtHoraEvaluacionPrevia');
            $txtMuestraAnalisis = $request->input('txtMuestraAnalisis');
            $txtCies = $request->input('cadenacies');
            $txtTemperatura = $request->input('txtTemperatura');
            //Armamos datos de las tablas

            $txtEvalHemodialisis = '';
            
            for ($i=1; $i <= 8; $i++) {                 
                for ($j=1; $j <= 10; $j++) { 
                    $txtEvalHemodialisis .= $request->input('txtEvalHemodialisis'.$i.$j);
                    if($j != 10) {
                        $txtEvalHemodialisis .= '&ilid&';
                    }
                }  
                if($i != 8) {  
                    $txtEvalHemodialisis .= '&iliu&';
                }
            }

            $txtAdmiMedic = '';

            for ($i=1; $i <= 6; $i++) { 
                for ($j=1; $j <= 2; $j++) {
                    $txtAdmiMedic .= $request->input('txtAdmiMedic'.$i.$j);
                    if($j != 2) {
                        $txtAdmiMedic .= '&ilid&';
                    }
                } 
                if($i != 6) {   
                    $txtAdmiMedic .= '&iliu&';
                }
            }

            $fecha_atencioni = date('Y-m-d 00:00:00');
            $fecha_atencionf = date('Y-m-d 23:59:59');

            //Fin de recepción de datos

            if($request->input("id_hc")!==NULL&&$request->input("id_hc")!=="") {
                $historiaclinica = HistoriaClinica::find($request->input("id_hc"));
            } else {
                $historiaclinica = HistoriaClinica::where('historia_id', '=', $historia_id)
                            ->where('estado', '!=', 'C')
                            ->whereBetween('fecha_atencion', [$fecha_atencioni, $fecha_atencionf])
                            ->first();
                $historiaclinica->numero = HistoriaClinica::numeroSigue();
            }
            
            $user = Person::find(Session::get('person_id'));
            //MEDICO
            $historiaclinica->doctor_id = $request->input("idInputFaltaMedico");
            //Responsable
            $historiaclinica->user_id = Auth::user()->id;
            //Guardo demás datos
            $historiaclinica->historia_id = $historia_id;            
            if($historiaclinica->txtCies!==$txtCies) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'CIES';
                $bita->valoranterior = $historiaclinica->txtCies;
                $bita->valornuevo = $txtCies;
                $bita->save();
            }
            $historiaclinica->txtCies = $txtCies;
            $historiaclinica->nsesion = $nsesion;
            $historiaclinica->frecuencia = $frecuencia;
            $historiaclinica->turno = $turno;            
            if($historiaclinica->txtEvoSigSin!==$txtEvoSigSin) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'EVOLUCIÓN: SIGNOS Y SÍNTOMAS';
                $bita->valoranterior = $historiaclinica->txtEvoSigSin;
                $bita->valornuevo = $txtEvoSigSin;
                $bita->save();
            }
            $historiaclinica->txtEvoSigSin = $txtEvoSigSin;            
            if($historiaclinica->txtPA!==$txtPA) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PA';
                $bita->valoranterior = $historiaclinica->txtPA;
                $bita->valornuevo = $txtPA;
                $bita->save();
            }
            $historiaclinica->txtPA = $txtPA;
            if($historiaclinica->txtTemperatura!==$txtTemperatura) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'TEMPERATURA';
                $bita->valoranterior = $historiaclinica->txtTemperatura;
                $bita->valornuevo = $txtTemperatura;
                $bita->save();
            }
            $historiaclinica->txtTemperatura = $txtTemperatura;
            if($historiaclinica->txtFC!==$txtFC) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'FC';
                $bita->valoranterior = $historiaclinica->txtFC;
                $bita->valornuevo = $txtFC;
                $bita->save();
            }
            $historiaclinica->txtFC = $txtFC;
            if($historiaclinica->txtFR!==$txtFR) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'FR';
                $bita->valoranterior = $historiaclinica->txtFR;
                $bita->valornuevo = $txtFR;
                $bita->save();
            }
            $historiaclinica->txtFR = $txtFR;
            if($historiaclinica->txtHorasHemodialisis!==$txtHorasHemodialisis) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'HORAS DE HEMODIÁLISIS';
                $bita->valoranterior = $historiaclinica->txtHorasHemodialisis;
                $bita->valornuevo = $txtHorasHemodialisis;
                $bita->save();
            }
            $historiaclinica->txtHorasHemodialisis = $txtHorasHemodialisis;
            if($historiaclinica->txtPesoInicial!==$txtPesoInicial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO INICIAL';
                $bita->valoranterior = $historiaclinica->txtPesoInicial;
                $bita->valornuevo = $txtPesoInicial;
                $bita->save();
            }
            $historiaclinica->txtPesoInicial = $txtPesoInicial;
            if($historiaclinica->txtQb!==$txtQb) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'Qb';
                $bita->valoranterior = $historiaclinica->txtQb;
                $bita->valornuevo = $txtQb;
                $bita->save();
            }
            $historiaclinica->txtQb = $txtQb;
            if($historiaclinica->txtNaInicial!==$txtNaInicial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'NA INICIAL';
                $bita->valoranterior = $historiaclinica->txtNaInicial;
                $bita->valornuevo = $txtNaInicial;
                $bita->save();
            }
            $historiaclinica->txtNaInicial = $txtNaInicial;
            if($historiaclinica->txtDosisHepa!==$txtDosisHepa) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'DOSIS DE HEPARINA';
                $bita->valoranterior = $historiaclinica->txtDosisHepa;
                $bita->valornuevo = $txtDosisHepa;
                $bita->save();
            }
            $historiaclinica->txtDosisHepa = $txtDosisHepa;
            if($historiaclinica->txtPesoFinal!==$txtPesoFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO FINAL';
                $bita->valoranterior = $historiaclinica->txtPesoFinal;
                $bita->valornuevo = $txtPesoFinal;
                $bita->save();
            }
            $historiaclinica->txtPesoFinal = $txtPesoFinal;
            if($historiaclinica->txtQd!==$txtQd) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'Qd';
                $bita->valoranterior = $historiaclinica->txtQd;
                $bita->valornuevo = $txtQd;
                $bita->save();
            }
            $historiaclinica->txtQd = $txtQd;
            if($historiaclinica->txtNaFinal!==$txtNaFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'NA FINAL';
                $bita->valoranterior = $historiaclinica->txtNaFinal;
                $bita->valornuevo = $txtNaFinal;
                $bita->save();
            }
            $historiaclinica->txtNaFinal = $txtNaFinal;
            if($historiaclinica->txtPesoSeco!==$txtPesoSeco) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO SECO';
                $bita->valoranterior = $historiaclinica->txtPesoSeco;
                $bita->valornuevo = $txtPesoSeco;
                $bita->save();
            }
            $historiaclinica->txtPesoSeco = $txtPesoSeco;
            if($historiaclinica->txtPerfilUF!==$txtPerfilUF) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PERFIL DE UF';
                $bita->valoranterior = $historiaclinica->txtPerfilUF;
                $bita->valornuevo = $txtPerfilUF;
                $bita->save();
            }
            $historiaclinica->txtPerfilUF = $txtPerfilUF;
            if($historiaclinica->txtBufer!==$txtBufer) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'BUFFER';
                $bita->valoranterior = $historiaclinica->txtBufer;
                $bita->valornuevo = $txtBufer;
                $bita->save();
            }
            $historiaclinica->txtBufer = $txtBufer;
            if($historiaclinica->txtPerfilNa!==$txtPerfilNa) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PERFIL DE NA';
                $bita->valoranterior = $historiaclinica->txtPerfilNa;
                $bita->valornuevo = $txtPerfilNa;
                $bita->save();
            }
            $historiaclinica->txtPerfilNa = $txtPerfilNa;
            if($historiaclinica->txtMedicacion!==$txtMedicacion) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MEDICACIÓN';
                $bita->valoranterior = $historiaclinica->txtMedicacion;
                $bita->valornuevo = $txtMedicacion;
                $bita->save();
            }
            $historiaclinica->txtMedicacion = $txtMedicacion;
            if($historiaclinica->txtUltrafiltrado!==$txtUltrafiltrado) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO A PROGRAMAR';
                $bita->valoranterior = $historiaclinica->txtUltrafiltrado;
                $bita->valornuevo = $txtUltrafiltrado;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltrado = $txtUltrafiltrado;
            if($historiaclinica->txtConductividad!==$txtConductividad) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'CONDUCTIVIDAD';
                $bita->valoranterior = $historiaclinica->txtConductividad;
                $bita->valornuevo = $txtConductividad;
                $bita->save();
            }
            $historiaclinica->txtConductividad = $txtConductividad;
            if($historiaclinica->txtAreaDializador!==$txtAreaDializador) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ÁREA DE DIALIZADOR';
                $bita->valoranterior = $historiaclinica->txtAreaDializador;
                $bita->valornuevo = $txtAreaDializador;
                $bita->save();
            }
            $historiaclinica->txtAreaDializador = $txtAreaDializador;
            if($historiaclinica->txtMembranaDializador!==$txtMembranaDializador) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MEMBRANA DE DIALIZADOR';
                $bita->valoranterior = $historiaclinica->txtMembranaDializador;
                $bita->valornuevo = $txtMembranaDializador;
                $bita->save();
            }
            $historiaclinica->txtMembranaDializador = $txtMembranaDializador;
            if($historiaclinica->txtCondicionClinicaFinal!==$txtCondicionClinicaFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'CONDICIÓN CLÍNICA DEL PACIENTE AL FINALIZAR HEMODIÁLISIS';
                $bita->valoranterior = $historiaclinica->txtCondicionClinicaFinal;
                $bita->valornuevo = $txtCondicionClinicaFinal;
                $bita->save();
            }
            $historiaclinica->txtCondicionClinicaFinal = $txtCondicionClinicaFinal;
            if($historiaclinica->txtPAInicial!==$txtPAInicial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PA INICIAL';
                $bita->valoranterior = $historiaclinica->txtPAInicial;
                $bita->valornuevo = $txtPAInicial;
                $bita->save();
            }
            $historiaclinica->txtPAInicial = $txtPAInicial;
            if($historiaclinica->txtNPuesto!==$txtNPuesto) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'N° PUESTO';
                $bita->valoranterior = $historiaclinica->txtNPuesto;
                $bita->valornuevo = $txtNPuesto;
                $bita->save();
            }
            $historiaclinica->txtNPuesto = $txtNPuesto;
            if($historiaclinica->txtPesoInicial2!==$txtPesoInicial2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO INICIAL';
                $bita->valoranterior = $historiaclinica->txtPesoInicial2;
                $bita->valornuevo = $txtPesoInicial2;
                $bita->save();
            }
            $historiaclinica->txtPesoInicial2 = $txtPesoInicial2;
            if($historiaclinica->txtMarcaModeloMaquina!==$txtMarcaModeloMaquina) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MARCA DE MÁQUINA';
                $bita->valoranterior = $historiaclinica->txtMarcaModeloMaquina;
                $bita->valornuevo = $txtMarcaModeloMaquina;
                $bita->save();
            }
            $historiaclinica->txtMarcaModeloMaquina = $txtMarcaModeloMaquina;
            if($historiaclinica->txtMarcaModeloMaquina2!==$txtMarcaModeloMaquina2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MODELO DE MÁQUINA';
                $bita->valoranterior = $historiaclinica->txtMarcaModeloMaquina2;
                $bita->valornuevo = $txtMarcaModeloMaquina2;
                $bita->save();
            }
            $historiaclinica->txtMarcaModeloMaquina2 = $txtMarcaModeloMaquina2;
            if($historiaclinica->txtUltrafiltadoProgramado!==$txtUltrafiltadoProgramado) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO EXCESIVO';
                $bita->valoranterior = $historiaclinica->txtUltrafiltadoProgramado;
                $bita->valornuevo = $txtUltrafiltadoProgramado;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltadoProgramado = $txtUltrafiltadoProgramado;
            if($historiaclinica->txtUltrafiltadoProgramado2!==$txtUltrafiltadoProgramado2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO PROGRAMADO';
                $bita->valoranterior = $historiaclinica->txtUltrafiltadoProgramado2;
                $bita->valornuevo = $txtUltrafiltadoProgramado2;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltadoProgramado2 = $txtUltrafiltadoProgramado2;
            if($historiaclinica->txtUltrafiltadoProgramado3!==$txtUltrafiltadoProgramado3) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO EFECTIVO';
                $bita->valoranterior = $historiaclinica->txtUltrafiltadoProgramado3;
                $bita->valornuevo = $txtUltrafiltadoProgramado3;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltadoProgramado3 = $txtUltrafiltadoProgramado3;
            if($historiaclinica->txtLoteSerieFiltro!==$txtLoteSerieFiltro) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'LOTE DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtLoteSerieFiltro;
                $bita->valornuevo = $txtLoteSerieFiltro;
                $bita->save();
            }
            $historiaclinica->txtLoteSerieFiltro = $txtLoteSerieFiltro;
            if($historiaclinica->txtLoteSerieFiltro2!==$txtLoteSerieFiltro2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'SERIE DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtLoteSerieFiltro2;
                $bita->valornuevo = $txtLoteSerieFiltro2;
                $bita->save();
            }
            $historiaclinica->txtLoteSerieFiltro2 = $txtLoteSerieFiltro2;
            if($historiaclinica->txtAccesoVascularArterial!==$txtAccesoVascularArterial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ACCESO VASCULAR ARTERIAL';
                $bita->valoranterior = $historiaclinica->txtAccesoVascularArterial;
                $bita->valornuevo = $txtAccesoVascularArterial;
                $bita->save();
            }
            $historiaclinica->txtAccesoVascularArterial = $txtAccesoVascularArterial;
            if($historiaclinica->txtAccesoVascularVenoso!==$txtAccesoVascularVenoso) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ACCESO VASCULAR VENOSO';
                $bita->valoranterior = $historiaclinica->txtAccesoVascularVenoso;
                $bita->valornuevo = $txtAccesoVascularVenoso;
                $bita->save();
            }
            $historiaclinica->txtAccesoVascularVenoso = $txtAccesoVascularVenoso;
            if($historiaclinica->txtPAFinal!==$txtPAFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PA FINAL';
                $bita->valoranterior = $historiaclinica->txtPAFinal;
                $bita->valornuevo = $txtPAFinal;
                $bita->save();
            }
            $historiaclinica->txtPAFinal = $txtPAFinal;
            if($historiaclinica->txtNMAquina!==$txtNMAquina) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'N° DE MÁQUINA';
                $bita->valoranterior = $historiaclinica->txtNMAquina;
                $bita->valornuevo = $txtNMAquina;
                $bita->save();
            }
            $historiaclinica->txtNMAquina = $txtNMAquina;
            if($historiaclinica->txtPesoFinal2!==$txtPesoFinal2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO FINAL';
                $bita->valoranterior = $historiaclinica->txtPesoFinal2;
                $bita->valornuevo = $txtPesoFinal2;
                $bita->save();
            }
            $historiaclinica->txtPesoFinal2 = $txtPesoFinal2;
            if($historiaclinica->txtAreaMembranaFiltro!==$txtAreaMembranaFiltro) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ÁREA/MEMBRANA DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtAreaMembranaFiltro;
                $bita->valornuevo = $txtAreaMembranaFiltro;
                $bita->save();
            }
            $historiaclinica->txtAreaMembranaFiltro = $txtAreaMembranaFiltro;
            if($historiaclinica->txtValoracionEnfermeria!==$txtValoracionEnfermeria) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'VALORACIÓN DE ENFERMERÍA';
                $bita->valoranterior = $historiaclinica->txtValoracionEnfermeria;
                $bita->valornuevo = $txtValoracionEnfermeria;
                $bita->save();
            }
            $historiaclinica->txtValoracionEnfermeria = $txtValoracionEnfermeria;
            if($historiaclinica->txtEvalHemodialisis!==$txtEvalHemodialisis) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'EVALUACIÓN DE HEMODIÁLISIS';
                $bita->valoranterior = $historiaclinica->txtEvalHemodialisis;
                $bita->valornuevo = $txtEvalHemodialisis;
                $bita->save();
            }
            $historiaclinica->txtEvalHemodialisis = $txtEvalHemodialisis;
            if($historiaclinica->txtObservacionFinal!==$txtObservacionFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'OBSERVACIÓN FINAL';
                $bita->valoranterior = $historiaclinica->txtObservacionFinal;
                $bita->valornuevo = $txtObservacionFinal;
                $bita->save();
            }
            $historiaclinica->txtObservacionFinal = $txtObservacionFinal;
            if($historiaclinica->txtAspectoFiltro!==$txtAspectoFiltro) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ASPECTO DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtAspectoFiltro;
                $bita->valornuevo = $txtAspectoFiltro;
                $bita->save();
            }
            $historiaclinica->txtAspectoFiltro = $txtAspectoFiltro;
            if($historiaclinica->txtAdmiMedic!==$txtAdmiMedic) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ADMINISTRACIÓN DE MEDICAMENTOS ENDOVENOSOS';
                $bita->valoranterior = $historiaclinica->txtAdmiMedic;
                $bita->valornuevo = $txtAdmiMedic;
                $bita->save();
            }
            $historiaclinica->txtAdmiMedic = $txtAdmiMedic;
            $historiaclinica->estado = 'F';
            $historiaclinica->txtHoraEvaluacionPrevia = $txtHoraEvaluacionPrevia;
            if($historiaclinica->txtMuestraAnalisis!==$txtMuestraAnalisis) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MUESTRA DE ANÁLISIS';
                $bita->valoranterior = $historiaclinica->txtMuestraAnalisis;
                $bita->valornuevo = $txtMuestraAnalisis;
                $bita->save();
            }
            $historiaclinica->txtMuestraAnalisis = $txtMuestraAnalisis;

            if($request->input("id_hc")!==NULL&&$request->input("id_hc")!=="") {
                if(date("Y-m-d")==date("Y-m-d", strtotime("Y-m-d", strtotime($historiaclinica->fecha_atencion)))) {
                    $historiaclinica->fecha_atencion = date('Y-m-d H:i:s');
                }
            } else {
                $historiaclinica->fecha_atencion = date('Y-m-d H:i:s');
            }            

            $historiaclinica->save();

            //Indicamos que la historia ya se registro
            $history = Historia::find($historia_id);
            $history->estado2 = 'F';
            $history->save();

            //Eliminar Bitácoras Nulas

            $bitacoras = BitacoraTratamiento::where('historiaclinica_id', '=', $historiaclinica->id)->where('valoranterior', '=', '')->whereNull('valornuevo')->get();

            foreach ($bitacoras as $bita) {
                $bita->delete();
            }

        });

        return is_null($error) ? "OK" : $error;
    }

    public function tablaCita(Request $request){

        $ruta             = $this->rutas;

        $historia_id = $request->input('historia_id');

        $resultado = HistoriaClinica::where('historia_id', '=', $historia_id)->where('estado', '!=', 'C')->orderBy('numero', 'ASC')->get();

        $tabla = "<table class='table table-bordered table-striped table-condensed table-hover'>
                            <thead>
                                <tr>
                                    <th class='text-center'>Nro</th>
                                    <th class='text-center'>Fecha</th>
                                    <th class='text-center'>Ver</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($resultado) == '0') {
            $tabla .= '<tr><td colspan="3"><center>No Hay Citas Anteriores</center></td></tr>';
        } else {
            foreach($resultado as $value){

                $tabla = $tabla . "<tr>
                <td><center>" . $value->numero . "</center></td>
                <td><center>" . date('d-m-Y',strtotime($value->fecha_atencion)) . "</center></td>
                <td><center><button class='btn btn-success btn-sm btnVerCita' id='btnVerCita' onclick='ver(".$value->id.")' data-toggle='modal' data-target='#exampleModal1' type='button'><i class='fa fa-eye fa-lg'></i> Ver Cita</button></center>
                </td></tr>";

            }
        }           

        $tabla = $tabla . "</tbody></table>";

        return $tabla;

    }

    public function ver(Request $request)
    {
        $cita_id             = $request->input('cita_id');
        $cita                = HistoriaClinica::find($cita_id);
        $historia            = Historia::find($cita->historia_id);
        //$cie10               = Cie::find($cita->cie_id);
        $doctor              = Person::find($cita->doctor_id);
        $user                = User::find($cita->user_id);
        $user2 = Auth::user();


        $texto = "<table class='table table-responsive table-hover'>
            <thead>
                <tr>
                    <td colspan='2'>
                        <center style='color:red'>
                            <h3>
                                Tratamiento N°". $cita->numero ." / ". date('d-m-Y',strtotime($cita->fecha_atencion)) ."
                            </h3>
                        </center>
                    </td>
                </tr>
            </thead>
            <tbody>";

                if($historia != null){
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Paciente</font></strong>
                        </td>
                        <td width='85%'>"
                            . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Paciente</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($doctor != null){

                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Médico</font></strong>
                        </td>
                        <td width='85%'>"
                            . $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Médico</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($historia != null){
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Historia</font></strong><br>
                        </td>
                        <td>"
                            . $historia->numero .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Historia</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                $citaproxima = Cita::find($cita->citaproxima);

                if($citaproxima != null){

                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Próxima cita</font></strong><br>
                        </td>
                        <td>"
                            . date('d-m-Y',strtotime( $citaproxima->fecha )) .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Próxima cita</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                 $cies = Detallehistoriacie::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

                if(count($cies) != 0){
                    $cont = 1;
                    $cies2 = "";
                    foreach ($cies as $value) {
                        $cies2 .= $cont . ' - ' . $value->cie->descripcion .'<br>';
                        $cont++;
                    }
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Cie 10</font></strong><br>
                        </td>
                        <td>"
                            . $cies2 .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Cie 10</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->motivo != null){
                
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Motivo</font></strong><br>
                        </td>
                        <td>"
                            . $cita->motivo .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Motivo</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->diagnostico != null){

                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Diagnóstico</font></strong><br>
                        </td>
                        <td>"
                            . $cita->diagnostico .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Diagnóstico</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->tratamiento != null){
                
                $texto .= "<tr>
                    <td>
                        <strong><font style='color:blue'>Tratamiento</font></strong><br>
                    </td>
                    <td>"
                        . $cita->tratamiento .
                    "</td>
                </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Tratamiento</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                $examenes = Examenhistoriaclinica::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

                if(count($examenes) != 0){

                    $cont = 1;
                    $examenes2 = "";
                    foreach ($examenes as $value) {
                        $examenes2 .= $cont . ' - ' . $value->servicio->nombre .'<br>';
                        $cont++;
                    }

                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Exámenes</font></strong><br>
                        </td>
                        <td>"
                            . $examenes2 .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Exámenes</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->exploracion_fisica != null){
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Exploración física</font></strong><br>
                        </td>
                        <td><div class='table-responsive' style='max-width:450px;'>"
                            . $cita->exploracion_fisica .
                        "</div></td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Exploración física</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($user != null){
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Responsable</font></strong><br>
                        </td>
                        <td>"
                            . $user->person->apellidopaterno . ' ' . $user->person->apellidomaterno . ' ' . $user->person->nombres .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Responsable</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

             $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Comentario</font></strong><br>
                        </td>
                        <td><textarea class='form-control' id='anadirComentario' rows='8'>" . $cita->comentario . "</textarea>";

            if($user2->usertype_id == 5 || $user2->usertype_id == 7 || $user2->usertype_id == 1) {

                $texto .= "<a class='btn btn-danger btn-xs' href='#' onclick='anadirComentario(" . $cita_id . ")'>Añadir</a>";
            }

            $texto .= "</td></tr></tbody></table>";

        return $texto;
    }

    public function tablaAtendidos(Request $request){
        date_default_timezone_set('America/Lima');
        $nombre = $request->input('nombre_atendido');
        $fecha = $request->input('fechaatencion');
        if($fecha == '') {
            $fecha = date('Y-m-d');
        }
        $ruta             = $this->rutas;
        $resultado = HistoriaClinica::leftjoin('historia as h', 'h.id', '=', 'historiaclinica.historia_id')
            ->join('person as paciente', 'paciente.id', '=', 'h.person_id')
            ->where('fecha_atencion', 'LIKE', date( "Y-m-d", strtotime($fecha))."%")
            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.$nombre.'%')
            ->orderBy('fecha_atencion', 'DESC')
            ->select('historiaclinica.*', 'h.baja');

        if(date( "Y-m-d", strtotime($fecha)) == date('Y-m-d')) {
            $resultado = $resultado->where('historiaclinica.estado', '!=', 'P');
        }

        $resultado = $resultado->get();

        $usertype_id = Auth::user()->usertype_id;

        $tabla = "<table style='width:100%;'>
                    <thead>
                        <tr>
                            <th class='text-center'>Nro</th>
                            <th class='text-center'>Hora</th>
                            <th class='text-center'>Paciente</th>
                            <th class='text-center'>Estado</th>
                            <th class='text-center'>Médico</th>
                            <th class='text-center' colspan='3'>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>";

        if(count($resultado) == 0) {
            $tabla .= '<tr><td colspan="6"><center>Aún no hay atendidos hoy.</center></td></tr>';
        } else {
            $c = 1;
            foreach($resultado as $value){

                $historia            = Historia::find($value->historia_id);

                $doctor              = Person::find($value->doctor_id);

                $estado = 'ATENDIÉNDOSE';
                $colors = 'blue';

                if($value->estado == 'F') {
                    $estado = 'FINALIZADO';
                    $colors = 'green';
                }

                else if($value->estado == 'C') {
                    $estado = 'CANCELADO';
                    $colors = 'red';
                }
                else if($value->estado == 'N') {
                    $estado = 'AUSENTE';
                    $colors = 'orange';
                } 
                else if($value->estado == 'P') {
                    $estado = 'PENDIENTE';
                    $colors = 'black';
                }

                $tabla .= "<tr>
                <td><center>" . $c . "</center></td>
                <td><center>" . date('h:i:s a',strtotime($value->txtHoraEvaluacionPrevia)) . "</center></td>
                <td><center>" . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres  . "</center></td>
                <td><center><b style='color:".$colors.";'>" . $estado  . "</b></center></td>
                <td><center>" . ($doctor === NULL ? '-' : ($doctor->apellidopaterno . ' ' . $doctor->nombres)) . "</center></td><td><center>";

                if($value->estado == 'F') {
                    $tabla .= "<button class='btn btn-danger btn-xs' onclick='ver(".$value->id.")' type='button'><i class='fa fa-file fa-lg'></i> Reporte</button>";
                    //$tabla .= "<button class='btn btn-warning btn-xs' onclick='modal(\"historiaclinica/reporte?hid=".$value->id."\", \"Formato de Atención de " . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres . "\", this)' type='button'><i class='fa fa-diamond fa-lg'></i> Formato</button>";
                    /*if($value->numeroformato !== NULL && $value->numeroformato !== '') {
                        $tabla .= "<button class='btn btn-success btn-xs' onclick='reporteformatoo(\"".$value->id."\");' type='button'><i class='fa fa-file fa-lg'></i></button>";
                    } else {
                        $tabla .= "<button class='btn btn-success btn-xs' type='button' disabled='disabled'><i class='fa fa-file fa-lg'></i></button>";
                    }*/
                } else if($value->estado == 'A'||$value->estado == 'P') {
                    $tabla .= "<button class='btn btn-danger btn-xs' onclick='ver(".$value->id.")' type='button' disabled='disabled'><i class='fa fa-file fa-lg'></i> Reporte </button>";
                    //$tabla .= "<button class='btn btn-warning btn-xs' onclick='modal(\"historiaclinica/reporte?hid=".$value->id."\", \"Formato de Atención de " . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres . "\", this)' type='button'  disabled='disabled'><i class='fa fa-diamond fa-lg'></i> Formato</button>";
                    //$tabla .= "<button class='btn btn-success btn-xs' type='button' disabled='disabled'><i class='fa fa-file fa-lg'></i></button>";
                } else if($value->estado == 'C'||$value->estado == 'N') {
                    $tabla .= "<button class='btn btn-danger btn-xs' onclick='ver(".$value->id.")' type='button' disabled='disabled'><i class='fa fa-file fa-lg'></i> Reporte </button>";
                    //$tabla .= "<button class='btn btn-warning btn-xs' onclick='modal(\"historiaclinica/reporte?hid=".$value->id."\", \"Formato de Atención de " . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres . "\", this)' type='button'  disabled='disabled'><i class='fa fa-diamond fa-lg'></i> Formato</button>";
                    //$tabla .= "<button class='btn btn-success btn-xs' type='button' disabled='disabled'><i class='fa fa-file fa-lg'></i></button>";
                }

                if($value->baja !== 'S') {
                    if($value->estado == 'A') {
                        /*$tabla .= "</center>
                        <td><center><button class='btn btn-primary btn-xs btnLlamarPaciente2' data-id='".$value->historia_id."' onclick='editar(".$value->id.")' type='button'><i class='fa fa-pencil fa-lg'></i> Editar</button></center>
                        </td>";*/
                        $tabla .= "</center>
                        <td><center><button class='btn btn-primary btn-xs btnLlamarPaciente2' data-hid='".$value->id."' data-id='".$value->historia_id."' type='button'><i class='fa fa-pencil fa-lg'></i> Editar</button></center>
                        </td>";
                    } else if($value->estado == 'C'||$value->estado == 'N') {
                        $tabla .= "</center>
                        <td><center><button disabled='disabled' class='btn btn-primary btn-xs' type='button'><i class='fa fa-pencil fa-lg'></i> Editar</button></center>
                        </td>";
                    } else if($value->estado == 'P') {
                        /*$tabla .= "</center>
                        <td><center><button class='btn btn-primary btn-xs btnLlamarPaciente2' data-id='".$value->historia_id."' onclick='editar(".$value->id.")' type='button'><i class='fa fa-pencil fa-lg'></i> Editar</button></center>
                        </td>";*/
                        $tabla .= "</center>
                        <td><center><button class='btn btn-primary btn-xs btnLlamarPaciente2' data-hid='".$value->id."' data-id='".$value->historia_id."' type='button'><i class='fa fa-pencil fa-lg'></i> Editar</button></center>
                        </td>";
                    }
                } else {
                    $tabla .= "</center>
                        <td><center><button class='btn btn-danger btn-xs' type='button'><i class='fa fa-pencil fa-lg'></i> Dado de baja</button></center>
                        </td>";
                }

                $tabla .= "<td><center>";

                /*$tabla .= "<td>
                            <center>
                            <button class='btn btn-info btn-xs' data-toggle='modal' data-target='#exampleModal4' onclick='abrirModalAntecedentesPasados(\"".$historia->numero."\", \"" . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres . "\")' type='button'><i class='fa fa-eye fa-lg'></i> Antecedentes
                            </button>
                                </center>
                        </td>
                    <td>
                    <center>";*/

                if($usertype_id == 1 || $usertype_id == 2 || $usertype_id == 31) {
                    $tabla .= "<button class='btn btn-success btn-xs btnSeguimiento' data-toggle='modal' data-target='#exampleModal12' onclick='verSeguimiento(".$value->id.")' type='button'><i class='fa fa-diamond fa-lg'></i> Seguimiento</button>";
                } else {
                    $tabla .= "-";
                }

                $tabla .= '</center></td></tr>';
                $c++;
            }
        }           

        $tabla = $tabla . "</tbody></table>";

        $teibol = array(
            "tabla" => $tabla,
            "fecha" => date( "d-m-Y", strtotime($fecha)),
        );

        return $teibol;

    }

    public function editarCita(Request $request){

        $historiaclinica = HistoriaClinica::find($request->input('cita_id'));

        $historia = Historia::find($historiaclinica->historia_id);

        $Ticket   = Movimiento::find($historiaclinica->ticket_id);

        $detallemovcaja = Detallemovcaja::where('movimiento_id', $historiaclinica->ticket_id)->first();

        $doctor = Person::find($detallemovcaja->persona_id);

        $fondo = "NO";
        if($Ticket->tiempo_fondo !== null){
            $fondo = "SI";
        }

        $citaproxima = null;

        if($historiaclinica->citaproxima !== null){
            $cita = Cita::find($historiaclinica->citaproxima);
            $citaproxima = $cita->fecha;
        }

        $examenes = Examenhistoriaclinica::leftjoin('servicio as servicio', 'servicio.id', '=', 'examenhistoriaclinica.servicio_id')
                    ->where('examenhistoriaclinica.historiaclinica_id', $historiaclinica->id )
                    ->get();

        //$cie10 = Cie::find($historiaclinica->cie_id);

        $cies = Detallehistoriacie::leftjoin('cie', 'cie.id', '=', 'detallehistoriacie.cie_id')
        ->where('detallehistoriacie.historiaclinica_id',  $historiaclinica->id )->get();

        if($citaproxima != null){


            $jsondata = array(
                'atencion_id' => $request->input('cita_id'),
                'fecha' => date('d-m-Y',strtotime($historiaclinica->fecha_atencion)) ,
                'citaproxima' => date('Y-m-d',strtotime($citaproxima)) ,
                'fondo' => $fondo,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'antecedentes' => $historia->antecedentes,
                'numero' => $historiaclinica->numero,
                'motivo' => $historiaclinica->motivo,
                //'cie10' => (is_null($cie10)?'':$cie10->codigo),
                'sintomas' => $historiaclinica->sintomas,
                'tratamiento' => $historiaclinica->tratamiento,
                'diagnostico' => $historiaclinica->diagnostico,
                'exploracion_fisica' => $historiaclinica->exploracion_fisica,
                'examenes' => $examenes,
                'cies' => $cies,
                'cantcies' => count($cies),
            );

        }else{
            
            $jsondata = array(
                'atencion_id' => $request->input('cita_id'),
                'fecha' => date('d-m-Y',strtotime($historiaclinica->fecha_atencion)) ,
                'fondo' => $fondo,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'antecedentes' => $historia->antecedentes,
                'numero' => $historiaclinica->numero,
                'motivo' => $historiaclinica->motivo,
                //'cie10' => (is_null($cie10)?'':$cie10->codigo),
                'sintomas' => $historiaclinica->sintomas,
                'tratamiento' => $historiaclinica->tratamiento,
                'diagnostico' => $historiaclinica->diagnostico,
                'exploracion_fisica' => $historiaclinica->exploracion_fisica,
                'examenes' => $examenes,
                'cies' => $cies,
                'cantcies' => count($cies),
            );

        }

        return json_encode($jsondata);

    }

    public function guardarEditado(Request $request){

        if($request->input('citaproxima') != null){

            $historiaclinica   = HistoriaClinica::find($request->input('cita_id'));

            if( $historiaclinica->citaproxima != null){

                $error = DB::transaction(function() use($request, $historiaclinica){
                    $cita  = Cita::find($historiaclinica->citaproxima);
                    $cita->fecha  = $request->input('citaproxima');
                    $cita->save();
                });
                
            }else{
                            
                $error = DB::transaction(function() use($request, $historiaclinica){
                        
                    $Cita       = new Cita();

                    $user = Auth::user();
                    
                    //sucursal_id
                    $sucursal_id = 1;

                    $Cita->sucursal_id = $sucursal_id;
                    $Cita->fecha = $request->input('citaproxima');

                    $historia = Historia::find($historiaclinica->historia_id);

                    $Cita->paciente_id = $historia->persona->id;


                    $Cita->paciente = $historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres;
                    $Cita->historia = $historia->numero;
                    $Cita->tipopaciente = $historia->tipopaciente;


                    $Cita->historia_id = $historia->id;
                    
                    $Cita->doctor_id = $historiaclinica->doctor_id;
                    
                    $Cita->situacion='P';//Pendiente
        
                    $Cita->usuario_id = $user->person_id;
                    $Cita->save();

                });


            }

        }


        $error = DB::transaction(function() use($request){
            $historiaclinica   = HistoriaClinica::find($request->input('cita_id'));
            $historiaclinica->tratamiento    = strtoupper($request->input('tratamiento'));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diagnostico    = strtoupper($request->input('diagnostico'));
            //$historiaclinica->examenes             = strtoupper($request->input('examenes'));
            $historiaclinica->motivo               = strtoupper($request->input('motivo'));
            $historiaclinica->exploracion_fisica   = strtoupper($request->input('exploracion_fisica'));          
            $user = Auth::user();
            $historiaclinica->user_id   = $user->id;

            if($request->input('citaproxima') != null){
                    $cita_id = Cita::where('historia_id',$historiaclinica->historia_id)->where('paciente_id', $historiaclinica->historia->persona->id)->max('id');
                    $historiaclinica->citaproxima     = $cita_id;
            }else{
                if($historiaclinica->citaproxima != null){
                    $citaant = Cita::find($historiaclinica->citaproxima);
                    $citaant->delete();
                    $historiaclinica->citaproxima     =  null ;
                }
            }

            $historiaclinica->save();

            $historia = Historia::find($historiaclinica->historia_id);
            $historia->antecedentes = strtoupper($request->input('antecedentes'));
            $historia->save();

            $Ticket   = Movimiento::find($historiaclinica->ticket_id);

            $now = new \DateTime();
/*
            if( $request->input('fondo') == "SI"){
                if($Ticket->situacion2 != 'F'){
                    $Ticket->tiempo_fondo  = $now;
                    $Ticket->situacion2 = 'F'; // Cola por fondo
                }
            }else{
                $Ticket->tiempo_fondo  = null;
                $Ticket->situacion2 = 'L'; // Cola por fondo
            }
*/
            $Ticket->save();

        });


        $historiaclinica = HistoriaClinica::find( $request->input('cita_id') );

        $ciesborrar = Detallehistoriacie::where('historiaclinica_id', $historiaclinica->id )->get();
        foreach ($ciesborrar as $value) {
            $error = DB::transaction(function() use($request, $value){
                $value->delete();
            });
            
        }
        $cies = json_decode($request->input('cies'));
        foreach ($cies->{"data"} as $cie) {
            $error = DB::transaction(function() use($request, $historiaclinica, $cie){
                $detallehistoriacie = new Detallehistoriacie();
                $detallehistoriacie->historiaclinica_id = $historiaclinica->id;
                $detallehistoriacie->cie_id = $cie->{"id"};
                $detallehistoriacie->save();
            });
        }


        $examenesborrar = Examenhistoriaclinica::where('historiaclinica_id', $historiaclinica->id )->get();

        foreach ($examenesborrar as $value) {

            $error = DB::transaction(function() use($request, $value){

                $value->delete();

            });
            
        }

        $examenes = json_decode($request->input('examenes'));

        foreach ($examenes->{"data"} as $examen) {
            $error = DB::transaction(function() use($request, $historiaclinica, $examen){

                $examenhistoriaclinica = new Examenhistoriaclinica();
                $examenhistoriaclinica->situacion = 'N';
                $examenhistoriaclinica->historiaclinica_id = $historiaclinica->id;
                $examenhistoriaclinica->servicio_id = $examen->{"id"};
                $examenhistoriaclinica->save();

            });
        }
        return is_null($error) ? "OK" : $error;
    }

    public function infoPaciente(Request $request){
       
        $historia_id = $request->input('historia');
        $historia = Historia::find($historia_id);
        $paciente = Person::find($historia->person_id);
        $texto = "<table class='table table-responsive table-hover'>
            <thead>
                <tr>
                    <td colspan='2'>
                        <center style='color:red'>
                            <h3>
                                Paciente: ". $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres ."
                            </h3>
                        </center>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width='30%'>
                        <strong><font style='color:blue'>DNI/CE:</font></strong>
                    </td>
                    <td width='70%'>";
                        if($paciente->dni !=null ){
                            $texto .= $paciente->dni;
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Fecha de nacimiento:</font></strong><br>
                    </td>
                    <td>";
                        if( $paciente->fechanacimiento != null){
                            $texto .= date('d-m-Y',strtotime($paciente->fechanacimiento));
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Edad:</font></strong><br>
                    </td>
                    <td>";
                        
                    $dia=date("d");
                    $mes=date("m");
                    $ano=date("Y");
                    $dianaz=date("d",strtotime($paciente->fechanacimiento));
                    $mesnaz=date("m",strtotime($paciente->fechanacimiento));
                    $anonaz=date("Y",strtotime($paciente->fechanacimiento));
                    //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
                    if (($mesnaz == $mes) && ($dianaz > $dia)) {
                    $ano=($ano-1); }
                    //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
                    if ($mesnaz > $mes) {
                    $ano=($ano-1);}
                    //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
                    $edad=($ano-$anonaz);
                    if( $paciente->fechanacimiento != null){
                        $texto .= $edad;
                    }else{
                        $texto .= " - ";
                    }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Teléfono:</font></strong><br>
                    </td>
                    <td>";
                        if( $paciente->fechanacimiento != null){
                            $texto .= $paciente->telefono;
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Dirección</font></strong><br>
                    </td>
                    <td>";
                        if( $paciente->fechanacimiento != null){
                            $texto .= $paciente->direccion;
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
            </tbody>
        </table>";
        return $texto;

    }

    public function cantidadCitasFecha(Request $request){
        $fecha = $request->input('fecha');

        $cantidad = Cita::where('fecha', '=', ''.$fecha.'')->count('id');

        return $cantidad;
    }

    public function anadirComentario(Request $request)
    {
        $error = DB::transaction(function() use($request){

            $cita_id = $request->input('cita_id');
            $comentario = $request->input('comentario');

            $cita     = HistoriaClinica::find($cita_id);
            $cita->comentario = $comentario;
            $cita->save();

        });

        return $error == null ? '1' : $error;
    }

    public function infoAntecedentes(Request $request){       
        $numhistoria = $request->input('historia');
        $historia = Historia::where('numero','=', $numhistoria)->first();
        $texto = $historia->antecedentes2;
        return $texto;
    }

    public function actualizarAntecedentes(Request $request){       
        $numhistoria = $request->input('historia');
        $antecedentes = $request->input('antecedentes');
        $historia = Historia::where('numero','=', $numhistoria)->first();
        $historia->antecedentes2 = $antecedentes;
        $historia->save();
    }

    public function cambiarEstadoIP(Request $request) {
        date_default_timezone_set('America/Lima');
        //L: LLAMANDO
        //A: ATENDIENDOSE
        //N: NO ESTÁ
        //P: PENDIENTE
        //F: FINALIZADO
        //C: CANCELADA
        $historia_id = $request->input('historia_id');
        $action = $request->input('action');
        //$historiaclinica = HistoriaClinica::where('historia_id', '=', $historia_id)->where('estado', '!=', 'C')->where('fecha_atencion', '>=', date('Y-m-d'))->first();
        $historiaclinica = HistoriaClinica::find($request->input("cid"));
        if($action=='0') {
            $historiaclinica->ip = $request->ip();
            $historiaclinica->estado = 'A';
        } elseif ($action=='1') {
            $historiaclinica->ip = $request->ip();
            $historiaclinica->estado = 'A';
        } else {
            $historiaclinica->ip = NULL;
            $historiaclinica->estado = 'N';
        }            
        $historiaclinica->save();
    }

    public function testearIPPendientes(Request $request) {
        date_default_timezone_set('America/Lima');
        $historiaclinica = HistoriaClinica::where('ip', '=', $request->ip())->where('estado', '!=', 'F')->where('estado', '!=', 'C')->where('estado', '!=', 'N')->first();
        if($historiaclinica !== NULL) {
            $historia = Historia::find($historiaclinica->historia_id);
            if($historiaclinica!==NULL) {            
                $persona = $historia->persona->apellidopaterno .' '.$historia->persona->apellidomaterno.' '.$historia->persona->nombres;
                //penultima hc
                $phc = HistoriaClinica::where('historia_id', '=', $historia->id)->where('estado', '=', 'F')->where('estado', '!=', 'C')->orderBy('fecha_atencion', 'DESC')->first();
                //Calculo veces por semana (frecuencia)
                $ordencitas = explode(';', $historia->ordencitas);
                $frecuencia = count($ordencitas)-1;
                //Calculo N Hemodialisis mes
                if($request->input('cid')!==NULL&&$request->input('cid')!=="") {
                    $fecha = date("Y-m-d", strtotime($historiaclinica->fecha_atencion));
                    $diaa = date("d", strtotime($historiaclinica->fecha_atencion));
                    $mess = date("m", strtotime($historiaclinica->fecha_atencion));
                    $anoo = date("Y", strtotime($historiaclinica->fecha_atencion));
                } else {
                    $fecha = date('Y-m-d');
                    $diaa = date("d", strtotime($fecha));
                    $mess = date("m", strtotime($fecha));
                    $anoo = date("Y", strtotime($fecha));
                }
                $a = 0;
                for ($i=1; $i <= $diaa; $i++) {
                    $fechina = $anoo . "-" . $mess . "-" . $i;
                    $hcc = HistoriaClinica::where('historia_id', '=', $historia->id)->where('fecha_atencion', 'LIKE', $fechina."%")->first();
                    if($hcc!==NULL) {
                        $a++;
                    }
                }
                $numsesion = $a+1;
                //Calculo prox cita
                $citaproxima = '';
                $fechaa = date("Y-m-d", strtotime($historiaclinica->fecha_atencion));
                $fecha = strtotime($fechaa);
                $diasemana = date('w', strtotime($fechaa));
                if($diasemana==0) {
                    $diasemana=7;
                }
                $fecha0 = explode('-', $fechaa);
                $fechai = $fecha0[0].'-'.$fecha0[1].'-';
                for ($i=($fecha0[2]+1); $i <= 31; $i++) {   
                    $fechaf = strtotime($fechai.$i);
                    $diasemana = date('w', $fechaf);
                    if($diasemana==0) {
                        $diasemana=7;
                    }      
                    foreach ($ordencitas as $f0) {
                        if($f0==$diasemana) {
                            $citaproxima=$fechai.$i;
                            $i=31;
                            break;
                        }
                    }           
                }

                $PesoSeco = "";
                if($historiaclinica->txtPesoSeco!==NULL&&$historiaclinica->txtPesoSeco!=="") {
                    $PesoSeco = $historiaclinica->txtPesoSeco;
                } else {
                    if($phc!==NULL) {
                        $PesoSeco = $phc->txtPesoSeco;
                    }
                }     

                //txtMembranaDializador
                $MembranaDializador = "POLISULFONA";
                if($historiaclinica->txtMembranaDializador!==NULL&&$historiaclinica->txtMembranaDializador!=="") {
                    $MembranaDializador = $historiaclinica->txtMembranaDializador;
                }     

                //txtBufer   
                $Bufer = "BICARBONATO";
                if($historiaclinica->txtBufer!==NULL&&$historiaclinica->txtBufer!=="") {
                    $Bufer = $historiaclinica->txtBufer;
                }

                //txtMedicacion
                $Medicacion = "* EPOETINA ALFA 2000 UI/ML. INY 1 ML: \n* HIERRO 20MG FE/ML. INY 5 ML: \n* VITAMINA B12 HIDROXICOBALAMINA 1MG7ML INY 1 ML: ";
                if($historiaclinica->txtMedicacion!==NULL&&$historiaclinica->txtMedicacion!=="") {
                    $Medicacion = $historiaclinica->txtMedicacion;
                }

                //txtMarcaModeloMaquina

                $MarcaModeloMaquina = "NIPRO";
                if($historiaclinica->txtMarcaModeloMaquina!==NULL&&$historiaclinica->txtMarcaModeloMaquina!=="") {
                    $MarcaModeloMaquina = $historiaclinica->txtMarcaModeloMaquina;
                }

                //txtMarcaModeloMaquina2

                $MarcaModeloMaquina2 = "DIAMAX";
                if($historiaclinica->txtMarcaModeloMaquina2!==NULL&&$historiaclinica->txtMarcaModeloMaquina2!=="") {
                    $MarcaModeloMaquina2 = $historiaclinica->txtMarcaModeloMaquina2;
                }

                $dat = array(
                    'action'=>'SI', 
                    'historia_id'=>$historiaclinica->historia_id,
                    'cid'=>$historiaclinica->id,
                    'fecha_atencion'=> date("Y-m-d", strtotime($historiaclinica->fecha_atencion)),
                    'numhistoria' => $historia->numero,
                    'paciente' => $persona,
                    'turno' => $historia->turno->hora,
                    'romano' => $historia->turno->romano,
                    'plan_susalud' => $historia->carnet,
                    'convenio' => $historia->convenio->nombre,
                    'frecuencia' => $frecuencia,
                    'numsesion' => $numsesion,
                    'citaproxima' => date('d-m-Y', strtotime($citaproxima)),
                    'estado' => $historiaclinica->estado,

                    'txtEvoSigSin' => $historiaclinica->txtEvoSigSin,
                    'txtPA' => $historiaclinica->txtPA,
                    'txtFC' => $historiaclinica->txtFC,
                    'txtFR' => $historiaclinica->txtFR,
                    'txtHorasHemodialisis' => $historiaclinica->txtHorasHemodialisis,
                    'txtPesoInicial' => $historiaclinica->txtPesoInicial,
                    'txtQb' => $historiaclinica->txtQb,
                    'txtNaInicial' => $historiaclinica->txtNaInicial,
                    'txtDosisHepa' => $historiaclinica->txtDosisHepa,
                    'txtPesoFinal' => $historiaclinica->txtPesoFinal,
                    'txtQd' => $historiaclinica->txtQd,
                    'txtNaFinal' => $historiaclinica->txtNaFinal,
                    'txtPesoSeco' => $PesoSeco,
                    'txtPerfilUF' => $historiaclinica->txtPerfilUF,
                    'txtBufer' => $Bufer,
                    'txtPerfilNa' => $historiaclinica->txtPerfilNa,
                    'txtMedicacion' => $Medicacion,
                    'txtUltrafiltrado' => $historiaclinica->txtUltrafiltrado,
                    'txtConductividad' => $historiaclinica->txtConductividad,
                    'txtAreaDializador' => $historiaclinica->txtAreaDializador,
                    'txtMembranaDializador' => $MembranaDializador,
                    'txtCondicionClinicaFinal' => $historiaclinica->txtCondicionClinicaFinal,
                    'txtPAInicial' => $historiaclinica->txtPAInicial,
                    'txtNPuesto' => $historiaclinica->txtNPuesto,
                    'txtPesoInicial2' => $historiaclinica->txtPesoInicial2,
                    'txtMarcaModeloMaquina' => $MarcaModeloMaquina,
                    'txtMarcaModeloMaquina2' => $MarcaModeloMaquina2,
                    'txtUltrafiltadoProgramado' => $historiaclinica->txtUltrafiltadoProgramado,
                    'txtUltrafiltadoProgramado2' => $historiaclinica->txtUltrafiltadoProgramado2,
                    'txtUltrafiltadoProgramado3' => $historiaclinica->txtUltrafiltadoProgramado3,
                    'txtLoteSerieFiltro' => $historiaclinica->txtLoteSerieFiltro,
                    'txtLoteSerieFiltro2' => $historiaclinica->txtLoteSerieFiltro2,
                    'txtAccesoVascularArterial' => $historiaclinica->txtAccesoVascularArterial,
                    'txtAccesoVascularVenoso' => $historiaclinica->txtAccesoVascularVenoso,
                    'txtPAFinal' => $historiaclinica->txtPAFinal,
                    'txtTemperatura' => $historiaclinica->txtTemperatura,
                    'txtNMAquina' => $historiaclinica->txtNMAquina,
                    'txtPesoFinal2' => $historiaclinica->txtPesoFinal2,
                    'txtAreaMembranaFiltro' => $historiaclinica->txtAreaMembranaFiltro,
                    'txtValoracionEnfermeria' => $historiaclinica->txtValoracionEnfermeria,
                    'txtEvalHemodialisis' => $historiaclinica->txtEvalHemodialisis,
                    'txtObservacionFinal' => $historiaclinica->txtObservacionFinal,
                    'txtAspectoFiltro' => $historiaclinica->txtAspectoFiltro,
                    'txtAdmiMedic' => $historiaclinica->txtAdmiMedic,
                    'txtMuestraAnalisis' => $historiaclinica->txtMuestraAnalisis,
                    'txtCies' => $historiaclinica->txtCies,
                    'txtHoraEvaluacionPrevia' => date('H:i:s'),
                );
            } else {
                $dat = array(
                    'action'=>'NO',
                );
            }
        } else {
            $dat = array(
                'action'=>'NO',
            );
        }
        return json_encode($dat);
    }

    public function registrarHistoriaClinica2(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $error = DB::transaction(function() use($request){
            //Recepción de datos:
            $historia_id = $request->input('historia_id');
            $nsesion = $request->input('nsesion');
            $frecuencia = $request->input('frecuencia');
            $turno = Turno::where("hora", "=", $request->input('turno'))->first();
            $turno = $turno->id;
            $txtEvoSigSin = $request->input('txtEvoSigSin');
            $txtPA = $request->input('txtPA');
            $txtFC = $request->input('txtFC');
            $txtFR = $request->input('txtFR');
            $txtHorasHemodialisis = $request->input('txtHorasHemodialisis');
            $txtPesoInicial = $request->input('txtPesoInicial');
            $txtQb = $request->input('txtQb');
            $txtNaInicial = $request->input('txtNaInicial');
            $txtDosisHepa = $request->input('txtDosisHepa');
            $txtPesoFinal = $request->input('txtPesoFinal');
            $txtQd = $request->input('txtQd');
            $txtNaFinal = $request->input('txtNaFinal');
            $txtPesoSeco = $request->input('txtPesoSeco');
            $txtPerfilUF = $request->input('txtPerfilUF');
            $txtBufer = $request->input('txtBufer');
            $txtPerfilNa = $request->input('txtPerfilNa');
            $txtMedicacion = $request->input('txtMedicacion');
            $txtUltrafiltrado = $request->input('txtUltrafiltrado');
            $txtConductividad = $request->input('txtConductividad');
            $txtAreaDializador = $request->input('txtAreaDializador');
            $txtMembranaDializador = $request->input('txtMembranaDializador');
            $txtCondicionClinicaFinal = $request->input('txtCondicionClinicaFinal');
            $txtPAInicial = $request->input('txtPAInicial');
            $txtNPuesto = $request->input('txtNPuesto');
            $txtPesoInicial2 = $request->input('txtPesoInicial2');
            $txtMarcaModeloMaquina = $request->input('txtMarcaModeloMaquina');
            $txtMarcaModeloMaquina2 = $request->input('txtMarcaModeloMaquina2');
            $txtUltrafiltadoProgramado = $request->input('txtUltrafiltadoProgramado');
            $txtUltrafiltadoProgramado2 = $request->input('txtUltrafiltadoProgramado2');
            $txtUltrafiltadoProgramado3 = $request->input('txtUltrafiltadoProgramado3');
            $txtLoteSerieFiltro = $request->input('txtLoteSerieFiltro');
            $txtLoteSerieFiltro2 = $request->input('txtLoteSerieFiltro2');
            $txtAccesoVascularArterial = $request->input('txtAccesoVascularArterial');
            $txtAccesoVascularVenoso = $request->input('txtAccesoVascularVenoso');
            $txtPAFinal = $request->input('txtPAFinal');
            $txtNMAquina = $request->input('txtNMAquina');
            $txtPesoFinal2 = $request->input('txtPesoFinal2');
            $txtAreaMembranaFiltro = $request->input('txtAreaMembranaFiltro');
            $txtValoracionEnfermeria = $request->input('txtValoracionEnfermeria');            
            $txtObservacionFinal = $request->input('txtObservacionFinal');
            $txtAspectoFiltro = $request->input('txtAspectoFiltro');
            $txtHoraEvaluacionPrevia = $request->input('txtHoraEvaluacionPrevia');
            $txtMuestraAnalisis = $request->input('txtMuestraAnalisis');
            $txtCies = $request->input('cadenacies');
            $txtTemperatura = $request->input('txtTemperatura');
            //Armamos datos de las tablas

            $txtEvalHemodialisis = '';
            
            for ($i=1; $i <= 8; $i++) {                 
                for ($j=1; $j <= 10; $j++) { 
                    $txtEvalHemodialisis .= $request->input('txtEvalHemodialisis'.$i.$j);
                    if($j != 10) {
                        $txtEvalHemodialisis .= '&ilid&';
                    }
                }  
                if($i != 8) {  
                    $txtEvalHemodialisis .= '&iliu&';
                }
            }

            $txtAdmiMedic = '';

            for ($i=1; $i <= 6; $i++) { 
                for ($j=1; $j <= 2; $j++) {
                    $txtAdmiMedic .= $request->input('txtAdmiMedic'.$i.$j);
                    if($j != 2) {
                        $txtAdmiMedic .= '&ilid&';
                    }
                } 
                if($i != 6) {   
                    $txtAdmiMedic .= '&iliu&';
                }
            }

            $fecha_atencioni = date('Y-m-d 00:00:00');
            $fecha_atencionf = date('Y-m-d 23:59:59');

            //Fin de recepción de datos

            if($request->input("id_hc")!==NULL&&$request->input("id_hc")!=="") {
                $historiaclinica = HistoriaClinica::find($request->input("id_hc"));
            } else {
                $historiaclinica = HistoriaClinica::where('historia_id', '=', $historia_id)
                            ->where('estado', '!=', 'C')
                            ->whereBetween('fecha_atencion', [$fecha_atencioni, $fecha_atencionf])
                            ->first();
            }
                            
            $user = Person::find(Session::get('person_id'));
            //Responsable
            $historiaclinica->user_id = Auth::user()->id;
            //MEDICO SOLO SI ES UN USUARIO QUE GUARDA
            $userp_id = Auth::user()->person_id;
            if(Auth::user()->usertype_id==28||Auth::user()->usertype_id==29) {
                $historiaclinica->doctor_id = $userp_id;
            } 
            //Guardo demás datos
            $historiaclinica->historia_id = $historia_id;            
            if($historiaclinica->txtCies!==$txtCies) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'CIES';
                $bita->valoranterior = $historiaclinica->txtCies;
                $bita->valornuevo = $txtCies;
                $bita->save();
            }
            $historiaclinica->txtCies = $txtCies;
            $historiaclinica->nsesion = $nsesion;
            $historiaclinica->frecuencia = $frecuencia;
            $historiaclinica->turno = $turno;            
            if($historiaclinica->txtEvoSigSin!==$txtEvoSigSin) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'EVOLUCIÓN: SIGNOS Y SÍNTOMAS';
                $bita->valoranterior = $historiaclinica->txtEvoSigSin;
                $bita->valornuevo = $txtEvoSigSin;
                $bita->save();
            }
            $historiaclinica->txtEvoSigSin = $txtEvoSigSin;            
            if($historiaclinica->txtPA!==$txtPA) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PA';
                $bita->valoranterior = $historiaclinica->txtPA;
                $bita->valornuevo = $txtPA;
                $bita->save();
            }
            $historiaclinica->txtPA = $txtPA;
            if($historiaclinica->txtTemperatura!==$txtTemperatura) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'TEMPERATURA';
                $bita->valoranterior = $historiaclinica->txtTemperatura;
                $bita->valornuevo = $txtTemperatura;
                $bita->save();
            }
            $historiaclinica->txtTemperatura = $txtTemperatura;
            if($historiaclinica->txtFC!==$txtFC) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'FC';
                $bita->valoranterior = $historiaclinica->txtFC;
                $bita->valornuevo = $txtFC;
                $bita->save();
            }
            $historiaclinica->txtFC = $txtFC;
            if($historiaclinica->txtFR!==$txtFR) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'FR';
                $bita->valoranterior = $historiaclinica->txtFR;
                $bita->valornuevo = $txtFR;
                $bita->save();
            }
            $historiaclinica->txtFR = $txtFR;
            if($historiaclinica->txtHorasHemodialisis!==$txtHorasHemodialisis) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'HORAS DE HEMODIÁLISIS';
                $bita->valoranterior = $historiaclinica->txtHorasHemodialisis;
                $bita->valornuevo = $txtHorasHemodialisis;
                $bita->save();
            }
            $historiaclinica->txtHorasHemodialisis = $txtHorasHemodialisis;
            if($historiaclinica->txtPesoInicial!==$txtPesoInicial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO INICIAL';
                $bita->valoranterior = $historiaclinica->txtPesoInicial;
                $bita->valornuevo = $txtPesoInicial;
                $bita->save();
            }
            $historiaclinica->txtPesoInicial = $txtPesoInicial;
            if($historiaclinica->txtQb!==$txtQb) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'Qb';
                $bita->valoranterior = $historiaclinica->txtQb;
                $bita->valornuevo = $txtQb;
                $bita->save();
            }
            $historiaclinica->txtQb = $txtQb;
            if($historiaclinica->txtNaInicial!==$txtNaInicial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'NA INICIAL';
                $bita->valoranterior = $historiaclinica->txtNaInicial;
                $bita->valornuevo = $txtNaInicial;
                $bita->save();
            }
            $historiaclinica->txtNaInicial = $txtNaInicial;
            if($historiaclinica->txtDosisHepa!==$txtDosisHepa) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'DOSIS DE HEPARINA';
                $bita->valoranterior = $historiaclinica->txtDosisHepa;
                $bita->valornuevo = $txtDosisHepa;
                $bita->save();
            }
            $historiaclinica->txtDosisHepa = $txtDosisHepa;
            if($historiaclinica->txtPesoFinal!==$txtPesoFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO FINAL';
                $bita->valoranterior = $historiaclinica->txtPesoFinal;
                $bita->valornuevo = $txtPesoFinal;
                $bita->save();
            }
            $historiaclinica->txtPesoFinal = $txtPesoFinal;
            if($historiaclinica->txtQd!==$txtQd) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'Qd';
                $bita->valoranterior = $historiaclinica->txtQd;
                $bita->valornuevo = $txtQd;
                $bita->save();
            }
            $historiaclinica->txtQd = $txtQd;
            if($historiaclinica->txtNaFinal!==$txtNaFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'NA FINAL';
                $bita->valoranterior = $historiaclinica->txtNaFinal;
                $bita->valornuevo = $txtNaFinal;
                $bita->save();
            }
            $historiaclinica->txtNaFinal = $txtNaFinal;
            if($historiaclinica->txtPesoSeco!==$txtPesoSeco) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO SECO';
                $bita->valoranterior = $historiaclinica->txtPesoSeco;
                $bita->valornuevo = $txtPesoSeco;
                $bita->save();
            }
            $historiaclinica->txtPesoSeco = $txtPesoSeco;
            if($historiaclinica->txtPerfilUF!==$txtPerfilUF) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PERFIL DE UF';
                $bita->valoranterior = $historiaclinica->txtPerfilUF;
                $bita->valornuevo = $txtPerfilUF;
                $bita->save();
            }
            $historiaclinica->txtPerfilUF = $txtPerfilUF;
            if($historiaclinica->txtBufer!==$txtBufer) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'BUFFER';
                $bita->valoranterior = $historiaclinica->txtBufer;
                $bita->valornuevo = $txtBufer;
                $bita->save();
            }
            $historiaclinica->txtBufer = $txtBufer;
            if($historiaclinica->txtPerfilNa!==$txtPerfilNa) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PERFIL DE NA';
                $bita->valoranterior = $historiaclinica->txtPerfilNa;
                $bita->valornuevo = $txtPerfilNa;
                $bita->save();
            }
            $historiaclinica->txtPerfilNa = $txtPerfilNa;
            if($historiaclinica->txtMedicacion!==$txtMedicacion) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MEDICACIÓN';
                $bita->valoranterior = $historiaclinica->txtMedicacion;
                $bita->valornuevo = $txtMedicacion;
                $bita->save();
            }
            $historiaclinica->txtMedicacion = $txtMedicacion;
            if($historiaclinica->txtUltrafiltrado!==$txtUltrafiltrado) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO A PROGRAMAR';
                $bita->valoranterior = $historiaclinica->txtUltrafiltrado;
                $bita->valornuevo = $txtUltrafiltrado;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltrado = $txtUltrafiltrado;
            if($historiaclinica->txtConductividad!==$txtConductividad) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'CONDUCTIVIDAD';
                $bita->valoranterior = $historiaclinica->txtConductividad;
                $bita->valornuevo = $txtConductividad;
                $bita->save();
            }
            $historiaclinica->txtConductividad = $txtConductividad;
            if($historiaclinica->txtAreaDializador!==$txtAreaDializador) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ÁREA DE DIALIZADOR';
                $bita->valoranterior = $historiaclinica->txtAreaDializador;
                $bita->valornuevo = $txtAreaDializador;
                $bita->save();
            }
            $historiaclinica->txtAreaDializador = $txtAreaDializador;
            if($historiaclinica->txtMembranaDializador!==$txtMembranaDializador) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MEMBRANA DE DIALIZADOR';
                $bita->valoranterior = $historiaclinica->txtMembranaDializador;
                $bita->valornuevo = $txtMembranaDializador;
                $bita->save();
            }
            $historiaclinica->txtMembranaDializador = $txtMembranaDializador;
            if($historiaclinica->txtCondicionClinicaFinal!==$txtCondicionClinicaFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'CONDICIÓN CLÍNICA DEL PACIENTE AL FINALIZAR HEMODIÁLISIS';
                $bita->valoranterior = $historiaclinica->txtCondicionClinicaFinal;
                $bita->valornuevo = $txtCondicionClinicaFinal;
                $bita->save();
            }
            $historiaclinica->txtCondicionClinicaFinal = $txtCondicionClinicaFinal;
            if($historiaclinica->txtPAInicial!==$txtPAInicial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PA INICIAL';
                $bita->valoranterior = $historiaclinica->txtPAInicial;
                $bita->valornuevo = $txtPAInicial;
                $bita->save();
            }
            $historiaclinica->txtPAInicial = $txtPAInicial;
            if($historiaclinica->txtNPuesto!==$txtNPuesto) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'N° PUESTO';
                $bita->valoranterior = $historiaclinica->txtNPuesto;
                $bita->valornuevo = $txtNPuesto;
                $bita->save();
            }
            $historiaclinica->txtNPuesto = $txtNPuesto;
            if($historiaclinica->txtPesoInicial2!==$txtPesoInicial2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO INICIAL';
                $bita->valoranterior = $historiaclinica->txtPesoInicial2;
                $bita->valornuevo = $txtPesoInicial2;
                $bita->save();
            }
            $historiaclinica->txtPesoInicial2 = $txtPesoInicial2;
            if($historiaclinica->txtMarcaModeloMaquina!==$txtMarcaModeloMaquina) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MARCA DE MÁQUINA';
                $bita->valoranterior = $historiaclinica->txtMarcaModeloMaquina;
                $bita->valornuevo = $txtMarcaModeloMaquina;
                $bita->save();
            }
            $historiaclinica->txtMarcaModeloMaquina = $txtMarcaModeloMaquina;
            if($historiaclinica->txtMarcaModeloMaquina2!==$txtMarcaModeloMaquina2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MODELO DE MÁQUINA';
                $bita->valoranterior = $historiaclinica->txtMarcaModeloMaquina2;
                $bita->valornuevo = $txtMarcaModeloMaquina2;
                $bita->save();
            }
            $historiaclinica->txtMarcaModeloMaquina2 = $txtMarcaModeloMaquina2;
            if($historiaclinica->txtUltrafiltadoProgramado!==$txtUltrafiltadoProgramado) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO PROGRAMADO';
                $bita->valoranterior = $historiaclinica->txtUltrafiltadoProgramado;
                $bita->valornuevo = $txtUltrafiltadoProgramado;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltadoProgramado = $txtUltrafiltadoProgramado;
            if($historiaclinica->txtUltrafiltadoProgramado2!==$txtUltrafiltadoProgramado2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO PROGRAMADO';
                $bita->valoranterior = $historiaclinica->txtUltrafiltadoProgramado2;
                $bita->valornuevo = $txtUltrafiltadoProgramado2;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltadoProgramado2 = $txtUltrafiltadoProgramado2;
            if($historiaclinica->txtUltrafiltadoProgramado3!==$txtUltrafiltadoProgramado3) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ULTRAFILTRADO PROGRAMADO';
                $bita->valoranterior = $historiaclinica->txtUltrafiltadoProgramado3;
                $bita->valornuevo = $txtUltrafiltadoProgramado3;
                $bita->save();
            }
            $historiaclinica->txtUltrafiltadoProgramado3 = $txtUltrafiltadoProgramado3;
            if($historiaclinica->txtLoteSerieFiltro!==$txtLoteSerieFiltro) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'LOTE DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtLoteSerieFiltro;
                $bita->valornuevo = $txtLoteSerieFiltro;
                $bita->save();
            }
            $historiaclinica->txtLoteSerieFiltro = $txtLoteSerieFiltro;
            if($historiaclinica->txtLoteSerieFiltro2!==$txtLoteSerieFiltro2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'SERIE DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtLoteSerieFiltro2;
                $bita->valornuevo = $txtLoteSerieFiltro2;
                $bita->save();
            }
            $historiaclinica->txtLoteSerieFiltro2 = $txtLoteSerieFiltro2;
            if($historiaclinica->txtAccesoVascularArterial!==$txtAccesoVascularArterial) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ACCESO VASCULAR ARTERIAL';
                $bita->valoranterior = $historiaclinica->txtAccesoVascularArterial;
                $bita->valornuevo = $txtAccesoVascularArterial;
                $bita->save();
            }
            $historiaclinica->txtAccesoVascularArterial = $txtAccesoVascularArterial;
            if($historiaclinica->txtAccesoVascularVenoso!==$txtAccesoVascularVenoso) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ACCESO VASCULAR VENOSO';
                $bita->valoranterior = $historiaclinica->txtAccesoVascularVenoso;
                $bita->valornuevo = $txtAccesoVascularVenoso;
                $bita->save();
            }
            $historiaclinica->txtAccesoVascularVenoso = $txtAccesoVascularVenoso;
            if($historiaclinica->txtPAFinal!==$txtPAFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PA FINAL';
                $bita->valoranterior = $historiaclinica->txtPAFinal;
                $bita->valornuevo = $txtPAFinal;
                $bita->save();
            }
            $historiaclinica->txtPAFinal = $txtPAFinal;
            if($historiaclinica->txtNMAquina!==$txtNMAquina) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'N° DE MÁQUINA';
                $bita->valoranterior = $historiaclinica->txtNMAquina;
                $bita->valornuevo = $txtNMAquina;
                $bita->save();
            }
            $historiaclinica->txtNMAquina = $txtNMAquina;
            if($historiaclinica->txtPesoFinal2!==$txtPesoFinal2) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'PESO FINAL';
                $bita->valoranterior = $historiaclinica->txtPesoFinal2;
                $bita->valornuevo = $txtPesoFinal2;
                $bita->save();
            }
            $historiaclinica->txtPesoFinal2 = $txtPesoFinal2;
            if($historiaclinica->txtAreaMembranaFiltro!==$txtAreaMembranaFiltro) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ÁREA/MEMBRANA DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtAreaMembranaFiltro;
                $bita->valornuevo = $txtAreaMembranaFiltro;
                $bita->save();
            }
            $historiaclinica->txtAreaMembranaFiltro = $txtAreaMembranaFiltro;
            if($historiaclinica->txtValoracionEnfermeria!==$txtValoracionEnfermeria) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'VALORACIÓN DE ENFERMERÍA';
                $bita->valoranterior = $historiaclinica->txtValoracionEnfermeria;
                $bita->valornuevo = $txtValoracionEnfermeria;
                $bita->save();
            }
            $historiaclinica->txtValoracionEnfermeria = $txtValoracionEnfermeria;
            if($historiaclinica->txtEvalHemodialisis!==$txtEvalHemodialisis) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'EVALUACIÓN DE HEMODIÁLISIS';
                $bita->valoranterior = $historiaclinica->txtEvalHemodialisis;
                $bita->valornuevo = $txtEvalHemodialisis;
                $bita->save();
            }
            $historiaclinica->txtEvalHemodialisis = $txtEvalHemodialisis;
            if($historiaclinica->txtObservacionFinal!==$txtObservacionFinal) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'OBSERVACIÓN FINAL';
                $bita->valoranterior = $historiaclinica->txtObservacionFinal;
                $bita->valornuevo = $txtObservacionFinal;
                $bita->save();
            }
            $historiaclinica->txtObservacionFinal = $txtObservacionFinal;
            if($historiaclinica->txtAspectoFiltro!==$txtAspectoFiltro) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ASPECTO DE FILTRO';
                $bita->valoranterior = $historiaclinica->txtAspectoFiltro;
                $bita->valornuevo = $txtAspectoFiltro;
                $bita->save();
            }
            $historiaclinica->txtAspectoFiltro = $txtAspectoFiltro;
            if($historiaclinica->txtAdmiMedic!==$txtAdmiMedic) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'ADMINISTRACIÓN DE MEDICAMENTOS ENDOVENOSOS';
                $bita->valoranterior = $historiaclinica->txtAdmiMedic;
                $bita->valornuevo = $txtAdmiMedic;
                $bita->save();
            }
            $historiaclinica->txtAdmiMedic = $txtAdmiMedic;
            $historiaclinica->estado = 'A';
            $historiaclinica->txtHoraEvaluacionPrevia = $txtHoraEvaluacionPrevia;
            if($historiaclinica->txtMuestraAnalisis!==$txtMuestraAnalisis) {
                $bita = new BitacoraTratamiento();
                $bita->numero = BitacoraTratamiento::NumeroSigue();
                $bita->person_id = $user->id;
                $bita->historiaclinica_id = $historiaclinica->id;
                $bita->campo = 'MUESTRA DE ANÁLISIS';
                $bita->valoranterior = $historiaclinica->txtMuestraAnalisis;
                $bita->valornuevo = $txtMuestraAnalisis;
                $bita->save();
            }
            $historiaclinica->txtMuestraAnalisis = $txtMuestraAnalisis;
            if($request->input("id_hc")!==NULL&&$request->input("id_hc")!=="") {
                if(date("Y-m-d")==date("Y-m-d", strtotime("Y-m-d", strtotime($historiaclinica->fecha_atencion)))) {
                    $historiaclinica->fecha_atencion = date('Y-m-d H:i:s');
                }
            } else {
                $historiaclinica->fecha_atencion = date('Y-m-d H:i:s');
            }
            $historiaclinica->ip = null;
            $historiaclinica->save();

            //Indicamos que la historia ya se registro
            //$history = Historia::find($historia_id);
            //$history->estado2 = 'F';
            //$history->save();

            //Eliminar Bitácoras Nulas

            $bitacoras = BitacoraTratamiento::where('historiaclinica_id', '=', $historiaclinica->id)->where('valoranterior', '=', '')->whereNull('valornuevo')->get();

            foreach ($bitacoras as $bita) {
                $bita->delete();
            }

        });

        return is_null($error) ? "OK" : $error;
    }

    public function inicializarTablaCies(Request $request) {
        $cies = $request->input('cies');
        $cies2 = explode(';', $cies);
        $text = '';
        foreach ($cies2 as $c) {
            $cie = Cie::find($c);
            if($cie !== null) {
                $text .= '<tr align="center" data-id="'.$cie->id.'" id="'.$cie->id.'" ><td style="vertical-align: middle; text-align: left;">'.$cie->codigo.' - '.$cie->descripcion.'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
            }
        }
        return $text;
    }

    public function inicializarTablaCies2(Request $request) {
        $cies = $request->input('cies');
        $cies2 = explode(';', $cies);
        $text = '';
        foreach ($cies2 as $c) {
            $sa = explode(',', $c);
            $cie = Cie::find($sa[0]);
            if($cie !== null) {
                $text .= '<tr align="center" data-id="'.$cie->id.'" id="'.$cie->id.'" ><td style="vertical-align: middle; text-align: left;">'.$cie->codigo.' - '.$cie->descripcion.'</td>
                <td style="vertical-align: middle; text-align: left;"><select name="form-control" class="selectito" id="t'. $cie->id .'">
                    <option value="P"'.($sa[1]==='P'?' selected="selected"':'').'>P</option>
                    <option value="D"'.($sa[1]==='D'?' selected="selected"':'').'>D</option>
                    <option value="R"'.($sa[1]==='R'?' selected="selected"':'').'>R</option>
                </select></td>
                <td style="vertical-align: middle;"><a onclick="eliminarDetalleCie22(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
            }
        }
        return $text;
    }

    public function cargarMedicamentos(Request $request) {
        $text = '';
        $id=$request->input("id");
        $hc = HistoriaClinica::find($id);
        if($hc!==NULL) {
            $cadenamedicamentos = $hc->txtAdmiMedic;
            $cadenamedicamentos2 = explode('&iliu&', $cadenamedicamentos);
            if(count($cadenamedicamentos2)>0) {
                foreach ($cadenamedicamentos2 as $c) {
                    $sa = explode('&ilid&', $c);
                    $medicamento = Producto::find($sa[0]);
                    if($medicamento !== null) {
                        $text .= '<tr data-id="'.$medicamento->id.'" align="center" id="ee'.$medicamento->id.'"><td style="vertical-align: middle; text-align: left;">'.$medicamento->nombre.'</td><td style="vertical-align: middle; text-align: left;"><input type="text" value="'.(empty($sa[1])?"":$sa[1]).'" id="ttt'.$medicamento->id.'" class="form-control input-xs inputcito numerin"></td><td style="vertical-align: middle;"><a onclick="eliminarDetalleMedicamento(this,1)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div></a></td></tr>';
                    }
                }
            }
        }                        
        return $text;
    }

    public function verSeguimiento(Request $request)
    {
        $bitacora = BitacoraTratamiento::where('historiaclinica_id', '=', $request->input('id'))->orderBy('created_at', 'DESC')->get();
        $hc = HistoriaClinica::find($request->input('id'));

        $texto = '';

        if(count($bitacora) == 0) {
            $texto = '<h3>No se hizo movimiento sobre este Tratamiento.</h3>';
        } else {
            $texto = '<h4 style="color:blue;font-weight:bold;">Bitácota del tratamiento de ' . $hc->historia->persona->apellidopaterno . ' ' . $hc->historia->persona->apellidomaterno . ' ' . $hc->historia->persona->nombres . '</h4><br>';
            $texto .= '<table width="100%" style="font-size:11px;">';
            $texto .= '<thead style="font-weight:bold;background-color:yellow;"><tr>
                        <td width="2%">#</td>
                        <td width="18%">CAMPO</td>
                        <td width="28%">VALOR ANTERIOR</td>
                        <td width="28%">VALOR NUEVO</td>
                        <td width="9%">HORA Y FECHA</td>
                        <td width="15%">RESPONSABLE</td>
                    </tr></thead><tbody>';
            $i = 1;
            foreach ($bitacora as $bit) {

                $md1 = $bit->valoranterior;
                $md2 = $bit->valornuevo;

                if($bit->campo == 'EVALUACIÓN DE HEMODIÁLISIS' || $bit->campo == 'ADMINISTRACIÓN DE MEDICAMENTOS ENDOVENOSOS') {
                    $md1 = '';
                    $md2 = '';
                    $medicamentos1 = explode('&iliu&', $bit->valoranterior);
                    if(count($medicamentos1)>0) {
                        foreach ($medicamentos1 as $value1) {
                            $vacio = '';
                            $palabra = '';
                            $aaa = 0;
                            $medicamentos2 = explode('&ilid&', $value1);
                            foreach ($medicamentos2 as $value2) {
                                if($bit->campo == 'ADMINISTRACIÓN DE MEDICAMENTOS ENDOVENOSOS' && $aaa == 0) {
                                    $prod = Producto::find($value2);
                                    if($prod!==NULL) {
                                        $value2 = $prod->nombre;
                                    }                                    
                                }
                                $palabra.=$value2.' | ';
                                $vacio.=$value2;
                                $aaa++;
                            }
                            $palabra.="<br>";
                            if($vacio !== '') {
                                $md1.=$palabra;
                            }
                        }
                    }  
                    $medicamentos1 = explode('&iliu&', $bit->valornuevo);
                    if(count($medicamentos1)>0) {
                        foreach ($medicamentos1 as $value1) {
                            $vacio = '';
                            $palabra = '';
                            $aaa = 0;
                            $medicamentos2 = explode('&ilid&', $value1);
                            foreach ($medicamentos2 as $value2) {
                                if($bit->campo == 'ADMINISTRACIÓN DE MEDICAMENTOS ENDOVENOSOS' && $aaa == 0) {
                                    $prod = Producto::find($value2);
                                    if($prod!==NULL) {
                                        $value2 = $prod->nombre;
                                    }                                    
                                }
                                $palabra.=$value2.' | ';
                                $vacio.=$value2;
                                $aaa++;
                            }
                            $palabra.="<br>";
                            if($vacio !== '') {
                                $md2.=$palabra;
                            }
                        }
                    }  
                } elseif ($bit->campo == 'CIES') {
                    $md1 = '';
                    $md2 = '';
                    $av1 = explode(';', $bit->valoranterior);
                    if(count($av1)>0) {
                        foreach ($av1 as $value1) {
                            if($value1!=='') {
                                $cie1 = Cie::find($value1);
                                $md1.=$cie1->codigo.': '.$cie1->descripcion."<br>";
                            }                                
                        }
                    }
                    $av2 = explode(';', $bit->valornuevo);
                    if(count($av2)>0) {
                        foreach ($av2 as $value1) {
                            if($value1!=='') {
                                $cie1 = Cie::find($value1);
                                $md2.=$cie1->codigo.': '.$cie1->descripcion."<br>";
                            }
                        }
                    }
                } elseif ($bit->campo == 'ACCESO VASCULAR ARTERIAL'||$bit->campo == 'ACCESO VASCULAR VENOSO') {
                    $md1 = '';
                    $md2 = '';
                    if($bit->valoranterior=='1') {
                        $md1.='FAV';
                    } elseif ($bit->valoranterior=='2') {
                        $md1.='Autoinjerto';
                    } elseif ($bit->valoranterior=='3') {
                        $md1.='Injerto';
                    } elseif ($bit->valoranterior=='4') {
                        $md1.='CVCP';
                    } elseif ($bit->valoranterior=='5') {
                        $md1.='CVCT';
                    } elseif ($bit->valoranterior=='6') {
                        $md1.='Cperitoneal';
                    }

                    if($bit->valornuevo=='1') {
                        $md2.='FAV';
                    } elseif ($bit->valornuevo=='2') {
                        $md2.='Autoinjerto';
                    } elseif ($bit->valornuevo=='3') {
                        $md2.='Injerto';
                    } elseif ($bit->valornuevo=='4') {
                        $md2.='CVCP';
                    } elseif ($bit->valornuevo=='5') {
                        $md2.='CVCT';
                    } elseif ($bit->valornuevo=='6') {
                        $md2.='Cperitoneal';
                    }
                }

                $texto .= '
                    <tr>
                        <td>' . $i . '</td>
                        <td>' . $bit->campo . '</td>
                        <td>' . (($md1==''||$md1==null) ? '<font color="red">** VACÍO **</font>' : $md1) . '</td>
                        <td>' . (($md2==''||$md2==null) ? '<font color="red">** VACÍO **</font>' : $md2) . '</td>
                        <td>' . date('d-m-Y H:i', strtotime($bit->created_at)) . '</td>
                        <td>' . $bit->person->apellidopaterno . ' ' . $bit->person->nombres . '</td>
                    </tr>';
                $i++;
            }
            $texto .= '</tbody></table>';
        }       

        return $texto;
    }

    public function reporte(Request $request)
    {
        $user = Auth::user();
        $doctor = Person::find($user->person_id);
        $prestacion = (!empty($request->input("prestacion"))?1:2);
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $id = $request->input('hid');
        $entidad  = 'HC';
        $tip = 0;
        $mens = 2;
        $codigoano = "";
        $ciesreporteultimo = "";
        if($request->input('mensual')!==''&&$request->input('mensual')!==NULL) {
            $formato = $request->input('formato');
            if($formato == '1') {
                $hc = ConsultaSaludMental::find($id);
            } else if($formato == '2') {
                $hc = ConsultaNefrologica::find($id);
            } else if($formato == '3') {
                $hc = ConsultaServicioSocial::find($id);
            } else {
                $hc = ConsultaNutricion::find($id);
            }            
            $historia = Historia::where('person_id', '=', $hc->persona_id)->first();
            $paciente = Person::find($hc->persona_id);   
            $mens = 1;
            $tip = $formato;   
            $codigoano = ($hc->fecha_atencion==NULL?$hc->fechaformato:$hc->fecha_atencion);
            
        } else {
            $hc = HistoriaClinica::find($id);
            $historia = Historia::find($hc->historia_id);
            $paciente = Person::find($historia->person_id);
            $codigoano = $hc->fecha_atencion;
        }
        $formData = array('class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Guardar'; 
        return view($this->folderview.'.reporte')->with(compact('tip', 'mens', 'id', 'hc', 'doctor', 'historia', 'paciente', 'formData', 'entidad', 'boton', 'listar', "prestacion", 'codigoano'));
    }

    public function registrarFormato(Request $request) 
    {
        $error = DB::transaction(function() use($request){
            $user = Auth::user();
            $formato = $request->input('formatotipo');
            $id = $request->input('idformato');
            if($request->input('formatomensual')==='2') {
                $hc = HistoriaClinica::find($id);
                $hc->mensuales2 = $request->input("mensuales2");
                if($request->input("mensuales2") !== "2") {
                    $hc->mensuales = $request->input("mensuales");
                }
                
            } else if($request->input('formatomensual')==='1') {
                if($formato == '1') {
                    $hc = ConsultaSaludMental::find($id);
                } else if($formato == '2') {
                    $hc = ConsultaNefrologica::find($id);
                } else if($formato == '3') {
                    $hc = ConsultaServicioSocial::find($id);
                } else if($formato == '4') {
                    $hc = ConsultaNutricion::find($id);
                }
            }

            if($request->input('formatomensual')==='1') {
                $hc->fechaformato = $request->input("fechaformato");                
            }

            if($request->input('formatomensual') == 2 && $request->input('formatotipo') == 0) {
                $hc->fecha_atencion = date('Y-m-d', strtotime($hc->fecha_atencion)) . " " . date('H:i:s', strtotime($request->input("horaformato")));
            }

            $hc->numeroformato = $request->input('numeroformato');
            $hc->doctor_id = $request->input('medico_id');
            if($request->input('formatomensual')==='2') {
                $hc->txtAdmiMedic = Libreria::getParam($request->input("cadenamedicamentos"),'');
            }            
            $hc->td = $request->input('td');
            $hc->numeroconsideracion = $request->input('txtReconsideracion');
            //$hc->fecha_a = date('Y-m-d');
            $hc->prestacionformato = $request->input('prestacionformato');
            $hc->observacionformato1 = $request->input('observacionformato1');
            $hc->observacionformato2 = $request->input('observacionformato2');
            $hc->observacionformato3 = $request->input('observacionformato3');
            $hc->observacionformato4 = $request->input('observacionformato4');
            $hc->ciesformato = $request->input('cadenacies22');
            $hc->responsableformato_id = $user->person_id;
            $hc->save();
        });

        return is_null($error) ? "OK" : $error;            
    }

    public function nomMedicamento(Request $request) {
        $id = $request->input('id');
        $medicamento = Producto::find($id);
        if($medicamento !== NULL&& $id !== '') {
            echo json_encode($medicamento->nombre);
        } else {
            echo json_encode("");
        }
    }

    public function cerradoEspecial(Request $request) {
        date_default_timezone_set('America/Lima');
        $hc = HistoriaClinica::where("historia_id", "=", $request->input("hid"))->where("estado", "!=", "C")->orderBy("id", "DESC")->first();
        $historia = Historia::find($request->input("hid"));
        $user_id = Auth::user()->id;
        if($hc!=NULL) {
            //Cancelo la hc
            $hc->estado = "C";
            $hc->ip = NULL;
            $hc->save();

            //Creo nueva hc
            $user = Person::find(Session::get('person_id'));
            $hcn = new HistoriaClinica();
            $hcn->historia_id = $request->input("hid");
            $hcn->fecha_atencion = date("Y-m-d H:i:s");
            $hcn->numero = HistoriaClinica::numeroSigue();
            $hcn->user_id = $user->id;
            $hcn->turno = $historia->horacita;
            $hcn->tipo="V";
            $hcn->estado="P";
            $hcn->save();
        }
    }

    public function comprobarMedico(Request $request) {
        $id = $request->input("id");
        $historiaclinica = HistoriaClinica::find($id);
        $idd = "";
        $nombree = "";
        if ($historiaclinica!==NULL) {
            if ($historiaclinica->doctor!==NULL) {
                $idd = $historiaclinica->doctor->id;
                $nombree = $historiaclinica->doctor->apellidopaterno." ".$historiaclinica->doctor->apellidomaterno." ".$historiaclinica->doctor->nombres;
            }            
        }

        $datos = array(
            "idmedico" => $idd,
            "nombremedico" => $nombree,
        ); 

        return json_encode($datos);
    }

    public function pruebita(Request $request) {
        //$txtAdmiMedic;
                //ARMAMOS EL TXTADMIMEDIC SI ES QUE SE HA PROGRAMADO PESH
                $historiamensual = ConsultaNefrologica::where("persona_id", "=", 50)
                                ->where(DB::raw("MONTH(fecha)"), "=", "09")
                                ->where(DB::raw("YEAR(fecha)"), "=", "2019")
                                ->first();
                $diahemodialisis = "07";
                $historiaclinica = HistoriaClinica::find(2673);
                $cantepo = "";
                $canthierro = "";
                $cantvita = "";
                //SOLO SI EXISTE Y SE HA PROGRAMADO
                if($historiamensual !== NULL) {
                    if($historiamensual->cadenaepo!==NULL&&$historiamensual->cadenaepo!=="") {
                        $cadenaepo = explode("**", $historiamensual->cadenaepo);
                        foreach ($cadenaepo as $epo) {
                            $diap = explode(";", $epo);
                            if($diap[0]==$diahemodialisis) {
                                $cantepo = $diap[1];
                                break;
                            }
                        }
                    }
                    if($historiamensual->cadenahierro!==NULL&&$historiamensual->cadenahierro!=="") {
                        $cadenahierro = explode("**", $historiamensual->cadenahierro);
                        foreach ($cadenahierro as $hierro) {
                            $diap = explode(";", $hierro);
                            if($diap[0]==$diahemodialisis) {
                                $canthierro = $diap[1];
                                break;
                            }
                        }
                    }
                    if($historiamensual->cadenavita!==NULL&&$historiamensual->cadenavita!=="") {
                        $cadenavita = explode("**", $historiamensual->cadenavita);
                        foreach ($cadenavita as $vita) {
                            $diap = explode(";", $vita);
                            if($diap[0]==$diahemodialisis) {
                                $cantvita = $diap[1];
                                break;
                            }
                        }
                    }
                }
                //ARMANDO TXTADMIMEDICA PARA SETEAR
                $txtAdmiMedic = "1&ilid&" . $cantepo . "&iliu&2&ilid&" . $canthierro . "&iliu&3&ilid&" . $cantvita . "&iliu&&ilid&&iliu&&ilid&&iliu&&ilid&";
                $seteohistoriaclinica = HistoriaClinica::find(2673);
                $seteohistoriaclinica->txtAdmiMedic = $txtAdmiMedic;
                $seteohistoriaclinica->save();

                echo $txtAdmiMedic;
    }
}
