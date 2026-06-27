<?php
/**
 * Recepcion de Ventas
 * @author Jonathan Juarez
 * @version 2.0 20170504
 */
session_start();
$today = date('d-m-Y');
require_once("../../models/inventarios.php");
require_once("../../models/admin.php");
$inventarios = new Inventarios();
$admin = new Admin();
$getVenta = $inventarios->getVenta($_REQUEST['idVenta']);
//print_r($getVenta);
$tipoOperacion = array(
    "Bienes", "Servicio", "Varios", "Exportacion"
);
$tipoCompra = array(
    "Contado", "Credito"
);
$getSucursales = $admin->getSucursales($_SESSION['idEmpresa'], $params);
$getConfPaisEmpresa = $admin->getConfPaisEmpresa($_SESSION['idEmpresa']);
$facturacionConf = $inventarios->facturacionConf('facturacion', $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas']);
?>
<div class="row">
    <input type="hidden" id="ivaEmpresa" value="<?= $getConfPaisEmpresa[0]['iva'] ?: 0; ?>"/>
    <input type="hidden" id="idPedido"/>
    <input type="hidden" id="valExistencias" value="<?= $facturacionConf['valExistencias']; ?>"/>
    <input type="hidden" id="editarPrecio" value="1">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Datos del Cliente
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sx-6 col-sm-6 col-md-6 col-lg-6">
                        <label><i class="fa fa-search"></i> Buscador de Clientes</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_clientes', 'Listado de Clientes', 'clientes');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idClientes">
                            <input class="form-control input-sm" type="text" id="nit" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadClientesNit();" value="<?= $getVenta['nit']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-hashtag"></i> Nombre Cliente</label>
                        <input type="text" class="form-control input-sm" id="nombre" value="<?= $getVenta['nombre']; ?>"/>
                    </div>
                    <div class="col-lg-12">
                        <label><i class="fa fa-map-marker"></i> Direccion</label>
                        <input type="text" class="form-control input-sm" id="direccion" value="<?= $getVenta['direccion']; ?>"/>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-filter"></i> Tipo de Operacion</label>
                        <select class="form-control input-sm" id="idTipoOperacion">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoOperacion as $key => $value) {
                                if (($key + 1) == $getVenta['idTipoOperacion']) {
                                    ?>
                                    <option value="<?= ($key + 1); ?>" selected=""><?= $value; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                    <?php
                                }
                            }
                            ?>F
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-filter"></i> Tipo de Venta</label>
                        <select class="form-control input-sm" id="idTipoVenta">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoCompra as $key => $value) {
                                if (($key + 1) == $getVenta['idTipoVenta']) {
                                    ?>
                                    <option value="<?= ($key + 1); ?>" selected=""><?= $value; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12">
                        <label><i class="fa fa-fil"></i> Concepto De Venta</label>
                        <textarea class="form-control input-sm" id="conceptoVenta" style="text-transform: uppercase !important;" rows="3"><?= $getVenta['conceptoVenta']; ?></textarea>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Datos Factura
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <label><i class="fa fa-map-marker"></i> Establecimiento</label>
                        <select id="idSucursales" class="form-control input-sm" onchange="loadProductosVenta();">
                            <option value="">[Seleccione...]</option>
                            <?php
                            foreach ($getSucursales as $key => $value) {
                                if ($value['id'] == $getVenta['idSucursales']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-check"></i> Autorizacion</label>
                        <input type="text" class="form-control input-sm" id="autorizacion" value="<?= $getVenta['autorizacion']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-check"></i> Fecha Emision</label>
                        <input type="date" class="form-control input-sm" id="fechaEmision"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-check"></i> Hora Emision</label>
                        <input type="time" class="form-control input-sm" id="horaEmision"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-check"></i> Serie</label>
                        <input type="text" class="form-control input-sm" id="serieFactura" value="<?= $getVenta['serie']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-hashtag"></i> Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" value="<?= $getVenta['correlativo']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Valor Factura</label>
                        <input type="text" class="form-control input-sm" id="totalDolares" value="<?= $getVenta['valorFactura']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Tipo de Cambio</label>
                        <input type="text" class="form-control input-sm" id="tipoCambio" value="<?= ($getVenta['tipoCambio']?:1); ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Factura x TC</label>
                        <input type="text" class="form-control input-sm" id="valorFactura" value="<?= $getVenta['total']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Sub-Total</label>
                        <input type="text" class="form-control input-sm" id="subTotal" readonly="" name="subtotal" value="<?= $getVenta['subtotal']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Descuento </label>
                        <input type="text" class="form-control input-sm" id="descuentoM" value="0.00" name="descuento" value="<?= $getVenta['descuento']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-percent"></i> Descuento</label>
                        <input type="text" class="form-control input-sm" id="descuentoP" value="0" value="<?= $getVenta['descuentoP']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Total </label>
                        <input type="text" class="form-control input-sm" id="total" readonly="" name="total" value="<?= $getVenta['total']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Iva</label>
                        <input type="text" class="form-control input-sm" id="iva" readonly="" name="iva" value="<?= $getVenta['iva']; ?>"/>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-lg-12">
                        <?php
                        if ($_REQUEST['idVenta'] != '') {
                            ?>
                            <button class="btn btn-warning btn-sm" onclick="actualizarVenta();">
                                <i class="fa fa-refresh"></i> Actualizar
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarVenta();">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                            <?php
                        } else {
                            ?>
                            <button class="btn btn-success btn-sm" onclick="guardarVenta();">
                                <i class="fa fa-floppy-o"></i> Guardar
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="cancelarVenta();">
                                <i class="fa fa-trash"></i> Cancelar
                            </button>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading tab-bg-dark-navy-blue ">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#detalleItems">Detalle de Artículos</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#contabilidad">Contabilidad</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div id="detalleItems" class="tab-pane active">
                        <input type="hidden" id="numeroProductos"/>
                        <input type="hidden" id="totalItems"/>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                    <label>Codigo Producto</label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-sm" type="button" onclick="busqueda('inventario', 'Inventario Sucursal', 'FAC');"> 
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                        <input type="text" class="form-control input-sm facturacion" id="codigo" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                            loadProductoByCodigo();" tabindex="6">
                                    </div>
                                </div>
                                <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                    <label>Descripcion</label>
                                    <input type="text" class="form-control input-sm" id="descProducto">
                                    <input type="hidden" id="idProducto"/>
                                    <input type="hidden" id="tipoProducto"/>
                                    <input type="hidden" id="utilizaSerie"/>
                                </div>
                                <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                                    <label>Existencias</label>
                                    <input type="text" class="form-control input-sm" id="existencia" readonly="">
                                </div>
                                <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                                    <label>Precio</label>
                                    <input type="text" class="form-control input-sm" id="precioProducto">
                                </div>
                                <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                                    <label>Cantidad</label>
                                    <input type="text" class="form-control input-sm facturacion" id="cantidadProducto" value="" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        agregarProductoVenta();">
                                </div>
                                <div class="clear">&nbsp;</div>
                                <div class="col-lg-12">
                                    <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr class="info">
                                                <td>No.</td>
                                                <td style="width: 10px;">&nbsp;</td>
                                                <td>Codigo</td>
                                                <td>Descripcion</td>
                                                <td class="text-right">Cantidad</td>
                                                <td class="text-right">Precio</td>
                                                <td class="text-right">Total</td>
                                            </tr>
                                        </thead>
                                        <tbody id="detalle">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="contabilidad" class="tab-pane">
                        <div class="row">
                            <div class="col-lg-12">
                                <label><i class="fa fa-list"></i> Partida Contable</label>
                                <div class="input-group m-bot15">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_formatos', 'Partidas Contables', 'partidaContable');">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </span>
                                    <input type="hidden" id="idVenta" value="<?= $_REQUEST['idVenta']; ?>">
                                    <input type="hidden" id="idFormato" value="<?= $getCompra['idFormatos'] ?: 0; ?>">
                                    <input type="hidden" id="idPartida" value="<?= $getCompra['idPartida'] ?: 0; ?>">
                                    <input class="form-control input-sm" type="text" readonly="" id="formato" value="<?= $getCompra['formato']; ?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="verPartida();">
                                            <i class="fa fa-list"></i> Ver Integración
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-12" id="detallePartida">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>