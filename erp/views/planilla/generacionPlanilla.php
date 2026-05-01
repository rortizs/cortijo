<style>
    #fixTable {
        width: 1800px !important;
    }
</style>
<?php
/*
 * PLANILLA - GENERACION DE PLANILLA
 */
session_start();
require_once("../../models/planilla.php");
require_once("../../models/admin.php");
$planilla = new Planilla();
$admin = new Admin();
$getHrmDepartamentos2 = $planilla->getHrmDepartamentos($_SESSION['idEmpresa'], '');
$getHrmPlanillas = $planilla->getHrmPlanillas($_SESSION['idEmpresa']);
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
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
                        <label>Nomina a Trabajar</label>
                        <select id="idHrmPlanillas" class="form-control input-sm" onchange="loadDepartamentos();">
                            <option value="">[Seleccione...]</option>
                            <?php
                            foreach ($getHrmPlanillas as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Departamento</label>
                        <select id="idHrmDepartamentos" class="form-control input-sm">
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Periodo</label>
                        <select id="periodo" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getAnos as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Mes</label>
                        <select id="mes" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getMeses as $key => $value) {
                                ?>
                                <option value="<?= $value['number']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Quincena a Trabajar</label>
                        <select id="quincena" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <option value="1">1RA. QUINCENA</option>
                            <option value="2">2DA. QUINCENA</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-primary btn-sm" onclick="generarPlanilla();">
                            <i class="fa fa-table"></i> Generar Planilla
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="generarExcelPlanilla();">
                            <i class="fa fa-table"></i> Imprimir Planilla
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="imprimirRecibosPdf();">
                            <i class="fa fa-table"></i> Imprimir Recibos
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="cargaBancosTxt();">
                            <i class="fa fa-table"></i> Carga a Banco
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
if (isset($_POST['idHrmPlanilla']) && isset($_POST['periodo']) && isset($_POST['mes']) && isset($_POST['quincena'])) {
    $getHrmDepartamentos = $planilla->getHrmDepartamentos($_POST['idHrmPlanilla'], $_SESSION['idEmpresa'], $_POST['idHrmDepartamentos']);
    $fechaInicio = "";
    $fechaFin = "";
    if ($_POST['quincena'] == '1') {
        $fechaInicio = $_POST['periodo'] . "-" . $_POST['mes'] . "-01";
        $fechaFin = $_POST['periodo'] . "-" . $_POST['mes'] . "-15";
    } else {
        $fechaInicio = $_POST['periodo'] . "-" . $_POST['mes'] . "-16";
        $fechaFin = $_POST['periodo'] . "-" . $_POST['mes'] . "-30";
    }
    ?>
    <div class="row">
        <div class="col-lg-12" id="detalle">
            <section class="panel">
                <div class="panel-body">
                    <center>
                        <h4>
                            Generación Planilla de Sueldos<br/>
                            <small>
                                Nomina a Trabajar <b><?= $_POST['idHrmPlanillaTXT']; ?></b><br/>
                                Periodo <b><?= $_POST['periodo']; ?></b> Mes <b><?= $_POST['mesTXT']; ?></b><br/>
                                Quincena a Trabajar: <b><?= $_POST['quincenaTXT']; ?>
                            </small>
                        </h4>
                    </center>
                    <div class="clearfix">&nbsp;</div>
                    <div class="table-responsive" id="reportContainer">
                        <?php
                        $totalEmpleados = 0;
                        foreach ($getHrmDepartamentos as $key => $value) {
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
                            $total24 = 0;
                            $total25 = 0;
                            $total26 = 0;
                            $total27 = 0;
                            $total28 = 0;
                            $total29 = 0;
                            $total30 = 0;
                            $total31 = 0;
                            $total32 = 0;
                            $total33 = 0;
                            $total34 = 0;
                            $total35 = 0;
                            ?>
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" style="width: 1843px !important;">
                                <thead>
                                    <tr>
                                        <td colspan="44"><?= $value['descripcion']; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="max-width: 50px; min-width: 50px !important;">Centro<br/>Costo</td>
                                        <td style="max-width: 25px; min-width: 25px !important;">No.</td>
                                        <td style="max-width: 300px; min-width: 300px !important;">Nombre Empleado</td>	
                                        <td style="max-width: 200px; min-width: 200px !important;">Puesto</td>
                                        <td style="max-width: 25px; min-width: 25px !important;">CDR</td>
                                        <td style="max-width: 100px; min-width: 100px !important;">Fecha<br/>Ingreso</td>
                                        <td>Sueldo Base</td>
                                        <td>Bono Decreto<br/>37-2001</td>
                                        <td class="primera">Dias Laborados<br/>1ra. Quincena</td>
                                        <td class="segunda">Dias Laborados<br/>2da. Quincena</td>
                                        <td class="segunda">Dias Laborados<br/>Mensual</td>
                                        <td class="primera">Total Sueldo<br/>Ordinario<br/>1ra. Quincena</td>
                                        <td class="segunda">Total Sueldo<br/>Ordinario<br/>2da. Quincena</td>
                                        <td class="segunda">Total Sueldo<br/>Ordinario Mensual</td>
                                        <td class="primera">Total Bono decreto<br/>37-2001<br>1ra. Quincena</td>
                                        <td class="primera">Bonificacion por<br/>cumplimiento de metas </td>
                                        <td class="segunda">Total Bono decreto<br/>37-2001<br>2da. Quincena</td>
                                        <td class="segunda">Total Bono decreto<br/>37-2001 Mensual</td>
                                        <td class="primera">Total Devengado<br/>1ra. Quincena</td>
                                        <td class="segunda">Total Devengado Mensual</td>
                                        <td class="primera">IGSS mensual a Pagar<br/>1ra quincena</td>
                                        <td class="segunda">IGSS mensual a Pagar<br/>2da Quincena</td>
                                        <td class="segunda">ISR Mensual</td>
                                        <td class="primera">Prestamos Internos<br/>1ra quincena</td>
                                        <td class="segunda">Prestamos Internos<br/>2da quincena</td>
                                        <td class="primera">Creditos Empleados<br/>1ra quincena</td>
                                        <td class="segunda">Creditos Empleados<br/>2da quincena</td>
                                        <td class="primera">Otros Descuentos<br/>1ra quincena</td>
                                        <td class="segunda">Otros Descuentos<br/>2da quincena</td>
                                        <td class="primera">Faltantes<br/>1ra quincena</td>
                                        <td class="segunda">Faltantes<br/>2da quincena</td>
                                        <td>Descuento<br/>Uniformes</td>
                                        <td>Prestamo<br/>Bantrab</td>
                                        <td>Descuento Seguro<br/>Vida y Gastos Medicos</td>
                                        <td class="primera">Total Descuentos<br/>1ra quincena</td>
                                        <td class="segunda">Total Descuentos<br/>2da quincena</td>
                                        <td class="primera">Total A Pagar<br/>1ra quincena</td>
                                        <td class="segunda">Total A Pagar<br/>2da quincena</td>
                                        <td class="segunda">Total A Pagar<br/>Mensual</td>
                                        <td>Forma Pago</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $_POST['idHrmDepartamentos'] = $value['id'];
                                    $_POST['idEmpresas'] = $_SESSION['idEmpresa'];
                                    $generacionPlanilla = $planilla->generacionPlanilla($_POST, $fechaInicio, $fechaFin);
                                    foreach ($generacionPlanilla as $key2 => $value2) {
                                        $total35 += 1;
                                        //CALCULOS
                                        $salarioHora = round((($value2['salarioOrdinario'] / 30) / 8), 2);
                                        $totalSueldoOrdinarioPQ = round((($value2['salarioOrdinario'] / 30) * $value2['diasTrabajadosPQ']), 2);
                                        $totalSueldoOrdinarioSQ = round((($value2['salarioOrdinario'] / 30) * $value2['diasTrabajadosSQ']), 2);
                                        $totalSueldoOrdinario = ($totalSueldoOrdinarioPQ + $totalSueldoOrdinarioSQ);
                                        $bonoQuincenalPQ = round((($value2['bonificacion'] / 30) * $value2['diasTrabajadosPQ']), 2);
                                        $bonoQuincenalSQ = round((($value2['bonificacion'] / 30) * $value2['diasTrabajadosSQ']), 2);
                                        $bonoMensual = ($bonoQuincenalPQ + $value2['comisiones'] + $bonoQuincenalSQ);
                                        $totalDevPQ = ($totalSueldoOrdinarioPQ + $bonoQuincenalPQ + $value2['comisiones']);
                                        $totalDevSQ = ($value2['salarioOrdinario'] + $bonoMensual);
                                        $seguroSocial = (($totalSueldoOrdinario * 4.83) / 100);
                                        $tipoCalculo = "normal";
                                        $isr = 0;
                                        if ($value2['tipoCalculoISR'] == '2') {
                                            $tipoCalculo = "especial";
                                            $tda = ($value2['salarioOrdinario'] * 12);
                                            $bonos = ($value2['salarioOrdinario'] * 2);
                                            $bonosExcedente = ((($totalDevSQ - $value2['salarioOrdinario']) - 250) * 2);
                                            $bonosDecreto = (($totalDevSQ - $value2['salarioOrdinario']) * 12);
                                            $exentos = ($bonos + ($seguroSocial * 12) + $value2['deduccionSinComprobacion']);
                                            $isrFormula = ($tda + $bonos + $bonosExcedente + $bonosDecreto) - $exentos;
                                            if ($isrFormula <= $value2['ingresoSujetosISR']) {
                                                $isr = (((($isrFormula * $value2['isr']) / 100) / 12));
                                            }
                                            if ($isr < 1) {
                                                $isr = 0;
                                            }
                                        } else {
                                            $isrFormula = ($totalDevSQ * 12) + ($value2['salarioOrdinario'] * 2) - (($seguroSocial * 12) + $value2['deduccionSinComprobacion'] + ($value2['salarioOrdinario'] * 2));
                                            if ($isrFormula <= $value2['ingresoSujetosISR']) {
                                                $isr = (((($isrFormula * $value2['isr']) / 100) / 12));
                                            }
                                            if ($isr < 1) {
                                                $isr = 0;
                                            }
                                        }
                                        $otrosDescuentos = 0;
                                        $faltantes = 0;
                                        $totalMensualDesPQ = ($value2['prestamosPQ'] + $value2['creditoPQ'] + $value2['otrosPQ'] + $value2['faltantesPQ'] + $value2['uniformes'] + $value2['bantrab']);
                                        $totalMensualDesSQ = ($seguroSocial + $isr + $value2['prestamosSQ'] + $value2['creditoSQ'] + $value2['otrosSQ'] + $value2['faltantesSQ'] + $value2['seguro']);
                                        $totalPagadoPQ = ($totalDevPQ - $totalMensualDesPQ);
                                        $totalPagadoSQ = ($totalDevSQ - $totalMensualDesSQ - $totalDevPQ);
                                        $totalMensualDL = ($value2['diasTrabajadosPQ'] + $value2['diasTrabajadosSQ']);
                                        $totalPagado = ($totalPagadoPQ + $totalPagadoSQ);
                                        //TOTALES
                                        $total1 += $value2['salarioOrdinario'];
                                        $total2 += $value2['bonificacion'];
                                        $total3 += $value2['diasTrabajadosPQ'];
                                        $total4 += $value2['diasTrabajadosSQ'];
                                        $total5 += $totalMensualDL;
                                        $total7 += $totalSueldoOrdinarioPQ;
                                        $total8 += $totalSueldoOrdinarioSQ;
                                        $total9 += $totalSueldoOrdinario;
                                        $total10 += $bonoQuincenalPQ;
                                        $total11 += $value2['comisiones'];
                                        $total12 += $bonoQuincenalSQ;
                                        $total13 += $bonoMensual;
                                        $total14 += $totalDevPQ;
                                        $total15 += $totalDevSQ;
                                        $total16 += 0;
                                        $total17 += $seguroSocial;
                                        $total18 += $isr;
                                        $total19 += $value2['prestamosPQ'];
                                        $total20 += $value2['prestamosSQ'];
                                        $total21 += $value2['creditoPQ'];
                                        $total22 += $value2['creditoSQ'];
                                        $total23 += $value2['otrosPQ'];
                                        $total24 += $value2['otrosSQ'];
                                        $total25 += $value2['faltantesPQ'];
                                        $total26 += $value2['faltantesSQ'];
                                        $total27 += $value2['uniformes'];
                                        $total28 += $value2['bantrab'];
                                        $total29 += $value2['seguro'];
                                        $total30 += $totalMensualDesPQ;
                                        $total31 += $totalMensualDesSQ;
                                        $total32 += $totalPagadoPQ;
                                        $total33 += $totalPagadoSQ;
                                        $total34 += $totalPagado;
                                        ?>
                                        <tr>
                                            <td style="max-width: 50px; min-width: 50px !important;"><?= $value2['centrosCosto']; ?></td>
                                            <td style="max-width: 25px; min-width: 25px !important;"><?= ($key2 + 1); ?></td>
                                            <td style="text-transform: uppercase; max-width: 300px; min-width: 300px !important;"><?= $value2['nombreEmpleado']; ?></td>
                                            <td style="text-transform: uppercase; max-width: 200px; min-width: 200px !important; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= $value2['puesto']; ?></td>
                                            <td style="text-transform: uppercase; max-width: 200px; min-width: 200px !important;"><?= $value2['cdr']; ?></td>
                                            <td style="text-transform: uppercase; text-align: center; max-width: 100px; min-width: 100px !important;"><?= $value2['fechaIngreso']; ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($value2['salarioOrdinario'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($value2['bonificacion'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= $value2['diasTrabajadosPQ']; ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= $value2['diasTrabajadosSQ']; ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= $totalMensualDL; ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($totalSueldoOrdinarioPQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($totalSueldoOrdinarioSQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($totalSueldoOrdinario, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($bonoQuincenalPQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($value2['comisiones'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($bonoQuincenalSQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($bonoMensual, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($totalDevPQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($totalDevSQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera">-</td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($seguroSocial, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($isr, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($value2['prestamosPQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($value2['prestamosSQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($value2['creditoPQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($value2['creditoSQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($value2['otrosPQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($value2['otrosSQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($value2['faltantesPQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($value2['faltantesSQ'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($value2['uniformes'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($value2['bantrab'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($value2['seguro'], 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($totalMensualDesPQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($totalMensualDesSQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($totalPagadoPQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($totalPagadoSQ, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($totalPagado, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: center;"><?= $value2['noCuentaBancaria']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    $totalEmpleados += $total35;
                                    ?>
                                </tbody>
                                <thead>
                                    <tr>
                                        <td>-</td> 
                                        <td><?= $total35; ?></td>   
                                        <td colspan="4">TOTALES</td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($total1, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($total2, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= $total3; ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= $total4 ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= $total5; ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total7, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total8, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total9, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total10, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total11, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total12, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total13, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total14, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total15, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera">-</td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total17, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total18, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total19, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total20, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total21, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total22, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total23, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total24, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total25, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total26, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($total27, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($total28, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;"><?= number_format($total29, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total30, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total31, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="primera"><?= number_format($total32, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total33, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: right;" class="segunda"><?= number_format($total34, 2); ?></td>
                                            <td style="text-transform: uppercase; text-align: center;"></td>
                             
 
                                    </tr>
                                </thead>
                            </table> 
                            <?php
                        }
                        ?>
                        Total Empleados:<?= $totalEmpleados; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php
}
?>