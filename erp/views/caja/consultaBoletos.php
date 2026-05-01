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
                        <label>No. Pagare</label>
                        <input type='text' class="form-control input-sm" id="noPagare"/>
                    </div>
                    <div class="col-lg-2">
                        <label>No. Boleto</label>
                        <input type='text' class="form-control input-sm" id="noBoleto"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Proveedor</label>
                        <input type='text' class="form-control input-sm" id="proveedor"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Linea Aerea</label>
                        <input type='text' class="form-control input-sm" id="lineaAerea"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Vendedor</label>
                        <input type='text' class="form-control input-sm" id="vendedores"/>
                    </div>
                    <div class="col-lg-2">
                        <label>Cliente</label>
                        <input type='text' class="form-control input-sm" id="cliente"/>
                    </div>  
                    <div class="col-lg-2">
                        <label>Tipo Pago</label>
                        <select id="tipoPago" class="form-control input-sm">                       
                            <option value="0">Todos..</option>
                            <option value="1">Tarjeta</option>
                            <option value="2">Efectivo</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Pasajero</label>
                        <input type='text' class="form-control input-sm" id="pasajero"/>
                    </div>   
                    <div class="col-lg-12">
                        <label>&nbsp;</label><br/>
                        <button class="btn btn-primary btn-sm" onclick="consultarBoletos();">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="verBoleto();">
                            <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarReporteBoletos();">
                            <span class="fa fa-file-excel-o"></span> Exportar a Excel
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="ExportarReporteBoletosPDF();">
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
                            <td>Prov</td>
                            <td>Cod Cliente</td>
                            <td>Pagare</td>
                            <td>Boleto</td>
                            <td>Fecha</td>
                            <td>Pasajero</td>
                            <td>Tarifa</td>
                            <td>Imp.Nac</td>
                            <td>Imp. Ext</td>
                            <td >Total</td>
                            <td>Tarjeta</td>
                            <td>Comision</td>
                            <td>Iva</td>
                            <td>A Pagar</td>
                            <td>Tasa</td>
                            <td>Vendedor</td>
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