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
                        <label>Tipo de Facturacion</label>
                        <select id="tipoFacturacion" class="form-control input-sm">
                            <option value="">[Seleccione...]</option>
                            <option value="1">Cargos Por Servicio</option>
                            <option value="2">Total</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>No. Factura</label>
                        <input id="documento" type="text" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-2">
                        <label>No. Pagare</label>
                        <input type='text' class="form-control input-sm" id="noPagare"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Cliente</label>
                        <input id="cliente" type="text" class="form-control input-sm"/>
                    </div>
                                      <div class="col-lg-2">
                        <label>Vendedor</label>
                        <input type='text' class="form-control input-sm" id="vendedores"/>
                    </div>


                    <div class="col-lg-12">
                        <label>&nbsp;</label><br/>


                        <button class="btn btn-primary btn-sm" onclick="loadModuloFacturacion();">
                            <i class="fa fa-plus"></i> Nuevo
                        </button>                       
                            <button class="btn btn-primary btn-sm" onclick="consultarFacturasLaxTravel();">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        <button class="btn btn-info btn-sm" onclick="reImprimirFacturaLaxTravel();">
                            <i class="fa fa-print"></i> Re-Imprimir
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="anularFactura();">
                            <i class="fa fa-exclamation-circle"></i> Anular Factura
                        </button>
                         <button class="btn btn-danger btn-sm" onclick="eliminarFactura();">
                            <i class="fa fa-trash"></i> Eliminar Registro
                        </button>
                        <br><br>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarReporteFacturacionLaxTravel();">
                            <span class="fa fa-file-excel-o"></span> Exportar a Excel
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="ExportarReporteFacturacionLaxTravelPDF();">
                            <i class="fa fa-file-pdf-o"></i> Exportar a PDF
                        </button>
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
                            <td>Tipo Venta</td>
                            <td>Tipo Facturacion</td>
                            <td>No. Factura</td>
                            <td>No. Pagare</td>
                            <td>NIT</td>
                            <td>Codigo Cliente</td>
                            <td>Nombre Cliente</td>
                            <td>Vendedor</td>
                            <td>Estatus</td>
                            <td>Total Cargos Por Servicio</td>
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
