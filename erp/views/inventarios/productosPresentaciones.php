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
//print_r($infoProducto);
$flag = $_REQUEST['flag'] ?: '';
$message = "";
switch ($flag) {
    case '1':
        $agregarPresentacion = $inventarios->agregarPresentacion($_POST);
        $message = $agregarPresentacion;
        break;
    case '3':
        $eliminarPresentacion = $inventarios->eliminarPresentacion($_POST);
        $message = $eliminarPresentacion;
        break;
}
$listadoPresentaciones = $inventarios->listadoPresentaciones($_REQUEST['idProductoPrincipal']);
?>
<div class="row">
    <div class="col-md-10">
        <!--breadcrumbs start -->
        <ul class="breadcrumb">
            <li>Productos</li>
            <li>Presentaciones de Venta <strong>[<?= $infoProducto['descLarga']; ?> - <?= $infoProducto['sku']; ?>]</strong></li>
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
        Ingreso de Presentaciones
    </header>
    <div class="panel-body">
        <div class="control-group col-lg-3">
            Nombre<br/>
            <input type="text" class="form-control input-sm" id="descripcion" value="">
        </div>
        <div class="control-group col-lg-3">
            Unidad de Medida a Descontar<br/>
            <input type="text" class="form-control input-sm" id="medida" value="<?= $infoProducto['medidaInventario']; ?>" readonly="">
            <input type="hidden" id="idProductoPrincipal" value="<?= $_REQUEST['idProductoPrincipal']; ?>"/>
            <input type="hidden" id="idMedidas" value="<?= $infoProducto['idMedidasInventario']; ?>"/>
        </div>
        <div class="control-group col-lg-2">
            Unidades<br/>
            <input type="text" class="form-control input-sm" id="unidades" value="">
        </div>
        <div class="control-group col-lg-2">
            Precio Venta<br/>
            <input type="text" class="form-control input-sm" id="precioVenta" value="">
        </div>
        <div class="control-group col-lg-2">
            &nbsp;<br/>
            <button id="add" type="button" class="btn btn-success btn-sm" onclick="ingresarPresentacion();">
                <i class="fa fa-plus"></i>&nbsp;Agregar
            </button>
        </div>
    </div>
</section>
<section class="panel">
    <header class="panel-heading">
        Listado de Presentaciones
    </header>
    <div class="panel-body">
        <table id="dataTable" class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr class="info text-uppercase" style="font-weight: bold;">
                    <td></td>
                    <td>Nombre</td>
                    <td>Unidad de Medida a Descontar</td>
                    <td>Unidades</td>
                    <td align="right">Precio Venta</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($listadoPresentaciones as $key => $value) {
                    ?>
                    <tr>
                        <td>
                            <button class="btn btn-danger btn-xs" onclick="eliminarPresentacion('<?= $value['idPresentacion']; ?>');">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                        <td><?= $value['descripcion']; ?></td>
                        <td><?= $value['medida']; ?></td>
                        <td><?= $value['unidades']; ?></td>
                        <td align="right">Q. <?= number_format($value['precio'], 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
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