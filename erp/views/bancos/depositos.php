<?php
/*
 * BANCOS - CHEQUES
 * 
 */
session_start();
require_once("../../models/bancos.php");
$bancos = new Bancos();
$getCuentasBancarias = $bancos->getCuentasBancarias($_SESSION['idEmpresa']);
$getDeposito = $bancos->getDepositos($_REQUEST['idDeposito']);
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Datos del Deposito
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
                            <input type="hidden" id="idCuentaBancaria" value="<?= $getDeposito['idCuentasBancarias']; ?>">
                            <input class="form-control input-sm" type="text" readonly="" id="cuentaBancaria" value="<?= $getDeposito['numeroCuenta']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>Nombre Cuenta</label>
                        <input type="text" class="form-control input-sm" id="nombreCuenta" readonly="" value="<?= $getDeposito['nombreCuenta']; ?>"/>
                    </div>
                    <div class="col-lg-3">
                        <label>Banco</label>
                        <input type="text" class="form-control input-sm" id="banco" readonly=""  value="<?= $getDeposito['idBancos']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Saldo en Libro</label>
                        <input type="text" class="form-control input-sm" id="saldoLibros" readonly="" value="<?= $getDeposito['saldoLibros']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Saldo en Banco</label>
                        <input type="text" class="form-control input-sm" id="saldoBanco" readonly="" value="<?= $getDeposito['saldoBanco']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <label>Documento</label>
                        <input type="hidden" id="idDocumentosCorrelativos" value="<?= $getCompra['idDocumentosCorrelativos']; ?>"/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value="<?= $getDeposito['correlativo']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>No. Boleta</label>
                        <input type="text" class="form-control input-sm" id="noBoleta" name="noBoleta" value="<?= $getDeposito['correlativo']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha de Deposito</label>
                        <input type="text" class="form-control input-sm" id="fechaDeposito" value="<?= $getDeposito['fecha']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Monto</label>
                        <input type="text" class="form-control input-sm" id="monto" name="monto" value="<?= $getDeposito['monto']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Realizado Por:</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_clientes', 'Listado de Clientes', 'clientes');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idClientes" value="">
                            <input type="hidden" id="nit" value="">
                            <input type="text" class="form-control input-sm" id="nombre" value="<?= $getDeposito['nombreDeposito']; ?>"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Facturas Liquidadas / Motivo</label>
                        <textarea id="motivo" class="form-control input-sm" style="text-transform: uppercase !important; text-align: left !important;"><?= $getDeposito['motivo']; ?></textarea>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-lg-6 pull-right" style="text-align: right;">
                        &nbsp;<br/>
                        <button class="btn btn-primary btn-sm" onclick="cxc();">
                            <i class="fa fa-list"></i> Consulta de Facturas
                        </button>
                        <button class="btn btn-success btn-sm" onclick="guardarDeposito();">
                            <i class="fa fa-floppy-o"></i> Guardar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="loadDepositos();">
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
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Partida Contable</label>
                                <div class="input-group m-bot15">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_formatos', 'Partidas Contables', 'partidaContable');">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </span>
                                    <input type="hidden" id="idDeposito" value="<?= $_REQUEST['idDeposito']; ?>">
                                    <input type="hidden" id="idFormato" value="<?= $getCompra['idFormatos']; ?>">
                                    <input type="hidden" id="idPartida" value="<?= $getCompra['idPartida']; ?>">
                                    <input class="form-control input-sm" type="text" readonly="" id="formato" value="<?= $getDeposito['formato']; ?>">
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
            </div>
        </section>
    </div>
</div>
