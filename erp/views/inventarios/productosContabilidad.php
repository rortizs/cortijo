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
<div class="col-lg-12">
    <table id="formatoDetalle" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr class="info">
                <td style="width: 38% !important;">Cuenta - Descripción</td>
                <td style="width: 10% !important;">Debe</td>
                <td style="width: 10% !important;">Haber</td>
                <td style="width: 20% !important;">C. Costo</td>
                <td style="width: 20% !important;">Modulo</td>
                <td style="width: 2% !important;">
                    <button class="btn btn-success btn-sm" onclick="addRowProductoContabilidad('formatoDetalle');">
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
            </tr>
        </thead>
        <tbody id="detalle">
        </tbody>
    </table>
</div>
