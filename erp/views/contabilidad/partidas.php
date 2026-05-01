<?php
/*
 * Contabilidad - Partidas Manuales
 */
require_once("../../models/contabilidad.php");
$conta = new Contabilidad();
$getPartida = "";
if (isset($_REQUEST['idPartida'])) {
    $getPartida = $conta->getPartida($_REQUEST['idPartida']);
}
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Descripción de Partida
                <span class="tools pull-right" onclick="loadData('vw_partidas', 'Contabilidad', 'Partidas Manuales', 0, 0, 1);" style="cursor: pointer !important;">
                    <a href="javascript:;" class="fa fa-undo"></a> Regresar
                </span>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td>Número</td>
                                    <td>Fecha</td>
                                    <td>Descripción</td>
                                    <td>Tipo</td>
                                    <td style="width: 2% !important;">
                                        <button class="btn btn-success btn-sm" id="save" onclick="savePartida();" title="Guardar Partida">
                                            <i class="fa fa-floppy-o"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" id="update" onclick="updatePartida();" title="Actualizar Partida">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="info">
                                    <td style="width: 10% !important;">
                                        <input type="hidden" class="form-control input-sm" id="idPartida" value="<?= $_REQUEST['idPartida']; ?>"/>
                                        <input type="hidden" class="form-control input-sm" id="idDocumento"/>
                                        <input type="text" class="form-control input-sm" id="numero" readonly="" value="<?= $getPartida['numero']; ?>"/>
                                    </td>
                                    <td style="width: 10% !important;">
                                        <input type="text" class="form-control input-sm" id="partida_at" value="<?= $getPartida['partida_at'] ?: date('d-m-Y'); ?>"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm" id="descripcion" value="<?= $getPartida['descripcion']; ?>"/>
                                    </td>
                                    <td style="width: 15% !important;" colspan="2">
                                        <input type="hidden" id="idTipoOperacionPartidaEdit" class="form-control input-sm" value="<?= $getPartida['idTipoOperacionPartida']; ?>"/>
                                        <select class="form-control input-sm" id="idTipoOperacionPartida">
                                        </select>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Detalle de Partida
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table id="partidaDetalle" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td style="width: 46% !important;">Cuenta - Descripción</td>
                                    <td style="width: 12% !important;">Debe</td>
                                    <td style="width: 12% !important;">Haber</td>
                                    <td style="width: 23% !important;">C. Costo</td>
                                    <td style="width: 2% !important;">
                                        <button class="btn btn-success btn-sm" onclick="addRowPartida('partidaDetalle');">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="detalle">
                            </tbody>
                            <thead>
                                <tr>
                                    <td align="center">Totales</td>
                                    <td><input type="text" class="form-control input-sm" id="totalDebe" readonly=""/></td>
                                    <td><input type="text" class="form-control input-sm" id="totalHaber" readonly=""/></td>
                                    <td colspan="2"></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
