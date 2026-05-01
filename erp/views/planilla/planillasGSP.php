<?php
/*
 * PLANILLA - GENERACION DE PLA
 */
session_start();
require_once("../../models/planilla.php");
$planilla = new Planilla();
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
                        <label>Fecha Inicio<br/>Planilla:</label>
                        <div class='input-group date' id="fechaInicio">
                            <input type='text' class="form-control input-sm"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha Fin<br/>Planilla:</label>
                        <div class='input-group date' id="fechaFin">
                            <input type='text' class="form-control input-sm"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>Fuente de Generación<br/>Horas Extras</label>
                        <select id="fuenteHorasExtras" class="form-control input-sm">
                            <option>[Seleccione...]</option>
                            <option value="1">Marcajes</option>
                            <option value="2">Horas manuales</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha Inicio<br/>Horas Extras:</label>
                        <div class='input-group date' id="fechaInicioHE">
                            <input type='text' class="form-control input-sm"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha Fin<br/>Horas Extras:</label>
                        <div class='input-group date' id="fechaFinHE">
                            <input type='text' class="form-control input-sm"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-primary btn-sm" onclick="generarPlanillaGSP();">
                            <i class="fa fa-table"></i> Generar Planilla
                        </button>
                        <button class="btn btn-warning btn-sm hidden" onclick="generarPlanillaPDF(1);" id="btnImprimir1">
                            <i class="fa fa-print"></i> Imprimir Planilla Admin.
                        </button>
                        <button class="btn btn-warning btn-sm hidden" onclick="generarPlanillaPDF(2);" id="btnImprimir2">
                            <i class="fa fa-print"></i> Imprimir Planilla Legal
                        </button>
                        <button class="btn btn-warning btn-sm hidden" onclick="impresionBoletaPago();" id="btnBoletaPago">
                            <i class="fa fa-print"></i> Impresión Boleta Pago
                        </button>
                        <!--
                        <button class="btn btn-danger btn-sm hidden" onclick="cerrarPlanilla();" id="btnCerrarPlanilla">
                            <i class="fa fa-check"></i> Cerrar Planilla
                        </button>
                        -->
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
    if (isset($_REQUEST['fechaInicio']) && isset($_REQUEST['fechaFin'])) {
        $getHrmDepartamentos = $planilla->getHrmDepartamentos($_SESSION['idEmpresa']);
        ?>
        <div class="col-lg-12" id="detalle">
            <section class="panel">
                <div class="panel-body">
                    <center>
                        <h4>
                            Pre-generación Planilla de Sueldos<br/>
                            <small>
                                Rango de Fecha <?= $_REQUEST['fechaInicio']; ?> al <?= $_REQUEST['fechaFin']; ?><br/>
                                Rango de Fecha Horas Extras <?= $_REQUEST['fechaInicioHE']; ?> al <?= $_REQUEST['fechaFinHE']; ?><br/>
                                Dias Trabajados: <?= $_REQUEST['dias']; ?><br/>
                                Fuente horas extras: <?= $_REQUEST['fuenteHorasExtras']; ?>
                            </small>
                        </h4>
                    </center>
                    <div class="clearfix">&nbsp;</div>
                    <div class="table-responsive">
                        <?php
                        $totalF1 = 0;
                        $totalF2 = 0;
                        $totalF3 = 0;
                        $totalF4 = 0;
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
                        $totalF17 = 0;
                        $totalF18 = 0;
                        $totalF19 = 0;
                        $totalF20 = 0;
                        $totalF21 = 0;
                        $totalF22 = 0;
                        $totalF23 = 0;
                        foreach ($getHrmDepartamentos as $key => $value) {
                            ?>
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" style="width: 1843px !important;">
                                <thead>
                                    <tr>
                                        <td colspan="29"><?= $value['descripcion']; ?></td>
                                    </tr>
                                    <tr class="info text-center">
                                        <td>No.</td>
                                        <td>Cod.</td>
                                        <td>Empleado</td>
                                        <td>Fecha<br/>Ingreso</td>
                                        <td>Sueldo<br/>Diario</td>
                                        <td>Dias<br/>Lab</td>
                                        <td>Sueldo<br/>Ordinario</td>
                                        <td>Bono<br/>Decreto</td>
                                        <td>Hrs.<br/>Diurnas</td>
                                        <td>Hrs. Extr.<br/>Diurnas</td>
                                        <td>Hrs.<br/>Nocturnas</td>
                                        <td>Hrs. Extr.<br/>Nocturnas</td>
                                        <td>Hrs.<br/>Mixtas</td>
                                        <td>Hrs. Extr.<br/>Mixtas</td>
                                        <td>Comisiones</td>
                                        <td>Bono<br/>Fijo</td>
                                        <td>Bono<br/>Incentivo</td>
                                        <td>Bono<br/>Calidad</td>
                                        <td>Bono<br/>Product.</td>
                                        <td>Bono<br/>Flocking</td>
                                        <td>Otros<br/>Bonos</td>
                                        <td>Total<br/>Ingresos</td>
                                        <td>Seguro<br/>Social</td>
                                        <td>ISR</td>
                                        <td>Anticipos</td>
                                        <td>Prestamos UPA</td>
                                        <td>Otros<br/>Descuentos</td>
                                        <td>Total<br/>Deducciones</td>
                                        <td>Liquido<br/>Recibir</td>
                                    </tr>
                                </thead>
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
                                    $total17 = 0;
                                    $total18 = 0;
                                    $total19 = 0;
                                    $total20 = 0;
                                    $total21 = 0;
                                    $total22 = 0;
                                    $total23 = 0;
                                    $generacionPlanilla = $planilla->generacionPlanilla($_REQUEST, $value['descripcion'], $_SESSION['idEmpresa']);
                                    foreach ($generacionPlanilla as $key2 => $value2) {
                                        //CALCULO DE HORAS EXTRAS
                                        $totalDiurnas = 0;
                                        $totalNocturnas = 0;
                                        for ($i = date("Y-m-d", strtotime($_REQUEST['fechaInicioHE'])); $i <= date("Y-m-d", strtotime($_REQUEST['fechaFinHE'])); $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
                                            //$fecha = date($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-' . addZero($i));
                                            $marcaje = $planilla->horaMarcaje($value2['codigoEmpleado'], $i, $_SESSION['idEmpresa']);
                                            $hrsExtrasDiurnas = 0;
                                            $hrsExtrasNocturnas = 0;
                                            $horaEntrada = $marcaje['horaEntrada'] ?: '--';
                                            $horaSalida = $marcaje['horaSalida'] ?: '--';
                                            $style = "";
                                            $turno = "";
                                            if ($marcaje['turno'] != '') {
                                                if ($marcaje['horaEntrada'] != '' && $marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaSalida'] != '') {
                                                    if ($marcaje['dia'] == 'Friday') {
                                                        $hrsExtrasNocturnas = ($planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']) - 2);
                                                    } else {
                                                        $hrsExtrasNocturnas = ($planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']) - 1);
                                                    }
                                                } else if ($marcaje['horaEntrada'] != '' && $marcaje['horaSalidaTurno'] != '18:00:00' && $marcaje['horaSalida'] > $marcaje['horaSalidaTurno']) {
                                                    $hrsExtrasDiurnas = $planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']);
                                                }
                                                if ($marcaje['horaEntrada'] == $marcaje['horaSalida'] || $marcaje['horaEntrada'] == '' || $marcaje['horaSalida'] == '') {
                                                    $style = "style='color:blue;'";
                                                    $hrsExtrasDiurnas = 0;
                                                    $turno = "MARCAJE INCOMPLETO";
                                                } else if ($marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaSalida'] > '08:00:00') {
                                                    $style = "style='color:blue;'";
                                                    $hrsExtrasNocturnas = 0;
                                                    $turno = "MARCAJE INCOMPLETO";
                                                } else if ($marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaEntrada'] < '17:00:00') {
                                                    $style = "style='color:blue;'";
                                                    $hrsExtrasNocturnas = 0;
                                                    $turno = "MARCAJE INCOMPLETO";
                                                } else {
                                                    $style = "style='color:green;'";
                                                    $turno = $marcaje['turno'];
                                                }
                                            } else if ($marcaje['dia'] == 'Saturday' || $marcaje['dia'] == 'Sunday') {
                                                $style = "style='color:black;'";
                                                $turno = "";
                                                $horaEntrada = "";
                                                $horaSalida = "";
                                            } else if ($marcaje['turno'] == '' && $marcaje['horaEntrada'] != $marcaje['horaSalida']) {
                                                $style = "style='color:red;'";
                                                $turno = "SIN TURNO DEFINIDO";
                                            } else if ($marcaje['turno'] == '' && $marcaje['horaEntrada'] == $marcaje['horaSalida'] && $marcaje['horaEntrada'] != '' && $marcaje['horaSalida'] != '') {
                                                $style = "style='color:blue;'";
                                                $turno = "SIN TURNO DEFINIDO";
                                                $hrsExtrasDiurnas = 0;
                                            }
                                            $totalDiurnas += ($hrsExtrasDiurnas < 0 ? 0 : $hrsExtrasDiurnas);
                                            $totalNocturnas += ($hrsExtrasNocturnas < 0 ? 0 : $hrsExtrasNocturnas);
                                        }
                                        //END CALCULO DE HORAS EXTRAS
                                        $seguroSocial = 0;
                                        $totalPagado = ($value2['ordinario'] + $value2['bonificacion'] + $value2['bonificacionFija'] + ($value2['horasSimples'] * ($value2['salarioDiarioHE'] / 8) * 1.5) + ($value2['horasDobles'] * ($value2['salarioDiarioHE'] / 8) * 2) + $value2['horasExtMixtas'] + $value2['comisiones'] + $value2['bonoIncentivo'] + $value2['bonoCalidad'] + $value2['bonoProductividad'] + $value2['bonoFlocking'] + $value2['otrosPagos']);
                                        $totalPagado2 = ($value2['ordinario'] + ($value2['horasSimples'] * ($value2['salarioDiarioHE'] / 8) * 1.5) + ($value2['horasDobles'] * ($value2['salarioDiarioHE'] / 8) * 2) + $value2['horasExtMixtas']);
                                        $seguroSocial = (($totalPagado2) * $value2['seguroSocial']) / 100;
                                        $isr = 0;
                                        $isrFormula = ($totalPagado * 24) + ($value2['salarioOrdinario'] * 2) - (($seguroSocial * 24) + $value2['deduccionSinComprobacion'] + ($value2['salarioOrdinario'] * 2));
                                        if ($isrFormula <= $value2['ingresoSujetosISR']) {
                                            $isr = (((($isrFormula * $value2['isr']) / 100) / 12) / (30 / $_REQUEST['dias']));
                                        }
                                        if ($isr < 1) {
                                            $isr = 0;
                                        }
                                        $totalDescuentos = ($seguroSocial + $isr + $value2['anticipos'] + $value2['prestamos'] + $value2['otrosDescuentos']);
                                        $liquidoRecibir = $totalPagado - $totalDescuentos;
                                        $total1 += $value2['ordinario'];
                                        $total2 += $value2['bonificacion'];
                                        if ($_REQUEST['fuenteHorasExtras'] == 1) {
                                            $total3 += $totalDiurnas;
                                            $total4 += ($totalDiurnas * ($value2['salarioDiarioHE'] / 8) * 1.5);
                                            $total5 += $totalNocturnas;
                                            $total6 += ($totalNocturnas * ($value2['salarioDiarioHE'] / 8) * 2);
                                            $total7 += $value2['horasMixtas'];
                                            $total8 += $value2['horasExtMixtas'];
                                        } else {
                                            $total3 += $value2['horasSimples'];
                                            $total4 += $value2['horasExtSimples'];
                                            $total5 += $value2['horasDobles'];
                                            $total6 += $value2['horasExtDobles'];
                                            $total7 += $value2['horasMixtas'];
                                            $total8 += $value2['horasExtMixtas'];
                                        }
                                        $total9 += $value2['comisiones'];
                                        $total10 += $value2['bonificacionFija'];
                                        $total11 += $value2['bonoIncentivo'];
                                        $total12 += $value2['bonoCalidad'];
                                        $total13 += $value2['bonoProductividad'];
                                        $total14 += $value2['bonoFlocking'];
                                        $total15 += $value2['otrosPagos'];
                                        $total16 += $totalPagado;
                                        $total17 += $seguroSocial;
                                        $total18 += $isr;
                                        $total19 += $value2['anticipos'];
                                        $total20 += $value2['prestamos'];
                                        $total21 += $value2['otrosDescuentos'];
                                        $total22 += $totalDescuentos;
                                        $total23 += $liquidoRecibir;
                                        ?>
                                        <tr>
                                            <td><?= ($key2 + 1); ?></td>
                                            <td><?= $value2['codigoEmpleado']; ?></td>
                                            <td><?= $value2['nombreCompleto']; ?></td>
                                            <td><?= $value2['fechaIngreso']; ?></td>
                                            <td align="center"><?= number_format($value2['sueldoDiario'], 2); ?></td>
                                            <td align="center"><?= $value2['diasTrabajados']; ?></td>
                                            <td align="right"><?= number_format($value2['ordinario'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['bonificacion'], 2); ?></td>
                                            <?php
                                            if ($_REQUEST['fuenteHorasExtras'] == 1) {
                                                ?>
                                                <td align="right"><?= $totalDiurnas; ?></td>
                                                <td align="right"><?= number_format(($totalDiurnas * ($value2['salarioDiarioHE'] / 8) * 1.5), 2); ?></td>
                                                <td align="right"><?= $totalNocturnas; ?></td>
                                                <td align="right"><?= number_format(($totalNocturnas * ($value2['salarioDiarioHE'] / 8) * 2), 2); ?></td>
                                                <td align="right"><?= number_format($value2['horasMixtas'], 2); ?></td>
                                                <td align="right"><?= number_format($value2['horasExtMixtas'], 2); ?></td>
                                                <?php
                                            } else {
                                                ?>
                                                <td align="right"><?= number_format($value2['horasSimples'], 2); ?></td>
                                                <td align="right"><?= number_format($value2['horasExtSimples'], 2); ?></td>
                                                <td align="right"><?= number_format($value2['horasDobles'], 2); ?></td>
                                                <td align="right"><?= number_format($value2['horasExtDobles'], 2); ?></td>
                                                <td align="right"><?= number_format($value2['horasMixtas'], 2); ?></td>
                                                <td align="right"><?= number_format($value2['horasExtMixtas'], 2); ?></td>
                                                <?php
                                            }
                                            ?>
                                            <td align="right"><?= number_format($value2['comisiones'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['bonificacionFija'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['bonoIncentivo'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['bonoCalidad'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['bonoProductividad'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['bonoFlocking'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['otrosPagos'], 2); ?></td>
                                            <td align="right"><?= number_format($totalPagado, 2); ?></td>
                                            <td align="right"><?= number_format($seguroSocial, 2); ?></td>
                                            <td align="right"><?= number_format($isr, 2); ?></td>
                                            <td align="right"><?= number_format($value2['anticipos'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['prestamos'], 2); ?></td>
                                            <td align="right"><?= number_format($value2['otrosDescuentos'], 2); ?></td>
                                            <td align="right"><?= number_format($totalDescuentos, 2); ?></td>
                                            <td align="right"><?= number_format($liquidoRecibir, 2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <thead>
                                    <tr>
                                        <td colspan="6" align="center" style="width: 400px !important; min-width: 400px !important; max-width: 400px !important;">Total Departamento</td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total1, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total2, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total3, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total4, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total5, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total6, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total7, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total8, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total9, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total10, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total11, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total12, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total13, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total14, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total15, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total16, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total17, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total18, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total19, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total20, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total21, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total22, 2); ?></td>
                                        <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($total23, 2); ?></td>
                                    </tr>
                                </thead>
                            </table>
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
                            $totalF17 += $total17;
                            $totalF18 += $total18;
                            $totalF19 += $total19;
                            $totalF20 += $total20;
                            $totalF21 += $total21;
                            $totalF22 += $total22;
                            $totalF23 += $total23;
                        }
                        ?>
                        <table class="table table-striped table-bordered table-hover" cellspacing="0" style="width: 1843px !important;">
                            <thead>
                                <tr>
                                    <td colspan="6" align="center" style="width: 400px !important; min-width: 400px !important; max-width: 400px !important;">Total Planilla</td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF1, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF2, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF3, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF4, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF5, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF6, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF7, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF8, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF9, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF10, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF11, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF12, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF13, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF14, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF15, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF16, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF17, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF18, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF19, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF20, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF21, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF22, 2); ?></td>
                                    <td align="right" style="width: 70px !important; min-width: 70px !important; max-width: 70px !important;"><?= number_format($totalF23, 2); ?></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }
    ?>
</div>