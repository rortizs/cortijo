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
                        <button class="btn btn-primary btn-sm" onclick="consultarFacturasSuple();">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-info btn-sm" onclick="reImprimirFactura();">
                            <i class="fa fa-print"></i> Re-Imprimir
                        </button>
                        <button class="btn-reImprimirEnvio btn btn-info btn-sm" onclick="reImprimirEnvio();">
                            <i class="fa fa-print"></i> Re-Imprimir Envio
                        </button>
                        <button class="btn-reImprimirGarantia btn btn-info btn-sm" onclick="reImprimirGarantia();">
                            <i class="fa fa-print"></i> Re-Imprimir Garantia
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarFactura();">
                            <i class="fa fa-trash"></i> Eliminar Registro
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="anularFactura();">
                            <i class="fa fa-trash"></i> Anular Factura
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarReporteFacturacionSuple();">
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
                            <td>&nbsp;</td>
                            <td>Fecha</td>
                            <td>Tipo Venta</td>
                            <td>Documento</td>
                            <td>NIT</td>
                            <td>Nombre Cliente</td>
                            <td>Sucursal</td>
                            <td>Vendedor</td>
                            <td>Estatus</td>
                            <td>Fecha Anulacion</td>
                            <td class="text-right">Galones</td>
                            <td class="text-right">Anticipo</td>
                            <td class="text-right">Saldo</td>
                            <td class="text-right">Subtotal</td>
                            <td class="text-right">IVA</td>
                            <td class="text-right">Total Venta</td>
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