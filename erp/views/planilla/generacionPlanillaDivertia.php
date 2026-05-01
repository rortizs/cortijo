<?php
/*
 * PLANILLA - GENERACION DE PLA
 */
session_start();
require_once("../../models/planilla.php");
require_once("../../models/admin.php");
$planilla = new Planilla();
$admin = new Admin();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Parametros
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label>Año</label>
                        <select class="form-control input-sm" id="year">
                            <?php
                            $getYear = $admin->getAnos();
                            foreach ($getYear as $key => $value) {
                                if ($value['descripcion'] == date('Y')) {
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
                        <label>Mes</label>
                        <select class="form-control input-sm" id="month">
                            <?php
                            $getMonth = $admin->getMeses();
                            foreach ($getMonth as $key => $value) {
                                if ($value['number'] == date('m')) {
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
                        <label>Tipo de Planilla</label>
                        <select class="form-control input-sm" id="tipoPlanilla">
                            <option value="">[Seleccione...]</option>
                            <option value="1">Primera Quincena</option>
                            <option value="2">Primera Quincena (Anticipo)</option>
                            <option value="3">Segunda Quincena</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Observaciones</label>
                        <textarea class="form-control input-sm" id="observaciones"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-primary btn-sm" onclick="generarPlanillaDivertia();">
                            <i class="fa fa-table"></i> Generar Planilla
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="generarPlanillaPDF(3);">
                            <i class="fa fa-print"></i> Imprimir Planilla
                        </button>
                        <!--                        <button class="btn btn-warning btn-sm" onclick="impresionBoletaPago(2);">
                                                    <i class="fa fa-print"></i> Impresión Boletas de Pago
                                                </button>-->
                        <!--
                        <button class="btn btn-warning btn-sm" onclick="generarPlanillaPDF(2);">
                            <i class="fa fa-print"></i> Imprimir Planilla Legal
                        </button>
                        -->
                        <!--                        <button class="btn btn-warning btn-sm" onclick="impresionBoletaPago();">
                                                    <i class="fa fa-print"></i> Impresión Boleta Pago
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="impresionBoletaPago();">
                                                    <i class="fa fa-upload"></i> Generar archivo Cuentas Bancarias
                                                </button>-->
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
    if (isset($_REQUEST['year']) && isset($_REQUEST['month']) && $_REQUEST['tipoPlanilla'] != '') {
        $getHrmDepartamentos = $planilla->getHrmDepartamentos($_SESSION['idEmpresa']);
        ?>
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body">
                    <center>
                        <h4 style="text-transform: uppercase !important;">
                            Pre-generación Planilla de Sueldos<br/>
                            <small>
                                <?php
                                $tipoPlanilla = "";
                                $periodo = "";
                                $observaciones = "";
                                $_REQUEST['fechaInicio'] = "";
                                $_REQUEST['fechaFin'] = "";
                                $cols = "16";
                                switch ($_REQUEST['tipoPlanilla']) {
                                    case '1':
                                        $tipoPlanilla = $_REQUEST['tipoPlanillaTXT'];
                                        $periodo = "Del 01 al 15 " . $_REQUEST['monthTXT'] . " - " . $_REQUEST['yearTXT'];
                                        $observaciones = $_REQUEST['observaciones'];
                                        $_REQUEST['fechaInicio'] = date('01-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                        $_REQUEST['fechaFin'] = date('15-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                        $cols = "17";
                                        break;
                                    case '2':
                                        $tipoPlanilla = $_REQUEST['tipoPlanillaTXT'];
                                        $periodo = "Del 01 al 15 " . $_REQUEST['monthTXT'] . " - " . $_REQUEST['yearTXT'];
                                        $observaciones = $_REQUEST['observaciones'];
                                        $_REQUEST['fechaInicio'] = date('01-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                        $_REQUEST['fechaFin'] = date('15-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                        break;
                                    case '3':
                                        $tipoPlanilla = $_REQUEST['tipoPlanillaTXT'];
                                        $periodo = "Del 16 al 30 " . $_REQUEST['monthTXT'] . " - " . $_REQUEST['yearTXT'];
                                        $observaciones = $_REQUEST['observaciones'];
                                        $_REQUEST['fechaInicio'] = date('16-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                        $_REQUEST['fechaFin'] = date('30-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                        $cols = "25";
                                        break;
                                }
                                ?>
                                <?= $tipoPlanilla; ?><br/>
                                <?= $periodo; ?><br/>
                                <?= $observaciones; ?>
                            </small>
                        </h4>
                    </center>
                    <div class="clearfix">&nbsp;</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" cellspacing="0" style="width: 1140px !important;">
                            <thead>
                                <tr class="info text-center">
                                    <td>Codigo</td>
                                    <td>Nombre</td>
                                    <td>Centro de Costo</td>
                                    <td>Puesto</td>
                                    <td>Fecha<br/>Ingreso</td>
                                    <td>NIT</td>
                                    <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Salario<br/>Mensual</td>
                                    <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Bonificación<br/>Incentivo</td>
                                    <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Salario<br/>Total</td>
                                    <td style="width: 35px !important; min-width: 35px !important; max-width: 35px !important;">Dias<br/>Lab.</td>
                                    <td style="width: 35px !important; min-width: 35px !important; max-width: 35px !important;">Dias<br/>Desc.</td>
                                    <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Salario<br/>Ordinario</td>
                                    <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Bonificación<br/>Incentivo</td>
                                    <?php
                                    switch ($_REQUEST['tipoPlanilla']) {
                                        case '1':
                                            ?>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Seguro Social</td>
                                            <?php
                                            break;
                                        case '3':
                                            ?>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Salario<br/>ExtraOrdinario</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Vacaciones</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Otros<br/>Ingresos</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Seguro<br/>Social</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">ISR</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Anticipos</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Prestamos</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Otros<br/>Egresos</td>
                                            <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Total<br/>Descuentos</td>
                                            <?php
                                            break;
                                    }
                                    ?>
                                    <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Total<br/>Devengado</td>
                                    <td style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;">Liquido</td>
                                </tr>
                            </thead>
                            <?php
                            $totalF1 = 0;
                            $totalF2 = 0;
                            $totalF3 = 0;
                            $totalF5 = 0;
                            $totalF6 = 0;
                            $totalF7 = 0;
                            $totalF8 = 0;
                            $totalF9 = 0;
                            $totalF10 = 0;
                            $totalF11 = 0;
                            $totalF12 = 0;
                            $totalF13 = 0;
                            $totalF14 = 0;
                            $totalF15 = 0;
                            $totalF16 = 0;
                            foreach ($getHrmDepartamentos as $key => $value) {
                                ?>
                                <tbody>
                                    <?php
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
                                    $generacionPlanilla = $planilla->generacionPlanilla($_REQUEST, $value['descripcion'], $_SESSION['idEmpresa']);
                                    foreach ($generacionPlanilla as $key2 => $value2) {
                                        $totalDev = 0;
                                        $anticipos = 0;
                                        $salOrd = 0;
                                        $bonif = 0;
                                        $totalDescuentos = 0;
                                        $seguroSocial = 0;
                                        $isr = 0;
                                        $totalPagado = 0;
                                        $vacaciones = 0;
                                        $dias = 15;
                                        switch ($_REQUEST['tipoPlanilla']) {
                                            case '1':
                                                $totalDev = ($value2['ordinario'] + round($value2['bonif'], 2));
                                                $salOrd = $value2['ordinario'];
                                                $bonif = $value2['bonif'];
                                                $totalPagado = ($value2['diasTrabajados'] < 0 ? 0 : $value2['ordinario']);
                                                $seguroSocial = (($totalPagado) * $value2['seguroSocial']) / 100;
                                                $isrFormula = ($value2['salarioTotal'] * 12) + ($value2['salarioOrdinario'] * 2) - (($seguroSocial * 12) + $value2['deduccionSinComprobacion'] + ($value2['salarioOrdinario'] * 2));
                                                if ($isrFormula <= $value2['ingresoSujetosISR']) {
                                                    $isr = ((((($isrFormula * $value2['isr']) / 100) / 12) / (30 / $_REQUEST['dias'])) * 2);
                                                }
                                                if ($isr < 1) {
                                                    $isr = 0;
                                                }
                                                $totalDescuentos = ($seguroSocial);
                                                break;
                                            case '2':
                                                $totalDev = ($value2['ordinario'] + round($value2['bonif'], 2));
                                                $salOrd = $value2['ordinario'];
                                                $bonif = $value2['bonif'];
                                                break;
                                            case '3':
                                                $totalDev = (($value2['ordinario'] * 2) + round($value2['bonifDecreto'], 2));
                                                $anticipos = ($value2['ordinario'] + round($value2['bonif'], 2));
                                                if ($value2['periodoVacaciones'] == 0) {
                                                    $salOrd = ($value2['ordinario'] * 2);
                                                } else {
                                                    $vacaciones = ($value2['ordinario'] * 2);
                                                }
                                                $bonif = $value2['bonifDecreto'];
                                                $totalPagado = ($value2['diasTrabajados'] < 0 ? 0 : $value2['salario']);
                                                $seguroSocial = (($totalPagado) * $value2['seguroSocial']) / 100;
                                                $isrFormula = ($value2['salarioTotal'] * 12) + ($value2['salarioOrdinario'] * 2) - (($seguroSocial * 12) + $value2['deduccionSinComprobacion'] + ($value2['salarioOrdinario'] * 2));
                                                if ($isrFormula <= $value2['ingresoSujetosISR']) {
                                                    if ($value2['diasTrabajados'] > 0) {
                                                        $isr = ((((($isrFormula * $value2['isr']) / 100) / 12) / (30 / $_REQUEST['dias'])) * 2);
                                                    }
                                                }
                                                if ($isr < 1) {
                                                    $isr = 0;
                                                }
                                                $totalDescuentos = (round($seguroSocial, 2) + round($isr, 2) + round($anticipos, 2) + round($value2['prestamos'], 2) + round($value2['otrosDescuentos'], 2));
                                                $dias = 30;
                                                break;
                                        }
                                        $salarioExtOrd = ($value2['horasExtSimples'] + $value2['horasExtDobles'] + $value2['horasExtMixtas']);
                                        $liquidoRecibir = ($totalDev - $totalDescuentos);
                                        $total1 += round($value2['salario'], 2);
                                        $total2 += round($value2['bonifDecreto'], 2);
                                        $total3 += round($value2['salarioTotal'], 2);
                                        $total4 += round(($salOrd < 0 ? 0 : $salOrd), 2);
                                        $total5 += round(($salOrd < 0 ? 0 : $bonif), 2);
                                        $total6 += round($salarioExtOrd, 2);
                                        $total7 += round($vacaciones, 2);
                                        $total8 += round($value2['otrosPagos'], 2);
                                        $total9 += round($seguroSocial, 2);
                                        $total10 += round($isr, 2);
                                        $total11 += round($value2['diasTrabajados'] < 0 ? 0 : $anticipos, 2);
                                        $total12 += round($value2['prestamos'], 2);
                                        $total13 += round($value2['otrosDescuentos'], 2);
                                        $total14 += round(($value2['diasTrabajados'] < 0 ? 0 : $totalDescuentos), 2);
                                        $total15 += round(($value2['diasTrabajados'] < 0 ? 0 : $totalDev), 2);
                                        $total16 += round(($value2['diasTrabajados'] < 0 ? 0 : $liquidoRecibir), 2);
                                        ?>
                                        <tr>
                                            <td><?= $value2['codigoEmpleado']; ?></td>
                                            <td><?= $value2['nombreCompleto']; ?></td>
                                            <td><?= $value2['centroCosto']; ?></td>
                                            <td><?= $value2['hrmPuestos']; ?></td>
                                            <td align="center"><?= $value2['fechaIngreso']; ?></td>
                                            <td align="center"><?= $value2['noTributario']; ?></td>
                                            <td align="right"><?= number_format($value2['salario'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['bonifDecreto'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['salarioTotal'], 2); ?></td>
                                            <td align="center"><?= $dias; ?></td>
                                            <td align="center"><?= $value2['diasDesc']; ?></td>
                                            <td align="right"><?= number_format(($salOrd < 0 ? 0 : $salOrd), 2); ?></td>
                                            <td align="right"><?= number_format(($salOrd < 0 ? 0 : $bonif), 2); ?></td>
                                            <?php
                                            switch ($_REQUEST['tipoPlanilla']) {
                                                case '1':
                                                    ?>
                                                    <td align="right"><?= number_format($seguroSocial, 2); ?></td>
                                                    <?php
                                                    break;
                                                case '3':
                                                    ?>
                                                    <td align="right"><?= number_format($salarioExtOrd, 2); ?></td>
                                                    <td align="right"><?= number_format($vacaciones, 2); ?></td>
                                                    <td align="right"><?= number_format($value2['otrosPagos'], 2); ?></td>
                                                    <td align="right"><?= number_format($seguroSocial, 2); ?></td>
                                                    <td align="right"><?= number_format($isr, 2); ?></td>
                                                    <td align="right"><?= number_format(($anticipos < 0 ? 0 : $anticipos), 2); ?></td>
                                                    <td align="right"><?= number_format($value2['prestamos'], 2); ?></td>
                                                    <td align="right"><?= number_format(($value2['otrosDescuentos'] < 0 ? 0 : $value2['otrosDescuentos']), 2); ?></td>
                                                    <td align="right"><?= number_format(($totalDescuentos < 0 ? 0 : $totalDescuentos), 2); ?></td>
                                                    <?php
                                                    break;
                                            }
                                            ?>
                                            <td align="right"><?= number_format(($value2['diasTrabajados'] < 0 ? 0 : $totalDev), 2); ?></td>
                                            <td align="right"><?= number_format(($value2['diasTrabajados'] < 0 ? 0 : $liquidoRecibir), 2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
        <!--                                    <tr class="info">
                        <td colspan="7" align="center" style="width: 680px !important; min-width: 680px !important; max-width: 680px !important; font-weight: bold;">Total Departamento</td>
                        <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total1, 2); ?></td>
                        <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total2, 2); ?></td>
                        <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total3, 2); ?></td>
                        <td align="right" style="width: 35px !important; min-width: 35px !important; max-width: 35px !important; font-weight: bold;"></td>
                        <td align="right" style="width: 35px !important; min-width: 35px !important; max-width: 35px !important; font-weight: bold;"></td>
                        <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total4, 2); ?></td>
                        <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total5, 2); ?></td>
                                    <?php
                                    switch ($_REQUEST['tipoPlanilla']) {
                                        case '1':
                                            ?>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total9, 2); ?></td>
                                            <?php
                                            break;
                                        case '3':
                                            ?>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total6, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total7, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total8, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total9, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total10, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total11, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total12, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total13, 2); ?></td>
                                                                <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total14, 2); ?></td>
                                            <?php
                                            break;
                                    }
                                    ?>
                        <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total15, 2); ?></td>
                        <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important; font-weight: bold;"><?= number_format($total16, 2); ?></td>
                    </tr>-->

                                    <?php
                                    $totalF1 += $total1;
                                    $totalF2 += $total2;
                                    $totalF3 += $total3;
                                    $totalF4 += $total4;
                                    $totalF5 += $total5;
                                    $totalF6 += $total6;
                                    $totalF7 += $total7;
                                    $totalF8 += $total8;
                                    $totalF9 += $total9;
                                    $totalF10 += $total10;
                                    $totalF11 += $total11;
                                    $totalF12 += $total12;
                                    $totalF13 += $total13;
                                    $totalF14 += $total14;
                                    $totalF15 += $total15;
                                    $totalF16 += $total16;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="info text-center">
                                    <td colspan="6" align="center" style="width: 680px !important; min-width: 680px !important; max-width: 680px !important;">Total Planilla</td>
                                    <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF1, 2); ?></td>
                                    <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF2, 2); ?></td>
                                    <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF3, 2); ?></td>
                                    <td align="right" style="width: 35px !important; min-width: 35px !important; max-width: 35px !important;"></td>
                                    <td align="right" style="width: 35px !important; min-width: 35px !important; max-width: 35px !important;"></td>
                                    <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF4, 2); ?></td>
                                    <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF5, 2); ?></td>
                                    <?php
                                    switch ($_REQUEST['tipoPlanilla']) {
                                        case '1':
                                            ?>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF9, 2); ?></td>
                                            <?php
                                            break;
                                        case '3':
                                            ?>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF6, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF7, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF8, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF9, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF10, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF11, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF12, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF13, 2); ?></td>
                                            <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF14, 2); ?></td>
                                            <?php
                                            break;
                                    }
                                    ?>
                                    <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF15, 2); ?></td>
                                    <td align="right" style="width: 100px !important; min-width: 100px !important; max-width: 100px !important;"><?= number_format($totalF16, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }
    ?>
</div>