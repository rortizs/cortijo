<?php
session_start();
require_once("../../models/flujosBovedas.php");
$formArqueoBoveda = flujosBovedas::formArqueoBoveda();
?>
<table class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <td>Fecha</td>
            <?php
            foreach ($formArqueoBoveda as $key => $value) {
                ?>
                <td><?= $value['descripcion']; ?><br/><?= $value['operaciones']; ?></td>
                <?php
            }
            ?>
            <td>Movimiento De Bóveda</td>
            <td>Net Win</td>
            <td>Fundación Pediátrica</td>
            <td>Facturación Del Mes</td>
            <td>Comparación Bóveda</td>
        </tr>
    </thead>
    <tbody>
        <?php
        $fecha1 = "2017-05-01";
        $fecha2 = "2017-05-31";
        $deuda = 0;
        for ($i = $fecha1; $i <= $fecha2; $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
            $movBoveda = 0;
            $winGeneral = 0;
            $netWin = 0;
            $fp = 0;
            $facMes = 0;
            $comparacion = 0;
            $licencia = 4729.60;
            ?>
            <tr>
                <td><?= $i; ?></td>
                <?php
                foreach ($formArqueoBoveda as $key => $value) {
                    $arqueoDetalle = flujosBovedas::arqueoDetalle($value['id'], $i);
                    if ($value['operacion'] == '1') {
                        $total += str_replace(",", "", $arqueoDetalle['valor']);
                    } else {
                        $total -= str_replace(",", "", $arqueoDetalle['valor']);
                    }
                    if ($value['id'] == 7 || $value['id'] == 8 || $value['id'] == 9 || $value['id'] == 10 || $value['id'] == 22 || $value['id'] == 23) {
                        if ($value['operacion'] == '1') {
                            $netWin += str_replace(",", "", $arqueoDetalle['valor']);
                        } else {
                            $netWin -= str_replace(",", "", $arqueoDetalle['valor']);
                        }
                    }
                    ?>
                    <td align="right"><?= $value['operaciones']; ?><?= $arqueoDetalle['valor']; ?></td>
                    <?php
                }
                if ($netWin < 1) {
                    $fp = 0;
                    $deuda += $licencia;
                } else if ($netWin < ($licencia + $deuda)) {
                    $fp = 0;
                    $deuda += $licencia;
                } else {
                    $fp = $licencia + $deuda;
                    $deuda = 0;
                }
                ?>
                <td align="right"><?= number_format($total, 2); ?></td>
                <td align="right"><?= number_format($netWin, 2); ?></td>
                <td align="right"><?= number_format($fp, 2); ?></td>
                <td align="right"><?= number_format($netWin - $fp, 2); ?></td>
                <td align="right"><?= number_format($comparacion, 2); ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>