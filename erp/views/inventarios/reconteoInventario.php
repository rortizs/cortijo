<?php
session_start();
require_once("../../models/inventarios.php");
$inventarios = new Inventarios();
$getProductos = $inventarios->getProductos();
?>
<table>
    <tr>
        <td>Query</td>
    </tr>
    <?php
    foreach ($getProductos as $key => $productos) {
        ?>
        <tr>
            <td><?= $productos['producto']; ?></td>
        </tr>
        <?php
        $getInventario = $inventarios->getInventario($productos['id']);
        $saldo = 0;
        foreach ($getInventario as $key => $value) {
            $sql = "";
            $saldo += ($value['ingreso'] - $value['salida']);
            $sql = "update inventarios set saldo='" . $saldo . "' where id=" . $value['id'] . ";";
            $updateSaldosInventario = $inventarios->updateSaldosInventario($sql);
            ?>
            <tr>
                <td>
                    <?= $updateSaldosInventario; ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>
