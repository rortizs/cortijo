<?php

session_start();
require_once("../../models/planillaFarmandina.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$nominaClase = new PlanillaFarmandina();
$workbook = new Workbook("-");
$filaHoja = 3;
$workbook->HeaderingExcel('Nomina_NC.xls');
$worksheet1 = & $workbook->add_worksheet('Nomina NC');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$nomina = $nominaClase->nominaNC($_REQUEST);
//FORMATOS CELDAS
$formato_encabezado = & $workbook->add_format();
$formato_encabezado->set_size(10);
$formato_encabezado->set_align('center');
$formato_encabezado->set_color('black');
$formato_encabezado->set_pattern();
$formato_encabezado->set_bold();
$formato_encabezado->set_fg_color('gray');
$formato_encabezado->set_border(1);
$formato_encabezado->set_border_color('black');
//
$formato_encabezado2 = & $workbook->add_format();
$formato_encabezado2->set_size(10);
$formato_encabezado2->set_align('center');
$formato_encabezado2->set_color('black');
$formato_encabezado2->set_pattern();
$formato_encabezado2->set_bold();
$formato_encabezado2->set_fg_color('gray');
$formato_encabezado2->set_border(0);
$formato_encabezado2->set_border_color('black');
//
$formato_encabezado3 = & $workbook->add_format();
$formato_encabezado3->set_size(10);
$formato_encabezado3->set_align('left');
$formato_encabezado3->set_color('black');
$formato_encabezado3->set_pattern();
$formato_encabezado3->set_bold();
$formato_encabezado3->set_fg_color('white');
$formato_encabezado3->set_border(0);
$formato_encabezado3->set_border_color('black');
//
$formato_celda = & $workbook->add_format();
$formato_celda->set_size(8);
$formato_celda->set_align('right');
$formato_celda->set_color('black');
$formato_celda->set_pattern();
$formato_celda->set_fg_color('white');
$formato_celda->set_border(1);
$formato_celda->set_border_color('gray');

//ENCABEZADOS
$worksheet1->write_string(0, 0, 'Planilla de ' . $_REQUEST['month'] . ' / ' . $_REQUEST['year'] . '', $formato_encabezado3);
$worksheet1->merge_cells(0, 0, 0, 17);
$worksheet1->write_string(1, 0, 'Empresa: ' . $_SESSION['nombreEmpresa'] . '', $formato_encabezado3);
$worksheet1->merge_cells(1, 0, 1, 17);
$worksheet1->write_string(2, 0, 'No.', $formato_encabezado);
$worksheet1->write_string(2, 1, 'Nombre', $formato_encabezado);
$worksheet1->write_string(2, 2, 'Cargo', $formato_encabezado);
$worksheet1->write_string(2, 3, 'Sueldo', $formato_encabezado);
$worksheet1->write_string(2, 4, 'Sueldo La Sante', $formato_encabezado);
$worksheet1->write_string(2, 5, 'Depreciacion', $formato_encabezado);
$worksheet1->write_string(2, 6, 'Depreciacion La Sante', $formato_encabezado);
$worksheet1->write_string(2, 7, 'Comision', $formato_encabezado);
$worksheet1->write_string(2, 8, 'Comision La Sante', $formato_encabezado);
$worksheet1->write_string(2, 9, 'Total', $formato_encabezado);
$worksheet1->write_string(2, 10, 'Costo Dicegsa', $formato_encabezado);
$worksheet1->write_string(2, 11, 'Costo La Sante', $formato_encabezado);
$worksheet1->write_string(2, 12, 'INATEC', $formato_encabezado);
$worksheet1->write_string(2, 13, 'INATEC La Sante', $formato_encabezado);
$worksheet1->write_string(2, 14, 'INSS', $formato_encabezado);
$worksheet1->write_string(2, 15, 'INSS La Sante', $formato_encabezado);
$worksheet1->write_string(2, 16, 'Total', $formato_encabezado);
$worksheet1->write_string(2, 17, 'Costo La Sante', $formato_encabezado);
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
//CONTENIDO
foreach ($nomina as $key => $value) {
    $total1 += $value['sueldo'];
    $total2 += $value['sueldoLaSante'];
    $total3 += $value['depreciacion'];
    $total4 += $value['depreciacionLaSante'];
    $total5 += $value['comision'];
    $total6 += $value['comisionLaSante'];
    $totales1 = ($value['sueldo'] + $value['depreciacion'] + $value['comision']);
    $totales2 = ($value['sueldoLaSante'] + $value['depreciacionLaSante'] + $value['comisionLaSante']);
    $totales3 = ($totales1 - $totales2);
    $total7 += $totales1;
    $total8 += $totales3;
    $total9 += $totales2;
    $total10 += $value['inatec'];
    $total11 += $value['inatecLaSante'];
    $total12 += $value['INSS'];
    $total13 += $value['INSSLaSante'];
    $costoTotal = ($totales1 + $value['inatec'] + $value['INSS']);
    $costoTotalLaSante = ($totales2 + $value['inatecLaSante'] + $value['INSSLaSante']);
    $total14 += $costoTotal;
    $total15 += $costoTotalLaSante;
    //
    $worksheet1->write_string($filaHoja, 0, ($key + 1), $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['codigoEmpleado'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['empleado'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, number_format($value['sueldo'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 4, number_format($value['sueldoLaSante'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 5, number_format($value['depreciacion'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 6, number_format($value['depreciacionLaSante'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 7, number_format($value['comision'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 8, number_format($value['comisionLaSante'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 9, number_format($totales1, 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 10, number_format($totales3, 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 11, number_format($totales2, 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 12, number_format($value['inatec'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 13, number_format($value['inatecLaSante'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 14, number_format($value['INSS'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 15, number_format($value['INSSLaSante'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 16, number_format($costoTotal, 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 17, number_format($costoTotalLaSante, 2), $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'Total NC', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 0, $filaHoja, 2);
$worksheet1->write_string($filaHoja, 3, number_format($total1, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 4, number_format($total2, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 5, number_format($total3, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 6, number_format($total4, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 7, number_format($total5, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 8, number_format($total6, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 9, number_format($total7, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 10, number_format($total8, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 11, number_format($total9, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 12, number_format($total10, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 13, number_format($total11, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 14, number_format($total12, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 15, number_format($total13, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 16, number_format($total14, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 17, number_format($total15, 2), $formato_encabezado);
//
//
$filaHoja2 = ($filaHoja + 5);
$worksheet1->write_string($filaHoja2, 0, 'Realizado Gestion de Personal', $formato_encabezado2);
$worksheet1->merge_cells($filaHoja2, 0, $filaHoja2, 2);
$worksheet1->write_string($filaHoja2, 4, 'Gerencia Administrativa', $formato_encabezado2);
$worksheet1->merge_cells($filaHoja2, 4, $filaHoja2, 6);
$worksheet1->write_string($filaHoja2, 8, 'Gerencia General', $formato_encabezado2);
$worksheet1->merge_cells($filaHoja2, 8, $filaHoja2, 11);
$workbook->close();
?>

