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
$dias = $planilla->dateDiff(date("Y-m-d", strtotime($_POST['fechaInicio'])), date("Y-m-d", strtotime($_POST['fechaFin'])));
$params['idHrmDepartamentos'] = $_REQUEST['idDepartamentos'];
$params['idEmpleados'] = $_REQUEST['idEmpleados'];
$getEmpleados = $planilla->getHrmEmpleados($params, $_SESSION['idEmpresa']);

//
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
    <title>Reporte Detalle de Horas Extras</title>
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
            <h4>
                Reporte Detalle de Horas Extras<br/>
                <small>
                    De ' . $_REQUEST['fechaInicio'] . ' al ' . $_REQUEST['fechaFin'] . '<br/>
                    Departamento(s): ' . $_REQUEST['txtDepartamentos'] . ' | Empleado(s): ' . $_REQUEST['txtEmpleados'] . '
                </small>
            </h4>
        <table style="width:100% !important;">
            <thead style="background-color:#CCC !important;">
                <tr>
                    <td class="text-center" colspan="' . ($dias + 4) . '" style="border: 1px solid black; font-weight:bold;">
                        De ' . $_REQUEST['fechaInicio'] . ' al ' . $_REQUEST['fechaFin'] . '
                    </td>
                </tr>
                <tr class="info">
                    <td style="border: 1px solid black; font-weight:bold;">Empleado</td>
                    <td style="border: 1px solid black; font-weight:bold;">Departamento</td>';
for ($i = date("Y-m-d", strtotime($fechaInicio)); $i <= date("Y-m-d", strtotime($fechaFin)); $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
    $html .= '<td style="border: 1px solid black; font-weight:bold;">' . date("d-m-Y", strtotime($i)) . '<br/>' . nombreDia(date("l", strtotime($i))) . '</td>';
}
$html .= '<td align="center" style="border: 1px solid black; font-weight:bold;">Total<br/>Horas</td>';
$html .= '</tr></thead>';
for ($a = 0; $a < count($getEmpleados); $a++) {
    $totalDiurnas = 0;
    $totalNocturnas = 0;
    $html .= '<tr>';
    $html .= '<td style="border: 1px solid black;">' . $getEmpleados[$a]['codigoEmpleado'] . ' - ' . strtoupper($getEmpleados[$a]['nombreEmpleado']) . '</td>';
    $html .= '<td style="border: 1px solid black;">' . strtoupper($getEmpleados[$a]['departamento']) . '</td>';
    for ($i = date("Y-m-d", strtotime($fechaInicio)); $i <= date("Y-m-d", strtotime($fechaFin)); $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
        $marcaje = $planilla->horaMarcaje($getEmpleados[$a]['codigoEmpleado'], $i, $_SESSION['idEmpresa']);
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
        $totalDiurnas += ($hrsExtrasDiurnas < 0 ? 0 : $hrsExtrasDiurnas);
        $totalNocturnas += ($hrsExtrasNocturnas < 0 ? 0 : $hrsExtrasNocturnas);
        $html .= '<td align="center" style="border: 1px solid black;">';
        $html .= ($marcaje['horaEntrada'] ?: 0) . '<br/>';
        $html .= ($marcaje['horaSalida'] ?: 0);
        $html .= '<hr/>';
        $html .= ($hrsExtrasDiurnas < 0 ? 0 : $hrsExtrasDiurnas) . '<br/>';
        $html .= ($hrsExtrasNocturnas < 0 ? 0 : $hrsExtrasNocturnas);
        $html .= '</td>';
    }
    $html .= '<td align="center" style="border: 1px solid black;">';
    $html .= $totalDiurnas . '<br/>';
    $html .= $totalNocturnas;
    $html .= '</td>';
}
$html .= '</table></center></body>
</html>';
//echo $html;
$dompdf->set_paper("Legal", "landscape");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$canvas = $dompdf->get_canvas();
$font = Font_Metrics::get_font("helvetica", "12");
$canvas->page_text(900, 1, "Pagina {PAGE_NUM} - {PAGE_COUNT}", $font, 10, array(0, 0, 0));
$dompdf->stream('Reporte Detalle de Horas Extras.pdf', array('Attachment' => 0));
