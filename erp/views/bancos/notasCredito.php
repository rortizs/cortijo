<?php
/*
 * BANCOS - CHEQUES
 * 
 */
session_start();
require_once("../../models/bancos.php");
$bancos = new Bancos();
$getCuentasBancarias = $bancos->getCuentasBancarias($_SESSION['idEmpresa']);
$getNCBancos = $bancos->getNCBancos($_REQUEST['idNCBancos']);
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Datos Nota Credito
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label>No. Cuenta Bancaria</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_cuentasBancarias', 'Cuentas Bancarias', 'cuentasBancarias');">
                                    <i class="fa fa-question"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idCuentaBancaria" value="<?= $getNCBancos['idCuentaBancaria']; ?>">
                            <input class="form-control input-sm" type="text" readonly="" id="cuentaBancaria" value="<?= $getNCBancos['numeroCuenta']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>Nombre Cuenta</label>
                        <input type="text" class="form-control input-sm" id="nombreCuenta" readonly="" value="<?= $getNCBancos['numeroCuenta']; ?>"/>
                    </div>
                    <div class="col-lg-3">
                        <label>Banco</label>
                        <input type="text" class="form-control input-sm" id="banco" readonly="" value="<?= $getNCBancos['idBancos']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Saldo en Libro</label>
                        <input type="text" class="form-control input-sm" id="saldoLibros" readonly="" value="<?= $getNCBancos['saldoLibros']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Saldo en Banco</label>
                        <input type="text" class="form-control input-sm" id="saldoBanco" readonly="" value="<?= $getNCBancos['saldoBanco']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label>Documento</label>
                        <input type="hidden" id="idDocumentosCorrelativos" value="<?= $getNCBancos['idDocumentosCorrelativos']; ?>"/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value="<?= $getNCBancos['correlativo']; ?>"/>
                    </div>
                    <div class="col-lg-3">
                        <label>No. Nota Credito</label>
                        <input type="text" class="form-control input-sm" id="noNotaCredito" name="noNotaCredito" value="<?= $getNCBancos['noNotaCredito']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha de Nota Credito</label>
                        <input type="text" class="form-control input-sm" id="fechaNC" value="<?= $getNCBancos['fechaNC']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Monto</label>
                        <input type="text" class="form-control input-sm" id="monto" name="monto" value="<?= $getNCBancos['monto']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Realizado Por:</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('clientes', 'Listado de Clientes', 'clientes');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idClientes" value="">
                            <input type="hidden" id="nit" value="">
                            <input type="text" class="form-control input-sm" id="nombre" value="<?= $getNCBancos['nombrePagoNC']; ?>"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Motivo</label>
                        <textarea id="motivo" class="form-control input-sm" style="text-transform: uppercase !important; text-align: left !important;"><?= $getNCBancos['motivo']; ?></textarea>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-lg-6 pull-right" style="text-align: right;">
                        &nbsp;<br/>
                        <button class="btn btn-primary btn-sm" onclick="cxc();">
                            <i class="fa fa-list"></i> CXC
                        </button>
                        <button class="btn btn-success btn-sm" id="save" onclick="guardarNCBancos();">
                            <i class="fa fa-floppy-o"></i> Guardar
                        </button>
                        <button class="btn btn-warning btn-sm" id="update" onclick="updateNCBancos('<?= $_REQUEST['idNCBancos']; ?>');">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="loadData('vw_notasCreditoBancos', 'Bancos', 'Listado de Notas de Debito', 0, 0, 0);">
                            <i class="fa fa-trash"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading tab-bg-dark-navy-blue">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#cxc">Cuenta por Cobrar</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#contabilidad">Contabilidad</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div id="cxc" class="tab-pane active">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-striped table-bordered" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <td>No.</td>
                                            <td>No. Factura</td>
                                            <td>Fecha Factura</td>
                                            <td style="width: 15% !important; text-align:right">Valor Factura</td>
                                            <td style="width: 15% !important; text-align:right">Saldo</td>
                                            <td>Fecha Ultimo Abono</td>
                                            <td style="width: 15% !important; text-align:right">Abono</td>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle">
                                    </tbody>
                                    <thead>
                                        <tr>
                                            <td colspan="3">Total Abonos</td>
                                            <td><input class="form-control input-sm" type="text" style="text-align:right;" id="totalFacturas" readonly=""/></td>
                                            <td><input class="form-control input-sm" type="text" style="text-align:right;" id="totalSaldo" readonly=""/></td>
                                            <td></td>
                                            <td><input class="form-control input-sm" type="text" style="text-align:right;" id="totalAbonos" readonly=""/></td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="contabilidad" class="tab-pane">
                        <div class="col-lg-12">
                            <label>Partida Contable</label>
                            <div class="input-group m-bot15">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_formatos', 'Partidas Contables', 'partidaContable');">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </span>
                                <input type="hidden" id="idFormato" value="<?= $getNCBancos['idFormatos']; ?>">
                                <input class="form-control input-sm" type="text" readonly="" id="formato"  value="<?= $getNCBancos['formato']; ?>">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-sm" type="button" onclick="verPartida();">
                                        <i class="fa fa-list"></i> Ver Integracion
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-12" id="detallePartida">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>