<?php
/**
 * POS / Inventarios - Requisicion de Compras
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label>Tipo Documento</label>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly=""/>
                    </div>
                    <div class="col-lg-4">
                        <label>Solicitado Por</label>
                        <input type="text" class="form-control input-sm" id="solicitadoPor"/>
                    </div>
                    <div class="col-lg-3">
                        <label>Departamento </label>
                        <select class="form-control input-sm" id="idHrmDepartamentos">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <label>Observaciones </label>
                        <textarea id="observaciones" class="form-control input-sm"></textarea>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Listado de items Requisición
            </header>
            <div class="panel-body">
                <button class="btn btn-success btn-sm" onclick="guardarRequisicion();">
                    <i class="fa fa-floppy-o"></i> Guardar
                </button>
                <button class="btn btn-danger btn-sm" onclick="cancelarProcesoCompra('requisicionCompra');">
                    <i class="fa fa-trash"></i> Cancelar
                </button>
                <div class="clearfix">&nbsp;</div>
                <input type="hidden" id="numeroProductos"/>
                <input type="hidden" id="totalOC"/>
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr class="info">
                            <td colspan="2">Accion</td>
                            <td>Codigo</td>
                            <td>Descripcion</td>
                            <td>Unidad de Medida</td>
                            <td style="width: 10% !important;">Cantidad</td>
                        </tr>
                        <tr class="info">
                            <td colspan="2">
                                <button class="btn btn-primary btn-xs" onclick="busqueda('vw_productos', 'Catálogo de Productos','requisiciones');">
                                    <i class="fa fa-question"></i>
                                </button>
                                <button class="btn btn-primary btn-xs" onclick="addItemRequisionCompra();">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" id="codigo"/>
                                <input type="hidden" class="form-control input-sm" id="idProducto"/>
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" id="descProducto" style="text-transform: uppercase !important;"/>
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" id="unidadMedida"/>
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm" id="cantidad"/>
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