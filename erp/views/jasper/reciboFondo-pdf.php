<?php

header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/caja.php");
$dompdf = new Dompdf();
$caja = new Caja();
$idFondo = $_REQUEST['idFondo'];
$getFondoCaja = $caja->getFondoCaja($idFondo);
$html = '
<html>
    <head>
    <title>Recibo Recepcion Fondos</title>
    <style>
    body{
        font-family: Arial, Helvetica, sans-serif;
    }
    table {
        border-collapse: collapse;
        font-size:0.8em !important;
    }
    </style>
    </head>
    <body>
        <table width="100%">
        <tr>
            <td align="left">' . $getFondoCaja['razonSocial'] . '</td>
            <td align="right"><b>Fecha de Generación:</b></td>
        </tr>
        <tr>
            <td>' . $getFondoCaja['nit'] . '</td>
            <td align="right">' . $getFondoCaja['fecha'] . '</td>
        </tr>
        <tr>
            <td>' . $getFondoCaja['direccion'] . '</td>
        </tr>
        <tr>
            <td>' . $getFondoCaja['telefono'] . '</td>
        </tr>
        <br/>
        <br/>
        <br/>
        </table>
        <center>
            <h3>Recibo Recepción Fondo de Caja</h3>
        </center>
        <p align="justify">
            Yo, ' . $getFondoCaja['empleado'] . ' recibi hoy ' . date('d-m-Y') . ' la cantida de: Q.' . number_format($getFondoCaja['monto'], 2) . ' como fondo de caja para operaciones en la sucursal ' . $getFondoCaja['sucursal'] . '.
        </p>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <table width="100%">
        <tr>
            <td align="center"><b>Firma Recepcion</b></td>
            <td align="center"><b>Firma Supervisor</b></td>
        </tr>
        </table>';
if ($_REQUEST['print'] == '1') {
    $html .= '<script type="text/javascript">
                this.print();
            </script>';
}
$html .= '</body>
</html>';
$dompdf->set_paper("Letter", "portrait");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$dompdf->stream('reciboRecepcionFondos.pdf', array('Attachment' => 0));
?>