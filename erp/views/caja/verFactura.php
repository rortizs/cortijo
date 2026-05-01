<?php
session_start();
require_once("../../models/reportes.php");
$reportes = new Reportes();
$_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
$verFactura = $reportes->consultaFacturas($_REQUEST);
$detalleProdFactura = $reportes->detalleProductosFactura($_REQUEST);
$status="success";
if($verFactura[0]['statusFactura']=='Anulada'){
    $status="danger";
}
//print_r($verFactura);
?>
<div class="row">
    <div class="col-lg-10">
        <!--breadcrumbs start -->
        <ul class="breadcrumb">
            <li id="modulo"></li>
            <li id="opcion"></li>
        </ul>
        <!--breadcrumbs end -->
    </div>
    <div class="col-lg-2">
        <section class="panel">
            <div class="panel-body btn-controller">
                <button class="btn btn-info btn-sm" onclick="loadConsultaFacturas();">
                    <i class="fa fa-undo"></i> Regresar
                </button>
            </div>
        </section>
    </div>
</div>
<div class="row">
    <!-- Documento y Info Cliente -->
    <div class="col-lg-6">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-4">
                        <label>Fecha Facturación</label>
                        <input type="text" class="form-control input-sm" id="fechaFactura" value="<?= $verFactura[0]['fecha']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Documento</label>
                        <input type='text' class="form-control input-sm" id="correlativo" value="<?= $verFactura[0]['documento']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <div class="alert alert-<?=$status;?> text-center" role="alert">
                            <?= $verFactura[0]['statusFactura']; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>NIT</label>
                        <input type='text' class="form-control input-sm" id="nit" value="<?= $verFactura[0]['nit']; ?>"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Nombre</label>
                        <input type="text" class="form-control TabOnEnter input-sm" id="nombre" value="<?= $verFactura[0]['nombre']; ?>"/>
                    </div>
                    <div class="col-lg-12">
                        <label>Direccion</label>
                        <input type="text" class="form-control TabOnEnter input-sm" id="direccion" value="<?= $verFactura[0]['direccion']; ?>"/>
                    </div>
                    <div class="col-lg-12">
                        <label>Vendedor</label>
                        <input type='text' class="form-control input-sm" id="vendedores" value="<?= $verFactura[0]['vendedor']; ?>"/>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /Documento y Info Cliente -->
    <!-- Facturacion -->
    <div class="col-lg-6">
        <section class="panel">
            <div class="panel-body">
                <input type="hidden" id="subTotal" value="0"/>
                <input type="hidden" id="descuentoM" value="0">
                <input type="hidden" id="total" value="0"/>
                <input type="hidden" id="cambio" value="0">
                <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                        Subtotal
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtSubTotal">
                        <?= number_format(($verFactura[0]['subTotal'] + $verFactura[0]['descuentoM']),2); ?>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal" style="border-bottom: 2px solid #000;">
                        Descuento
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtDescuento" style="border-bottom: 2px solid #000;">
                        <?= $verFactura[0]['descuentoM']; ?>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                        Total
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtTotal">
                        <?= $verFactura[0]['total']; ?>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                        Cambio
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtCambio">
                        <?= $verFactura[0]['cambio']; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-md-4 col-lg-4" id="divDescuentoP">
                        <label>Descuento %</label>
                        <input type="text" class="form-control" value="<?= $verFactura[0]['descuentoP']; ?>" id="descuentoP"/>
                    </div>
                    <div class="col-sm-4 col-md-4 col-lg-4" id="divSubTotal">
                        <label>Tipo Venta</label>
                        <input type="text" class="form-control" value="Contado" id="tipoVenta"/>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /Facturacion -->
    <!-- Listado de Productos venta -->
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td>No.</td>
                                    <td>Codigo</td>
                                    <td>Descripcion</td>
                                    <td class="text-right">Cantidad</td>
                                    <td class="text-right">Precio</td>
                                    <td class="text-right">Total</td>
                                </tr>
                            </thead>
                            <tbody id="detalle">
                                <?php
                                foreach ($detalleProdFactura as $key => $value) {
                                    ?>
                                    <tr>
                                        <td><?= ($key + 1); ?></td>
                                        <td><?= $value['sku']; ?></td>
                                        <td><?= $value['descLarga']; ?></td>
                                        <td><?= $value['cantidad']; ?></td>
                                        <td><?= $value['precio']; ?></td>
                                        <td><?= $value['total']; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>