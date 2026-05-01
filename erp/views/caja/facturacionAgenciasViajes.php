<?php
session_start();
require_once ("../../models/inventarios.php");
require_once ("../../models/admin.php");
$inventarios = new Inventarios();
$admin = new Admin();
$getTiposVenta = $admin->getTiposVenta();
?>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <!-- Documento y Info   --> 
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Boletos</label>
                        <button style="width: 100% !important;" class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_agenciasViajes', 'Consulta de Boletos', 'boletos');">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <input type="hidden" id="idPedido"/>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Fecha Facturación</label>
                        <input type="text" class="form-control input-sm" id="fechaFactura" value="<?= $inventarios->dateViews; ?>"/>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Fact.Correlativo</label>
                        <input type='hidden' id="availablePrint" readonly=""/>
                        <input type='hidden' id="idCorrelativoFactura" readonly=""/>
                        <input type='hidden' id="valTarjetaHidden" readonly=""/>
                        <input type='text' class="form-control input-sm" id="correlativoFactura" readonly=""/>
                        </select>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Pagare Correlativo</label>
                        <input type='hidden' id="availablePrint" readonly=""/>
                        <input type='hidden' id="idCorrelativoPagare" readonly=""/>
                        <input type='text' class="form-control input-sm" id="correlativoPagare" readonly=""/>
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
                        <label>Nombre de Facturacion</label>
                        <input type="hidden" id="idClientes"/>
                        <input type="hidden" id="availableCreditLimit"/>
                        <input type="text" class="form-control input-sm facturacion" value=""  id="nombre" tabindex="3"/>
                    </div>
                    <div class="col-lg-6">
                        <label>No. Reserva</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="noReserva" tabindex="4" readonly=""/>
                    </div>
                    <div class="col-lg-6">
                        <label>Itinerario</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" style="width: 100% !important;" type="button" onclick="getItinerario();">Consultar <i class="fa fa-plane"></i></button>
                            </span>
                            <input type="hidden" id="itinerario"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Direccion</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="direccion" tabindex="5"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Linea Aerea</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="lineaArea" tabindex="6" readonly=""/>
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
                    <input type="hidden"id="boletos" class="form-control input-sm">
                    <div class="col-lg-6">
                        <label>Vendedor</label>
                        <select class="form-control facturacion input-sm" id="vendedores" tabindex="5">
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Proveedor</label>
                        <select class="form-control facturacion input-sm" id="proveedores" tabindex="5">
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label>Motivo Factura</label>
                        <textarea type="text" class="form-control input-sm" value="" id="motivo" rows="5"></textarea>
                    </div>
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
                <input type="hidden" id="descuentoM" value="0">
                <input type="hidden" id="total" value="0"/>
                <input type="hidden" id="cambio" value="0">
                <input type="hidden" id="efectivoHidden" value="0">
                <input type="hidden" id="tarjetaHidden" value="0">
                <input type="hidden" id="nombreTarjetaHidden" value="0">
                <input type="hidden" id="noAutorizacionHidden" value="0">
                <input type="hidden" id="totalPagado" value="0" readonly="">
                <input type="hidden" id="iva" value="0" readonly="">
                <input type="hidden" id="formaPago" value="0" readonly="">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 txtTotal">
                        Subtotal
                    </div>
                    <input type="hidden" id="subTotalHidden" value="0">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtSubTotal">
                        0.00
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtSubTotalDE">
                        0.00
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 txtTotal">
                        IMPUESTOS
                    </div>
                    <input type="hidden" id="impuestoHidden" value="0">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtImpuestos">
                        0.00
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtImpuestoslDE">
                        0.00
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 txtTotal">
                        Cargos X Servicio
                        <input type="hidden" id="fee" value="0">
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 txtTotal">
                        &nbsp;               
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 txtTotal">
                        &nbsp;              
                    </div>

                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtCargos">
                        0.00
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtCargosDE">
                        0.00
                    </div>

                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 txtTotal otrosCargosDiv">
                        Otros
                        <input type="hidden" id="otrosCargosHidden" value="0">
                    </div>


                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right otrosCargosDiv" id="txtOtrosCargos">
                        0.00
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right otrosCargosDiv" id="txtOtrosCargosDE">
                        0.00
                    </div>

                    <input type="hidden" id="totalHidden" value="0">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 txtTotal">
                        Total
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtTotal">
                        0.00
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 total text-right" id="txtTotalDE">
                        0.00
                    </div>
                </div>
                <div class="row">
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
                        <label>T. Cambio Emision</label>
                        <input type="text" class="form-control input-sm" id="tasaCambio" value="0" readonly="">
                    </div>
                    <div class="col-lg-4">
                        <label>Tipo Impresion</label>
                        <select class="form-control input-sm facturacion" id="tipoImpresion" onchange="">
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label>Tipo Facturacion</label>
                        <select class="form-control input-sm facturacion" id="tipoFacturacion" onchange="cargarTipoImpresion()">

                            <option value="1">Cargos Por Servicio</option>
                            <option value="2">Total</option>

                        </select>
                    </div>
                    <div class="col-lg-4 otrosCargosDiv">
                        <label>% Otros Cargos X Servicio</label>
                        <input type="text" class="form-control input-sm" id="otrosCargos" value="0" onkeypress="agregarOtrosCargos()">
                    </div>
                    <div class="col-lg-4 otrosCargosDiv">
                        <label>(Q.)Otros Cargos X Servicio</label>
                        <input type="text" class="form-control input-sm" id="otrosCargosD" value="0.00" onkeypress="agregarOtrosCargos()">
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
    <!--c END Facturacion -->
    <!-- Listado de Productos venta -->
    <div class="clear">&nbsp;</div>
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">

                    <div class="clear">&nbsp;</div>
                    <div class="col-lg-12">
                        <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td>No.</td>
                                    <td style="width: 10px;">&nbsp;</td>
                                    <td>Boleto</td>
                                    <td>Pasajero</td>
                                    <td class="text-right">Tarifa</td>
                                    <td class="text-right">Total Impuestos</td>
                                    <td class="text-right">Tasa Cambio</td>                                   
                                    <td class="text-right">FEE ($)</td>                              
                                    <td class="text-right">Total Quetzales</td>
                                    <td class="text-right">Total Dolares</td>
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
