<?php
session_start();
require_once ("../../models/facturas.php");
require_once ("../../models/caja.php");
require_once ("../../models/reportes.php");
$facturas = new Facturas();
$caja = new Caja();
$reportes = new Reportes();
$getEmpresa = $facturas->getEmpresa($_SESSION['idSucursalesS']);
$getCorte = $caja->getCorte($_REQUEST['idCorte']);
$getVales = $caja->getVales($getCorte['idUsuarios'], $getCorte['idSucursalesS'], $getCorte['fechaCorte']);
$getTotalVentasTJDetalle = $caja->getTotalVentasTJDetalle($getCorte['idSucursales'], $getCorte['idUsuarios'], $getCorte['fechaCorte']);
$getTotalVentasCheques = $caja->getTotalVentasChequesDetalle($getCorte['idSucursales'], $getCorte['idUsuarios'], $getCorte['fechaCorte']);
$getTotalVentaExencionDetalle = $caja->getTotalVentaExencionDetalle($getCorte['idSucursales'], $getCorte['idUsuarios'], $getCorte['fechaCorte']);
$params['fechaInicio'] = $getCorte['fechaCorte'];
$params['fechaFin'] = $getCorte['fechaCorte'];
$params['idCajero'] = $getCorte['idUsuarios'];
$params['idSucursales'] = $getCorte['idSucursales'];
$params['idEmpresas'] = $getCorte['idEmpresas'];
$getVentas = $reportes->consultaFacturas($params);
//print_r($getVentas);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
            @page {
                size: auto;
                margin: 10%;
            }
            body{
                font-family: Arial, Helvetica, sans-serif;
                -webkit-print-color-adjust:exact;
            }
            table {
                border-collapse: collapse;
                font-size:0.8em !important;
            }
        </style>
    </head>
    <table width='100%'>
        <tr>
            <td>
                <label style="text-transform: uppercase; font-size: 1.5em !important;"><?= $getEmpresa['razonSocial']; ?></label><br/>
                <label style="text-transform: uppercase;"><?= $getEmpresa['direccion']; ?></label><br/>
                <label style="text-transform: uppercase;"><?= $getEmpresa['nit']; ?></label><br/>
            </td>
            <td style="text-align: right;">
                <label style="text-transform: uppercase;">Fecha de Cierre: <?= $getCorte['fechaCorte']; ?></label><br/>
                <label style="text-transform: uppercase;">Fecha y Hora de Ejecucion: <?= $getCorte['created_at']; ?></label>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <label style="text-transform: uppercase; font-size: 1.5em !important;">Corte de Caja</label><br/>
                <label style="text-transform: uppercase;">Sucursal:</label> <?= $_SESSION['nombreSucursal']; ?> <br/>
                <label style="text-transform: uppercase;">Cajero:</label> <?= $_SESSION['userName']; ?>
            </td>
        </tr>    
    </table>
    <br/>
    <table border='1'>
        <tr>
            <td colspan="2" style="background-color: #CCC;">
                <label style="text-transform: uppercase; font-size: 1.2em !important;">Resumen de Corte</label><br/>
            </td>
        </tr>
        <!--
        <tr>
            <td>Tasa Cambio</td>
            <td></td>
        </tr>
        -->
        <tr>
            <td>Fondo de Caja</td>
            <td style="text-align: right;"><?= number_format($getCorte['fondoCaja'], 2); ?></td>
        </tr>
        <tr>
            <td>Vales</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalVales'], 2); ?></td>
        </tr>
        <tr>
            <td>Efectivo en Caja</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalEfectivo'], 2); ?></td>
        </tr>
        <tr>
            <td>Efectivo en Caja Dolares</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalEfectivoDolares'], 2); ?></td>
        </tr>
        <tr>
            <td>Total exenciones</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalExenciones'], 2); ?></td>
        </tr>
        <tr>
            <td>Total Cheques</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalCheques'], 2); ?></td>
        </tr>
        <tr>
            <td>Vouchers</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalVouchers'], 2); ?></td>
        </tr>
        <tr>
            <td>Total Corte</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalCorte'], 2); ?></td>
        </tr>
        <tr>
            <td>Total Ventas (Contado)</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalVentasContado'], 2); ?></td>
        </tr>
        <tr>
            <td>Total Ventas (Credito)</td>
            <td style="text-align: right;"><?= number_format($getCorte['totalVentasCredito'], 2); ?></td>
        </tr>
        <tr>
            <td>Diferencia</td>
            <td style="text-align: right;"><?= number_format($getCorte['diferencia'], 2); ?></td>
        </tr>
    </table>
    <br/>
    <table border='1'>
        <tr>
            <td colspan="3" style="background-color: #CCC;">
                <label style="text-transform: uppercase; font-size: 1.2em !important;">Detalle Vales</label><br/>
            </td>
        </tr>
        <tr>
            <td>Solicitado Por</td>
            <td>Motivo</td>
            <td style="text-align: right;">Valor</td>
        </tr>
        <?php
        $total03 = 0;
        foreach ($getVales as $key => $value) {
            $total03 += $value['monto'];
            ?>
            <tr>
                <td><?= $value['solicitado']; ?></td>
                <td><?= $value['observaciones']; ?></td>
                <td style="text-align: right;"><?= number_format($value['monto'], 2); ?></td>
            </tr>
            <?php
        }
        ?>
        <tr style="background-color: #CCC;">
            <td colspan="2">Totales</td>
            <td style="text-align: right;"><?= number_format($total03, 2); ?></td>
        </tr>
    </table>
    <br/>
    <table border='1'>
        <tr>
            <td colspan="3" style="background-color: #CCC;">
                <label style="text-transform: uppercase; font-size: 1.2em !important;">Detalle Transacciones Tarjeta Credito/Debito</label><br/>
            </td>
        </tr>
        <tr>
            <td>Emisor</td>
            <td>Autorización</td>
            <td style="text-align: right;">Valor</td>
        </tr>
        <?php
        $total01 = 0;
        foreach ($getTotalVentasTJDetalle as $key => $value) {
            $total01 += $value['valor'];
            ?>
            <tr>
                <td><?= $value['emisor']; ?></td>
                <td><?= $value['auth']; ?></td>
                <td style="text-align: right;"><?= number_format($value['valor'], 2); ?></td>
            </tr>
            <?php
        }
        ?>
        <tr style="background-color: #CCC;">
            <td colspan="2">Totales</td>
            <td style="text-align: right;"><?= number_format($total01, 2); ?></td>
        </tr>
    </table>
    <br/>
    <table border='1'>
        <tr>
            <td colspan="3" style="background-color: #CCC;">
                <label style="text-transform: uppercase; font-size: 1.2em !important;">Detalle Exenciones</label><br/>
            </td>
        </tr>
        <tr>
            <td>Autorización</td>
            <td style="text-align: right;">Valor</td>
        </tr>
        <?php
        $total02 = 0;
        foreach ($getTotalVentaExencionDetalle as $key => $value) {
            $total02 += $value['valor'];
            ?>
            <tr>
                <td><?= $value['auth']; ?></td>
                <td style="text-align: right;"><?= number_format($value['valor'], 2); ?></td>
            </tr>
            <?php
        }
        ?>
        <tr style="background-color: #CCC;">
            <td>Totales</td>
            <td style="text-align: right;"><?= number_format($total02, 2); ?></td>
        </tr>
    </table>
    <br/>
    <table border='1' width='100%'>
        <tr>
            <td colspan="10" style="background-color: #CCC;">
                <label style="text-transform: uppercase; font-size: 1.2em !important;">Detalle Transacciones</label><br/>
            </td>
        </tr>
        <tr style="background-color: #CCC;">
            <td>Documento</td>
            <td>Tipo Venta</td>
            <td>NIT</td>
            <td>Nombre Cliente</td>
            <td>Cajero</td>
            <td>Vendedor</td>
            <td>Status</td>
            <td style="text-align: right;">Sub Total</td>
            <td style="text-align: right;">Iva</td>
            <td style="text-align: right;">Total</td>
        </tr>
        <?php
        $total1 = 0;
        $total2 = 0;
        $total3 = 0;
        foreach ($getVentas as $key => $value) {
            $total1 += $value['subtotal'];
            $total2 += $value['iva'];
            $total3 += $value['total'];
            ?>
            <tr>
                <td><?= $value['documento']; ?></td>
                <td><?= $value['tipoVenta']; ?></td>
                <td><?= $value['nit']; ?></td>
                <td><?= $value['nombreCliente']; ?></td>
                <td><?= $value['cajero']; ?></td>
                <td><?= $value['vendedor']; ?></td>
                <td><?= $value['estatus']; ?></td>
                <td style="text-align: right;"><?= number_format($value['subtotal'], 2); ?></td>
                <td style="text-align: right;"><?= number_format($value['iva'], 2); ?></td>
                <td style="text-align: right;"><?= number_format($value['total'], 2); ?></td>
            </tr>
            <?php
        }
        ?>
        <tr style="background-color: #CCC;">
            <td colspan="7">Totales</td>
            <td style="text-align: right;"><?= number_format($total1, 2); ?></td>
            <td style="text-align: right;"><?= number_format($total2, 2); ?></td>
            <td style="text-align: right;"><?= number_format($total3, 2); ?></td>
        </tr>
    </table>
    <?php
    if ($_REQUEST['print'] == '1') {
        echo'<script type="text/javascript">this.print();</script>';
    }
    ?>
</html>