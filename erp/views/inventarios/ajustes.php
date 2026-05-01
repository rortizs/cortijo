<?php
/**
 * POS / Ajustes
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
?>
<input type="hidden" id="numeroProductos"/>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Informacion de Ajuste
                <div class="pull-right" style="position: relative; bottom: 5px !important;">
                    <button class="btn btn-info btn-sm" onclick="loadData('vw_ajustes', 'Inventarios', 'Listado de Ajustes', 0, 0, 0);">
                        <i class="fa fa-undo"></i> Regresar
                    </button>
                    <button class="btn btn-success btn-sm" onclick="procesarAjuste();">
                        <i class="fa fa-upload"></i> Guardar
                    </button>
                </div>
            </header>
            <div class="panel-body">
                <div class="col-lg-3">
                    <label><i class="fa fa-calendar"></i> Fecha</label>
                    <input type="text" id="fechaOperacion" class="form-control input-sm"/>
                </div>
                <div class="col-lg-3">
                    <label><i class="fa fa-filter"></i> Inventario de</label>
                    <select class="form-control input-sm" id="ingresoA" onchange="ingresoA2();">
                        <option value="">[Seleccione...]</option>
                        <option value="1">Bodega</option>
                        <option value="2">Sucursal</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label><i class="fa fa-map-marker"></i> Lugar</label>
                    <select class="form-control input-sm" id="idPuntoIngreso">
                    </select>
                </div>
                <div class="col-lg-3">
                    <label>Tipo de Ajuste</label>
                    <select class="form-control input-sm" id="operacion" onchange="loadDocAjustes();">
                        <option value="">[Seleccione...]</option>
                        <option value="1">Positivo</option>
                        <option value="2">Negativo</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label><i class="fa fa-filter"></i> Tipo Documento</label>
                    <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                    </select>
                </div>
                <div class="col-lg-3">
                    <label><i class="fa fa-hashtag"></i> Correlativo</label>
                    <input type="text" class="form-control input-sm" id="correlativo" readonly=""/>
                </div>
                <div class="col-lg-3">
                    <label>SERIE FACTURA</label>
                    <input type="text" class="form-control input-sm" id="serieFactura"/>
                </div>
                <div class="col-lg-3">
                    <label>NO FACTURA</label>
                    <input type="text" class="form-control input-sm" id="noFactura"/>
                </div>
                <div class="col-lg-12">
                    <label>Descripcion</label>
                    <textarea class="form-control input-sm" id="descripcionAjuste" style="width: 100% !important;"></textarea>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Detalle de Productos en Ajuste
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td colspan="2">
                                        <label>Código Producto</label>
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_productos', 'Listado de Productos', 'ajustes');">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </span>
                                            <input type="text" class="form-control input-sm" id="codigo" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                        loadProductoSKU('ajustes');" tabindex="6">
                                        </div>
                                    </td>
                                    <td style="width: 49% !important;">
                                        <label>Descripción</label>
                                        <input type="text" class="form-control input-sm" id="descProducto" readonly="">
                                        <input type="hidden" class="form-control input-sm" id="idProducto"/>
                                        <input type="hidden" class="form-control input-sm" id="utilizaSerie"/>
                                    </td>
                                    <?php
                                    if ($_SESSION['dbProject'] === 'pos_kasualcosmeticos') {
                                        ?>
                                        <td><label>Item</label></td>
                                        <td><label>Marca</label></td>
                                        <?php
                                    }
                                    ?>
                                    <td class="text-right" style="width: 15% !important;">
                                        <label>Tipo de Producto</label>
                                        <input type="text" class="form-control input-sm" id="tipoProducto" readonly="">
                                    </td>
                                    <td class="text-right" style="width: 15% !important;">
                                        <label>Cantidad</label>
                                        <input type="text" class="form-control input-sm facturacion" id="cantidad" value="" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                    addItemAjuste();" style="text-align: right !important;">
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="detalle">
                            </tbody>
                            <thead id="summary">
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>