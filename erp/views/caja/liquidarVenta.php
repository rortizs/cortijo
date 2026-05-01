<table id="table">
    <tr>
        <td colspan="2" class="txtTotal">total venta</td>
        <td class="total text-right">
            <span id="total1">0.00</span>
        </td>
        <!--<td class="total text-right">
            $. <span id="total2">0.00</span>
        </td>-->
    </tr>
    <tr>
        <td colspan="2" class="txtTotal">total pagado</td>
        <td class="total text-right">
            <span id="totalP1">0.00</span>
        </td>
        <!--<td class="total text-right">
            $. <span id="totalP2">0.00</span>
        </td>-->
    </tr>
    <tr>
        <td colspan="2" class="txtTotal">cambio</td>
        <td class="total text-right">
            <span id="dif">(0.00)</span>
        </td>
       <!-- <td class="total text-right">
            $. <span id="dif2">0.00</span>
        </td>-->
    </tr>
</table>
<table id="liquidarVenta" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <td>Forma de Pago</td>
            <td>Valor</td>
            <td>Emisor</td>
            <td>No. Autorización</td>
            <td></td>
        </tr>
    </thead>
    <tr>
        <td>Efectivo</td>
        <td><input type="text" class="form-control formaPago input-sm ingresoPago" title="efectivo" data-type="1" id="efectivo"/></td>
        <td>
            <select class="form-control input-sm emisores" disabled="">
            </select>
        </td>
        <td><input type="text" class="form-control input-sm auth" disabled=""/></td>
        <td></td>
    </tr>
    <tr>
        <td>Efectivo Dolares</td>
        <td><input type="text" class="form-control formaPago input-sm ingresoPago" title="dolares" data-type="2"/></td>
        <td>
            <select class="form-control input-sm emisores" disabled="">
            </select>
        </td>
        <td><input type="text" class="form-control input-sm auth" disabled=""/></td>
        <td></td>
    </tr>
    <tr>
        <td>Retención Exención</td>
        <td><input type="text" class="form-control formaPago input-sm ingresoPago" disabled="" title="retencion" data-type="3"/></td>
        <td>
            <select class="form-control input-sm emisores" disabled="">
            </select>
        </td>
        <td><input type="text" class="form-control input-sm auth retencion"/></td>
        <td></td>
    </tr>
    <tr>
        <td>Cheques</td>
        <td><input type="text" class="form-control formaPago input-sm ingresoPago" title="cheque" data-type="4"/></td>
        <td>
            <select class="form-control input-sm emisores" disabled="">
            </select>
        </td>
        <td><input type="text" class="form-control input-sm auth"/></td>
        <td></td>
    </tr>
    <tr>
        <td>Tarjeta Credito/Debito</td>
        <td><input type="text" class="form-control formaPago input-sm ingresoPago" title="tarjeta" data-id="0" id="tarjeta" data-type="5"/></td>
        <td>
            <select class="form-control input-sm emisores emisor0" title="emisor" id="nombreTarjeta">
                <option value="">[Seleccione...]</option>
                <option value="1">VISA</option>
                <option value="2">MASTER CARD</option>
                <option value="3">AMERICAN EXPRESS</option>
            </select>
        </td>
        <td><input type="text" class="form-control input-sm auth tarjeta0" id="noAutorizacion" title="auth"/></td>
        <td>
            <button class="btn btn-primary btn-sm" onclick="addItem();">
                <i class="fa fa-plus"></i>
            </button>
        </td>
    </tr>
</table>
<div class="clear">&nbsp;</div>

<?php 
session_start();
 if($_SESSION['dbProject']=='erp_laxTravelTopacio'){ ?>
<button class="btn btn-success btn-sm" id="btnImprimir" onclick="cerrarVentaAgenciaViajes();">
<?php } else { ?> 
<button class="btn btn-success btn-sm" id="btnImprimir" onclick="cerrarVenta();">
<?php } ?>
    <i class="fa fa-print"></i> Imprimir Factura
</button>
<button class='btn btn-danger btn-sm' onclick='cancelarModal();'>
    <i class='fa fa-times'></i> Cancelar
</button>