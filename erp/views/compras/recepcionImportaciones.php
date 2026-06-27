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
$inventarios = new Inventarios();
$admin = new Admin();
$getData = $inventarios->getImportacion($_REQUEST['idImportacion']);
//print_r($getData);
$tipoOperacion = array(
    "Bienes", "Activos Fijos", "Servicio", "Importaciones"
);
$tipoImportacion = array(
    "Contado", "Credito"
);
$tipoProrrateo = array(
    "Al Inventario", "Al Valor", "Al Peso"
);
$tipoGasto = array(
    "Flete", "Seguro", "DUA", "Otros", "DAI"
);
$getSucursales = $admin->getSucursales($_SESSION['idEmpresa'], $params);
?>
<div class="row">
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
                            <input type="hidden" class="form-control input-sm" id="moduloProceso" value="importaciones"/>
                            <input type="hidden" class="form-control input-sm" id="idOrdenCompra" readonly=""/>
                            <input type="text" class="form-control input-sm" id="noOrdenCompra" readonly=""/>
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
                            <input type="hidden" id="idProveedores" value="<?= $getData['idProveedores']; ?>">
                            <input class="form-control input-sm" type="text" id="nit" placeholder="Ingrese NIT del Proveedor" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadProveedorByNit();" value="<?= $getData['nitP']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <label><i class="fa fa-user"></i> Nombre del Proveedor</label>
                        <input type="text" class="form-control input-sm" readonly="" id="nombre" value="<?= $getData['nombreProveedor']; ?>"/>
                    </div>
                    <div class="col-lg-12">
                        <label><i class="fa fa-map-marker"></i> Dirección</label>
                        <input type="text" class="form-control input-sm" readonly="" id="direccion" value="<?= $getData['direccionP']; ?>"/>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-calendar"></i> Dias Credito</label>
                        <input type="text" class="form-control input-sm" readonly="" id="diasCreditoP" value="<?= $getData['diasCredito']; ?>"/>
                        <input type="hidden" class="form-control input-sm" readonly="" id="pequenoContribuyente">
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-filter"></i> Tipo de Operacion</label>
                        <select class="form-control input-sm" id="idTipoOperacion">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoOperacion as $key => $value) {
                                if (($key + 1) == $getData['idTipoOperacion']) {
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
                        <label><i class="fa fa-filter"></i> Tipo de Importacion</label>
                        <select class="form-control input-sm" id="idTipoImportacion">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoImportacion as $key => $value) {
                                if (($key + 1) == $getData['idTipoImportacion']) {
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
                        <label><i class="fa fa-filter"></i> Tipo de Prorrateo</label>
                        <select class="form-control input-sm" id="idTipoProrrateo">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoProrrateo as $key => $value) {
                                if (($key + 1) == $getData['idTipoProrrateo']) {
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
                        <label><i class="fa fa-fil"></i> Concepto De Importacion</label>
                        <textarea class="form-control input-sm" id="conceptoImportacion" style="text-transform: uppercase !important; text-align: left !important;" rows="8"><?= $getData['conceptoImportacion']; ?></textarea>
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
                    <div class="col-lg-4">
                        <label><i class="fa fa-map-marker"></i> Establecimiento</label>
                        <select id="idSucursales" class="form-control input-sm">
                            <option value="">[Seleccione...]</option>
                            <?php
                            foreach ($getSucursales as $key => $value) {
                                if ($value['id'] == $getData['idSucursales']) {
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
                        <input type="hidden" id="idDocumentosCorrelativos" value="<?= $getData['idDocumentosCorrelativos']; ?>"/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-hashtag"></i> Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value="<?= $getData['correlativo']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-check"></i> Serie<br/>Factura</label>
                        <input type="text" class="form-control input-sm" id="serieFactura" style="text-transform: uppercase;" value="<?= $getData['serieFactura']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-hashtag"></i> Numero<br/>Factura</label>
                        <input type="text" class="form-control input-sm" id="noFactura" value="<?= $getData['noFactura']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-calendar"></i> Fecha de Contabilizacion</label>
                        <input type="text" class="form-control input-sm" id="fechaContabilizacion" readonly="" value="<?= $getData['fechaContabilizacion'] ?: $today; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-calendar"></i> Fecha de <br/>Factura</label>
                        <input type="text" class="form-control input-sm" id="fechaFactura" readonly="" value="<?= $getData['fechaFactura'] ?: $today; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-calendar"></i> Fecha de <br/>Pago</label>
                        <input type="text" class="form-control input-sm" id="fechaPago" readonly="" value="<?= $getData['fechaPago'] ?: $today; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Tipo de <br/>Cambio DUA</label>
                        <input type="text" class="form-control input-sm" id="tipoCambioDUA" value="<?= $getData['tipoCambioDUA']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Tipo de <br/>Cambio</label>
                        <input type="text" class="form-control input-sm" id="tipoCambio" value="<?= $getData['tipoCambio']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Valor Factura Importacion</label>
                        <input type="text" class="form-control input-sm" id="valorFacturaImportacion" value="<?= $getData['valorFacturaImportacion']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Valor Gastos<br/>Importacion</label>
                        <input type="text" class="form-control input-sm" id="valorGastosImportacion" readonly="" value="<?= $getData['valorGastosImportacion']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Valor Total<br/>Importacion</label>
                        <input type="text" class="form-control input-sm" id="valorTotalImportacion" readonly="" value="<?= $getData['valorTotalImportacion']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Descuentos Importacion</label>
                        <input type="text" class="form-control input-sm" id="descuentoM" name="descuento" value="<?= $getData['descuentoM'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Valor Factura<br/>X Tipo Cambio</label>
                        <input type="text" class="form-control input-sm" id="valorFacturaTipoCambio" readonly="" value="<?= $getData['valorFacturaImportacion']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Sub-Total<br/>X Tipo Cambio</label>
                        <input type="text" class="form-control input-sm" id="subTotal" readonly="" name="subtotal" value="<?= $getData['subtotal'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Iva<br/>Moneda Local</label>
                        <input type="text" class="form-control input-sm" id="iva" readonly="" name="iva" value="<?= $getData['iva'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Total<br/>Moneda Local</label>
                        <input type="text" class="form-control input-sm" id="total" readonly="" name="total" value="<?= $getData['total'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Cantidad de <br/>Items Factura</label>
                        <input type="text" class="form-control input-sm" id="cantidadItems" readonly="" name="cantidadItems" value="<?= $getData['total'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> Total Peso<br/>Importacion</label>
                        <input type="text" class="form-control input-sm" id="totalPeso" readonly="" name="totalPeso" value="<?= $getData['totalPeso'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> FACTOR AL<br/>INVENTARIO</label>
                        <input type="text" class="form-control input-sm" id="factorInventario" readonly="" name="factorInventario" value="<?= $getData['factorInventario'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> FACTOR AL<br/>VALOR</label>
                        <input type="text" class="form-control input-sm" id="factorValor" readonly="" name="factorValor" value="<?= $getData['factorValor'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label><i class="fa fa-money"></i> FACTOR AL<br/>PESO</label>
                        <input type="text" class="form-control input-sm" id="factorPeso" readonly="" name="factorPeso" value="<?= $getData['factorValor'] ?: 0; ?>"/>
                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                &nbsp;<br/>
                                <?php
                                if ($_REQUEST['idImportacion'] != '') {
                                    ?>
                                    <button class="btn btn-info btn-sm" onclick="loadData('vw_importaciones', 'Inventarios', 'Importaciones', 0, 0, 0);">
                                        <i class="fa fa-arrow-left"></i> Regresar a listado
                                    </button>
                                    <?php
                                } else {
                                    ?>
                                    <button class="btn btn-success btn-sm" onclick="guardarImportacion();">
                                        <i class="fa fa-floppy-o"></i> Guardar
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="cancelarProcesoCompra('importaciones');">
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
                        <a data-toggle="tab" href="#detalleGastos">Detalle de Gastos</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#contabilidad">Contabilidad</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
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
                            <div class="col-lg-4">
                                <label><i class="fa fa-money"></i> Moneda de Compra</label>
                                <select class="form-control input-sm" id="monedaCompra">
                                    <option value="">[Seleccione...]</option>
                                    <option value="1">Local</option>
                                    <option value="2">Extranjera</option>
                                </select>
                            </div>
                        </div>
                        <div class="clear">&nbsp;</div>
                        <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td colspan="2" style="width: 20% !important;">
                                        <label>Código Producto</label>
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_productos', 'Consulta Productos', 'importaciones');"> 
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                            <input type="text" class="form-control input-sm facturacion" id="codigo" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                        loadProductoSKU('compras');" tabindex="6" readonly="">
                                        </div>
                                    </td>
                                    <td style="width: 20% !important;">
                                        <label>Descripción</label>
                                        <input type="text" class="form-control input-sm" id="descProducto" readonly="">
                                        <input type="hidden" id="idProducto" class="form-control input-sm"/>
                                        <input type="hidden" id="tipoProducto" class="form-control input-sm"/>
                                        <input type="hidden" id="utilizaSerie" class="form-control input-sm"/>
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Cantidad</label>
                                        <input type="text" class="form-control input-sm" id="cantidad" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Peso</label>
                                        <input type="text" class="form-control input-sm" id="peso" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Arancel %</label>
                                        <input type="text" class="form-control input-sm" id="arancel" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Precio</label>
                                        <input type="text" class="form-control input-sm" id="precioProducto" style="text-align: right !important;">
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>Total</label>
                                        <input type="text" class="form-control input-sm" id="totalProducto" style="text-align: right !important;" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                    addItemImportacion();">
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="detalleImportacion">
                            </tbody>
                            <thead id="summary">
                            </thead>
                        </table>
                    </div>
                    <div id="detalleGastos" class="tab-pane">
                        <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td style="width: 10% !important;">
                                        <label>Tipo Documento</label>
                                        <select class="form-control input-sm" id="idTipoDocumentoGasto">
                                            <option value="">[Seleccione...]</option>
                                            <option value="1">Recibo</option>
                                            <option value="2">Factura</option>
                                        </select>
                                    </td>
                                    <td style="width: 10% !important;">
                                        <label>nit</label>
                                        <input type="text" class="form-control input-sm" id="nitPG"/>
                                    </td>
                                    <td>
                                        <label>nombre proveedor</label>
                                        <input type="text" class="form-control input-sm" id="proveedorPG"/>
                                    </td>
                                    <td>
                                        <label>Tipo Gasto</label>
                                        <select class="form-control input-sm" id="idTipoGasto">
                                            <option value="">Seleccione</option>
                                            <?php
                                            foreach ($tipoGasto as $key => $value) {
                                                if (($key + 1) == $getData['idTipoGasto']) {
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
                                    </td>
                                    <td style="width: 10% !important;">
                                        <label>Fecha Factura</label>
                                        <input type="text" class="form-control input-sm" id="fechaFacturaPG" value="<?= $today; ?>"/>
                                    </td>
                                    <td style="width: 10% !important;">
                                        <label>serie factura</label>
                                        <input type="text" class="form-control input-sm" id="serieFacturaPG"/>
                                    </td>
                                    <td style="width: 10% !important;">
                                        <label>no. factura</label>
                                        <input type="text" class="form-control input-sm" id="numeroFacturaPG"/>
                                    </td>
                                    <td style="width: 10% !important;">
                                        <label>moneda</label>
                                        <select class="form-control input-sm" id="idMoneda">
                                            <option value="">[Seleccione...]</option>
                                            <option value="1">Local</option>
                                            <option value="2">Extranjera</option>
                                        </select>
                                    </td>
                                    <td class="text-right" style="width: 12% !important;">
                                        <label>valor</label>
                                        <input type="text" class="form-control input-sm" id="valorPG" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                    addGastoImportacion();"/>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="detalle-gastos">
                            </tbody>
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
                                    <input type="hidden" id="idFormato" value="<?= $getData['idFormatos'] ?: 0; ?>">
                                    <input type="hidden" id="idPartida" value="<?= $getData['idPartida'] ?: 0; ?>">
                                    <input class="form-control input-sm" type="text" readonly="" id="formato" value="<?= $getData['formato']; ?>">
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