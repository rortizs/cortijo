<?php
/**
 * MODULO CAJA / FACTURACION
 */
session_start();
require_once ("../../models/inventarios.php");
require_once ("../../models/admin.php");
$inventarios = new Inventarios();
$admin = new Admin();
$getTiposVenta = $admin->getTiposVenta();
$facturacionConf = $inventarios->facturacionConf('facturacion', $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'], $_SESSION['idSucursalesS']);
?>
<input type="hidden" id="idPedido"/>
<input type='hidden' id="availablePrint" readonly=""/>
<input type="hidden" id="idClientes" value="0"/>
<input type="hidden" id="numeroProductos"/>
<input type="hidden" id="subTotal" value="0"/>
<input type="hidden" id="iva" value="0"/>
<input type="hidden" id="descuentoM" value="0">
<input type="hidden" id="total" value="0"/>
<input type="hidden" id="totalDetalle" value="0"/>
<input type="hidden" id="costo_de_venta" value="0"/>
<input type="hidden" id="cambio" value="0">
<input type="hidden" id="totalPagado" value="0" readonly="">
<input type="hidden" id="ingresoA" value="<?= $facturacionConf['ingresoA']; ?>"/>
<input type="hidden" id="idPuntoIngreso" value="<?= $facturacionConf['idPuntoIngreso']; ?>"/>
<input type="hidden" id="numeroItemsFactura" value="0" readonly="">
<input type="hidden" id="valExistencias" value="<?= $facturacionConf['valExistencias']; ?>"/>
<input type="hidden" id="idFormato">
<input type="hidden" id="editarPrecio" value="<?= $facturacionConf['editarPrecio']; ?>">

<div class="row">
    <!-- Documento y Info Cliente -->
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>No. Pedido</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_pedidos', 'Consulta de Pedidos', 'pedidos2');"><i class="fa fa-search"></i></button>
                            </span>
                            <input type="text" class="form-control input-sm" id="noPedido" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        getPedido($(this).val(), 'get');"/>
                        </div>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Fecha Facturación</label>
                        <input type="text" class="form-control input-sm" id="fechaFactura" value="<?= $inventarios->dateViews; ?>"/>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Documento</label>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Correlativo</label>
                        <input type='text' class="form-control input-sm" id="correlativo" readonly=""/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <label>INGRESE NIT/CODIGO</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_clientes', 'Busqueda Clientes', 'clientes');"><i class="fa fa-search"></i></button>
                            </span>
                            <input type='text' class="form-control input-sm facturacion" id="nit" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadClientesNit();" tabindex="2" style="text-transform: uppercase !important;"/>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>CODIGO</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="codigoC" tabindex="3"/>
                    </div>    
                    <div class="col-lg-6">
                        <label>Nombre</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="nombre" tabindex="3"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Teléfono</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="telefono" tabindex="3"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Direccion</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="direccion" tabindex="4"/>
                    </div>
                    <div class="col-lg-12">
                        <label>Observaciones</label>
                        <textarea class="form-control input-sm pedidos" id="observaciones" tabindex="5"></textarea>
                    </div>
                    <div class="col-lg-12">
                        <label>Vendedor</label>
                        <select class="form-control facturacion input-sm" id="vendedores" tabindex="5">
                        </select>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /Documento y Info Cliente -->
    <!-- Facturacion -->
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                        Subtotal
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtSubTotal">
                        0.00
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                        Impuestos
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtIva">
                        0.00
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal" style="border-bottom: 2px solid #000;">
                        Descuento
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtDescuento" style="border-bottom: 2px solid #000;">
                        0.00
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                        Total
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtTotal">
                        0.00
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <label>Descuento Q</label>
                        <input type="text" class="form-control input-sm facturacion" value="0" id="descuentoMoneda" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                            generarDescuentoMonedaCajaQuetzales();">
                        <input type="hidden" class="form-control input-sm" id="tasaCambio" value="0">
                    </div>
                    <!--                    <div class="col-lg-4">
                                            <label>Descuento %</label>
                                            <input type="text" class="form-control input-sm facturacion" value="0" id="descuentoP" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                        generarDescuentoMonedaCaja();">
                                        </div>-->
                    <!-- <div class="col-lg-6">
                        <label>Tasa de Cambio</label>
                        
                    </div>  -->                   
                    <div class="col-lg-4">
                        <label>Tipo Venta</label>
                        <select class="form-control input-sm facturacion" id="tipoVenta" onchange="loadCamposHidden();">
                            <?php
                            foreach ($getTiposVenta as $key => $value) {
                                $selected = "";
                                if ($value['predefinido'] == '1') {
                                    $selected = "selected=''";
                                }
                                ?>
                                <option value="<?= $value['id']; ?>" <?= $selected; ?>><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Tipo Transaccion</label>
                        <select class="form-control input-sm facturacion" id="tipoTransaccion" onchange="loadCamposHidden();">
                            <option value="1">Factura Electronica</option>
                            <option value="2">Recibo de Venta</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <!-- CAMPOS CREDITO-->
                    <div id="camposCredito">
                        <div class="col-lg-4">
                            <label>Total</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="totalP" readonly=""/>
                        </div>
                        <div class="col-lg-4">
                            <label>Anticipo</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="anticipoP"/>
                        </div>
                        <div class="col-lg-4">
                            <label>Saldo</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="saldoP" readonly=""/>
                        </div>
                    </div>
                    <!-- END CAMPOS CREDITO-->
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button type="button" class="btn btn-success btn-sm" onclick="OpenLiquidarVenta();">
                            <span class="fa fa-shopping-cart"></span>&nbsp;Liquidar Venta
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="cancelarVenta" onclick="cancelarVenta();">
                            <span class="fa fa-trash"></span>&nbsp;Eliminar Venta
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- END Facturacion -->
    <!-- Listado de Productos venta -->
    <div class="clear">&nbsp;</div>
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Codigo Producto</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('inventario', 'Consulta de Productos en Inventario', 'Facturacion');"> 
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="text" class="form-control input-sm facturacion" id="codigo" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                loadProductoByCodigo();" tabindex="6" style="text-transform: uppercase !important;">
                        </div>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Descripcion</label>
                        <input type="text" class="form-control input-sm" id="descProducto" readonly="" style="text-transform: uppercase !important;">
                        <input type="hidden" id="idProducto"/>
                        <input type="hidden" id="tipoProducto"/>
                        <input type="hidden" id="costoProducto"/>
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Existencias</label>
                        <input type="text" class="form-control input-sm" id="existencia" readonly="">
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Precio</label>
                        <?php
                        if($_SESSION['dbProject']==='erp_josue'){
                            ?>
                            <select class="form-control input-sm" id="precioProducto">
                            </select>
                            <?php
                        }else{
                            ?>
                            <input type="text" class="form-control input-sm" id="precioProducto">
                            <?php
                        }
                        ?>
                        
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Cantidad</label>
                        <input type="text" class="form-control input-sm facturacion" id="cantidad" value="" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                    agregarProductoVenta();">
                    </div>
                    <div class="clear">&nbsp;</div>
                    <div class="col-lg-12 table-responsive">
                        <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td>No.</td>
                                    <td style="width: 10px;">&nbsp;</td>
                                    <td>Codigo</td>
                                    <td>Descripcion</td>
                                    <?php
                                    if ($_SESSION['dbProject'] === 'pos_kasualcosmeticos') {
                                        ?>
                                        <td>Item</td>
                                        <td>Marca</td>
                                        <?php
                                    }
                                    ?>
                                    <td class="text-right" style="width: 12% !important;">Cantidad</td>
                                    <td class="text-right" style="width: 12% !important;">Precio</td>
                                    <td class="text-right" style="width: 12% !important;">Total</td>
                                </tr>
                            </thead>
                            <tbody id="detalle">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- END Listado de Productos venta -->
</div>
