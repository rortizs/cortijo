<?php
session_start();
require_once("../../models/flujosBovedas.php");
$getBovedas = flujosBovedas::getBovedas($_SESSION['idEmpresa']);
$getAnos = flujosBovedas::getAnos();
$getMeses = flujosBovedas::getMeses();
$tipoReporte = array(
    array('id' => 1, 'descripcion' => 'Maquinas'),
    array('id' => 2, 'descripcion' => 'Mesas')
);
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros Reporte
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label><i class="fa fa-filter"></i> Tipo Reporte</label>
                        <select class="form-control input-sm" id="idTipoReporte">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($tipoReporte as $key => $value) {
                                if ($value['id'] == $_POST['idTipoReporte']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label><i class="fa fa-database"></i> Sala</label>
                        <select class="form-control input-sm" id="idSalas">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getBovedas as $key => $value) {
                                if ($value['id'] == $_POST['idSalas']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label><i class="fa fa-calendar"></i> Año</label>
                        <select class="form-control input-sm" id="year">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getAnos as $key => $value) {
                                if ($value['descripcion'] == $_POST['year']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label><i class="fa fa-calendar"></i> Mes</label>
                        <select class="form-control input-sm" id="month">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getMeses as $key => $value) {
                                if ($value['id'] == $_POST['month']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        &nbsp;<br/>
                        <button class="btn btn-sm btn-primary" onclick="generarReporteVisanet();">
                            Generar Reporte
                        </button>
                        <!--<button class="btn btn-sm btn-success">Exportar a Excel</button>-->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
if (isset($_POST['idSalas']) && isset($_POST['year']) && isset($_POST['month'])) {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body">
                    <center>
                        <h4>
                            Reporte Control de Tarjetas de Credito Y/O Debito <?= $_POST['tipoReporte']; ?><br/>
                            <small>
                                SALA: <?= $_POST['sala']; ?><br/>
                                AÑO: <?= $_POST['year']; ?> MES: <?= $_POST['monthName']; ?>
                            </small>
                        </h4>
                    </center>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info text-uppercase">
                                    <td>FECHA</td>
                                    <td>VISANET SIN COMISION VIP</td>
                                    <td>TARJETAS DE DEBITO Y/O CREDITO</td>
                                    <td>TARJETAS DE DEBITO Y/O CREDITO</td>
                                    <td>Monto</td>
                                    <td>Iva Monto</td>
                                    <td>Monto sin Iva</td>
                                    <td>Comision</td>
                                    <td>Iva Comision</td>
                                    <td>Ret. Iva</td>
                                    <td>Monto Depositado</td>
                                    <td>NO. DE DOCUMENTO</td>
                                </tr>
<!--                                <tr class="text-danger">
                                    <td colspan="12">VALORES CORRESPONDIENTES AL MES ANTERIOR</td>
                                </tr>-->
                            </thead>
<!--                            <tr class="text-danger">
                                <td>28-12-2016</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                            </tr>
                            <tr class="text-danger">
                                <td>29-12-2016</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                            </tr>
                            <tr class="text-danger">
                                <td>30-12-2016</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                                <td align="right">0.00</td>
                            </tr>-->
                            <thead>
                                <tr class="text-info">
                                    <td colspan="12">VALORES CORRESPONDIENTES AL MES ACTUAL</td>
                                </tr>
                            </thead>
                            <?php
                            $reporte = flujosBovedas::reporteVisanet($_POST['year'], $_POST['month'], $_POST['idSalas'], $_SESSION['idEmpresa']);
                            $total1 = 0;
                            $total2 = 0;
                            $total3 = 0;
                            $total4 = 0;
                            $total5 = 0;
                            $total6 = 0;
                            $total7 = 0;
                            $total8 = 0;
                            $total9 = 0;
                            $total10 = 0;
                            foreach ($reporte as $key => $value) {
                                $total1 += $value['v1'];
                                $total2 += $value['v2'];
                                $total3 += $value['monto'];
                                $total4 += $value['monto'];
                                $total5 += $value['montoIva'];
                                $total6 += $value['montoSinIva'];
                                $total7 += $value['comision'];
                                $total8 += $value['comsionIva'];
                                $total9 += $value['retIva'];
                                $total10 += $value['montoDepositado'];
                                ?>
                                <tr>
                                    <td><?= $value['arqueo_at'] ?></td>
                                    <td align="right"><?= number_format($value['v1'], 2); ?></td>
                                    <td align="right"><?= number_format($value['v2'], 2); ?></td>
                                    <td align="right"><?= number_format($value['monto'], 2); ?></td>
                                    <td align="right"><?= number_format($value['monto'], 2); ?></td>
                                    <td align="right"><?= number_format($value['montoIva'], 2); ?></td>
                                    <td align="right"><?= number_format($value['montoSinIva'], 2); ?></td>
                                    <td align="right"><?= number_format($value['comision'], 2); ?></td>
                                    <td align="right"><?= number_format($value['comsionIva'], 2); ?></td>
                                    <td align="right"><?= number_format($value['retIva'], 2); ?></td>
                                    <td align="right"><?= number_format($value['montoDepositado'], 2); ?></td>
                                    <td align="right">--</td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr class="info headerReporte">
                                <td>Totales</td>
                                <td align="right"><?= number_format($total1, 2); ?></td>
                                <td align="right"><?= number_format($total2, 2); ?></td>
                                <td align="right"><?= number_format($total3, 2); ?></td>
                                <td align="right"><?= number_format($total4, 2); ?></td>
                                <td align="right"><?= number_format($total5, 2); ?></td>
                                <td align="right"><?= number_format($total6, 2); ?></td>
                                <td align="right"><?= number_format($total7, 2); ?></td>
                                <td align="right"><?= number_format($total8, 2); ?></td>
                                <td align="right"><?= number_format($total9, 2); ?></td>
                                <td align="right"><?= number_format($total10, 2); ?></td>
                                <td align="right"></td>
                            </tr>   
                        </table>
                    </div>
            </section>
        </div>
    </div>
    <?php
}
?>