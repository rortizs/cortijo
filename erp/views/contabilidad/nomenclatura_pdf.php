<?php

session_start();
header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/admin.php");
require_once("../../models/contabilidad.php");
$dompdf = new Dompdf();
$admin = new Admin();
$conta = new Contabilidad();
$params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_REQUEST['idEmpresas'];
$getEmpresa = $admin->getEmpresas($params);
$getNomenclatura = $conta->getNomenclatura($_SESSION['nombreEmpresa']);
//
$html = '
<html>
    <head>
    <title>Nomenclatura Contable</title>
    <style>
    body{
        font-family: Arial, Helvetica, sans-serif;
    }
    table {
        border-collapse: collapse;
        font-size:0.7em !important;
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
                <h2>Nomenclatura Contable</h2>
        </center>
        <table width="100%">
            <tr style="border: 1px solid black; font-weight:bold;">
                <td style="border-top: 1px solid black; border-bottom: 1px solid black; font-weight:bold;"><b>Cuenta</b></td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black; font-weight:bold;"><b>Nivel</b></td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black; font-weight:bold;"><b>Nombre</b></td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black; font-weight:bold;"><b>Padre</b></td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black; font-weight:bold;"><b>Tipo Cuenta</b></td>
                <td style="border-top: 1px solid black; border-bottom: 1px solid black; font-weight:bold;"><b>Tipo de Operación</b></td>
            </tr>';
foreach ($getNomenclatura as $key => $value) {
    $html .= '<tr>';
    $html .= '<td>'.$value['cuenta'].'</td>';
    $html .= '<td>'.$value['nivel'].'</td>';
    $html .= '<td>'.$value['descripcion'].'</td>';
    $html .= '<td>'.$value['padre'].'</td>';
    $html .= '<td>'.$value['tipoCuenta'].'</td>';
    $html .= '<td>'.$value['idTipoOperacionContable'].'</td>';
    $html .= '</tr>';
}
$html .= ' </table>';
if ($_REQUEST['print'] == '1') {
    $html .= '<script type="text/javascript">
                this.print();
            </script>';
}
$html .= '</body>
</html>';
//echo $html;
$dompdf->set_paper("Letter", "portrait");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$canvas = $dompdf->get_canvas();
$font = Font_Metrics::get_font("helvetica", "12");
$canvas->page_text(520, 1, "Pagina {PAGE_NUM} - {PAGE_COUNT}", $font, 10, array(0, 0, 0));
$dompdf->stream('libroMayor.pdf', array('Attachment' => 0));
?>