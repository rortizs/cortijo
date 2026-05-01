<?php

session_start();
require_once("../../models/planilla.php");
require_once("../../models/admin.php");
$planilla = new Planilla();
$admin = new Admin();
$getHrmDepartamentos2 = $planilla->getHrmDepartamentos($_SESSION['idEmpresa'], '');
$getHrmPlanillas = $planilla->getHrmPlanillas($_SESSION['idEmpresa']);
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();

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
            $totalMensualDesPQ = ($value2['prestamosPQ'] + $value2['creditoPQ'] + $value2['otrosPQ'] + $value2['faltantesPQ'] + $value2['uniformes']);
            $totalMensualDesSQ = ($seguroSocial + $isr + $value2['prestamosSQ'] + $value2['creditoSQ'] + $value2['otrosSQ'] + $value2['faltantesSQ'] + $value2['bantrab'] + $value2['seguro']);
            $totalPagadoPQ = ($totalDevPQ - $totalMensualDesPQ);
            $totalPagadoSQ = ($totalDevSQ - $totalMensualDesSQ - $totalDevPQ);
            $totalMensualDL = ($value2['diasTrabajadosPQ'] + $value2['diasTrabajadosSQ']);
            $totalPagado = ($totalPagadoPQ + $totalPagadoSQ);
            //TOTALES
            $carga = fopen("cargaBanco.txt", "w+")or die("problema");

    
            fwrite($bitacora, $value2['nombreEmpleado'].','.$totalSueldoOrdinarioPQ . PHP_EOL);
        }
    }
}