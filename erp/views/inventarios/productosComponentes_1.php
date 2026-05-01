<?php
/**
 * POS /Traslados desde modulo de administracion
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
require_once ("../../models/inventarios.php");
$inventarios = new Inventarios();
$infoProducto = $inventarios->getInfoProducto($_REQUEST['idProductoPrincipal']);
$flag = $_REQUEST['flag'] ?: '';
$message = "";
switch ($flag) {
    case '1':
        $agregarComponenteProducto = $inventarios->agregarComponenteProducto($_POST);
        $message = $agregarComponenteProducto;
        break;
    case '2':
        $actualizarComponenteProducto = $inventarios->actualizarComponenteProducto($_POST);
        $message = $actualizarComponenteProducto;
        break;
    case '3':
        $eliminarComponenteProducto = $inventarios->eliminarComponenteProducto($_POST);
        $message = $eliminarComponenteProducto;
        break;
}
$listadoProductosComponentes = $inventarios->listadoProductosComponentes($_REQUEST['idProductoPrincipal']);
?>
<div class="row">
    <div class="col-md-10">
        <!--breadcrumbs start -->
        <ul class="breadcrumb">
            <li>Productos</li>
            <li>Listado de Componentes</li>
        </ul>
        <!--breadcrumbs end -->
    </div>
    <div class="col-lg-2">
        <section class="panel">
            <div class="panel-body btn-controller">
                <button class="btn btn-info btn-sm" onclick="loadData('vw_productos', 'Administracion', 'Productos', 1, 1, 0);">
                    <i class="fa fa-undo"></i> Regresar
                </button>
            </div>
        </section>
    </div>
</div>
<?= $message; ?>
<section class="panel">
    <header class="panel-heading">
        Ingreso de Componentes<br/>
        <strong>[<?= $infoProducto['descLarga']; ?> - <?= $infoProducto['sku']; ?>]</strong>
    </header>
    <div class="panel-body">
        <div class="control-group col-lg-4">
            Producto<br/>
            <div class="input-group input-group-sm m-bot15">
                <span class="input-group-addon btn-info" onclick="busqueda('vw_productosMedidas', 'Productos', 'componentes');"><i class="fa fa-search"></i></span>
                <input type="text" class="form-control" id="descProducto" readonly="">
                <input type="hidden" id="idProductoPrincipal" value="<?= $_REQUEST['idProductoPrincipal']; ?>"/>
                <input type="hidden" id="idProducto"/>
            </div>
        </div>
        <div class="control-group col-lg-2">
            Medida<br/>
            <input type="text" class="form-control input-sm" id="medida" value="" readonly="">
            <input type="hidden" id="idMedidas"/>
        </div>
        <div class="control-group col-lg-2">
            Costo<br/>
            <input type="text" class="form-control input-sm" id="costo" value="" readonly="">
        </div>
        <div class="control-group col-lg-2">
            Unidades<br/>
            <input type="text" class="form-control input-sm" id="unidades" value="">
        </div>
        <div class="control-group col-lg-2">
            &nbsp;<br/>
            <button id="update" class="btn btn-warning input-sm" onclick="actualizarProductoListadoComponentes();">
                <i class="fa fa-refresh"></i>&nbsp;Actualizar
            </button>
            <button id="add" class="btn btn-success input-sm" onclick="ingresarProductoListadoComponentes();">
                <i class="fa fa-plus"></i>&nbsp;Agregar
            </button>
        </div>
    </div>
</section>
<section class="panel">
    <header class="panel-heading">
        Listado de Productos
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr class="info text-uppercase" style="font-weight: bold;">
                    <td></td>
                    <td>Codigo</td>
                    <td>Descripcion</td>
                    <td>Medida</td>
                    <td align="right">Costo Unitario</td>
                    <td align="right">Unidades</td>
                    <td align="right">Costo Total</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($listadoProductosComponentes as $key => $value) {
                    $total += $value['total'];
                    ?>
                    <tr>
                        <td>
                            <button class="btn btn-warning btn-xs" onclick="loadComponente('<?= $value['idProducto']; ?>', '<?= $value['sku']; ?>', '<?= $value['descLarga']; ?>', '<?= $value['idMedidas']; ?>', '<?= $value['medida']; ?>', '<?= $value['costo']; ?>', '<?= $value['unidades']; ?>');">
                                <i class="fa fa-pencil"></i>
                            </button>
                            &nbsp;
                            <button class="btn btn-danger btn-xs" onclick="eliminarComponente('<?= $value['idComponente']; ?>');">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                        <td><?= $value['sku']; ?></td>
                        <td><?= $value['descLarga']; ?></td>
                        <td><?= $value['medida']; ?></td>
                        <td align="right">Q. <?= $value['costo']; ?></td>
                        <td align="right"><?= $value['unidades']; ?></td>
                        <td align="right">Q. <?= number_format($value['total'], 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="6">Costo Total</td>
                    <td align="right">Q. <?= number_format($total, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
<!-- CONTROLLER DIALOG-->
<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" id="controllers">
            </div>
        </div>
    </div>
</div>
<!-- /CONTROLLER DIALOG -->