<?php
/**
 * POS / Inventarios - Requisicion de Compras
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
require_once("../../models/inventarios.php");
$inventarios = new Inventarios();
$getRequisicion = $inventarios->getRequisicion($_REQUEST['idRequisicion']);
$documento = explode('-', $getRequisicion['documento']);
$estado = "";
$estadoBtn = "";
switch ($getRequisicion['status']) {
    case 'Pendiente':
        $estado = "info";
        break;
    case 'Aprobada':
        $estado = "success";
        $estadoBtn = "hidden";
        break;
    case 'Rechazada':
        $estado = "danger";
        $estadoBtn = "hidden";
        break;
}
?>
<input type="hidden" id="idRequisicion" value="<?= $_REQUEST['idRequisicion']; ?>"/>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-4">
                        <label>Tipo Documento</label>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                            <option value=""><?= $documento[0]; ?></option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" value="<?= $documento[1]; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Solicitado Por</label>
                        <input type="text" class="form-control input-sm" id="solicitadoPor" value="<?= $getRequisicion['solicitadoPor']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Departamento </label>
                        <select class="form-control input-sm" id="idHrmDepartamentos">
                            <option value=""><?= $getRequisicion['idHrmDepartamentos']; ?></option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Realizado Por </label>
                        <input type="text" class="form-control input-sm" value="<?= $getRequisicion['idUsuarios']; ?>"/>
                    </div>
                    <div class="col-lg-4">
                        <label>Fecha y Hora de Requisición </label>
                        <input type="text" class="form-control input-sm" value="<?= $getRequisicion['created_at']; ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Observaciones </label>
                        <textarea id="observaciones" class="form-control input-sm"><?= $getRequisicion['observaciones']; ?></textarea>
                    </div>
                    <div class="col-lg-6">
                        <label>Observaciones Estado Requisicion</label>
                        <textarea id="observaciones" class="form-control input-sm"><?= $getRequisicion['observacionesStatus']; ?></textarea>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Listado de Productos de Requisicion
                <div class="pull-right alert alert-<?= $estado; ?>" role="alert" style="position: relative;top: -15px !important;right: -15px !important;">
                    <strong><?= $getRequisicion['status']; ?></strong>
                </div>
            </header>
            <div class="panel-body">
                <button class="btn btn-info btn-sm <?=$estadoBtn;?>" onclick="busqueda('productos', 'Catálogo de Productos', 'RequisionCompras');">
                    <i class="fa fa-search"></i> Buscar Producto
                </button>
                <button class="btn btn-success btn-sm <?=$estadoBtn;?>" onclick="actualizarRequisicion();">
                    <i class="fa fa-refresh"></i> Actualizar
                </button>
                <button class="btn btn-success btn-sm <?=$estadoBtn;?>" onclick="aprobarRequisicion();">
                    <i class="fa fa-check"></i> Aprobar
                </button>
                <button class="btn btn-danger btn-sm <?=$estadoBtn;?>" onclick="aprobarRequisicion();">
                    <i class="fa fa-times"></i> Rechazar
                </button>
                <!--
                <button class="btn btn-danger btn-sm" onclick="cancelarCompra();">
                    <i class="fa fa-trash"></i> Cancelar
                </button>
                -->
                <div class="clearfix">&nbsp;</div>
                <input type="hidden" id="numeroProductos"/>
                <input type="hidden" id="totalOC"/>
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr class="info">
                            <td></td>
                            <td>No.</td>
                            <td>Codigo</td>
                            <td>Descripcion</td>
                            <td>Unidad de Medida</td>
                            <td align="right">Cantidad</td>
                        </tr>
                    </thead>
                    <tbody id="detalle">
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>