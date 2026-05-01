<?php
session_start();
require_once ("../../models/inventarios.php");
require_once ("../../models/admin.php");
$inventarios = new Inventarios();
$admin = new Admin();
$getTiposVenta = $admin->getTiposVenta();
?>
<div class="row">
    <!-- Documento y Info Cliente -->
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sx-12 col-sm-12 col-md-12 col-lg-12">
                        <label>Sucursal</label>
                        <select class="form-control input-sm" id="idSucursales"></select>
                    </div>
                    <div class="col-sx-6 col-sm-6 col-md-6 col-lg-6">
                        <label>Fecha</label>
                        <input type="text" class="form-control input-sm" id="fechaFactura" value="<?= $inventarios->dateViews; ?>"/>
                    </div>
                    <div class="col-sx-6 col-sm-6 col-md-6 col-lg-6">
                        <label>Documento</label>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-sx-6 col-sm-6 col-md-6 col-lg-6">
                        <label>Correlativo</label>
                        <input type='hidden' id="availablePrint" readonly=""/>
                        <input type='text' class="form-control input-sm" id="correlativo" readonly=""/>
                    </div>
                    <div class="col-sx-6 col-sm-6 col-md-6 col-lg-6">
                        <label>Partida Contable</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_formatos', 'Partidas Contables', 'partidaContable');">
                                    <i class="fa fa-question"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idFormato">
                            <input class="form-control input-sm" type="text" readonly="" id="formato">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="button" onclick="verPartida();">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>NIT</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('clientes', 'Busqueda Clientes', 'clientes');">Buscar <i class="fa fa-search"></i></button>
                            </span>
                            <input type='text' class="form-control input-sm facturacion" id="nit" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadClientesNit();" tabindex="2"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Nombre</label>
                        <input type="hidden" id="idClientes"/>
                        <input type="hidden" id="availableCreditLimit"/>
                        <input type="text" class="form-control input-sm facturacion" value="" id="nombre" tabindex="3"/>
                    </div>
                    <div class="col-lg-12">
                        <label>Direccion</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="direccion" tabindex="4"/>
                    </div>
                    <!-- CAMPOS CREDITO-->
                    <div id="camposCredito">
                        <div class="col-lg-4">
                            <label>Limite Credito</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="limiteCredito" readonly=""/>
                        </div>
                        <div class="col-lg-4">
                            <label>Dias Credito</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="diasCredito" readonly=""/>
                        </div>
                        <div class="col-lg-4">
                            <label>Saldo Actual</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="saldoActual" readonly=""/>
                        </div>
                        <div class="col-lg-4">
                            <label>Total Pagado</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="totalPagado" readonly=""/>
                        </div>
                        <div class="col-lg-4">
                            <label>Credito Disponible</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="disponibleCredito" readonly=""/>
                        </div>
                        <div class="col-lg-4">
                            <label>Fecha Ultimo Pago</label>
                            <input type="text" class="form-control TabOnEnter input-sm" value="" id="fechaUltimoPago" readonly=""/>
                        </div>
                    </div>
                    <!-- END CAMPOS CREDITO-->
                </div>
            </div>
        </section>
    </div>
    <!-- /Documento y Info Cliente -->
    <!-- Facturacion -->
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <section class="panel">
            <div class="panel-body">
                <input type="hidden" id="numeroProductos"/>
                <input type="hidden" id="subTotal" value="0"/>
                <input type="hidden" id="subTotalIva" value="0" name="subtotal"/>
                <input type="hidden" id="descuentoM" value="0" name="descuento">
                <input type="hidden" id="total" value="0" name="total"/>
                <input type="hidden" id="iva" value="1" name="iva"/>
                <input type="hidden" id="cambio" value="0">
                <input type="hidden" id="totalPagado" value="0" readonly="">
                <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                        Subtotal
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtSubTotal">
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
                        <label>Descuento %</label>
                        <input type="text" class="form-control input-sm facturacion" value="0" id="descuentoP" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                    generarDescuentoMonedaCaja();">
                    </div>
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
                        <label>Tasa de Cambio</label>
                        <input type="text" class="form-control input-sm" id="tasaCambio" value="1">
                    </div>
                </div>
                <div class="row">
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
                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('inventario', 'Inventario Sucursal', 'facturacion');"> 
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="text" class="form-control input-sm facturacion" id="codigo" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadProductoByCodigo();" tabindex="6">
                        </div>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Descripcion</label>
                        <input type="text" class="form-control input-sm" id="descProducto" readonly="">
                        <input type="hidden" id="idProducto"/>
                        <input type="hidden" id="tipoProducto"/>
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Existencias</label>
                        <input type="text" class="form-control input-sm" id="existencia" readonly="">
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Precio</label>
                        <input type="text" class="form-control input-sm" id="precioPublico">
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Cantidad</label>
                        <input type="text" class="form-control input-sm facturacion" id="cantidad" value="" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                    agregarProductoVenta();">
                    </div>
                    <!--
                    <div class="col-lg-3">
                        <label>&nbsp;</label><br/>
                        <button type="button" class="btn btn-success btn-sm" onclick="agregarProductoVenta();">
                            <span class="fa fa-check"></span>&nbsp;Agregar
                        </button>
                    </div>
                    -->
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
        </section>
    </div>
    <!-- END Listado de Productos venta -->
</div>
