<?php
/**
 * POS /Traslados desde modulo de administracion
 * @author Jonathan Juarez
 * @version 1.0 20160205
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
$getProductosEquivalencias = $inventarios->getProductosEquivalencias($_REQUEST['idProductos'], $_SESSION['idEmpresa']);
$getMedidas = $inventarios->getMedidas($_SESSION['idEmpresa']);
?>
<?= $message; ?>
equivalencias
<section class="panel">
    <div class="panel-body">
        <div class="col-lg-offset-2 col-lg-8">
            <center>
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>
                            <button style="height: 30px !important;" class="btn btn-success btn-xs" onclick="saveProductosEquivalencias('<?= $_REQUEST['idProductos']; ?>');">
                                <i class="fa fa-floppy-o"></i> Guardar
                            </button>
                        </td>
                        <td>
                            <select id="idMedidasPE" class="form-control input-sm">
                                <option value="">[Seleccione...]</option>
                                <?php
                                foreach ($getMedidas as $key => $value) {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                        <td><input type="number" value="<?= $value['sku']; ?>" id="equivalentePE" class="form-control input-sm"/></td>
                        <td><input type="number" value="<?= $value['sku']; ?>" id="costoPE" class="form-control input-sm"/></td>
                    </tr>
                </table>
            </center>
        </div>
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr class="info text-uppercase" style="font-weight: bold;">
                    <td></td>
                    <td>Medida</td>
                    <td>Equivalente</td>
                    <td>Costo</td>
                    <td>Fecha de Creacion</td>
                    <td>Fecha de Actualizacion</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($getProductosEquivalencias as $key => $value) {
                    ?>
                    <tr>
                        <td>
                            <button class="btn btn-warning btn-xs" onclick="updateCodigoAlterno('<?= $value['id']; ?>');" <?= $status; ?>>
                                <i class="fa fa-refresh"></i>
                            </button>
                            &nbsp;
                            <button class="btn btn-danger btn-xs" onclick="deleteCodigoAlterno('<?= $value['id']; ?>');" <?= $status; ?>>
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                        <td><select id="idMedidas" class="form-control input-sm"></select></td>
                        <td><input type="text" class="form-control input-sm"/></td>
                        <td><input type="text" class="form-control input-sm"/></td>
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