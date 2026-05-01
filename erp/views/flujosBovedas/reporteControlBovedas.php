<style>
    .headerReporte{
        font-size: 1.5em !important;
    }
</style>
<?php
session_start();
require_once("../../models/flujosBovedas.php");

function lastDayMonth($mes) {
    $month = $mes;
    $year = date('Y');
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('d', mktime(0, 0, 0, $month, $day, $year));
}

function addZero($number) {
    $numero = "";
    if (strlen((string) $number) == 1) {
        $numero = "0" . $number;
    } else {
        $numero = $number;
    }
    return $numero;
}

$getBovedas = flujosBovedas::getBovedas($_SESSION['idEmpresa']);
$getAnos = flujosBovedas::getAnos();
$getMeses = flujosBovedas::getMeses();

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
                        <button class="btn btn-sm btn-primary" onclick="generarReporteControlBovedas();">
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
    $saldoInicialMes = flujosBovedas::saldoInicialMes($_POST['month'], $_POST['idSalas'], $_POST['idTipoReporte']);
    ?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body">
                    <center>
                        <h4>
                            Reporte de Control de Bovedas <?= $_POST['tipoReporte']; ?><br/>
                            <small>
                                SALA: <?= $_POST['sala']; ?><br/>
                                AÑO: <?= $_POST['year']; ?> MES: <?= $_POST['monthName']; ?>
                            </small>
                        </h4>
                    </center>
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr class="headerReporte">
                                <td colspan="5">Saldo Inicial</td>
                                <td align="right"><?= number_format($saldoInicialMes ?: '0.00', 2); ?></td>
                            </tr>
                            <tr class="info text-uppercase">
                                <td align="center">Concepto</td>
                                <td align="center">Saldo Inicial</td>
                                <td align="center">Aumentos</td>
                                <td align="center">Disminuciones</td>
                                <td align="center">Saldo Final</td>
                                <td align="center">Diferencias</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $reporte = flujosBovedas::reporteControlBovedas($_POST['year'], $_POST['month'], $_POST['idSalas'], $_SESSION['idEmpresa'], $_POST['idTipoReporte']);
                            $total1 = 0;
                            $total2 = 0;
                            $total4 = 0;
                            foreach ($reporte as $key => $value) {
                                $total1 += floatval(str_replace(',', '', $value['aumentos']));
                                $total2 += floatval(str_replace(',', '', $value['disminuciones']));
                                $total4 += floatval(str_replace(',', '', $value['diferenciaCorte']));
                                ?>
                                <tr>
                                    <td><?= $value['dia']; ?></td>
                                    <td align="right"><?= number_format(str_replace(',', '', $value['saldoInicial']), 2); ?></td>
                                    <td align="right"><?= number_format(str_replace(',', '', $value['aumentos']), 2); ?></td>
                                    <td align="right"><?= number_format(str_replace(',', '', $value['disminuciones']), 2); ?></td>
                                    <td align="right"><?= number_format(str_replace(',', '', $value['saldosFinal']), 2); ?></td>
                                    <td align="right"><?= number_format(str_replace(',', '', $value['diferenciaCorte']), 2); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tr class="info headerReporte">
                            <td>Totales</td>
                            <td align="right"></td>
                            <td align="right"><?= number_format(($total1 + $saldoInicialMes),2); ?></td>
                            <td align="right"><?= number_format($total2, 2); ?></td>
                            <td align="right"><?= number_format((($total1 + $saldoInicialMes) + $total2), 2); ?></td>
                            <td align="right"><?= number_format($total4, 2); ?></td>
                        </tr>    
                    </table>
                </div>
            </section>
        </div>
    </div>
    <?php
}
?>