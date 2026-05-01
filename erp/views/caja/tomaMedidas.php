<?php
session_start();
require_once ("../../models/admin.php");
require_once ("../../models/inventarios.php");
require_once('../../models/general.php');
$general = new General();
$admin = new Admin();
$getTiposVenta = $admin->getTiposVenta();
$inventarios = new Inventarios();
$facturacionConf = $inventarios->facturacionConf('pedidos', $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas']);
?>
<html>
    <head></head>
    <body>

        <div class="row">
            <!-- Documento y Info Cliente -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <section class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                                <input type="hidden" value="" id="idPedidoHidden">
                                <input type="hidden" value="" id="idTomaMedidasHidden">
                                <label>No. Pedido</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_pedidosMedidas', 'Consulta de Pedidos', 'pedidos2');"><i class="fa fa-search"></i></button>
                                    </span>
                                    <input type="text" class="form-control input-sm" id="noPedido" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                getPedidoMedida($(this).val(), 'get');"/>
                                </div>
                            </div>
                              <div class="col-lg-4">
                                <label>Nombre </label>
                                <input type="text" class="form-control input-sm pedidos" value="" id="nombrePedido" tabindex="2" readonly=""/>
                            </div>
                            <div class="col-lg-2">
                                <label>Fecha Entrega:</label>
                                <div class='input-group date' id="fechaEntrega">
                                    <input type='text' class="form-control input-sm" readonly="" value="<?= $general->dateViews; ?>"/>
                                    <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <label>Fecha Graduacion:</label>
                                <div class='input-group date' id="fechaGraduacion">
                                    <input type='text' class="form-control input-sm" readonly="" value="<?= $general->dateViews; ?>"/>
                                    <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <label>Fecha A Recoger:</label>
                                <div class='input-group date' id="fechaRecoger">
                                    <input type='text' class="form-control input-sm" readonly="" value="<?= $general->dateViews; ?>"/>
                                    <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                          

                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label>Observaciones</label>
                                <textarea type="text" class="form-control input-sm" value="" id="observacionesT" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                &nbsp;<br/>
                                <button type="button" id="add" class="btn btn-success btn-sm" onclick="generarTomaMedidas();">
                                    <span class="fa fa-check"></span>&nbsp;Generar Toma De Medida
                                </button>
                                <button type="button" id="edit" class="btn btn-warning btn-sm" onclick="actualizarTomaMedidas();">
                                    <span class="fa fa-refresh"></span>&nbsp;Actualizar
                                </button>
                                <button type="button" id="delete" class="btn btn-danger btn-sm" id="cancelarVenta" onclick="cancelarTomaMedidas();">
                                    <span class="fa fa-trash"></span>&nbsp;Cancelar
                                </button>
                                <button type="button" id="back" class="btn btn-info btn-sm" id="cancelarVenta" onclick="loadConsultaTomaMedidas();">
                                    <span class="fa fa-undo"></span>&nbsp;Regresar
                                </button>   
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- /Documento y Info Cliente -->

            <!-- Listado de Productos venta -->
            <div class="clear">&nbsp;</div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <section class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr class="info">
                                            <td>No.</td>
                                            <td style="width: 10px;">&nbsp;</td>
                                            <td>Apellido</td>
                                            <td>Nombre</td>
                                            <td>Hombros</td>
                                            <td>Manga</td>
                                            <td>Altura</td>
                                            <td>Cabeza</td>
                                            <td>Recibo</td>
                                            <td>Observaciones</td>
                                        </tr>
                                    </thead>
                                    <thead>
                                        <tr class="info">
                                            <td> </td>
                                            <td style="width: 10px;">&nbsp;</td>
                                            <td><input type="text" class="form-control input-sm medida" id="apellido"></td>
                                            <td><input type="text" class="form-control input-sm medida" id="nombre"></td>
                                            <td style="width: 60px;"><input type="text" class="form-control input-sm medidas" id="hombro"></td>
                                            <td style="width: 60px;"><input type="text" class="form-control input-sm medidas" id="manga"></td>
                                            <td style="width: 60px;"><input type="text" class="form-control input-sm medidas" id="altura"></td>
                                            <td style="width: 60px;"><input type="text" class="form-control input-sm medidas" id="cabeza"></td>
                                            <td style="width: 60px;"><input type="text" class="form-control input-sm medidas" id="recibo"></td>
                                            <td><input type="text" class="form-control input-sm medida" id="observaciones" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                        agregarTomaMedidasDetalle();"></td>
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
        </div>
        <!-- END Listado de Productos venta -->
    </body>
</html>