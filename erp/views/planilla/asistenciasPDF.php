<?php

session_start();
header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/admin.php");
require_once ("../../models/planilla.php");
$dompdf = new Dompdf();
$admin = new Admin();
$planilla = new Planilla();
$params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_REQUEST['idEmpresas'];
$getEmpresa = $admin->getEmpresas($params);
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$idDepartamentos = $_POST['idDepartamentos'];
$idEmpleados = $_POST['idEmpleados'];
$getDepartamentos = $planilla->getHrmDepartamentos($_SESSION['idEmpresa']);

function nombreDia($dia) {
    switch ($dia) {
        case 'Monday':
            return 'Lunes';
            break;
        case 'Tuesday':
            return 'Martes';
            break;
        case 'Wednesday':
            return 'Miercoles';
            break;
        case 'Thursday':
            return 'Jueves';
            break;
        case 'Friday':
            return 'Viernes';
            break;
        case 'Saturday':
            return 'Sabado';
            break;
        case 'Sunday':
            return 'Domingo';
            break;
    }
}

//
$html = '
<html>
    <head>
    <title>Reporte de Marcaje por Empleado</title>
    <style>
    body{
        font-family: Arial, Helvetica, sans-serif;
    }
    table {
        border-collapse: collapse;
        font-size:0.6em !important;
    }
    </style>
    </head>
    <body>
        <table width="100%">
            <tr>
                <td><b>Empresa:</b> ' . $getEmpresa[0]['razonSocial'] . '</td>
                <td align="right"><b>Fecha y Hora Impresión:</b></td>
            </tr>
            <tr>
                <td><b>NIT:</b> ' . $getEmpresa[0]['nit'] . '</td>
                <td align="right">' . date('d-m-Y H:i:s') . '</td>
            </tr>
            <tr>
                <td><b>Dirección:</b> ' . $getEmpresa[0]['direccion'] . '</td>
            </tr>
            </tr>
            <tr>
                <td><b>Teléfonos:</b> ' . $getEmpresa[0]['telefono'] . '</td>
            </tr>
        </table>
        <center>
            <h3>
                Reporte de Marcaje por Empleado
            </h3>
        <table style="width:100% !important;">
            <thead style="background-color:#CCC !important;">
                <tr>
                    <td colspan="7" style="border: 1px solid black; font-weight:bold;">Empleado: ' . $_REQUEST['nombreEmpleado'] . '</td>
                    <td colspan="2" style="border: 1px solid black; font-weight:bold;">Periodo:<br/> ' . $fechaInicio . ' al ' . $fechaFin . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; font-weight:bold;">Fecha</td>
                    <td style="border: 1px solid black; font-weight:bold;">Dia</td>
                    <td style="border: 1px solid black; font-weight:bold;">Turno</td>
                    <td style="border: 1px solid black; font-weight:bold;">Hora Entrada</td>
                    <td style="border: 1px solid black; font-weight:bold;">Hora Salida</td>
                    <td style="border: 1px solid black; font-weight:bold;">Hrs. Ext. Diurnas</td>
                    <td style="border: 1px solid black; font-weight:bold;">Hrs. Ext. Nocturnas</td>
                    <td style="border: 1px solid black; font-weight:bold;" colspan="2">Observaciones</td>
                </tr>
            </thead>';
$totalDiurnas = 0;
$totalNocturnas = 0;
for ($i = date("Y-m-d", strtotime($fechaInicio)); $i <= date("Y-m-d", strtotime($fechaFin)); $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
    $marcaje = $planilla->horaMarcaje($idEmpleados, $i, $_SESSION['idEmpresa']);
    $hrsExtrasDiurnas = 0;
    $hrsExtrasNocturnas = 0;
    $horaEntrada = $marcaje['horaEntrada'] ?: '--';
    $horaSalida = $marcaje['horaSalida'] ?: '--';
    $style = "";
    $turno = "";
    if ($marcaje['turno'] != '') {
        if ($marcaje['horaEntrada'] != '' && $marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaSalida'] != '') {
            if ($marcaje['dia'] == 'Friday') {
                $hrsExtrasNocturnas = ($planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']) - 2);
            } else {
                $hrsExtrasNocturnas = ($planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']) - 1);
            }
        } else if ($marcaje['horaEntrada'] != '' && $marcaje['horaSalidaTurno'] != '18:00:00' && $marcaje['horaSalida'] > $marcaje['horaSalidaTurno']) {
            $hrsExtrasDiurnas = $planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']);
        }
        if ($marcaje['horaEntrada'] == $marcaje['horaSalida'] || $marcaje['horaEntrada'] == '' || $marcaje['horaSalida'] == '') {
            $style = "style='color:blue;'";
            $hrsExtrasDiurnas = 0;
            $turno = "MARCAJE INCOMPLETO";
        } else if ($marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaSalida'] > '08:00:00') {
            $style = "style='color:blue;'";
            $hrsExtrasNocturnas = 0;
            $turno = "MARCAJE INCOMPLETO";
        } else if ($marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaEntrada'] < '17:00:00') {
            $style = "style='color:blue;'";
            $hrsExtrasNocturnas = 0;
            $turno = "MARCAJE INCOMPLETO";
        } else {
            $style = "style='color:green;'";
            $turno = $marcaje['turno'];
        }
    } else if ($marcaje['dia'] == 'Saturday' || $marcaje['dia'] == 'Sunday') {
        $style = "style='color:black;'";
        $turno = "";
        $horaEntrada = "";
        $horaSalida = "";
    } else if ($marcaje['turno'] == '' && $marcaje['horaEntrada'] != $marcaje['horaSalida']) {
        $style = "style='color:red;'";
        $turno = "SIN TURNO DEFINIDO";
    } else if ($marcaje['turno'] == '' && $marcaje['horaEntrada'] == $marcaje['horaSalida'] && $marcaje['horaEntrada'] != '' && $marcaje['horaSalida'] != '') {
        $style = "style='color:blue;'";
        $turno = "SIN TURNO DEFINIDO";
        $hrsExtrasDiurnas = 0;
    }
    $totalDiurnas += $hrsExtrasDiurnas;
    $totalNocturnas += $hrsExtrasNocturnas;
    $html .= '<tr>';
    $html .= '<td style="border: 1px solid black;">' . date("d-m-Y", strtotime($i)) . '</td>';
    $html .= '<td style="border: 1px solid black;">' . nombreDia($marcaje['dia']) . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $turno . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $horaEntrada . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $horaSalida . '</td>';
    $html .= '<td style="border: 1px solid black;" align="center">' . ($hrsExtrasDiurnas * 1) . '</td>';
    $html .= '<td style="border: 1px solid black;" align="center">' . ($hrsExtrasNocturnas * 1) . '</td>';
    $html .= '<td style="border: 1px solid black;" colspan="2"></td>';
    $html .= '</tr>';
}
$html .= '<thead style="background-color:#CCC !important">';
$html .= '<tr>';
$html .= '<td style="border: 1px solid black; font-weight:bold;" colspan="5" align="right">Total de Horas</td>';
$html .= '<td style="border: 1px solid black; font-weight:bold;" align="center">' . $totalDiurnas . '</td>';
$html .= '<td style="border: 1px solid black; font-weight:bold;" align="center">' . $totalNocturnas . '</td>';
$html .= '<td style="border: 1px solid black;" colspan="2"></td>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '</table></center></body>
</html>';
//echo $html;
$dompdf->set_paper("Letter", "portrait");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$canvas = $dompdf->get_canvas();
$font = Font_Metrics::get_font("helvetica", "12");
$canvas->page_text(520, 1, "Pagina {PAGE_NUM} - {PAGE_COUNT}", $font, 10, array(0, 0, 0));
$dompdf->stream('Reporte de Marcaje por Empleado.pdf', array('Attachment' => 0));
