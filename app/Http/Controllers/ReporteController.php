<?php

namespace App\Http\Controllers;

use App\Baja;
use App\Cie;
use App\ConsultaNefrologica;
use App\Historia;
use App\HistoriaClinica;
use App\Http\Controllers\Controller;
use App\Kardex;
use App\Movimiento;
use App\Person;
use App\Producto;
use App\Turno;
use App\User;
use DateTime;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{

    protected $folderview = 'app.reporte';
    protected $meses      = array("1" => "ENERO", "2" => "FEBRERO", "3" => "MARZO", "4" => "ABRIL", "5" => "MAYO", "6" => "JUNIO", "7" => "JULIO", "8" => "AGOSTO", "9" => "SETIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE");
    protected $dias       = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
    protected $anoos      = array("2019" => "2019", "2020" => "2020", "2021" => "2021", "2022" => "2022", "2023" => "2023", "2024" => "2024", "2025" => "2025", "2026" => "2026", "2027" => "2027", "2028" => "2028", "2029" => "2029", "2030" => "2030", "2031" => "2031", "2032" => "2032", "2033" => "2033", "2034" => "2034", "2035" => "2035", "2036" => "2036", "2037" => "2037", "2038" => "2038", "2039" => "2039", "2040" => "2040", "2041" => "2041", "2042" => "2042", "2043" => "2043", "2045" => "2045", "2046" => "2046", "2047" => "2047", "2048" => "2048", "2049" => "2049", "2050" => "2050");

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pacientesnuevos()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Pacientes Nuevos";
        $meses   = $this->meses;
        $anoos   = $this->anoos;
        return view($this->folderview . '.pacientesnuevos')->with(compact('entidad', 'title', 'meses', 'anoos'));
    }

    public function resultadosmensuales()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Resultados Mensuales";
        $meses   = $this->meses;
        $anoos   = $this->anoos;
        return view($this->folderview . '.resultadosmensuales')->with(compact('entidad', 'title', 'meses', 'anoos'));
    }

    public function hospitalizadosmensuales()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Registro mensual de Pacientes Hospitalizados";
        $meses   = $this->meses;
        $anoos   = $this->anoos;
        return view($this->folderview . '.hospitalizadosmensuales')->with(compact('entidad', 'title', 'meses', 'anoos'));
    }

    public function egresosmensuales()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Registro mensual de Egresos de Pacientes";
        $meses   = $this->meses;
        $anoos   = $this->anoos;
        return view($this->folderview . '.egresosmensuales')->with(compact('entidad', 'title', 'meses', 'anoos'));
    }

    public function stockactual()
    {
        date_default_timezone_set('America/Lima');
        $entidad   = 'Reporte';
        $title     = "Reporte de Stock y Kardex";
        $meses     = $this->meses;
        $anoos     = $this->anoos;
        $productos = Producto::select('nombre', 'producto.id')
            ->orderBy('nombre')
            ->get();
        $hoy = date("Y-m-d");
        return view($this->folderview . '.stockactual')->with(compact('entidad', 'title', 'meses', 'anoos', 'productos', 'hoy'));
    }

    public function comprasalmacen()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Compras de Almacén";
        $hoy     = date("Y-m-d");
        return view($this->folderview . '.comprasalmacen')->with(compact('entidad', 'title', 'hoy'));
    }

    public function programacionesdiariashd()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Programaciones Diarias de Hemodiálisis";
        $hoy     = date("Y-m-d");
        return view($this->folderview . '.programacionesdiariashd')->with(compact('entidad', 'title', 'hoy'));
    }

    public function registroaccesovascular()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Registro de Accesos Vasculares";
        $meses   = $this->meses;
        $anoos   = $this->anoos;
        return view($this->folderview . '.registroaccesovascular')->with(compact('entidad', 'title', 'meses', 'anoos'));
    }

    public function controldescartefiltro()
    {
        date_default_timezone_set('America/Lima');
        $entidad = 'Reporte';
        $title   = "Control de Descarte de Filtro";
        $meses   = $this->meses;
        $anoos   = $this->anoos;
        return view($this->folderview . '.controldescartefiltro')->with(compact('entidad', 'title', 'meses', 'anoos'));
    }

    public function show($id)
    {
        //
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function analisisMensualesKtvTru(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 1)
            ->where('historia.baja', '!=', "S")
            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
            ->orderBy(DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, ' ', person.nombres)"));

        $lista = $resultado->get();

        Excel::create("Res" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("Res" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array('A' => 5, 'B' => 40, 'C' => 10, 'D' => 10, 'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10, 'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10, 'S' => 10, 'T' => 10, 'U' => 10, 'V' => 10, 'W' => 10, 'X' => 10, 'Y' => 10, 'Z' => 10, 'AA' => 10, 'AB' => 10, 'AC' => 10, 'AD' => 10, 'AE' => 10, 'AF' => 10, 'AG' => 10, 'AH' => 10, 'AI' => 10));

                $celdas = 'B2:AI2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:AI4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                    $cells->setValignment('center');
                });

                $sheet->setBorder('B4:AI4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);
                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "ANÁLISIS SIS MENSUALES MES DE " . $mesnombre . " DEL " . $anoo . " - KTV Y TRU";
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "NOMBRES";
                $cabecera[] = "Hb (g/dl)";
                $cabecera[] = "Hto (%)";
                $cabecera[] = "Urea post (mg/dl)";
                $cabecera[] = "Urea pre (mg/dl)";
                $cabecera[] = "Crea-Pre (mg/dl)";
                $cabecera[] = "Sodio (mmol/L)";
                $cabecera[] = "Potasio (mmol/L)";
                $cabecera[] = "Cloro (mmol/L)";
                $cabecera[] = "Ca (mg/dl)";
                $cabecera[] = "P (mg/dl)";
                $cabecera[] = "TGO";
                $cabecera[] = "TGP";
                $cabecera[] = "FAL (U/L)";
                $cabecera[] = "PCR";
                $cabecera[] = "Pro T (g/dl)";
                $cabecera[] = "Albu (g/dl)";
                $cabecera[] = "Glob (g/dl)";
                $cabecera[] = "Hierro (ug/dl)";
                $cabecera[] = "Transferrina";
                $cabecera[] = "% de Saturación de transferrina";
                $cabecera[] = "Ferritina (ng/ml)";
                $cabecera[] = "Parathormona (pg/ml)";
                $cabecera[] = "HBsAg";
                $cabecera[] = "Ac HBsAg";
                $cabecera[] = "Core Toral";
                $cabecera[] = "HCV";
                $cabecera[] = "VIH";
                $cabecera[] = "VDRL";
                $cabecera[] = "PESO PRE";
                $cabecera[] = "PESO POST";
                $cabecera[] = "KTV";
                $cabecera[] = "TRU";
                $cabecera[] = "TIEMPO";
                $sheet->row(4, $cabecera);

                $c = 5;

                foreach ($lista as $row) {
                    //BUSCO LA ATENCION DE HEMODIALISIS EN LA QUE SE TOMARON MUESTRAS, LO BUSCO EN LA FUA DE LA CUAL SE TOMARON DATOS MENSUALES Y SACO TIEMPO, PESO PRE Y PESO POST
                    $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                        ->where("historia.person_id", "=", $row->persona_id)
                        ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $mes)
                        ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anoo)
                        ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
                        ->where("historiaclinica.estado", "!=", "C")
                        //->where("mensuales2", "=", 1)
                        ->first();
                    $time = 0;
                    $ppre = 0;
                    $ppos = 0;
                    if ($atencion !== null) {
                        $time = $atencion->txtHorasHemodialisis;
                        $ppre = $atencion->txtPesoInicial2;
                        $ppos = $atencion->txtPesoFinal2;
                    }
                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[] = "";
                    $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                    $cabecera[] = $row->txtDos;
                    $cabecera[] = $row->txtHem;
                    $cabecera[] = $row->txtUre2;
                    $cabecera[] = $row->txtUre;
                    $cabecera[] = $row->txtCre;
                    $cabecera[] = $row->txtSodio;
                    $cabecera[] = $row->txtPotasio;
                    $cabecera[] = $row->txtCloro;
                    $cabecera[] = $row->txtCal;
                    $cabecera[] = $row->txtFos;
                    $cabecera[] = $row->txtTgo;
                    $cabecera[] = $row->txtTgp;
                    $cabecera[] = $row->txtFos2;
                    $cabecera[] = $row->txtPcr;
                    $cabecera[] = $row->txtPro;
                    $cabecera[] = $row->txtAlbu;
                    $cabecera[] = $row->txtGlobu;

                    $cabecera[] = $row->txtHie;
                    $cabecera[] = $row->txtTransfe;
                    $cabecera[] = $row->txtSat;
                    $cabecera[] = $row->txtFer;
                    $cabecera[] = $row->txtPar;
                    $cabecera[] = $row->txtDet;
                    $cabecera[] = $row->txtDet2;
                    $cabecera[] = $row->txtDet3;
                    $cabecera[] = "";
                    $cabecera[] = $row->txtEli;

                    $cabecera[] = $row->txtPru;

                    //peso pre y post
                    $cabecera[] = $ppre == 0 ? "" : $ppre;
                    $cabecera[] = $ppos == 0 ? "" : $ppos;

                    //ARMO VALOR DE KTV
                    $ktv = "";
                    $tru = "";
                    if ($row->txtUre !== null && $row->txtUre !== "" && $ppos !== null && $ppos !== "" && $time != 0) {
                        $ktv = '=(-LN((E' . $c . '/F' . $c . ')-(0.008*AI' . $c . ')))+(4-((3.5*E' . $c . ')/F' . $c . '))*((AE' . $c . '-AF' . $c . ')/AF' . $c . '))';
                        //$ktv = -log(($row->txtUre/$row->txtUre2)-(0.008*$time))+(4-(3.5*$row->txtUre/$row->txtUre2))*(($ppre-$ppos)/$ppos);
                    }

                    //ARMO VALOR DE TRU
                    if ($row->txtUre !== null && $row->txtUre !== "") {
                        $tru = '=100-(E' . $c . '*100/F' . $c . ')';
                    }

                    //$cabecera[] = $ktv;

                    //$cabecera[] = $tru;

                    $sheet->SetCellValue("AG" . $c, $ktv);
                    $sheet->SetCellValue("AH" . $c, $tru);
                    $sheet->SetCellValue("AI" . $c, $time == 0 ? "" : $time);

                    //$cabecera[] = $time == 0 ? "" : $time;
                    $sheet->row($c, $cabecera);
                    /*$sheet->cells("B".$c.":M".$c, function($cells) {
                    $cells->getStyle()->getNumberFormat()->setFormatCode('0.00');
                    });*/
                    for ($resa = 5; $resa < $c; $resa++) {
                        $sheet->getStyle("AG" . $resa)->getNumberFormat()->setFormatCode('0.00');
                        $sheet->getStyle("AH" . $resa)->getNumberFormat()->setFormatCode('0.00');
                        $sheet->getStyle("AE" . $resa)->getNumberFormat()->setFormatCode('0.00');
                        $sheet->getStyle("AF" . $resa)->getNumberFormat()->setFormatCode('0.00');
                    }

                    $c++;
                }
                $sheet->setBorder('B5:AI' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function registroMensualPacientesNuevos(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $tipo      = $request->input("tipo");
        $convenio  = 'SIS';
        if($tipo === '2') {
            $convenio = 'ESSALUD';
        }
        $mesnombre = $this->meses[$mes];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', $tipo)
            ->where(DB::raw("MONTH(fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(fecha)"), "=", $anoo)
            ->select("person.nombres", "person.apellidopaterno", "person.apellidomaterno", "dni", "fechanacimiento", "sexo", "direccion", "ipress", "horacita", "telefono", "telefono2", "historia.created_at")
            ->orderBy("historia.created_at");

        $lista = $resultado->get();

        Excel::create("Paci" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes, $convenio) {

            $excel->sheet("Paci" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes, $convenio) {

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 40,
                    'C' => 15,
                    'D' => 8,
                    'E' => 8,
                    'F' => 40,
                    'G' => 10,
                    'H' => 40,
                    'I' => 40,
                    'J' => 15,
                    'K' => 10,
                    'L' => 10,
                    'M' => 10,
                ));

                $celdas = 'B2:M2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:M4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("C", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("D", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("E", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("G", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("J", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("K", function ($cells) {$cells->setAlignment('center');});

                $sheet->setBorder('B4:M4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);

                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "REGISTRO MENSUAL DE PACIENTES NUEVOS CON DIAGNÓSTICO DE INSUFICIENCIA RENAL CRÓNICA -  " . $mesnombre . " DEL " . $anoo . ' (' . $convenio . ')';
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "APELLIDOS Y NOMBRES";
                $cabecera[] = "DNI/CE";
                $cabecera[] = "EDAD";
                $cabecera[] = "SEXO";
                $cabecera[] = "DIRECCIÓN";
                $cabecera[] = "TELÉFONO";
                $cabecera[] = "IPRESS PUBLICA DE REFERENCIA";
                $cabecera[] = "IPRESS PRIVADA DE PROCEDENCIA";
                $cabecera[] = "FECHA DE INGRESO";
                $cabecera[] = "TURNO";
                $cabecera[] = "SECUENCIA";
                $cabecera[] = "SEROLOGÍA";
                $sheet->row(4, $cabecera);

                $c            = 5;
                $edadpaciente = "-";

                foreach ($lista as $row) {
                    if ($row->fechanacimiento != '') {
                        $fechanacimiento = new DateTime($row->fechanacimiento);
                        $hoy             = new DateTime();
                        $annos           = $hoy->diff($fechanacimiento);
                        $edadpaciente    = $annos->y;
                    } else {
                        $edadpaciente = '-';
                    }
                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[] = "";
                    $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                    $cabecera[] = $row->dni;
                    $cabecera[] = $edadpaciente;
                    $cabecera[] = $row->sexo;
                    $cabecera[] = strtoupper($row->direccion);
                    $cabecera[] = $row->telefono;
                    $cabecera[] = strtoupper($row->ipress);
                    $cabecera[] = strtoupper($row->ipress);
                    $cabecera[] = date("d-m-Y", strtotime($row->created_at));
                    $cabecera[] = $row->turno->romano;
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('B5:M' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function registroMensualPacientesHospitalizados(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Baja::where(DB::raw("MONTH(fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(fecha)"), "=", $anoo)
            ->where("estado", "=", "H")
            ->orderBy("created_at");

        $lista = $resultado->get();

        Excel::create("Hospi" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("Hospi" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 40,
                    'C' => 10,
                    'D' => 8,
                    'E' => 8,
                    'F' => 40,
                    'G' => 15,
                    'H' => 40,
                    'I' => 15,
                ));

                $celdas = 'B2:I2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:I4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("C", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("D", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("E", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("G", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("I", function ($cells) {$cells->setAlignment('center');});

                $sheet->setBorder('B4:I4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);

                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "REGISTRO MENSUAL DE PACIENTES HOSPITALIZADOS CON DIAGNÓSTICO DE INSUFICIENCIA RENAL CRÓNICA -  " . $mesnombre . " DEL " . $anoo;
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "APELLIDOS Y NOMBRES";
                $cabecera[] = "DNI/CE";
                $cabecera[] = "EDAD";
                $cabecera[] = "SEXO";
                $cabecera[] = "IPRESS PÚBLICA DE HOSPITALIZACIÓN";
                $cabecera[] = "FECHA DE HOSPITALIZACIÓN";
                $cabecera[] = "MOTIVO DE HOSPITALIZACIÓN";
                $cabecera[] = "FECHA DE ALTA";
                $sheet->row(4, $cabecera);

                $c            = 5;
                $edadpaciente = "-";

                foreach ($lista as $row) {
                    //FECHA DE ALTA
                    $fecha_baja_alta = ($row->baja !== null ? date("d-m-Y", strtotime($row->baja->fecha)) : "");
                    if ($row->historia->persona->fechanacimiento != '') {
                        $fechanacimiento = new DateTime($row->historia->persona->fechanacimiento);
                        $hoy             = new DateTime();
                        $annos           = $hoy->diff($fechanacimiento);
                        $edadpaciente    = $annos->y;
                    } else {
                        $edadpaciente = '-';
                    }
                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[] = "";
                    $cabecera[] = $row->historia->persona->apellidopaterno . " " . $row->historia->persona->apellidomaterno . " " . $row->historia->persona->nombres;
                    $cabecera[] = $row->historia->persona->dni;
                    $cabecera[] = $edadpaciente;
                    $cabecera[] = $row->historia->persona->sexo;
                    $cabecera[] = strtoupper($row->ipresshospitalizacion);
                    $cabecera[] = date("d-m-Y", strtotime($row->created_at));
                    $cabecera[] = strtoupper($row->motivo);
                    $cabecera[] = $fecha_baja_alta;
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('B5:I' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function registroMensualEgresos(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Baja::where(DB::raw("MONTH(fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(fecha)"), "=", $anoo)
            ->where(function ($query) {
                $query->orWhere('estado', "=", "F")
                    ->orWhere('estado', "=", "O");
            })
            ->orderBy("created_at")
            ->distinct();

        $lista = $resultado->get();

        Excel::create("Egresos" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("Egresos" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 40,
                    'C' => 10,
                    'D' => 8,
                    'E' => 8,
                    'F' => 15,
                    'G' => 30,
                    'H' => 40,
                ));

                $celdas = 'B2:H2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:H4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("C", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("D", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("E", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("F", function ($cells) {$cells->setAlignment('center');});

                $sheet->setBorder('B4:H4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);

                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "REGISTRO MENSUAL DE EGRESOS DE PACIENTES CON DIAGNÓSTICO DE INSUFICIENCIA RENAL CRÓNICA -  " . $mesnombre . " DEL " . $anoo;
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "APELLIDOS Y NOMBRES";
                $cabecera[] = "DNI/CE";
                $cabecera[] = "EDAD";
                $cabecera[] = "SEXO";
                $cabecera[] = "FECHA DE EGRESO";
                $cabecera[] = "MOTIVO DE EGRESO (*)";
                $cabecera[] = "CAUSA DE FALLECIMIENTO (SOLO EN EGRESOS POR FALLECIMIENTO)";
                $sheet->row(4, $cabecera);

                $c            = 5;
                $edadpaciente = "-";

                foreach ($lista as $row) {
                    //FECHA DE ALTA
                    $fecha_baja_alta = ($row->baja !== null ? date("d-m-Y", strtotime($row->baja->fecha)) : "");
                    if ($row->historia->persona->fechanacimiento != '') {
                        $fechanacimiento = new DateTime($row->historia->persona->fechanacimiento);
                        $hoy             = new DateTime();
                        $annos           = $hoy->diff($fechanacimiento);
                        $edadpaciente    = $annos->y;
                    } else {
                        $edadpaciente = '-';
                    }
                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[] = "";
                    $cabecera[] = $row->historia->persona->apellidopaterno . " " . $row->historia->persona->apellidomaterno . " " . $row->historia->persona->nombres;
                    $cabecera[] = $row->historia->persona->dni;
                    $cabecera[] = $edadpaciente;
                    $cabecera[] = $row->historia->persona->sexo;
                    $cabecera[] = date("d-m-Y", strtotime($row->created_at));
                    $cabecera[] = strtoupper($row->motivo2);
                    $cabecera[] = ($row->motivo2 == "FALLECIMIENTO" ? strtoupper($row->motivo) : "");
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('B5:H' . ($c - 1), 'thin');

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "(*) MOTIVOS DE EGRESO:";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "FALLECIMIENTO";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "ABANDONO";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "TRASPLANTE RENAL";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "CAMBIO DE TERAPIA DE DIÁLISIS";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "SIS INACTIVO";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "TRASLADO A OTRA IPRESS";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });

                $c++;
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "OTROS";
                $sheet->row($c, $cabecera);

                $celdas = 'B' . $c . ':B' . $c;
                $sheet->cells($celdas, function ($cells) {
                    $cells->setFont(array(
                        'bold' => true,
                    ));
                });
            });
        })->export('xls');
    }

    public function consultarStock(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes         = (int) date("m");
        $anoo        = date("Y");
        $mesnombre   = $this->meses[$mes];
        $producto_id = $request->input("id");

        $lista = Producto::where("nombre", "LIKE", "%%");
        //$lista = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('movimiento.almacen_id', '=',1)->orderBy('kardex.id', 'DESC');

        if ($producto_id !== "" && $producto_id !== null) {
            $lista = $lista->where('id', '=', $producto_id);
        }

        $lista = $lista->get();

        Excel::create("Stock" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("Stock" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 60,
                    'C' => 15,
                ));

                $celdas = 'B2:C2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:C4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("C", function ($cells) {$cells->setAlignment('center');});

                $sheet->setBorder('B4:C4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);

                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "STOCK DE PRODUCTOS  " . $mesnombre . " DEL " . $anoo;
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "NOMBRE DE PRODUCTO";
                $cabecera[] = "CANTIDAD";
                $sheet->row(4, $cabecera);

                $c = 5;
                foreach ($lista as $row) {
                    $ultimokardex = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('movimiento.almacen_id', '=', 1)->orderBy('kardex.id', 'DESC')->where("producto_id", "=", $row->id)->first();
                    $stocks       = 0;
                    if ($ultimokardex !== null) {
                        $stocks = $ultimokardex->stockactual;
                    }
                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[] = "";
                    $cabecera[] = $row->nombre;
                    $cabecera[] = $stocks;
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('B5:C' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function consultarKardex(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $fechai      = date("Y-m-d", strtotime($request->input("fechai")));
        $fechaf      = date("Y-m-d", strtotime($request->input("fechaf")));
        $producto_id = $request->input("id");

        $lista = Producto::where("nombre", "LIKE", "%%");
        //$lista = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('movimiento.almacen_id', '=',1)->orderBy('kardex.id', 'DESC');

        if ($producto_id !== "" && $producto_id !== null) {
            $lista = $lista->where('id', '=', $producto_id);
        }

        $lista = $lista->get();

        Excel::create("Kardex" . $fechai . "/" . $fechaf, function ($excel) use ($lista, $request, $fechai, $fechaf) {
            $aa = 1;
            foreach ($lista as $producto) {
                $excel->sheet($aa . "-" . trim(substr($producto->nombre, 0, 13)), function ($sheet) use ($producto, $request, $fechai, $fechaf) {

                    $sheet->setWidth(array(
                        'A' => 5,
                        'B' => 15,
                        'C' => 60,
                        'D' => 13,
                        'E' => 13,
                        'F' => 13,
                        //'G' => 13,
                    ));

                    $celdas = 'B2:F2';
                    $sheet->mergeCells($celdas);
                    $sheet->cells($celdas, function ($cells) {
                        $cells->setAlignment('center');
                        //$cells->setBorder('thin','thin','thin','thin');
                        $cells->setFont(array(
                            'family' => 'Calibri',
                            'size'   => '15',
                            'bold'   => true,
                        ));
                    });

                    $celdas = 'B4:F4';
                    $sheet->cells($celdas, function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setFont(array(
                            'family' => 'Calibri',
                            'size'   => '11',
                            'bold'   => true,
                        ));
                        $cells->setBackground('#5CC2C4');
                    });

                    $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                    $sheet->cells("B", function ($cells) {$cells->setAlignment('center');});
                    //$sheet->cells("C", function($cells) { $cells->setAlignment('center'); });

                    $sheet->setBorder('B4:F4', 'thin');

                    $sheet->getRowDimension(4)->setRowHeight(40);

                    //$sheet->getColumnDimension('A')->setWidth(100);

                    $title   = array();
                    $title[] = "";
                    $title[] = strtoupper($producto->nombre);
                    $sheet->row(2, $title);

                    $cabecera = array();
                    //$cabecera[]="N°";
                    $cabecera[] = "";
                    $cabecera[] = "FECHA KARDEX";
                    //$cabecera[]="FECHA VENC.";
                    $cabecera[] = "DETALLE";
                    $cabecera[] = "ENTRADA";
                    $cabecera[] = "SALIDA";
                    $cabecera[] = "SALDO";
                    $sheet->row(4, $cabecera);

                    $c = 5;
                    /*$kardexs = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')
                    ->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')
                    ->join("lote", "lote.id", "=", "kardex.lote_id")
                    ->where('movimiento.almacen_id', '=',1)
                    ->where("detallemovimiento.producto_id", "=", $producto->id)
                    ->whereBetween('movimiento.fecha', [$fechai, $fechaf])
                    ->orderBy('kardex.id', 'ASC')
                    ->select("movimiento.fecha", "lote.fechavencimiento", "movimiento.comentario", "kardex.tipo", "kardex.cantidad", "kardex.stockanterior", "kardex.stockactual")
                    ->get();

                    if(count($kardexs)>0) {
                    foreach ($kardexs as $row) {
                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[]="";
                    $cabecera[]=date("d-m-Y", strtotime($row->fecha));
                    $cabecera[]=date("d-m-Y", strtotime($row->fechavencimiento));
                    $cabecera[]=$row->comentario;
                    $cabecera[]=($row->tipo=="I"?$row->cantidad:"");
                    $cabecera[]=($row->tipo!=="I"?$row->cantidad:"");
                    $cabecera[]=$row->stockactual;
                    $sheet->row($c,$cabecera);
                    $c++;
                    }
                    } else {
                    $cabecera = array();
                    $cabecera[]="";
                    $cabecera[]="NO SE HAN REGISTRADO MOVIMIENTOS DE ESTE PRODUCTO";
                    $sheet->row($c,$cabecera);
                    $sheet->mergeCells('B5:G5');
                    $c++;
                    }*/

                    //ARMAMOS ESTRUCTURA DE SOLO ENTRADAS Y SALIDAS

                    $kardexs = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')
                        ->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')
                        ->join("lote", "lote.id", "=", "kardex.lote_id")
                        ->where('movimiento.almacen_id', '=', 1)
                        ->where("detallemovimiento.producto_id", "=", $producto->id)
                        ->whereBetween('movimiento.fecha', [$fechai, $fechaf])
                        ->orderBy('kardex.id', 'ASC')
                        ->groupBy('movimiento.id', 'kardex.tipo', 'detallemovimiento.producto_id')
                        ->select("movimiento.fecha", "lote.fechavencimiento", "movimiento.comentario", "kardex.tipo", DB::raw("SUM(kardex.cantidad) as cant"), "kardex.stockanterior")
                        ->get();

                    if (count($kardexs) > 0) {
                        foreach ($kardexs as $row) {
                            $cabecera = array();
                            //$cabecera[]=($c-4);
                            $cabecera[] = "";
                            $cabecera[] = date("d-m-Y", strtotime($row->fecha));
                            //$cabecera[]=date("d-m-Y", strtotime($row->fechavencimiento));
                            $cabecera[] = $row->comentario;
                            $cabecera[] = ($row->tipo == "I" ? $row->cant : "");
                            $cabecera[] = ($row->tipo !== "I" ? $row->cant : "");
                            $cabecera[] = ($row->tipo == "I" ? $row->cant + $row->stockanterior : $row->stockanterior - $row->cant);
                            $sheet->row($c, $cabecera);
                            $c++;
                        }
                    } else {
                        $cabecera   = array();
                        $cabecera[] = "";
                        $cabecera[] = "NO SE HAN REGISTRADO MOVIMIENTOS DE ESTE PRODUCTO";
                        $sheet->row($c, $cabecera);
                        $sheet->mergeCells('B5:F5');
                        $c++;
                    }
                    $sheet->cells('F' . ($c - 1) . ':F' . ($c - 1), function ($cells) {
                        $cells->setFont(array(
                            'family' => 'Calibri',
                            'size'   => '13',
                            'bold'   => true,
                        ));
                        $cells->setBackground('#FFFF00');
                    });
                    $sheet->setBorder('B5:F' . ($c - 1), 'thin');
                });
                $aa++;
            }

        })->export('xls');
    }

    public function consolidadoEvaluacionesExternasMensuales(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes           = (int) $request->input("mes");
        $anoo          = $request->input("anno");
        $mesnombre     = $this->meses[$mes];
        $formato       = $request->input("formato");
        $nombreformato = "";

        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->whereIn('historia.convenio_id',[1,2])
            ->where('historia.baja', '!=', "S")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
            ->where("c.estadoatencion", "=", 1)
            ->orderBy(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'));

        switch ($formato) {
            case '2':
                $nombreformato = "SALUD MENTAL";
                $resultado     = $resultado->join("consultasaludmental as c", "c.persona_id", "=", "person.id")
                    ->select('person.nombres', 'person.apellidopaterno', 'person.apellidomaterno', 'historia.numero', 'historia.id as hid', 'person.dni', 'person.id as pid', "historia.baja", "c.txtDiagnostico as txtDiagnostico22", "c.txtIntervencion", "c.txtObservacion2");
                break;
            case '3':
                $nombreformato = "SERVICIO SOCIAL";
                $resultado     = $resultado->join("consultaserviciosocial as c", "c.persona_id", "=", "person.id")
                    ->select('person.nombres', 'person.apellidopaterno', 'person.apellidomaterno', 'historia.numero', 'historia.id as hid', 'person.dni', 'person.id as pid', "historia.baja", "c.txtDiagnostico2 as txtDiagnostico22", "c.txtIntervencion", "c.txtObservacion2");
                break;
            case '1':
                $nombreformato = "NUTRICIÓN";
                $resultado     = $resultado->join("consultanutricion as c", "c.persona_id", "=", "person.id")
                    ->select('person.nombres', 'person.apellidopaterno', 'person.apellidomaterno', 'historia.numero', 'historia.id as hid', 'person.dni', 'person.id as pid', "historia.baja", "c.txtDiagnostico2 as txtDiagnostico22", "c.txtIntervencion", "c.txtObservacion2");
                break;
        }

        $lista = $resultado->get();

        Excel::create("Consoli" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes, $nombreformato) {

            $excel->sheet("Consoli" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes, $nombreformato) {

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 40,
                    'C' => 10,
                    'D' => 10,
                    'E' => 45,
                    'F' => 10,
                    'G' => 40,
                    'H' => 40,
                ));

                $celdas = 'B2:H2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:H4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("C", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("D", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("F", function ($cells) {$cells->setAlignment('center');});

                $sheet->setBorder('B4:H4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);

                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "CONSOLIDADO DE EVALUACIÓN MENSUAL DE " . $nombreformato . " -  " . $mesnombre . " DEL " . $anoo;
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "APELLIDOS Y NOMBRES";
                $cabecera[] = "EDAD";
                $cabecera[] = "SEXO";
                $cabecera[] = "DIAGNÓSTICO SOCIAL";
                $cabecera[] = "CIE";
                $cabecera[] = "INTERVENCIÓN";
                $cabecera[] = "OBSERVACIÓN";
                $sheet->row(4, $cabecera);

                $c            = 5;
                $edadpaciente = "-";

                $A = 1;
                foreach ($lista as $row) {
                    //ANALIZO LOS CIES MAN
                    $historia = Historia::find($row->hid);
                    $persona  = Person::find($row->pid);
                    if ($historia->persona->fechanacimiento != '') {
                        $fechanacimiento = new DateTime($historia->persona->fechanacimiento);
                        $hoy             = new DateTime();
                        $annos           = $hoy->diff($fechanacimiento);
                        $edadpaciente    = $annos->y;
                    } else {
                        $edadpaciente = '-';
                    }
                    $cs = explode(';', $row->txtDiagnostico22);
                    foreach ($cs as $ca) {
                        $cc = Cie::find($ca);
                        if ($cc !== null) {
                            $cabecera = array();
                            //$cabecera[]=($c-4);
                            $cabecera[] = "";
                            $cabecera[] = $persona->apellidopaterno . " " . $persona->apellidomaterno . " " . $persona->nombres;
                            $cabecera[] = $edadpaciente;
                            $cabecera[] = $persona->sexo;
                            $cabecera[] = $cc->descripcion;
                            $cabecera[] = $cc->codigo;
                            $cabecera[] = $row->txtIntervencion;
                            $cabecera[] = $row->txtObservacion2;
                            $sheet->row($c, $cabecera);
                            $c++;
                        }
                    }
                    $celdas = 'A' . ($c - count($cs) + 1) . ':A' . ($c - 1); $sheet->mergeCells($celdas);
                    $celdas = 'B' . ($c - count($cs) + 1) . ':B' . ($c - 1); $sheet->mergeCells($celdas);
                    $celdas = 'C' . ($c - count($cs) + 1) . ':C' . ($c - 1); $sheet->mergeCells($celdas);
                    $celdas = 'D' . ($c - count($cs) + 1) . ':D' . ($c - 1); $sheet->mergeCells($celdas);
                    $celdas = 'G' . ($c - count($cs) + 1) . ':G' . ($c - 1); $sheet->mergeCells($celdas);
                    $celdas = 'H' . ($c - count($cs) + 1) . ':H' . ($c - 1); $sheet->mergeCells($celdas);
                }
                $sheet->setBorder('B5:H' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function historialResultadosPorPaciente(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $historia_id = $request->input("historia_id");
        $anoo        = $request->input("anno");
        //$anoo = "2019";
        $historia = Historia::find($historia_id);

        if ($historia !== null) {
            Excel::create("ExaMens" . $anoo, function ($excel) use ($request, $anoo, $historia) {

                $excel->sheet("ExaMens" . $anoo, function ($sheet) use ($request, $anoo, $historia) {

                    $sheet->setWidth(array(
                        'A' => 5, 'B' => 30, 'C' => 20, 'D' => 13, 'E' => 13, 'F' => 13, 'G' => 13, 'H' => 13, 'I' => 13, 'J' => 13, 'K' => 13, 'L' => 13, 'M' => 13, 'N' => 13, 'O' => 13));

                    $celdas = 'B2:O2';
                    $sheet->mergeCells($celdas);
                    $sheet->cells("B2:O4", function ($cells) {
                        $cells->setAlignment('center');
                        //$cells->setBorder('thin','thin','thin','thin');
                        $cells->setFont(array(
                            'family' => 'Calibri',
                            'size'   => '15',
                            'bold'   => true,
                        ));
                    });

                    $celdas = 'B4:O4';
                    $sheet->cells($celdas, function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setFont(array(
                            'family' => 'Calibri',
                            'size'   => '11',
                            'bold'   => true,
                        ));
                        $cells->setBackground('#5CC2C4');
                        $cells->setValignment('center');
                    });

                    $sheet->mergeCells("A3:O3");
                    $sheet->mergeCells("B4:C5");
                    $sheet->mergeCells("A4:A5");
                    $sheet->mergeCells("D4:O4");

                    $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});

                    $title   = array();
                    $title[] = "";
                    $title[] = "RESULTADOS DEL PACIENTE " . strtoupper($historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres) . " - " . $anoo;
                    $sheet->row(2, $title);

                    $cabecera = array();
                    //$cabecera[]="N°";
                    $cabecera[] = "";
                    $cabecera[] = "EXÁMENES DE LABORATORIO";
                    $cabecera[] = "";
                    $cabecera[] = "RESULTADOS";
                    $sheet->row(4, $cabecera);

                    $cabecera   = array();
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "Enero";
                    $cabecera[] = "Febrero";
                    $cabecera[] = "Marzo";
                    $cabecera[] = "Abril";
                    $cabecera[] = "Mayo";
                    $cabecera[] = "Junio";
                    $cabecera[] = "Julio";
                    $cabecera[] = "Agosto";
                    $cabecera[] = "Setiembre";
                    $cabecera[] = "Octubre";
                    $cabecera[] = "Noviembre";
                    $cabecera[] = "Diciembre";
                    $sheet->row(5, $cabecera);

                    $column = 'B';
                    for ($row = 6; $row <= 46; $row++) {
                        $sheet->SetCellValue($column . $row, ($row - 5));
                    }

                    for ($row = 6; $row <= 46; $row++) {
                        if ($row < 35 || $row > 38) {
                            $sheet->mergeCells("B" . $row . ":C" . $row);
                        }
                    }                    

                    $sheet->SetCellValue("B6", 'Hemoglobina (g/dl)');
                    $sheet->SetCellValue("B7", 'Hematocrito (%)');
                    $sheet->SetCellValue("B8", 'Urea pre (mg/dl)');
                    $sheet->SetCellValue("B9", 'Urea post (mg/dl)');
                    $sheet->SetCellValue("B10", 'TGO (U/l)');
                    $sheet->SetCellValue("B11", 'TGP (U/l)');
                    $sheet->SetCellValue("B12", 'Creatinina Pre (mg/dl)');
                    $sheet->SetCellValue("B13", 'Creatinina Post (mg/dl)');
                    $sheet->SetCellValue("B14", 'Fosfatasa Alcalina (U/L)');
                    $sheet->SetCellValue("B15", 'Hierro (ug/dl)');
                    $sheet->SetCellValue("B16", 'Transferrina');
                    $sheet->SetCellValue("B17", '% de Saturación de transferrina');
                    $sheet->SetCellValue("B18", 'Proteínas Totales (g/dl)');
                    $sheet->SetCellValue("B19", 'Albumina (g/dl)');
                    $sheet->SetCellValue("B20", 'Globulina (g/dl)');
                    $sheet->SetCellValue("B21", 'Fósforo (mg/dl)');

                    $sheet->SetCellValue("B22", 'Sodio (mmol/L)');
                    $sheet->SetCellValue("B23", 'Potasio (mmol/L)');
                    $sheet->SetCellValue("B24", 'Cloro (mmol/L)');

                    $sheet->SetCellValue("B25", 'Calcio (mg/dl)');
                    $sheet->SetCellValue("B26", 'Calcio corregido (mg/dl)');                    
                    $sheet->SetCellValue("B27", 'Ferritina (ng/ml)');
                    $sheet->SetCellValue("B28", 'Parathormona (pg/ml)');
                    $sheet->SetCellValue("B29", 'Antígeno de superficie Hepatitis B');
                    $sheet->SetCellValue("B30", 'Anticuerpos antígeno de superficie Hepatitis B');
                    $sheet->SetCellValue("B31", 'Anticuerpos para Hepatitis C');
                    $sheet->SetCellValue("B32", 'ANTI HBcAg CORE TOTAL');
                    $sheet->SetCellValue("B33", 'AcHBC - lg M');
                    $sheet->SetCellValue("B34", 'AcHBC - lg O');
                    $sheet->SetCellValue("B35", "VACUNACIÓN PARA HVB");
                    $sheet->SetCellValue("B36", "");
                    $sheet->SetCellValue("B37", "");
                    $sheet->SetCellValue("B38", "");
                    $sheet->SetCellValue("B39", "VACUNACIÓN PARA NEUMOCOCO");
                    $sheet->SetCellValue("B40", "VDRL");
                    $sheet->SetCellValue("B41", "HIV");
                    $sheet->SetCellValue("B42", "KTV");
                    $sheet->SetCellValue("B43", "TRU");
                    $sheet->SetCellValue("B44", "Peso Seco");
                    $sheet->SetCellValue("B45", "Acceso Vascular");
                    $sheet->SetCellValue("B46", "Área del dializador");
                    $sheet->setBorder('B4:O46', 'thin');

                    $sheet->mergeCells("B35:B38");

                    $sheet->SetCellValue("C35", '1° Dosis');
                    $sheet->SetCellValue("C36", '2° Dosis');
                    $sheet->SetCellValue("C37", '3° Dosis');
                    $sheet->SetCellValue("C38", '4° Dosis');

                    $mesito = 1;
                    for ($i = "D"; $i <= "O"; $i++) {
                        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
                            ->where('historia.convenio_id', '=', 1)
                            ->where('historia.baja', '!=', "S")
                            ->where('historia.id', '=', $historia->id)
                            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
                            ->where(DB::raw("MONTH(c.fecha)"), "=", $mesito)
                            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
                            ->first();
                        if ($resultado !== null) {
                            $sheet->SetCellValue($i . "5", date("d-M", strtotime($resultado->fecha)));
                        }
                        $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                            ->where("historia.id", "=", $historia->id)
                            ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $mesito)
                            ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anoo)
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
                                $ktv = '=(-LN(' . $i . '9/' . $i . '8-0.008*' . $time . '))+(4-3.5*' . $i . '9/' . $i . '8)*((' . $ppre . '-' . $ppos . ')/' . $ppos . '))';
                            }

                            //ARMO VALOR DE TRU
                            if ($resultado->txtUre !== null && $resultado->txtUre !== "") {
                                $tru = '=100-(' . $i . '9*100/' . $i . '8)';
                            }
                            $sheet->SetCellValue($i . "6", $resultado->txtDos);
                            $sheet->SetCellValue($i . "7", $resultado->txtHem);
                            $sheet->SetCellValue($i . "8", $resultado->txtUre);
                            $sheet->SetCellValue($i . "9", $resultado->txtUre2);
                            $sheet->SetCellValue($i . "10", $resultado->txtTgo);
                            $sheet->SetCellValue($i . "11", $resultado->txtTgp);
                            $sheet->SetCellValue($i . "12", $resultado->txtCre);
                            $sheet->SetCellValue($i . "13", "");
                            $sheet->SetCellValue($i . "14", $resultado->txtFos2);
                            $sheet->SetCellValue($i . "15", $resultado->txtHie);
                            $sheet->SetCellValue($i . "16", $resultado->txtTransfe);
                            $sheet->SetCellValue($i . "17", $resultado->txtSat);
                            $sheet->SetCellValue($i . "18", $resultado->txtPro);
                            $sheet->SetCellValue($i . "19", $resultado->txtAlbu);
                            $sheet->SetCellValue($i . "20", $resultado->txtGlobu);
                            $sheet->SetCellValue($i . "21", $resultado->txtFos);

                            $sheet->SetCellValue($i . "22", $resultado->txtSodio);
                            $sheet->SetCellValue($i . "23", $resultado->txtPotasio);
                            $sheet->SetCellValue($i . "24", $resultado->txtCloro);

                            $sheet->SetCellValue($i . "25", $resultado->txtCal);
                            $sheet->SetCellValue($i . "26", "");                            
                            $sheet->SetCellValue($i . "27", $resultado->txtFer);
                            $sheet->SetCellValue($i . "28", $resultado->txtPar);
                            $sheet->SetCellValue($i . "29", $resultado->txtDet);
                            $sheet->SetCellValue($i . "30", $resultado->txtDet2);
                            $sheet->SetCellValue($i . "31", $resultado->txtDet4);
                            $sheet->SetCellValue($i . "32", $resultado->txtDet3);
                            $sheet->SetCellValue($i . "33", "");
                            $sheet->SetCellValue($i . "34", "");
                            $sheet->SetCellValue($i . "35", "");
                            $sheet->SetCellValue($i . "36", "");
                            $sheet->SetCellValue($i . "37", "");
                            $sheet->SetCellValue($i . "38", "");
                            $sheet->SetCellValue($i . "39", "");
                            $sheet->SetCellValue($i . "40", $resultado->txtPru);
                            $sheet->SetCellValue($i . "41", $resultado->txtEli);
                            $sheet->SetCellValue($i . "42", $ktv);

                            $sheet->getStyle($i . "42")->getNumberFormat()->setFormatCode('0.00');
                            $sheet->getStyle($i . "43")->getNumberFormat()->setFormatCode('0.00');

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

                            $sheet->SetCellValue($i . "43", $tru);
                            $sheet->SetCellValue($i . "44", ($atencion == null ? "" : $atencion->txtPesoSeco));
                            $sheet->SetCellValue($i . "45", ($accesin));
                            $sheet->SetCellValue($i . "46", ($atencion == null ? "" : $atencion->txtAreaDializador));
                            $sheet->setBorder('B4:O46', 'thin');
                        }
                        $mesito++;
                    }

                });
            })->export('xls');
        } else {
            echo "HA SELECCIONADO UNA HISTORIA QUE NO EXISTE O ESTÁ DE BAJA";
        }
    }

    public function programacionMensualMedicamentos(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 1)
        //->where('historia.baja', '!=', "S")
            ->where('c.estadoprogramacion', "=", 1)
            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
            ->orderBy(DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, ' ', person.nombres)"));

        $lista = $resultado->get();

        Excel::create("ProgMed" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("ProgMed" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array('A' => 5, 'B' => 40, 'C' => 70, 'D' => 70, 'E' => 70));

                $celdas = 'B2:E2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $sheet->cells("B4:E4", function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("C", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("D", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("E", function ($cells) {$cells->setAlignment('center');});

                $title   = array();
                $title[] = "";
                $title[] = "PROGRAMACIÓN DE MEDICAMENTOS - " . $mesnombre . " DEL " . $anoo;
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "APELLIDOS Y NOMBRES";
                $cabecera[] = "EPOETINA ALFA 2000 UI/ML. INY 1 ML.";
                $cabecera[] = "HIERRO 20MG FE/ML INY 5ML";
                $cabecera[] = "VITAMINA B12 HIDROXICOBALAMINA 1MG7ML INY 1 ML";
                $sheet->row(4, $cabecera);

                $c = 5;

                foreach ($lista as $row) {
                    $epos    = "";
                    $hier    = "";
                    $vita    = "";
                    $arrepos = explode("**", $row->cadenaepo);
                    $arrhier = explode("**", $row->cadenahierro);
                    $arrvita = explode("**", $row->cadenavita);

                    if (count($arrepos) > 0) {
                        foreach ($arrepos as $r1) {
                            $arrepos2 = explode(";", $r1);
                            if (count($arrepos2) == 2) {
                                if ($arrepos2[1] !== "") {
                                    $epos .= $arrepos2[0] . " → " . $arrepos2[1] . "; ";
                                }
                            }
                        }
                    }
                    if (count($arrhier) > 0) {
                        foreach ($arrhier as $r2) {
                            $arrhier2 = explode(";", $r2);
                            if (count($arrhier2) == 2) {
                                if ($arrhier2[1] !== "") {
                                    $hier .= $arrhier2[0] . " → " . $arrhier2[1] . "; ";
                                }
                            }
                        }
                    }
                    if (count($arrvita) > 0) {
                        foreach ($arrvita as $r3) {
                            $arrvita2 = explode(";", $r3);
                            if (count($arrvita2) == 2) {
                                if ($arrvita2[1] !== "") {
                                    $vita .= $arrvita2[0] . " → " . $arrvita2[1] . "; ";
                                }
                            }
                        }
                    }

                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[] = "";
                    $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                    $cabecera[] = substr($epos, 0, strlen($epos) - 2);
                    $cabecera[] = substr($hier, 0, strlen($hier) - 2);
                    $cabecera[] = substr($vita, 0, strlen($vita) - 2);
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('B4:E' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function analisisMensualesKtvTru2(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes = (int) $request->input('mes');
        $anoo = $request->input('anno');
        $mesnombre = $this->meses[$mes];

        $lista = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->join('consultanefrologica as c', 'c.persona_id', '=', 'person.id')
            ->where('historia.convenio_id', '=', 2)
            ->where('historia.baja', '!=', 'S')
            ->where(DB::raw('MONTH(c.fecha)'), '=', $mes)
            ->where(DB::raw('YEAR(c.fecha)'), '=', $anoo)
            ->select(
                'c.*', 'person.apellidopaterno', 'person.apellidomaterno', 'person.nombres',
                'historia.id as historia_id', 'historia.fecha as fecha_ingreso'
            )
            ->orderBy(DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, ' ', person.nombres)"))
            ->get();

        Excel::create('Res' . $mesnombre . $anoo, function ($excel) use ($lista, $mesnombre, $anoo, $mes) {
            $excel->sheet('Res' . $mesnombre . $anoo, function ($sheet) use ($lista, $mes, $anoo) {
                $anchos = array('A' => 5, 'B' => 40, 'C' => 18, 'D' => 18);
                for ($indice = 4; $indice < 58; $indice++) {
                    $anchos[\PHPExcel_Cell::stringFromColumnIndex($indice)] = 12;
                }
                $sheet->setWidth($anchos);

                $sheet->row(1, array(
                    'N°', 'NOMBRES', 'FECHA DE INGRESO A CLÍNICA', 'FECHA TOMA DE MUESTRA',
                    'Hto (%)', 'Hb (g/dl)', 'ERITROCITOS', 'LEUCOCITOS', 'PLAQUETAS', 'VCM',
                    'HCM', 'CHCM', 'RDW(%)', 'RDW-SD', 'ABASTONADOS', 'SEGMENTADOS', 'EOSINOFILOS',
                    'BASOFILOS', 'MONOCITOS', 'LINFOCITOS', 'Urea pre (mg/dl)', 'Urea post (mg/dl)',
                    'TGO', 'TGP', 'Ca (mg/dl)', 'P (mg/dl)', 'Albu (g/dl)', 'HBsAg', 'Anti HBs',
                    'Anti HBc', 'HCV', 'Crea-Pre (mg/dl)', 'Crea Post (mg/dl)', 'Pro T (g/dl)',
                    'FAL (U/L)', 'Ferritina (ng/ml)', 'Hierro (ug/dl)', 'TRANSFERRINA',
                    '% de Saturación de transferrina', 'Parathormona (pg/ml)', 'PCR', 'Colesterol',
                    'Triglicéridos', 'HDL Colesterol', 'LDL Colesterol', 'VIH', 'VDRL', 'Vitamina B12',
                    'Ac. Fólico', 'Ác. Úrico', 'PESO PRE', 'PESO POST', 'KTV', 'TRU', 'TIEMPO',
                    'Peso Seco', 'Superficie de Dializador', 'Acceso'
                ));
                $sheet->cells('A1:BF1', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setFont(array('family' => 'Calibri', 'size' => 11, 'bold' => true));
                    $cells->setBackground('#5CC2C4');
                });
                $sheet->setBorder('A1:BF1', 'thin');
                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->freezeFirstRow();

                $fila = 2;
                foreach ($lista as $resultado) {
                    $atencion = HistoriaClinica::where('historia_id', '=', $resultado->historia_id)
                        ->where(DB::raw('MONTH(fecha_atencion)'), '=', $mes)
                        ->where(DB::raw('YEAR(fecha_atencion)'), '=', $anoo)
                        ->where(DB::raw('LENGTH(txtMuestraAnalisis)'), '>', 0)
                        ->where('estado', '!=', 'C')
                        ->orderBy('fecha_atencion', 'desc')->orderBy('id', 'desc')->first();

                    $datosMedicos = HistoriaClinica::where('historia_id', '=', $resultado->historia_id)
                        ->where(DB::raw('MONTH(fecha_atencion)'), '=', $mes)
                        ->where(DB::raw('YEAR(fecha_atencion)'), '=', $anoo)
                        ->where('estado', '!=', 'C')
                        ->where(function ($query) {
                            $query->where('txtPesoSeco', '<>', '')
                                ->orWhere('txtAccesoVascularArterial', '<>', '')
                                ->orWhere('txtAreaDializador', '<>', '');
                        })
                        ->orderBy('fecha_atencion', 'desc')->orderBy('id', 'desc')->first();
                    if ($datosMedicos === null) {
                        $datosMedicos = $atencion;
                    }

                    $tiempo = $atencion === null ? '' : $atencion->txtHorasHemodialisis;
                    $pesoPre = $atencion === null ? '' : $atencion->txtPesoInicial2;
                    $pesoPost = $atencion === null ? '' : $atencion->txtPesoFinal2;
                    $fechaMuestra = $atencion === null
                        ? ($resultado->txtFechaLaboratorio ?: $resultado->fecha)
                        : $atencion->fecha_atencion;
                    $ktv = '';
                    $tru = '';
                    if ($resultado->txtUre !== null && $resultado->txtUre !== '' &&
                        $resultado->txtUre2 !== null && $resultado->txtUre2 !== '') {
                        $tru = '=100-(V' . $fila . '*100/U' . $fila . ')';
                        if ($pesoPost !== null && $pesoPost !== '' && $pesoPost != 0 &&
                            $tiempo !== null && $tiempo !== '' && $tiempo != 0) {
                            $ktv = '=(-LN(V' . $fila . '/U' . $fila . '-0.008*BC' . $fila . '))+(4-3.5*V' . $fila . '/U' . $fila . ')*((AY' . $fila . '-AZ' . $fila . ')/AZ' . $fila . '))';
                        }
                    }

                    $accesos = array('1' => 'FAV', '2' => 'Autoinjerto', '3' => 'Injerto', '4' => 'CVCP', '5' => 'CVCT', '6' => 'VP');
                    $codigoAcceso = $datosMedicos === null ? '' : $datosMedicos->txtAccesoVascularArterial;
                    $acceso = isset($accesos[$codigoAcceso]) ? $accesos[$codigoAcceso] : '';

                    $sheet->row($fila, array(
                        $fila - 1,
                        trim($resultado->apellidopaterno . ' ' . $resultado->apellidomaterno . ' ' . $resultado->nombres),
                        $resultado->fecha_ingreso ? date('d/m/Y', strtotime($resultado->fecha_ingreso)) : '',
                        $fechaMuestra ? date('d/m/Y', strtotime($fechaMuestra)) : '',
                        $resultado->txtHem, $resultado->txtDos, $resultado->txtHematies, $resultado->txtLeucocitos,
                        $resultado->txtPlaquetas, $resultado->txtVcm, $resultado->txtHcm, $resultado->txtCcmh,
                        $resultado->txtRdw, $resultado->txtRdwSd, $resultado->txtAbastonados,
                        $resultado->txtSegmentados, $resultado->txtEosinofilos, $resultado->txtBasofilos,
                        $resultado->txtMonocitos, $resultado->txtLinfocitos, $resultado->txtUre, $resultado->txtUre2,
                        $resultado->txtTgo, $resultado->txtTgp, $resultado->txtCal, $resultado->txtFos,
                        $resultado->txtAlbu, $resultado->txtDet, $resultado->txtDet2, $resultado->txtDet3,
                        $resultado->txtDet4, $resultado->txtCre, $resultado->txtCre2, $resultado->txtPro,
                        $resultado->txtFos2, $resultado->txtFer, $resultado->txtHie, $resultado->txtTransfe,
                        $resultado->txtSat, $resultado->txtPar, $resultado->txtPcr, $resultado->txtColesterol,
                        $resultado->txtTrigliceridos, $resultado->txtHdl, $resultado->txtLdl, $resultado->txtEli,
                        $resultado->txtPru, $resultado->txtVitaminaB12, $resultado->txtAcidoFolico,
                        $resultado->txtAcidoUrico, ($pesoPre == 0 ? '' : $pesoPre),
                        ($pesoPost == 0 ? '' : $pesoPost), $ktv, $tru, ($tiempo == 0 ? '' : $tiempo),
                        ($datosMedicos === null ? '' : $datosMedicos->txtPesoSeco),
                        ($datosMedicos === null ? '' : $datosMedicos->txtAreaDializador), $acceso
                    ));
                    $sheet->getStyle('BA' . $fila . ':BB' . $fila)->getNumberFormat()->setFormatCode('0.00');
                    $fila++;
                }
                if ($fila > 2) {
                    $sheet->setBorder('A2:BF' . ($fila - 1), 'thin');
                }
            });
        })->export('xls');
    }

    private function analisisMensualesKtvTru2Legacy(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 2)
            ->where('historia.baja', '!=', "S")
            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
            ->orderBy(DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, ' ', person.nombres)"));

        $lista = $resultado->get();

        Excel::create("Res" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("Res" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array('A' => 5, 'B' => 40, 'C' => 10, 'D' => 10, 'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10, 'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10, 'S' => 10, 'T' => 10, 'U' => 10, 'V' => 10, 'W' => 10, 'X' => 10, 'Y' => 10, 'Z' => 10, 'AA' => 10, 'AB' => 10, 'AC' => 10, 'AD' => 10, 'AE' => 10, 'AF' => 10, 'AG' => 10, 'AH' => 10, 'AI' => 10, 'AJ' => 10, 'AK' => 10, 'AL' => 10));

                $celdas = 'B2:AL2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:AL4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                    $cells->setValignment('center');
                });

                $sheet->setBorder('B4:AL4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);
                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "ANÁLISIS ESSALUD MENSUALES MES DE " . $mesnombre . " DEL " . $anoo . " - KTV Y TRU";
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "NOMBRES";
                $cabecera[] = "Hb (g/dl)";
                $cabecera[] = "Hto (%)";
                $cabecera[] = "Urea post (mg/dl)";
                $cabecera[] = "Urea pre (mg/dl)";
                $cabecera[] = "Crea-Pre (mg/dl)";
                $cabecera[] = "Sodio (mmol/L)";
                $cabecera[] = "Potasio (mmol/L)";
                $cabecera[] = "Cloro (mmol/L)";
                $cabecera[] = "Ca (mg/dl)";
                $cabecera[] = "P (mg/dl)";
                $cabecera[] = "TGO";
                $cabecera[] = "TGP";
                $cabecera[] = "FAL (U/L)";
                $cabecera[] = "PCR";
                $cabecera[] = "Pro T (g/dl)";
                $cabecera[] = "Albu (g/dl)";
                $cabecera[] = "Glob (g/dl)";
                $cabecera[] = "Hierro (ug/dl)";
                $cabecera[] = "Transferrina";
                $cabecera[] = "% de Saturación de transferrina";
                $cabecera[] = "Ferritina (ng/ml)";
                $cabecera[] = "Parathormona (pg/ml)";
                $cabecera[] = "HBsAg";
                $cabecera[] = "Ac HBsAg";
                $cabecera[] = "Core Toral";
                $cabecera[] = "HCV";
                $cabecera[] = "VIH";
                $cabecera[] = "VDRL";
                $cabecera[] = "PESO PRE";
                $cabecera[] = "PESO POST";
                $cabecera[] = "KTV";
                $cabecera[] = "TRU";
                $cabecera[] = "TIEMPO";
                $cabecera[] = "Peso Seco (Kg)";
                $cabecera[] = "A. Vascular";
                $cabecera[] = "Á. Dializador";
                $sheet->row(4, $cabecera);

                $c = 5;

                foreach ($lista as $row) {
                    //BUSCO LA ATENCION DE HEMODIALISIS EN LA QUE SE TOMARON MUESTRAS, LO BUSCO EN LA FUA DE LA CUAL SE TOMARON DATOS MENSUALES Y SACO TIEMPO, PESO PRE Y PESO POST
                    $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                        ->where("historia.person_id", "=", $row->persona_id)
                        ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $mes)
                        ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anoo)
                        ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
                        ->where("historiaclinica.estado", "!=", "C")
                        ->first();
                    $time = 0;
                    $ppre = 0;
                    $ppos = 0;
                    if ($atencion !== null) {
                        $time = $atencion->txtHorasHemodialisis;
                        $ppre = $atencion->txtPesoInicial2;
                        $ppos = $atencion->txtPesoFinal2;
                    }
                    $cabecera = array();
                    //$cabecera[]=($c-4);
                    $cabecera[] = "";
                    $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                    $cabecera[] = $row->txtDos;
                    $cabecera[] = $row->txtHem;
                    $cabecera[] = $row->txtUre2;
                    $cabecera[] = $row->txtUre;
                    $cabecera[] = $row->txtCre;
                    $cabecera[] = $row->txtSodio;
                    $cabecera[] = $row->txtPotasio;
                    $cabecera[] = $row->txtCloro;
                    $cabecera[] = $row->txtCal;
                    $cabecera[] = $row->txtFos;
                    $cabecera[] = $row->txtTgo;
                    $cabecera[] = $row->txtTgp;
                    $cabecera[] = $row->txtFos2;
                    $cabecera[] = $row->txtPcr;
                    $cabecera[] = $row->txtPro;
                    $cabecera[] = $row->txtAlbu;
                    $cabecera[] = $row->txtGlobu;

                    $cabecera[] = $row->txtHie;
                    $cabecera[] = $row->txtTransfe;
                    $cabecera[] = $row->txtSat;
                    $cabecera[] = $row->txtFer;
                    $cabecera[] = $row->txtPar;
                    $cabecera[] = $row->txtDet;
                    $cabecera[] = $row->txtDet2;
                    $cabecera[] = $row->txtDet3;
                    $cabecera[] = "";
                    $cabecera[] = $row->txtEli;

                    $cabecera[] = $row->txtPru;

                    //peso pre y post
                    $cabecera[] = $ppre == 0 ? "" : $ppre;
                    $cabecera[] = $ppos == 0 ? "" : $ppos;

                    //ARMO VALOR DE KTV
                    $ktv = "";
                    $tru = "";
                    if ($row->txtUre !== null && $row->txtUre !== "" && $ppos !== null && $ppos !== "" && $time != 0) {
                        $ktv = '=(-LN(E' . $c . '/F' . $c . '-0.008*AI' . $c . '))+(4-3.5*E' . $c . '/F' . $c . ')*((AE' . $c . '-AF' . $c . ')/AF' . $c . '))';
                    }

                    //ARMO VALOR DE TRU
                    if ($row->txtUre !== null && $row->txtUre !== "") {
                        $tru = '=100-(E' . $c . '*100/F' . $c . ')';
                    }

                    $cabecera[] = $ktv;

                    $accesin = "";
                    if ($row !== null) {
                        switch ($row->txtAccesoVascularArterial) {
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

                    $cabecera[] = $tru;
                    $cabecera[] = $time == 0 ? "" : $time;
                    $cabecera[] = $row->txtPesoSeco;
                    $cabecera[] = $accesin;
                    $cabecera[] = $row->txtAreaDializador;
                    $sheet->row($c, $cabecera);
                    for ($resa = 5; $resa < $c; $resa++) {
                        $sheet->getStyle("AG" . $resa)->getNumberFormat()->setFormatCode('0.00');
                        $sheet->getStyle("AH" . $resa)->getNumberFormat()->setFormatCode('0.00');
                        $sheet->getStyle("AE" . $resa)->getNumberFormat()->setFormatCode('0.00');
                        $sheet->getStyle("AF" . $resa)->getNumberFormat()->setFormatCode('0.00');
                        $sheet->getStyle("AJ" . $resa)->getNumberFormat()->setFormatCode('0.00');
                    }
                    $c++;
                }
                $sheet->setBorder('B5:AL' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function historialResultadosPorPaciente2(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $historia_id = $request->input("historia_id");
        $anoo        = $request->input("anno");
        //$anoo = "2019";
        $historia = Historia::find($historia_id);

        if ($historia !== null) {
            Excel::create("ExaMens" . $anoo, function ($excel) use ($request, $anoo, $historia) {

                $excel->sheet("ExaMens" . $anoo, function ($sheet) use ($request, $anoo, $historia) {

                    $sheet->setWidth(array(
                        'A' => 36, 'B' => 12, 'C' => 13, 'D' => 13, 'E' => 13, 'F' => 13, 'G' => 13, 'H' => 13, 'I' => 13, 'J' => 13, 'K' => 13, 'L' => 13, 'M' => 13, 'N' => 13));

                    $celdas = 'A2:N2';
                    $sheet->mergeCells($celdas);
                    $sheet->cells("A2:N4", function ($cells) {
                        $cells->setAlignment('center');
                        //$cells->setBorder('thin','thin','thin','thin');
                        $cells->setFont(array(
                            'family' => 'Calibri',
                            'size'   => '15',
                            'bold'   => true,
                        ));
                    });

                    $celdas = 'A4:N4';
                    $sheet->cells($celdas, function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setFont(array(
                            'family' => 'Calibri',
                            'size'   => '11',
                            'bold'   => true,
                        ));
                        $cells->setBackground('#5CC2C4');
                        $cells->setValignment('center');
                    });

                    $sheet->mergeCells("A3:N3");
                    $sheet->mergeCells("A4:B5");
                    $sheet->mergeCells("C4:N4");

                    $title   = array();
                    $title[] = "RESULTADOS DEL PACIENTE DE ESSALUD - " . strtoupper($historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres) . " - " . $anoo;
                    $sheet->row(2, $title);

                    $cabecera = array();
                    //$cabecera[]="N°";
                    $cabecera[] = "";
                    $cabecera[] = "EXÁMENES DE LABORATORIO";
                    $cabecera[] = "";
                    $cabecera[] = "RESULTADOS";
                    $sheet->row(4, $cabecera);

                    $cabecera   = array();
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "Enero";
                    $cabecera[] = "Febrero";
                    $cabecera[] = "Marzo";
                    $cabecera[] = "Abril";
                    $cabecera[] = "Mayo";
                    $cabecera[] = "Junio";
                    $cabecera[] = "Julio";
                    $cabecera[] = "Agosto";
                    $cabecera[] = "Setiembre";
                    $cabecera[] = "Octubre";
                    $cabecera[] = "Noviembre";
                    $cabecera[] = "Diciembre";
                    $sheet->row(5, $cabecera);

                    $sheet->SetCellValue("A4", "EXAMENES DE LABORATORIO");
                    $sheet->SetCellValue("B4", "");
                    $sheet->SetCellValue("C4", "RESULTADOS");
                    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre");
                    $columnaMes = "C";
                    foreach ($meses as $mes) {
                        $sheet->SetCellValue($columnaMes . "5", $mes);
                        $columnaMes++;
                    }
                    $sheet->SetCellValue("O5", "");

                    $column = 'A';
                    for ($row = 6; $row <= 65; $row++) {
                        $sheet->SetCellValue($column . $row, ($row - 5));
                    }

                    for ($row = 6; $row <= 65; $row++) {
                        if ($row < 32 || $row > 35) {
                            $sheet->mergeCells("B" . $row . ":C" . $row);
                        }
                    }

                    $sheet->mergeCells("B32:B35");

                    $sheet->SetCellValue("C32", '1° Dosis');
                    $sheet->SetCellValue("C33", '2° Dosis');
                    $sheet->SetCellValue("C34", '3° Dosis');
                    $sheet->SetCellValue("C35", '4° Dosis');

                    $sheet->SetCellValue("B6", 'Hemoglobina (g/dl)');
                    $sheet->SetCellValue("B7", 'Hematocrito (%)');
                    $sheet->SetCellValue("B8", 'Urea pre (mg/dl)');
                    $sheet->SetCellValue("B9", 'Urea post (mg/dl)');
                    $sheet->SetCellValue("B10", 'TGO (U/l)');
                    $sheet->SetCellValue("B11", 'TGP (U/l)');
                    $sheet->SetCellValue("B12", 'Creatinina Pre (mg/dl)');
                    $sheet->SetCellValue("B13", 'Creatinina Post (mg/dl)');
                    $sheet->SetCellValue("B14", 'Fosfatasa Alcalina (U/L)');
                    $sheet->SetCellValue("B15", 'Hierro (ug/dl)');
                    $sheet->SetCellValue("B16", 'Transferrina');
                    $sheet->SetCellValue("B17", '% de Saturación de transferrina');
                    $sheet->SetCellValue("B18", 'Proteínas Totales (g/dl)');
                    $sheet->SetCellValue("B19", 'Albumina (g/dl)');
                    $sheet->SetCellValue("B20", 'Globulina (g/dl)');
                    $sheet->SetCellValue("B21", 'Fósforo (mg/dl)');
                    $sheet->SetCellValue("B22", 'Calcio (mg/dl)');
                    $sheet->SetCellValue("B23", 'Calcio corregido (mg/dl)');
                    $sheet->SetCellValue("B24", 'Ferritina (ng/ml)');
                    $sheet->SetCellValue("B25", 'Parathormona (pg/ml)');
                    $sheet->SetCellValue("B26", 'Antígeno de superficie Hepatitis B');
                    $sheet->SetCellValue("B27", 'Anticuerpos antígeno de superficie Hepatitis B');
                    $sheet->SetCellValue("B28", 'Anticuerpos para Hepatitis C');
                    $sheet->SetCellValue("B29", 'ANTI HBcAg CORE TOTAL');
                    $sheet->SetCellValue("B30", 'AcHBC - lg M');
                    $sheet->SetCellValue("B31", 'AcHBC - lg O');
                    $sheet->SetCellValue("B32", "VACUNACIÓN PARA HVB");
                    $sheet->SetCellValue("B33", "");
                    $sheet->SetCellValue("B34", "");
                    $sheet->SetCellValue("B35", "");
                    $sheet->SetCellValue("B36", "VACUNACIÓN PARA NEUMOCOCO");
                    $sheet->SetCellValue("B37", "VDRL");
                    $sheet->SetCellValue("B38", "HIV");
                    $sheet->SetCellValue("B39", "KTV");
                    $sheet->SetCellValue("B40", "TRU");
                    $sheet->SetCellValue("B41", "Peso Seco");
                    $sheet->SetCellValue("B42", "Acceso Vascular");
                    $sheet->SetCellValue("B43", "Área del dializador");
                    $sheet->SetCellValue("B44", "Leucocitos");
                    $sheet->SetCellValue("B45", "Hematies");
                    $sheet->SetCellValue("B46", "Plaquetas");
                    $sheet->SetCellValue("B47", "VCM");
                    $sheet->SetCellValue("B48", "HCM");
                    $sheet->SetCellValue("B49", "CHCM");
                    $sheet->SetCellValue("B50", "RDW (%)");
                    $sheet->SetCellValue("B51", "RDW-SD");
                    $sheet->SetCellValue("B52", "Abastonados");
                    $sheet->SetCellValue("B53", "Segmentados");
                    $sheet->SetCellValue("B54", "Eosinofilos");
                    $sheet->SetCellValue("B55", "Basofilos");
                    $sheet->SetCellValue("B56", "Monocitos");
                    $sheet->SetCellValue("B57", "Linfocitos");
                    $sheet->SetCellValue("B58", "PCR");
                    $sheet->SetCellValue("B59", "Colesterol");
                    $sheet->SetCellValue("B60", "Trigliceridos");
                    $sheet->SetCellValue("B61", "HDL Colesterol");
                    $sheet->SetCellValue("B62", "LDL Colesterol");
                    $sheet->SetCellValue("B63", "Vitamina B12");
                    $sheet->SetCellValue("B64", "Ac. Folico");
                    $sheet->SetCellValue("B65", "Ac. Urico");
                    for ($row = 6; $row <= 65; $row++) {
                        try {
                            $sheet->unmergeCells("B" . $row . ":C" . $row);
                        } catch (\Exception $e) {
                        }
                    }
                    try {
                        $sheet->unmergeCells("B32:B35");
                    } catch (\Exception $e) {
                    }

                    for ($row = 6; $row <= 65; $row++) {
                        foreach (range('A', 'N') as $columna) {
                            $sheet->SetCellValue($columna . $row, "");
                        }
                        $sheet->SetCellValue("O" . $row, "");
                    }

                    $analisis = array(
                        6 => 'Hemoglobina (g/dl)',
                        7 => 'Hematocrito (%)',
                        8 => 'Urea pre (mg/dl)',
                        9 => 'Urea post (mg/dl)',
                        10 => 'TGO (U/l)',
                        11 => 'TGP (U/l)',
                        12 => 'Creatinina Pre (mg/dl)',
                        13 => 'Creatinina Post (mg/dl)',
                        14 => 'Proteinas Totales (g/dl)',
                        15 => 'Fosfatasa Alcalina (U/L)',
                        16 => 'Ferritina (ng/ml)',
                        17 => 'Hierro (ug/dl)',
                        18 => 'Transferrina',
                        19 => '% de Saturacion de transferrina',
                        20 => 'Parathormona (pg/ml)',
                        21 => 'Albumina (g/dl)',
                        22 => 'Fosforo (mg/dl)',
                        23 => 'Calcio (mg/dl)',
                        24 => 'Calcio corregido (mg/dl)',
                        25 => 'Eritrocitos',
                        26 => 'Leucocitos',
                        27 => 'Plaquetas',
                        28 => 'VCM',
                        29 => 'HCM',
                        30 => 'CHCM',
                        31 => 'RDW (%)',
                        32 => 'RDW-SD',
                        33 => 'Abastonados',
                        34 => 'Segmentados',
                        35 => 'Eosinofilos',
                        36 => 'Basofilos',
                        37 => 'Monocitos',
                        38 => 'Linfocitos',
                        39 => 'PCR',
                        40 => 'Colesterol',
                        41 => 'Trigliceridos',
                        42 => 'HDL Colesterol',
                        43 => 'LDL Colesterol',
                        44 => 'Vitamina B12',
                        45 => 'Ac. Folico',
                        46 => 'Ac. Urico',
                        47 => 'Antigeno de superficie Hepatitis B',
                        48 => 'Anticuerpos antigeno de superficie Hepatitis B',
                        49 => 'Hepatitis C (HCV)',
                        50 => 'ANTI HBcAg CORE TOTAL',
                        51 => 'VDRL',
                        52 => 'HIV',
                        53 => 'KTV',
                        54 => 'TRU',
                        55 => 'Peso Seco',
                        56 => 'Acceso Vascular',
                        57 => 'Area del dializador',
                    );
                    foreach ($analisis as $row => $nombre) {
                        $sheet->mergeCells("A" . $row . ":B" . $row);
                        $sheet->SetCellValue("A" . $row, $nombre);
                    }
                    $sheet->setBorder('A4:N57', 'thin');

                    $mesito = 1;
                    for ($i = "C"; $i <= "N"; $i++) {
                        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
                            ->where('historia.convenio_id', '=', 2)
                            ->where('historia.baja', '!=', "S")
                            ->where('historia.id', '=', $historia->id)
                            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
                            ->where(DB::raw("MONTH(c.fecha)"), "=", $mesito)
                            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
                            ->first();
                        $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                            ->select("historiaclinica.*")
                            ->where("historia.id", "=", $historia->id)
                            ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $mesito)
                            ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anoo)
                            ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
                            ->where("historiaclinica.estado", "!=", "C")
                            ->first();
                        $datosMedicos = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                            ->select("historiaclinica.*")
                            ->where("historia.id", "=", $historia->id)
                            ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $mesito)
                            ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anoo)
                            ->where("historiaclinica.estado", "!=", "C")
                            ->where(function ($query) {
                                $query->where("txtPesoSeco", "<>", "")
                                    ->orWhere("txtAccesoVascularArterial", "<>", "")
                                    ->orWhere("txtAreaDializador", "<>", "")
                                    ->orWhere("txtAreaMembranaFiltro", "<>", "");
                            })
                            ->orderBy("historiaclinica.fecha_atencion", "desc")
                            ->orderBy("historiaclinica.id", "desc")
                            ->first();
                        if ($datosMedicos === null) {
                            $datosMedicos = $atencion;
                        }
                        if ($resultado !== null) {
                            $fechaResultado = $resultado->txtFechaLaboratorio ?: $resultado->fecha;
                            if ($datosMedicos !== null && $datosMedicos->fecha_atencion !== null && $datosMedicos->fecha_atencion !== "") {
                                $fechaResultado = $datosMedicos->fecha_atencion;
                            }
                            if ($atencion !== null && $atencion->fecha_atencion !== null && $atencion->fecha_atencion !== "") {
                                $fechaResultado = $atencion->fecha_atencion;
                            }
                            $sheet->SetCellValue($i . "5", date("d-M", strtotime($fechaResultado)));
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
                                $ktv = '=(-LN(' . $i . '9/' . $i . '8-0.008*' . $time . '))+(4-3.5*' . $i . '9/' . $i . '8)*((' . $ppre . '-' . $ppos . ')/' . $ppos . '))';
                            }

                            //ARMO VALOR DE TRU
                            if ($resultado->txtUre !== null && $resultado->txtUre !== "") {
                                $tru = '=100-(' . $i . '9*100/' . $i . '8)';
                            }
                            $sheet->SetCellValue($i . "6", $resultado->txtDos);
                            $sheet->SetCellValue($i . "7", $resultado->txtHem);
                            $sheet->SetCellValue($i . "8", $resultado->txtUre);
                            $sheet->SetCellValue($i . "9", $resultado->txtUre2);
                            $sheet->SetCellValue($i . "10", $resultado->txtTgo);
                            $sheet->SetCellValue($i . "11", $resultado->txtTgp);
                            $sheet->SetCellValue($i . "12", $resultado->txtCre);
                            $sheet->SetCellValue($i . "13", $resultado->txtCre2);
                            $sheet->SetCellValue($i . "14", $resultado->txtFos2);
                            $sheet->SetCellValue($i . "15", $resultado->txtHie);
                            $sheet->SetCellValue($i . "16", $resultado->txtTransfe);
                            $sheet->SetCellValue($i . "17", $resultado->txtSat);
                            $sheet->SetCellValue($i . "18", $resultado->txtPro);
                            $sheet->SetCellValue($i . "19", $resultado->txtAlbu);
                            $sheet->SetCellValue($i . "20", $resultado->txtGlobu);
                            $sheet->SetCellValue($i . "21", $resultado->txtFos);
                            $sheet->SetCellValue($i . "22", $resultado->txtCal);
                            $sheet->SetCellValue($i . "23", $resultado->txtCal2);
                            $sheet->SetCellValue($i . "24", $resultado->txtFer);
                            $sheet->SetCellValue($i . "25", $resultado->txtPar);
                            $sheet->SetCellValue($i . "26", $resultado->txtDet);
                            $sheet->SetCellValue($i . "27", $resultado->txtDet2);
                            $sheet->SetCellValue($i . "28", $resultado->txtDet4);
                            $sheet->SetCellValue($i . "29", $resultado->txtDet3);
                            $sheet->SetCellValue($i . "30", $resultado->txtLg1);
                            $sheet->SetCellValue($i . "31", $resultado->txtLg2);
                            $sheet->SetCellValue($i . "32", ($resultado->txtVacu1 == null ? "" : date("d-m-Y", strtotime($resultado->txtVacu1))));
                            $sheet->SetCellValue($i . "33", ($resultado->txtVacu2 == null ? "" : date("d-m-Y", strtotime($resultado->txtVacu2))));
                            $sheet->SetCellValue($i . "34", ($resultado->txtVacu3 == null ? "" : date("d-m-Y", strtotime($resultado->txtVacu3))));
                            $sheet->SetCellValue($i . "35", ($resultado->txtVacu4 == null ? "" : date("d-m-Y", strtotime($resultado->txtVacu4))));
                            $sheet->SetCellValue($i . "36", ($resultado->txtNeumo == null ? "" : date("d-m-Y", strtotime($resultado->txtNeumo))));
                            $sheet->SetCellValue($i . "37", $resultado->txtPru);
                            $sheet->SetCellValue($i . "38", $resultado->txtEli);
                            $sheet->SetCellValue($i . "39", $ktv);
                            $sheet->SetCellValue($i . "40", $tru);

                            $sheet->getStyle($i . "39")->getNumberFormat()->setFormatCode('0.00');
                            $sheet->getStyle($i . "40")->getNumberFormat()->setFormatCode('0.00');
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

                            $sheet->SetCellValue($i . "41", ($atencion == null ? "" : $atencion->txtPesoSeco));
                            $sheet->SetCellValue($i . "42", ($atencion == null ? "" : $accesin));
                            $sheet->SetCellValue($i . "43", ($atencion == null ? "" : $atencion->txtAreaDializador));
                            $sheet->SetCellValue($i . "44", $resultado->txtLeucocitos);
                            $sheet->SetCellValue($i . "45", $resultado->txtHematies);
                            $sheet->SetCellValue($i . "46", $resultado->txtPlaquetas);
                            $sheet->SetCellValue($i . "47", $resultado->txtVcm);
                            $sheet->SetCellValue($i . "48", $resultado->txtHcm);
                            $sheet->SetCellValue($i . "49", $resultado->txtCcmh);
                            $sheet->SetCellValue($i . "50", $resultado->txtRdw);
                            $sheet->SetCellValue($i . "51", $resultado->txtRdwSd);
                            $sheet->SetCellValue($i . "52", $resultado->txtAbastonados);
                            $sheet->SetCellValue($i . "53", $resultado->txtSegmentados);
                            $sheet->SetCellValue($i . "54", $resultado->txtEosinofilos);
                            $sheet->SetCellValue($i . "55", $resultado->txtBasofilos);
                            $sheet->SetCellValue($i . "56", $resultado->txtMonocitos);
                            $sheet->SetCellValue($i . "57", $resultado->txtLinfocitos);
                            $sheet->SetCellValue($i . "58", $resultado->txtPcr);
                            $sheet->SetCellValue($i . "59", $resultado->txtColesterol);
                            $sheet->SetCellValue($i . "60", $resultado->txtTrigliceridos);
                            $sheet->SetCellValue($i . "61", $resultado->txtHdl);
                            $sheet->SetCellValue($i . "62", $resultado->txtLdl);
                            $sheet->SetCellValue($i . "63", $resultado->txtVitaminaB12);
                            $sheet->SetCellValue($i . "64", $resultado->txtAcidoFolico);
                            $sheet->SetCellValue($i . "65", $resultado->txtAcidoUrico);

                            $accesoMedico = "";
                            if ($datosMedicos !== null) {
                                switch ($datosMedicos->txtAccesoVascularArterial) {
                                    case '1':
                                        $accesoMedico = "FAV";
                                        break;
                                    case '2':
                                        $accesoMedico = "Autoinjerto";
                                        break;
                                    case '3':
                                        $accesoMedico = "Injerto";
                                        break;
                                    case '4':
                                        $accesoMedico = "CVCP";
                                        break;
                                    case '5':
                                        $accesoMedico = "CVCT";
                                        break;
                                    case '6':
                                        $accesoMedico = "VP";
                                        break;
                                }
                            }

                            $sheet->SetCellValue($i . "6", $resultado->txtDos);
                            $sheet->SetCellValue($i . "7", $resultado->txtHem);
                            $sheet->SetCellValue($i . "8", $resultado->txtUre);
                            $sheet->SetCellValue($i . "9", $resultado->txtUre2);
                            $sheet->SetCellValue($i . "10", $resultado->txtTgo);
                            $sheet->SetCellValue($i . "11", $resultado->txtTgp);
                            $sheet->SetCellValue($i . "12", $resultado->txtCre);
                            $sheet->SetCellValue($i . "13", $resultado->txtCre2);
                            $sheet->SetCellValue($i . "14", $resultado->txtPro);
                            $sheet->SetCellValue($i . "15", $resultado->txtFos2);
                            $sheet->SetCellValue($i . "16", $resultado->txtFer);
                            $sheet->SetCellValue($i . "17", $resultado->txtHie);
                            $sheet->SetCellValue($i . "18", $resultado->txtTransfe);
                            $sheet->SetCellValue($i . "19", $resultado->txtSat);
                            $sheet->SetCellValue($i . "20", $resultado->txtPar);
                            $sheet->SetCellValue($i . "21", $resultado->txtAlbu);
                            $sheet->SetCellValue($i . "22", $resultado->txtFos);
                            $sheet->SetCellValue($i . "23", $resultado->txtCal);
                            $sheet->SetCellValue($i . "24", $resultado->txtCal2);
                            $sheet->SetCellValue($i . "25", $resultado->txtHematies);
                            $sheet->SetCellValue($i . "26", $resultado->txtLeucocitos);
                            $sheet->SetCellValue($i . "27", $resultado->txtPlaquetas);
                            $sheet->SetCellValue($i . "28", $resultado->txtVcm);
                            $sheet->SetCellValue($i . "29", $resultado->txtHcm);
                            $sheet->SetCellValue($i . "30", $resultado->txtCcmh);
                            $sheet->SetCellValue($i . "31", $resultado->txtRdw);
                            $sheet->SetCellValue($i . "32", $resultado->txtRdwSd);
                            $sheet->SetCellValue($i . "33", $resultado->txtAbastonados);
                            $sheet->SetCellValue($i . "34", $resultado->txtSegmentados);
                            $sheet->SetCellValue($i . "35", $resultado->txtEosinofilos);
                            $sheet->SetCellValue($i . "36", $resultado->txtBasofilos);
                            $sheet->SetCellValue($i . "37", $resultado->txtMonocitos);
                            $sheet->SetCellValue($i . "38", $resultado->txtLinfocitos);
                            $sheet->SetCellValue($i . "39", $resultado->txtPcr);
                            $sheet->SetCellValue($i . "40", $resultado->txtColesterol);
                            $sheet->SetCellValue($i . "41", $resultado->txtTrigliceridos);
                            $sheet->SetCellValue($i . "42", $resultado->txtHdl);
                            $sheet->SetCellValue($i . "43", $resultado->txtLdl);
                            $sheet->SetCellValue($i . "44", $resultado->txtVitaminaB12);
                            $sheet->SetCellValue($i . "45", $resultado->txtAcidoFolico);
                            $sheet->SetCellValue($i . "46", $resultado->txtAcidoUrico);
                            $sheet->SetCellValue($i . "47", $resultado->txtDet);
                            $sheet->SetCellValue($i . "48", $resultado->txtDet2);
                            $sheet->SetCellValue($i . "49", $resultado->txtDet4);
                            $sheet->SetCellValue($i . "50", $resultado->txtDet3);
                            $sheet->SetCellValue($i . "51", $resultado->txtPru);
                            $sheet->SetCellValue($i . "52", $resultado->txtEli);
                            $sheet->SetCellValue($i . "53", $ktv);
                            $sheet->SetCellValue($i . "54", $tru);
                            $sheet->SetCellValue($i . "55", ($datosMedicos == null ? "" : $datosMedicos->txtPesoSeco));
                            $sheet->SetCellValue($i . "56", ($datosMedicos == null ? "" : $accesoMedico));
                            $areaDializador = "";
                            if ($datosMedicos !== null) {
                                $areaDializador = ($datosMedicos->txtAreaMembranaFiltro !== null && $datosMedicos->txtAreaMembranaFiltro !== "") ? $datosMedicos->txtAreaMembranaFiltro : $datosMedicos->txtAreaDializador;
                            }
                            $sheet->SetCellValue($i . "57", $areaDializador);
                            for ($filaLimpia = 58; $filaLimpia <= 65; $filaLimpia++) {
                                $sheet->SetCellValue($i . $filaLimpia, "");
                            }
                            $sheet->getStyle($i . "53")->getNumberFormat()->setFormatCode('0.00');
                            $sheet->getStyle($i . "54")->getNumberFormat()->setFormatCode('0.00');
                            $sheet->setBorder('A4:N57', 'thin');
                        }
                        $mesito++;
                    }

                });
            })->export('xls');
        } else {
            echo "HA SELECCIONADO UNA HISTORIA QUE NO EXISTE O ESTÁ DE BAJA";
        }
    }

    public function reporteComprasAlmacen(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $fechai = date("Y-m-d", strtotime($request->input("fechai")));
        $fechaf = date("Y-m-d", strtotime($request->input("fechaf")));

        //////////////////////////////////////////////////////////////////////////////////////////////////

        $resultadoegresos = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
            ->join('conceptopago', 'conceptopago.id', '=', 'movimiento.conceptopago_id')
            ->leftjoin('movimiento as m2', 'm2.movimiento_id', '=', 'movimiento.id')
            ->where('movimiento.sucursal_id', '=', 1)
            ->whereBetween('movimiento.fecha', [$fechai, $fechaf])
            ->whereNull('movimiento.cajaapertura_id')
            ->where(function ($query) {
                $query
                //BOLETAS Y FACTURAS :3
                ->whereIn('movimiento.tipodocumento_id', [6, 7])
                    ->orWhere('m2.situacion', '<>', 'R');
            })
            ->where('conceptopago.tipo', '=', 'E');

        $resultadoegresos = $resultadoegresos->select('movimiento.*', 'm2.situacion as situacion2', 'responsable.nombres as responsable2', DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'paciente.ruc as ruc', DB::raw('concat(paciente.ruc,\' \',paciente.bussinesname) as razonsocial'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

        $listaegresos = $resultadoegresos->get();

        //////////////////////////////////////////////////////////////////////////////////////////////////

        Excel::create("Compras", function ($excel) use ($listaegresos, $request) {

            $excel->sheet("Compras", function ($sheet) use ($listaegresos, $request) {

                $sheet->setWidth(array('A' => 15, 'B' => 15, 'C' => 15, 'D' => 10, 'E' => 15, 'F' => 15, 'G' => 65, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 55));

                $celdas = 'A2:K2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'A4:K5';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                    $cells->setValignment('center');
                });

                $sheet->setBorder('A4:K5', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(40);
                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "COMPRAS NEFROCIX S.A.C. REALIZADAS ENTRE " . date("d-m-Y", strtotime($request->input("fechai"))) . " Y " . date("d-m-Y", strtotime($request->input("fechaf")));
                $sheet->row(2, $title);

                $cabecera   = array();
                $cabecera[] = "CORRELATIVO";
                $cabecera[] = "FECHA EMISIÓN";
                $cabecera[] = "FECHA VTO.";
                $cabecera[] = "COMPROBANTE";
                $cabecera[] = "";
                $cabecera[] = "INFORMACIÓN DEL PROVEEDOR";
                $cabecera[] = "";
                $cabecera[] = "ADQUISICIONES GRAVADAS";
                $cabecera[] = "";
                $cabecera[] = "IMPORTE TOTAL";
                $cabecera[] = "COMENTARIO";
                $sheet->row(4, $cabecera);

                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "SERIE";
                $cabecera[] = "N° COMPROB.";
                $cabecera[] = "N° DOCUMENTO";
                $cabecera[] = "RAZÓN SOCIAL";
                $cabecera[] = "BASE IMPONIBLE";
                $cabecera[] = "IGV";
                $cabecera[] = "";
                $sheet->row(5, $cabecera);

                $sheet->mergeCells("A4:A5");
                $sheet->mergeCells("B4:B5");
                $sheet->mergeCells("C4:C5");
                $sheet->mergeCells("D4:E4");
                $sheet->mergeCells("F4:G4");
                $sheet->mergeCells("H4:I4");

                $c = 6;

                foreach ($listaegresos as $row) {
                    $cabecera   = array();
                    $cabecera[] = str_pad($row->id, 6, '0', STR_PAD_LEFT);
                    $cabecera[] = date("d/m/Y", strtotime($row->fecha));
                    $cabecera[] = date("d/m/Y", strtotime($row->fechaingreso));
                    $cabecera[] = ($row->tipodocumento_id == 6 ? "F" : "B") . str_pad($row->serie, 4, '0', STR_PAD_LEFT);
                    $cabecera[] = str_pad($row->numeroserie2, 8, '0', STR_PAD_LEFT);
                    $cabecera[] = $row->ruc;
                    $cabecera[] = $row->razonsocial;
                    $cabecera[] = $row->subtotal;
                    $cabecera[] = $row->igv;
                    $cabecera[] = $row->total;
                    $cabecera[] = $row->comentario;
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('A5:K' . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function reporteProgramacionesDiariasHD(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $fechai      = date("Y-m-d", strtotime($request->input("fechai")));
        $fechaf      = date("Y-m-d", strtotime($request->input("fechaf")));
        $convenio_id = $request->input("convenio_id");

        //////////////////////////////////////////////////////////////////////////////////////////////////

        $fechas = HistoriaClinica::join("historia as h", "h.id", "=", "historiaclinica.historia_id")
            ->where(DB::raw("LENGTH(historiaclinica.txtMarcaModeloMaquina)"), ">", 0)
            ->where("historiaclinica.estado", "!=", "C")
            ->where("h.convenio_id", "=", $convenio_id)
            ->select(DB::raw("DATE(historiaclinica.fecha_atencion) as ff"))
            ->orderBy("historiaclinica.fecha_atencion", "ASC")
            ->whereBetween(DB::raw("DATE(historiaclinica.fecha_atencion)"), [$fechai, $fechaf])
            ->distinct()
            ->get();

        $enfermeras = HistoriaClinica::join("historia as h", "h.id", "=", "historiaclinica.historia_id")
            ->where(DB::raw("LENGTH(historiaclinica.txtMarcaModeloMaquina)"), ">", 0)
            ->where("historiaclinica.estado", "!=", "C")
            ->where("h.convenio_id", "=", $convenio_id)
            ->join("user as enf", "enf.id", "=", "historiaclinica.user_id")
            ->where("usertype_id", "=", 30)
            ->select("historiaclinica.user_id")
            ->whereBetween(DB::raw("DATE(historiaclinica.fecha_atencion)"), [$fechai, $fechaf])
            ->distinct()
            ->get();

        $medicos = HistoriaClinica::join("historia as h", "h.id", "=", "historiaclinica.historia_id")
            ->where(DB::raw("LENGTH(historiaclinica.txtMarcaModeloMaquina)"), ">", 0)
            ->where("h.convenio_id", "=", $convenio_id)
            ->where("historiaclinica.estado", "!=", "C")
            ->join("person as doc", "doc.id", "=", "historiaclinica.doctor_id")
            ->select("historiaclinica.doctor_id")
            ->whereBetween(DB::raw("DATE(historiaclinica.fecha_atencion)"), [$fechai, $fechaf])
            ->distinct()
            ->get();

        $coordinadores = Person::join("user as u", "person.id", "=", "u.person_id")
            ->where("u.usertype_id", "=", 39)
            ->where("u.state", "=", "H")
            ->get();

        $turnos = Turno::orderBy("hora", "ASC")->get();

        //////////////////////////////////////////////////////////////////////////////////////////////////
        if (count($fechas) > 0) {

            Excel::create("Program" . $fechai . "|" . $fechaf, function ($excel) use ($fechas, $turnos, $enfermeras, $medicos, $coordinadores, $request, $convenio_id) {

                foreach ($fechas as $ff) {

                    $fechaa = date("d.m.Y", strtotime($ff->ff));

                    $mes       = (int) date("m", strtotime($ff->ff));
                    $anito     = date("Y", strtotime($ff->ff));
                    $mesnombre = $this->meses[$mes];
                    $dianombre = $this->dias[date('w', strtotime($ff->ff))];

                    $excel->sheet($fechaa, function ($sheet) use ($fechas, $turnos, $enfermeras, $medicos, $coordinadores, $request, $dianombre, $ff, $convenio_id) {

                        $sheet->setWidth(array('A' => 15, 'B' => 15, 'C' => 50, 'D' => 15, 'E' => 15, 'F' => 15, 'G' => 15, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 15, 'L' => 15, 'M' => 15));

                        $celdas = 'A5:M5';
                        $sheet->mergeCells($celdas);
                        $sheet->cells('A5:M13', function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                            $cells->setFont(array(
                                'family' => 'Comic Sans MS',
                                'size'   => '12',
                                'bold'   => true,
                            ));
                        });
                        $sheet->mergeCells("A13:M13");

                        $sheet->cells('A13:M13', function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        });

                        //////////////

                        $cantidadenf = count($enfermeras);
                        $cantidadmed = count($medicos);

                        //////////////

                        $title   = array();
                        $title[] = "PROGRAMACIÓN DIARIA DE HEMODIÁLISIS - " . ($convenio_id == 1 ? "SIS" : "ESSALUD");
                        $sheet->row(5, $title);

                        $title   = array();
                        $title[] = "Enfermera(s) coordinadora(s):";
                        $title[] = "";
                        $title[] = "";
                        $title[] = "Enfermeras:";
                        $title[] = "";
                        $title[] = "";
                        $title[] = "";
                        $title[] = "";
                        $title[] = "";
                        $title[] = "Médico:";
                        $sheet->row(8, $title);
                        for ($ask = 8; $ask <= 12; $ask++) {
                            $sheet->cells("A" . $ask . ":M" . $ask, function ($cells) {
                                $cells->setAlignment('center');
                                $cells->setValignment('center');
                                $cells->setFont(array(
                                    'bold' => true,
                                ));
                            });
                        }

                        $ncoord = 0;
                        for ($wsps = 0; $wsps < 5; $wsps++) {
                            if (isset($coordinadores[$wsps])) {
                                $sheet->SetCellValue("C" . ($ncoord + 8), $coordinadores[$wsps]->apellidopaterno . " " . $coordinadores[$wsps]->apellidomaterno . " " . $coordinadores[$wsps]->nombres);
                                $ncoord++;
                            }
                        }

                        $nenf = 0;
                        for ($wsps = 0; $wsps < 5; $wsps++) {
                            if (isset($enfermeras[$wsps])) {
                                //BUSCO A LA ENFERMERA
                                $enfer = User::find($enfermeras[$wsps]->user_id);
                                $sheet->SetCellValue("E" . ($nenf + 8), $enfer->person->apellidopaterno . " " . $enfer->person->apellidomaterno . " " . $enfer->person->nombres);
                                $nenf++;
                            }
                        }

                        $nenf = 0;
                        for ($wsps = 5; $wsps < 10; $wsps++) {
                            if (isset($enfermeras[$wsps])) {
                                //BUSCO A LA ENFERMERA
                                $enfer = User::find($enfermeras[$wsps]->doctor_id);
                                $sheet->SetCellValue("G" . ($nenf + 8), $enfer!==NULL&&$enfer->person!==NULL?($enfer->person->apellidopaterno . " " . $enfer->person->apellidomaterno . " " . $enfer->person->nombres):"");
                                $nenf++;
                            }
                        }

                        $nmed = 0;
                        for ($wsps = 0; $wsps < 5; $wsps++) {
                            if (isset($medicos[$wsps])) {
                                $medic = Person::find($medicos[$wsps]->doctor_id);
                                $sheet->SetCellValue("K" . ($nmed + 8), $medic->apellidopaterno . " " . $medic->apellidomaterno . " " . $medic->nombres);
                                $nmed++;
                            }
                        }

                        //UNO CELDAS
                        $sheet->mergeCells("A8:B8");
                        for ($aww = 8; $aww < 13; $aww++) {
                            $sheet->mergeCells("E" . $aww . ":F" . $aww);
                            $sheet->mergeCells("G" . $aww . ":H" . $aww);
                            $sheet->mergeCells("K" . $aww . ":M" . $aww);
                        }

                        $title   = array();
                        $title[] = $dianombre . " " . date("d/m/Y", strtotime($ff->ff));
                        $sheet->row(13, $title);

                        $c = 14;

                        $npacientes = 0;
                        $nelisio0   = 0;
                        $nelisio1   = 0;
                        $nelisio2   = 0;
                        $nelisio3   = 0;
                        $nfav       = 0;
                        $ncvc       = 0;
                        $ncvp       = 0;
                        $ninj       = 0;

                        foreach ($turnos as $turn) {
                            //ATENCIONES
                            $atencionesturno = HistoriaClinica::join("historia as h", "h.id", "=", "historiaclinica.historia_id")
                                ->join("person", "person.id", "=", "h.person_id")
                                ->where(DB::raw("LENGTH(historiaclinica.txtMarcaModeloMaquina)"), ">", 0)
                            //SOLO ESSALUD
                                ->where("h.convenio_id", "=", $convenio_id)
                                ->where("historiaclinica.estado", "!=", "C")
                                ->orderBy("historiaclinica.fecha_atencion", "ASC")
                                ->where(DB::raw("DATE(historiaclinica.fecha_atencion)"), "=", $ff->ff)
                                ->where("h.horacita", "=", $turn->id)
                                ->orderBy(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'))
                                ->get();
                            if (count($atencionesturno) > 0) {
                                $title   = array();
                                $title[] = $turn->romano . " TURNO";
                                $sheet->row($c, $title);
                                $sheet->mergeCells("A" . $c . ":M" . $c);
                                $sheet->cells("A" . $c . ":M" . $c, function ($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                    $cells->setFont(array(
                                        'bold' => true,
                                    ));
                                });
                                $c++;

                                //CABECERA
                                $cabecera   = array();
                                $cabecera[] = "N° Diálisis";
                                $cabecera[] = "N° de Máquina";
                                $cabecera[] = "Nombre del Paciente";
                                $cabecera[] = "Horas HD";
                                $cabecera[] = "A.V.";
                                $cabecera[] = "Q.B.";
                                $cabecera[] = "Peso Seco";
                                $cabecera[] = "Peso Inicial";
                                $cabecera[] = "Peso Final";
                                $cabecera[] = "P AI.";
                                $cabecera[] = "P AT.";
                                $cabecera[] = "FILTRO.";
                                $cabecera[] = "HEPARINA";
                                $sheet->row($c, $cabecera);
                                $sheet->cells("A" . $c . ":M" . $c, function ($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                    $cells->setFont(array(
                                        'bold' => true,
                                    ));
                                });
                                $c++;

                                foreach ($atencionesturno as $att) {
                                    //SUMAMOS LOS VALORES PARA EL RESUMEN EN PIE DE PAGINA

                                    $npacientes++;
                                    $accesito = "";

                                    switch ($att->txtAreaMembranaFiltro) {
                                        case '1.7':
                                            $nelisio0++;
                                            break;
                                        case '1.8':
                                            $nelisio1++;
                                            break;
                                        case '2.0':
                                            $nelisio2++;
                                            break;
                                        case '2.2':
                                            $nelisio3++;
                                            break;
                                    }

                                    switch ($att->txtAccesoVascularArterial) {
                                        case '1':
                                            $nfav++;
                                            $accesito = "F.A.V.";
                                            break;
                                        case '2':
                                            $accesito = "AUTOINJERTO";
                                            //$nelisio2++;
                                            break;
                                        case '3':
                                            $accesito = "INJERTO";
                                            $ninj++;
                                            break;
                                        case '4':
                                            $accesito = "C.V.P.";
                                            $ncvp++;
                                            break;
                                        case '5':
                                            $accesito = "C.V.C.";
                                            $ncvc++;
                                            break;
                                        case '6':
                                            $accesito = "V.P.";
                                            //$nelisio3++;
                                            break;
                                    }

                                    /////////////////////////////////////////////////
                                    $cabecera   = array();
                                    $cabecera[] = "SN";
                                    $cabecera[] = $att->txtNMAquina;
                                    $cabecera[] = $att->apellidopaterno . " " . $att->apellidomaterno . " " . $att->nombres;
                                    $cabecera[] = $att->txtHorasHemodialisis;
                                    $cabecera[] = $accesito;
                                    $cabecera[] = $att->txtQb;
                                    $cabecera[] = $att->txtPesoSeco;
                                    $cabecera[] = $att->txtPesoInicial2;
                                    $cabecera[] = $att->txtPesoInicial2;
                                    $cabecera[] = $att->txtPAInicial;
                                    $cabecera[] = $att->txtPAFinal;
                                    $cabecera[] = $att->txtAreaMembranaFiltro;
                                    $cabecera[] = $att->txtDosisHepa;
                                    $sheet->row($c, $cabecera);
                                    $sheet->cells("A" . $c . ":M" . $c, function ($cells) {
                                        $cells->setAlignment('center');
                                        $cells->setValignment('center');
                                    });
                                    $c++;
                                }
                            }
                        }

                        $sheet->setBorder('A13:M' . ($c - 1), 'thin');

                        $piepagina   = array();
                        $piepagina[] = "N° Pacientes:";
                        $piepagina[] = $npacientes;
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "N° Filtros";
                        $piepagina[] = "Elisio 200";
                        $piepagina[] = $nelisio2;
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $sheet->row($c, $piepagina);
                        $c++;

                        $piepagina   = array();
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "Elisio 180";
                        $piepagina[] = $nelisio1;
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "A.V.";
                        $piepagina[] = "F.A.V.";
                        $piepagina[] = $nfav;
                        $sheet->row($c, $piepagina);
                        $c++;

                        $piepagina   = array();
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "Elisio 170";
                        $piepagina[] = $nelisio0;
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "C.V.C.";
                        $piepagina[] = $ncvc;
                        $sheet->row($c, $piepagina);
                        $c++;

                        $piepagina   = array();
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "Elisio 220";
                        $piepagina[] = $nelisio3;
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "C.V.P.";
                        $piepagina[] = $ncvp;
                        $sheet->row($c, $piepagina);
                        $c++;

                        $piepagina   = array();
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "";
                        $piepagina[] = "INJ";
                        $piepagina[] = $ninj;
                        $sheet->row($c, $piepagina);
                        $c++;
                    });
                }

            })->export('xls');
        } else {
            echo "NO HAY ATENCIONES EN ESSALUD ENTRE ESTAS FECHAS";
        }
    }

    public function consolidadoMedicamentos(Request $request)
    {
        $id       = $request->input("id");
        $paciente = Person::find($id);
        date_default_timezone_set('America/Lima');

        //////////////////////////////////////////////////////////////////////////////////////////////////

        $fechas = ConsultaNefrologica::join("person as p", "p.id", "=", "consultanefrologica.persona_id")
            ->join("historia as h", "h.person_id", "=", "p.id")
            ->where("p.id", "=", $id)
            ->where("h.convenio_id", "=", 1)
            ->select(DB::raw("YEAR(consultanefrologica.fecha) as ff"))
            ->orderBy("consultanefrologica.fecha", "ASC")
            ->distinct()
            ->get();

        //////////////////////////////////////////////////////////////////////////////////////////////////

        Excel::create("Medicamentos", function ($excel) use ($fechas, $paciente, $request) {

            foreach ($fechas as $ff) {

                $anito = date("Y", strtotime($ff->ff));

                $excel->sheet($anito, function ($sheet) use ($anito, $paciente) {

                    $sheet->setWidth(array('A' => 60, 'B' => 15, 'C' => 15, 'D' => 15, 'E' => 15, 'F' => 15, 'G' => 15, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 15, 'L' => 15, 'M' => 15));

                    $celdas = 'A2:M2';
                    $sheet->mergeCells($celdas);
                    $sheet->cells('A2:M20', function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                        $cells->setFont(array(
                            'family' => 'Comic Sans MS',
                            'size'   => '12',
                            'bold'   => true,
                        ));
                    });
                    $sheet->cells('A5:A20', function ($cells) {
                        $cells->setAlignment('left');
                        $cells->setValignment('left');
                        $cells->setFont(array(
                            'bold' => false,
                        ));
                    });
                    $sheet->setBorder('A4:M20', 'thin');

                    $title   = array();
                    $title[] = "CONSOLIDADO DE MEDICAMENTOS EN " . $anito . " RECETADOS AL PACIENTE: " . $paciente->apellidopaterno . " " . $paciente->apellidomaterno . " " . $paciente->nombres;
                    $sheet->row(2, $title);

                    $title   = array();
                    $title[] = "DESCRIPCIÓN";
                    $title[] = "ENERO";
                    $title[] = "FEBRERO";
                    $title[] = "MARZO";
                    $title[] = "ABRIL";
                    $title[] = "MAYO";
                    $title[] = "JUNIO";
                    $title[] = "JULIO";
                    $title[] = "AGOSTO";
                    $title[] = "SETIEMBRE";
                    $title[] = "OCTUBRE";
                    $title[] = "NOVIEMBRE";
                    $title[] = "DICIEMBRE";
                    $sheet->row(4, $title);
                    
                    $sheet->SetCellValue("A5", "EPOETINA ALFA (ERITROPOYETINA) 2000 UI/ML INY 1 ML");
                    $sheet->SetCellValue("A6", "HIERRO (COMO SACARATO) 20MG FE/ML INY 5 ML");
                    $sheet->SetCellValue("A7", "VITAMINA B12 HIDROXICOBALAMINA 1MG/ML INY 1ML");
                    $sheet->SetCellValue("A8", "CALCIO CARBONATO 500 MG (EQUIV.A 500 MG DE CALCIO) TAB");
                    $sheet->SetCellValue("A9", "PIRIDOXINA 50MG TAB");
                    $sheet->SetCellValue("A10", "TIAMINA 100MG TAB");
                    $sheet->SetCellValue("A11", "ÁCIDO FÓLICO 0.5 MG TAB");

                    $sheet->SetCellValue("A12", "CALCITRIOL 1 MCG/ML INY");
                    $sheet->SetCellValue("A13", "CALCITRIOL 0.25ug CAP (**)");

                    $sheet->SetCellValue("A14", "ENALAPRIL MALEATO 10 MG TAB");
                    $sheet->SetCellValue("A15", "CAPTOPRIL 25 MG TAB");
                    $sheet->SetCellValue("A16", "AMLODIPINO (COMO BESILATO) 10 MG TAB");
                    $sheet->SetCellValue("A17", "NIFEDIPINO 10 MG");
                    $sheet->SetCellValue("A18", "NIFEDIPINO DE 30 MG");
                    $sheet->SetCellValue("A19", "METILDOPA 250 MG");
                    $sheet->SetCellValue("A20", "ATENOLOL 100 MG");
                    $sheet->SetCellValue("A21", "LOSARTAN 50 MG");

                    $mesito = 1;
                    for ($i = "B"; $i <= "M"; $i++) {
                        $atencion = ConsultaNefrologica::join("person as p", "p.id", "=", "consultanefrologica.persona_id")
                            ->join("historia as h", "h.person_id", "=", "p.id")
                            ->where("p.id", "=", $paciente->id)
                            ->where(DB::raw("MONTH(consultanefrologica.fecha)"), "=", $mesito)
                            ->where(DB::raw("YEAR(consultanefrologica.fecha)"), "=", $anito)
                            ->first();
                        if ($atencion !== null) {
                            $sheet->SetCellValue($i . "5", $atencion->c2);
                            $sheet->SetCellValue($i . "6", $atencion->c3);
                            $sheet->SetCellValue($i . "7", $atencion->c4);
                            $sheet->SetCellValue($i . "8", $atencion->c5);
                            $sheet->SetCellValue($i . "9", $atencion->c6);
                            $sheet->SetCellValue($i . "10", $atencion->c7);
                            $sheet->SetCellValue($i . "11", $atencion->c8);
                            $sheet->SetCellValue($i . "12", $atencion->c9);
                            $sheet->SetCellValue($i . "13", $atencion->c91);
                            $sheet->SetCellValue($i . "14", $atencion->c10);
                            $sheet->SetCellValue($i . "15", $atencion->c11);
                            $sheet->SetCellValue($i . "16", $atencion->c12);
                            $sheet->SetCellValue($i . "17", $atencion->c13);
                            $sheet->SetCellValue($i . "18", $atencion->c14);
                            $sheet->SetCellValue($i . "19", $atencion->c15);
                            $sheet->SetCellValue($i . "20", $atencion->c16);
                            $sheet->SetCellValue($i . "21", $atencion->c17);
                        }
                        $mesito++;
                    }
                });
            }

        })->export('xls');
    }

    public function consolidadoMedicamentosTodosPacientes(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 1)
            ->where('historia.baja', '!=', "S")
            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
            ->select("c.*", DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, '', person.nombres) as paciente"))
            ->orderBy(DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, ' ', person.nombres)"));

        $resultado = $resultado->get();

        Excel::create("Med" . $mes . "" . $anoo, function ($excel) use ($request, $mesnombre, $anoo, $mes, $resultado) {

            $excel->sheet("Med" . $mes . "" . $anoo, function ($sheet) use ($request, $mesnombre, $anoo, $mes, $resultado) {

                $sheet->setWidth(array('A' => 5, 'B' => 50, 'C' => 15, 'D' => 15, 'E' => 15, 'F' => 15, 'G' => 15, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 15, 'L' => 15, 'M' => 15, 'N' => 15, 'O' => 15, 'P' => 15, 'Q' => 15, 'R' => 15, 'S' => 15));

                $celdas = 'A2:S2';
                $sheet->mergeCells($celdas);
                $sheet->cells('A2:S4', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setFont(array(
                        'family' => 'Comic Sans MS',
                        'size'   => '8',
                        'bold'   => true,
                    ));
                });

                $sheet->cells('A2:S2', function ($cells) {
                    $cells->setFont(array(
                        'size' => '12',
                    ));
                });

                $sheet->cells('A4:S4', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#5CC2C4');
                });

                $title   = array();
                $title[] = "CONSOLIDADO DE MEDICAMENTOS EN " . $mesnombre . " - " . $anoo;
                $sheet->row(2, $title);

                $title   = array();
                $title[] = "N°";
                $title[] = "APELLIDOS Y NOMBRES";
                $title[] = "EPOETINA 2000 UI";
                $title[] = "HIERRO";
                $title[] = "VITAMINA B12";
                $title[] = "CARB. DE CAL.";
                $title[] = "PIRIDOXINA 50mg";
                $title[] = "TIAMINA 100mg";
                $title[] = "AC. FOLICO 0.5mg";
                $title[] = "CALCITR. 1mcg/ml";
                $title[] = "CALCITRIOL 0.25ug";
                $title[] = "ENALAP. MAL. 10mg";
                $title[] = "CAPTOPRIL 25mg";
                $title[] = "AMLODIPINO 10mg";
                $title[] = "NIFEDIPINO 10mg";
                $title[] = "NIFEDIPINO 30mg";
                $title[] = "METILDOPA 250mg";
                $title[] = "ATENOLOL 100mg";
                $title[] = "LOSARTAN 50mg";
                $sheet->row(4, $title);

                $mesito = 1;
                $c      = 5;
                $d1 = 0;
                $d2 = 0;
                $d3 = 0;
                $d4 = 0;
                $d5 = 0;
                $d6 = 0;
                $d7 = 0;
                $d8 = 0;
                $d9 = 0;
                $d91 = 0;
                $d10 = 0;
                $d11 = 0;
                $d12 = 0;
                $d13 = 0;
                $d14 = 0;
                $d15 = 0;
                $d16 = 0;
                foreach ($resultado as $row) {
                    $cabecera   = array();
                    $cabecera[] = ($c - 4);
                    $cabecera[] = $row->paciente;
                    $cabecera[] = $row->c2; $d1 += ($row->c2==""?0:$row->c2);
                    $cabecera[] = $row->c3; $d2 += ($row->c3==""?0:$row->c3);
                    $cabecera[] = $row->c4; $d3 += ($row->c4==""?0:$row->c4);
                    $cabecera[] = $row->c5; $d4 += ($row->c5==""?0:$row->c5);
                    $cabecera[] = $row->c6; $d5 += ($row->c6==""?0:$row->c6);
                    $cabecera[] = $row->c7; $d6 += ($row->c7==""?0:$row->c7);
                    $cabecera[] = $row->c8; $d7 += ($row->c8==""?0:$row->c8);
                    $cabecera[] = $row->c9; $d8 += ($row->c9==""?0:$row->c9);
                    $cabecera[] = $row->c91; $d91 += ($row->c91==""?0:$row->c91);
                    $cabecera[] = $row->c10; $d9 += ($row->c10==""?0:$row->c10);
                    $cabecera[] = $row->c11; $d10 += ($row->c11==""?0:$row->c11);
                    $cabecera[] = $row->c12; $d11 += ($row->c12==""?0:$row->c12);
                    $cabecera[] = $row->c13; $d12 += ($row->c13==""?0:$row->c13);
                    $cabecera[] = $row->c14; $d13 += ($row->c14==""?0:$row->c14);
                    $cabecera[] = $row->c15; $d14 += ($row->c15==""?0:$row->c15);
                    $cabecera[] = $row->c16; $d15 += ($row->c16==""?0:$row->c16);
                    $cabecera[] = $row->c17; $d16 += ($row->c17==""?0:$row->c17);
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->cells('A5:S' . ($c), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setFont(array(
                        'bold' => false,
                    ));
                });
                $sheet->cells('B5:B' . ($c), function ($cells) {
                    $cells->setAlignment('left');
                    $cells->setValignment('left');
                    $cells->setFont(array(
                        'bold' => false,
                    ));
                });
                $sheet->setBorder('A4:S' . ($c), 'thin');
                $sheet->fromArray(null, null, 'A1', false, false)
                    ->getStyle('A4:S' . ($c - 1))
                    ->getAlignment()
                    ->setWrapText(true);
                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = $d1;
                $cabecera[] = $d2;
                $cabecera[] = $d3;
                $cabecera[] = $d4;
                $cabecera[] = $d5;
                $cabecera[] = $d6;
                $cabecera[] = $d7;
                $cabecera[] = $d8;
                $cabecera[] = $d91;
                $cabecera[] = $d9;
                $cabecera[] = $d10;
                $cabecera[] = $d11;
                $cabecera[] = $d12;
                $cabecera[] = $d13;
                $cabecera[] = $d14;
                $cabecera[] = $d15;
                $cabecera[] = $d16;
                $sheet->row($c, $cabecera);
                $sheet->cells('C'. ($c) .':S' . ($c), function ($cells) {
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#FFFF00');
                });
            });
        })->export('xls');
    }

    public function consolidadoDatosClinicosCalculoKTVHBPorPaciente(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 1)
        //->where('historia.baja', '!=', "S")
            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
            ->orderBy(DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, ' ', person.nombres)"));

        $lista = $resultado->get();

        Excel::create("DatosCli" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("DatosCli" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array('A' => 5, 'B' => 40, 'C' => 15, 'D' => 15, 'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10, 'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 40));

                //UNIONES DE CELDAS
                $sheet->mergeCells("A4:A5");
                $sheet->mergeCells("B4:B5");
                $sheet->mergeCells("C4:C5");
                $sheet->mergeCells("D4:K4");
                $sheet->mergeCells("L4:M4");
                $sheet->mergeCells("N4:Q4");
                $sheet->mergeCells("R4:R5");
                $sheet->mergeCells("A6:C6");

                $celdas = 'A2:R2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'A4:R5';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                    $cells->setValignment('center');
                });

                $title   = array();
                $title[] = "CONSOLIDADO DE DATOS CLÍNICOS PARA EL CÁLCULO DE Kt/V, HB Y OTROS POR PACIENTE - " . $mesnombre . " " . $anoo;
                $sheet->row(2, $title);

                $cabecera   = array();
                $cabecera[] = "N°";
                //$cabecera[]="";
                $cabecera[] = "NOMBRE";
                $cabecera[] = "N° DOCUMENTO IDENTIDAD";
                $cabecera[] = "DATOS CLÍNICOS PARA EL CÁCULO DEL KT/V";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "HEMOGLOBINA";
                $cabecera[] = "";
                $cabecera[] = "Otros resultados clínicos";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "OBSERVACIONES";
                $sheet->row(4, $cabecera);

                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "FECHA DE TOMA DE MUESTRA";
                $cabecera[] = "UPOST";
                $cabecera[] = "UPRE";
                $cabecera[] = "TIEMPO DE HD";
                $cabecera[] = "PESO INICIAL";
                $cabecera[] = "PESO FINAL";
                $cabecera[] = "ULTRAFILTRACIÓN";
                $cabecera[] = "PESO SECO";
                $cabecera[] = "HB";
                $cabecera[] = "HTO";
                $cabecera[] = "Calcio sérico";
                $cabecera[] = "Fósforo sérico";
                $cabecera[] = "PTH";
                $cabecera[] = "Albúmina sérica";
                $cabecera[] = "";
                $sheet->row(5, $cabecera);

                $cabecera   = array();
                $cabecera[] = "UNIDADES";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "DD/MM/AÑO";
                $cabecera[] = "mg/dL";
                $cabecera[] = "mg/dL";
                $cabecera[] = "HR";
                $cabecera[] = "KG";
                $cabecera[] = "KG";
                $cabecera[] = "LITROS";
                $cabecera[] = "KG";
                $cabecera[] = "g/dL";
                $cabecera[] = "%";
                $cabecera[] = "mg/dL";
                $cabecera[] = "mg/dL";
                $cabecera[] = "pg/ml";
                $cabecera[] = "g/dL";
                $cabecera[] = "";
                $sheet->row(6, $cabecera);

                $c = 8;

                foreach ($lista as $row) {
                    //BUSCO LA ATENCION DE HEMODIALISIS EN LA QUE SE TOMARON MUESTRAS, LO BUSCO EN LA FUA DE LA CUAL SE TOMARON DATOS MENSUALES Y SACO TIEMPO, PESO PRE Y PESO POST
                    $atencion = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                        ->where("historia.person_id", "=", $row->persona_id)
                        ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $mes)
                        ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anoo)
                        ->where(DB::raw("LENGTH(txtMuestraAnalisis)"), ">", 0)
                        ->where("historiaclinica.estado", "!=", "C")
                        //->where("mensuales2", "=", 1)
                        ->first();
                    $cabecera   = array();
                    $cabecera[] = ($c - 7);
                    $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                    $cabecera[] = $row->dni;
                    $cabecera[] = ($atencion == null ? "" : (date("d/m/Y", strtotime($atencion->fecha_atencion))));
                    $cabecera[] = $row->txtUre2;
                    $cabecera[] = $row->txtUre;
                    $cabecera[] = ($atencion == null ? "" : $atencion->txtHorasHemodialisis);
                    $cabecera[] = ($atencion == null ? "" : $atencion->txtPesoInicial2);
                    $cabecera[] = ($atencion == null ? "" : $atencion->txtPesoFinal2);
                    $cabecera[] = ($atencion == null ? "" : $atencion->txtUltrafiltrado);
                    $cabecera[] = ($atencion == null ? "" : $atencion->txtPesoSeco);
                    $cabecera[] = $row->txtDos;
                    $cabecera[] = $row->txtHem;
                    $cabecera[] = $row->txtCal;
                    $cabecera[] = $row->txtFos;
                    $cabecera[] = $row->txtPar;
                    $cabecera[] = $row->txtAlbu;
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('A4:R' . ($c - 1), 'thin');
                $sheet->cells('A4:R' . ($c - 1), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                $sheet->cells('B8:B' . ($c - 1), function ($cells) {
                    $cells->setAlignment('left');
                });
                $sheet->getStyle('A4:R' . ($c - 1))
                    ->getAlignment()
                    ->setWrapText(true);
            });
        })->export('xls');
    }

    public function consolidadoDuracionSesiones(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 1)
        //->where('historia.baja', '!=', "S")
            ->where('historia.baja', '!=', "S")
            ->join("consultanefrologica as c", "c.persona_id", "=", "person.id")
            ->where(DB::raw("MONTH(c.fecha)"), "=", $mes)
            ->where(DB::raw("YEAR(c.fecha)"), "=", $anoo)
            ->orderBy(DB::raw("CONCAT(person.apellidopaterno, ' ', person.apellidomaterno, ' ', person.nombres)"));

        $lista = $resultado->get();

        Excel::create("DatosCli" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes) {

            $excel->sheet("DatosCli" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes) {

                $sheet->setWidth(array('A' => 5, 'B' => 40, 'C' => 18, 'D' => 18, 'E' => 18, 'F' => 18));

                //UNIONES DE CELDAS
                $sheet->mergeCells("A4:A5");
                $sheet->mergeCells("B4:B5");
                $sheet->mergeCells("C4:C5");
                $sheet->mergeCells("D4:D5");

                $celdas = 'A2:F2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '10',
                        'bold'   => true,
                    ));
                });

                $celdas = 'A4:F5';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '9',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                    $cells->setValignment('center');
                });

                $title   = array();
                $title[] = "CONSOLIDADO DE DURACIÓN DE SESIONES DE HEMODIÁLISIS REALIZADAS - " . $mesnombre . " " . $anoo;
                $sheet->row(2, $title);

                $cabecera   = array();
                $cabecera[] = "N°";
                $cabecera[] = "NOMBRE";
                $cabecera[] = "N° DOCUMENTO IDENTIDAD";
                $cabecera[] = "NÚMERO DE SESIÓN DE HD DEL MES";
                $cabecera[] = "FECHA SESIÓN";
                $cabecera[] = "DURACIÓN";
                $sheet->row(4, $cabecera);

                $cabecera   = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "DD/MM/AÑO";
                $cabecera[] = "HORAS";
                $sheet->row(5, $cabecera);

                $c   = 6;
                $yes = 1;

                foreach ($lista as $row) {
                    //BUSCO LA ATENCION DE HEMODIALISIS EN LA QUE SE TOMARON MUESTRAS, LO BUSCO EN LA FUA DE LA CUAL SE TOMARON DATOS MENSUALES Y SACO TIEMPO, PESO PRE Y PESO POST
                    $atenciones = HistoriaClinica::join("historia", "historiaclinica.historia_id", "=", "historia.id")
                        ->where("historia.person_id", "=", $row->persona_id)
                        ->where(DB::raw("MONTH(historiaclinica.fecha_atencion)"), "=", $mes)
                        ->where(DB::raw("YEAR(historiaclinica.fecha_atencion)"), "=", $anoo)
                        ->orderBy("historiaclinica.fecha_atencion", "ASC")
                        ->where("historiaclinica.estado", "!=", "C")
                        ->get();

                    $yas = 0;
                    foreach ($atenciones as $att) {
                        $cabe = "";
                        if ($yas == 0) {
                            $cabe = $yes;
                            $sheet->cells('A' . $c . ':F' . $c, function ($cells) {
                                $cells->setBackground('#FFF2BD');
                            });
                            $yes++;
                        }
                        $cabecera   = array();
                        $cabecera[] = $cabe;
                        $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                        $cabecera[] = $row->dni;
                        $cabecera[] = (int) $att->nsesion;
                        $cabecera[] = ($att == null ? "" : (date("d/m/Y", strtotime($att->fecha_atencion))));
                        $cabecera[] = $att->txtHorasHemodialisis;
                        $sheet->row($c, $cabecera);
                        $c++;
                        $yas++;
                    }
                }
                $sheet->setBorder('A4:F' . ($c - 1), 'thin');
                $sheet->cells('A4:F' . ($c - 1), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                $sheet->cells('B6:B' . ($c - 1), function ($cells) {
                    $cells->setAlignment('left');
                });
                $sheet->getStyle('A4:F' . ($c - 1))
                    ->getAlignment()
                    ->setWrapText(true);
            });
        })->export('xls');
    }

    public function consultarKardexConsolidado(Request $request)
    {
        date_default_timezone_set('America/Lima');
        //$fechai      = date("Y-m-d", strtotime($request->input("fechai")));
        //$fechaf      = date("Y-m-d", strtotime($request->input("fechaf")));
        $anual       = $request->input("anual");

        $producto_id = $request->input("id");

        $lista = Producto::where("nombre", "LIKE", "%%");
        //$lista = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('movimiento.almacen_id', '=',1)->orderBy('kardex.id', 'DESC');

        if ($producto_id !== "" && $producto_id !== null) {
            $lista = $lista->where('id', '=', $producto_id);
        }

        $lista = $lista->get();

        Excel::create("KardexConsolidado" . $anual, function ($excel) use ($lista, $request, $anual) {
            $excel->sheet("CONSOLIDADO DE KARDEX", function ($sheet) use ($lista, $request, $anual) {

                $sheet->setWidth(array(
                    'A' => 0,
                    'B' => 0,
                    'C' => 80,
                    'D' => 13,
                    'E' => 13,
                    'F' => 13,
                    'G' => 13,
                    'H' => 13,
                    'I' => 13,
                    'J' => 13,
                    'K' => 13,
                    'L' => 13,
                    'M' => 13,
                    'N' => 13,
                    'O' => 13,
                    'P' => 13,
                    'Q' => 13,
                    'R' => 13,
                    'S' => 13,
                    'T' => 13,
                    'U' => 13,
                    'V' => 13,
                    'W' => 13,
                    'X' => 13,
                    'Y' => 13,
                    'Z' => 13,
                    'AA' => 13,
                ));

                $celdas = 'B2:AA2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'D4:AA4';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $celdas = 'B5:AA5';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("A", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("B", function ($cells) {$cells->setAlignment('center');});
                //$sheet->cells("C", function($cells) { $cells->setAlignment('center'); });

                $sheet->setBorder('B4:AA4', 'thin');
                $sheet->setBorder('B5:AA5', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(20);
                $sheet->getRowDimension(5)->setRowHeight(20);

                $title   = array();
                $title[] = "";
                $title[] = strtoupper("KARDEX CONSOLIDADO DE PRODUCTOS - DEL AÑO " . $anual);
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "";
                //$cabecera[]="FECHA VENC.";
                $cabecera[] = "";
                $cabecera[] = "ENERO";
                $cabecera[] = "";
                $cabecera[] = "FEBRERO";
                $cabecera[] = "";
                $cabecera[] = "MARZO";
                $cabecera[] = "";
                $cabecera[] = "ABRIL";
                $cabecera[] = "";
                $cabecera[] = "MAYO";
                $cabecera[] = "";
                $cabecera[] = "JUNIO";
                $cabecera[] = "";
                $cabecera[] = "JULIO";
                $cabecera[] = "";
                $cabecera[] = "AGOSTO";
                $cabecera[] = "";
                $cabecera[] = "SETIEMBRE";
                $cabecera[] = "";
                $cabecera[] = "OCTUBRE";
                $cabecera[] = "";
                $cabecera[] = "NOVIEMBRE";
                $cabecera[] = "";
                $cabecera[] = "DICIEMBRE";
                $cabecera[] = "";
                $cabecera[] = "";
                $sheet->row(4, $cabecera);

                $sheet->mergeCells('D4:E4');
                $sheet->mergeCells('F4:G4');
                $sheet->mergeCells('H4:I4');
                $sheet->mergeCells('J4:K4');
                $sheet->mergeCells('L4:M4');
                $sheet->mergeCells('N4:O4');
                $sheet->mergeCells('P4:Q4');
                $sheet->mergeCells('R4:S4');
                $sheet->mergeCells('T4:U4');
                $sheet->mergeCells('V4:W4');
                $sheet->mergeCells('X4:Y4');
                $sheet->mergeCells('Z4:AA4');

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "";
                //$cabecera[]="FECHA VENC.";
                $cabecera[] = "PRODUCTO";
                for ($aaa=0; $aaa < 12; $aaa++) { 
                    $cabecera[] = "ENTRADA";
                    $cabecera[] = "SALIDA";
                }                
                $cabecera[] = "";
                $sheet->row(5, $cabecera);

                $c = 6;

                //$sheet->getColumnDimension('A')->setWidth(100);
                foreach ($lista as $producto) {

                    $cabecera = array();
                    $cabecera[] = "";
                    $cabecera[] = "";
                    //$cabecera[]=date("d-m-Y", strtotime($row->fechavencimiento));
                    $cabecera[] = $producto->nombre;

                    for ($uu=1; $uu < 13; $uu++) { 

                        //ARMAMOS ESTRUCTURA DE SOLO ENTRADAS Y SALIDAS

                        $kardexs = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')
                            ->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')
                            ->join("lote", "lote.id", "=", "kardex.lote_id")
                            ->where('movimiento.almacen_id', '=', 1)
                            ->where("detallemovimiento.producto_id", "=", $producto->id)
                            ->where(DB::raw('YEAR(movimiento.fecha)'), "=", $anual)
                            ->where(DB::raw('MONTH(movimiento.fecha)'), "=", $uu)
                            ->orderBy('kardex.id', 'ASC')
                            ->groupBy('movimiento.id', 'kardex.tipo', 'detallemovimiento.producto_id')
                            ->select("movimiento.fecha", "lote.fechavencimiento", "movimiento.comentario", "kardex.tipo", DB::raw("SUM(kardex.cantidad) as cant"), "kardex.stockanterior")
                            ->get();

                        $ingresos = 0;
                        $salidas = 0;

                        if (count($kardexs) > 0) {
                            foreach ($kardexs as $row) {
                                if($row->tipo == "I") { $ingresos+=$row->cant; }
                                if($row->tipo !== "I") { $salidas+=$row->cant; }
                            }
                        } else {
                            $ingresos   = 0;
                            $salidas = 0;
                        }

                        //AGREGO FILA
                        //$cabecera[]=($c-4);
                        $cabecera[] = $ingresos;
                        $cabecera[] = $salidas;
                    }
                    $sheet->row($c, $cabecera);
                    $c++;

                    //FIN LLENADO DE FILA
                    $sheet->setBorder('C5:AA' . ($c - 1), 'thin');
                }
            });

        })->export('xls');
    }

    public function reporteregistroaccesovascular(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        $mesnombre2 = $mes==12?"ENERO":$this->meses[$mes+1];
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 1)
            //->where(DB::raw("MONTH(historia.created_at)"), "=", $mes)
            //->where(DB::raw("YEAR(historia.created_at)"), "=", $anoo)
            ->select("person.nombres", "person.apellidopaterno", "person.apellidomaterno", "person.dni", "historia.created_at")
            ->orderBy("person.apellidopaterno", "ASC")
            ->orderBy("person.apellidomaterno", "ASC")
            ->orderBy("person.nombres", "ASC");

        $lista = $resultado->get();

        Excel::create("RegAccVasc", function ($excel) use ($lista, $request, $mesnombre, $mesnombre2, $anoo, $mes) {

            $excel->sheet("RegAccVasc", function ($sheet) use ($lista, $request, $mesnombre, $mesnombre2, $anoo, $mes) {

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 5,
                    'C' => 20,
                    'D' => 20,
                    'E' => 60,
                    'F' => 20,
                    'G' => 20,
                    'H' => 20,
                    'I' => 20,
                    'J' => 20,
                    'K' => 20,
                    'L' => 20,
                    'M' => 20,
                    'N' => 20,
                    'O' => 20,
                    'P' => 20,
                    'Q' => 20,
                    'R' => 20,
                    'S' => 20,
                    'T' => 20,
                    'U' => 20,
                    'V' => 20,
                    'W' => 20,
                    'X' => 20,
                ));

                //UNIÓN DE CELDAS
                $sheet->mergeCells('B4:B5');
                $sheet->mergeCells('C4:C5');
                $sheet->mergeCells('D4:D5');
                $sheet->mergeCells('E4:E5');
                $sheet->mergeCells('F4:I4');
                $sheet->mergeCells('J4:J5');
                $sheet->mergeCells('K4:K5');
                $sheet->mergeCells('L4:L5');
                $sheet->mergeCells('M4:M5');
                $sheet->mergeCells('N4:N5');
                $sheet->mergeCells('O4:O5');
                $sheet->mergeCells('P4:P5');
                $sheet->mergeCells('U4:X4');
                $sheet->mergeCells('Q4:T4');

                $celdas = 'B2:X2';
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas = 'B4:X5';
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("B", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("C", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("D", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("F", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("G", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("H", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("I", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("J", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("K", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("L", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("M", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("N", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("O", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("P", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("R", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("S", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("T", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("U", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("V", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("W", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("X", function ($cells) {$cells->setAlignment('center');});

                $sheet->setBorder('B4:X4', 'thin');

                $sheet->getRowDimension(4)->setRowHeight(50);
                $sheet->getRowDimension(5)->setRowHeight(60);

                //$sheet->getColumnDimension('A')->setWidth(100);

                $title   = array();
                $title[] = "";
                $title[] = "REGISTRO DE ACCESO VASCULAR";
                $sheet->row(2, $title);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "N°";
                $cabecera[] = "FECHA DE INGRESO A LA UPSS";
                $cabecera[] = "DNI/CE";
                $cabecera[] = "APELLIDOS Y NOMBRES";
                $cabecera[] = "UBICACIÓN DE ACCESO VASCULAR";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "ACCESOS VASCULARES ANTERIORES/FECHA DE CREACIÓN O INSTALACIÓN";
                $cabecera[] = "OBSERVACIONES DE ACCESOS VASCULARES ANTERIORES";
                $cabecera[] = "ACCESOS VASCULARES DE INICIO/UBICACIÓN";
                $cabecera[] = "Qb PROMEDIO";
                $cabecera[] = "CARACTERÍSTICAS DEL ACCESO VASCULAR AL INGRESO A LA HEMODIÁLISIS";
                $cabecera[] = "FECHA DE CREACIÓN O INSTALACIÓN DEL ACCESO VASCULAR";
                $cabecera[] = "FECHA DE INICIO DE LA FÍSTULA ARTERIOVENOSA";
                $cabecera[] = $mesnombre;
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = $mesnombre2;
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $sheet->row(4, $cabecera);

                $cabecera = array();
                //$cabecera[]="N°";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "FAV";
                $cabecera[] = "CVCT";
                $cabecera[] = "CVCP";
                $cabecera[] = "INJERTO";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "TIPO DE ACCESO VASCULAR DE CAMBIO/FECHA";
                $cabecera[] = "CAUSA DE CAMBIO";
                $cabecera[] = "MEDIDA CORRECTIVA/FECHA";
                $cabecera[] = "CAUSA DE MEDIDA CORRECTIVA";
                $cabecera[] = "TIPO DE ACCESO VASCULAR DE CAMBIO/FECHA";
                $cabecera[] = "CAUSA DE CAMBIO";
                $cabecera[] = "MEDIDA CORRECTIVA/FECHA";
                $cabecera[] = "CAUSA DE MEDIDA CORRECTIVA";
                $sheet->row(5, $cabecera);

                $c            = 6;
                $edadpaciente = "-";

                foreach ($lista as $row) {
                    $cabecera = array();
                    $cabecera[] = "";
                    $cabecera[]=($c-5);   
                    $cabecera[] = date("d-m-Y", strtotime($row->created_at));                 
                    $cabecera[] = $row->dni;                    
                    $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $cabecera[] = "";
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('B5:X' . ($c - 1), 'thin');
            });
        })->export('xls');
    }
    
    public function reportecontroldescartefiltro(Request $request)
    {
        date_default_timezone_set('America/Lima');
        $mes       = (int) $request->input("mes");
        $anoo      = $request->input("anno");
        $mesnombre = $this->meses[$mes];
        //CALCULO LOS DÍAS DE ESTE MES
        $dias_en_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anoo);
        //dd(date("w", strtotime($anoo."-" . $mes . "-10")));
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
            ->where('historia.convenio_id', '=', 1)
            //->where(DB::raw("MONTH(fecha)"), "=", $mes)
            //->where(DB::raw("YEAR(fecha)"), "=", $anoo)
            ->select("person.nombres", "person.apellidopaterno", "person.apellidomaterno", "ordencitas", "historia.created_at")
            ->orderBy("person.apellidopaterno", "ASC")
            ->orderBy("person.apellidomaterno", "ASC")
            ->orderBy("person.nombres", "ASC");

        $lista = $resultado->get();

        Excel::create("DescFiltro" . $mesnombre . $anoo, function ($excel) use ($lista, $request, $mesnombre, $anoo, $mes, $dias_en_mes) {

            $excel->sheet("DescFiltro" . $mesnombre . $anoo, function ($sheet) use ($lista, $request, $mesnombre, $anoo, $mes, $dias_en_mes) {

                $diitas = array("D","L","M","M","J","V","S");

                $sheet->setWidth(array(
                    'A' => 5,
                    'B' => 5,
                    'C' => 40,
                    'D' => 15,
                    'E' => 10,
                    'F' => 10,
                    'G' => 10,
                    'H' => 10,
                    'I' => 10,
                    'J' => 10,
                    'K' => 10,
                    'L' => 10,
                    'M' => 10,
                    'N' => 10,
                    'O' => 10,
                    'P' => 10,
                    'Q' => 10,
                    'R' => 10,
                    'S' => 10,
                    'T' => 10,
                    'U' => 10,
                    'V' => 10,
                    'W' => 10,
                    'X' => 10,
                    'Y' => 10,
                    'Z' => 10,
                    'AA' => 10,
                    'AB' => 10,
                    'AC' => 10,
                    'AD' => 10,
                    'AE' => 10,
                    'AF' => 10,
                    'AG' => 10,
                    'AH' => 10,
                    'AI' => 10,
                ));

                if($dias_en_mes == 28) {
                    $celdas = 'B2:AF2';
                }
                if($dias_en_mes == 29) {
                    $celdas = 'B2:AG2';
                }
                if($dias_en_mes == 30) {
                    $celdas = 'B2:AH2';
                }
                if($dias_en_mes == 31) {
                    $celdas = 'B2:AI2';
                }
                $sheet->mergeCells($celdas);
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    //$cells->setBorder('thin','thin','thin','thin');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '15',
                        'bold'   => true,
                    ));
                });

                $celdas_dias = "";
                $letra_fin = "";

                if($dias_en_mes == 28) {
                    $celdas = 'B4:AF4';
                    $celdas_dias = "E4:AF4";
                    $letra_fin = "AF";
                }
                if($dias_en_mes == 29) {
                    $celdas = 'B4:AG4';
                    $celdas_dias = "E4:AG4";
                    $letra_fin = "AG";
                }
                if($dias_en_mes == 30) {
                    $celdas = 'B4:AH4';
                    $celdas_dias = "E4:AH4";
                    $letra_fin = "AH";
                }
                if($dias_en_mes == 31) {
                    $celdas = 'B4:AI4';
                    $celdas_dias = "E4:AI4";
                    $letra_fin = "AI";
                }
                $sheet->cells($celdas, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFont(array(
                        'family' => 'Calibri',
                        'size'   => '11',
                        'bold'   => true,
                    ));
                    $cells->setBackground('#5CC2C4');
                });

                $sheet->cells("B", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("D", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("E", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("F", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("G", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("H", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("I", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("J", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("K", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("L", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("M", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("N", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("O", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("P", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("Q", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("R", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("S", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("T", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("U", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("V", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("W", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("X", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("Y", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("Z", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AA", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AB", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AC", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AD", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AE", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AF", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AG", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AH", function ($cells) {$cells->setAlignment('center');});
                $sheet->cells("AI", function ($cells) {$cells->setAlignment('center');});

                $sheet->setBorder($celdas, 'thin');

                $sheet->getRowDimension(4)->setRowHeight(20);

                //UNIÓN DE CELDAS
                $sheet->mergeCells('B4:B6');
                $sheet->mergeCells('C4:C6');
                $sheet->mergeCells('D4:D6');

                $sheet->mergeCells($celdas_dias);

                $title   = array();
                $title[] = "";
                $title[] = "CONTROL DE DESCARTE DE FILTRO -  " . $mesnombre . " DEL " . $anoo;
                $sheet->row(2, $title);

                $cabecera = array();
                $cabecera[] = "";
                $cabecera[] = "N°";
                $cabecera[] = "PACIENTE";
                $cabecera[] = "SECUENCIA";
                $cabecera[] = "DIA DE LA SEMANA";
                $sheet->row(4, $cabecera);

                $cabecera = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                for ($io = 1; $io <= $dias_en_mes; $io++) {
                    $fechita = $anoo . "-" . $mes . "-" . $io;
                    $num_dia = date("w", strtotime($fechita));
                    $cabecera[] = $diitas[$num_dia];
                }                
                $sheet->row(5, $cabecera);

                $cabecera = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                for ($io = 1; $io <= $dias_en_mes; $io++) { 
                    $cabecera[] = $io;
                }                
                $sheet->row(6, $cabecera);

                $c            = 7;
                $edadpaciente = "-";

                foreach ($lista as $row) {
                    //LLENO LA SECUENCIA
                    $ordencitas = $row->ordencitas;
                    $a_ordencitas = explode(";", $ordencitas);
                    $c_ordencitas = "";
                    foreach ($a_ordencitas as $aoc) {
                        if($aoc!=="") {
                            $c_ordencitas .= $diitas[$aoc]. "-";
                        }                        
                    }
                    $c_ordencitas = substr($c_ordencitas, 0, (strlen($c_ordencitas)-1));
                    $cabecera = array();
                    $cabecera[] = "";
                    $cabecera[] = ($c-6);
                    $cabecera[] = $row->apellidopaterno . " " . $row->apellidomaterno . " " . $row->nombres;
                    $cabecera[] = $c_ordencitas;
                    $cabecera[] = "";
                    $sheet->row($c, $cabecera);
                    $c++;
                }
                $sheet->setBorder('B4:' . $letra_fin . ($c - 1), 'thin');
            });
        })->export('xls');
    }

    public function consultarStockNumero(Request $request)
    {
        $error = DB::transaction(function() use($request,&$dat){
            $producto_id = $request->input("id");
            $dat=array("cantidad"=>"-");
            if ($producto_id !== "" && $producto_id !== null) {
                $ultimokardex = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('movimiento.almacen_id', '=', 1)->orderBy('kardex.id', 'DESC')->where("producto_id", "=", $producto_id)->first();
                $dat=array("cantidad"=>"0");
                if ($ultimokardex !== null) {
                    $dat=array("cantidad"=>$ultimokardex->stockactual);
                }
            }
        });
        return is_null($error) ? json_encode($dat) : $error;
    }
}
