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
                    <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                        <label>No. Pedido</label>
                        <input type="hidden" value="" id="idPedidoHidden">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_pedidosMedidas', 'Consulta de Pedidos', 'pedidos2');"><i class="fa fa-search"></i></button>
                            </span>
                            <input type="text" class="form-control input-sm" id="noPedido" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        getPedidoMedida($(this).val(), 'get');"/>
                        </div>
                    </div>
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
                    </div>

                </div>
                <div class="col-lg-12">
                    <label>&nbsp;</label><br/>
                    <button class="btn btn-primary btn-sm" onclick="loadTomaMedidas(0,'get');">
                        <i class="fa fa-plus"></i> Nuevo
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="consultarTomaMedidas();">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="editarTomaMedidas();">
                        <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarTomaMedida();">
                        <i class="fa fa-trash"></i> Eliminar Registro
                    </button>
                    <button class="btn btn-info btn-sm" onclick="reImprimirTomaMedidas();">
                        <i class="fa fa-print"></i> Re-Imprimir
                    </button>
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
                            <td>No. Toma</td>
                            <td>No. Pedido</td>
                            <td>Fecha Entrega</td>
                            <td>Fecha Recoger</td>
                            <td>Fecha Graduacion</td>
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
