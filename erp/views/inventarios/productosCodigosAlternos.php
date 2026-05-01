<?php
/**
 * POS /Traslados desde modulo de administracion
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
require_once ("../../models/inventarios.php");
$inventarios = new Inventarios();
$flag = $_REQUEST['flag'] ?: '';
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$message = "";
switch ($flag) {
    case '1':
        $saveCodigoAlterno = $inventarios->saveCodigoAlterno($_REQUEST);
        $message = $saveCodigoAlterno;
        break;
    case '2':
        $updateCodigoAlterno = $inventarios->updateCodigoAlterno($_REQUEST);
        $message = $updateCodigoAlterno;
        break;
    case '3':
        $deleteCodigoAlterno = $inventarios->deleteCodigoAlterno($_REQUEST);
        $message = $deleteCodigoAlterno;
        break;
}
$getProductosCodigosAlternos = $inventarios->getProductosCodigosAlternos($_REQUEST['idProductos'], $_SESSION['idEmpresa']);
?>
<?= $message; ?>
<section class="panel">
    <div class="panel-body">
        <center>
            <table class="table table-bordered table-striped" style="width: 354px !important;">
                <tr>
                    <td>
                        <button style="height: 30px !important;" class="btn btn-success btn-xs" onclick="saveCodigoAlterno('<?= $value['idProducto']; ?>', '<?= $value['sku']; ?>', '<?= $value['descLarga']; ?>', '<?= $value['idMedidas']; ?>', '<?= $value['medida']; ?>', '<?= $value['costo']; ?>', '<?= $value['unidades']; ?>');">
                            <i class="fa fa-floppy-o"></i> Guardar
                        </button>
                    </td>
                    <td><input value="<?= $value['sku']; ?>" id="skuNuevo" class="form-control input-sm"/></td>
                </tr>
            </table>
        </center>
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr class="info text-uppercase" style="font-weight: bold;">
                    <td></td>
                    <td>Codigo</td>
                    <td>Fecha de Creacion</td>
                    <td>Fecha de Actualizacion</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($getProductosCodigosAlternos as $key => $value) {
                    $status = "";
                    if ($key === 0) {
                        $status = "disabled='disabled'";
                    }
                    ?>
                    <tr>
                        <td>
                            <button class="btn btn-warning btn-xs" onclick="updateCodigoAlterno('<?= $value['id']; ?>');" <?=$status;?>>
                                <i class="fa fa-refresh"></i>
                            </button>
                            &nbsp;
                            <button class="btn btn-danger btn-xs" onclick="deleteCodigoAlterno('<?= $value['id']; ?>');" <?=$status;?>>
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                        <td><input value="<?= $value['sku']; ?>" id="sku-<?= $value['id']; ?>" class="form-control input-sm" <?=$status;?>/></td>
                        <td><?= $value['created_at']; ?></td>
                        <td><?= $value['updated_at']; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</section>