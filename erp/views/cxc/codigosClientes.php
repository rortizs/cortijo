<?php
session_start();
require_once("../../models/admin.php");
$admin = new Admin();
$getCodigosClientes = $admin->getCodigosClientes();
//print_r($getCodigosClientes);
?>
<table>
    <?php
    foreach ($getCodigosClientes as $key => $value) {
        ?>
        <tr>
            <td><?=$value['nombre'];?></td>
            <td><?=$value['nit'];?></td>
            <td><img src="http://www.codigos-qr.com/barcode/barcode.processor.php?encode=CODE128&bdata=<?=$value['nit'];?>"/></td>
        </tr>
        <?php
    }
    ?>
</table>