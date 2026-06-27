<?php
/** Reportes - Ventas Por Producto
 * @author Jonathan Juarez
 * @version 2.0 20161220
 */
session_start();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros de Reporte
            </header>
            <input type='hidden' id="idVendedor"/>
            <div class="panel-body">
                <div class="col-lg-12">
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
                    <div class="col-lg-3">
                        <label>Tipo de Familias</label>
                        <select class="form-control input-sm" id="nivelFamilias" onchange="loadFamilias()">
                            <option value="1">Familia Nivel 1</option>
                            <option value="2">Familia Nivel 2</option>
                            <option value="3">Familia Nivel 3</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label>Familias</label>
                        <select class="form-control input-sm" id="idFamilias">
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-4">
                        <label>Vendedor</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('usuarios', 'Usuarios', 'ventasPorVendedor');" style="z-index: 999;">Buscar <i class="fa fa-search"></i></button>
                            </span>
                            <input type="text" class="form-control input-sm" id="nombreVendedor">
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <label>&nbsp;</label><br/>
                        <button type="button" class="btn btn-primary btn-sm" onclick="generarReporteVentasPorVendedor();">
                            <span class="fa fa-list-alt"></span> Generar Reporte  
                        </button>             
                    </div>
                </div>
        </section>
    </div>
</div>
<div class="row" id="divReporte">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body" id="panel">
              
            </div>
        </section>
    </div>
</div>
