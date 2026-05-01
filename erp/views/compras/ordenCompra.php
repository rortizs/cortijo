<?php
/**
 * POS / Inventarios - Orden de Compras
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
require_once("../../models/inventarios.php");
require_once("../../models/contabilidad.php");
require_once("../../models/admin.php");
$inventarios = new Inventarios();
$conta = new Contabilidad();
$admin = new Admin();
$getOrdenCompra = $inventarios->getOrdenCompra($_REQUEST['idOrdenCompra']);
$documento = explode('-', $getOrdenCompra['documento']);
$getProveedor = $admin->getProveedoresByName($getOrdenCompra['idProveedores']);
$monedas = $conta->getMonedas($_SESSION['idEmpresa']);
$tipoCompra = array(
    "Compra Local", "Importacion"
);
//print_r($getOrdenCompra);
?>
<input type="hidden" id="idOrdenCompra" value="<?= $_REQUEST['idOrdenCompra']; ?>"/>
<div class="row">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Datos Orden de Compra
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <label>Tipo Documento</label>
                        <input type="hidden" id="tipoDocumento2" value="<?= $documento[0]; ?>"/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value="<?= $documento[1]; ?>"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Solicitado Por</label>
                        <input type="text" class="form-control input-sm" id="solicitadoPor" value="<?= $getOrdenCompra['solicitadoPor']; ?>"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Departamento </label>
                        <input type="hidden" id="idHrmDepartamentos2" value="<?= $getOrdenCompra['idHrmDepartamentos']; ?>"/>
                        <select class="form-control input-sm" id="idHrmDepartamentos">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <label>Observaciones </label>
                        <textarea id="observaciones" class="form-control input-sm"><?= $getOrdenCompra['observaciones']; ?></textarea>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-fil"></i> Tipo de Orden de Compra</label>
                        <select class="form-control input-sm" id="idTipoOrdenCompra">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoCompra as $key => $value) {
                                if (($value) == $getOrdenCompra['idTipoOrdenCompra']) {
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
                        <label>Moneda</label>
                        <select class="form-control input-sm" id="idMonedas">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($monedas as $key => $value) {
                                if ($value['descripcion'] == $getOrdenCompra['idMonedas']) {
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
                    <div class="col-lg-6 tipoOrdenCompra">
                        <label>Fecha estimada de arribo</label>
                        <input type="text" class="form-control input-sm" id="fechaArribo" value="<?= $getOrdenCompra['arribo_at']; ?>"/>
                    </div>
                    <div class="col-lg-6 tipoOrdenCompra">
                        <label>Tipo Cambio</label>
                        <input type="text" class="form-control input-sm" id="tipoCambio" value="<?= $getOrdenCompra['tipoCambio']; ?>"/>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Datos Proveedor
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label>Estado</label>
                        <input class="form-control input-sm" type="text" id="statusOrdenCompra" value="<?= $getOrdenCompra['status']; ?>" readonly=""/>
                    </div>
                    <div class="col-lg-12">
                        <label><i class="fa fa-search"></i> Buscador por NIT Proveedor</label>
                        <div class="input-group m-bot15" id="buscadorProveedores">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('proveedores', 'Listado de Proveedores', 'proveedores');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idProveedores">
                            <input class="form-control input-sm" type="text" id="nit" placeholder="Ingrese NIT del Proveedor" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadProveedorByNit();" value="<?= $getProveedor['nitP']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <label><i class="fa fa-user"></i> Nombre del Proveedor</label>
                        <input type="text" class="form-control input-sm" readonly="" id="nombre" value="<?= $getProveedor['descripcion']; ?>"/>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-map-marker"></i> Dirección</label>
                        <input type="text" class="form-control input-sm" readonly="" id="direccion" value="<?= $getProveedor['direccionP']; ?>"/>
                    </div>
                    <div class="col-lg-6">
                        <label><i class="fa fa-calendar"></i> Dias Credito</label>
                        <input type="text" class="form-control input-sm" readonly="" id="diasCreditoP" value="<?= $getProveedor['diasCredito']; ?>"/>
                        <input type="hidden" class="form-control input-sm" readonly="" id="pequenoContribuyente">
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12">
                        <button class="btn btn-success btn-sm" id="add" onclick="guardarOrdenCompra();">
                            <i class="fa fa-floppy-o"></i> Guardar
                        </button>
                        <button class="btn btn-warning btn-sm" id="update" onclick="actualizarOrdenCompra();">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                        <button class="btn btn-danger btn-sm" id="cancel" onclick="cancelarProcesoCompra('ordenCompra');">
                            <i class="fa fa-trash"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Listado de Productos de Orden de Compra
            </header>
            <div class="panel-body">
                <input type="hidden" id="numeroProductos"/>
                <input type="hidden" id="totalOC"/>
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr class="info">
                            <td colspan="2" style="width: 15% !important;">
                                <label>Código Producto</label>
                                <div class="input-group" style="width:100% !IMPORTANT;">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_productos', 'Consulta Productos', 'ordenCompra');"> 
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
                                <label>Precio FOB</label>
                                <input type="text" class="form-control input-sm" id="precioProducto" style="text-align: right !important;">
                            </td>
                            <td class="text-right" style="width: 12% !important;">
                                <label>Cantidad</label>
                                <input type="text" class="form-control input-sm facturacion" id="cantidadProducto" style="text-align: right !important;">
                            </td>
                            <td class="text-right" style="width: 12% !important;">
                                <label>Total</label>
                                <input type="text" class="form-control input-sm" id="totalProducto" style="text-align: right !important;" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                            addItemOrdenCompra();">
                            </td>
                        </tr>
                    </thead>
                    <tbody id="detalle">
                    </tbody>
                    <thead id="summary">
                    </thead>
                </table>
            </div>
        </section>
    </div>
</div>