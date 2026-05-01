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
                        <button class="btn btn-sm btn-primary" onclick="generarResumenIngresos();">
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
                            Resumen de Ingresos <?= $_POST['tipoReporte']; ?><br/>
                            <small>
                                SALA: <?= $_POST['sala']; ?><br/>
                                AÑO: <?= $_POST['year']; ?> MES: <?= $_POST['monthName']; ?>
                            </small>
                        </h4>
                    </center>
                    <div class="table-responsive">
                        <?php
                        if ($_POST['tipoReporte'] == 'Maquinas') {
                            ?>
                            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr class="info text-uppercase">
                                        <td>Fecha</td>
                                        <td>Win Preliminar</td>
                                        <td>Tarjeta De Crédito Sin Comisión</td>
                                        <td>Tarjeta De Crédito Con Comisión</td>
                                        <td>Comisión Tarjeta De Crédito</td>
                                        <td>Cartones Bingo</td>
                                        <td>Huérfanos</td>
                                        <td>Sobrantes De Bóveda Y Caja</td>
                                        <td>Retención Del 10% Isr</td>
                                        <td>Retención Del 3% Timbre</td>
                                        <td>Cheque Pida/Debe Incluir</td>
                                        <td>Depósitos Totales</td>
                                        <td>Traslados de fondos entre bóvedas</td>
                                        <td>Pago a Proveedores</td>
                                        <td>Promociones Con Descuento</td>
                                        <td>Promociones Sin Descuento</td>
                                        <td>Billetes Falsos</td>
                                        <td>Faltantes</td>
                                        <td>Ganancia / (Perdida) por diferencial cambiario</td>
                                        <td>Movimiento De Bóveda</td>
                                        <td>Net Win</td>
                                        <td>Fundación Pediátrica</td>
                                        <td>Facturación Del Mes</td>
                                        <td>Comparación Bóveda</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $reporte = flujosBovedas::resumenIngresosMaquinas($_POST['year'], $_POST['month'], $_POST['idSalas'], $_SESSION['idEmpresa']);
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
                                    $total11 = 0;
                                    $total12 = 0;
                                    $total13 = 0;
                                    $total14 = 0;
                                    $total15 = 0;
                                    $total16 = 0;
                                    $total17 = 0;
                                    $total18 = 0;
                                    $total19 = 0;
                                    $total20 = 0;
                                    $total21 = 0;
                                    $total22 = 0;
                                    $total23 = 0;
                                    $deuda = 0;
                                    foreach ($reporte as $key => $value) {
                                        $total1 += $value['b'];
                                        $total2 += $value['c'];
                                        $total3 += $value['d'];
                                        $total4 += $value['e'];
                                        $total5 += $value['f'];
                                        $total6 += $value['g'];
                                        $total7 += $value['h'];
                                        $total8 += $value['i'];
                                        $total9 += $value['j'];
                                        $total10 += $value['k'];
                                        $total11 += $value['l'];
                                        $total12 += $value['m'];
                                        $total13 += $value['n'];
                                        $total14 += $value['o'];
                                        $total15 += $value['p'];
                                        $total16 += $value['q'];
                                        $totalMB = ($value['b'] + $value['c'] + $value['d'] + $value['e'] + $value['f'] + $value['g'] + $value['h'] + $value['i'] + $value['j'] + $value['k'] + $value['l'] + $value['m'] + $value['n'] + $value['o'] + $value['p'] + $value['q'] + $value['s'] + $value['t']);
                                        $netWin = ($value['b'] + $value['m'] + $value['n']);
                                        $fp = 0;
                                        if ($netWin < 1) {
                                            $fp = 0;
                                            $deuda += $value['licencia'];
                                        } else if ($netWin < ($value['licencia'] + $deuda)) {
                                            $fp = 0;
                                            $deuda += $value['licencia'];
                                        } else {
                                            $fp = $value['licencia'] + $deuda;
                                            $deuda = 0;
                                        }
                                        $tFacturacion = ($netWin - $fp);
                                        $comprobacion = ($totalMB - $value['r']);
                                        $total17 += $totalMB;
                                        $total18 += $netWin;
                                        $total19 += $fp;
                                        $total20 += $tFacturacion;
                                        $total21 += $comprobacion;
                                        $total22 += $value['s'];
                                        $total23 += $value['t'];
                                        ?>
                                        <tr>
                                            <td><?= $value['arqueo_at'] ?></td>
                                            <td align="right"><?= number_format($value['b'], 2) ?></td>
                                            <td align="right"><?= number_format($value['c'], 2) ?></td>
                                            <td align="right"><?= number_format($value['d'], 2) ?></td>
                                            <td align="right"><?= number_format($value['e'], 2) ?></td>
                                            <td align="right"><?= number_format($value['f'], 2) ?></td>
                                            <td align="right"><?= number_format($value['g'], 2) ?></td>
                                            <td align="right"><?= number_format($value['h'], 2) ?></td>
                                            <td align="right"><?= number_format($value['i'], 2) ?></td>
                                            <td align="right"><?= number_format($value['j'], 2) ?></td>
                                            <td align="right"><?= number_format($value['k'], 2) ?></td>
                                            <td align="right"><?= number_format($value['l'], 2) ?></td>
                                            <td align="right"><?= number_format($value['s'], 2) ?></td>
                                            <td align="right"><?= number_format($value['t'], 2) ?></td>
                                            <td align="right"><?= number_format($value['m'], 2) ?></td>
                                            <td align="right"><?= number_format($value['n'], 2) ?></td>
                                            <td align="right"><?= number_format($value['o'], 2) ?></td>
                                            <td align="right"><?= number_format($value['p'], 2) ?></td>
                                            <td align="right"><?= number_format($value['q'], 2) ?></td>
                                            <td align="right"><?= number_format($totalMB, 2) ?></td>
                                            <td align="right"><?= number_format($netWin, 2) ?></td>
                                            <td align="right"><?= number_format($fp, 2) ?></td>
                                            <td align="right"><?= number_format($tFacturacion, 2) ?></td>
                                            <td align="right"><?= number_format($comprobacion, 2) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
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
                                    <td align="right"><?= number_format($total11, 2); ?></td>
                                    <td align="right"><?= number_format($total22, 2); ?></td>
                                    <td align="right"><?= number_format($total23, 2); ?></td>
                                    <td align="right"><?= number_format($total12, 2); ?></td>
                                    <td align="right"><?= number_format($total13, 2); ?></td>
                                    <td align="right"><?= number_format($total14, 2); ?></td>
                                    <td align="right"><?= number_format($total15, 2); ?></td>
                                    <td align="right"><?= number_format($total16, 2); ?></td>
                                    <td align="right"><?= number_format($total17, 2); ?></td>
                                    <td align="right"><?= number_format($total18, 2); ?></td>
                                    <td align="right"><?= number_format($total19, 2); ?></td>
                                    <td align="right"><?= number_format($total20, 2); ?></td>
                                    <td align="right"><?= number_format($total21, 2); ?></td>
                                </tr>
                            </table>
                            <?php
                        } else {
                            ?>
                            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr class="info text-uppercase">
                                        <td>Fecha</td>
                                        <td>Win Preliminar</td>
                                        <td>Tarjeta De Crédito Sin Comisión</td>
                                        <td>Tarjeta De Crédito Con Comisión</td>
                                        <td>Comisión Tarjeta De Crédito</td>
                                        <td>Ingresos de Bar por pagar a TABLES</td>
                                        <td>Huérfanos</td>
                                        <td>Sobrantes De Bóveda Y Caja</td>
                                        <td>Ingreso / (Egreso) Por Fichas De Mesa</td>
                                        <td>Retención Del 10% Isr</td>
                                        <td>Retención Del 3% Timbre</td>
                                        <td>Depósitos Totales</td>
                                        <td>Promociones</td>
                                        <td>Billetes Falsos</td>
                                        <td>Faltante</td>
                                        <td>Ganancia / (Perdida) por diferencial cambiario</td>
                                        <td>Otros Gastos</td>
                                        <td>Traslados Entre Bovedas</td>
                                        <td>Pago a Proveedores</td>
                                        <td>Movimiento De Bóveda</td>
                                        <td>Net Win</td>
                                        <td>Fundación Pediátrica</td>
                                        <td>Facturación Del Mes</td>
                                        <td>Comparación Bóveda</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $reporte = flujosBovedas::resumenIngresosMesas($_POST['year'], $_POST['month'], $_POST['idSalas'], $_SESSION['idEmpresa']);
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
                                    $total11 = 0;
                                    $total12 = 0;
                                    $total13 = 0;
                                    $total14 = 0;
                                    $total15 = 0;
                                    $total16 = 0;
                                    $total17 = 0;
                                    $total18 = 0;
                                    $total19 = 0;
                                    $total20 = 0;
                                    $total21 = 0;
                                    $total22 = 0;
                                    $total23 = 0;
                                    $deuda = 0;
                                    foreach ($reporte as $key => $value) {
                                        $total1 += $value['b'];
                                        $total2 += $value['c'];
                                        $total3 += $value['d'];
                                        $total4 += $value['e'];
                                        $total5 += $value['f'];
                                        $total6 += $value['g'];
                                        $total7 += $value['h'];
                                        $total8 += ($value['i'] + $value['j']);
                                        $total9 += $value['k'];
                                        $total10 += $value['l'];
                                        $total11 += $value['m'];
                                        $total12 += $value['n'];
                                        $total13 += $value['o'];
                                        $total14 += $value['p'];
                                        $total15 += $value['r'];
                                        $total16 += $value['s'];
                                        $total17 += $value['t'];
                                        $totalMB = ($value['b'] + $value['c'] + $value['d'] + $value['e'] + $value['f'] + $value['g'] + $value['h'] + $value['i'] + $value['j'] + $value['k'] + $value['l'] + $value['m'] + $value['n'] + $value['o'] + $value['p'] + $value['r'] + $value['s'] + $value['t'] + $value['u']);
                                        $netWin = ($value['b'] + $value['n'] + $value['s']);
                                        $fp = 0;
                                        if ($netWin < 1) {
                                            $fp = 0;
                                            $deuda += $value['licencia'];
                                        } else if ($netWin < ($value['licencia'] + $deuda)) {
                                            $fp = 0;
                                            $deuda += $value['licencia'];
                                        } else {
                                            $fp = $value['licencia'] + $deuda;
                                            $deuda = 0;
                                        }
                                        $tFacturacion = ($netWin - $fp);
                                        $comprobacion = ($totalMB - $totalMB);
                                        $total18 += $totalMB;
                                        $total19 += $netWin;
                                        $total20 += $fp;
                                        $total21 += $tFacturacion;
                                        $total22 += $comprobacion;
                                        $total23 += $value['u'];
                                        ?>
                                        <tr>
                                            <td><?= $value['arqueo_at'] ?></td>
                                            <td align="right"><?= number_format($value['b'], 2) ?></td>
                                            <td align="right"><?= number_format($value['c'], 2) ?></td>
                                            <td align="right"><?= number_format($value['d'], 2) ?></td>
                                            <td align="right"><?= number_format($value['e'], 2) ?></td>
                                            <td align="right"><?= number_format($value['f'], 2) ?></td>
                                            <td align="right"><?= number_format($value['g'], 2) ?></td>
                                            <td align="right"><?= number_format($value['h'], 2) ?></td>
                                            <td align="right"><?= number_format(($value['i'] + $value['j']), 2) ?></td>
                                            <td align="right"><?= number_format($value['k'], 2) ?></td>
                                            <td align="right"><?= number_format($value['l'], 2) ?></td>
                                            <td align="right"><?= number_format($value['m'], 2) ?></td>
                                            <td align="right"><?= number_format($value['n'], 2) ?></td>
                                            <td align="right"><?= number_format($value['o'], 2) ?></td>
                                            <td align="right"><?= number_format($value['p'], 2) ?></td>
                                            <td align="right"><?= number_format($value['r'], 2) ?></td>
                                            <td align="right"><?= number_format($value['s'], 2) ?></td>
                                            <td align="right"><?= number_format($value['t'], 2) ?></td>
                                            <td align="right"><?= number_format($value['u'], 2) ?></td>
                                            <td align="right"><?= number_format($totalMB, 2) ?></td>
                                            <td align="right"><?= number_format($netWin, 2) ?></td>
                                            <td align="right"><?= number_format($fp, 2) ?></td>
                                            <td align="right"><?= number_format($tFacturacion, 2) ?></td>
                                            <td align="right"><?= number_format($comprobacion, 2) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
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
                                    <td align="right"><?= number_format($total11, 2); ?></td>
                                    <td align="right"><?= number_format($total12, 2); ?></td>
                                    <td align="right"><?= number_format($total13, 2); ?></td>
                                    <td align="right"><?= number_format($total14, 2); ?></td>
                                    <td align="right"><?= number_format($total15, 2); ?></td>
                                    <td align="right"><?= number_format($total16, 2); ?></td>
                                    <td align="right"><?= number_format($total17, 2); ?></td>
                                    <td align="right"><?= number_format($total23, 2); ?></td>
                                    <td align="right"><?= number_format($total18, 2); ?></td>
                                    <td align="right"><?= number_format($total19, 2); ?></td>
                                    <td align="right"><?= number_format($total20, 2); ?></td>
                                    <td align="right"><?= number_format($total21, 2); ?></td>
                                    <td align="right"><?= number_format($total22, 2); ?></td>
                                </tr>
                            </table>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php
}
?>