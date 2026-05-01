<?php

session_start();
header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/admin.php");
require_once ("../../models/reportes.php");
$dompdf = new Dompdf();
$admin = new Admin();
$reportes = new Reportes();
$dias = $_REQUEST['dias'];
$params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
$getEmpresa = $admin->getEmpresas($params);
$reporteCobros = $reportes->reporteCobros($dias);
$totalClientes = count($reporteCobros);
$saldoTotal = 0;
$html = '
<html>
    <head>
    <title>Cuentas Por Cobrar</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
        }
        table {
            border-collapse: collapse;
        }

        table, th, td {
            border-bottom: 1px solid black;
            font-size:0.9em !important;
        }
    </style>
    </head>
    <body>
        <table width="100%">
            <tr>
                <td><b>' . $getEmpresa[0]['razonSocial'] . '</b></td>
            </tr>
            <tr>
                <td><b>NIT:</b> ' . $getEmpresa[0]['nit'] . '</td>
            </tr>
            <tr>
                <td><b>Dirección:</b> ' . $getEmpresa[0]['direccion'] . '</td>
            </tr>
            </tr>
            <tr>
                <td><b>Telefonos:</b> ' . $getEmpresa[0]['telefono'] . '</td>
            </tr>
        </table>
        <center>  
            <h2>
                Reporte Cuentas Por Cobrar<br/>
                <small>Cliente con Credito de: ' . $dias . ' dias</small>
            </h2>
        </center>
        <table width="100%">
            <tr>
                <td style="font-weight:bold; text-transform:uppercase;">Nombre</td>
                <td style="font-weight:bold; text-transform:uppercase;">Direccion</td>
                <td style="font-weight:bold; text-transform:uppercase;"># Cuenta</td>
                <td style="font-weight:bold; text-transform:uppercase;">Telefono</td>
                <td style="font-weight:bold; text-transform:uppercase;">Saldo Actual</td>
                <td style="font-weight:bold; text-transform:uppercase;">Fecha Ultimo Abono</td>
                <td style="font-weight:bold; text-transform:uppercase;">Dias en Mora</td>
            </tr>';
foreach ($reporteCobros as $key => $value) {
    $saldoTotal += $value['saldo_actual'];
    $html .= '<tr>';
    $html .= '<td>' . $value['nombre'] . '</td>';
    $html .= '<td>' . $value['direccion'] . '</td>';
    $html .= '<td>' . $value['nit'] . '</td>';
    $html .= '<td>' . $value['telefono'] . '</td>';
    $html .= '<td>' . number_format($value['saldo_actual'], 2) . '</td>';
    $html .= '<td>' . $value['fecha_ultimo_abono'] . '</td>';
    $html .= '<td>' . $value['diasMora'] . '</td>';
    $html .= '</tr>';

}
$html .= '
            <tr>
                <td colspan="3" align="left" style="font-weight:bold; text-transform:uppercase;">Total Clientes: ' . $totalClientes . '</td>
                <td align="left" style="font-weight:bold; text-transform:uppercase;">Saldo Total:</td>
                <td>' . number_format($saldoTotal, 2) . '</td>
                <td colspan="2" align="right"></td>
            </tr>
                <tr>
                <td colspan="7" align="right"><b>Fecha de Generación:</b> ' . $reportes->timestampViews . '</td>
            </tr>
        </table>
    </body>
</html>';
//echo $html;
$dompdf->set_paper("Legal", "Landscape");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$dompdf->stream('ReporteCobros.pdf', array('Attachment' => 0));
?>