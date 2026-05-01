<?php

session_start();
require_once("../../models/planillaFarmandina.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$nominaClase = new PlanillaFarmandina();
$workbook = new Workbook("-");
$filaHoja = 3;
$workbook->HeaderingExcel('Nomina_HN.xls');
$worksheet1 = & $workbook->add_worksheet('Nomina HN');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$nomina = $nominaClase->nominaHN($_REQUEST);
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
$worksheet1->merge_cells(0, 0, 0, 11);
$worksheet1->write_string(1, 0, 'Empresa: ' . $_SESSION['nombreEmpresa'] . '', $formato_encabezado3);
$worksheet1->merge_cells(1, 0, 1, 11);
$worksheet1->write_string(2, 0, 'No.', $formato_encabezado);
$worksheet1->write_string(2, 1, 'Nombre', $formato_encabezado);
$worksheet1->write_string(2, 2, 'Cargo', $formato_encabezado);
$worksheet1->write_string(2, 3, 'Salario Base', $formato_encabezado);
$worksheet1->write_string(2, 4, 'Variable', $formato_encabezado);
$worksheet1->write_string(2, 5, 'Depr. Veh. Base', $formato_encabezado);
$worksheet1->write_string(2, 6, 'Deducciones', $formato_encabezado);
$worksheet1->write_string(2, 7, 'Total a Recibir', $formato_encabezado);
$worksheet1->write_string(2, 8, 'IHSS', $formato_encabezado);
$worksheet1->write_string(2, 9, 'RAP', $formato_encabezado);
$worksheet1->write_string(2, 10, 'INFOP', $formato_encabezado);
$worksheet1->write_string(2, 11, 'Total Lempiras', $formato_encabezado);
$total1 = 0;
$total2 = 0;
$total3 = 0;
$total4 = 0;
$total5 = 0;
$total6 = 0;
$total7 = 0;
$total8 = 0;
$total9 = 0;
//CONTENIDO
foreach ($nomina as $key => $value) {
    $totalRecibir = ($value['salarioBase'] + $value['variable'] + $value['depreciacion']) - $value['prestamos'];
    $totalLempiras = ($totalRecibir + $value['prestamos'] + $value['IHSS'] + $value['RAP'] + $value['infop']);
    $total1 += $value['salarioBase'];
    $total2 += $value['variable'];
    $total3 += $value['depreciacion'];
    $total4 += $value['prestamos'];
    $total5 += $totalRecibir;
    $total6 += $value['IHSS'];
    $total7 += $value['RAP'];
    $total8 += $value['infop'];
    $total9 += $totalLempiras;
    //
    $worksheet1->write_string($filaHoja, 0, ($key + 1), $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['codigoEmpleado'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['empleado'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, number_format($value['salarioBase'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 4, number_format($value['variable'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 5, number_format($value['depreciacion'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 6, number_format($value['prestamos'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 7, number_format($totalRecibir, 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 8, number_format($value['IHSS'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 9, number_format($value['RAP'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 10, number_format($value['infop'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 11, number_format($totalLempiras, 2), $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'Total HN', $formato_encabezado);
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

