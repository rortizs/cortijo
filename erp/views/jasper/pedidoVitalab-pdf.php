<?php

header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/facturas.php");
$dompdf = new Dompdf();
$facturas = new Facturas();
$idPedido = $_REQUEST['idPedido'];
$getPedido = $facturas->getPedido($idPedido);
$getEmpresa = $facturas->getEmpresa($getPedido['idSucursales']);
$getDetallePedido = $facturas->getDetallePedido($idPedido);
$html = '
<html>
    <head>
    <title>Pedido</title>
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
                <td>' . $getEmpresa['nombreComercial'] . '</td>
                <td align="right">Pedido #. ' . $getPedido['documento'] . '</td>
            </tr>
            <tr>
                <td>' . $getEmpresa['nit'] . '</td>
                <td align="right">Fecha: ' . $getPedido['created_at'] . '</td>
            </tr>
            <tr>
                <td>' . $getEmpresa['direccion'] . '</td>
                <td align="right">' . $getPedido['resolucionSAT'] . '</td>
            </tr>
            </tr>
            <tr>
                <td>' . $getEmpresa['telefono'] . '</td>
            </tr>
        </table>
        <br/>
        <br/>
        <table width="100%">
            <tr>
                <td><b>Pedido a nombre de:</b></td>
            </tr>
            <tr>
                <td>Nombre: ' . $getPedido['nombre'] . '</td>
            </tr>
            <tr>
                <td>NIT: ' . $getPedido['nit'] . '</td>
            </tr>
            <tr>
                <td>Direccion: ' . $getPedido['direccion'] . '</td>
            </tr>
        </table>
        <center>
            <h3>Detalle Pedido</h3>
        </center>
        <table width="100%">
        <tr>
            <td style="text-align:center; width:10%;">CANTIDAD</td>
            <td style="text-align:center; width:60%;">DESCRIPCION</td>
            <td style="text-align:right; width:15%;">COSTO UNITARIO</td>
            <td style="text-align:right; width:15%;">TOTAL</td>
        </tr>';
foreach ($getDetallePedido as $key => $value) {
    $html .= '<tr>';
    $html .= '<td align="center">' . $value['cantidad'] . '</td>';
    $html .= '<td align="center">' . $value['sku'] . '-' . $value['descCorta'] . '</td>';
    $html .= '<td style="text-align:right;">' . $value['costoUnitario'] . '</td>';
    $html .= '<td style="text-align:right;">' . $value['total'] . '</td>';
    $html .= '</tr>';
}
$html .= '
        <tr>
            <td colspan="2">
            </td>
            <td style="text-align:left;">
                TOTAL
            </td>
            <td style="text-align:right;">' . number_format($getPedido['total'], 2) . '</td>
        </tr>
        </table>';
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
$dompdf->stream('pedido.pdf', array('Attachment' => 0));
?>