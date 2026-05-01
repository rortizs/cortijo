<?php
require_once("../../models/flujosBovedas.php");

function lastDayMonth($mes) {
    $month = $mes;
    $year = date('Y');
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('d', mktime(0, 0, 0, $month, $day, $year));
}

function addZero($number) {
    $numero = "";
    if (strlen((string) $number) == 1) {
        $numero = "0" . $number;
    } else {
        $numero = $number;
    }
    return $numero;
}

$saldoInicial = flujosBovedas::saldoInicialMes(date('m'));
?>
<div class="text-center"> 
    <h4>Reporte Cuadro de Faltantes (Cuentas por Cobrar Empleados)</h4>
    <p>Sucursal: <br/>
        Mes:</p>
</div>
<table class="table table-bordered table-striped">
    <tr class="info text-uppercase">
        <td>Fecha</td>
        <td>Monto</td>
        <td>Empleado</td>
    </tr>
    <?php
    $lastDayMonth = lastDayMonth(date('m'));
    $total1=0;
    for ($i = 1; $i <= $lastDayMonth; $i++) {
        $dia=addZero($i).'-'.date('m').'-'.date('Y');
        ?>
        <tr>
            <td><?= $dia ?></td>
            <td align="right">0.00</td>
            <td align="left">-</td>
        </tr>
    <?php
}
?>
    <tr class="info">
        <td>Totales</td>
        <td align="right"><?= number_format($total1,2);?></td>
        <td align="center">-</td>
    </tr>    
</table>
