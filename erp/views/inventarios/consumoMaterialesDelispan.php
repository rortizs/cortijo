<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
require_once("../../models/inventarios.php");
$inventarios = new Inventarios();
$getProductosMateriaPrima = $inventarios->getProductosMateriaPrima($_REQUEST);
//print_r($getProductosMateriaPrima);
?>
<table class="table table-striped table-bordered" cellspacing="0" width="100%">
    <tr>
        <td>FECHA</td>
        <td>TIPO MOVIMIENTO</td>
        <?php
        foreach ($getProductosMateriaPrima as $key => $value) {
            ?>
            <td><?= $value['sku']; ?>-<?= $value['descLarga']; ?></td>
            <?php
        }
        ?>
    </tr>
    <?php
    $getConsumoMateriaPrima = $inventarios->getConsumoMateriaPrima($_REQUEST);
    foreach ($getConsumoMateriaPrima as $key => $value) {
        ?>
        <tr>
            <td><?= $value['fecha']; ?></td>
            <td><?= $value['tipoMovimiento']; ?></td>
        </tr>
        <?php
    }
    ?>
</table>
