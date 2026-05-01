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
                        <label>No. Cotizacion</label>
                        <input type='text' class="form-control input-sm" id="noCotizacion"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Estado Cotizacion</label>
                        <select id="estadoCotizacion" class="form-control input-sm">
                            <option value="">[Seleccione...]</option>
                            <option value="1">Abierto</option>
                            <option value="2">Cancelado</option>
                            <option value="3">Procesada</option>
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
                </div>
                <div class="col-lg-12">
                    <label>&nbsp;</label><br/>
                    <button class="btn btn-primary btn-sm" id="add" onclick="loadModuloCotizaciones();">
                        <i class="fa fa-plus"></i> Nuevo
                    </button>
                    <button class="btn btn-warning btn-sm" id="update" onclick="editarCotizacion();">
                        <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="consultarCotizaciones();">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                    <button class="btn btn-info btn-sm" onclick="reImprimirCotizacion();">
                        <i class="fa fa-print"></i> Re-Imprimir
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportarReporteCotizaciones();">
                        <span class="fa fa-file-excel-o"></span> Exportar a Excel
                    </button>
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
                            <td>No. Pedido</td>
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
