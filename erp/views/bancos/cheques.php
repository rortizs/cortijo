<?php
/*
 * BANCOS - CHEQUES
 * 
 */
session_start();
require_once("../../models/bancos.php");
$bancos = new Bancos();
$getCuentasBancarias = $bancos->getCuentasBancarias($_SESSION['idEmpresa']);
$getCheque = $bancos->getCheque($_REQUEST['idCheque']);
//print_r($getCheque);
?>
<input type="hidden" id="status" value="<?= $getCheque['status']; ?>">
<input type="hidden" id="idCheques" value="<?= $_REQUEST['idCheque']; ?>">
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Datos del Cheque
                <div class="col-lg-1 pull-right btn-back">
                    <button class="btn btn-info btn-sm" onclick="loadData('vw_cheques', 'Bancos', 'Listado de Cheques', 0, 0, 0);">
                        <i class="fa fa-undo"></i> Salir
                    </button>
                </div>
                <div class="col-lg-2 pull-right alert alert-warning statusBancos">
                    <?= $getCheque['statusDocumento']; ?>
                </div>
            </header>
            <div class="panel-body">
                <div class="clear">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-2">
                        <label>No. Cuenta Bancaria</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button id="btnCuentaBancaria" class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_cuentasBancarias', 'Cuentas Bancarias', 'cuentasBancarias');">
                                    <i class="fa fa-question"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idCuentaBancaria" value="<?= $getCheque['idCuentasBancarias']; ?>">
                            <input class="form-control input-sm" type="text" readonly="" id="cuentaBancaria" value="<?= $getCheque['numeroCuenta']; ?>"/>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>Nombre Cuenta</label>
                        <input type="text" class="form-control input-sm" id="nombreCuenta" readonly="" value="<?= $getCheque['nombreCuenta']; ?>"/>
                    </div>
                    <div class="col-lg-3">
                        <label>Banco</label>
                        <input type="text" class="form-control input-sm" id="banco" readonly="" value="<?= $getCheque['idBancos']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Saldo en Libro</label>
                        <input type="text" class="form-control input-sm" id="saldoLibros" readonly="" value="<?= $getCheque['saldoLibros']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Saldo en Banco</label>
                        <input type="text" class="form-control input-sm" id="saldoBanco" readonly="" value="<?= $getCheque['saldoBanco']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <label>Documento</label>
                        <input type="hidden" id="idDocumentosCorrelativos" value="<?= $getCheque['idDocumentosCorrelativos']; ?>"/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value="<?= $getCheque['correlativo']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>No. Cheque</label>
                        <input type="text" class="form-control input-sm" id="noCheque" name="noCheque" value="<?= $getCheque['noCheque']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha de Cheque</label>
                        <input type="text" class="form-control input-sm" id="fechaCheque" value="<?= $getCheque['fechaCheque2']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha de Cobro</label>
                        <input type="text" class="form-control input-sm" id="fechaCobro" value="<?= $getCheque['fechaCobro2']; ?>"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Monto</label>
                        <input type="text" class="form-control input-sm" id="monto" name="monto" value="<?= $getCheque['monto']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Pago a la orden de:</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('proveedores', 'Listado de Proveedores', 'proveedores');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idProveedores" value="">
                            <input type="text" class="form-control input-sm" id="nombre" value="<?= $getCheque['nombreCheque']; ?>"/>
                        </div>
                        <label style="position: relative; top: -14px !important;"><input type="checkbox" value="1" id="noNegociable"> No Negociable</label>
                    </div>
                    <div class="col-lg-6">
                        <label>Motivo</label>
                        <textarea id="motivo" class="form-control input-sm" style="text-transform: uppercase !important; text-align: left !important;"><?= $getCheque['motivo']; ?></textarea>
                    </div>
                    <div class="col-lg-6" style="text-align: right;">
                        &nbsp;<br/>
                        <button class="btn-cxp btn btn-primary btn-sm" onclick="cxp();">
                            <i class="fa fa-list"></i> CXP
                        </button>
                        <button class="btn-save btn btn-success btn-sm" onclick="guardarCheque();">
                            <i class="fa fa-cloud-upload"></i> Guardar
                        </button>
                        <button class="btn-update btn btn-warning btn-sm" onclick="actualizarCheque();">
                            <i class="fa fa-cloud-upload"></i> Actualizar
                        </button>
                        <button class="btn-print btn btn-info btn-sm" onclick="imprimirCheque('<?= $_REQUEST['idCheque']; ?>');">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                        <button class="btn-anular btn btn-danger btn-sm" onclick="anularCheque('<?= $_REQUEST['idCheque']; ?>');">
                            <i class="fa fa-times"></i> Anular
                        </button>
                        <button class="btn-eliminar btn btn-danger btn-sm" onclick="eliminarCheque('<?= $_REQUEST['idCheque']; ?>');">
                            <i class="fa fa-trash"></i> Eliminar
                        </button>
                        <button class="btn-conciliar btn btn-success btn-sm" onclick="conciliarCheque('<?= $_REQUEST['idCheque']; ?>');">
                            <i class="fa fa-check"></i> Conciliar
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
                        <a data-toggle="tab" href="#cxp">Cuenta por Pagar</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#contabilidad">Contabilidad</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#cajaChica">Liquidaciones</a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div id="cxp" class="tab-pane active">
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
                                            <td>Valor Ultimo Abono</td>
                                            <td style="width: 15% !important; text-align:right">Abono</td>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle">
                                    </tbody>
                                    <thead>
                                        <tr>
                                            <td colspan="6">Total Abonos</td>
                                            <td><input class="form-control input-sm" type="text" style="text-align:right;" value="0.00" id="totalAbonos" readonly=""/></td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
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
                                    <input type="hidden" id="idFormato" value="<?= $getCheque['idFormatos'] ?: 0; ?>">
                                    <input type="hidden" id="idPartida" value="<?= $getCheque['idPartidas'] ?: 0; ?>">
                                    <input class="form-control input-sm" type="text" readonly="" id="formato" value="<?= $getCheque['formato']; ?>">
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
                    <div id="cajaChica" class="tab-pane">
                        <div class="row">
                            <div class="col-lg-12">
                                <label><i class="fa fa-list"></i> Seleccione liquidacion</label>
                                <div class="input-group m-bot15">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-sm" id="liquidacion" type="button" onclick="busqueda('vw_cajaChica', 'Liquidaciones', 'cajaChica');">
                                            <i class="fa fa-search"></i> Liquidaciones
                                        </button>
                                        <button class="btn btn-primary btn-sm" id="reintegro" type="button" onclick="busqueda('vw_cajaChica', 'Liquidaciones', 'cajaChicaReintegros');">
                                            <i class="fa fa-search"></i> Reintegros 
                                        </button>
                                    </span>
                                    <input class="form-control input-sm" type="hidden" readonly="" id="idCajaChica"/>
                                    <input class="form-control input-sm" type="hidden" readonly="" id="idTipoLiquidaciones"/>
                                    <input class="form-control input-sm" type="hidden" readonly="" id="moduloLiquidaciones"/>
                                    <input class="form-control input-sm" type="text" readonly="" id="cajaChicaDoc"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
