<?php

session_start();
header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/admin.php");
require_once("../../models/inventarios.php");
require_once("../../models/contabilidad.php");
$dompdf = new Dompdf();
$admin = new Admin();
$inventarios = new Inventarios();
$conta = new Contabilidad();
$params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_REQUEST['idEmpresas'];
$params['idCompra'] = $_REQUEST['idCompra'];
$getEmpresa = $admin->getEmpresas($params);
$getCompra = $inventarios->getCompra($_REQUEST['idCompra']);
$getCompraDetalle = $inventarios->getCompraDetalle($params);
$params['idFormato'] = $getCompra['idFormato'];
$getFormatoDetalle = $conta->getFormatoDetalle($params);
$total1 = 0;
$total2 = 0;
$html = '
<html>
    <head>
    <title>Compra</title>
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
                <td><b>Empresa:</b> ' . $getEmpresa[0]['razonSocial'] . '</td>
                <td align="right"><b>Compra #.</b> ' . $getCompra['idDocumentosCorrelativos'] . ' - ' . $getCompra['correlativo'] . '</td>
            </tr>
            <tr>
                <td><b>NIT:</b> ' . $getEmpresa[0]['nit'] . '</td>
                <td align="right"><b>Fecha y Hora Ingreso:</b> ' . $getCompra['created_at'] . '</td>
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
                <h2>Información de Compra</h2>
        </center>
        <table width="100%">
        <tr>
            <td><b>Estado Compra</b></td>
            <td><b>Fecha de Factura</b></td>
            <td><b>Fecha de Pago</b></td>
            <td><b>Sub-Total</b></td>
            <td><b>Exento</b></td>
        </tr>
        <tr>
            <td>' . $getCompra['idTipoCompra'] . '</td>
            <td>' . $getCompra['fechaFactura'] . '</td>
            <td>' . $getCompra['fechaPago'] . '</td>
            <td>' . $getCompra['subTotal'] . '</td>
            <td>' . $getCompra['exento'] . '</td>
        </tr>
        <tr>
            <td><b>Descuento</b></td>
            <td><b>Descuento %</b></td>
            <td><b>Total</b></td>
            <td><b>Iva</b></td>
            <td><b>Proveedor</b></td>
        </tr>
        <tr>
            <td>' . $getCompra['descuentoM'] . '</td>
            <td>' . $getCompra['descuentoP'] . '</td>
            <td>' . $getCompra['total'] . '</td>
            <td>' . $getCompra['iva'] . '</td>
            <td>' . $getCompra['idProveedores'] . '</td>
        </tr>
        <tr>
            <td colspan="4"><b>Concepto de Compra</b></td>
        </tr>
        <tr>
            <td colspan="4">' . $getCompra['conceptoCompra'] . '</td>
        </tr>        
        </table>
        <br/>
        <table width="100%" style="font-size:0.7em !important;">
            <tr>
                <td align="center" colspan="9" style="border: 1px solid black; font-weight:bold;">Listado de Productos de Compra</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; font-weight:bold;">No.</td>
                <td style="border: 1px solid black; font-weight:bold;">No. Orden Compra</td>
                <td style="border: 1px solid black; font-weight:bold;">Codigo</td>
                <td style="border: 1px solid black; font-weight:bold;">Descripcion</td>
                <td style="border: 1px solid black; font-weight:bold;" align="right">Cantidad</td>
                <td style="border: 1px solid black; font-weight:bold;" align="right">Precio</td>
                <td style="border: 1px solid black; font-weight:bold;" align="right">Descuento</td>
                <td style="border: 1px solid black; font-weight:bold;" align="right">Descuento %</td>
                <td style="border: 1px solid black; font-weight:bold;" align="right">Total</td>
            </tr>';
foreach ($getCompraDetalle as $key => $value) {
    $total += $value['total'];
    $html .= '<tr>';
    $html .= '<td style="border: 1px solid black;">' . ($key + 1) . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $value['idComprasOrdenes'] . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $value['sku'] . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $value['descLarga'] . '</td>';
    $html .= '<td style="border: 1px solid black;" align="right">' . $value['cantidad'] . '</td>';
    $html .= '<td style="border: 1px solid black;" align="right">' . $value['precioCompra'] . '</td>';
    $html .= '<td style="border: 1px solid black;" align="right">' . $value['descuentoM'] . '</td>';
    $html .= '<td style="border: 1px solid black;" align="right">' . $value['descuentoP'] . '</td>';
    $html .= '<td style="border: 1px solid black;" align="right">' . $value['total'] . '</td>';
    $html .= '</tr>';
}
$html .= '<tr>';
$html .= '<td style="border: 1px solid black; font-weight:bold;" colspan="8">Totales</td>';
$html .= '<td style="border: 1px solid black; font-weight:bold;" align="right">' . number_format($total, 2) . '</td>';
$html .= '</tr>';
$html .= '</table><br/>';
$html .= '<table width="100%" style="font-size:0.7em !important; border: 1px solid black !important;">';
$html .= '<tr>';
$html .= '<td colspan="4" align="center" style="border: 1px solid black; font-weight:bold;">Partida Contable</td>';
$html .= '</tr>';
$html .= '<tr style="border: 1px solid black; font-weight:bold;">';
$html .= '<td align="left">Cuenta Contable</td>';
$html .= '<td align="right">Debe</td>';
$html .= '<td align="right">Haber</td>';
$html .= '<td align="center">Centro Costo</td>';
$html .= '</tr>';
$totalDebe = 0;
$totalHaber = 0;
foreach ($getFormatoDetalle as $key => $value) {
    $totalDebe += $getCompra[strtolower($value['debe'])];
    $totalHaber += $getCompra[strtolower($value['haber'])];
    $html .= '<tr>';
    $html .= '<td align="left">' . $value['cuentaContable'] . '</td>';
    $html .= '<td align="right">' . $getCompra[strtolower($value['debe'])] . '</td>';
    $html .= '<td align="right">' . $getCompra[strtolower($value['haber'])] . '</td>';
    $html .= '<td align="center" style="border-right: 1px solid black;">' . $value['centroCosto'] . '</td>';
    $html .= '</tr>';
}
$html .= '<tr style="border: 1px solid black; font-weight:bold;">';
$html .= '<td align="left">Totales</td>';
$html .= '<td align="right">' . number_format($totalDebe, 2) . '</td>';
$html .= '<td align="right">' . number_format($totalHaber, 2) . '</td>';
$html .= '<td align="center">-</td>';
$html .= '</tr>';
$html .= '</table>';
if ($_REQUEST['print'] == '1') {
    $html .= '<script type="text/javascript">
                this.print();
            </script>';
}
$html .= '</body>
</html>';
//echo $html;
$dompdf->set_paper("Letter", "landscape");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$dompdf->stream('ordenCompra_' . $getRequisicion['documento'] . '.pdf', array('Attachment' => 0));
?>