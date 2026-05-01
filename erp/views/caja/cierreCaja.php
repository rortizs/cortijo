<?php
session_start();
?>
<table width='100%' class='table table-bordered table-striped'>
    <tr>
        <td>
            <label id="txtFechaCierre"></label>
            <input type='hidden' id='fechaCierre'/>
        </td>
        <td colspan='2' class='text-center'>Partida Cierre</td>
    </tr>
    <tr>
        <td>Forma de Pago Efectivo</td>
        <td style='width: 20% ! important;' class="text-right">
            <label id="txtVentaEnEfectivo"></label>
            <input type='hidden' id='ventaEnEfectivo'/>
        </td>
        <td style='width: 20% ! important;'></td>
    </tr>
    <tr>
        <td>Forma de Pago Tarjetas</td>
        <td class="text-right">
            <label id="txtVentaEnTarjeta"></label>
            <input type='hidden' id='ventaEnTarjeta'/>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>(-) Total Cambios</td>
        <td style='width: 20% ! important;' class="text-right">
            <label id="txtTotalCambio"></label>
            <input type='hidden' id='totalCambio'/>
        </td>
        <td style='width: 20% ! important;'></td>
    </tr>
    <tr>
        <td>Fondo</td>
        <td></td>
        <td class="text-right">
            <label id="txtFondoCajaC"></label>
            <input type='hidden' id='fondoCajaC'/>
        </td>
    </tr>
    <tr>
        <td>- Vales</td>
        <td></td>
        <td class="text-right">
            <label id="txtTotalValesC"></label>
            <input type='hidden' id='totalValesC'/>
        </td>
    </tr>
    <tr>
        <td>Totales </td>
        <td class="text-right">
            <label id="txtTotalCierre"></label>
            <input type='hidden' id='totalCierre'/>
        </td>
        <td class="text-right">
            <label id="txtTotalCorte"></label>
            <input type='hidden' id='totalCorte'/>
        </td>
    </tr>
    <tr>
        <td>Diferencia</td>
        <td colspan='2' class='text-center'>
            <label id="txtDiferencia"></label>
            <input type='hidden' id='diferencia'/>
        </td>
    </tr>
    <tr>
        <td colspan='3'>Observaciones:<br/>
            <textarea rows="5" class='form-control input-sm' id='observaciones'></textarea><br/>
            <button class='btn btn-success btn-sm' onclick='procesarCierre();'><i class='fa fa-check'></i> Procesar Cierre</button>
            <button class='btn btn-danger btn-sm' data-dismiss='modal'><i class='fa fa-times'></i> Cancelar</button>
        </td>
    </tr>
</table>