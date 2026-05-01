<?php
/* CAJA - RECIBOS
 * 
 */
session_start();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                DATOS DE RECIBO
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label>Documento</label>
                        <input type="hidden" id="idDocumentosCorrelativos" value=""/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha</label>
                        <input type="text" class="form-control input-sm" id="fechaRecibo" value=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Monto</label>
                        <input type="text" class="form-control input-sm" id="monto" name="monto" value=""/>
                    </div>
                    <div class="col-lg-3">
                        <label>Moneda</label>
                        <select class="form-control input-sm" id="idMonedas">
                            <option value="">[Seleccione...]</option>
                            <option value="1">QUETZALES</option>
                            <option value="2">DOLARES</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Recibimos de:</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_clientes', 'Listado de Clientes', 'clientes');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idClientes" value="">
                            <input type="hidden" id="nit" value="">
                            <input type="text" class="form-control input-sm" id="nombre" value=""/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Por concepto de:</label>
                        <textarea id="motivo" class="form-control input-sm" style="text-transform: uppercase !important; text-align: left !important;"></textarea>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-lg-12" style="text-align: right;">
                        <button class="btn btn-primary btn-sm" onclick="cxc();">
                            <i class="fa fa-list"></i> Consulta de Facturas
                        </button>
                        <button class="btn btn-success btn-sm" onclick="generarReciboDeCaja();">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="loadModuloRecibos();">
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
            <header class="panel-heading">
                FORMA DE PAGO
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped table-bordered" cellspacing="0">
                            <thead>
                                <tr>
                                    <td>CHEQUE NO.</td>
                                    <td>NO. DE CUENTA</td>
                                    <td>NOMBRE DEL BANCO</td>
                                    <td>NOMBRE DE LA CUENTA</td>
                                    <td>VALOR DE CHEQUE</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control input-sm" id="chequeNo"/></td>
                                    <td><input type="text" class="form-control input-sm" id="noDeCuenta"/></td>
                                    <td><input type="text" class="form-control input-sm" id="nombreDelBanco"/></td>
                                    <td><input type="text" class="form-control input-sm" id="nombreDelaCuenta"/></td>
                                    <td><input type="text" class="form-control input-sm" id="valorDeCheque"/></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                LISTADO DE FACTURAS
            </header>
            <div class="panel-body">
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
        </section>
    </div>
</div>
