<?php
/** CONTABILIDAD - PARTIDAS AUTOMATICAS
 * 
 */
require_once("../../models/contabilidad.php");
$conta = new Contabilidad();
$getFormato = "";
if (isset($_REQUEST['idFormato'])) {
    $getFormato = $conta->getFormato($_REQUEST['idFormato']);
}
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Descripción de Partida
                <span class="tools pull-right" onclick="loadData('vw_formatos', 'Contabilidad', 'Partidas Automáticas', 0, 0, 1);" style="cursor: pointer !important;">
                    <a href="javascript:;" class="fa fa-undo"></a> Regresar
                </span>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td>Descripción</td>
                                    <td style="width: 4% !important;">
                                        <button class="btn btn-success btn-sm" id="save" onclick="saveFormato();">
                                            <i class="fa fa-floppy-o"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" id="update" onclick="updateFormato();">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="info">
                                    <input type="hidden" class="form-control input-sm" id="idFormato" value="<?= $_REQUEST['idFormato']; ?>"/>
                                    <td colspan="2"><input type="text" class="form-control input-sm" id="descripcion" style="text-transform: uppercase;" value="<?= $getFormato['descripcion']; ?>"/></td>
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
                        <table id="formatoDetalle" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info">
                                    <td style="width: 46% !important;">Cuenta - Descripción</td>
                                    <td style="width: 12% !important;">Debe</td>
                                    <td style="width: 12% !important;">Haber</td>
                                    <td style="width: 23% !important;">C. Costo</td>
                                    <td style="width: 2% !important;">
                                        <button class="btn btn-success btn-sm" onclick="addRowFormato('formatoDetalle');">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="detalle">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
