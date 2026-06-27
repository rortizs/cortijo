<?php
/**
 * POS / Inventarios - Recepcion de Compras
 * @author Jonathan Juarez
 * @version 2.1 20170814
 */
session_start();
$today = date('d-m-Y');
require_once("../../models/inventarios.php");
require_once("../../models/admin.php");
require_once("../../models/contabilidad.php");
$inventarios = new Inventarios();
$admin = new Admin();
$conta = new Contabilidad();
$getCentrosCosto = $conta->getCentrosCosto($_SESSION['idEmpresa']);
$getCompra = $inventarios->getCompra($_REQUEST['idCompra']);
$tipoOperacion = array(
    "Bienes", "Activos Fijos", "Servicio", "IDP (Compra Combustible)", "Bienes - Inventario"
);
$tipoCompra = array(
    "Contado", "Credito"
);
$generaIva = array(
    "Si", "No"
);
$getBodegas = $admin->getBodegas($_SESSION['idEmpresa']);
$getSucursales = $admin->getSucursales($_SESSION['idEmpresa'], $params);
$getConfPaisEmpresa = $admin->getConfPaisEmpresa($_SESSION['idEmpresa']);
//print_r($getCompra);
?>
<div class="row">
    <input type="hidden" id="ivaEmpresa" value="<?= $getConfPaisEmpresa[0]['iva'] ?: 0; ?>"/>
    <div class="col-sm-6 col-md-6 col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Datos Proveedor
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <label>No. Orden de Compra</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_comprasOrdenes', 'Consulta de Ordenes de Compra', 'ordenesCompra');"><i class="fa fa-search"></i></button>
                            </span>
                            <input type="hidden" class="form-control input-sm" id="modulo" value="compras"/>
                            <input type="hidden" class="form-control input-sm" id="idOrdenCompra" readonly=""/>
                            <input type="text" class="form-control input-sm" id="noOrdenCompra" readonly=""/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-list"></i> Seleccione Caja Chica</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_cajaChica', 'Caja Chica', 'cajaChicaCompras');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input class="form-control input-sm" type="hidden" readonly="" id="idCajaChica"/>
                            <input class="form-control input-sm" type="text" readonly="" id="cajaChicaDoc"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-search"></i> Buscador por NIT Proveedor</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('proveedores', 'Listado de Proveedores', 'proveedores');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idProveedores" value="<?= $getCompra['idProveedores']; ?>">
                            <input class="form-control input-sm" type="text" id="nit" placeholder="Ingrese NIT del Proveedor" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadProveedorByNit();" value="<?= $getCompra['nitP']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-user"></i> Nombre del Proveedor</label>
                        <input type="text" class="form-control input-sm" readonly="" id="nombre" value="<?= $getCompra['nombreProveedor']; ?>"/>
                    </div>
                    <div class="col-lg-12">
                        <label><i class="fa fa-map-marker"></i> Dirección</label>
                        <input type="text" class="form-control input-sm" readonly="" id="direccion" value="<?= $getCompra['direccionP']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-calendar"></i> Dias Credito</label>
                        <input type="text" class="form-control input-sm" readonly="" id="diasCreditoP" value="<?= $getCompra['diasCredito']; ?>"/>
                        <input type="hidden" class="form-control input-sm" readonly="" id="pequenoContribuyente">
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-filter"></i> Tipo de Operacion</label>
                        <select class="form-control input-sm" id="idTipoOperacion">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoOperacion as $key => $value) {
                                if (($key + 1) == $getCompra['idTipoOperacion']) {
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
                    <div class="col-lg-4">
                        <label><i class="fa fa-filter"></i> Genera Iva?</label>
                        <select class="form-control input-sm" id="generaIva">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($generaIva as $key => $value) {
                                if (($key + 1) == $getCompra['generaIva']) {
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
                    <div class="col-lg-6">
                        <label><i class="fa fa-filter"></i> Tipo de Compra</label>
                        <select class="form-control input-sm" id="idTipoCompra">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoCompra as $key => $value) {
                                if (($key + 1) == $getCompra['idTipoCompra']) {
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
                    <div class="col-lg-6">
                        <label><i class="fa fa-filter"></i> Centro de Costo</label>
                        <select class="form-control input-sm" id="idCentrosCosto">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($getCentrosCosto as $key => $value) {
                                if (($key + 1) == $getCompra['idCentrosCosto']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['cuenta']; ?> - <?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['cuenta']; ?> - <?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-12 col-lg-12">
                        <label><i class="fa fa-fil"></i> Concepto De Compra</label>
                        <textarea class="form-control input-sm" id="conceptoCompra" style="text-transform: uppercase !important; text-align: left !important;" rows="4"><?= $getCompra['conceptoCompra']; ?></textarea>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Datos Factura
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label><i class="fa fa-map-marker"></i> Establecimiento</label>
                        <select id="idSucursales" class="form-control input-sm">
                            <option value="">[Seleccione...]</option>
                            <?php
                            foreach ($getSucursales as $key => $value) {
                                if ($value['id'] == $getCompra['idSucursales']) {
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
                    <div class="col-lg-4">
                        <label><i class="fa fa-filter"></i> Tipo Documento</label>
                        <input type="hidden" id="idDocumentosCorrelativos" value="<?= $getCompra['idDocumentosCorrelativos']; ?>"/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-hashtag"></i> Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value="<?= $getCompra['correlativo']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-check"></i> Serie</label>
                        <input type="text" class="form-control input-sm" id="serieFactura" style="text-transform: uppercase;" value="<?= $getCompra['serieFactura']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-hashtag"></i> Correlativo<br/>Factura</label>
                        <input type="text" class="form-control input-sm" id="noFactura" value="<?= $getCompra['noFactura']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-calendar"></i> Fecha de Contabilizacion</label>
                        <input type="text" class="form-control input-sm" id="fechaContabilizacion" value="<?= $getCompra['fechaContabilizacion'] ?: $today; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-calendar"></i> Fecha de Factura<br/>&nbsp;</label>
                        <input type="text" class="form-control input-sm" id="fechaFactura" value="<?= $getCompra['fechaFactura'] ?: $today; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-calendar"></i> Fecha de Pago</label>
                        <input type="text" class="form-control input-sm" id="fechaPago" value="<?= $getCompra['fechaPago'] ?: $today; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Valor Factura</label>
                        <input type="text" class="form-control input-sm" id="valorFactura" value="<?= $getCompra['valorFactura']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Sub-Total</label>
                        <input type="text" class="form-control input-sm" id="subTotal" readonly="" name="subtotal" value="<?= $getCompra['subtotal'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Exento </label>
                        <div class="input-group">
                            <span class="input-group-btn hidden" id="tablaImpuesto">
                                <button class="btn btn-primary btn-sm" type="button" onclick="tablaImpuesto();">
                                    IDP
                                </button>
                            </span>
                            <input class="form-control input-sm" type="text" id="exento" name="exento" value="<?= $getCompra['exento'] ?: 0; ?>"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Descuento </label>
                        <input type="text" class="form-control input-sm" id="descuentoM" name="descuento" value="<?= $getCompra['descuentoM'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-percent"></i> Descuento</label>
                        <input type="text" class="form-control input-sm" id="descuentoP" value="<?= $getCompra['descuentoP'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> INGUAT</label>
                        <input type="text" class="form-control input-sm" id="inguat" name="inguat" value="<?= $getCompra['inguat'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Total </label>
                        <input type="text" class="form-control input-sm" id="total" readonly="" name="total" value="<?= $getCompra['total'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Iva</label>
                        <input type="text" class="form-control input-sm" id="iva" readonly="" name="iva" value="<?= $getCompra['iva'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                &nbsp;<br/>
                                <?php
                                if ($_REQUEST['idCompra'] != '') {
                                    ?>
                                    <button class="btn btn-warning btn-sm" onclick="actualizarCompra();">
                                        <i class="fa fa-refresh"></i> Actualizar
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarCompra();">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                    <button class="btn btn-info btn-sm" onclick="cancelarProcesoCompra('compra');">
                                        <i class="fa fa-arrow-left"></i> Cancelar
                                    </button>
                                    <?php
                                } else {
                                    ?>
                                    <button class="btn btn-success btn-sm" onclick="guardarCompra();">
                                        <i class="fa fa-floppy-o"></i> Guardar
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="cancelarProcesoCompra('compra');">
                                        <i class="fa fa-trash"></i> Cancelar
                                    </button>
                                    <?php
                                }
                                ?>  
                            </div>
                        </div>
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
                <input type="hidden" id="numeroProductos"/>
                <input type="hidden" id="totalItems"/>
                <div class="tab-content">
                    <div id="detalleItems" class="tab-pane active">
                        <div class="row">
                            <div class="col-lg-2">
                                <label><i class="fa fa-filter"></i> Ingreso A</label>
                                <select class="form-control input-sm" id="ingresoA" onchange="ingresoA2();">
                                    <option value="">[Seleccione...]</option>
                                    <option value="1">Bodega</option>
                                    <option value="2">Sucursal</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label><i class="fa fa-map-marker"></i> Lugar de Ingreso</label>
                                <select class="form-control input-sm" id="idPuntoIngreso">

                                </select>
                            </div>
                        </div>
                        <div class="clear">&nbsp;</div>
                        <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td colspan="2">
                                        <label>Código Producto</label>
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_productos', 'Consulta Productos', 'compras');"> 
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                            <input type="text" class="form-control input-sm facturacion" id="codigo" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                        loadProductoSKU('compras');" tabindex="6">
                                        </div>
                                    </td>
                                    <td style="width: 31% !important;">
                                        <label>Descripción</label>
                                        <input type="text" class="form-control input-sm" id="descProducto" readonly="">
                                        <input type="hidden" id="idProducto" class="form-control input-sm"/>
                                        <input type="hidden" id="tipoProducto" class="form-control input-sm"/>
                                        <input type="hidden" id="utilizaSerie" class="form-control input-sm"/>
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Descuento</label>
                                        <input type="text" class="form-control input-sm" id="descuentoProducto" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Precio</label>
                                        <input type="text" class="form-control input-sm" id="precioProducto" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Cantidad</label>
                                        <input type="text" class="form-control input-sm facturacion" id="cantidadProducto" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Total</label>
                                        <input type="text" class="form-control input-sm" id="totalProducto" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Fecha Vencimiento</label>
                                        <input type="date" class="form-control input-sm facturacion" id="fechaVencimiento">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>No Lote</label>
                                        <input type="text" class="form-control input-sm facturacion" id="noLote" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                    addItemCompra();"> 
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="detalleCompra">
                            </tbody>
                            <thead id="summaryCompra">
                            </thead>
                        </table>
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
                                    <input type="hidden" id="idCompra" value="<?= $_REQUEST['idCompra']; ?>">
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