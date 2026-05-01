<?php

session_start();
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
require_once("../../models/planilla.php");
require_once("../../models/admin.php");
$planilla = new Planilla();  
$admin = new Admin();
$fechaInicio="";
$fechaFin=""; 
$getHrmDepartamentos = $planilla->getHrmDepartamentos($_REQUEST['idHrmPlanilla'], $_SESSION['idEmpresa'], $_REQUEST['idHrmDepartamentos']);
$getHrmDepartamentos2 = $planilla->getHrmDepartamentos($_SESSION['idEmpresa'], '');
$getHrmPlanillas = $planilla->getHrmPlanillas($_SESSION['idEmpresa']);
$getAnos = $admin->getAnos(); 
$getMeses = $admin->getMeses();
    if ($_REQUEST['quincena'] == '1') {
        $fechaInicio = $_REQUEST['periodo'] . "-" . $_REQUEST['mes'] . "-01";
        $fechaFin = $_REQUEST['periodo'] . "-" . $_REQUEST['mes'] . "-15";
    } else {
        $fechaInicio = $_REQUEST['periodo'] . "-" . $_REQUEST['mes'] . "-16";
        $fechaFin = $_REQUEST['periodo'] . "-" . $_REQUEST['mes'] . "-30";
    }

$workbook = new Workbook("-");
$filaHoja = 0;
$workbook->HeaderingExcel('Planilla de Sueldos' . $_REQUEST['idPuntoIngresoTxt'] . '.xls');
$worksheet1 = & $workbook->add_worksheet('Planilla de Sueldos');
//FORMATOS CELDAS

$formato_encabezado = & $workbook->add_format();
$formato_encabezado->set_size(10);
$formato_encabezado->set_align('center');
$formato_encabezado->set_color('white');
$formato_encabezado->set_pattern();
$formato_encabezado->set_bold();
$formato_encabezado->set_fg_color('grey');
$formato_encabezado->set_border(1);
$formato_encabezado->set_border_color('black');
//
$formato_celda = & $workbook->add_format();
$formato_celda->set_size(10);
$formato_celda->set_align('center'); 
$formato_celda->set_color('black');
$formato_celda->set_pattern();
$formato_celda->set_fg_color('white');
$formato_celda->set_border(1); 
$formato_celda->set_border_color('grey');
//
$totalEmpleados = 0;
$numeroFilaTitulo = 0;

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

//ENCABEZADOS
   

       
    if ($_REQUEST['quincena'] == "1") { 
        $worksheet1->write_string($filaHoja, 0, $value['descripcion'] . $_REQUEST['idPuntoIngresoTxt'] . '', $formato_encabezado);
        $worksheet1->merge_cells($filaHoja, 0, $filaHoja, 23);
         $filaHoja++;
        $worksheet1->write_string($filaHoja, 0, 'Centro Costo', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 1, 'No.', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 2, 'Nombre Empleado', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 3, 'Puesto', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 4, 'CDR', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 5, 'Fecha Ingreso', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 6, 'Sueldo Base', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 7, 'Bono Decreto 37-2001', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 8, 'Dias Laborados 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 9, 'Total Sueldo Ordinario 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 10, 'Total Bono decreto 37-2001 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 11, 'Bonificacion Por Cumplimiento De Metas', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 12, 'Total Devengado 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 13, 'IGGS mensual a pagar 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 14, 'Prestamos Internos 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 15, 'Creditos Empleados 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 16, 'Otros Descuentos 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 17, 'Faltantes 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 18, 'Descuentos Uniformes', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 19, 'Prestamos Bantrab', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 20, 'Descuentos Seguro De Vida y Gastos Medicos', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 21, 'Total descuento Primera Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 22, 'Total A Pagar 1ra Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 23, 'Forma De Pago', $formato_encabezado);
        $filaHoja++;
        //CONTENIDO
        $_REQUEST['idHrmDepartamentos'] = $value['id'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $generacionPlanilla = $planilla->generacionPlanilla($_REQUEST, $fechaInicio, $fechaFin);
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
            $worksheet1->write_string($filaHoja, 0, $value2['centrosCosto'], $formato_celda);
            $worksheet1->write_string($filaHoja, 1, $key2 + 1, $formato_celda);
            $worksheet1->write_string($filaHoja, 2, $value2['nombreEmpleado'], $formato_celda);
            $worksheet1->write_string($filaHoja, 3, $value2['puesto'], $formato_celda);
            $worksheet1->write_string($filaHoja, 4, $value2['cdr'], $formato_celda);
            $worksheet1->write_string($filaHoja, 5, $value2['fechaIngreso'], $formato_celda);
            $worksheet1->write_string($filaHoja, 6, number_format($value2['salarioOrdinario'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 7, number_format($value2['bonificacion'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 8, $value2['diasTrabajadosPQ'], $formato_celda);
            $worksheet1->write_string($filaHoja, 9, number_format($totalSueldoOrdinarioPQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 10, number_format($bonoQuincenalPQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 11, number_format($value2['comisiones']), $formato_celda);
            $worksheet1->write_string($filaHoja, 12, number_format($totalDevPQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 13, '-', $formato_celda);
            $worksheet1->write_string($filaHoja, 14, number_format($value2['prestamosPQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 15, number_format($value2['creditoPQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 16, number_format($value2['otrosPQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 17, number_format($value2['faltantesPQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 18, number_format($value2['uniformes'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 19, number_format($value2['bantrab'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 20, number_format($value2['seguro'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 21, number_format($totalMensualDesPQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 22, number_format($totalPagadoPQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 23, $value2['formaPago'], $formato_celda);
            

            $filaHoja++;
        }
            $worksheet1->write_string($filaHoja, 0, '-', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 1, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 2, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 3, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 4, 'Totales', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 5, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 6, number_format($total1, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 7, number_format($total2, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 8, $total3, $formato_encabezado);
            $worksheet1->write_string($filaHoja, 9, number_format($total7, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 10, number_format($total10, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 11, number_format($total11,2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 12, number_format($total14, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 13, '-', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 14, number_format($total19, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 15, number_format($total21, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 16, number_format($total23, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 17, number_format($total25, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 18, number_format($total27, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 19, number_format($total28, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 20, number_format($total29, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 21, number_format($total30, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 22, number_format($total32, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 23, '', $formato_encabezado);

    } else if ($_REQUEST['quincena'] == "2") {
        $worksheet1->write_string($filaHoja, 0, $value['descripcion'] . $_REQUEST['idPuntoIngresoTxt'] . '', $formato_encabezado);
        $worksheet1->merge_cells($filaHoja, 0, $filaHoja, 27);
         $filaHoja++;
        $worksheet1->write_string($filaHoja, 0, 'Centro Costo', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 1, 'No.', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 2, 'Nombre Empleado', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 3, 'Puesto', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 4, 'CDR', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 5, 'Fecha Ingreso', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 6, 'Sueldo Base', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 7, 'Bono Decreto 37-2001', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 8, 'Dias Laborados 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 9, 'Dias Laborados Mensual', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 10, 'Total Sueldo Ordinario 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 11, 'Total Sueldo Ordinario Mensual', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 12, 'Total Bono decreto 37-2001 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 13, 'Total Bono decreto 37-2001 Mensual', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 14, 'Total Devengado Mensual', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 15, 'IGGS Mensual A Pagar 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 16, 'ISR Mensual', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 17, 'Prestamos Internos 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 18, 'Creditos Empleados 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 19, 'Otros Descuentos 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 20, 'Faltantes 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 21, 'Descuento Uniformes', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 22, 'Prestamos Bantrab', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 23, 'Descuento Seguro De Vida y Gastos Medicos', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 24, 'Total Descuentos 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 25, 'Total a Pagar 2da Quincena', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 26, 'Total a Pagar Mensual', $formato_encabezado);
        $worksheet1->write_string($filaHoja, 27, 'Forma De Pago', $formato_encabezado);
         $filaHoja++;
        //CONTENIDO
        $_REQUEST['idHrmDepartamentos'] = $value['id'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $generacionPlanilla = $planilla->generacionPlanilla($_REQUEST, $fechaInicio, $fechaFin);
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
            $worksheet1->write_string($filaHoja, 0, $value2['centrosCosto'], $formato_celda);
            $worksheet1->write_string($filaHoja, 1, $key2 + 1, $formato_celda);
            $worksheet1->write_string($filaHoja, 2, $value2['nombreEmpleado'], $formato_celda);
            $worksheet1->write_string($filaHoja, 3, $value2['puesto'], $formato_celda);
            $worksheet1->write_string($filaHoja, 4, $value2['cdr'], $formato_celda);
            $worksheet1->write_string($filaHoja, 5, $value2['fechaIngreso'], $formato_celda);
            $worksheet1->write_string($filaHoja, 6, number_format($value2['salarioOrdinario'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 7, number_format($value2['bonificacion'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 8, $value2['diasTrabajadosSQ'], $formato_celda);
            $worksheet1->write_string($filaHoja, 9, $totalMensualDL, $formato_celda);
            $worksheet1->write_string($filaHoja, 10, number_format($totalSueldoOrdinarioSQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 11, number_format($totalSueldoOrdinario, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 12, number_format($bonoQuincenalSQ), $formato_celda);
            $worksheet1->write_string($filaHoja, 13, number_format($bonoMensual, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 14, number_format($totalDevSQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 15, number_format($seguroSocial, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 16, number_format($isr, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 17, number_format($value2['prestamosSQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 18, number_format($value2['creditoSQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 19, number_format($value2['otrosSQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 20, number_format($value2['faltantesSQ'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 21, number_format($value2['uniformes'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 22, number_format($value2['bantrab'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 23, number_format($value2['seguro'], 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 24, number_format($totalMensualDesSQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 25, number_format($totalPagadoSQ, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 26, number_format($totalPagado, 2), $formato_celda);
            $worksheet1->write_string($filaHoja, 27, $value2['formaPago'], $formato_celda);

            $filaHoja++;
        }
            $worksheet1->write_string($filaHoja, 0, '-', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 1, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 2, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 3, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 4, 'Totales', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 5, '', $formato_encabezado);
            $worksheet1->write_string($filaHoja, 6, number_format($total1, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 7, number_format($total2, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 8, $total4, $formato_encabezado);
            $worksheet1->write_string($filaHoja, 9, $total5, $formato_encabezado);
            $worksheet1->write_string($filaHoja, 10, number_format($total8, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 11, number_format($total9, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 12, number_format($total12,2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 13, number_format($total13, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 14, number_format($total15, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 15, number_format($total17, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 16, number_format($total18, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 17, number_format($total20, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 18, number_format($total22, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 19, number_format($total24, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 20, number_format($total26, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 21, number_format($total27, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 22, number_format($total28, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 23, number_format($total29, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 24, number_format($total31, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 25, number_format($total33, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 26, number_format($total34, 2), $formato_encabezado);
            $worksheet1->write_string($filaHoja, 27, ' ', $formato_encabezado);
    }
    $filaHoja = $filaHoja + 2;
}
 
$workbook->close();
?>

