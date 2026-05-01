<?php
session_start();
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
                        <label>Tipo de Venta</label>
                        <select id="tipoVentaCF" class="form-control input-sm">
                            <option value="">[Seleccione...]</option>
                            <option value="1">Contado</option>
                            <option value="2">Credito</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Estatus</label>
                        <select id="estatus" class="form-control input-sm">
                            <option value="">[Seleccione...]</option>
                            <option value="0">Activa</option>
                            <option value="1">Anulada</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Serie</label>
                        <input id="serieFactura" type="text" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Correlativo</label>
                        <input id="correlativoFactura" type="text" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Cliente</label>
                        <input id="cliente" type="text" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Sucursal</label>
                        <select id="idSucursalesCF" class="form-control input-sm">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Vendedor</label>
                        <select id="vendedores" class="form-control input-sm">
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label>&nbsp;</label><br/>
                        <button class="btn btn-primary btn-sm" onclick="consultarFacturasDetallado();">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarReporteFacturacionDetallado();">
                            <span class="fa fa-file-excel-o"></span> Exportar a Excel
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body table-responsive">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">  
                    <thead>
                        <tr>
                            <td>Fecha</td>
                            <td>Documento</td>
                            <td>Cliente</td>
                            <td>Vendedor</td>
                            <td class="text-right">Total Documento</td>
                            <td>Codigo Producto</td>
                            <td>Desc. Producto</td>
                            <td class="text-right">Cantidad</td>
                            <td class="text-right">Costo</td>
                            <td class="text-right">Precio Venta</td>
                            <td class="text-right">Total Venta</td>
                            <td class="text-right">Total Costo</td>
                            <td class="text-right">Utilidad</td>
                            <td class="text-right">Margen</td>
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
