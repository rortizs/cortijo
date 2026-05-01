<?php

session_start();
require_once("../../models/planillaFarmandina.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$nominaClase = new PlanillaFarmandina();
$workbook = new Workbook("-");
$filaHoja = 3;
$workbook->HeaderingExcel('Nomina_CR.xls');
$worksheet1 = & $workbook->add_worksheet('Nomina CR');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$nomina = $nominaClase->nominaCR($_REQUEST);
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
$worksheet1->merge_cells(0, 0, 0, 14);
$worksheet1->write_string(1, 0, 'Empresa: ' . $_SESSION['nombreEmpresa'] . '', $formato_encabezado3);
$worksheet1->merge_cells(1, 0, 1, 14);
$worksheet1->write_string(2, 0, 'No.', $formato_encabezado);
$worksheet1->write_string(2, 1, 'Nombre', $formato_encabezado);
$worksheet1->write_string(2, 2, 'Cargo', $formato_encabezado);
$worksheet1->write_string(2, 3, 'Salario Base', $formato_encabezado);
$worksheet1->write_string(2, 4, 'Salario Bruto', $formato_encabezado);
$worksheet1->write_string(2, 5, 'Comision', $formato_encabezado);
$worksheet1->write_string(2, 6, 'Total a Pagar', $formato_encabezado);
$worksheet1->write_string(2, 7, 'C.C.S.S', $formato_encabezado);
$worksheet1->write_string(2, 8, 'INS', $formato_encabezado);
$worksheet1->write_string(2, 9, 'Aguinaldo', $formato_encabezado);
$worksheet1->write_string(2, 10, 'Vacaciones', $formato_encabezado);
$worksheet1->write_string(2, 11, 'Cesantia', $formato_encabezado);
$worksheet1->write_string(2, 12, 'Preaviso', $formato_encabezado);
$worksheet1->write_string(2, 13, 'Depreciacion', $formato_encabezado);
$worksheet1->write_string(2, 14, 'Total a Pagar', $formato_encabezado);
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
//CONTENIDO
foreach ($nomina as $key => $value) {
    $totalAPagar = ($value['totalAPagar'] + $value['CCSS'] + $value['INS'] + $value['aguinaldo'] + $value['vacaciones'] + $value['cesantia'] + $value['preaviso'] + $value['depreciacion']);
    $total1 += $value['salarioBase'];
    $total2 += $value['salarioBruto'];
    $total3 += $value['comision'];
    $total4 += $value['totalAPagar'];
    $total5 += $value['CCSS'];
    $total6 += $value['INS'];
    $total7 += $value['aguinaldo'];
    $total8 += $value['vacaciones'];
    $total9 += $value['cesantia'];
    $total10 += $value['preaviso'];
    $total11 += $value['depreciacion'];
    $total12 += $totalAPagar;
    //
    $worksheet1->write_string($filaHoja, 0, ($key + 1), $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['codigoEmpleado'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['empleado'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, number_format($value['salarioBase'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 4, number_format($value['salarioBruto'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 5, number_format($value['comision'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 6, number_format($value['totalAPagar'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 7, number_format($value['CCSS'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 8, number_format($value['INS'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 9, number_format($value['aguinaldo'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 10, number_format($value['vacaciones'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 11, number_format($value['cesantia'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 12, number_format($value['preaviso'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 13, number_format($value['depreciacion'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 14, number_format($totalAPagar, 2), $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'Total CRC', $formato_encabezado);
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
//
$filaHoja2 = ($filaHoja + 5);
$worksheet1->write_string($filaHoja2, 0, 'Realizado Gestion de Personal', $formato_encabezado2);
$worksheet1->merge_cells($filaHoja2, 0, $filaHoja2, 2);
$worksheet1->write_string($filaHoja2, 4, 'Gerencia Administrativa', $formato_encabezado2);
$worksheet1->merge_cells($filaHoja2, 4, $filaHoja2, 9);
$worksheet1->write_string($filaHoja2, 11, 'Gerencia General', $formato_encabezado2);
$worksheet1->merge_cells($filaHoja2, 11, $filaHoja2, 14);
$workbook->close();
?>

