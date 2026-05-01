<?php
session_start();
require_once("../../models/general.php");
$general = new General();
?>
<div class="row" id="corteCaja">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-12" style="font-weight: bold; font-size: 2em !important;">
                <?= $_SESSION['empresa']; ?>
            </div>
            <div class="col-lg-4">
                <label style="font-weight: bold; text-transform: uppercase;">Fecha:</label>
                <input type="text" class="form-control input-sm" id="fechaCorte"/>
            </div>
            <!--
            <div class="col-lg-3">
                <label>Documento del # al #</label>
            </div>
            -->
            <div class="col-lg-4">
                <label style="font-weight: bold; text-transform: uppercase;">Cajero:</label> 
                <input class="form-control input-sm" type="text" value="<?= $_SESSION['userName']; ?>" readonly=""/>
            </div>
            <div class="col-lg-4">
                <label style="font-weight: bold; text-transform: uppercase;">Sucursal:</label>
                <input class="form-control input-sm" type="text" value="<?= $_SESSION['nombreSucursal']; ?>" readonly=""/>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="col-lg-12">
                <label class="text-uppercase"><h4>Efectivo en Caja</h4></label>
            </div>
            <div class="col-lg-4">
                <label>Cantidad</label>
                <div class="row">
                    <div class="col-lg-12">
                        <input type='text' class='form-control input-sm cantidad' value='N/A' readonly='' title='Monedas'/>
                        <input type='text' class='form-control input-sm cantidad corteCaja monedas' value='' title='1' tabindex='1'/>
                        <input type='text' class='form-control input-sm cantidad corteCaja monedas' value='' title='5' tabindex='2'/>
                        <input type='text' class='form-control input-sm cantidad corteCaja monedas' value='' title='10' tabindex='3'/>
                        <input type='text' class='form-control input-sm cantidad corteCaja monedas' value='' title='20' tabindex='4'/>
                        <input type='text' class='form-control input-sm cantidad corteCaja monedas' value='' title='50' tabindex='5'/>
                        <input type='text' class='form-control input-sm cantidad corteCaja monedas' value='' title='100' tabindex='6'/>
                        <input type='text' class='form-control input-sm cantidad corteCaja monedas' value='' title='200' tabindex='7'/>
                        <input type='text' class='form-control input-sm cantidad' value='N/A' readonly='' title='Dolares'/>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <label>Denominacion</label>
                <div class="row">
                    <div class="col-lg-12">
                        <input type='text' class='form-control input-sm' value='Monedas' readonly=''/>
                        <input type='text' class='form-control input-sm' value='Q. 1.00' readonly=''/>
                        <input type='text' class='form-control input-sm' value='Q. 5.00' readonly=''/>
                        <input type='text' class='form-control input-sm' value='Q. 10.00' readonly=''/>
                        <input type='text' class='form-control input-sm' value='Q. 20.00' readonly=''/>
                        <input type='text' class='form-control input-sm' value='Q. 50.00' readonly=''/>
                        <input type='text' class='form-control input-sm' value='Q. 100.00' readonly=''/>
                        <input type='text' class='form-control input-sm' value='Q. 200.00' readonly=''/>
                        <input type='text' class='form-control input-sm' value='$. Dolares' readonly=''/>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <label>Total</label>
                <div class="row">
                    <div class="col-lg-12">
                        <input type='text' class='form-control input-sm corteCaja totalesM' value='0.00' tabindex='8' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm totalesM' value='0.00' id='result1' readonly='' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm totalesM' value='0.00' id='result5' readonly='' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm totalesM' value='0.00' id='result10' readonly='' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm totalesM' value='0.00' id='result20' readonly='' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm totalesM' value='0.00' id='result50' readonly='' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm totalesM' value='0.00' id='result100' readonly='' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm totalesM' value='0.00' id='result200' readonly='' data-title="moneda-local"/>
                        <input type='text' class='form-control input-sm corteCaja totalesM' value='0.00' tabindex='9' data-title="dolares" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="col-lg-12">
                <label class="text-uppercase"><h4>Tarjeta Credito / Debito</h4></label>
            </div>
            <div class="col-lg-4">
                <label>Emisor</label>
                <div class="row">
                    <div class="col-lg-12">
                        <input type='text' class='form-control input-sm' value='VISA' readonly=''/>
                        <input type='text' class='form-control input-sm' value='MASTERCARD' readonly=''/>
                        <input type='text' class='form-control input-sm' value='AMERICAN EXPRESS' readonly=''/>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <label>Totales Sistema</label>
                <div class="row">
                    <div class="col-lg-12">
                        <input type='text' class='form-control input-sm' value='0.00' id="totalVisa" readonly=""/>
                        <input type='text' class='form-control input-sm' value='0.00' id="totalMC" readonly=""/>
                        <input type='text' class='form-control input-sm' value='0.00' id="totalAM" readonly=""/>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <label>Totales POS</label>
                <div class="row">
                    <div class="col-lg-12">
                        <input type='text' class='form-control input-sm totalesTJ corteCaja' value='0.00' tabindex='10' title='VISA'/>
                        <input type='text' class='form-control input-sm totalesTJ corteCaja' value='0.00' tabindex='11' title='MASTERCARD'/>
                        <input type='text' class='form-control input-sm totalesTJ corteCaja' value='0.00' tabindex='12' title='AMERICAN EXPRESS'/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="col-lg-12">
                <label class="text-uppercase"><h4>Resumen</h4></label>
            </div>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <label>Tasa de Cambio</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' id="tasaCambio2" style="color: blue !important;font-weight: bold;"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Fondo de Caja</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='fondoCorte' style="color: red !important;font-weight: bold;"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Vales</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='valesCorte'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Efectivo en Caja</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='totalEfectivo'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Efectivo en Caja Dolares</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='totalEfectivoDolares'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Total Exenciones</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id="totalExencion"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Total Cheques</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id="totalCheques"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Vouchers</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='totalesTJ'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Recibos</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='totalRecibos'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Total Corte</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='totalCorte'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Total Ventas (Contado)</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='totalVentasContado'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Total Ventas (Credito)</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='totalVentasCredito'/>
                    </div>
                    <div class="col-lg-6">
                        <label>Diferencias</label>
                    </div>
                    <div class="col-lg-6">
                        <input type='text' class='form-control input-sm' value='0.00' readonly='' id='diferencia'/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        &nbsp;<br/>
        <button class='btn btn-success btn-sm' onclick='generarCorte();'>
            <i class='fa fa-check'></i> Procesar Corte
        </button>
        <button class='btn btn-danger btn-sm' onclick='cancelarModal();'>
            <i class='fa fa-times'></i> Cancelar
        </button>
    </div>
</div>