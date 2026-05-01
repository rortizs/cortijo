<?php
session_start();
?>
<input type="hidden" id="idProductoSerie" class="form-control input-sm"/>
<input type="hidden" id="cantidadSeries" class="form-control input-sm"/>
<input type="hidden" id="action" class="form-control input-sm"/>
<table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
    <thead>
        <tr class="info">
            <td colspan="2">
                Serie
                <input type="text" class="form-control input-sm" id="serie" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                            ingresoSeriesProducto();"/>
            </td>
        </tr>
    </thead>
    <tbody id="detalleSeries">
    </tbody>
    <tfoot>
        <tr style="text-align: right !important;" onclick="cancelarModal();">
            <td colspan="2">
                <button class="btn btn-sm btn-primary">Continuar</button>
            </td>
        </tr>
    </tfoot>
</table>

