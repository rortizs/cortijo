<?php

session_start();
header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/admin.php");
require_once("../../models/inventarios.php");
$dompdf = new Dompdf();
$admin = new Admin();
$inventarios = new Inventarios();
$params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_REQUEST['idEmpresas'];
$params['idRequisicion'] = $_REQUEST['idRequisicion'];
$getEmpresa = $admin->getEmpresas($params);
$getRequisicion = $inventarios->getRequisicion($_REQUEST['idRequisicion']);
$getRequisicionDetalle = $inventarios->getRequisicionDetalle($params);
$html = '
<html>
    <head>
    <title>Requisición de Compra</title>
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
                <td align="right"><b>Requisición #.</b> ' . $getRequisicion['documento'] . '</td>
            </tr>
            <tr>
                <td><b>NIT:</b> ' . $getEmpresa[0]['nit'] . '</td>
                <td align="right"><b>Fecha:</b> ' . $getRequisicion['created_at'] . '</td>
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
                <h2>Requisición de Compra</h2>
        </center>
        <table width="100%">
        <tr>
            <td><b>Solicitado Por</b></td>
            <td><b>Departamento</b></td>
            <td><b>Realizado Por</b></td>
            <td><b>Estado</b></td>
        </tr>
        <tr>
            <td>' . $getRequisicion['solicitadoPor'] . '</td>
            <td>' . $getRequisicion['idHrmDepartamentos'] . '</td>
            <td>' . $getRequisicion['idUsuarios'] . '</td>
            <td>' . $getRequisicion['status'] . '</td>
        </tr>
        <tr>
            <td colspan="4"><b>Observaciones</b></td>
        </tr>
        <tr>
            <td colspan="4">' . $getRequisicion['observaciones'] . '</td>
        </tr> 
        </table>
        <br/>
        <table width="100%">
            <tr>
                <td align="center" colspan="5" style="border: 1px solid black; font-weight:bold;">Listado de Productos de Requisicion</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; font-weight:bold;">No.</td>
                <td style="border: 1px solid black; font-weight:bold;">Codigo</td>
                <td style="border: 1px solid black; font-weight:bold;">Descripcion</td>
                <td style="border: 1px solid black; font-weight:bold;">Unidad de Medida</td>
                <td style="border: 1px solid black; font-weight:bold;">Cantidad</td>
            </tr>';
foreach ($getRequisicionDetalle as $key => $value) {
    $html .= '<tr>';
    $html .= '<td style="border: 1px solid black;">' . ($key + 1) . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $value['codigo'] . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $value['descProducto'] . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $value['unidadMedida'] . '</td>';
    $html .= '<td style="border: 1px solid black;">' . $value['cantidad'] . '</td>';
    $html .= '</tr>';
}
$html .= '</table>';
$html .= '<br/><br/><br/><br/><br/><br/>'
        . '<table width="100%">';
$html .= '<tr>';
$html .= '<td align="center"><hr/></td>'
        . '<td align="center"><hr/></td>'
        . '<td align="center"><hr/></td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center">Firma Solicitante</td>'
        . '<td align="center">Firma Autorización 1</td>'
        . '<td align="center">Firma Autorización 2</td>';
$html .= '</tr>';
$html .= '</table>';
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
$canvas = $dompdf->get_canvas();
$font = Font_Metrics::get_font("helvetica", "12");
$canvas->page_text(500, 770, "Pagina {PAGE_NUM} - {PAGE_COUNT}", $font, 10, array(0, 0, 0));
$canvas->page_text(20, 770, 'Fecha y Hora impresión '.date('d-m-Y H:i:s'), $font, 10, array(0, 0, 0));
$dompdf->stream('requisicionCompra_' . $getRequisicion['documento'] . '.pdf', array('Attachment' => 0));
?>