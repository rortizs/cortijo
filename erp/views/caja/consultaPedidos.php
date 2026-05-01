<?php
require_once('../../models/general.php');
$general = new General();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros de Consulta
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label>Fecha Inicio:</label>
                        <div class='input-group date' id="fechaInicio">
                            <input type='text' class="form-control input-sm" value="<?= $general->dateViews; ?>"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha Fin:</label>
                        <div class='input-group date' id="fechaFin">
                            <input type='text' class="form-control input-sm" value="<?= $general->dateViews; ?>"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>No. Pedido</label>
                        <input type='text' class="form-control input-sm" id="noPedido"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Estado Pedido</label>
                        <select id="estadoPedido" class="form-control input-sm">
                            <option value="">[Seleccione...]</option>
                            <option value="1">Abierto</option>
                            <option value="2">Cancelado</option>
                            <option value="3">Facturado</option>
                            <option value="4">Confirmado</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Vendedor</label>
                        <select id="vendedores" class="form-control input-sm">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Cliente</label>
                        <input type='text' class="form-control input-sm" id="cliente"/>
                    </div>
                    <div class="col-lg-12">
                        <label>&nbsp;</label><br/>
                        <button class="btn btn-primary btn-sm" onclick="consultarPedidos();">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-warning btn-sm" id="update" onclick="editarPedido();">
                            <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                        </button>
                        <button class="btn btn-info btn-sm" onclick="reImprimirPedido();">
                            <i class="fa fa-print"></i> Re-Imprimir
                        </button>
                        <button id="reImprimirPedidoMercaderia" class="btn btn-info btn-sm" onclick="reImprimirPedidoMercaderia();">
                            <i class="fa fa-print"></i> Re-Imprimir Pedido Mercaderia
                        </button>
                        <button id="reImprimirExamenesPedido" class="btn btn-info btn-sm" onclick="reImprimirExamenesPedido();">
                            <i class="fa fa-print"></i> Re-Imprimir Examenes Pedido
                        </button>
                        <button id="cancel" class="btn btn-danger btn-sm" onclick="cancelarPedido();">
                            <i class="fa fa-trash"></i> Cancelar Pedido
                        </button>
                        <button id="confirmarPedido" class="btn btn-success btn-sm" onclick="confirmarPedido();">
                            <i class="fa fa-check"></i> Confirmar Pedido para Facturacion
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarReportePedidos();">
                            <span class="fa fa-file-excel-o"></span> Exportar a Excel
                        </button>
                        <!-- <button type="button" class="btn btn-danger btn-sm" onclick="generarConsolidadoPedidos();">
                            <span class="fa fa-file-pdf-o"></span> Generar Consolidado
                        </button> -->
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body table-responsive" id="reportContainer">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">  
                    <thead>
                        <tr>
                            <td>&nbsp;</td>
                            <td>Fecha</td>
                            <td>No. Documento</td>
                            <td>NIT</td>
                            <td>Nombre Cliente</td>
                            <td>Direccion</td>
                            <td>Observaciones</td>
                            <td>Sucursal</td>
                            <td>Vendedor</td>
                            <td>Estatus</td>
                            <td>No. Doc Venta</td>
                            <td class="text-right">Total</td>
                        </tr>
                    </thead>
                    <tbody id="detalle">
                    </tbody>
                    <thead id="summary">
                    </thead>
                </table>
            </div>
        </section>
    </div>
</div>
