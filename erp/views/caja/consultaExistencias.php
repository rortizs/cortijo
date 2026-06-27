<?php
session_start();
require_once('../../models/general.php');
require_once('../../models/admin.php');
$general = new General();
$admin = new Admin();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros de Consulta
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <select class="form-control input-sm" id="ingresoA">
                            <option>[Seleccione...]</option>
                            <option value="1">Bodega</option>
                            <?php
                            if ($_SESSION['idRoles'] == 3) {
                                ?>
                                <option value="2">Sucursal</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-8">
                        <input type="text" class="form-control input-sm" placeholder="Ingrese nombre del producto o código" id="busqueda" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                    busquedaExistencias();" value="<?= $_REQUEST['busqueda']; ?>"/>
                    </div>
                    <div class="col-lg-1">
                        <button class="btn btn-primary btn-sm" onclick="busquedaExistencias();">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
    if (isset($_REQUEST['busqueda'])) {
        $getPuntosIngreso = "";
        if ($_REQUEST['ingresoA'] === '1') {
            $getPuntosIngreso = $admin->getBodegas($_SESSION['idEmpresa']);
        } else {
            $getPuntosIngreso = $admin->getSucursales($_SESSION['idEmpresa']);
        }
        $getProductos = $admin->getProductos($_REQUEST['busqueda'], $_SESSION['idEmpresa']);
        ?>
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    <?= count($getProductos); ?> resultados busqueda "<?= $_REQUEST['busqueda']; ?>"
                </header>
                <div class="panel-body table-responsive">
                    <table class="table table-striped table-bordered table-hover" cellspacing="0"> 
                        <thead>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="<?= count($getPuntosIngreso) + 1; ?>" align="center">Existencias</td>
                            </tr>
                            <tr>
                                <td>No.</td>
                                <td>Producto</td>
                                <?php
                                for ($a = 0; $a < count($getPuntosIngreso); $a++) {
                                    ?>
                                    <td style="width: 10% !important; text-align: center;"><?= $getPuntosIngreso[$a]['descripcion']; ?></td>
                                    <?php
                                }
                                ?>
                                <td>Total</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($b = 0; $b < count($getProductos); $b++) {
                                ?>
                                <tr>
                                    <td><?= ($b + 1); ?></td>
                                    <td><?= $getProductos[$b]['sku']; ?> - <?= $getProductos[$b]['descLarga']; ?></td>
                                    <?php
                                    $total = 0;
                                    for ($a = 0; $a < count($getPuntosIngreso); $a++) {
                                        $existencias = $admin->consultaExistencias($_REQUEST['ingresoA'], $getPuntosIngreso[$a]['id'], $getProductos[$b]['id'], $_SESSION['idEmpresa']);
                                        $total += ($existencias ?: '0');
                                        ?>
                                        <td style="width: 10% !important; text-align: center;"> <?= $existencias ?: '0' ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td align="right"><?= number_format($total, 2); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    <?php
}
?>