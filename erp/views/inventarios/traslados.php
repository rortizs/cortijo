<?php
/**
 * POS /Traslados desde modulo de administracion
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
require_once ("../../models/inventarios.php");
$inventarios = new Inventarios();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Informacion de Traslado
                <div class="pull-right" style="position: relative; bottom: 5px !important;">
                    <button class="btn btn-info btn-sm" onclick="loadData('vw_traslados', 'Inventarios', 'Listado de Traslados', 0, 0, 0);">
                        <i class="fa fa-undo"></i> Regresar
                    </button>
                    <button class="btn btn-success btn-sm" onclick="finalizarTrasladoBodega();">
                        <i class="fa fa-upload"></i> Guardar
                    </button>
                </div>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label><i class="fa fa-calendar"></i> Fecha</label>
                        <input type="text" id="fechaOperacion" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-2">
                        <label><i class="fa fa-filter"></i> Salida de</label>
                        <select class="form-control input-sm" id="salidaDe" onchange="salidaTraslados();">
                            <option value="">[Seleccione...]</option>
                            <option value="1">Bodega</option>
                            <option value="2">Sucursal</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label><i class="fa fa-map-marker"></i> Lugar</label>
                        <select class="form-control input-sm" id="idPuntoSalida">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Ingreso A</label>
                        <select class="form-control input-sm" id="ingresoA" onchange="ingresoA();">
                            <option value="">[Seleccione...]</option>
                            <option value="1">BODEGA</option>
                            <option value="2">SUCURSAL</option>
                        </select>
                    </div>
                    <div class="col-lg-2" id="bodegas">
                        <label>Bodega de Entrada</label>
                        <select class="form-control input-sm" id="idBodegasIngreso">
                        </select>
                    </div>
                    <div class="col-lg-2" id="sucursales">
                        <label>Sucursal de Entrada</label>
                        <select class="form-control input-sm" id="idSucursalesIngreso">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label><i class="fa fa-filter"></i> Tipo Documento</label>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label><i class="fa fa-hashtag"></i> Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly=""/>
                    </div>
                    <div class="col-lg-12">
                        <label>Observaciones</label>
                        <textarea id="observaciones" class="form-control input-sm"></textarea>
                    </div>    
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Listado de Productos a Trasladar
            </header>
            <div class="panel-body">
                <input type="hidden" id="numeroProductos"/>
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">    
                    <thead>
                        <tr class="info">
                            <td colspan="2" style="width: 23% !important;">
                                <label>Código Producto</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-sm" type="button" onclick="busqueda('inventario', 'Consulta de Inventarios', 'traslados');">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                    <input type="text" class="form-control input-sm" id="codigo">
                                </div>
                            </td>
                            <td style="width: 55% !important;">
                                <label>Descripción</label>
                                <input type="text" class="form-control input-sm" id="descProducto" readonly="">
                                <input type="hidden" class="form-control input-sm" id="idProducto"/>
                                <input type="hidden" class="form-control input-sm" id="utilizaSerie"/>
                            </td>
                            <td class="text-right" style="width: 10% !important;">
                                <label>Existencia</label>
                                <input type="text" class="form-control input-sm facturacion" id="existencia" readonly=""/>
                            </td>
                            <td class="text-right" style="width: 10% !important;">
                                <label>Cantidad</label>
                                <input type="text" class="form-control input-sm facturacion" id="cantidad" value="" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                            addItemTraslado();" style="text-align: right !important;">
                            </td>
                        </tr>
                    </thead>
                    <tbody id="detalle">
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>