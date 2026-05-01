<?php
/* CAJA - RECIBOS
 * 
 */
session_start();
?>
<div class="row">
    <div class="col-lg-9 col-lg-offset-1">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label>Documento</label>
                        <input type="hidden" id="idDocumentosCorrelativos" value=""/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" value=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Sucursal</label>
                        <select id="idSucursales" class="form-control input-sm"></select>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha</label>
                        <input type="text" class="form-control input-sm" id="fechaFactura" value=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Galones</label>
                        <input type="text" class="form-control input-sm" id="galones" name="galones" readonly=""/>
                    </div>
                    <div class="col-lg-2">
                        <label>Monto</label>
                        <input type="text" class="form-control input-sm" id="monto" name="monto" readonly=""/>
                    </div>
                    <div class="col-lg-6">
                        <label>Cliente</label>
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
                        <label>Observaciones</label>
                        <textarea id="motivo" class="form-control input-sm" style="text-transform: uppercase !important; text-align: left !important;"></textarea>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="col-lg-12" style="text-align: right;">
                        <button class="btn btn-primary btn-sm" onclick="consultarValesLiquidar();">
                            <i class="fa fa-list"></i> Consulta de Vales
                        </button>
                        <button class="btn btn-success btn-sm" onclick="liquidarVales();">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="loadLiquidacionVales();">
                            <i class="fa fa-trash"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-lg-9 col-lg-offset-1">
        <section class="panel">
            <header class="panel-heading">
                LISTADO DE VALES
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-striped table-bordered" cellspacing="0">
                            <thead>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Documento</td>
                                    <td>Fecha</td>
                                    <td>NIT</td>
                                    <td>Solicitado Por</td>
                                    <td>Realizado Por</td>
                                    <td>Estado</td>
                                    <td align="right">Valor</td>
                                    <td align="right">Galones</td>
                                    <td align="right">Precio Unitario</td>
                                    <td align="right">Costo</td>
                                    <td align="right">Costo Total</td>
                                </tr>
                            </thead>
                            <tbody id="detalle">
                            </tbody>
                            <thead id="summary">
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
