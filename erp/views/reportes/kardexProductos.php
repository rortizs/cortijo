<?php
/** KARDEX PRODUCTOS
 * 
 */
session_start();
require_once("../../models/reportes.php");
$reportes = new Reportes();
$kardex = $reportes->kardex($_REQUEST, $_SESSION['idEmpresa']);
?>
<address class="text-center">
    <strong>Reporte de Kardex <?= $_REQUEST['ingresoATxt']; ?></strong><br>
    Inventario de: <?= $_REQUEST['idPuntoIngresoTxt']; ?><br>
    Fecha de Generacion: <?= date('d-m-Y'); ?>
</address>
<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <td>Fecha</td>
            <td>Documento</td>
            <td>Inventario De</td>
            <td>Lugar</td>
            <td>Serie</td>
            <td></td>
            <td>Ingresos</td>
            <td>Salidas</td>
            <td>Saldo</td>
            <td>Costo</td>
            <td>Unidad de Medida</td>
            <td>Observaciones</td>
        </tr>
    </thead>
    <tbody>
        <?php
        for ($i = 0; $i < count($kardex); $i++) {
            ?>
            <tr class="<?= $kardex[$i]['class']; ?>">
                <td><?= $kardex[$i]['created_at']; ?></td>
                <td><?= $kardex[$i]['documento']; ?></td>
                <td><?= $kardex[$i]['ingresoA']; ?></td>
                <td><?= $kardex[$i]['idPuntoIngreso']; ?></td>
                <td><?= $kardex[$i]['serie']; ?></td>
                <td><?= $kardex[$i]['sku']; ?></td>
                <td class="ingresos"><?= $kardex[$i]['ingreso']; ?></td>
                <td class="salidas"><?= $kardex[$i]['salida']; ?></td>
                <td><?= $kardex[$i]['saldo']; ?></td>
                <td><?= $kardex[$i]['costo']; ?></td>
                <td><?= $kardex[$i]['unidadMedida']; ?></td>
                <td><?= $kardex[$i]['observaciones']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <thead>
        <tr>
            <td colspan="5">TOTALES</td>
            <td id="totalIngresos"></td>
            <td id="totalSalidas"></td>
            <td colspan="5"></td>
        </tr>
    </thead>
</table> 
