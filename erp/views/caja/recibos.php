<?php
/* CAJA - RECIBOS
 * 
 */
session_start();
?>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
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
                    <div class="col-lg-3">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value=""/>
                    </div>
                    <div class="col-lg-3">
                        <label>Fecha</label>
                        <input type="date" class="form-control input-sm" id="created_at" value="<?=date('Y-m-d');?>"/>
                    </div>
                    <div class="col-lg-3">
                        <label>Monto</label>
                        <input type="text" class="form-control input-sm" id="monto" name="monto" value=""/>
                    </div>
                    <div class="col-lg-4">
                        <label>INGRESE NIT/CODIGO</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_clientes', 'Listado de Clientes', 'clientes');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idClientes" value="">
                            <input type="text" class="form-control input-sm" id="nit" value="" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadClientesNit();"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>CODIGO</label>
                        <input type="text" class="form-control input-sm facturacion" value="" id="codigoC" tabindex="3"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Nombre Facturación</label>
                        <input type="text" class="form-control input-sm nombre" value="" id="nombre" tabindex="3"/>
                    </div>
                    <div class="col-lg-12">
                        <label>Observaciones</label>
                        <textarea id="motivo" class="form-control input-sm" style="text-transform: uppercase !important; text-align: left !important;"></textarea>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-lg-12" style="text-align: right;">
                        <button class="btn btn-primary btn-sm" onclick="cxc();">
                            <i class="fa fa-list"></i> Consulta de Facturas
                        </button>
                        <button class="btn btn-success btn-sm" onclick="generarReciboCXC();">
                            <i class="fa fa-print"></i> Generar Recibo
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
    <div class="col-lg-6">
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
                                    <td>NO. DOCUMENTO</td>
                                    <td>BANCO (SI FUERA DEPOSITO O CHEQUE)</td>
                                    <td>VALOR</td>
                                    <td>FECHA COBRO</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control input-sm" id="chequeNo"/></td>
                                    <td><input type="text" class="form-control input-sm" id="nombreDelBanco"/></td>
                                    <td><input type="number" class="form-control input-sm" id="valor"/></td>
                                    <td><input type="date" class="form-control input-sm" id="fechaCobro"/></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-6">
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
