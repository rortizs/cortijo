<?php

header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/facturas.php");
$dompdf = new Dompdf();
$facturas = new Facturas();
$idVenta = $_REQUEST['idVenta'];
$getVenta = $facturas->getVenta($idVenta);
$getEmpresa = $facturas->getEmpresa($getVenta['idSucursales']);
$getDetalleVenta = $facturas->getDetalleVenta($idVenta);
$html = '
<html>
    <head>
    <title>Factura Demo</title>
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
                <td>' . $getEmpresa['razonSocial'] . '</td>
                <td align="right">Factura #. ' . $getVenta['serie'] . '-' . $getVenta['correlativo'] . '</td>
            </tr>
            <tr>
                <td>' . $getEmpresa['nit'] . '</td>
                <td align="right">Fecha: ' . $getVenta['created_at'] . '</td>
            </tr>
            <tr>
                <td>' . $getEmpresa['direccion'] . '</td>
                <td align="right">' . $getVenta['resolucionSAT'] . '</td>
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
                <td><b>Facturar A</b></td>
            </tr>
            <tr>
                <td>Nombre: ' . $getVenta['nombre'] . '</td>
            </tr>
            <tr>
                <td>NIT: ' . $getVenta['nit'] . '</td>
            </tr>
            <tr>
                <td>Direccion: ' . $getVenta['direccion'] . '</td>';
if ($_REQUEST['reimpresion'] == '1') {
    $html .= '<td>Reimpresión de Factura</td>';
}
$html .= '</tr>
        </table>
        <center>
                <h2>Detalle Factura</h2>
        </center>
        <table width="100%">
        <tr>
            <td style="text-align:left; width:10%;">CANTIDAD</td>
            <td style="text-align:left; width:60%;">DESCRIPCION</td>
            <td style="text-align:right; width:15%;">COSTO UNITARIO</td>
            <td style="text-align:right; width:15%;">TOTAL</td>
        </tr>';
foreach ($getDetalleVenta as $key => $value) {
    $html .= '<tr>';
    $html .= '<td>' . $value['cantidad'] . '</td>';
    $html .= '<td>' . $value['sku'] . '-' . $value['descCorta'] . '</td>';
    $html .= '<td style="text-align:right;">' . $value['costoUnitario'] . '</td>';
    $html .= '<td style="text-align:right;">' . $value['total'] . '</td>';
    $html .= '</tr>';
}
$html .= '
        <tr>
            <td colspan="2">
            </td>
            <td style="text-align:left;">
                SUBTOTAL
            </td>
            <td style="text-align:right;">' . $getVenta['subtotal'] . '</td>
        </tr>
        <tr>
            <td colspan="2">
            </td>
            <td style="text-align:left;">
                DESCUENTO
            </td>
            <td style="text-align:right;">' . $getVenta['descuentoM'] . '</td>
        </tr>
        <tr>
            <td colspan="2">
            </td>
            <td style="text-align:left;">
                TOTAL
            </td>
            <td style="text-align:right;">' . $getVenta['total'] . '</td>
        </tr>
        <tr>
            <td colspan="2">
            </td>
            <td style="text-align:left;">
                CAMBIO
            </td>
            <td style="text-align:right;">' . $getVenta['cambio'] . '</td>
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
$dompdf->stream('factura.pdf', array('Attachment' => 0));
?>