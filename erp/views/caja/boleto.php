<?php
session_start();
require_once ("../../models/inventarios.php");
require_once ("../../models/admin.php");
$inventarios = new Inventarios();
$admin = new Admin();
$getTiposVenta = $admin->getTiposVenta();
?>
<input type="hidden" id="idUsuariosPedidos" value="<?= $_SESSION['idUsuarios']; ?>"/>
<input type="hidden" id="idBoleto"/>
<div class="row">
    <!-- Documento y Info Cliente -->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Boleto</label>
                        <input type="text" class="form-control input-sm" id="boleto" value="" readonly=""/>
                    </div> 
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Reserva</label>
                        <input type="text" class="form-control input-sm" id="reserva" value="" readonly=""/>
                    </div> 
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>file</label>
                        <input type="text" class="form-control input-sm" id="file" value="" readonly=""/>
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>gua</label>
                        <input type="text" class="form-control input-sm" id="gua" value="" readonly=""/>
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Fecha</label>
                        <input type="text" class="form-control input-sm" id="fecha" value="" readonly=""/>
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Codigo Auth</label>
                        <input type="text" class="form-control input-sm" id="codigoAuth" value="" readonly=""/>
                    </div>
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>Codigo Linea Aerea</label>
                        <input type="text" class="form-control input-sm" id="codigoLineaAerea" value="" readonly=""/>
                    </div>
                    <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                        <label>Linea Aerea</label>
                        <input type="text" class="form-control input-sm" id="lineaAerea" value="" readonly=""/>
                    </div>
                    <div class="col-sx-6 col-sm-6 col-md-6 col-lg-6">
                        <label>Pasajero</label>
                        <input type="text" class="form-control input-sm" id="pasajero" value="" readonly=""/>
                    </div>
                    <div class="col-sx-4 col-sm-4 col-md-4 col-lg-4">
                        <label>Endosos</label>
                        <input type="text" class="form-control input-sm" id="endosos" value="" readonly=""/>
                    </div>
                    <div class="col-sx-4 col-sm-4 col-md-4 col-lg-4">
                        <label>Status Boleto</label>
                        <input type="text" class="form-control input-sm" id="statusBoleto" value="" readonly=""/>
                    </div>
                    <div class="col-lg-12">
                        <label>Itinerario</label>
                        <textarea class="form-control input-sm" id="itinerario" tabindex="5" readonly=""></textarea>
                    </div>

                </div>
        </section>
    </div>
    <!-- /Documento y Info Cliente -->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label>Codigo Vendedor</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="codigoVendedor"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Codigo Cliente</label>
                        <input type='text' class="form-control input-sm pedidos" id="codigoCliente" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                    loadClientesNit();" tabindex="1"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Nombre Cliente</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="nombreFacturacion" readonly=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Tasa Cambio</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="tasaCambio" readonly=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>FEE</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="fee"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Tarifa</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="montoSinImpuestos" readonly=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Impuestos</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="impuestos" readonly=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Total</label>
                        <input type="text" class="form-control input-sm pedidos" value="" id="total" readonly=""/>
                    </div>
                    <br/><br/>
                    <div class="col-lg-12">
                        <br/><br/>
                        <button type="button" id="add" class="btn btn-success btn-sm" onclick="actualizarBoleto();">
                            <span class="fa fa-check"></span>&nbsp;Actualizar Boleto
                        </button>
                    </div>
                </div>
        </section>
    </div>

</div>
