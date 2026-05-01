<?php
/** Reportes - Ventas Por Producto
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros de Reporte
            </header>
            <div class="panel-body">
                <!--
                <div class="col-lg-2">
                    <label>Fecha Inicio</label>
                    <div class='input-group date' id='fechaInicio'>
                        <input type='text' class="form-control input-sm"/>
                        <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-lg-2">
                    <label>Fecha Fin</label>
                    <div class='input-group date' id='fechaFin'>
                        <input type='text' class="form-control input-sm"/>
                        <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                        </span>
                    </div>
                </div>
                -->
                <div class="col-lg-4">
                    <label>Producto</label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_productos', 'Catalogo de Productos', 'compras');" style="z-index: 999;">Buscar <i class="fa fa-search"></i></button>
                        </span>
                        <input type="text" class="form-control input-sm" id="codigo" placeholder="Ingrese código del Producto">
                    </div>
                </div>
                <div class="col-lg-5">
                    <label>&nbsp;</label><br/>
                    <button type="button" class="btn btn-primary btn-sm" onclick="generarHistorialCostos();">
                        <span class="fa fa-list-alt"></span> Generar Reporte
                    </button>
                    <!--
                    <button type="button" class="btn btn-success btn-sm" onclick="generarReporteVentasExcel();">
                        <span class="fa fa-file-excel-o"></span> Exportar a Excel
                    </button>
                    -->
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row" id="divReporte">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body" style="overflow-y: auto !important;">
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td>Fecha Compra</td>
                            <td>Documento</td>
                            <td>Codigo</td>
                            <td>Descripcion</td>
                            <td class="text-right">Exist. Inventario</td>
                            <td class="text-right">Costo Unitario Inventario</td>
                            <td class="text-right">Costo total Inventario</td>
                            <td class="text-right">Ultima Compra</td>
                            <td class="text-right">Costo Unitario Compra</td>
                            <td class="text-right">Costo total Compra</td>
                            <td class="text-right">Costo Promedio</td>
                        </tr>
                    </thead>
                    <tbody id="detalle"></tbody>
                </table>
            </div>
        </section>
    </div>
</div>
