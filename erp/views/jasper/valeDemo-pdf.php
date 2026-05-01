<?php

header('Content-Type: text/html; charset=utf-8');
require_once ("../../models/librerias/dompdf060/dompdf_config.inc.php");
require_once ("../../models/facturas.php");
$dompdf = new Dompdf();
$facturas = new Facturas();
$idVale = $_REQUEST['idVale'];
$getVale = $facturas->getVale($idVale);
$html = '
<html>
    <head>
    <title>Vale Demo</title>
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
            <td align="left">' . $getVale['empresa'] . '</td>
            <td align="right">Fecha de Solicitud<br/>' . $getVale['fechaVale'] . '</td>
        </tr>
        <tr>
            <td>' . $getVale['nit'] . '</td>
        </tr>
        <tr>
            <td>' . $getVale['direccion'] . '</td>
        </tr>
        <br/>
        <br/>
        <br/>
        </table>
        <center>
                <h2>Vale de Caja</h2>
        </center>
        <table width="100%">
        <tr>
            <td style="width:20% !important;"><b>Solicitado Por:</b></td>
            <td>' . $getVale['solicitado'] . '</td>
        </tr>
        <tr>
            <td><b>La suma de:</b></td>
            <td>Q. ' . number_format($getVale['monto'],2) . '</td>
        </tr>
        <tr>
            <td><b>Observaciones:</b></td>
            <td>' . $getVale['observaciones'] . '</td>
        </tr>
        <tr>     
            <td><b>Sucursal Pagadora:</b></td>
            <td>' . $getVale['sucursal'] . '</td>
        </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <table width="100%">
        <tr>
            <td align="center"><b>Firma Solicitante</b></td>
            <td align="center"><b>Firma Pagador</b></td>
        </tr>
        </table>
        <script type="text/javascript">
            this.print();
        </script>
    </body>
</html>';
$dompdf->set_paper("Letter", "portrait");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$dompdf->stream('valeDemo.pdf', array('Attachment' => 0));
?>