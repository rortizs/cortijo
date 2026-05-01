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
                        <label>Recibo</label>
                        <input id="recibo" type="text" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Factura</label>
                        <input id="factura" type="text" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Cliente</label>
                        <input id="cliente" type="text" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-12">
                        <label>&nbsp;</label><br/>
                        <button class="btn btn-primary btn-sm" onclick="consultarRecibos();">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarRecibo();">
                            <i class="fa fa-trash"></i> Eliminar Registro
                        </button>
                        <button class="btn btn-info btn-sm" onclick="reImprimirRecibo();">
                            <i class="fa fa-print"></i> Re-Imprimir
                        </button>
                        <!--
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarReporteFacturacion();">
                            <span class="fa fa-file-excel-o"></span> Exportar a Excel
                        </button>
                        -->
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
                            <td>&nbsp;</td>
                            <td>Fecha</td>
                            <td>Documento</td>
                            <td>Nombre Cliente</td>
                            <td>NIT</td>
                            <td>Factura</td>
                            <td class="text-right">Monto Factura</td>
                            <td class="text-right">Monto</td>
                            <td class="text-right">Saldo</td>
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
